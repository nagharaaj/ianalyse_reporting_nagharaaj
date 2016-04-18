<?php
class ExportDataController extends AppController {
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
            'PitchStage',
            'ClientRevenueByService',
        );

        public $unwantedArray = array( 'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '\''=>'', '"'=>'', ' '=>'', '`'=>'' );

        public function export_to_dan_format() {
                set_time_limit(0);
                ini_set('memory_limit', '-1');

                $pitchStatusMappings = $this->PitchStage->find('list', array('fields' => array('PitchStage.pitch_stage', 'PitchStage.dan_mapping')));
                $currencies = $this->Currency->find('list', array('fields' => array('Currency.id', 'Currency.convert_rate')));
                $services = $this->Service->find('list', array('fields' => array('Service.id', 'Service.service_name')));
                $cities = $this->City->find('list', array('fields' => array('City.id', 'City.city')));

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
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->getStyle("A1:AZ1")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 11, 'name'  => 'Calibri', 'color' => array('rgb' => 'FFFFFF')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS, 'wrap' => false), 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
                $objPHPExcel->getActiveSheet()->getStyle('A1:AZ1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('305496');
                for($i = 'A'; $i !== 'BA'; $i++) {
                        $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
                }
                $arrDataExcel = array();
                $arrDataExcel[] = array('Title', 'Client', 'Country', 'Industry Sector', 'Client Notes', 'Pitch Reference Number', 'Pitch Name',
                    'Client Requirements', 'Type of Pitch', 'Date Pitch Raised', 'Global Pitch Split', 'Global Pitch Association', 'Annual Billings',
                    'Annual Billing (GBP)', 'Annual Billing (USD)', 'Est. Annual Revenue', 'Est. Annual Revenue (GBP)', 'Est. Annual Revenue (USD)',
                    'Turnover (m) (GBP)', 'Turnover (m) (USD)', 'Pitch Status', 'Pitch Stage', 'Pitch Start Date', 'Potential Renewal Date', 'Network Brand',
                    'Lead Country', 'Pitch Reason', 'Pitch Logged in Billing System', 'Publish Pitch Externally', 'Lessons Learnt', 'Cities', 'Region',
                    'Client Holding Company', 'Sub-Region', 'Financial Threshold', 'Pitch Closed Date', 'Holding Brand Name', 'Multiple Networks Involved',
                    'Scope', 'Next Key Pitch Date', 'Turnover', 'Support Network', 'Type of Network', 'Archive Pitch', 'Pitch Win/Loss Comment',
                    'Pitch Lead(s)', 'Pitch Originiator', 'Incumbant Agency', 'Competitor Agency', 'SnapShot Date', 'Other Country(s) Involved', 'Services');

                $clients = $this->ClientRevenueByService->find('all', array(
                    'fields' => array(
                        'ClientRevenueByService.client_name', 'ClientRevenueByService.parent_company', 'ClientRevenueByService.pitch_date',
                        'ClientRevenueByService.pitch_stage', 'ClientRevenueByService.client_since_month', 'ClientRevenueByService.client_since_year',
                        'ClientRevenueByService.lost_date', 'ClientRevenueByService.agency_id', 'group_concat(NULLIF(ClientRevenueByService.comments,"")) as comments',
                        'group_concat(ClientRevenueByService.estimated_revenue) as estimated_revenue', 'group_concat(ClientRevenueByService.currency_id) as currency_id',
                        'group_concat(NULLIF(ClientRevenueByService.active_markets,"")) as active_markets', 'group_concat(ClientRevenueByService.service_id) as service_id',
                        'group_concat(NULLIF(ClientRevenueByService.city_id,"")) as city_id',
                        'LeadAgency.agency',
                        'Country.country',
                        'ClientCategory.dan_mapping',
                    ),
                    'conditions' => array(
                        "(pitch_stage like 'Live%' or pitch_stage like 'Won%' or pitch_stage like 'Lost%' or pitch_stage='Cancelled' or pitch_stage='Declined') and pitch_stage != 'Lost - archive'"
                    ),
                    'group' => array('ClientRevenueByService.client_name', 'ClientRevenueByService.country_id', 'ClientRevenueByService.pitch_stage'),
                    'order' => 'ClientRevenueByService.client_name asc, ClientRevenueByService.pitch_stage asc, ClientRevenueByService.pitch_date desc'
                ));
                //echo '<pre>'; print_r($clients);

                $dtCreated = date('m/d/Y');
                $i = 1;
                foreach($clients as $client) {
                        if($client['Country']['country'] == 'United States') {
                                $country = 'United States of America';
                        } else {
                                $country = $client['Country']['country'];
                        }
                        $arrComments = array_unique(explode(',', $client[0]['comments']));
                        $pitchStatus = $pitchStatusMappings[$client['ClientRevenueByService']['pitch_stage']];
                        if (preg_match('/Live/', $client['ClientRevenueByService']['pitch_stage'])) {
                                $pitchStage = '';
                        } else {
                                $pitchStage = 'Closed';
                        }
                        if($client['ClientRevenueByService']['pitch_date'] != '0000-00-00') {
                                $arrPitchDate = explode('-', $client['ClientRevenueByService']['pitch_date']);
                                $pitchDate = date('m/d/Y', mktime(0, 0, 0, $arrPitchDate[1], $arrPitchDate[2], $arrPitchDate[0]));
                        } else {
                                $pitchDate = '';
                        }
                        if(preg_match('/Won/', $client['ClientRevenueByService']['pitch_stage'])) {
                                if($client['ClientRevenueByService']['client_since_year'] != null) {
                                        if($client['ClientRevenueByService']['client_since_month'] != null) {
                                                $pitchCloseDate = date('m/d/Y', mktime(0, 0, 0, $client['ClientRevenueByService']['client_since_month'], 1, $client['ClientRevenueByService']['client_since_year']));
                                        } else {
                                                $pitchCloseDate = date('m/d/Y', mktime(0, 0, 0, 1, 1, $client['ClientRevenueByService']['client_since_year']));
                                        }
                                } else {
                                        $pitchCloseDate = '';
                                }
                        } else if(preg_match('/Lost/', $client['ClientRevenueByService']['pitch_stage']) || $client['ClientRevenueByService']['pitch_stage'] == 'Cancelled' || $client['ClientRevenueByService']['pitch_stage'] == 'Declined') {
                                if($client['ClientRevenueByService']['lost_date'] != '0000-00-00') {
                                        $arrPitchCloseDate = explode('-', $client['ClientRevenueByService']['lost_date']);
                                        $pitchCloseDate = date('m/d/Y', mktime(0, 0, 0, $arrPitchCloseDate[1], $arrPitchCloseDate[2], $arrPitchCloseDate[0]));
                                } else {
                                        $pitchCloseDate = '';
                                }
                        } else {
                                $pitchCloseDate = '';
                        }
                        if($client['ClientRevenueByService']['agency_id'] == null || $client['LeadAgency']['agency'] == 'iProspect') {
                                $supportNetwork = '';
                                $multipleNetworksInvolved = '';
                        } else {
                                $supportNetwork = $client['LeadAgency']['agency'];
                                $multipleNetworksInvolved = 'Yes';
                        }
                        $estimatedRevenue = 0;
                        $arrEstRevenue = explode(',', $client[0]['estimated_revenue']);
                        $arrCurrency = explode(',', $client[0]['currency_id']);
                        foreach($arrEstRevenue as $index => $estRevenue) {
                                if($estRevenue != 0) {
                                        if($arrCurrency[$index] != null) {
                                                $estimatedRevenue +=  ($estRevenue * $currencies[$arrCurrency[$index]]);
                                        } else {
                                                $estimatedRevenue +=  $estRevenue;
                                        }
                                }
                        }
                        if($client[0]['active_markets'] != null) {
                                $activeMarkets = array_unique(explode(',', $client[0]['active_markets']));
                                if (($key = array_search($client['Country']['country'], $activeMarkets)) !== false) {
                                        unset($activeMarkets[$key]);
                                }
                                if(count($activeMarkets) > 1) {
                                        $scope = 'Multi-Market';
                                } else {
                                        $scope = 'Local';
                                }
                        } else {
                                $activeMarkets = array();
                                $scope = '';
                        }
                        $arrServices = array();
                        $arrServiceId = array_unique(explode(',', $client[0]['service_id']));
                        foreach($arrServiceId as $serviceId) {
                                $arrServices[] = $services[$serviceId];
                        }
                        $arrCities = array();
                        $arrCityId = array_unique(explode(',', $client[0]['city_id']));
                        foreach($arrCityId as $cityId) {
                                if($cityId != null) {
                                        $arrCities[] = $cities[$cityId];
                                }
                        }

                        $arrDataExcel[] = array('', $client['ClientRevenueByService']['client_name'], $country,
                            $client['ClientCategory']['dan_mapping'], implode(', ', $arrComments), '', '', '', 'Contract', $dtCreated,
                            '', '', '', '', '', '', '', $estimatedRevenue, '', '', $pitchStatus, $pitchStage,
                            $pitchDate, '', 'iProspect', $country, '', '', '', '',
                            implode(', ', $arrCities), '', $client['ClientRevenueByService']['parent_company'], '', '', $pitchCloseDate,
                            'iProspect', $multipleNetworksInvolved, $scope, '', '', $supportNetwork, 'Digital and Creative', 'No', '', '', '',
                            '', '', '', implode(', ', $activeMarkets), implode(', ', $arrServices));
                        $i++;
                }
                $objPHPExcel->getActiveSheet()->fromArray($arrDataExcel, null, 'A1');
                $objPHPExcel->getActiveSheet()->setAutoFilter('A1:AZ'.$i);

                // Rename sheet
                $objPHPExcel->getActiveSheet()->setTitle('Digital & Creative');

                // Save Excel 2007 file
                $fileName = 'DAN_Client_Data_' . date('m-d-Y') . '.xlsx';
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                $objWriter->save('files/' . $fileName);
        }

        public function get_data_structure () {
                $this->autoRender=false;

                $url = "http://team.test.dentsuaegis.com/sites/nbr/_api/web";
                //$url = "http://team.test.dentsuaegis.com/sites/nbr/AUT/_api/web/lists/getbytitle('Pitch')/items";
                //$url = "http://team.test.dentsuaegis.com/sites/nbr/_api/web/lists(guid'05044235-92a2-442b-ba8d-959ce4859c51')/items";
                //$url = "http://team.test.dentsuaegis.com/sites/NBR/_vti_bin/listdata.svc/Client(111)/IndustryCateogry";
                //$url = "http://team.test.dentsuaegis.com/sites/nbr/_api/web/lists/getbytitle('Country')/items";
                //$filter = urlencode('((Title eq \'Austria Client A\' or startswith(Title,\'Austria Client\')) or (DACltHoldCompany eq \'Austria Client A\' or startswith(DACltHoldCompany,\'Austria Client\')))');
                //$url = 'team.test.dentsuaegis.com/sites/nbr/_api/web/lists/getByTitle(\'Client\')/items?$select=Id,Title,DACltHoldCompany,DACountry/Title,DAIndustryCateogry/Title,DAIndustryCateogry/Id&$expand=DACountry,DAIndustryCateogry&$filter=' . $filter;
                //$url = 'team.test.dentsuaegis.com/sites/nbr/_api/web/lists/getbytitle(\'Country\')/items?$select=Id,Title,DACountryCode&$filter=' . urlencode('Title eq \'Argentina\'');

                //$data = "{Title: 'AB InBev', ClientHolidngCompany: 'AB InBev', Country: {Title: 'United Kingdom'}, IndustryCateogry: {Title: 'Alcoholic Drinks'}, TypeOfNetworkValue: 'Digital and Creative'}";
                //$url = 'team.test.dentsuaegis.com/sites/nbr/_vti_bin/listdata.svc/Client';

                //$cookie = tempnam ("/tmp", "CURLCOOKIE");
                $ch = curl_init();
                //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                curl_setopt( $ch, CURLOPT_URL, $url );
                //curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);

                /* for post operation */
                //curl_setopt($ch, CURLOPT_POST, true);
                //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type: application/json", "accept: application/json;odata=verbose"));
                $content = curl_exec( $ch );
                $response = curl_getinfo( $ch );
                curl_close ( $ch );

                print_r( array( $content, $response ) );
                echo '<pre>'; print_r(json_decode($content)); echo '</pre>';
                //$xml = simplexml_load_string($content) or die("Error: Cannot create object");
                //echo '<pre>'; print_r($xml); echo '</pre>';
        }

        public function sync_client_data() {
                $this->autoRender=false;

                $ch = curl_init();
                //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                //curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json;odata=verbose"));

                $ch1 = curl_init();
                //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                //curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch1, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch1, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch1, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch1, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                curl_setopt($ch1, CURLOPT_HTTPHEADER, array("content-type: application/json", "accept: application/json;odata=verbose"));
                curl_setopt($ch1, CURLOPT_POST, true);

                $clients = $this->ClientRevenueByService->find('all', array(
                    'fields' => array(
                        'ClientRevenueByService.client_name', 'ClientRevenueByService.parent_company', 'Country.country', 'ClientCategory.dan_mapping'
                    ),
                    'conditions' => array(
                        "(pitch_stage like 'Live%' or pitch_stage like 'Won%' or pitch_stage like 'Lost%' or pitch_stage='Cancelled' or pitch_stage='Declined') and pitch_stage != 'Lost - archive'"
                    ),
                    'group' => array('ClientRevenueByService.client_name', 'ClientRevenueByService.parent_company'),
                    'order' => 'ClientRevenueByService.client_name asc',
                    'limit' => '15'
                ));

                //echo '<pre>'; print_r($clients);

                foreach($clients as $client) {
                        $apiUrl = 'team.test.dentsuaegis.com/sites/nbr/_api/web/lists/GetByTitle(\'Client\')/items';
                        $select = 'Title,DACltHoldCompany,DAIndustryCateogry,DACountry/Title,DAIndustryCateogry/Title';
                        $expand = 'DACountry,DAIndustryCateogry';
                        $exactCientName = $client['ClientRevenueByService']['client_name'];
                        $rowClientName = strtr( $client['ClientRevenueByService']['client_name'], $this->unwantedArray );
                        $filter = urlencode('((Title eq \'' . $exactCientName . '\' or substringof(\'' . $rowClientName . '\', Title)) or (DACltHoldCompany eq \'' . $exactCientName . '\' or substringof(\'' . $rowClientName . '\', DACltHoldCompany)))');
                        $url = $apiUrl. '?$select=' . $select. '&$expand=' . $expand . '&$filter=' . $filter;
                        curl_setopt( $ch, CURLOPT_URL, $url );
                        $content = json_decode(curl_exec( $ch ));
                        $response = curl_getinfo( $ch );
                        //print_r( array( $content, $response ) );
                        //echo '<pre>'; print_r($content); echo '</pre>';
                        $result = $content->d;
                        echo '<pre>'; print_r($result); echo '</pre>';
                        if(empty($result->results)) {
                                echo 'generating post request...<br/>';
                                if($client['Country']['country'] == 'USA') {
                                        $country = 'United States of America';
                                } else {
                                        $country = $client['Country']['country'];
                                }
                                $data = "{Title: '".$client['ClientRevenueByService']['client_name']."', ClientHolidngCompany: '".$client['ClientRevenueByService']['parent_company']."', Country: {Title: '".$country."'}, IndustryCateogry: {Title: '".$client['ClientCategory']['dan_mapping']."'}, TypeOfNetworkValue: 'Digital and Creative'}";
                                $url1 = 'team.test.dentsuaegis.com/sites/nbr/_vti_bin/listdata.svc/Client';
                                curl_setopt($ch1, CURLOPT_POSTFIELDS, $data);
                                curl_setopt( $ch1, CURLOPT_URL, $url1 );
                                $savedContent = json_decode(curl_exec( $ch1 ));
                                echo '<pre>'; print_r($savedContent); echo '</pre>';
                        }
                }
                curl_close ( $ch );
                curl_close ( $ch1 );
        }

        public function sync_daily_pitch_data() {
                $this->autoRender=false;

                $currDt = date('Y-m-d');
                $lastMonthDt = date('Y-m-d', strtotime('-30 days'));

                $siteUrl = 'team.test.dentsuaegis.com/sites/nbr/';
                $pitchStatusMappings = $this->PitchStage->find('list', array('fields' => array('PitchStage.pitch_stage', 'PitchStage.dan_mapping')));
                $currencies = $this->Currency->find('list', array('fields' => array('Currency.id', 'Currency.convert_rate')));
                $services = $this->Service->find('list', array('fields' => array('Service.id', 'Service.service_name')));
                $cities = $this->City->find('list', array('fields' => array('City.id', 'City.city')));

                $ch = curl_init();
                //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                //curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json;odata=verbose"));

                $ch1 = curl_init();
                //curl_setopt( $ch1, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                //curl_setopt( $ch1, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch1, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch1, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch1, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch1, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                curl_setopt($ch1, CURLOPT_HTTPHEADER, array("content-type: application/json", "accept: application/json;odata=verbose"));
                curl_setopt($ch1, CURLOPT_POST, true);
                
                $ch2 = curl_init();
                //curl_setopt( $ch2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                //curl_setopt( $ch2, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch2, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch2, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch2, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                curl_setopt($ch2, CURLOPT_HTTPHEADER, array("content-type: application/json", "accept: application/json;odata=verbose"));
                curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'PUT');

                $ch3 = curl_init();
                //curl_setopt( $ch3, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                //curl_setopt( $ch3, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch3, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch3, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch3, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch3, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                curl_setopt($ch3, CURLOPT_HTTPHEADER, array("content-type: application/json", "accept: application/json;odata=verbose"));
                curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, 'DELETE');

                $arrPitchStatus = array();
                $pitchStatusUrl = $siteUrl . "_api/web/lists(guid'c480924f-528a-42b9-99ed-0414d9a63dc3')/items";
                curl_setopt( $ch, CURLOPT_URL, $pitchStatusUrl );
                $pitchStatusContent = json_decode(curl_exec( $ch ));
                $pitchStatusResult = $pitchStatusContent->d->results;
                foreach($pitchStatusResult as $result) {
                        $arrPitchStatus[$result->Id] = $result->Title;
                }

                $arrPitchStage = array();
                $pitchStageUrl = $siteUrl . "_api/web/lists(guid'2c6f3309-2fd7-4a44-8978-ae758345744d')/items";
                curl_setopt( $ch, CURLOPT_URL, $pitchStageUrl );
                $pitchStageContent = json_decode(curl_exec( $ch ));
                $pitchStageResult = $pitchStageContent->d->results;
                foreach($pitchStageResult as $result) {
                        $arrPitchStage[$result->Id] = $result->Title;
                }

                $arrNetworkBrand = array();
                $networkBrandUrl = $siteUrl . "_api/web/lists(guid'070e49a1-13d3-4f6b-8f12-09f0f7c6bf6c')/items";
                curl_setopt( $ch, CURLOPT_URL, $networkBrandUrl );
                $networkBrandContent = json_decode(curl_exec( $ch ));
                $networkBrandResult = $networkBrandContent->d->results;
                foreach($networkBrandResult as $result) {
                        $arrNetworkBrand[$result->Id] = $result->Title;
                }
                
                $arrIndustryCategory = array();
                $industryCategoryUrl = $siteUrl . "_api/web/lists(guid'05044235-92a2-442b-ba8d-959ce4859c51')/items";
                curl_setopt( $ch, CURLOPT_URL, $industryCategoryUrl );
                $industryCategoryContent = json_decode(curl_exec( $ch ));
                $industryCategoryResult = $industryCategoryContent->d->results;
                foreach($industryCategoryResult as $result) {
                        $arrIndustryCategory[$result->Id] = $result->Title;
                }

                $clients = $this->ClientRevenueByService->find('all', array(
                    'fields' => array(
                        'ClientRevenueByService.client_name', 'ClientRevenueByService.parent_company', 'ClientRevenueByService.pitch_date',
                        'ClientRevenueByService.pitch_stage', 'ClientRevenueByService.client_since_month', 'ClientRevenueByService.client_since_year',
                        'ClientRevenueByService.lost_date', 'ClientRevenueByService.agency_id', 'group_concat(NULLIF(ClientRevenueByService.comments,"")) as comments',
                        'date_format(ClientRevenueByService.created, "%Y-%m-%d") as created',
                        'date_format(ClientRevenueByService.modified, "%Y-%m-%d") as modified',
                        'group_concat(ClientRevenueByService.estimated_revenue) as estimated_revenue', 'group_concat(ClientRevenueByService.currency_id) as currency_id',
                        'group_concat(NULLIF(ClientRevenueByService.active_markets,"")) as active_markets', 'group_concat(ClientRevenueByService.service_id) as service_id',
                        'group_concat(NULLIF(ClientRevenueByService.city_id,"")) as city_id',
                        'LeadAgency.agency',
                        'Country.country',
                        'ClientCategory.dan_mapping',
                    ),
                    'conditions' => array(
                        "(pitch_stage like 'Live%' or pitch_stage like 'Won%' or pitch_stage like 'Lost%' or pitch_stage='Cancelled' or pitch_stage='Declined') and pitch_stage != 'Lost - archive'",
                        //"(ClientRevenueByService.created like '" .date('Y-m-d'). "%' or ClientRevenueByService.modified like '" .date('Y-m-d'). "%')",
                        "ClientRevenueByService.created between '" . $lastMonthDt . "' and '" . $currDt . "' or ClientRevenueByService.modified between '" . $lastMonthDt . "' and '" . $currDt . "'",
                        "Country.country IN ('United States', 'United Kingdom', 'France', 'Germany', 'Spain', 'Singapore', 'Australia', 'Mexico', 'Poland')"
                    ),
                    'group' => array('ClientRevenueByService.client_name', 'ClientRevenueByService.country_id', 'ClientRevenueByService.pitch_stage'),
                    'order' => 'Country.country', 'ClientRevenueByService.client_name asc, ClientRevenueByService.pitch_stage asc, ClientRevenueByService.pitch_date desc'
                    //'limit' => 10
                ));
                echo '<pre>'; print_r($clients);

                $arrCountryCode = array();
                $arrCountryId = array();
                foreach($clients as $client) {
                        if($client['Country']['country'] == 'United States') {
                                $country = 'United States of America';
                        } else {
                                $country = $client['Country']['country'];
                        }

                        if(array_key_exists($country, $arrCountryCode) === false) {
                                $countryCodeUrl = $siteUrl . '_api/web/lists/getbytitle(\'Country\')/items?$select=Id,Title,DACountryCode&$filter=' . urlencode('Title eq \'' . $country . '\'');
                                curl_setopt( $ch, CURLOPT_URL, $countryCodeUrl );
                                $countryCodeData = json_decode(curl_exec( $ch ));
                                $arrCountryCode[$country] = $countryCodeData->d->results[0]->DACountryCode;
                                $arrCountryId[$country] = $countryCodeData->d->results[0]->Id;
                        }
                        $countryCode = $arrCountryCode[$country];
                        $countryId = $arrCountryId[$country];

                        $clientSearchUrl = $siteUrl . '_api/web/lists/GetByTitle(\'Client\')/items';
                        $clientSearchSelect = 'Id,Title,DACltHoldCompany,DAIndustryCateogry/Id';
                        $clientSearchExpand = 'DAIndustryCateogry';
                        $exactCientName = $client['ClientRevenueByService']['client_name'];
                        $rowClientName = strtr( $client['ClientRevenueByService']['client_name'], $this->unwantedArray );
                        $clientSearchFilter = urlencode("((Title eq '" . str_replace("'", "''", $exactCientName) . "' or substringof('" . $rowClientName . "', Title)) or (DACltHoldCompany eq '" . str_replace("'", "''", $exactCientName) . "' or substringof('" . $rowClientName . "', DACltHoldCompany)))");
                        $clientSearchUrl = $clientSearchUrl. '?$select=' . $clientSearchSelect. '&$expand=' . $clientSearchExpand . '&$filter=' . $clientSearchFilter;
                        curl_setopt( $ch, CURLOPT_URL, $clientSearchUrl );
                        $clientSearchContent = json_decode(curl_exec( $ch ));
                        $clientSearchResult = $clientSearchContent->d;
                        //echo '<pre>'; print_r($clientSearchContent); echo '</pre>';
                        if(empty($clientSearchResult->results)) {
                                $industryCategoryId = array_search($client['ClientCategory']['dan_mapping'], $arrIndustryCategory);
                                $newClientData = array(
                                    'Title' => $client['ClientRevenueByService']['client_name'],
                                    'ClientHolidngCompany' => $client['ClientRevenueByService']['parent_company'],
                                    'CountryId' => $countryId,
                                    'IndustryCateogryId' => $industryCategoryId,
                                    'TypeOfNetworkValue' => 'Digital and Creative'
                                );
                                $newClientUrl = $siteUrl . '_vti_bin/listdata.svc/Client';
                                curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($newClientData));
                                curl_setopt( $ch1, CURLOPT_URL, $newClientUrl );
                                $newClientContent = json_decode(curl_exec( $ch1 ));
                                //echo '<pre>'; print_r($newClientContent); echo '</pre>';
                                
                                $clientId = $newClientContent->d->Id;
                                $clientHolidngCompany = $client['ClientRevenueByService']['parent_company'];
                        } else {
                                $clientId = $clientSearchResult->results[0]->Id;
                                $clientHolidngCompany = $clientSearchResult->results[0]->DACltHoldCompany;
                                $industryCategoryId = array_search($client['ClientCategory']['dan_mapping'], $arrIndustryCategory);
                        }
                        
                        if($clientId != null) {
                                $arrComments = array_unique(explode(',', $client[0]['comments']));
                                $pitchStatus = $pitchStatusMappings[$client['ClientRevenueByService']['pitch_stage']];
                                if (preg_match('/Live/', $client['ClientRevenueByService']['pitch_stage'])) {
                                        $pitchStage = '';
                                } else {
                                        $pitchStage = 'Closed';
                                }
                                if($client[0]['created'] != '0000-00-00' && $client[0]['created'] != null) {
                                        $arrDtCreated = explode('-', $client[0]['created']);
                                        $dtCreated = date('c', mktime(0, 0, 0, $arrDtCreated[1], $arrDtCreated[2], $arrDtCreated[0]));
                                } else {
                                        $dtCreated = date('c');
                                }
                                if($client['ClientRevenueByService']['pitch_date'] != '0000-00-00') {
                                        $arrPitchDate = explode('-', $client['ClientRevenueByService']['pitch_date']);
                                        $pitchDate = date('c', mktime(0, 0, 0, $arrPitchDate[1], $arrPitchDate[2], $arrPitchDate[0]));
                                } else {
                                        $pitchDate = null;
                                }
                                if(preg_match('/Won/', $client['ClientRevenueByService']['pitch_stage'])) {
                                        if($client['ClientRevenueByService']['client_since_year'] != null) {
                                                if($client['ClientRevenueByService']['client_since_month'] != null) {
                                                        $pitchCloseDate = date('c', mktime(0, 0, 0, $client['ClientRevenueByService']['client_since_month'], 1, $client['ClientRevenueByService']['client_since_year']));
                                                } else {
                                                        $pitchCloseDate = date('c', mktime(0, 0, 0, 1, 1, $client['ClientRevenueByService']['client_since_year']));
                                                }
                                        } else {
                                                $pitchCloseDate = null;
                                        }
                                } else if(preg_match('/Lost/', $client['ClientRevenueByService']['pitch_stage']) || $client['ClientRevenueByService']['pitch_stage'] == 'Cancelled' || $client['ClientRevenueByService']['pitch_stage'] == 'Declined') {
                                        if($client['ClientRevenueByService']['lost_date'] != '0000-00-00') {
                                                $arrPitchCloseDate = explode('-', $client['ClientRevenueByService']['lost_date']);
                                                $pitchCloseDate = date('c', mktime(0, 0, 0, $arrPitchCloseDate[1], $arrPitchCloseDate[2], $arrPitchCloseDate[0]));
                                        } else {
                                                $pitchCloseDate = null;
                                        }
                                } else {
                                        $pitchCloseDate = null;
                                }
                                if($client['ClientRevenueByService']['agency_id'] == null || $client['LeadAgency']['agency'] == 'iProspect') {
                                        $supportNetwork = '';
                                        $multipleNetworksInvolved = false;
                                } else {
                                        $supportNetwork = $client['LeadAgency']['agency'];
                                        $multipleNetworksInvolved = true;
                                }
                                $estimatedRevenue = 0;
                                $arrEstRevenue = explode(',', $client[0]['estimated_revenue']);
                                $arrCurrency = explode(',', $client[0]['currency_id']);
                                foreach($arrEstRevenue as $index => $estRevenue) {
                                        if($estRevenue != 0) {
                                                if($arrCurrency[$index] != null) {
                                                        $estimatedRevenue +=  ($estRevenue * $currencies[$arrCurrency[$index]]);
                                                } else {
                                                        $estimatedRevenue +=  $estRevenue;
                                                }
                                        }
                                }
                                if($client[0]['active_markets'] != null) {
                                        $activeMarkets = array_unique(explode(',', $client[0]['active_markets']));
                                        if (($key = array_search($client['Country']['country'], $activeMarkets)) !== false) {
                                                unset($activeMarkets[$key]);
                                        }
                                        if(count($activeMarkets) > 1) {
                                                $scope = 'Multi-Market';
                                        } else {
                                                $scope = 'Local';
                                        }
                                } else {
                                        $activeMarkets = array();
                                        $scope = '';
                                }
                                $arrServices = array();
                                $arrServiceId = array_unique(explode(',', $client[0]['service_id']));
                                foreach($arrServiceId as $serviceId) {
                                        $arrServices[] = $services[$serviceId];
                                }
                                $arrCities = array();
                                $arrCityId = array_unique(explode(',', $client[0]['city_id']));
                                foreach($arrCityId as $cityId) {
                                        if($cityId != null) {
                                                $arrCities[] = $cities[$cityId];
                                        }
                                }
                                $typeOfPitch = 'Contract';
                                $networkBrand = 'iProspect';
                                $typeOfNetwork = 'Digital and Creative';
                                $isArchivePitch = false;
                                $pitchStatusId = array_search($pitchStatus, $arrPitchStatus);
                                $pitchStageId = (!empty($pitchStage) ? array_search($pitchStage, $arrPitchStage) : 0);
                                $networkBrandId = array_search($networkBrand, $arrNetworkBrand);

                                $pitchExistsUrl = $siteUrl . $countryCode .'/_api/web/lists/getbytitle(\'Pitch\')/items';
                                $pitchExistsFilter = urlencode('DACLient eq ' . $clientId . ' and DACountry eq ' . $countryId . ' and DAPitchStatus eq ' . $pitchStatusId);
                                $pitchExistsUrl = $pitchExistsUrl . '?$filter=' . $pitchExistsFilter;
                                curl_setopt( $ch, CURLOPT_URL, $pitchExistsUrl );
                                $pitchExistsContent = json_decode(curl_exec( $ch ));
                                $pitchExistsResult = $pitchExistsContent->d;
                                //echo '<pre>'; print_r($pitchExistsContent); echo '</pre>';
                                if(empty($pitchExistsResult->results)) {
                                        $data = array (
                                            'ClientId' => $clientId,
                                            'CountryId' => $countryId,
                                            'IndustryCateogryId' => $industryCategoryId,
                                            'ClientNotes' => implode(', ', $arrComments),
                                            'TypeOfPitchValue' => $typeOfPitch,
                                            'DatePitchRaised' => $dtCreated,
                                            'EstAnnualRevenueUSD' => $estimatedRevenue,
                                            'PitchStatusId' => $pitchStatusId,
                                            'PitchStageId' => $pitchStageId,
                                            'PitchStartDate' => $pitchDate,
                                            'NetworkBrandId' => $networkBrandId,
                                            'LeadCountryId' => $countryId,
                                            'Cities' => implode(', ', $arrCities),
                                            'ClientHolidngCompany' => $clientHolidngCompany,
                                            'PitchClosedDate' => $pitchCloseDate,
                                            'MultipleNetworksInvolved' => $multipleNetworksInvolved,
                                            'ScopeValue' => $scope,
                                            'SupportNetwork' => $supportNetwork,
                                            'TypeOfNetworkValue' => $typeOfNetwork,
                                            'ArchivePitch' => $isArchivePitch,
                                            'OtherCountrySInvolved' => implode(', ', $activeMarkets),
                                            'Services' => implode(', ', $arrServices)
                                        );
                                        echo json_encode($data) . "<br/>";
                                        $url1 = $siteUrl . $countryCode .'/_vti_bin/listdata.svc/Pitch';
                                        curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($data));
                                        curl_setopt( $ch1, CURLOPT_URL, $url1 );
                                        $savedContent = json_decode(curl_exec( $ch1 ));
                                        //echo '<pre>'; print_r($savedContent); echo '</pre>';
                                } else {
                                        $existingServices = explode(', ', $pitchExistsResult->results[0]->DAServices);
                                        $newServices = array_diff($arrServices, $existingServices);
                                        if(!empty($newServices)) {
                                                $arrServices = array_merge($existingServices, $newServices);
                                                $estimatedRevenue = $pitchExistsResult->results[0]->DAEstimatedAnnualRevenueUSD;
                                                $arrEstRevenue = explode(',', $client[0]['estimated_revenue']);
                                                $arrCurrency = explode(',', $client[0]['currency_id']);
                                                foreach($newServices as $index => $service) {
                                                        $estRevenue = $arrEstRevenue[$index];
                                                        if($estRevenue != 0) {
                                                                if($arrCurrency[$index] != null) {
                                                                        $estimatedRevenue +=  ($estRevenue * $currencies[$arrCurrency[$index]]);
                                                                } else {
                                                                        $estimatedRevenue +=  $estRevenue;
                                                                }
                                                        }
                                                }
                                                $data = array (
                                                    'EstAnnualRevenueUSD' => $estimatedRevenue,
                                                    'Services' => implode(', ', $arrServices)
                                                );
                                                $url2 = $siteUrl . $countryCode .'/_vti_bin/listdata.svc/Pitch('.$pitchExistsResult->results[0]->Id.')';
                                                curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
                                                curl_setopt( $ch2, CURLOPT_URL, $url2 );
                                                $updatedContent = json_decode(curl_exec( $ch2 ));
                                                //echo '<pre>'; print_r($updatedContent); echo '</pre>';

                                                if(preg_match('/Won/', $pitchStatus) || preg_match('/Lost/', $pitchStatus)) {
                                                        $livePitchExistsUrl = $siteUrl . $countryCode .'/_api/web/lists/getbytitle(\'Pitch\')/items';
                                                        $livePitchExistsFilter = urlencode('DACLient eq ' . $clientId . ' and DACountry eq ' . $countryId . ' and (DAPitchStatus eq \'Offensive Pitch\' or DAPitchStatus eq \'Defensive Pitch\')');
                                                        $livePitchExistsUrl = $livePitchExistsUrl . '?$filter=' . $livePitchExistsFilter;
                                                        curl_setopt( $ch, CURLOPT_URL, $livePitchExistsUrl );
                                                        $livePitchExistsContent = json_decode(curl_exec( $ch ));
                                                        $livePitchExistsResult = $livePitchExistsContent->d;
                                                        //echo '<pre>'; print_r($livePitchExistsContent); echo '</pre>';
                                                        if(!empty($livePitchExistsResult->results)) {
                                                                $existingLiveServices = explode(', ', $livePitchExistsResult->results[0]->DAServices);
                                                                $updatedServices = array_intersect($newServices, $existingLiveServices);
                                                                if(!empty($updatedServices)) {
                                                                        if(count($updatedServices) == count($existingLiveServices)) {
                                                                                $url3 = $siteUrl . $countryCode .'/_vti_bin/listdata.svc/Pitch('.$livePitchExistsResult->results[0]->Id.')';
                                                                                curl_setopt( $ch3, CURLOPT_URL, $url3 );
                                                                                $deletedContent = json_decode(curl_exec( $ch3 ));
                                                                                //echo '<pre>'; print_r($deletedContent); echo '</pre>';
                                                                        } else {
                                                                                $newLiveServices = array_diff($existingLiveServices, $updatedServices);
                                                                                $estimatedRevenue = $livePitchExistsResult->results[0]->DAEstimatedAnnualRevenueUSD;
                                                                                $arrEstRevenue = explode(',', $client[0]['estimated_revenue']);
                                                                                $arrCurrency = explode(',', $client[0]['currency_id']);
                                                                                foreach($updatedServices as $index => $service) {
                                                                                        $estRevenue = $arrEstRevenue[$index];
                                                                                        if($estRevenue != 0) {
                                                                                                if($arrCurrency[$index] != null) {
                                                                                                        $estimatedRevenue -=  ($estRevenue * $currencies[$arrCurrency[$index]]);
                                                                                                } else {
                                                                                                        $estimatedRevenue -=  $estRevenue;
                                                                                                }
                                                                                        }
                                                                                }
                                                                                $data = array (
                                                                                    'EstAnnualRevenueUSD' => $estimatedRevenue,
                                                                                    'Services' => implode(', ', $newLiveServices)
                                                                                );
                                                                                $url2 = $siteUrl . $countryCode .'/_vti_bin/listdata.svc/Pitch('.$livePitchExistsResult->results[0]->Id.')';
                                                                                curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
                                                                                curl_setopt( $ch2, CURLOPT_URL, $url2 );
                                                                                $updatedContent = json_decode(curl_exec( $ch2 ));
                                                                                //echo '<pre>'; print_r($updatedContent); echo '</pre>';
                                                                        }
                                                                }
                                                        }
                                                }
                                        }
                                }
                        }
                }
                curl_close ( $ch );
                curl_close ( $ch1 );
                curl_close ( $ch2 );
                curl_close ( $ch3 );
        }
        
        public function delete_records() {
                $this->autoRender=false;

                $countryCode = 'AUT';
                $ch = curl_init();
                //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                //curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json;odata=verbose"));
        
                $ch3 = curl_init();
                //curl_setopt( $ch3, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
                //curl_setopt( $ch3, CURLOPT_COOKIEJAR, $cookie );
                curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
                curl_setopt( $ch3, CURLOPT_CONNECTTIMEOUT, 25 );
                curl_setopt( $ch3, CURLOPT_TIMEOUT, 25 );
                curl_setopt($ch3, CURLOPT_USERPWD, 'Media\sysSP-T-NBR:hR9uttedua');
                curl_setopt($ch3, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
                curl_setopt($ch3, CURLOPT_HTTPHEADER, array("content-type: application/json", "accept: application/json;odata=verbose"));
                curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, 'DELETE');
                
                $pitchExistsUrl = 'http://team.test.dentsuaegis.com/sites/nbr/'.$countryCode.'/_api/web/lists/getbytitle(\'Pitch\')/items';
                $pitchExistsFilter = urlencode('DATypeOfNetwork eq \'Digital and Creative\'');
                $pitchExistsUrl = $pitchExistsUrl . '?$filter=' . $pitchExistsFilter;
                curl_setopt( $ch, CURLOPT_URL, $pitchExistsUrl );
                $pitchExistsContent = json_decode(curl_exec( $ch ));
                $pitchExistsResult = $pitchExistsContent->d;
                //echo '<pre>'; print_r($pitchExistsContent); echo '</pre>';
                if(!empty($pitchExistsResult->results)) {
                        foreach($pitchExistsResult->results as $result) {
                                if($result->DACLientId != '') {
                                        $clientSearchUrl = 'http://team.test.dentsuaegis.com/sites/nbr/_api/web/lists/GetByTitle(\'Client\')/items('.$result->DACLientId.')';
                                        echo $clientSearchUrl . '<br/>';
                                        curl_setopt( $ch, CURLOPT_URL, $clientSearchUrl );
                                        $clientSearchContent = json_decode(curl_exec( $ch ));
                                        $clientSearchResult = $clientSearchContent->d;
                                        //echo '<pre>'; print_r($clientSearchContent); echo '</pre>';
                                        if(empty($clientSearchResult)) {
                                                $url3 = 'http://team.test.dentsuaegis.com/sites/nbr/' . $countryCode .'/_vti_bin/listdata.svc/Pitch('.$result->Id.')';
                                                curl_setopt( $ch3, CURLOPT_URL, $url3 );
                                                $deletedContent = json_decode(curl_exec( $ch3 ));
                                                //echo '<pre>'; print_r($deletedContent); echo '</pre>';
                                        }
                                } else {
                                        $url3 = 'http://team.test.dentsuaegis.com/sites/nbr/' . $countryCode .'/_vti_bin/listdata.svc/Pitch('.$result->Id.')';
                                        curl_setopt( $ch3, CURLOPT_URL, $url3 );
                                        $deletedContent = json_decode(curl_exec( $ch3 ));
                                        //echo '<pre>'; print_r($deletedContent); echo '</pre>';
                                }
                        }
                }
                curl_close ( $ch );
                curl_close ( $ch3 );
        }
}
