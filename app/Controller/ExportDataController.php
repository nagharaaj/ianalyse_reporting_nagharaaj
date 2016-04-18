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
                        "(pitch_stage like 'Live%' or pitch_stage like 'Won%' or pitch_stage like 'Lost%' or pitch_stage='Cancelled') and pitch_stage != 'Lost - archive'"
                    ),
                    'group' => array('ClientRevenueByService.client_name', 'ClientRevenueByService.country_id', 'ClientRevenueByService.pitch_stage'),
                    'order' => 'ClientRevenueByService.client_name asc, ClientRevenueByService.pitch_stage asc, ClientRevenueByService.pitch_date desc'
                ));
                //echo '<pre>'; print_r($clients);

                $dtCreated = date('m/d/Y');
                foreach($clients as $client) {
                        if($client['Country']['country'] == 'USA') {
                                $country = 'United States of America';
                        } else {
                                $country = $client['Country']['country'];
                        }
                        $arrComments = array_unique(explode(',', $client[0]['comments']));;
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
                        } else if(preg_match('/Lost/', $client['ClientRevenueByService']['pitch_stage']) || $client['ClientRevenueByService']['pitch_stage'] == 'Cancelled') {
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
                }
                $objPHPExcel->getActiveSheet()->fromArray($arrDataExcel, null, 'A1');

                // Rename sheet
                $objPHPExcel->getActiveSheet()->setTitle('Digital & Creative');

                // Save Excel 2007 file
                $fileName = 'DAN_Client_Data_' . date('m-d-Y') . '.xlsx';
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                $objWriter->save('files/' . $fileName);
        }
}