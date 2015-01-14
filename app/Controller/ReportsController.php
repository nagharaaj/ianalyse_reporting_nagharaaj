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
            'ClientRevenueByService'
        );

        public $months = array(1 => 'Jan (1)', 'Feb (2)', 'Mar (3)', 'Apr (4)', 'May (5)', 'Jun (6)', 'Jul (7)', 'Aug (8)', 'Sep (9)', 'Oct (10)', 'Nov (11)', 'Dec (12)');

	public function index() {
                
                $this->set('cities', json_encode($this->City->find('list', array('fields' => array('City.city', 'City.city'), 'order' => 'City.city Asc'))));
                $this->set('categories', json_encode($this->ClientCategory->find('list', array('fields' => array('ClientCategory.category', 'ClientCategory.category'), 'order' => 'ClientCategory.category Asc'))));
                $this->set('countries', json_encode($this->Country->find('list', array('fields' => array('Country.country', 'Country.country'), 'order' => 'Country.country Asc'))));
                $this->set('currencies', json_encode($this->Currency->find('list', array('fields' => array('Currency.currency', 'Currency.currency'), 'order' => 'Currency.currency Asc'))));
                $this->set('agencies', json_encode($this->LeadAgency->find('list', array('fields' => array('LeadAgency.agency', 'LeadAgency.agency'), 'order' => 'LeadAgency.agency Asc'))));
                $arrMarkets = array();
                $markets = $this->Market->find('all', array('order' => 'Country.country Asc'));
                foreach ($markets as $market)
                {
                        $arrMarkets[$market['Country']['country']] = $market['Country']['country'];
                }
                $this->set('markets', json_encode($arrMarkets));
                $this->set('regions', $this->Region->find('list', array('order' => 'Region.region Asc')));
                $this->set('services', json_encode($this->Service->find('list', array('fields' => array('Service.service_name', 'Service.service_name'), 'order' => 'Service.service_name Asc'))));
	}

        public function save_data($market) {

                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                                //Configure::write('debug', 0);
                        }
                        
                        $arrData = $this->request->data;
                        //print_r($data);
                        
                        $market = $this->Market->find('first', array('contain' => array('Country'), 'conditions' => array('country' => $market)));
                        $marketId = $market['Market']['id'];
                        
                        $arrClient = array();
                        foreach($arrData as $data) {
                                $category = $this->ClientCategory->findByCategory(trim($data['ClientCategory']));
                                $country = $this->Country->findByCountry(trim($data['Country']));
                                $city = $this->City->findByCity(trim($data['City']));
                                $agency = $this->LeadAgency->findByAgency(trim($data['LeadAgency']));
                                $currency = $this->Currency->findByCurrency(trim($data['Currency']));
                                $service = $this->Service->findByServiceName(trim($data['Service']));

                                if(!empty($category)) {
                                        $categoryId = $category['ClientCategory']['id'];
                                } else {
                                        $categoryId = 0;
                                }
                                if(!empty($country)) {
                                        $countryId = $country['Country']['id'];
                                } else {
                                        $countryId =0;
                                }
                                if(!empty($city)) {
                                        $cityId = $city['City']['id'];
                                } else {
                                        $cityId =0;
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
                                $month = array_search(trim($data['ClientMonth']), $this->months);
                                $year = $data['ClientYear'];
                                $activeMarkets = $data['ActiveMarkets'];
                                $companyName = $data['ParentCompany'];
                                $clientName = $data['ClientName'];
                                $estimatedRevenue = $data['EstimatedRevenue'];
                                $actualRevenue = $data['ActualRevenue'];

                                $arrClient[] = array(
                                    'marketId' => $marketId,
                                    'companyName' => $companyName, 
                                    'clientName' => $clientName, 
                                    'categoryId' => $categoryId, 
                                    'month' => $month, 
                                    'year' => $year, 
                                    'agencyId' => $agencyId, 
                                    'countryId' => $countryId, 
                                    'cityId' => $cityId, 
                                    'activeMarkets' => $activeMarkets, 
                                    'serviceId' => $serviceId, 
                                    'currencyId' => $currencyId, 
                                    'estimatedRevenue' => $estimatedRevenue, 
                                    'actualRevenue' => $actualRevenue
                                );
                        }

                        foreach($arrClient as $client) {
                                $this->ClientRevenueByService->create();
                                $this->ClientRevenueByService->save(
                                        array(
                                                'ClientRevenueByService' => array(
                                                        'market_id' => $client['marketId'],
                                                        'client_name' => $client['clientName'],
                                                        'parent_company' => $client['companyName'],
                                                        'category_id' => $client['categoryId'],
                                                        'client_since_month' => $client['month'],
                                                        'client_since_year' => $client['year'],
                                                        'agency_id' => $client['agencyId'],
                                                        'country_id' => $client['countryId'],
                                                        'city_id' => $client['cityId'],
                                                        'active_markets' => $client['activeMarkets'],
                                                        'service_id' => $client['serviceId'],
                                                        'currency_id' => $client['currencyId'],
                                                        'estimated_revenue' => $client['estimatedRevenue'],
                                                        'actual_revenue' => $client['actualRevenue']
                                                )
                                        )
                                );
                        }
                }
        }
        
        public function export_data($market) {

                if($this->RequestHandler->isAjax()){
                        $this->autoRender=false;
                        //Configure::write('debug', 0);
                }

                App::import('Vendor', 'zend_include_path');
                App::import('Vendor', 'Zend_Gdata', true, false, 'Zend/Gdata.php');
                
                Zend_Loader::loadClass('Zend_Http_Client');
                Zend_Loader::loadClass('Zend_Gdata');
                Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
                Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
                
                //$zend = new Zend_Gdata_Spreadsheets();
		
                $authService = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
                $user = 'iprospectawards@gmail.com';
                $pass = 'awards2014';
                $this->currKey = '1gx1F_eNm9K1XwVya6LH5CjdULhKOHqWqxTqxnXJJ25s';

                try {
                        $httpClient = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $authService);
                        $this->gdClient = new Zend_Gdata_Spreadsheets($httpClient);

                        $this->promptForWorksheet(0); // Put the 0th worksheet of our sheet to $this->currWkshtId
                        $this->listGetAction(); // Will list all the rows inside the worksheet

                        $arrData = $this->request->data;
                        //print_r($data);

                        $market = $this->Market->find('first', array('contain' => array('Country'), 'conditions' => array('country' => $market)));
                        $marketId = $market['Market']['id'];
                        $arrClient = array();
                        foreach($arrData as $data) {
                                $arrClient[] = array(
                                    'client' => $data['ClientName'],
                                    'parentcompany' => $data['ParentCompany'], 
                                    'clientcategory' => $data['ClientCategory'], 
                                    'clientsincemonth' => $data['ClientMonth'], 
                                    'clientsinceyear' => $data['ClientYear'], 
                                    'leadagency' => $data['LeadAgency'], 
                                    'managingcountry' => $data['Country'], 
                                    'managingcity' => $data['City'], 
                                    'activemarkets' => $data['ActiveMarkets'], 
                                    'service' => $data['Service'], 
                                    'currency' => $data['Currency'], 
                                    'estimatedannualrevenue' => $data['EstimatedRevenue'], 
                                    'actualannualrevenue' => $data['ActualRevenue']
                                );
                        }
                        //$row = array('column1'=>'value','column2'=>'value','columnN'=>'value');
                        $this->listInsertAction($arrClient);
                } catch ( Exception $e )  {
                        echo $e->getMessage();
                }
        }
        
        public function promptForWorksheet($wordSheetI=0)
        {
                $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
                $query->setSpreadsheetKey($this->currKey);
                $feed = $this->gdClient->getWorksheetFeed($query);
                print "== Available Worksheets ==\n";
                $this->printFeed($feed);
                $input = $wordSheetI;
                $currWkshtId = split('/', $feed->entries[$input]->id->text);
                $this->currWkshtId = $currWkshtId[8];
        }

        public function listGetAction()
        {
                $query = new Zend_Gdata_Spreadsheets_ListQuery();
                $query->setSpreadsheetKey($this->currKey);
                $query->setWorksheetId($this->currWkshtId);
                $this->listFeed = $this->gdClient->getListFeed($query);
                print "entry id | row-content in column A | column-header: cell-content\n".
                "Please note: The 'dump' command on the list feed only dumps data until the first blank row is encountered.\n\n";

                $this->printFeed($this->listFeed);
                print "\n";
        }

        public function printFeed($feed)
        {
                $i = 0;
                foreach($feed->entries as $entry) {
                        if ($entry instanceof Zend_Gdata_Spreadsheets_CellEntry) {
                                print $entry->title->text .' '. $entry->content->text . "\n";
                        } else if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry) {
                                print $i .' '. $entry->title->text .' | '. $entry->content->text . "\n";
                        } else {
                                print $i .' '. $entry->title->text . "\n";
                        }
                        $i++;
                }
        }

        public function listInsertAction($rowArray)
        {
                //$rowArray = $this->stringToArray($rowData);
                $entry = $this->gdClient->insertRow($rowArray, $this->currKey,
                $this->currWkshtId);

                if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry) {
                        foreach ($rowArray as $column_header => $value) {
                                echo "Success! Inserted '$value' in column '$column_header' at row ".
                                substr($entry->getTitle()->getText(), 5) ."\n";
                        }
                }
        }
}
