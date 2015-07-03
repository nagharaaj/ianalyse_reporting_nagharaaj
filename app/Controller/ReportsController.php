<?php
App::uses('CakeEmail', 'Network/Email');

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
            'OfficeAttribute',
            'OfficeKeyContact',
            'OfficeServiceContact',
            'OfficeLanguage',
            'OfficeEmployeeCountByDepartment',
            'Language',
            'PitchStage',
            'Division',
            'UserLoginRole',
            'ClientDeleteLog'
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
                $this->set('stages', json_encode($this->PitchStage->find('list', array('fields' => array('PitchStage.pitch_stage', 'PitchStage.pitch_stage'), 'order' => 'PitchStage.id Asc'))));
                $this->set('divisions', json_encode($this->Division->find('list', array('fields' => array('Division.division', 'Division.division'), 'order' => 'Division.id Asc'))));

                //$arrMarkets = array('Global' => 'Global');
                $arrMarkets = array();
                $arrRegions = array();
                $arrCities = array();
                if ($this->Auth->user('role') == 'Regional') {
                        $userRegion = $this->UserMarket->find('first', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $regions = $this->Region->find('all', array('conditions' => array('Region.id' => $userRegion['UserMarket']['market_id']), 'order' => 'Region.region Asc'));
                } else if($this->Auth->user('role') == 'Country') {
                        $userCountry = $this->UserMarket->find('list', array('fields' => array('UserMarket.id', 'UserMarket.market_id'), 'conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $userRegion = $this->Market->find('list', array('fields' => array('Market.id', 'Market.region_id'), 'conditions' => array('Market.country_id IN (' . implode(',', $userCountry) . ')'), 'order' => 'Market.region_id Asc', 'group' => 'Market.region_id'));
                        $regions = $this->Region->find('all', array('conditions' => array('Region.id IN (' . implode(',', $userRegion) . ')'), 'order' => 'Region.region Asc'));
                } else {
                        $regions = $this->Region->find('all', array('order' => 'Region.region Asc'));
                }
                foreach ($regions as $region) {
                        //$arrMarkets[$region['Region']['region']] = 'Regional - ' . $region['Region']['region'];
                        $arrRegions[$region['Region']['region']] = $region['Region']['region'];
                        if($this->Auth->user('role') == 'Country') {
                                $markets = $this->Market->find('list', array('fields' => array('Market.country_id', 'Market.market'), 'conditions' => array('Market.region_id' => $region['Region']['id'], 'Market.country_id IN (' . implode(',', $userCountry) . ')'), 'order' => 'Market.market Asc'));
                        } else {
                                $markets = $this->Market->find('list', array('fields' => array('Market.country_id', 'Market.market'), 'conditions' => array('Market.region_id' => $region['Region']['id']), 'order' => 'Market.market Asc'));
                        }
                        if(!empty($markets)) {
                                foreach ($markets as $countryId => $market)
                                {
                                        $arrMarkets[$region['Region']['region']][$market] = $market;
                                        $cities = $this->City->find('list', array('fields' => array('City.city', 'City.city'), 'conditions' => array('City.country_id' => $countryId), 'order' => 'City.city Asc'));
                                        if(!empty($cities)) {
                                                $arrCities[$market] = $cities;
                                        }
                                }
                        }
                }
                $this->set('regions', json_encode($arrRegions));
                $this->set('markets', json_encode($arrMarkets));
                $this->set('cities', json_encode($arrCities));
                $this->set('services', json_encode($this->Service->find('list', array('fields' => array('Service.service_name', 'Service.service_name'), 'order' => 'Service.service_name Asc'))));
                $this->set('currMonth', date('n'));
                $this->set('currYear', date('Y'));
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

                if ($this->request->isPost()) {
                        if($this->RequestHandler->isAjax()) {
                                $this->autoRender=false;
                        }

                        $arrData = $this->request->data;

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
                        $division = $this->Division->findByDivision(trim($arrData['Division']));

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
                        if(!empty($division) > 0) {
                                $divisionId = $division['Division']['id'];
                        } else {
                                $divisionId = 0;
                        }
                        $pitchStage = $arrData['PitchStage'];
                        $pitchStart = explode('/', $arrData['PitchStart']);
                        $pitchDate = $pitchStart[1] . '-' . $pitchStart[0] . '-01';
                        //$pitchLeader = $arrData['PitchLeader'];
                        if(!preg_match('/Live/', $pitchStage) && $pitchStage != 'Cancelled') {
                                $clientSinceMonth = $arrData['ClientSinceMonth'];
                                $clientSinceYear = $arrData['ClientSinceYear'];
                        } else {
                                $clientSinceMonth = null;
                                $clientSinceYear = null;
                        }
                        if(preg_match('/Lost/', $pitchStage) || $pitchStage == 'Cancelled') {
                                $lost = explode('/', $arrData['LostDate']);
                                $lostDate = $lost[1] . '-' . $lost[0] . '-01';
                        } else {
                                $lostDate = '0000-00-00';
                        }
                        $activeMarkets = $arrData['ActiveMarkets'];
                        $companyName = $arrData['ParentCompany'];
                        $clientName = $arrData['ClientName'];
                        $estimatedRevenue = $arrData['EstimatedRevenue'];
                        $comments = $arrData['Comments'];
                        $parentId = $arrData['parentId'];

                        $this->ClientRevenueByService->create();
                        $this->ClientRevenueByService->save(
                                array(
                                        'ClientRevenueByService' => array(
                                                'pitch_date' => $pitchDate,
                                                //'pitch_leader' => $pitchLeader,
                                                'pitch_stage' => $pitchStage,
                                                'client_name' => $clientName,
                                                'parent_company' => $companyName,
                                                'comments' => $comments,
                                                'category_id' => $categoryId,
                                                'client_since_month' => $clientSinceMonth,
                                                'client_since_year' => $clientSinceYear,
                                                'lost_date' => $lostDate,
                                                'agency_id' => $agencyId,
                                                'region_id' => $regionId,
                                                'managing_entity' => $managingEntity,
                                                'country_id' => $countryId,
                                                'city_id' => $cityId,
                                                'active_markets' => $activeMarkets,
                                                'service_id' => $serviceId,
                                                'division_id' => $divisionId,
                                                'currency_id' => $currencyId,
                                                'estimated_revenue' => $estimatedRevenue,
                                                'year' => date('Y'),
                                                'parent_id' => $parentId,
                                                'created' => date('Y-m-d H:i:s')
                                        )
                                )
                        );
                }
                if ($arrData) {
                        $this->UserLoginRole->Behaviors->attach('Containable');
                        $globalUsers = $this->UserLoginRole->find('all', array('fields' => array('User.display_name', 'User.email_id'), 'contain' => array('User', 'LoginRole'), 'conditions' => array('LoginRole.name' => 'Global'), 'order' => 'User.display_name'));

                        $emailTo = array();
                        foreach($globalUsers as $globalUser) {
                                $emailTo[] = $globalUser['User']['email_id'];
                        }

                        $email = new CakeEmail('gmail');
                        $email->viewVars(array('title_for_layout' => 'Client & New Business data', 'type' => 'New Pitch', 'data' => $arrData));
                        $email->template('new_pitch', 'default')
                            ->emailFormat('html')
                            ->to(array('mathilde.natier@iprospect.com'))
                            ->from(array('connectiprospect@gmail.com' => 'Connect iProspect'))
                            ->subject('New pitch added')
                            ->send();
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
                        }

                        $arrData = $this->request->data;
                        $clientRecord = $this->ClientRevenueByService->findById($arrData['RecordId']);
                        $clientRecord['loggedUser']['display_name'] = $this->Session->read('loggedUser.displayName');

                        if ($this->ClientRevenueByService->delete($arrData['RecordId'])) {
                                if ($clientRecord) {
                                        $this->ClientDeleteLog->create();
                                        $this->ClientDeleteLog->save(
                                                array(
                                                        'ClientDeleteLog' => array(
                                                                'record_id' => $clientRecord['ClientRevenueByService']['id'],
                                                                'pitch_date' => $clientRecord['ClientRevenueByService']['pitch_date'],
                                                                'pitch_stage' => $clientRecord['ClientRevenueByService']['pitch_stage'],
                                                                'lost_date' => $clientRecord['ClientRevenueByService']['lost_date'],
                                                                'parent_id' => $clientRecord['ClientRevenueByService']['parent_id'],
                                                                'client_name' => $clientRecord['ClientRevenueByService']['client_name'],
                                                                'parent_company' => $clientRecord['ClientRevenueByService']['parent_company'],
                                                                'comments' => $clientRecord['ClientRevenueByService']['comments'],
                                                                'category_id' => $clientRecord['ClientRevenueByService']['category_id'],
                                                                'client_since_month' => $clientRecord['ClientRevenueByService']['client_since_month'],
                                                                'client_since_year' => $clientRecord['ClientRevenueByService']['client_since_year'],
                                                                'agency_id' => $clientRecord['ClientRevenueByService']['agency_id'],
                                                                'region_id' => $clientRecord['ClientRevenueByService']['region_id'],
                                                                'managing_entity' => $clientRecord['ClientRevenueByService']['managing_entity'],
                                                                'country_id' => $clientRecord['ClientRevenueByService']['country_id'],
                                                                'city_id' => $clientRecord['ClientRevenueByService']['city_id'],
                                                                'active_markets' => $clientRecord['ClientRevenueByService']['active_markets'],
                                                                'service_id' => $clientRecord['ClientRevenueByService']['service_id'],
                                                                'division_id' => $clientRecord['ClientRevenueByService']['division_id'],
                                                                'currency_id' => $clientRecord['ClientRevenueByService']['currency_id'],
                                                                'estimated_revenue' => $clientRecord['ClientRevenueByService']['estimated_revenue'],
                                                                'actual_revenue' => $clientRecord['ClientRevenueByService']['actual_revenue'],
                                                                'year' => $clientRecord['ClientRevenueByService']['year'],
                                                                'created' => ($clientRecord['ClientRevenueByService']['created'] != '') ? $clientRecord['ClientRevenueByService']['created'] : 'NULL',
                                                                'modified' => ($clientRecord['ClientRevenueByService']['modified'] != '') ? $clientRecord['ClientRevenueByService']['modified'] : 'NULL',
                                                                'deleted_by' => $this->Auth->user('id'),
                                                                'deleted' => date('Y-m-d H:i:s')
                                                        )
                                                )
                                        );
                                
                                        $email = new CakeEmail('gmail');
                                        $email->viewVars(array('title_for_layout' => 'Client & New Business data', 'type' => 'Delete Pitch', 'data' => $clientRecord));
                                        $email->template('delete_pitch', 'default')
                                            ->emailFormat('html')
                                            ->to(array('mathilde.natier@iprospect.com'))
                                            ->from(array('connectiprospect@gmail.com' => 'Connect iProspect'))
                                            ->subject('Pitch is deleted')
                                            ->send();
                                }
                                
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
                $condition = NULL;
                if ($this->Auth->user('role') == 'Regional') {
                        $region = $this->UserMarket->find('first', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $condition = 'ClientRevenueByService.region_id = ' . $region['UserMarket']['market_id'];
                }
                if ($this->Auth->user('role') == 'Country') {
                        $countries = $this->UserMarket->find('all', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $arrCountries = array();
                        foreach ($countries as $country) {
                                $arrCountries[] = $country['UserMarket']['market_id'];
                        }
                        $condition = 'ClientRevenueByService.country_id IN (' . implode(',', $arrCountries) . ')';
                }
                $this->ClientRevenueByService->Behaviors->attach('Containable');
                $clients = $this->ClientRevenueByService->query("CALL allClientsWithFilter('{$condition}');");

                foreach ($clients as $client) {
                        $clientData[$i]['id'] = $client['ClientRevenueByService']['id'];
                        $clientData[$i]['RecordId'] = $client['ClientRevenueByService']['id'];
                        $clientData[$i]['Region'] = $client[0]['region'];
                        if ($client['ClientRevenueByService']['managing_entity'] == 'Global') {
                                $clientData[$i]['Country'] = 'Global';
                                $clientData[$i]['City'] = 'Global';
                        } elseif ($client['ClientRevenueByService']['managing_entity'] == 'Regional') {
                                $clientData[$i]['Country'] = 'Regional - ' . $client[0]['region'];
                                $clientData[$i]['City'] = 'Regional - ' . $client[0]['region'];
                        } else {
                                $clientData[$i]['Country'] = $client[0]['country'];
                                $clientData[$i]['City'] = $client[0]['city'];
                        }
                        $clientData[$i]['LeadAgency'] = $client[0]['agency'];
                        $clientData[$i]['ClientName'] = $client['ClientRevenueByService']['client_name'];
                        $clientData[$i]['ParentCompany'] = $client['ClientRevenueByService']['parent_company'];
                        $clientData[$i]['ClientCategory'] = $client[0]['category'];
                        if ($client['ClientRevenueByService']['pitch_date'] != '0000-00-00') {
                                $pitchDate = explode('-', $client['ClientRevenueByService']['pitch_date']);
                                $clientData[$i]['PitchStart'] = $pitchDate[1] . '/' . $pitchDate[0];
                        } else {
                                $clientData[$i]['PitchStart'] = '';
                        }
                        //$clientData[$i]['PitchLeader'] = $client['ClientRevenueByService']['pitch_leader'];
                        $clientData[$i]['PitchStage'] = $client['ClientRevenueByService']['pitch_stage'];
                        if ($client['ClientRevenueByService']['lost_date'] != '0000-00-00') {
                                $lostDate = explode('-', $client['ClientRevenueByService']['lost_date']);
                                $clientData[$i]['Lost'] = $lostDate[1] . '/' . $lostDate[0];
                        } else {
                                $clientData[$i]['Lost'] = '';
                        }
                        $clientData[$i]['ClientMonth'] = ($client['ClientRevenueByService']['client_since_month'] != 0 && $client['ClientRevenueByService']['client_since_month'] != null) ? $this->months[$client['ClientRevenueByService']['client_since_month']] : '';
                        $clientData[$i]['ClientYear'] = $client['ClientRevenueByService']['client_since_year'];
                        if ($client['ClientRevenueByService']['client_since_month'] != 0 && $client['ClientRevenueByService']['client_since_year'] != 0) {
                                $clientData[$i]['ClientSince'] = $client['ClientRevenueByService']['client_since_month']. '/' .$client['ClientRevenueByService']['client_since_year'];
                        } else if ($client['ClientRevenueByService']['client_since_month'] == 0 && $client['ClientRevenueByService']['client_since_year'] != 0) {
                                $clientData[$i]['ClientSince'] = '01/' .$client['ClientRevenueByService']['client_since_year'];
                        } else {
                                $clientData[$i]['ClientSince'] = '';
                        }
                        $clientData[$i]['Service'] = $client[0]['service_name'];
                        $clientData[$i]['Division'] = $client[0]['division'];
                        $clientData[$i]['ActiveMarkets'] = $client['ClientRevenueByService']['active_markets'];
                        $clientData[$i]['Currency'] = $client[0]['currency'];
                        $clientData[$i]['EstimatedRevenue'] = $client['ClientRevenueByService']['estimated_revenue'];
                        $clientData[$i]['ActualRevenue'] = $client['ClientRevenueByService']['actual_revenue'];
                        $clientData[$i]['Comments'] = $client['ClientRevenueByService']['comments'];
                        $clientData[$i]['Year'] = $client['ClientRevenueByService']['year'];
                        $clientData[$i]['ParentId'] = $client['ClientRevenueByService']['parent_id'];

                        $i++;
                }
                echo json_encode($clientData);
        }

        public function get_client_report_data() {
                $this->autoRender=false;

                $clientData = array();
                $i = 0;
                $condition = NULL;
                if ($this->Auth->user('role') == 'Regional') {
                        $region = $this->UserMarket->find('first', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        //$condition = 'ClientRevenueByService.region_id = ' . $region['UserMarket']['market_id'];
                }
                if ($this->Auth->user('role') == 'Country') {
                        $countries = $this->UserMarket->find('all', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $arrCountries = array();
                        foreach ($countries as $country) {
                                $arrCountries[] = $country['UserMarket']['market_id'];
                        }
                        //$condition = 'ClientRevenueByService.country_id IN (' . implode(',', $arrCountries) . ')';
                }
                $this->ClientRevenueByService->Behaviors->attach('Containable');
                $clients = $this->ClientRevenueByService->query("CALL allClientsWithFilter('{$condition}');");

                foreach ($clients as $client) {
                        $clientData[$i]['id'] = $client['ClientRevenueByService']['id'];
                        $clientData[$i]['RecordId'] = $client['ClientRevenueByService']['id'];
                        $clientData[$i]['Region'] = $client[0]['region'];
                        if ($client['ClientRevenueByService']['managing_entity'] == 'Global') {
                                $clientData[$i]['Country'] = 'Global';
                                $clientData[$i]['City'] = 'Global';
                        } elseif ($client['ClientRevenueByService']['managing_entity'] == 'Regional') {
                                $clientData[$i]['Country'] = 'Regional - ' . $client[0]['region'];
                                $clientData[$i]['City'] = 'Regional - ' . $client[0]['region'];
                        } else {
                                $clientData[$i]['Country'] = $client[0]['country'];
                                $clientData[$i]['City'] = $client[0]['city'];
                        }
                        $clientData[$i]['LeadAgency'] = $client[0]['agency'];
                        $clientData[$i]['ClientName'] = $client['ClientRevenueByService']['client_name'];
                        $clientData[$i]['ParentCompany'] = $client['ClientRevenueByService']['parent_company'];
                        $clientData[$i]['ClientCategory'] = $client[0]['category'];
                        if ($client['ClientRevenueByService']['pitch_date'] != '0000-00-00') {
                                $pitchDate = explode('-', $client['ClientRevenueByService']['pitch_date']);
                                $clientData[$i]['PitchStart'] = $pitchDate[1] . '/' . $pitchDate[0];
                        } else {
                                $clientData[$i]['PitchStart'] = '';
                        }
                        //$clientData[$i]['PitchLeader'] = $client['ClientRevenueByService']['pitch_leader'];
                        $clientData[$i]['PitchStage'] = $client['ClientRevenueByService']['pitch_stage'];
                        if ($client['ClientRevenueByService']['lost_date'] != '0000-00-00') {
                                $lostDate = explode('-', $client['ClientRevenueByService']['lost_date']);
                                $clientData[$i]['Lost'] = $lostDate[1] . '/' . $lostDate[0];
                        } else {
                                $clientData[$i]['Lost'] = '';
                        }
                        $clientData[$i]['ClientMonth'] = ($client['ClientRevenueByService']['client_since_month'] != 0 && $client['ClientRevenueByService']['client_since_month'] != null) ? $this->months[$client['ClientRevenueByService']['client_since_month']] : '';
                        $clientData[$i]['ClientYear'] = $client['ClientRevenueByService']['client_since_year'];
                        if ($client['ClientRevenueByService']['client_since_month'] != 0 && $client['ClientRevenueByService']['client_since_year'] != 0) {
                                $clientData[$i]['ClientSince'] = $client['ClientRevenueByService']['client_since_month']. '/' .$client['ClientRevenueByService']['client_since_year'];
                        } else if ($client['ClientRevenueByService']['client_since_month'] == 0 && $client['ClientRevenueByService']['client_since_year'] != 0) {
                                $clientData[$i]['ClientSince'] = '01/' .$client['ClientRevenueByService']['client_since_year'];
                        } else {
                                $clientData[$i]['ClientSince'] = '';
                        }
                        $clientData[$i]['Service'] = $client[0]['service_name'];
                        $clientData[$i]['Division'] = $client[0]['division'];
                        $clientData[$i]['ActiveMarkets'] = $client['ClientRevenueByService']['active_markets'];
                        if ($this->Auth->user('role') == 'Viewer') {
                                $clientData[$i]['EstimatedRevenue'] = '';
                                $clientData[$i]['ActualRevenue'] = '';
                                $clientData[$i]['Currency'] = '';
                        } else {
                                if ($this->Auth->user('role') == 'Regional') {
                                        if ($region['UserMarket']['market_id'] == $client['ClientRevenueByService']['region_id']) {
                                                $clientData[$i]['EstimatedRevenue'] = $client['ClientRevenueByService']['estimated_revenue'];
                                                $clientData[$i]['ActualRevenue'] = $client['ClientRevenueByService']['actual_revenue'];
                                                $clientData[$i]['Currency'] = $client[0]['currency'];
                                        } else {
                                                $clientData[$i]['EstimatedRevenue'] = '';
                                                $clientData[$i]['ActualRevenue'] = '';
                                                $clientData[$i]['Currency'] = '';
                                        }
                                } else if ($this->Auth->user('role') == 'Country') {
                                        if (in_array($client['ClientRevenueByService']['country_id'], $arrCountries)) {
                                                $clientData[$i]['EstimatedRevenue'] = $client['ClientRevenueByService']['estimated_revenue'];
                                                $clientData[$i]['ActualRevenue'] = $client['ClientRevenueByService']['actual_revenue'];
                                                $clientData[$i]['Currency'] = $client[0]['currency'];
                                        } else {
                                                $clientData[$i]['EstimatedRevenue'] = '';
                                                $clientData[$i]['ActualRevenue'] = '';
                                                $clientData[$i]['Currency'] = '';
                                        }
                                } else {
                                        $clientData[$i]['EstimatedRevenue'] = $client['ClientRevenueByService']['estimated_revenue'];
                                        $clientData[$i]['ActualRevenue'] = $client['ClientRevenueByService']['actual_revenue'];
                                        $clientData[$i]['Currency'] = $client[0]['currency'];
                                }
                        }
                        $clientData[$i]['Comments'] = $client['ClientRevenueByService']['comments'];
                        $clientData[$i]['Year'] = $client['ClientRevenueByService']['year'];
                        $clientData[$i]['ParentId'] = $client['ClientRevenueByService']['parent_id'];
                        $clientData[$i]['Created'] = $client['ClientRevenueByService']['created'];
                        $clientData[$i]['Modified'] = $client['ClientRevenueByService']['modified'];

                        $i++;
                }
                echo json_encode($clientData);
        }

        public function update_client_record() {
                if ($this->request->isPost()) {
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                        }

                        $arrData = $this->request->data;

                        $recordId = $arrData['RecordId'];
                        
                        $existingStatus = $this->ClientRevenueByService->find('first', array('fields' => array('pitch_stage'), 'conditions' => array('ClientRevenueByService.id' => $recordId)));

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
                        $division = $this->Division->findByDivision(trim($arrData['Division']));

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
                        if(!empty($division) > 0) {
                                $divisionId = $division['Division']['id'];
                        } else {
                                $divisionId = 0;
                        }
                        $pitchStage = trim($arrData['PitchStage']);
                        $pitchStart = explode('/', trim($arrData['PitchStart']));
                        $pitchDate = $pitchStart[1] . '-' . $pitchStart[0] . '-01';
                        //$pitchLeader = trim($arrData['PitchLeader']);
                        if(!preg_match('/Live/', $pitchStage) && $pitchStage != 'Cancelled') {
                                if(is_numeric(trim($arrData['ClientSinceMonth']))) {
                                        $clientMonth = trim($arrData['ClientSinceMonth']);
                                } else {
                                        $clientMonth = array_search(trim($arrData['ClientSinceMonth']), $this->months);
                                }
                                $clientYear = trim($arrData['ClientSinceYear']);
                        } else {
                                $clientMonth = null;
                                $clientYear = null;
                        }
                        if(preg_match('/Lost/', $pitchStage) || $pitchStage == 'Cancelled') {
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
                        $parentId = $arrData['ParentId'];

                        $this->ClientRevenueByService->id = $recordId;
                        $this->ClientRevenueByService->save(
                                array(
                                        'ClientRevenueByService' => array(
                                                'pitch_date' => $pitchDate,
                                                //'pitch_leader' => $pitchLeader,
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
                                                'division_id' => $divisionId,
                                                'currency_id' => $currencyId,
                                                'estimated_revenue' => $estimatedRevenue,
                                                'actual_revenue' => $actualRevenue,
                                                'year' => date('Y'),
                                                'modified' => date('Y-m-d H:i:s')
                                        )
                                )
                        );
                        
                        if($parentId == 0 || $parentId == null || $parentId == '') {
                                $assocRecords = $this->ClientRevenueByService->find('all', array('fields' => array('ClientRevenueByService.id'), 'conditions' => array('ClientRevenueByService.parent_id' => $recordId)));
                                foreach($assocRecords as $assocRecord) {
                                        $this->ClientRevenueByService->id = $assocRecord['ClientRevenueByService']['id'];
                                        $this->ClientRevenueByService->save(
                                                array(
                                                        'ClientRevenueByService' => array(
                                                                'pitch_date' => $pitchDate,
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
                                                                'division_id' => $divisionId,
                                                                'currency_id' => $currencyId,
                                                                'year' => date('Y'),
                                                                'modified' => date('Y-m-d H:i:s')
                                                        )
                                                )
                                        );
                                }
                        } else {
                                $assocRecords = $this->ClientRevenueByService->find('all', array('fields' => array('ClientRevenueByService.id'), 'conditions' => array('ClientRevenueByService.parent_id' => $parentId)));
                                foreach($assocRecords as $assocRecord) {
                                        $this->ClientRevenueByService->id = $assocRecord['ClientRevenueByService']['id'];
                                        $this->ClientRevenueByService->save(
                                                array(
                                                        'ClientRevenueByService' => array(
                                                                'pitch_date' => $pitchDate,
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
                                                                'division_id' => $divisionId,
                                                                'currency_id' => $currencyId,
                                                                'year' => date('Y'),
                                                                'modified' => date('Y-m-d H:i:s')
                                                        )
                                                )
                                        );
                                }
                                
                                $this->ClientRevenueByService->id = $parentId;
                                $this->ClientRevenueByService->save(
                                        array(
                                                'ClientRevenueByService' => array(
                                                        'pitch_date' => $pitchDate,
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
                                                        'division_id' => $divisionId,
                                                        'currency_id' => $currencyId,
                                                        'year' => date('Y'),
                                                        'modified' => date('Y-m-d H:i:s')
                                                )
                                        )
                                );
                        }
                }
                if ($arrData) {
                        if(preg_match('/Live/', $existingStatus['ClientRevenueByService']['pitch_stage']) && !preg_match('/Live/', $pitchStage)
                                && $existingStatus['ClientRevenueByService']['pitch_stage'] != $pitchStage) {
                                if(preg_match('/Lost/', $pitchStage) || $pitchStage == 'Cancelled') {
                                        $subject = 'Pitch is lost';
                                        $template = 'lost_pitch';
                                } else {
                                        $subject = 'Pitch is won';
                                        $template = 'won_pitch';
                                }
                                $this->UserLoginRole->Behaviors->attach('Containable');
                                $globalUsers = $this->UserLoginRole->find('all', array('fields' => array('User.display_name', 'User.email_id'), 'contain' => array('User', 'LoginRole'), 'conditions' => array('LoginRole.name' => 'Global'), 'order' => 'User.display_name'));

                                $emailTo = array();
                                foreach($globalUsers as $globalUser) {
                                        $emailTo[] = $globalUser['User']['email_id'];
                                }

                                $email = new CakeEmail('gmail');
                                $email->viewVars(array('title_for_layout' => 'Client & New Business data', 'type' => 'Pitch updated', 'data' => $arrData));
                                $email->template($template, 'default')
                                    ->emailFormat('html')
                                    ->to(array('mathilde.natier@iprospect.com'))
                                    ->from(array('connectiprospect@gmail.com' => 'iProspect Connect'))
                                    ->subject('iProspect Connect: ' . $subject)
                                    ->send();
                        }
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }

        public function client_report() {

                $this->set('currencies', json_encode($this->Currency->find('list', array('fields' => array('Currency.convert_rate', 'Currency.currency'), 'order' => 'Currency.currency Asc'))));
                $this->set('current_year', date('Y'));

                $this->set('loggedUser', $this->Auth->user());    
                $this->set('userAcl', $this->Acl);
                $this->set('userRole', $this->Auth->user('role'));
        }

        public function export_client_data() {
                set_time_limit(0);
                ini_set('memory_limit', '-1');

                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                        }
                        date_default_timezone_set($this->request->data['timezone']);

                        $arrData = $this->request->data['datarows'];
                        $exportCurrency = $this->request->data['currency'];
                        $exportFormat = $this->request->data['format'];
                        $currencies = $this->Currency->find('list', array('fields' => array('Currency.convert_rate', 'Currency.currency'), 'order' => 'Currency.currency Asc'));
                        $convertRatio = array_search($exportCurrency, $currencies);

                        App::import('Vendor', 'PHPExcel', array('file' => 'PhpExcel/PHPExcel.php'));
                        if (!class_exists('PHPExcel')) {
                                throw new CakeException('Vendor class PHPExcel not found!');
                        }
                        if($exportFormat == 'csv') {
                                App::import('Vendor', 'PHPExcel_Writer_CSV', array('file' => 'PhpExcel/PHPExcel/Writer/CSV.php'));
                                if (!class_exists('PHPExcel_Writer_CSV')) {
                                        throw new CakeException('Vendor class PHPExcel not found!');
                                }
                        } else {
                                App::import('Vendor', 'PHPExcel_Writer_Excel2007', array('file' => 'PhpExcel/PHPExcel/Writer/Excel2007.php'));
                                if (!class_exists('PHPExcel_Writer_Excel2007')) {
                                        throw new CakeException('Vendor class PHPExcel not found!');
                                }
                        }
                        $objPHPExcel = new PHPExcel();

                        // Set properties
                        $objPHPExcel->getProperties()->setCreator("Siddharth Kulkarni");
                        $objPHPExcel->getProperties()->setLastModifiedBy("Siddharth Kulkarni");
                        $objPHPExcel->getProperties()->setTitle("Client Data by date " . date('m/d/Y'));
                        $objPHPExcel->getProperties()->setSubject("Client Data by date " . date('m/d/Y'));


                        // Add some data
                        $objPHPExcel->setActiveSheetIndex(0);
                        if ($this->Auth->user('role') != 'Viewer') {
                                if($exportFormat != 'csv') {
                                        $objPHPExcel->getActiveSheet()->getStyle("A1:T1")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 12, 'name'  => 'Calibri'), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS, 'wrap' => true), 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
                                }
                        } else {
                                $objPHPExcel->getActiveSheet()->getStyle("A1:O1")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 12, 'name'  => 'Calibri'), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS, 'wrap' => true), 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
                        }
                        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCC0DA');
                        $objPHPExcel->getActiveSheet()->getStyle('F1:K1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C5D9F1');
                        if ($this->Auth->user('role') != 'Viewer') {
                                if($exportFormat != 'csv') {
                                        $objPHPExcel->getActiveSheet()->getStyle('L1:T1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCD5B4');
                                }
                        } else {
                                $objPHPExcel->getActiveSheet()->getStyle('L1:O1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCD5B4');
                        }
                        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(15);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(40);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(40);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(35);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(12);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(30);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(10);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(10);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(10);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(35);
                        if ($this->Auth->user('role') != 'Viewer') {
                                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(14);
                                $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(20);
                                $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(20);
                                $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(40);
                                $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(15);
                                $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(15);
                        } else {
                                $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(40);
                        }

                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Region');
                        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Country');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'City');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Client');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Parent Company');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Client Category');
                        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Lead Agency');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Status');
                        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Service');
                        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Division');
                        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Client Since (M-Y)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Lost Since(M-Y)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Pitched (M-Y)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Active Markets');
                        if ($this->Auth->user('role') == 'Viewer') {
                                $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Comments');
                        } else {
                                $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Currency');
                                $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'iP estimated revenue');
                                $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'iP 2014 Actual revenue');
                                $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Comments');
                                $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Created on');
                                $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Last modified on');
                        }

                        $i = 1;
                        $arrDataExcel = array();
                        foreach($arrData as $data) {
                                if($data['ClientSince'] != '') {
                                        $clientSince = date('m/Y', strtotime($data['ClientSince']));
                                } else {
                                        $clientSince = '';
                                }
                                if($data['Lost'] != '') {
                                        $lostDate = date('m/Y', strtotime($data['Lost']));
                                } else {
                                        $lostDate = '';
                                }
                                if($data['PitchStart'] != '') {
                                        $pitchDate = date('m/Y', strtotime($data['PitchStart']));
                                } else {
                                        $pitchDate = '';
                                }
                                if($exportCurrency == "Actual currencies") {
                                        $currency = $data['Currency'];
                                } else {
                                        $currency = $exportCurrency;
                                }
                                if($data['Created'] != '') {
                                        $createdDate = date('m/d/Y', strtotime($data['Created']));
                                } else {
                                        $createdDate = '01/01/2015';
                                }
                                if($data['Modified'] != '') {
                                        $modifiedDate = date('m/d/Y', strtotime($data['Modified']));
                                } else {
                                        $modifiedDate = '';
                                }
                                $estimatedRevenue = 0;
                                $actualRevenue = 0;
                                if ($this->Auth->user('role') != 'Viewer') {
                                        if($exportCurrency == "Actual currencies") {
                                                $estimatedRevenue = $data['EstimatedRevenue'];
                                                $actualRevenue = $data['ActualRevenue'];
                                        } else {
                                                if(is_numeric($data['EstimatedRevenue'])) {
                                                        if($data['Currency'] == $exportCurrency) {
                                                                $estimatedRevenue = $data['EstimatedRevenue'];
                                                        } else {
                                                                $dollarConvertRatio = array_search($data['Currency'], $currencies);
                                                                if($exportCurrency == "USD") {
                                                                     $estimatedRevenue = ($data['EstimatedRevenue'] * $dollarConvertRatio);
                                                                } else {
                                                                     $dollarEstRevenue = ($data['EstimatedRevenue'] * $dollarConvertRatio);
                                                                     $estimatedRevenue = ($dollarEstRevenue / $convertRatio);
                                                                }
                                                        }
                                                }
                                                if(is_numeric($data['ActualRevenue'])) {
                                                        if($data['Currency'] == $exportCurrency) {
                                                                $actualRevenue = $data['ActualRevenue'];
                                                        } else {
                                                                $dollarConvertRatio = array_search($data['Currency'], $currencies);
                                                                if($exportCurrency == "USD") {
                                                                     $actualRevenue = ($data['ActualRevenue'] * $dollarConvertRatio);
                                                                } else {
                                                                     $dollarEstRevenue = ($data['ActualRevenue'] * $dollarConvertRatio);
                                                                     $actualRevenue = ($dollarEstRevenue / $convertRatio);
                                                                }
                                                        }
                                                }
                                        }
                                        $arrDataExcel[] = array($data['Region'], $data['Country'], $data['City'], 
                                            $data['ClientName'], $data['ParentCompany'], $data['ClientCategory'], $data['LeadAgency'],
                                            $data['PitchStage'], $data['Service'], $data['Division'], $clientSince, $lostDate, $pitchDate,
                                            $data['ActiveMarkets'], $currency, $estimatedRevenue, $actualRevenue, $data['Comments'],
                                            $createdDate, $modifiedDate);
                                } else {
                                        $arrDataExcel[] = array($data['Region'], $data['Country'], $data['City'], 
                                            $data['ClientName'], $data['ParentCompany'], $data['ClientCategory'], $data['LeadAgency'],
                                            $data['PitchStage'], $data['Service'], $data['Division'], $clientSince, $lostDate, $pitchDate,
                                            $data['ActiveMarkets'], $data['Comments']);
                                }
                                $i++;
                        }
                        if(!empty($arrDataExcel)) {
                                if ($this->Auth->user('role') != 'Viewer') {
                                        $objPHPExcel->getActiveSheet()->getStyle('A2:T'.$i)->applyFromArray(array('font' => array('size'  => 11, 'name'  => 'Calibri'), 'alignment' => array('wrap' => true), 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
                                        $objPHPExcel->getActiveSheet()->getStyle('P2:P'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                                        $objPHPExcel->getActiveSheet()->getStyle('Q2:Q'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                                        $objPHPExcel->getActiveSheet()->fromArray($arrDataExcel, null, 'A2');
                                        $objPHPExcel->getActiveSheet()->setAutoFilter('A1:R'.$i);
                                } else {
                                        if($exportFormat != 'csv') {
                                                $objPHPExcel->getActiveSheet()->getStyle('A2:O'.$i)->applyFromArray(array('font' => array('size'  => 11, 'name'  => 'Calibri'), 'alignment' => array('wrap' => true), 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
                                        }
                                        $objPHPExcel->getActiveSheet()->fromArray($arrDataExcel, null, 'A2');
                                        if($exportFormat != 'csv') {
                                                $objPHPExcel->getActiveSheet()->setAutoFilter('A1:O'.$i);
                                        }
                                }
                        }

                        // Rename sheet
                        $objPHPExcel->getActiveSheet()->setTitle('Client List');

                        // Save Excel 2007/CSV file
                        if($exportFormat == 'csv') {
                                $fileName = 'Client_Data_' . date('m-d-Y') . '.csv';
                                $objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
                        } else {
                                $fileName = 'Client_Data_' . date('m-d-Y') . '.xlsx';
                                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                        }
                        $objWriter->save('files/' . $fileName);
                }
                $result = array('filename' => $fileName);
                $result['success'] = true;
                return json_encode($result);
        }

        public function client_report_new() {

        }

        public function office_data() {

                $arrKeyDepts = array('Executive' => 'executive', 'FinanceHead' => 'finance_head', 'ProductHead' => 'product_head', 'StrategyHead' => 'strategy_head', 'ClientHead' => 'client_head', 'BusinessHead' => 'business_head', 'MarketingHead' => 'marketing_head');
                $arrServices = array(1 => 'Affiliates', 2 => 'Content', 3 => 'Conversion', 4 => 'Data', 5 => 'Development', 6 => 'Display', 7 => 'Feeds', 8 => 'Lead', 9 => 'Mobile', 10 => 'RTB', 11 => 'Search', 12 => 'SEO', 13 => 'SocialPaid', 14 => 'SocialMangement', 15 => 'Strategy', 16 => 'Technology', 17 => 'Video');
                $countries = $this->Country->find('list', array('fields' => array('Country.country', 'Country.country'), 'order' => 'Country.country Asc'));
                $this->set('countries', json_encode($countries, JSON_HEX_APOS));
                $arrRegions = array();
                $regions = $this->Region->find('all', array('order' => 'Region.region Asc'));
                foreach ($regions as $region) {
                        $arrRegions[$region['Region']['region']] = $region['Region']['region'];
                }
                $this->set('regions', json_encode($arrRegions));
                $arrLanguages = $this->Language->find('list', array('fields' => array('Language.language', 'Language.language'), 'order' => 'Language.language Asc'));

                $this->set('departments', $arrKeyDepts);
                $this->set('services', $arrServices);
                $this->set('json_services', json_encode($arrServices));
                $this->set('languages', json_encode($arrLanguages));
                $this->set('userRole', $this->Auth->user('role'));
                $userMarkets = array();
                if($this->Auth->user('role') == 'Regional') {
                        $arrUserMarkets = $this->UserMarket->find('first', array('fields' => array('UserMarket.market_id'),'conditions' => array('UserMarket.user_id' => $this->Auth->user('id'), 'UserMarket.active' => 1)));
                        $userMarkets = $this->Region->find('list', array('conditions' => array('Region.id' => $arrUserMarkets['UserMarket']['market_id'])));
                } else if($this->Auth->user('role') == 'Country') { 
                        $arrUserMarkets = $this->UserMarket->find('all', array('fields' => array('UserMarket.market_id'),'conditions' => array('UserMarket.user_id' => $this->Auth->user('id'), 'UserMarket.active' => 1)));
                        $arrCountries = array();
                        foreach($arrUserMarkets as $arrUserMarket) {
                               $arrCountries[] = $arrUserMarket['UserMarket']['market_id'];
                        }
                        $userMarkets = $this->Country->find('list', array('conditions' => array('Country.id in (' . implode(',', $arrCountries) . ')')));
                }
                $this->set('userMarkets', json_encode($userMarkets));
        }

        public function get_office_data() {
                $this->autoRender=false;

                $officeData = array();
                $arrKeyDepts = array('Executive' => 'executive', 'FinanceHead' => 'finance_head', 'ProductHead' => 'product_head', 'StrategyHead' => 'strategy_head', 'ClientHead' => 'client_head', 'BusinessHead' => 'business_head', 'MarketingHead' => 'marketing_head');
                $arrServices = array(1 => 'Affiliates', 2 => 'Content', 3 => 'Conversion', 4 => 'Data', 5 => 'Development', 6 => 'Display', 7 => 'Feeds', 8 => 'Lead', 9 => 'Mobile', 10 => 'RTB', 11 => 'Search', 12 => 'SEO', 13 => 'SocialPaid', 14 => 'SocialMangement', 15 => 'Strategy', 16 => 'Technology', 17 => 'Video');
                $arrLanguages = $this->Language->find('list', array('fields' => array('Language.id', 'Language.language'), 'order' => 'Language.language Asc'));

                $i = 0;
                $conditions = array();
                $this->Office->Behaviors->attach('Containable');
                $offices = $this->Office->find('all', array('conditions' => $conditions, 'order' => 'Region.region Asc, Country.country, City.city'));

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
                                                $arrServiceEmployeeCount[$officeEmployeeCountByDepartment['department_id']] = round(($officeEmployeeCountByDepartment['employee_count']*100),2) . '%';
                                        } else {
                                                $arrServiceEmployeeCount[$officeEmployeeCountByDepartment['department_id']] = round($officeEmployeeCountByDepartment['employee_count'],2);
                                        }
                                        $totalServiceEmpCount += $officeEmployeeCountByDepartment['employee_count'];
                                } else {
                                        if($officeEmployeeCountByDepartment['count_type'] == 'FTE') {
                                                $arrKeyEmployeeCount[$officeEmployeeCountByDepartment['department_type']] = round(($officeEmployeeCountByDepartment['employee_count']*100),2) . '%';
                                        } else {
                                                $arrKeyEmployeeCount[$officeEmployeeCountByDepartment['department_type']] = round($officeEmployeeCountByDepartment['employee_count'],2);
                                        }
                                        $totalKeyEmpCount += $officeEmployeeCountByDepartment['employee_count'];
                                }
                        }

                        $keyContacts = array();
                        foreach($office['OfficeKeyContact'] as $officeKeyContact) {
                                $keyContacts[$officeKeyContact['contact_type']][] = $officeKeyContact['contact_name'] . (!empty($officeKeyContact['contact_title']) ? '/' . $officeKeyContact['contact_title'] : '/title') . (!empty($officeKeyContact['contact_email']) ? '/' . $officeKeyContact['contact_email'] : '/email');
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
                                $serviceContacts[$officeServiceContact['service_id']][] = $officeServiceContact['contact_name'] . (!empty($officeServiceContact['contact_title']) ? '/' . $officeServiceContact['contact_title'] : '/title') . (!empty($officeServiceContact['contact_email']) ? '/' . $officeServiceContact['contact_email'] : '/email');
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
                                $officeData[$i]['SupportedLanguages'] = implode(',', $supportedLanguages);
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

        public function save_office_record() {
                $arrKeyDepts = array('Executive' => 'executive', 'FinanceHead' => 'finance_head', 'ProductHead' => 'product_head', 'StrategyHead' => 'strategy_head', 'ClientHead' => 'client_head', 'BusinessHead' => 'business_head', 'MarketingHead' => 'marketing_head');
                $arrServices = array(1 => 'Affiliates', 2 => 'Content', 3 => 'Conversion', 4 => 'Data', 5 => 'Development', 6 => 'Display', 7 => 'Feeds', 8 => 'Lead', 9 => 'Mobile', 10 => 'RTB', 11 => 'Search', 12 => 'SEO', 13 => 'SocialPaid', 14 => 'SocialMangement', 15 => 'Strategy', 16 => 'Technology', 17 => 'Video');
                $arrLanguages = $this->Language->find('list', array('fields' => array('Language.id', 'Language.language'), 'order' => 'Language.language Asc'));

                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                        }
                        $arrData = $this->request->data;
                        if(!empty($arrData['RecordId'])) {
                                $officeId = $arrData['RecordId'];

                                $this->Office->id = $officeId;
                                $this->Office->save(
                                        array(
                                                'Office' => array(
                                                        'year_established' => $arrData['YearEstablished'],
                                                        'employee_count' => $arrData['EmployeeCount'],
                                                        'address' => $arrData['Address']
                                                )
                                        )
                                );

                                $this->OfficeAttribute->query('DELETE FROM `office_attributes` WHERE `office_id` = ' . $officeId);
                                $this->OfficeKeyContact->query('DELETE FROM `office_key_contacts` WHERE `office_id` = ' . $officeId);
                                $this->OfficeServiceContact->query('DELETE FROM `office_service_contacts` WHERE `office_id` = ' . $officeId);
                                $this->OfficeEmployeeCountByDepartment->query('DELETE FROM `office_employee_count_by_departments` WHERE `office_id` = ' . $officeId);
                                $this->OfficeLanguage->query('DELETE FROM `office_languages` WHERE `office_id` = ' . $officeId);
                        } else {
                                $region = $this->Region->findByRegion(trim($arrData['Region']));
                                $country = $this->Country->findByCountry(trim($arrData['Country']));
                                $marketExists = $this->Market->find('count', array('conditions' => array('region_id' => $region['Region']['id'], 'country_id' => $country['Country']['id'])));
                                if($marketExists == 0) {
                                        $this->Market->create();
                                        $this->Market->save(
                                                array('Market' => array(
                                                                'region_id' => $region['Region']['id'],
                                                                'country_id' => $country['Country']['id'],
                                                                'market' => $arrData['Country']
                                                        )
                                                )
                                        );
                                }

                                $city = $arrData['City'];
                                $this->City->create();
                                $this->City->save(
                                        array('City' => array(
                                                        'city' => $city,
                                                        'country_id' => $country['Country']['id']
                                                )
                                        )
                                );
                                $cityId = $this->City->getLastInsertId();

                                $this->Office->create();
                                $this->Office->save(
                                        array(
                                                'Office' => array(
                                                        'region_id' => $region['Region']['id'],
                                                        'country_id' => $country['Country']['id'],
                                                        'city_id' => $cityId,
                                                        'year_established' => $arrData['YearEstablished'],
                                                        'employee_count' => $arrData['EmployeeCount'],
                                                        'address' => $arrData['Address']
                                                )
                                        )
                                );
                                $officeId = $this->Office->getLastInsertId();
                        }

                        if(!empty($arrData['Telephone'])) {
                                $this->OfficeAttribute->create();
                                $this->OfficeAttribute->save(
                                        array(
                                                'OfficeAttribute' => array(
                                                        'office_id' => $officeId,
                                                        'attribute_type' => 'telephone',
                                                        'attribute_value' => $arrData['Telephone']
                                                )
                                        )
                                );
                        }
                        if(!empty($arrData['ContactEmail'])) {
                                $this->OfficeAttribute->create();
                                $this->OfficeAttribute->save(
                                        array(
                                                'OfficeAttribute' => array(
                                                        'office_id' => $officeId,
                                                        'attribute_type' => 'contact_email',
                                                        'attribute_value' => $arrData['ContactEmail']
                                                )
                                        )
                                );
                        }
                        if(!empty($arrData['Website'])) {
                                $this->OfficeAttribute->create();
                                $this->OfficeAttribute->save(
                                        array(
                                                'OfficeAttribute' => array(
                                                        'office_id' => $officeId,
                                                        'attribute_type' => 'website',
                                                        'attribute_value' => $arrData['Website']
                                                )
                                        )
                                );
                        }
                        if(!empty($arrData['SocialAccount'])) {
                                $this->OfficeAttribute->create();
                                $this->OfficeAttribute->save(
                                        array(
                                                'OfficeAttribute' => array(
                                                        'office_id' => $officeId,
                                                        'attribute_type' => 'social_account',
                                                        'attribute_value' => $arrData['SocialAccount']
                                                )
                                        )
                                );
                        }

                        foreach($arrData['KeyContacts'] as $deptContacts) {
                                $keyDept = $arrKeyDepts[$deptContacts['dept_name']];
                                foreach($deptContacts['dept_contacts'] as $deptContact) {
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
                                $deptEmpCount = $deptContacts['dept_emp_count'];
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

                        foreach($arrData['ServicesContacts'] as $serviceContacts) {
                                $serviceId = array_search($serviceContacts['service_name'], $arrServices);
                                foreach($serviceContacts['service_contacts'] as $serviceContact) {
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
                                $serviceEmpCount = $serviceContacts['service_emp_count'];
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

                        $supportedLanguages = explode(',', $arrData['SupportedLanguages']);
                        foreach($supportedLanguages as $supportedLanguage) {
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
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }

        public function delete_office_record() {
                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                        }

                        $arrData = $this->request->data;

                        if ($this->Office->delete($arrData['RecordId'])) {
                                $this->OfficeAttribute->query('DELETE FROM `office_attributes` WHERE `office_id` = ' . $arrData['RecordId']);
                                $this->OfficeKeyContact->query('DELETE FROM `office_key_contacts` WHERE `office_id` = ' . $arrData['RecordId']);
                                $this->OfficeServiceContact->query('DELETE FROM `office_service_contacts` WHERE `office_id` = ' . $arrData['RecordId']);
                                $this->OfficeEmployeeCountByDepartment->query('DELETE FROM `office_employee_count_by_departments` WHERE `office_id` = ' . $arrData['RecordId']);
                                $this->OfficeLanguage->query('DELETE FROM `office_languages` WHERE `office_id` = ' . $arrData['RecordId']);

                                $result = array();
                                $result['success'] = true;
                                return json_encode($result);
                        }
                }
        }

        public function export_office_data() {
                set_time_limit(0);
                ini_set('memory_limit', '-1');

                if ($this->request->isPost())
		{
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
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
                        $objPHPExcel->getProperties()->setCreator($this->Auth->user('display_name'));
                        $objPHPExcel->getProperties()->setLastModifiedBy($this->Auth->user('display_name'));
                        $objPHPExcel->getProperties()->setTitle("Office Data by date " . date('m/d/Y'));
                        $objPHPExcel->getProperties()->setSubject("Office Data by date " . date('m/d/Y'));

                        // Add some data
                        $objPHPExcel->setActiveSheetIndex(0);
                        $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
                        $objPHPExcel->getActiveSheet()->mergeCells('F1:J1');
                        $objPHPExcel->getActiveSheet()->mergeCells('K1:Y1');
                        $objPHPExcel->getActiveSheet()->mergeCells('Z1:AA1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AB1:AC1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AD1:AE1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AF1:AG1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AH1:AI1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AJ1:AK1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AL1:AM1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AN1:AO1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AP1:AQ1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AR1:AS1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AT1:AU1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AV1:AW1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AX1:AY1');
                        $objPHPExcel->getActiveSheet()->mergeCells('AZ1:BA1');
                        $objPHPExcel->getActiveSheet()->mergeCells('BB1:BC1');
                        $objPHPExcel->getActiveSheet()->mergeCells('BD1:BE1');
                        $objPHPExcel->getActiveSheet()->mergeCells('BF1:BG1');
                        $objPHPExcel->getActiveSheet()->mergeCells('BI1:BJ1');
                        $objPHPExcel->getActiveSheet()->mergeCells('BK1:BL1');
                        
                        $objPHPExcel->getActiveSheet()->getStyle("A1:BL2")->applyFromArray(array("font" => array( "bold" => true, 'size'  => 12, 'name'  => 'Calibri'), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS, 'wrap' => true), 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
                        //$objPHPExcel->getActiveSheet()->getStyle('A1:BL999')->getAlignment()->setWrapText(true);
                        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCC0DA');
                        $objPHPExcel->getActiveSheet()->getStyle('F1:J1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C5D9F1');
                        $objPHPExcel->getActiveSheet()->getStyle('K1:Y1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCC0DA');
                        $objPHPExcel->getActiveSheet()->getStyle('Z1:BH1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C5D9F1');
                        $objPHPExcel->getActiveSheet()->getStyle('BI1:BJ1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCC0DA');
                        $objPHPExcel->getActiveSheet()->getStyle('BK1:BL1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C5D9F1');
                        $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCC0DA');
                        $objPHPExcel->getActiveSheet()->getStyle('F2:J2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C5D9F1');
                        $objPHPExcel->getActiveSheet()->getStyle('K2:Y2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCC0DA');
                        $objPHPExcel->getActiveSheet()->getStyle('Z2:BH2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C5D9F1');
                        $objPHPExcel->getActiveSheet()->getStyle('BI2:BJ2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCC0DA');
                        $objPHPExcel->getActiveSheet()->getStyle('BK2:BL2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C5D9F1');

                        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(11);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(11);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(18);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(14);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(14);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(16);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(19);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(18);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(34);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(23);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(23);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("X")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AD")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AE")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AF")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AH")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AI")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AJ")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AK")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AL")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AM")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AN")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AO")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AP")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AQ")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AR")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AS")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AT")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AU")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AV")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AW")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AX")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AY")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("AZ")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BA")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BB")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BC")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BD")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BE")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BF")->setWidth(28);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BG")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BH")->setWidth(9);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BI")->setWidth(11);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BJ")->setWidth(17);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BK")->setWidth(20);
                        $objPHPExcel->getActiveSheet()->getColumnDimension("BL")->setWidth(20);
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'General Information');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Contact details');
                        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Key management contacts');
                        $objPHPExcel->getActiveSheet()->SetCellValue('Z1', 'Affiliates');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AB1', 'Content');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AD1', 'Conversion Opt.');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AF1', 'Data and Insights');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AH1', 'Development');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AJ1', 'Display');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AL1', 'Feeds');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AN1', 'Lead Gen');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AP1', 'Mobile');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AR1', 'STB');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AT1', 'Search - PPC');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AV1', 'SEO');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AX1', 'Social - Paid');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AZ1', 'Social - Management');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BB1', 'Strategy');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BD1', 'Technology');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BF1', 'Video');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BH1', '');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BI1', 'Languages');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BK1', 'Other');

                        $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Region');
                        $objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Market');
                        $objPHPExcel->getActiveSheet()->SetCellValue('C2', 'Location name (City)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('D2', 'Year established');
                        $objPHPExcel->getActiveSheet()->SetCellValue('E2', 'Total # employees');
                        $objPHPExcel->getActiveSheet()->SetCellValue('F2', 'Address');
                        $objPHPExcel->getActiveSheet()->SetCellValue('G2', 'Telephone number');
                        $objPHPExcel->getActiveSheet()->SetCellValue('H2', 'General email contact');
                        $objPHPExcel->getActiveSheet()->SetCellValue('I2', 'Website');
                        $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Twitter');
                        $objPHPExcel->getActiveSheet()->SetCellValue('K2', 'Executive Contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('L2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('M2', 'CFO or Finacial Lead (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('N2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('O2', 'Head of Products & Services (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('P2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('Q2', 'Head of Stratetgy (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('R2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('S2', 'Head of Client Services (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('T2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('U2', 'New Business (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('V2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('W2', 'Marketing (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('X2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('Y2', 'Total # management employees');
                        $objPHPExcel->getActiveSheet()->SetCellValue('Z2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AA2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AB2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AC2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AD2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AE2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AF2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AG2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AH2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AI2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AJ2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AK2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AL2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AM2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AN2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AO2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AP2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AQ2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AR2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AS2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AT2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AU2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AV2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AW2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AX2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AY2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('AZ2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BA2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BB2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BC2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BD2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BE2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BF2', 'Key contact (Name/title/email address)');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BG2', '# employees or % FTE');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BH2', 'Total # management employees');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BI2', '# of supported languages');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BJ2', 'List supported languages');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BK2', 'Recent awards');
                        $objPHPExcel->getActiveSheet()->SetCellValue('BL2', 'Interesting news');

                        $i = 2;
                        $arrDataExcel = array();
                        foreach($arrData as $data) {
                                $arrDataExcel[] = array($data['Region'], $data['Country'], $data['City'], $data['YearEstablished'], 
                                    $data['TotalEmployee'], $data['Address'], $data['Telephone'], $data['GeneralEmail'], $data['Website'], $data['SocialAccount'],
                                    $data['Executive'], $data['countExecutive'], $data['FinanceHead'], $data['countFinanceHead'], $data['ProductHead'], $data['countProductHead'],
                                    $data['StrategyHead'], $data['countStrategyHead'], $data['ClientHead'], $data['countClientHead'], $data['BusinessHead'], $data['countBusinessHead'],
                                    $data['MarketingHead'], $data['countMarketingHead'], $data['totalKeyEmployeeCount'], $data['Affiliates'], $data['countAffiliates'],
                                    $data['Content'], $data['countContent'], $data['Conversion'], $data['countConversion'], $data['Data'], $data['countData'],
                                    $data['Development'], $data['countDevelopment'], $data['Display'], $data['countDisplay'], $data['Feeds'], $data['countFeeds'],
                                    $data['Lead'], $data['countLead'], $data['Mobile'], $data['countMobile'], $data['RTB'], $data['countRTB'], $data['Search'], $data['countSearch'],
                                    $data['SEO'], $data['countSEO'], $data['SocialPaid'], $data['countSocialPaid'], $data['SocialMangement'], $data['countSocialMangement'],
                                    $data['Strategy'], $data['countStrategy'], $data['Technology'], $data['countTechnology'], $data['Video'], $data['countVideo'], $data['totalServiceEmployeeCount'],
                                    $data['countSupportedLanguages'], $data['SupportedLanguages'], $data['RecentAwards'], $data['News']);
                                $i++;
                        }
                        if(!empty($arrDataExcel)) {
                                $objPHPExcel->getActiveSheet()->getStyle('A3:BL'.$i)->applyFromArray(array('font' => array('size'  => 11, 'name'  => 'Calibri'), 'alignment' => array('wrap' => true), 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
                                $objPHPExcel->getActiveSheet()->fromArray($arrDataExcel, null, 'A3');
                                $objPHPExcel->getActiveSheet()->setAutoFilter('A2:BL'.$i);
                        }

                        // Rename sheet
                        $objPHPExcel->getActiveSheet()->setTitle('Offices List');

                        // Save Excel 2007 file
                        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                        $objWriter->save('files/Office_Data_' . date('m-d-Y') . '.xlsx');
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }

        public function associate_records() {
                
        }

        public function deassociate_records() {
                
        }

}
