<?php

class DashboardController extends AppController {
	public $helpers = array('Html', 'Form');

        public $components = array('RequestHandler');

        public function beforeFilter() {

                $this->Auth->loginAction = array(
                  'controller' => 'users',
                  'action' => 'login'
                );
                $this->Auth->logoutRedirect = array(
                  'controller' => 'users',
                  'action' => 'login'
                );
                $this->Auth->loginRedirect = array(
                  'controller' => 'dashboard',
                  'action' => 'index'
                );
                $this->Auth->authError = array(
                  'controller' => 'users',
                  'action' => 'login'
                );
        }

        public $uses = array(
            'Market',
            'Region',
            'ClientRevenueByService',
            'User',
            'UserLoginRole',
            'UserMarket',
            'OverviewAnnouncement',
            'OverviewSection',
            'OverviewSectionBrand'
        );

        public function beforeRender() {
                if($this->Auth->user()) {
                        $this->set('admNavLinks', parent::generateNav($this->arrNav, $this->Auth->user()));
                }
        }

        public function index() {
                $this->set('loggedUser', $this->Auth->user());
                $this->set('loggedUserRole', $this->Auth->user('role'));
                
                $this->set('announcement_details', $this->OverviewAnnouncement->find('first'));
                $this->OverviewSection->Behaviors->attach('Containable');
                $this->set('section_details', $this->OverviewSection->find('all'));
        }

        public function global_growth() {
                $this->set('loggedUser', $this->Auth->user());
                if($this->Auth->user('role') == 'Regional') {
                        $userRegions = $this->UserMarket->find('list', array('fields' => array('UserMarket.id', 'UserMarket.market_id'), 'conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $regions = $this->Region->find('list', array('conditions' => array('Region.id in (' . implode(',', $userRegions) . ')'), 'order' => 'Region.region Asc'));
                        $this->set('userRegions', $regions);
                }
        }

        public function local_growth() {

        }
        
        public function save_announcements() {
                $this->autoRender=false;

                $arrData = $this->request->data;
                
                if(isset($arrData['announcement'])) {
                        $this->OverviewAnnouncement->query('DELETE FROM `overview_announcements` WHERE 1');
                        $this->OverviewAnnouncement->create();
                        $this->OverviewAnnouncement->save(
                                array(
                                        'OverviewAnnouncement' => array(
                                                'announcement_details' => $arrData['announcement']
                                        )
                                )
                        );
                } else {
                        $result = array();
                        $result['success'] = false;
                        $result['errors'] = 'Missing announcement data...';
                        return json_encode($result);
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }
        
        public function save_section_data() {
                $this->autoRender=false;

                $arrData = $this->request->data;
                
                if(isset($arrData)) {
                        $brandCnt = count($arrData['brandData'])-1;
                        if(isset($arrData['sectionId']) && $arrData['sectionId'] != null) {
                                $this->OverviewSection->id = $arrData['sectionId'];
                                $this->OverviewSection->save(
                                        array(
                                                'OverviewSection' => array(
                                                        'section_title' => strtoupper($arrData['sectionTitle']),
                                                        'section_no' => $arrData['sectionNo'],
                                                        'brand_cnt' => $brandCnt
                                                )
                                        )
                                );
                                $sectionId = $arrData['sectionId'];
                        } else {
                                $this->OverviewSection->create();
                                $this->OverviewSection->save(
                                        array(
                                                'OverviewSection' => array(
                                                        'section_title' => strtoupper($arrData['sectionTitle']),
                                                        'section_no' => $arrData['sectionNo'],
                                                        'brand_cnt' => $brandCnt
                                                )
                                        )
                                );
                                $sectionId = $this->OverviewSection->getLastInsertId();
                        }
                        
                        $brands = array_slice($arrData['brandData'], 1);
                        $this->OverviewSectionBrand->query('DELETE FROM `overview_section_brands` WHERE section_id=' . $sectionId);
                        foreach($brands as $brand) {
                                if($brand['clientName'] != "CLIENT NAME") {
                                        if(isset($brand['brandId']) && $brand['brandId'] != null) {
                                                $this->OverviewSectionBrand->id = $brand['sectionId'];
                                                $this->OverviewSectionBrand->save(
                                                        array(
                                                                'OverviewSectionBrand' => array(
                                                                        'section_id' => $sectionId,
                                                                        'brand_name' => strtoupper($brand['clientName']),
                                                                        'brand_services' => strtoupper($brand['services']),
                                                                        'brand_markets' => strtoupper($brand['markets']),
                                                                        'brand_synopsis' => $brand['synopsis'],
                                                                        'brand_no' => $brand['brandNo']
                                                                )
                                                        )
                                                );
                                        } else {
                                                $this->OverviewSectionBrand->create();
                                                $this->OverviewSectionBrand->save(
                                                        array(
                                                                'OverviewSectionBrand' => array(
                                                                        'section_id' => $sectionId,
                                                                        'brand_name' => strtoupper($brand['clientName']),
                                                                        'brand_services' => strtoupper($brand['services']),
                                                                        'brand_markets' => strtoupper($brand['markets']),
                                                                        'brand_synopsis' => $brand['synopsis'],
                                                                        'brand_no' => $brand['brandNo']
                                                                )
                                                        )
                                                );
                                        }
                                }
                        }
                }
                $result = array();
                $result['success'] = true;
                $result['sectionId'] = $sectionId;
                return json_encode($result);
        }
        
        public function brand_logo_upload() {
                $this->autoRender=false;

                if(isset($_FILES["file"]["type"]))
                {
                        $validextensions = array("jpeg", "jpg", "png");
                        $temporary = explode(".", $_FILES["file"]["name"]);
                        $file_extension = end($temporary);
                        if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
                        ) && ($_FILES["file"]["size"] < 1048576)//Approx. 1MB files can be uploaded.
                        && in_array($file_extension, $validextensions)) {
                                if ($_FILES["file"]["error"] > 0) {
                                        echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
                                } else {
                                        if (file_exists("upload/" . $_FILES["file"]["name"])) {
                                                echo $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
                                        } else {
                                                $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
                                                $targetPath = 'files/' . $_FILES['file']['name']; // Target path where file is to be stored
                                                move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
                                                echo 'files/' . $_FILES["file"]["name"];
                                        }
                                }
                        } else {
                                echo "<span id='invalid'>***Invalid file Size or Type***<span>";
                        }
                }
        }
}
