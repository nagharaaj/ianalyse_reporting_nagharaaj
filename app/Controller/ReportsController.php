<?php
class ReportsController extends AppController {
	public $helpers = array('Html', 'Form');
        
        public $components = array('RequestHandler');
        
        public $uses = array(
            'City',
            'ClientCategory',
            'Country',
            'Currency',
            'LeadAgency',
            'Market',
            'Region',
            'Service',
            'ClientRevenueByService',
            'UserMarket',
            'Office',
            'Language'
        );

        public $months = array(1 => 'Jan (1)', 'Feb (2)', 'Mar (3)', 'Apr (4)', 'May (5)', 'Jun (6)', 'Jul (7)', 'Aug (8)', 'Sep (9)', 'Oct (10)', 'Nov (11)', 'Dec (12)');

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
                $this->Auth->loginRedirect = array(
                  'controller' => 'dashboard',
                  'action' => 'index'
                );
        }
        
        public function beforeRender() {
                $this->set('admNavLinks', parent::generateNav($this->arrNav, $this->Auth->user()));
        }

        public function client_data() {
                
                $this->set('userRole', $this->Auth->user('role'));
                $this->set('categories', json_encode($this->ClientCategory->find('list', array('fields' => array('ClientCategory.category', 'ClientCategory.category'), 'order' => 'ClientCategory.category Asc'))));
                $countries = $this->Country->find('list', array('fields' => array('Country.country', 'Country.country'), 'order' => 'Country.country Asc'));
                $this->set('countries', json_encode($countries, JSON_HEX_APOS));
                $this->set('currencies', json_encode($this->Currency->find('list', array('fields' => array('Currency.currency', 'Currency.currency'), 'order' => 'Currency.currency Asc'))));
                $this->set('agencies', json_encode($this->LeadAgency->find('list', array('fields' => array('LeadAgency.agency', 'LeadAgency.agency'), 'order' => 'LeadAgency.agency Asc'))));

                $arrMarkets = array('Global' => 'Global');
                $arrRegions = array();
                $arrCities = array();
                $regions = $this->Region->find('all', array('order' => 'Region.region Asc'));
                foreach ($regions as $region) {
                        $arrMarkets[$region['Region']['region']] = 'Regional - ' . $region['Region']['region'];
                        $arrRegions[$region['Region']['region']] = $region['Region']['region'];
                }
                $this->set('regions', json_encode($arrRegions));

                $markets = $this->Market->find('all', array('order' => 'Country.country Asc'));
                foreach ($markets as $market)
                {
                        $arrMarkets[$market['Market']['market']] = $market['Market']['market'];
                        $cities = $this->City->find('list', array('fields' => array('City.city', 'City.city'), 'conditions' => array('City.country_id' => $market['Market']['country_id']), 'order' => 'City.city Asc'));
                        if(!empty($cities)) {
                                $arrCities[$market['Market']['market']] = $cities;
                        }
                }
                $this->set('markets', json_encode($arrMarkets));
                $this->set('cities', json_encode($arrCities));
                $this->set('services', json_encode($this->Service->find('list', array('fields' => array('Service.service_name', 'Service.service_name'), 'order' => 'Service.service_name Asc'))));
	}
        
        function search_client() {
                
                $this->autoRender=false;
                
                $arrData = $this->request->data;
                
                $searchResult = array();
                if (!empty($arrData['name_startsWith'])) {
                        $clients = $this->ClientRevenueByService->find('all', array('fields' => array('client_name', 'parent_company'), 'conditions' => ('client_name like \'' . $arrData['name_startsWith'] . '%\''), 'order' => 'client_name ASC'));
                        foreach ($clients as $client) {
                                $searchResult[] = array('advertiser_name' => $client['ClientRevenueByService']['client_name'], 'parent_company' => $client['ClientRevenueByService']['parent_company']);
                        }
                }
                return json_encode($searchResult);
        }

        public function save_client_record() {

                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                                //Configure::write('debug', 0);
                        }
                        
                        $arrData = $this->request->data;
                        //print_r($arrData);
                        
                        $category = $this->ClientCategory->findByCategory(trim($arrData['ClientCategory']));
                        $region = $this->Region->findByRegion(trim($arrData['Region']));
                        if(trim($arrData['Country']) == 'Global') {
                                $managingEntity = 'Global';
                                $country = null;
                                $city = null;
                        } else if(strpos(trim($arrData['Country']),'Regional') !== false) {
                                $managingEntity = 'Regional';
                                $country = null;
                                $city = null;
                        } else {
                                $managingEntity = 'Country';
                                $country = $this->Market->findByMarket(trim($arrData['Country']));
                                $city = $this->City->findByCity(trim($arrData['City']));
                        }
                        $agency = $this->LeadAgency->findByAgency(trim($arrData['LeadAgency']));
                        $currency = $this->Currency->findByCurrency(trim($arrData['Currency']));
                        $service = $this->Service->findByServiceName(trim($arrData['Service']));

                        if(!empty($category)) {
                                $categoryId = $category['ClientCategory']['id'];
                        } else {
                                $categoryId = 0;
                        }
                        if(!empty($region)) {
                                $regionId = $region['Region']['id'];
                        } else {
                                $regionId = 0;
                        }
                        if(!empty($country)) {
                                $countryId = $country['Market']['country_id'];
                        } else {
                                $countryId = 0;
                        }
                        if(!empty($city)) {
                                $cityId = $city['City']['id'];
                        } else {
                                $cityId = 0;
                        }
                        if(!empty($agency) > 0) {
                                $agencyId = $agency['LeadAgency']['id'];
                        } else {
                                $agencyId = 0;
                        }
                        if(!empty($currency) > 0) {
                                $currencyId = $currency['Currency']['id'];
                        } else {
                                $currencyId = 0;
                        }
                        if(!empty($service) > 0) {
                                $serviceId = $service['Service']['id'];
                        } else {
                                $serviceId = 0;
                        }
                        $pitchStage = $arrData['PitchStage'];
                        $pitchStart = explode('/', $arrData['PitchStart']);
                        $pitchDate = $pitchStart[1] . '-' . $pitchStart[0] . '-01';
                        $pitchLeader = $arrData['PitchLeader'];
                        $clientSinceMonth = $arrData['ClientSinceMonth'];
                        $clientSinceYear = $arrData['ClientSinceYear'];
                        $activeMarkets = $arrData['ActiveMarkets'];
                        $companyName = $arrData['ParentCompany'];
                        $clientName = $arrData['ClientName'];
                        $estimatedRevenue = $arrData['EstimatedRevenue'];
                        $comments = $arrData['Comments'];

                        $this->ClientRevenueByService->create();
                        $this->ClientRevenueByService->save(
                                array(
                                        'ClientRevenueByService' => array(
                                                'pitch_date' => $pitchDate,
                                                'pitch_leader' => $pitchLeader,
                                                'pitch_stage' => $pitchStage,
                                                'client_name' => $clientName,
                                                'parent_company' => $companyName,
                                                'comments' => $comments,
                                                'category_id' => $categoryId,
                                                'client_since_month' => $clientSinceMonth,
                                                'client_since_year' => $clientSinceYear,
                                                'agency_id' => $agencyId,
                                                'region_id' => $regionId,
                                                'managing_entity' => $managingEntity,
                                                'country_id' => $countryId,
                                                'city_id' => $cityId,
                                                'active_markets' => $activeMarkets,
                                                'service_id' => $serviceId,
                                                'currency_id' => $currencyId,
                                                'estimated_revenue' => $estimatedRevenue,
                                                'year' => date('Y')
                                        )
                                )
                        );
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }
        
        public function delete_client_record() {
                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                                //Configure::write('debug', 0);
                        }
                        
                        $arrData = $this->request->data;
	
                        if ($this->ClientRevenueByService->delete($arrData['RecordId'])) {
                                $result = array();
                                $result['success'] = true;
                                return json_encode($result);
                        }
                }
        }
        
        public function get_client_data() {
                $this->autoRender=false;
                
                $clientData = array();
                $i = 0;
                $conditions = array();
                if($this->Auth->user('role') == 'Regional') {
                        $region = $this->UserMarket->find('first', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $conditions['ClientRevenueByService.region_id'] = $region['UserMarket']['market_id'];
                }
                if($this->Auth->user('role') == 'Country') {
                        $countries = $this->UserMarket->find('all', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $arrCountries = array();
                        foreach($countries as $country) {
                                $arrCountries[] = $country['UserMarket']['market_id'];
                        }
                        $conditions['ClientRevenueByService.country_id'] = $arrCountries;
                }
                $this->ClientRevenueByService->Behaviors->attach('Containable');
                $clients = $this->ClientRevenueByService->find('all', array('conditions' => $conditions, 'order' => 'ClientRevenueByService.client_name Asc'));

                foreach($clients as $client) {
                        $clientData[$i]['id'] = $client['ClientRevenueByService']['id'];
                        $clientData[$i]['RecordId'] = $client['ClientRevenueByService']['id'];
                        $clientData[$i]['Region'] = $client['Region']['region'];
                        if ($client['ClientRevenueByService']['managing_entity'] == 'Global') {
                                $clientData[$i]['Country'] = 'Global';
                                $clientData[$i]['City'] = 'Global';
                        } elseif ($client['ClientRevenueByService']['managing_entity'] == 'Regional') {
                                $clientData[$i]['Country'] = 'Regional - ' . $client['Region']['region'];
                                $clientData[$i]['City'] = 'Regional - ' . $client['Region']['region'];
                        } else {
                                $managingEntity = $this->Market->find('first', array('conditions' => array('Market.country_id' => $client['ClientRevenueByService']['country_id'])));
                                $clientData[$i]['Country'] = $managingEntity['Market']['market'];
                                $clientData[$i]['City'] = $client['City']['city'];
                        }
                        $clientData[$i]['LeadAgency'] = $client['LeadAgency']['agency'];
                        $clientData[$i]['ClientName'] = $client['ClientRevenueByService']['client_name'];
                        $clientData[$i]['ParentCompany'] = $client['ClientRevenueByService']['parent_company'];
                        $clientData[$i]['ClientCategory'] = $client['ClientCategory']['category'];
                        if($client['ClientRevenueByService']['pitch_date'] != '0000-00-00') {
                                $pitchDate = explode('-', $client['ClientRevenueByService']['pitch_date']);
                                $clientData[$i]['PitchStart'] = $pitchDate[1] . '/' . $pitchDate[0];
                        } else {
                                $clientData[$i]['PitchStart'] = '';
                        }
                        $clientData[$i]['PitchLeader'] = $client['ClientRevenueByService']['pitch_leader'];
                        $clientData[$i]['PitchStage'] = $client['ClientRevenueByService']['pitch_stage'];
                        if($client['ClientRevenueByService']['lost_date'] != '0000-00-00') {
                                $lostDate = explode('-', $client['ClientRevenueByService']['lost_date']);
                                $clientData[$i]['Lost'] = $lostDate[1] . '/' . $lostDate[0];
                        } else {
                                $clientData[$i]['Lost'] = '';
                        }
                        $clientData[$i]['ClientMonth'] = $client['ClientRevenueByService']['client_since_month'];
                        $clientData[$i]['ClientYear'] = $client['ClientRevenueByService']['client_since_year'];
                        if($client['ClientRevenueByService']['client_since_month'] != 0 && $client['ClientRevenueByService']['client_since_year'] != 0) {
                                $clientData[$i]['ClientSince'] = $client['ClientRevenueByService']['client_since_month']. '/' .$client['ClientRevenueByService']['client_since_year'];
                        } else {
                                $clientData[$i]['ClientSince'] = '';
                        }
                        $clientData[$i]['Service'] = $client['Service']['service_name'];
                        $clientData[$i]['ActiveMarkets'] = $client['ClientRevenueByService']['active_markets'];
                        $clientData[$i]['Currency'] = $client['Currency']['currency'];
                        $clientData[$i]['EstimatedRevenue'] = $client['ClientRevenueByService']['estimated_revenue'];
                        $clientData[$i]['ActualRevenue'] = $client['ClientRevenueByService']['actual_revenue'];
                        $clientData[$i]['Comments'] = $client['ClientRevenueByService']['comments'];
                        
                        $i++;
                }
                echo json_encode($clientData);
        }
        
        public function update_client_record() {
                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                                //Configure::write('debug', 0);
                        }
                        
                        $arrData = $this->request->data;
                        //print_r($arrData);
                        
                        $recordId = $arrData['RecordId'];
                        
                        $category = $this->ClientCategory->findByCategory(trim($arrData['ClientCategory']));
                        $region = $this->Region->findByRegion(trim($arrData['Region']));
                        if(trim($arrData['Country']) == 'Global') {
                                $managingEntity = 'Global';
                                $country = null;
                                $city = null;
                        } else if(strpos(trim($arrData['Country']),'Regional') !== false) {
                                $managingEntity = 'Regional';
                                $country = null;
                                $city = null;
                        } else {
                                $managingEntity = 'Country';
                                $country = $this->Market->findByMarket(trim($arrData['Country']));
                                $city = $this->City->findByCity(trim($arrData['City']));
                        }
                        $agency = $this->LeadAgency->findByAgency(trim($arrData['LeadAgency']));
                        $currency = $this->Currency->findByCurrency(trim($arrData['Currency']));
                        $service = $this->Service->findByServiceName(trim($arrData['Service']));

                        if(!empty($category)) {
                                $categoryId = $category['ClientCategory']['id'];
                        } else {
                                $categoryId = 0;
                        }
                        if(!empty($region)) {
                                $regionId = $region['Region']['id'];
                        } else {
                                $regionId = 0;
                        }
                        if(!empty($country)) {
                                $countryId = $country['Market']['country_id'];
                        } else {
                                $countryId = 0;
                        }
                        if(!empty($city)) {
                                $cityId = $city['City']['id'];
                        } else {
                                $cityId = 0;
                        }
                        if(!empty($agency) > 0) {
                                $agencyId = $agency['LeadAgency']['id'];
                        } else {
                                $agencyId = 0;
                        }
                        if(!empty($currency) > 0) {
                                $currencyId = $currency['Currency']['id'];
                        } else {
                                $currencyId = 0;
                        }
                        if(!empty($service) > 0) {
                                $serviceId = $service['Service']['id'];
                        } else {
                                $serviceId = 0;
                        }
                        $pitchStage = trim($arrData['PitchStage']);
                        $pitchStart = explode('/', trim($arrData['PitchStart']));
                        $pitchDate = $pitchStart[1] . '-' . $pitchStart[0] . '-01';
                        $pitchLeader = trim($arrData['PitchLeader']);
                        $clientMonth = array_search(trim($arrData['ClientSinceMonth']), $this->months);
                        $clientYear = trim($arrData['ClientSinceYear']);
                        if(trim($arrData['LostDate']) != 'No' && trim($arrData['LostDate']) != '') {
                                $lost = explode('/', trim($arrData['LostDate']));
                                $lostDate = $lost[1] . '-' . $lost[0] . '-01';
                        } else {
                                $lostDate = '';
                        }
                        $activeMarkets = trim($arrData['ActiveMarkets']);
                        $companyName = trim($arrData['ParentCompany']);
                        $clientName = trim($arrData['ClientName']);
                        $estimatedRevenue = $arrData['EstimatedRevenue'];
                        $actualRevenue = $arrData['ActualRevenue'];
                        $comments = trim($arrData['Comments']);

                        $this->ClientRevenueByService->id = $recordId;
                        $this->ClientRevenueByService->save(
                                array(
                                        'ClientRevenueByService' => array(
                                                'pitch_date' => $pitchDate,
                                                'pitch_leader' => $pitchLeader,
                                                'pitch_stage' => $pitchStage,
                                                'client_name' => $clientName,
                                                'parent_company' => $companyName,
                                                'comments' => $comments,
                                                'category_id' => $categoryId,
                                                'agency_id' => $agencyId,
                                                'region_id' => $regionId,
                                                'managing_entity' => $managingEntity,
                                                'country_id' => $countryId,
                                                'city_id' => $cityId,
                                                'client_since_month' => $clientMonth,
                                                'client_since_year' => $clientYear,
                                                'lost_date' => $lostDate,
                                                'active_markets' => $activeMarkets,
                                                'service_id' => $serviceId,
                                                'currency_id' => $currencyId,
                                                'estimated_revenue' => $estimatedRevenue,
                                                'actual_revenue' => $actualRevenue,
                                                'year' => date('Y')
                                        )
                                )
                        );
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }
        
        public function client_report() {
                
                $this->set('currencies', json_encode($this->Currency->find('list', array('fields' => array('Currency.convert_rate', 'Currency.currency'), 'order' => 'Currency.currency Asc'))));

                $this->set('loggedUser', $this->Auth->user());    
                $this->set('userAcl', $this->Acl);
        }
        
        public function export_client_data() {
                set_time_limit(0);
                ini_set('memory_limit', '-1');
                
                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                                //Configure::write('debug', 0);
                        }
                        
                        $arrData = $this->request->data;
                        
                        App::import('Vendor', 'PHPExcel', array('file' => 'PhpExcel/PHPExcel.php'));
                        if (!class_exists('PHPExcel')) {
                                throw new CakeException('Vendor class PHPExcel not found!');
                        }
                        App::import('Vendor', 'PHPExcel_Writer_Excel2007', array('file' => 'PhpExcel/PHPExcel/Writer/Excel2007.php'));
                        if (!class_exists('PHPExcel_Writer_Excel2007')) {
                                throw new CakeException('Vendor class PHPExcel not found!');
                        }
                        
                        $objPHPExcel = new PHPExcel();

                        // Set properties
                        $objPHPExcel->getProperties()->setCreator("Siddharth Kulkarni");
                        $objPHPExcel->getProperties()->setLastModifiedBy("Siddharth Kulkarni");
                        $objPHPExcel->getProperties()->setTitle("Client Data by date " . date('m/d/Y'));
                        $objPHPExcel->getProperties()->setSubject("Client Data by date " . date('m/d/Y'));
                        //$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");


                        // Add some data
                        //echo date('H:i:s') . " Add some data\n";
                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Region');
                        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Managing Entity');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Managing City');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Lead Agency');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Client');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Parent Company');
                        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Client Category');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Pitch Start');
                        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Pitch Leader');
                        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Stage');
                        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Client Since (M-Y)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Lost (M-Y)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Service');
                        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Active Markets');
                        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Currency');
                        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Estimated Annual Revenue');
                        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Actual Annual Revenue');
                        $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Comments');

                        $i = 2;
                        foreach($arrData as $data) {
                                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $data['Region']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, $data['Country']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, $data['City']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $data['LeadAgency']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, $data['ClientName']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $data['ParentCompany']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, $data['ClientCategory']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $data['PitchStart']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, $data['PitchLeader']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, $data['PitchStage']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, $data['ClientSince']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, $data['Lost']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, $data['Service']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, $data['ActiveMarkets']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, $data['Currency']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, $data['EstimatedRevenue']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, $data['ActualRevenue']);
                                $objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, $data['Comments']);
                                $i++;
                        }
                        
                        // Rename sheet
                        //echo date('H:i:s') . " Rename sheet\n";
                        $objPHPExcel->getActiveSheet()->setTitle('Client List');


                        // Save Excel 2007 file
                        //echo date('H:i:s') . " Write to Excel2007 format\n";
                        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                        $objWriter->save('files/Client_Data_' . date('m-d-Y') . '.xlsx');
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }
        
        public function client_report_new() {
                
        }
        
        public function office_data() {
                
                $arrKeyDepts = array('Executive' => 'executive', 'FinanceHead' => 'finance_head', 'ProductHead' => 'product_head', 'StrategyHead' => 'strategy_head', 'ClientHead' => 'client_head', 'BusinessHead' => 'business_head', 'MarketingHead' => 'marketing_head');
                $arrServices = $this->Service->find('list', array('fields' => array('Service.id', 'Service.service_name'), 'order' => 'Service.service_name Asc'));
                $arrLanguages = $this->Language->find('list', array('fields' => array('Language.id', 'Language.language'), 'order' => 'Language.language Asc'));
                
                $this->set('departments', $arrKeyDepts);
                $this->set('services', $arrServices);
                $this->set('languages', $arrLanguages);
        }
        
        public function get_office_data() {
                $this->autoRender=false;
                
                $officeData = array();
                $arrKeyDepts = array('Executive' => 'executive', 'FinanceHead' => 'finance_head', 'ProductHead' => 'product_head', 'StrategyHead' => 'strategy_head', 'ClientHead' => 'client_head', 'BusinessHead' => 'business_head', 'MarketingHead' => 'marketing_head');
                $arrServices = array(1 => 'Affiliates', 2 => 'Content', 3 => 'Conversion', 4 => 'Data', 5 => 'Development', 6 => 'Display', 7 => 'Feeds', 8 => 'Lead', 9 => 'Mobile', 10 => 'RTB', 11 => 'Search', 12 => 'SEO', 13 => 'SocialPaid', 14 => 'SocialMangement', 15 => 'Strategy', 16 => 'Technology', 17 => 'Video');
                $arrLanguages = $this->Language->find('list', array('fields' => array('Language.id', 'Language.language'), 'order' => 'Language.language Asc'));
                
                $i = 0;
                $conditions = array();
                if($this->Auth->user('role') == 'Regional') {
                        $region = $this->UserMarket->find('first', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $conditions['Office.region_id'] = $region['UserMarket']['market_id'];
                }
                if($this->Auth->user('role') == 'Country') {
                        $countries = $this->UserMarket->find('all', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $arrCountries = array();
                        foreach($countries as $country) {
                                $arrCountries[] = $country['UserMarket']['market_id'];
                        }
                        $conditions['Office.country_id'] = $arrCountries;
                }
                $this->Office->Behaviors->attach('Containable');
                $offices = $this->Office->find('all', array('conditions' => $conditions, 'order' => 'Office.id Asc'));
                //echo '<pre>'; print_r($offices);
                foreach($offices as $office) {
                        $officeData[$i]['RecordId'] = $office['Office']['id'];
                        $officeData[$i]['Region'] = $office['Region']['region'];
                        $officeData[$i]['Country'] = $office['Country']['country'];
                        $officeData[$i]['City'] = $office['City']['city'];
                        
                        $officeData[$i]['YearEstablished'] = $office['Office']['year_established'];
                        $officeData[$i]['TotalEmployee'] = $office['Office']['employee_count'];
                        $officeData[$i]['Address'] = $office['Office']['address'];
                        
                        $arrTelephones = array();
                        $arrEmails = array();
                        $arrWebsites = array();
                        $arrSocialAccounts = array();
                        foreach($office['OfficeAttribute'] as $officeAttribute) {
                                if($officeAttribute['attribute_type'] == 'telephone') {
                                        $arrTelephones[] = $officeAttribute['attribute_value'];
                                }
                                if($officeAttribute['attribute_type'] == 'contact_email') {
                                        $arrEmails[] = $officeAttribute['attribute_value'];
                                }
                                if($officeAttribute['attribute_type'] == 'website') {
                                        $arrWebsites[] = $officeAttribute['attribute_value'];
                                }
                                if($officeAttribute['attribute_type'] == 'social_account') {
                                        $arrSocialAccounts[] = $officeAttribute['attribute_value'];
                                }
                        }
                        if(!empty($arrTelephones)) {
                                $officeData[$i]['Telephone'] = implode("\n", $arrTelephones);
                        } else {
                                $officeData[$i]['Telephone'] = '';
                        }
                        if(!empty($arrEmails)) {
                                $officeData[$i]['GeneralEmail'] = implode("\n", $arrEmails);
                        } else {
                                $officeData[$i]['GeneralEmail'] = '';
                        }
                        if(!empty($arrWebsites)) {
                                $officeData[$i]['Website'] = implode("\n", $arrWebsites);
                        } else {
                                $officeData[$i]['Website'] = '';
                        }
                        if(!empty($arrSocialAccounts)) {
                                $officeData[$i]['SocialAccount'] = implode("\n", $arrSocialAccounts);
                        } else {
                                $officeData[$i]['SocialAccount'] = '';
                        }
                        
                        $arrKeyEmployeeCount = array();
                        $totalKeyEmpCount = 0;
                        $totalServiceEmpCount = 0;
                        $arrServiceEmployeeCount = array();
                        foreach($office['OfficeEmployeeCountByDepartment'] as $officeEmployeeCountByDepartment) {
                                if($officeEmployeeCountByDepartment['department_type'] == 'service') {
                                        if($officeEmployeeCountByDepartment['count_type'] == 'FTE') {
                                                $arrServiceEmployeeCount[$officeEmployeeCountByDepartment['department_id']] = round(($officeEmployeeCountByDepartment['employee_count']*100),2);
                                        } else {
                                                $arrServiceEmployeeCount[$officeEmployeeCountByDepartment['department_id']] = round($officeEmployeeCountByDepartment['employee_count'],2);
                                        }
                                        $totalKeyEmpCount += $officeEmployeeCountByDepartment['employee_count'];
                                } else {
                                        if($officeEmployeeCountByDepartment['count_type'] == 'FTE') {
                                                $arrKeyEmployeeCount[$officeEmployeeCountByDepartment['department_type']] = round(($officeEmployeeCountByDepartment['employee_count']*100),2);
                                        } else {
                                                $arrKeyEmployeeCount[$officeEmployeeCountByDepartment['department_type']] = round($officeEmployeeCountByDepartment['employee_count'],2);
                                        }
                                        $totalServiceEmpCount += $officeEmployeeCountByDepartment['employee_count'];
                                }
                        }
                        
                        $keyContacts = array();
                        foreach($office['OfficeKeyContact'] as $officeKeyContact) {
                                $keyContacts[$officeKeyContact['contact_type']][] = $officeKeyContact['contact_name'] . (!empty($officeKeyContact['contact_title']) ? '/' . $officeKeyContact['contact_title'] : '') . (!empty($officeKeyContact['contact_email']) ? '/' . $officeKeyContact['contact_email'] : '');
                        }
                        foreach($arrKeyDepts as $key => $keyDept) {
                                if(isset($keyContacts[$keyDept])) {
                                        $officeData[$i][$key] = implode("\n", $keyContacts[$keyDept]);
                                } else {
                                        $officeData[$i][$key] = '';
                                }
                                
                                if(isset($arrKeyEmployeeCount[$keyDept])) {
                                        $officeData[$i]['count'.$key] = $arrKeyEmployeeCount[$keyDept];
                                } else {
                                        $officeData[$i]['count'.$key] = '';
                                }
                        }
                        $officeData[$i]['totalKeyEmployeeCount'] = round($totalKeyEmpCount,2);

                        $serviceContacts = array();
                        foreach($office['OfficeServiceContact'] as $officeServiceContact) {
                                $serviceContacts[$officeServiceContact['service_id']][] = $officeServiceContact['contact_name'] . (!empty($officeServiceContact['contact_title']) ? '/' . $officeServiceContact['contact_title'] : '') . (!empty($officeServiceContact['contact_email']) ? '/' . $officeServiceContact['contact_email'] : '');
                        }
                        foreach($arrServices as $serviceId => $service) {
                                if(isset($serviceContacts[$serviceId])) {
                                        $officeData[$i][$service] = implode("\n", $serviceContacts[$serviceId]);
                                } else {
                                        $officeData[$i][$service] = '';
                                }
                                
                                if(isset($arrServiceEmployeeCount[$serviceId])) {
                                        $officeData[$i]['count'.$service] = $arrServiceEmployeeCount[$serviceId];
                                } else {
                                        $officeData[$i]['count'.$service] = '';
                                }
                        }
                        $officeData[$i]['totalServiceEmployeeCount'] = round($totalServiceEmpCount,2);
                        
                        $supportedLanguages = array();
                        foreach($office['OfficeLanguage'] as $officeLanguage) {
                                $supportedLanguages[] = $arrLanguages[$officeLanguage['language_id']];
                        }
                        if(!empty($supportedLanguages)) {
                                $officeData[$i]['SupportedLanguages'] = implode(', ', $supportedLanguages);
                                $officeData[$i]['countSupportedLanguages'] = count($supportedLanguages);
                        } else {
                                $officeData[$i]['SupportedLanguages'] = '';
                                $officeData[$i]['countSupportedLanguages'] = '';
                        }
                        $officeData[$i]['RecentAwards'] = $office['Office']['recent_awards'];
                        $officeData[$i]['News'] = $office['Office']['news'];
                        
                        $i++;
                }
                echo json_encode($officeData);
        }
}
