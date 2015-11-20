<?php
class ImportDataController extends AppController {
	public $helpers = array('Html', 'Form');

        public $components = array('RequestHandler');

        public $uses = array(
            'City',
            'ClientCategory',
            'Country',
            'Currency',
            'Division',
            'LeadAgency',
            'Market',
            'Region',
            'Service',
            'ClientRevenueByService',
            'Language',
            'Office',
            'OfficeAttribute',
            'OfficeKeyContact',
            'OfficeServiceContact',
            'OfficeLanguage',
            'OfficeEmployeeCountByDepartment'
        );

        public $months = array(1 => 'Jan (1)', 'Feb (2)', 'Mar (3)', 'Apr (4)', 'May (5)', 'Jun (6)', 'Jul (7)', 'Aug (8)', 'Sep (9)', 'Oct (10)', 'Nov (11)', 'Dec (12)');

        public $servicesMap = array();
        public $unknownServices = array();
        public $arrClient = array();

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

        public function client_import() {
		set_time_limit(0);
                ini_set('memory_limit', '-1');

		$this->City->Behaviors->attach('Containable');
		$this->Country->Behaviors->attach('Containable');
		$this->ClientRevenueByService->Behaviors->attach('Containable');
		$this->Region->Behaviors->attach('Containable');
                $this->servicesMap = $this->Service->find('list', array('fields' => array('Service.id', 'Service.service_name'), 'order' => 'Service.service_name Asc'));

                $status = null;
                $sheetNames = array('Client List');
                if ($this->request->isPost()) {
                        if(isset($this->request->data['MarketImport'])) {
                                $status = $this->request->data['MarketImport']['status'];
                        }
                        if($status == null && isset($this->request->data['ClientAnalyse'])) {
                                $status = $this->request->data['ClientAnalyse']['status'];
                        }
                        if(empty($status)) {
                                $fileName = $this->request->data['MarketImport']['excel_file']['name'];
                                $pathinfo = pathinfo($fileName);
                                if(isset($pathinfo['extension']) && strtolower($pathinfo['extension']) != 'xls') {
                                        $this->set('failure', true);
                                } else {
                                        $this->request->data['MarketImport']['excel_file'] = $this->request->data['MarketImport']['excel_file']['tmp_name'];
                                        $status = 'analyse';
                                        if (isset($this->request->data['MarketImport']['excel_file']) && !empty($this->request->data['MarketImport']['excel_file'])) {
                                                move_uploaded_file($this->request->data['MarketImport']['excel_file'], ROOT . DS . APP_DIR . DS . 'tmp' . DS . $fileName);
                                                $excelFile = ROOT . DS . APP_DIR . DS . 'tmp' . DS . $fileName;
                                        }
                                        if(isset($excelFile)) {
                                                $this->read_excel($excelFile, $status, $sheetNames);
                                                $this->set('excelFile', $excelFile);
                                        }
                                        $this->set('services', $this->servicesMap);
                                }
                        } elseif($status == 'analyse') {
                                $status = 'import';

                                if(isset($this->request->data['ClientAnalyse']['analyse_service'])) {
                                        $excelFile = $this->request->data['ClientAnalyse']['excel_file'];

                                        for($cnt = 1; $cnt < $this->request->data['ClientAnalyse']['unknown_service_count']; $cnt++ ) {
                                                $unknownService = $this->request->data['ClientAnalyse'][$cnt]['unknown_service'];
                                                if($this->request->data['ClientAnalyse']['ServiceMain'][$cnt]['service_id'] != '') {
                                                        $serviceMainsId = $this->request->data['ClientAnalyse']['ServiceMain'][$cnt]['service_id'];
                                                        $this->unknownServices[$unknownService] = $serviceMainsId;
                                                }
                                        }
                                } else {
                                        $excelFile = $this->request->data['MarketImport']['excel_file'];
                                }
                                if(isset($excelFile)) {
                                        $this->read_excel($excelFile, $status, $sheetNames);
                                }

                                unlink($excelFile);
                                $this->set('complete', true);
                        }
		}
                $this->set('status', $status);
        }

        protected function read_excel($file, $status, $sheetNames, $dataType = 'client') {
                $this->ExcelReader = $this->Components->load('ExcelReader');
                $this->ExcelReader->initialize($this);
                $this->set('filename', $file);
                try {
                        $data = array();
                        foreach($sheetNames as $sheetName) {
                                if($dataType == 'office') {
                                        $data[$sheetName] = $this->ExcelReader->readExcelSheet($file, $sheetName, true);

                                        $this->parse_office_list($data[$sheetName], $status);
                                } else {
                                        $data[$sheetName] = $this->ExcelReader->readExcelSheet($file, $sheetName);

                                        $this->parse_client_list($data[$sheetName], $status);
                                }
                        }

                } catch (Exception $e) {
                        echo 'Could not read the file. Please check file format and try again.<br />';
                        echo $e->getMessage();
                        exit;
                }
        }

        protected function parse_client_list($dataClientList = array(), $status = null) {
                $arrClient = array();
                $arrServices = $this->Service->find('list', array('fields' => array('Service.id', 'Service.service_name'), 'order' => 'Service.service_name Asc'));
                $fiscalYr = date('Y');

                if(!empty($dataClientList)) {

                        $opportunities = $this->LeadAgency->find('list', array('fields' => array('LeadAgency.id', 'LeadAgency.agency'), 'order' => 'LeadAgency.agency Asc'));
                        $currencies = $this->Currency->find('list', array('fields' => array('Currency.id', 'Currency.currency'), 'order' => 'Currency.currency Asc'));

                        $arrCnt = count($dataClientList);
                        if(count($dataClientList[1]) >= 17) {
                                $searchRow = 2;
                                $endOfList = 0;
                                for($i = $searchRow; $i <= $arrCnt; $i++) {
                                        if(!empty($dataClientList[$i]['D'])) {
                                                $endOfList = 0;
                                                if(!array_search(trim(str_replace('&', 'and', $dataClientList[$i]['I'])), $arrServices)) {
                                                        if(!in_array(trim(str_replace('&', 'and', $dataClientList[$i]['I'])), $this->unknownServices)) {
                                                                $this->unknownServices[] = trim($dataClientList[$i]['I']);
                                                        }
                                                }

                                                $vertical = $this->ClientCategory->findByCategory(trim(str_replace('&', 'and', $dataClientList[$i]['F'])));
                                                $country = $this->Market->findAllByMarket(trim($dataClientList[$i]['B']));
                                                $managedCity = $this->City->findByCity(trim($dataClientList[$i]['C']));
                                                $division = $this->Division->findByDivision(trim($dataClientList[$i]['J']));

                                                if(array_search(trim(str_replace('&', 'and', $dataClientList[$i]['G'])), $opportunities)) {
                                                        $agencyOpportunity = array_search(trim(str_replace('&', 'and', $dataClientList[$i]['G'])), $opportunities);
                                                } else {
                                                        $agencyOpportunity = null;
                                                }

                                                if(array_search(trim($dataClientList[$i]['O']), $currencies)) {
                                                        $currency = array_search(trim($dataClientList[$i]['O']), $currencies);
                                                } else {
                                                        $currency = null;
                                                }

                                                if(!empty($vertical)) {
                                                        $verticalId = $vertical['ClientCategory']['id'];
                                                } else {
                                                        $verticalId = null;
                                                }
                                                if(array_search(trim(str_replace('&', 'and', $dataClientList[$i]['I'])), $arrServices)) {
                                                        $serviceId = array_search(trim(str_replace('&', 'and', $dataClientList[$i]['I'])), $arrServices);
                                                } else {
                                                        if(isset($this->unknownServices[trim(str_replace('&', 'and', $dataClientList[$i]['I']))])) {
                                                                $serviceId = $this->unknownServices[trim(str_replace('&', 'and', $dataClientList[$i]['I']))];
                                                        } else {
                                                                $serviceId = 0;
                                                        }
                                                }
                                                if(!empty($country)) {
                                                        $countryId = $country[0]['Market']['country_id'];
                                                        $regionId = $country[0]['Market']['region_id'];
                                                        $managingEntity = 'Country';
                                                } else {
                                                        $countryId =null;
                                                        $regionId =null;
                                                        $managingEntity = 'Global';
                                                }
                                                if(!empty($managedCity)) {
                                                        $managedCityId = $managedCity['City']['id'];
                                                } else {
                                                        $managedCityId =null;
                                                }
                                                if(!empty($division)) {
                                                        $divisionId = $division['Division']['id'];
                                                } else {
                                                        $divisionId =null;
                                                }
                                                //$month = array_search(trim($dataClientList[$i]['E']), $this->months);
                                                //$month = trim($dataClientList[$i]['E']);
                                                //$year = trim($dataClientList[$i]['F']);
                                                $companyName = trim($dataClientList[$i]['E']);
                                                $clientName = trim($dataClientList[$i]['D']);
                                                $activeMarkets = trim($dataClientList[$i]['N']);
                                                $revenue = $dataClientList[$i]['Q'];
                                                $revenueForecast = $dataClientList[$i]['P'];
                                                if(!empty($dataClientList[$i]['H'])) {
                                                        $pitchStage = trim($dataClientList[$i]['H']);
                                                        if(!empty($dataClientList[$i]['M'])) {
                                                                //$arrPitchDate = explode('/', $this->ExcelReader->readDateFromExcel((trim($dataClientList[$i]['M']))));
                                                                $arrPitchDate = explode('/', trim($dataClientList[$i]['M']));
                                                                $pitchDate = $arrPitchDate[1].'-'.$arrPitchDate[0].'-01';
                                                        } else {
                                                                $pitchDate = '0000-00-00';
                                                        }
                                                } else {
                                                        $pitchStage = 'Current client';
                                                        $pitchDate = '0000-00-00';
                                                }
                                                if(!empty($dataClientList[$i]['L']) && $dataClientList[$i]['L'] != '-') {
                                                        //$arrLostDate = explode('/', $this->ExcelReader->readDateFromExcel((trim($dataClientList[$i]['L']))));
                                                        $arrLostDate = explode('/', trim($dataClientList[$i]['L']));
                                                        $lostDate = $arrLostDate[1].'-'.$arrLostDate[0].'-01';
                                                } else {
                                                        $lostDate = '0000-00-00';
                                                }
                                                if(!empty($dataClientList[$i]['K']) && $dataClientList[$i]['K'] != '-') {
                                                        //$arrClientSince = explode('/', $this->ExcelReader->readDateFromExcel((trim($dataClientList[$i]['K']))));
                                                        $arrClientSince = explode('/', trim($dataClientList[$i]['K']));
                                                        $month = $arrClientSince[0];
                                                        $year = $arrClientSince[1];
                                                } else {
                                                        $month = 0;
                                                        $year = 0;
                                                }
                                                if(!empty($dataClientList[$i]['R'])) {
                                                        $notes = trim($dataClientList[$i]['R']);
                                                } else {
                                                        $notes = null;
                                                }
                                                if(!empty($dataClientList[$i]['S']) && $dataClientList[$i]['S'] != '-') {
                                                        //$arrCreatedDate = explode('/', $this->ExcelReader->readDateFromExcel((trim($dataClientList[$i]['S']))));
                                                        $arrCreatedDate = explode('/', trim($dataClientList[$i]['S']));
                                                        $createdDate = $arrCreatedDate[2].'-'.$arrCreatedDate[0].'-'.$arrCreatedDate[1];
                                                } else {
                                                        $createdDate = date('Y-m-d');
                                                }
                                                if(!empty($dataClientList[$i]['T']) && $dataClientList[$i]['T'] != '-') {
                                                        //$arrModifiedDate = explode('/', $this->ExcelReader->readDateFromExcel((trim($dataClientList[$i]['T']))));
                                                        $arrModifiedDate = explode('/', trim($dataClientList[$i]['T']));
                                                        $modifiedDate = $arrModifiedDate[2].'-'.$arrModifiedDate[0].'-'.$arrModifiedDate[1];
                                                } else {
                                                        $modifiedDate = date('Y-m-d');
                                                }

                                                if($countryId != null) {
                                                        $arrClient[] = array(
                                                            'clientName' => $clientName,
                                                            'companyName' => $companyName,
                                                            'clientSinceYear' => $year,
                                                            'verticalId' => $verticalId,
                                                            'serviceId' => $serviceId,
                                                            'regionId' => $regionId,
                                                            'countryId' => $countryId,
                                                            'cityId' => $managedCityId,
                                                            'divisionId' => $divisionId,
                                                            'managingEntity' => $managingEntity,
                                                            'activeMarkets' => $activeMarkets,
                                                            'fiscalYr' => $fiscalYr,
                                                            'clientSinceMonth' => $month,
                                                            'revenue' => $revenue,
                                                            'agencyOpportunity' => $agencyOpportunity,
                                                            'currency' => $currency,
                                                            'revenueForecast' => $revenueForecast,
                                                            'pitchStage' => $pitchStage,
                                                            'pitchDate' => $pitchDate,
                                                            'lostDate' => $lostDate,
                                                            'comments' => $notes,
                                                            'createdDate' => $createdDate,
                                                            'modifiedDate' => $modifiedDate
                                                        );
                                                }
                                        } else {
                                                $endOfList++;
                                        }
                                        // if 10 consecutive rows are emtpy consider end of records and move out of loop
                                        if($endOfList >= 10) {
                                                break;
                                        }
                                }

                                if($status == 'analyse') {
                                        $this->set('clientDetails', $arrClient);
                                        $this->set('unknownServices', $this->unknownServices);
                                        $this->set('services_list', $this->Service->find('list', array('order' => 'Service.service_name ASC')));
                                } else {
                                        foreach($arrClient as $client) {
                                                $this->ClientRevenueByService->create();
                                                $this->ClientRevenueByService->save(
                                                        array(
                                                                'ClientRevenueByService' => array(
                                                                        'client_name' => $client['clientName'],
                                                                        'parent_company' => $client['companyName'],
                                                                        'category_id' => $client['verticalId'],
                                                                        'agency_id' => $client['agencyOpportunity'],
                                                                        'region_id' => $client['regionId'],
                                                                        'managing_entity' => $client['managingEntity'],
                                                                        'country_id' => $client['countryId'],
                                                                        'city_id' => $client['cityId'],
                                                                        'division_id' => $client['divisionId'],
                                                                        'active_markets' => $client['activeMarkets'],
                                                                        'service_id' => $client['serviceId'],
                                                                        'currency_id' => $client['currency'],
                                                                        'estimated_revenue' => $client['revenueForecast'],
                                                                        'actual_revenue' => $client['revenue'],
                                                                        'year' => $client['fiscalYr'],
                                                                        'client_since_month' => $client['clientSinceMonth'],
                                                                        'client_since_year' => $client['clientSinceYear'],
                                                                        'pitch_stage' => $client['pitchStage'],
                                                                        'pitch_date' => $client['pitchDate'],
                                                                        'lost_date' => $client['lostDate'],
                                                                        'comments' => $client['comments'],
                                                                        'created' => $client['createdDate'],
                                                                        'modified' => $client['modifiedDate']
                                                                )
                                                        )
                                                );
                                        }
                                }
                        }
                        $dataClientList = null;
                }
        }

        public function office_import() {
		set_time_limit(0);
                ini_set('memory_limit', '-1');

		$this->City->Behaviors->attach('Containable');
		$this->Country->Behaviors->attach('Containable');
		$this->Office->Behaviors->attach('Containable');
		$this->Region->Behaviors->attach('Containable');
                $this->servicesMap = $this->Service->find('list', array('fields' => array('Service.id', 'Service.service_name'), 'order' => 'Service.service_name Asc'));

                $status = null;
                $sheetNames = array('Offices');
                if ($this->request->isPost()) {
                        if(isset($this->request->data['MarketImport'])) {
                                $status = $this->request->data['MarketImport']['status'];
                        }
                        if(empty($status)) {
                                $fileName = $this->request->data['MarketImport']['excel_file']['name'];
                                $pathinfo = pathinfo($fileName);
                                if(isset($pathinfo['extension']) && strtolower($pathinfo['extension']) != 'xls') {
                                        $this->set('failure', true);
                                } else {
                                        $this->request->data['MarketImport']['excel_file'] = $this->request->data['MarketImport']['excel_file']['tmp_name'];
                                        $status = 'analyse';
                                        if (isset($this->request->data['MarketImport']['excel_file']) && !empty($this->request->data['MarketImport']['excel_file'])) {
                                                move_uploaded_file($this->request->data['MarketImport']['excel_file'], ROOT . DS . APP_DIR . DS . 'tmp' . DS . $fileName);
                                                $excelFile = ROOT . DS . APP_DIR . DS . 'tmp' . DS . $fileName;
                                        }
                                        if(isset($excelFile)) {
                                                $this->read_excel($excelFile, $status, $sheetNames, 'office');
                                                $this->set('excelFile', $excelFile);
                                        }
                                        $this->set('services', $this->servicesMap);
                                }
                        } elseif($status == 'analyse') {
                                $status = 'import';

                                if(isset($this->request->data['ClientAnalyse']['analyse_service'])) {
                                        $excelFile = $this->request->data['ClientAnalyse']['excel_file'];

                                        for($cnt = 1; $cnt < $this->request->data['ClientAnalyse']['unknown_service_count']; $cnt++ ) {
                                                $unknownService = $this->request->data['ClientAnalyse'][$cnt]['unknown_service'];
                                                if($this->request->data['ClientAnalyse']['ServiceMain'][$cnt]['service_id'] != '') {
                                                        $serviceMainsId = $this->request->data['ClientAnalyse']['ServiceMain'][$cnt]['service_id'];
                                                        $this->unknownServices[$unknownService] = $serviceMainsId;
                                                }
                                        }
                                } else {
                                        $excelFile = $this->request->data['MarketImport']['excel_file'];
                                }
                                if(isset($excelFile)) {
                                        $this->read_excel($excelFile, $status, $sheetNames, 'office');
                                }

                                unlink($excelFile);
                                $this->set('complete', true);
                        }
		}
                $this->set('status', $status);
        }

        protected function parse_office_list($dataOfficeList = array(), $status = null) {
                $arrOffice = array();
                $arrKeyDepts = array('executive', 'finance_head', 'product_head', 'strategy_head', 'client_head', 'business_head', 'marketing_head');
                $arrServices = $this->Service->find('list', array('fields' => array('Service.id', 'Service.service_name'), 'order' => 'Service.service_name Asc'));
                $arrRegions = $this->Region->find('list', array('fields' => array('Region.id', 'Region.region'), 'order' => 'Region.region Asc'));
                $arrCountries = $this->Market->find('list', array('fields' => array('Market.country_id', 'Market.market'), 'order' => 'Market.market Asc'));
                $arrCities = $this->City->find('list', array('fields' => array('City.id', 'City.city'), 'order' => 'City.city Asc'));
                $arrLanguages = $this->Language->find('list', array('fields' => array('Language.id', 'Language.language'), 'order' => 'Language.language Asc'));

                if(!empty($dataOfficeList)) {

                        $searchRow = 3;
                        $endOfList = 0;
                        $arrCnt = count($dataOfficeList);
                        for($i = $searchRow; $i <= $arrCnt; $i++) {
                                $cityId = null;
                                $countryId = null;
                                $regionId = null;
                                if(!empty($dataOfficeList[$i]['C'])) {
                                        $cityId = array_search(trim($dataOfficeList[$i]['C']), $arrCities);
                                        $countryId = array_search(trim($dataOfficeList[$i]['B']), $arrCountries);
                                        $regionId = array_search(trim($dataOfficeList[$i]['A']), $arrRegions);

                                        $yearEstablished = trim($dataOfficeList[$i]['D']);
                                        $employeeCount = trim($dataOfficeList[$i]['E']);
                                        $address = trim($dataOfficeList[$i]['F']);

                                        $arrTelephones = explode("\n", trim($dataOfficeList[$i]['G']));
                                        $arrContactEmails = explode("\n", trim($dataOfficeList[$i]['H']));
                                        $arrWebsites = explode("\n", trim($dataOfficeList[$i]['I']));
                                        $arrSocialAccounts = explode("\n", trim($dataOfficeList[$i]['J']));

                                        $arrKeyContacts = array();

                                        $arrKeyContacts['executive'] = explode("\n", trim($dataOfficeList[$i]['K']));
                                        $arrKeyContacts['finance_head'] = explode("\n", trim($dataOfficeList[$i]['M']));
                                        $arrKeyContacts['product_head'] = explode("\n", trim($dataOfficeList[$i]['O']));
                                        $arrKeyContacts['strategy_head'] = explode("\n", trim($dataOfficeList[$i]['Q']));
                                        $arrKeyContacts['client_head'] = explode("\n", trim($dataOfficeList[$i]['S']));
                                        $arrKeyContacts['business_head'] = explode("\n", trim($dataOfficeList[$i]['U']));
                                        $arrKeyContacts['marketing_head'] = explode("\n", trim($dataOfficeList[$i]['W']));

                                        $arrServiceContacts = array();

                                        $arrServiceContacts['Affiliates'] = explode("\n", trim($dataOfficeList[$i]['Z']));
                                        $arrServiceContacts['Content'] = explode("\n", trim($dataOfficeList[$i]['AB']));
                                        $arrServiceContacts['Conversion Opt.'] = explode("\n", trim($dataOfficeList[$i]['AD']));
                                        $arrServiceContacts['Data and Insights'] = explode("\n", trim($dataOfficeList[$i]['AF']));
                                        $arrServiceContacts['Development'] = explode("\n", trim($dataOfficeList[$i]['AH']));
                                        $arrServiceContacts['Display'] = explode("\n", trim($dataOfficeList[$i]['AJ']));
                                        $arrServiceContacts['Feeds'] = explode("\n", trim($dataOfficeList[$i]['AL']));
                                        $arrServiceContacts['Lead Gen'] = explode("\n", trim($dataOfficeList[$i]['AN']));
                                        $arrServiceContacts['Mobile'] = explode("\n", trim($dataOfficeList[$i]['AP']));
                                        $arrServiceContacts['RTB'] = explode("\n", trim($dataOfficeList[$i]['AR']));
                                        $arrServiceContacts['Search - PPC'] = explode("\n", trim($dataOfficeList[$i]['AT']));
                                        $arrServiceContacts['SEO'] = explode("\n", trim($dataOfficeList[$i]['AV']));
                                        $arrServiceContacts['Social - Paid'] = explode("\n", trim($dataOfficeList[$i]['AX']));
                                        $arrServiceContacts['Social - Management'] = explode("\n", trim($dataOfficeList[$i]['AZ']));
                                        $arrServiceContacts['Strategy'] = explode("\n", trim($dataOfficeList[$i]['BB']));
                                        $arrServiceContacts['Technology'] = explode("\n", trim($dataOfficeList[$i]['BD']));
                                        $arrServiceContacts['Video'] = explode("\n", trim($dataOfficeList[$i]['BF']));

                                        $arrDepartmentEmployeeCount = array();

                                        $arrDepartmentEmployeeCount['executive'] = trim($dataOfficeList[$i]['L']);
                                        $arrDepartmentEmployeeCount['finance_head'] = trim($dataOfficeList[$i]['N']);
                                        $arrDepartmentEmployeeCount['product_head'] = trim($dataOfficeList[$i]['P']);
                                        $arrDepartmentEmployeeCount['strategy_head'] = trim($dataOfficeList[$i]['R']);
                                        $arrDepartmentEmployeeCount['client_head'] = trim($dataOfficeList[$i]['T']);
                                        $arrDepartmentEmployeeCount['business_head'] = trim($dataOfficeList[$i]['V']);
                                        $arrDepartmentEmployeeCount['marketing_head'] = trim($dataOfficeList[$i]['X']);

                                        $arrDepartmentEmployeeCount['Affiliates'] = trim($dataOfficeList[$i]['AA']);
                                        $arrDepartmentEmployeeCount['Content'] = trim($dataOfficeList[$i]['AC']);
                                        $arrDepartmentEmployeeCount['Conversion Opt.'] = trim($dataOfficeList[$i]['AE']);
                                        $arrDepartmentEmployeeCount['Data and Insights'] = trim($dataOfficeList[$i]['AG']);
                                        $arrDepartmentEmployeeCount['Development'] = trim($dataOfficeList[$i]['AI']);
                                        $arrDepartmentEmployeeCount['Display'] = trim($dataOfficeList[$i]['AK']);
                                        $arrDepartmentEmployeeCount['Feeds'] = trim($dataOfficeList[$i]['AM']);
                                        $arrDepartmentEmployeeCount['Lead Gen'] = trim($dataOfficeList[$i]['AO']);
                                        $arrDepartmentEmployeeCount['Mobile'] = trim($dataOfficeList[$i]['AQ']);
                                        $arrDepartmentEmployeeCount['RTB'] = trim($dataOfficeList[$i]['AS']);
                                        $arrDepartmentEmployeeCount['Search - PPC'] = trim($dataOfficeList[$i]['AU']);
                                        $arrDepartmentEmployeeCount['SEO'] = trim($dataOfficeList[$i]['AW']);
                                        $arrDepartmentEmployeeCount['Social - Paid'] = trim($dataOfficeList[$i]['AY']);
                                        $arrDepartmentEmployeeCount['Social - Management'] = trim($dataOfficeList[$i]['BA']);
                                        $arrDepartmentEmployeeCount['Strategy'] = trim($dataOfficeList[$i]['BC']);
                                        $arrDepartmentEmployeeCount['Technology'] = trim($dataOfficeList[$i]['BE']);
                                        $arrDepartmentEmployeeCount['Video'] = trim($dataOfficeList[$i]['BG']);

                                        $arrSupportedLanguages = explode(",", trim($dataOfficeList[$i]['BJ']));

                                        $awards = trim($dataOfficeList[$i]['BK']);
                                        $news = trim($dataOfficeList[$i]['BL']);

                                        if($cityId != null) {
                                                $arrOffice[] = array(
                                                        'cityId' => $cityId,
                                                        'countryId' => $countryId,
                                                        'regionId' => $regionId,
                                                        'yearEstablished' => $yearEstablished,
                                                        'employeeCount' => $employeeCount,
                                                        'address' => $address,
                                                        'telephones' => $arrTelephones,
                                                        'emails' => $arrContactEmails,
                                                        'websites' => $arrWebsites,
                                                        'socialAccounts' => $arrSocialAccounts,
                                                        'keyContacts' => $arrKeyContacts,
                                                        'servicesContacts' => $arrServiceContacts,
                                                        'deptEmpCount' => $arrDepartmentEmployeeCount,
                                                        'supportedLanguages' => $arrSupportedLanguages,
                                                        'awards' => $awards,
                                                        'news' => $news
                                                );
                                        }
                                } else {
                                        $endOfList++;
                                }
                                // if 10 consecutive rows are emtpy consider end of records and move out of loop
                                if($endOfList >= 10) {
                                        break;
                                }
                        }
                        if($status == 'analyse') {
                                $this->set('officeDetails', $arrOffice);
                                $this->set('regions', $arrRegions);
                                $this->set('countries', $arrCountries);
                                $this->set('cities', $arrCities);
                                $this->set('services_list', $this->Service->find('list', array('order' => 'Service.service_name ASC')));
                        } else {
                                foreach($arrOffice as $office) {
                                        $this->Office->create();
                                        $this->Office->save(
                                                array(
                                                        'Office' => array(
                                                                'region_id' => $office['regionId'],
                                                                'country_id' => $office['countryId'],
                                                                'city_id' => $office['cityId'],
                                                                'year_established' => $office['yearEstablished'],
                                                                'employee_count' => $office['employeeCount'],
                                                                'address' => $office['address'],
                                                                'recent_awards' => $office['awards'],
                                                                'news' => $office['news']
                                                        )
                                                )
                                        );
                                        $officeId = $this->Office->getLastInsertId();

                                        foreach($office['telephones'] as $telephone) {
                                                if(!empty($telephone)) {
                                                        $this->OfficeAttribute->create();
                                                        $this->OfficeAttribute->save(
                                                                array(
                                                                        'OfficeAttribute' => array(
                                                                                'office_id' => $officeId,
                                                                                'attribute_type' => 'telephone',
                                                                                'attribute_value' => $telephone
                                                                        )
                                                                )
                                                        );
                                                }
                                        }
                                        foreach($office['emails'] as $contactEmail) {
                                                if(!empty($contactEmail)) {
                                                        $this->OfficeAttribute->create();
                                                        $this->OfficeAttribute->save(
                                                                array(
                                                                        'OfficeAttribute' => array(
                                                                                'office_id' => $officeId,
                                                                                'attribute_type' => 'contact_email',
                                                                                'attribute_value' => $contactEmail
                                                                        )
                                                                )
                                                        );
                                                }
                                        }
                                        foreach($office['websites'] as $website) {
                                                if(!empty($website)) {
                                                        $this->OfficeAttribute->create();
                                                        $this->OfficeAttribute->save(
                                                                array(
                                                                        'OfficeAttribute' => array(
                                                                                'office_id' => $officeId,
                                                                                'attribute_type' => 'website',
                                                                                'attribute_value' => $website
                                                                        )
                                                                )
                                                        );
                                                }
                                        }
                                        foreach($office['socialAccounts'] as $socialAccount) {
                                                if(!empty($socialAccount)) {
                                                        $this->OfficeAttribute->create();
                                                        $this->OfficeAttribute->save(
                                                                array(
                                                                        'OfficeAttribute' => array(
                                                                                'office_id' => $officeId,
                                                                                'attribute_type' => 'social_account',
                                                                                'attribute_value' => $socialAccount
                                                                        )
                                                                )
                                                        );
                                                }
                                        }

                                        foreach($office['keyContacts'] as $keyDept => $deptContacts) {
                                                foreach($deptContacts as $deptContact) {
                                                        if(!empty($deptContact)) {
                                                                $dataDeptContact = explode('/', $deptContact);
                                                                $contactName = $dataDeptContact[0];
                                                                if(isset($dataDeptContact[1]) && $dataDeptContact[1] != 'title') {
                                                                        $contactTitle = $dataDeptContact[1];
                                                                } else {
                                                                        $contactTitle = '';
                                                                }
                                                                if(isset($dataDeptContact[2]) && $dataDeptContact[2] != 'email') {
                                                                        $contactEmail = $dataDeptContact[2];
                                                                } else {
                                                                        $contactEmail = '';
                                                                }
                                                                $this->OfficeKeyContact->create();
                                                                $this->OfficeKeyContact->save(
                                                                        array(
                                                                                'OfficeKeyContact' => array(
                                                                                        'office_id' => $officeId,
                                                                                        'contact_type' => $keyDept,
                                                                                        'contact_name' => $contactName,
                                                                                        'contact_title' => $contactTitle,
                                                                                        'contact_email' => $contactEmail
                                                                                )
                                                                        )
                                                                );
                                                        }
                                                }
                                        }

                                        foreach($office['servicesContacts'] as $service => $serviceContacts) {
                                                $serviceId = array_search(trim($service), $arrServices);
                                                foreach($serviceContacts as $serviceContact) {
                                                        if(!empty($serviceContact)) {
                                                                $dataServiceContact = explode('/', $serviceContact);
                                                                $contactName = $dataServiceContact[0];
                                                                if(isset($dataServiceContact[1]) && $dataServiceContact[1] != 'title') {
                                                                        $contactTitle = $dataServiceContact[1];
                                                                } else {
                                                                        $contactTitle = '';
                                                                }
                                                                if(isset($dataServiceContact[2]) && $dataServiceContact[2] != 'email') {
                                                                        $contactEmail = $dataServiceContact[2];
                                                                } else {
                                                                        $contactEmail = '';
                                                                }
                                                                $this->OfficeServiceContact->create();
                                                                $this->OfficeServiceContact->save(
                                                                        array(
                                                                                'OfficeServiceContact' => array(
                                                                                        'office_id' => $officeId,
                                                                                        'service_id' => $serviceId,
                                                                                        'contact_name' => $contactName,
                                                                                        'contact_title' => $contactTitle,
                                                                                        'contact_email' => $contactEmail
                                                                                )
                                                                        )
                                                                );
                                                        }
                                                }
                                        }

                                        foreach($arrKeyDepts as $keyDept) {
                                                $deptEmpCount = $office['deptEmpCount'][$keyDept];
                                                if(!empty($deptEmpCount)) {
                                                        if(is_numeric($deptEmpCount)) {
                                                                $countType = 'numeric';
                                                        } else {
                                                                $countType = 'FTE';
                                                                $deptEmpCount = (substr($deptEmpCount, 0, -1))/100;
                                                        }
                                                        $this->OfficeEmployeeCountByDepartment->create();
                                                        $this->OfficeEmployeeCountByDepartment->save(
                                                                array(
                                                                        'OfficeEmployeeCountByDepartment' => array(
                                                                                'office_id' => $officeId,
                                                                                'department_type' => $keyDept,
                                                                                'count_type' => $countType,
                                                                                'employee_count' => $deptEmpCount
                                                                        )
                                                                )
                                                        );
                                                }
                                        }
                                        foreach($arrServices as $serviceId => $service) {
                                                if(isset($office['deptEmpCount'][$service])) {
                                                        $serviceEmpCount = $office['deptEmpCount'][$service];
                                                        if(!empty($serviceEmpCount)) {
                                                                if(is_numeric($serviceEmpCount)) {
                                                                        $countType = 'numeric';
                                                                } else {
                                                                        $countType = 'FTE';
                                                                        $serviceEmpCount = (substr($serviceEmpCount, 0, -1))/100;
                                                                }
                                                                $this->OfficeEmployeeCountByDepartment->create();
                                                                $this->OfficeEmployeeCountByDepartment->save(
                                                                        array(
                                                                                'OfficeEmployeeCountByDepartment' => array(
                                                                                        'office_id' => $officeId,
                                                                                        'department_type' => 'service',
                                                                                        'department_id' => $serviceId,
                                                                                        'count_type' => $countType,
                                                                                        'employee_count' => $serviceEmpCount
                                                                                )
                                                                        )
                                                                );
                                                        }
                                                }
                                        }

                                        foreach($office['supportedLanguages'] as $supportedLanguage) {
                                                $languageId = array_search(trim($supportedLanguage), $arrLanguages);
                                                if(!empty($languageId)) {
                                                        $this->OfficeLanguage->create();
                                                        $this->OfficeLanguage->save(
                                                                array(
                                                                        'OfficeLanguage' => array(
                                                                                'office_id' => $officeId,
                                                                                'language_id' => $languageId
                                                                        )
                                                                )
                                                        );
                                                }
                                        }
                                }
                        }
                }
        }
}
