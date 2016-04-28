<?php
App::import('Vendor', 'Ldap', array('file' => 'Ldap' . DS . 'Ldap.class.php'));

class UsersController extends AppController {
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
            'LoginRole',
            'User',
            'UserLoginRole',
            'UserMarket',
            'UserMailNotificationClient',
            'UserAdminAccess',
            'AdministrationLink'
        );

        public $name = 'Users';
        public $ldapConfig = null;

        public function beforeFilter() {

                $this->Auth->allow('login', 'logout');

                $this->Auth->loginAction = array(
                  'controller' => 'users',
                  'action' => 'login'
                );
                $this->Auth->logoutRedirect = array(
                  'controller' => 'users',
                  'action' => 'login'
                );
                $this->Auth->loginRedirect = array(
                  'controller' => 'reports',
                  'action' => 'client_report'
                );
                $this->Auth->authError = array(
                  'controller' => 'dashboard',
                  'action' => 'index'
                );

                $this->ldapConfig = Configure::read('IP.ldap_configuration');
        }

        public function beforeRender() {
                if($this->Auth->user()) {
                        $this->set('admNavLinks', parent::generateNav($this->arrNav, $this->Auth->user()));
                }
        }

        public function login() {
                $domain     = $this->ldapConfig['domain'];
                $baseDN     = $this->ldapConfig['base_dn'];
                $ldapServer = $this->ldapConfig['ldap_server'];
                $ldapPort   = $this->ldapConfig['ldap_port'];

                $this->set('title_for_layout', 'Log In');

                if ($this->request->is('post')) {
                        $username = $this->request->data['User']['username'];
                        $password = $this->request->data['User']['password'];

                        $ldap = new CLdapLogin($ldapServer, $ldapPort, $domain, $username, $password, $baseDN);
                        if (true == $ldap->login()) {
                                $loggedUser = $this->User->find('first', array('conditions' => array('User.username' => $username, 'User.is_active' => 1)));
                                if($loggedUser) {
                                        $userLoginRole = $this->UserLoginRole->find('first', array('conditions' => array('UserLoginRole.user_id' => $loggedUser['User']['id'])));
                                        $userId = $loggedUser['User']['id'];
                                        $roleId = $userLoginRole['UserLoginRole']['role_id'];
                                        $displayName = $loggedUser['User']['display_name'];
                                        $this->request->data['User'] = array_merge(
                                            $this->request->data['User'],
                                            array('id' => $userId, 'role_id' => $roleId, 'role' => $userLoginRole['LoginRole']['name'], 'display_name' => $displayName)
                                        );
                                        unset($this->request->data['User']['password']);
                                        $this->Auth->login($this->request->data['User']);
                                        $this->Session->write('loggedUser.displayName', $displayName);
                                        $this->Session->write('loggedUser.role', $userLoginRole['LoginRole']['name']);
                                        return $this->redirect($this->Auth->redirect());
                                }
                        }
                        $this->set('complete', false);
                        $this->data = array();
                }
        }

        public function logout() {
                $this->Session->delete('loggedUser');
                $this->Session->destroy();
                return $this->redirect($this->Auth->logout());
        }

        public function user_permissions() {

                $this->set('countries', json_encode($this->Country->find('list', array('fields' => array('Country.country', 'Country.country'), 'order' => 'Country.country Asc')), JSON_HEX_APOS));
                $markets = $this->Market->find('all', array('order' => 'Country.country Asc'));
                foreach ($markets as $market) {
                        $arrMarkets[$market['Market']['market']] = $market['Market']['market'];
                }
                $this->set('markets', json_encode($arrMarkets));
                $this->set('regions', json_encode($this->Region->find('list', array('order' => 'Region.region Asc'))));
                $this->set('loginRoles', json_encode($this->LoginRole->find('list', array('order' => 'LoginRole.id Asc'))));
                $this->set('adminLinks', $this->AdministrationLink->find('list', array('order' => 'AdministrationLink.id Asc')));
        }

        public function get_client_list() {
                $this->autoRender=false;

                $clients = $this->ClientRevenueByService->find('all', array('fields' => array('DISTINCT parent_company', 'client_name'), 'order' => 'client_name', 'group' => 'client_name'));
                $clientList = array();
                foreach($clients as $client) {
                        $clientList[] = array(
                            'display_name' => $client['ClientRevenueByService']['client_name'] . (!empty($client['ClientRevenueByService']['parent_company']) ? ' - ' . $client['ClientRevenueByService']['parent_company'] : ''),
                            'client_name' => $client['ClientRevenueByService']['client_name']
                        );
                }
                return json_encode($clientList);
        }

        public function search_user() {

                $this->autoRender=false;

                $username 	= $this->ldapConfig['ldap_user'];
                $baseDN         = $this->ldapConfig['base_dn'];
                $ldapServer     = $this->ldapConfig['ldap_server'];
                $ldapPort       = $this->ldapConfig['ldap_port'];
                $domain         = $this->ldapConfig['domain'];
                $password 	= $this->ldapConfig['ldap_passwd'];

                $ldap = new CLdapLogin($ldapServer, $ldapPort, $domain, $username, $password, $baseDN);

                if($this->request->data['name_startsWith']) {
                        $nameStartsWith = $this->request->data['name_startsWith'];
                }
                if (false == $ldap->login()) {
                        echo '<div class="alert alert-danger">Failed to login with UserName :: <u>' . $ldap->getUserName(true) . '</u></div>';
                } else {
                        // This code is used to display the user name and group associated to that user.
                        $ldap->getAllUserInfo($nameStartsWith);
                }
        }

        public function save_user() {
                $this->autoRender=false;

                $arrData = $this->request->data;

                $isActive = $arrData['activeflag'];

                if(isset($arrData['username'])) {
                        $userExists = $this->User->find('first', array('conditions' => array('User.username' => $arrData['username'])));
                        if(isset($userExists['User']['id'])) {
                                $result = array();
                                $result['success'] = false;
                                $result['errors'] = 'User already exists...';
                                return json_encode($result);
                        }
                }

                $this->User->create();
                $this->User->save(
                        array(
                                'User' => array(
                                        'display_name' => $arrData['displayname'],
                                        'username' => $arrData['username'],
                                        'title' => $arrData['title'],
                                        'location' => $arrData['location'],
                                        'email_id' => $arrData['email'],
                                        'is_active' => $isActive,
                                        'daily_sync_mail' => $arrData['dailysyncmails'],
                                        'weekly_summary_mail' => $arrData['weeklysummarymails'],
                                        'client_specific_mail' => $arrData['clientpitchmails']
                                )
                        )
                );
                $userId = $this->User->getLastInsertId();

                if(isset($arrData['permission'])) {
                        $loginRole = $this->LoginRole->findByName($arrData['permission']);
                        $loginRoleId = $loginRole['LoginRole']['id'];
                        $this->UserLoginRole->create();
                        $this->UserLoginRole->save(
                                array(
                                        'UserLoginRole' => array(
                                            'user_id' => $userId,
                                            'role_id' => $loginRoleId,
                                            'active' => $isActive
                                        )
                                )
                        );

                        if($arrData['permission'] == 'Regional' && isset($arrData['nameofentity'])) {
                                $regions = $this->Region->find('all', array('conditions' => array('Region.region in (\'' . str_replace(",", "','", $arrData['nameofentity']) . '\')')));
                                foreach($regions as $region) {
                                        $this->UserMarket->create();
                                        $this->UserMarket->save(
                                                array(
                                                        'UserMarket' => array(
                                                            'user_id' => $userId,
                                                            'market_id' => $region['Region']['id'],
                                                            'active' => $isActive
                                                        )
                                                )
                                        );
                                }
                        }

                        if(($arrData['permission'] == 'Country' || $arrData['permission'] == 'Country - Viewer') && isset($arrData['nameofentity'])) {
                                $countries = $this->Market->find('all', array('conditions' => array('Market.market in (\'' . str_replace(",", "','", $arrData['nameofentity']) . '\')')));
                                foreach($countries as $country) {
                                        $this->UserMarket->create();
                                        $this->UserMarket->save(
                                                array(
                                                        'UserMarket' => array(
                                                            'user_id' => $userId,
                                                            'market_id' => $country['Market']['country_id'],
                                                            'active' => $isActive
                                                        )
                                                )
                                        );
                                }
                        }

                        if($arrData['permission'] == 'Global' && $arrData['clientpitchmails'] === true && $arrData['targetclients'] != '') {
                                $targetClients = explode(',', $arrData['targetclients']);
                                foreach($targetClients as $targetClient) {
                                        $this->UserMailNotificationClient->create();
                                        $this->UserMailNotificationClient->save(
                                                array(
                                                        'UserMailNotificationClient' => array(
                                                            'user_id' => $userId,
                                                            'client_name' => $targetClient
                                                        )
                                                )
                                        );
                                }
                        }

                        if($arrData['permission'] == 'Global' && isset($arrData['adminlinks'])) {
                                foreach($arrData['adminlinks'] as $adminLink) {
                                        $this->UserAdminAccess->create();
                                        $this->UserAdminAccess->save(
                                                array(
                                                        'UserAdminAccess' => array(
                                                            'user_id' => $userId,
                                                            'admin_link_id' => $adminLink
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

        public function get_users() {
                $this->autoRender=false;

                $userData = array();
                $i = 0;
                $users = $this->User->find('all', array('order' => 'User.display_name Asc'));
                foreach($users as $user) {
                        $userData[$i]['targetclients'] = '';

                        $userData[$i]['userid'] = $user['User']['id'];
                        $userData[$i]['displayname'] = $user['User']['display_name'];
                        $userData[$i]['title'] = $user['User']['title'];
                        $userData[$i]['location'] = $user['User']['location'];
                        $userData[$i]['email'] = $user['User']['email_id'];

                        $userLoginRole = $this->UserLoginRole->find('first', array('conditions' => array('UserLoginRole.user_id' => $user['User']['id'])));
                        $userData[$i]['permission'] = $userLoginRole['LoginRole']['name'];

                        if($userLoginRole['LoginRole']['name'] == 'Regional') {
                                $arrRegions = array();
                                $userMarkets = $this->UserMarket->find('all', array('conditions' => array('UserMarket.user_id' => $user['User']['id'])));
                                foreach($userMarkets as $userMarket) {
                                        $region = $this->Region->find('first', array('conditions' => array('Region.id' => $userMarket['UserMarket']['market_id'])));
                                        $arrRegions[] = $region['Region']['region'];
                                }
                                $userData[$i]['nameofentity'] = implode(",", $arrRegions);
                        } elseif($userLoginRole['LoginRole']['name'] == 'Country' || $userLoginRole['LoginRole']['name'] == 'Country - Viewer') {
                                $arrCountries = array();
                                $userMarkets = $this->UserMarket->find('all', array('conditions' => array('UserMarket.user_id' => $user['User']['id'])));
                                foreach($userMarkets as $userMarket) {
                                        $country = $this->Market->find('first', array('conditions' => array('Market.country_id' => $userMarket['UserMarket']['market_id'])));
                                        $arrCountries[] = $country['Market']['market'];
                                }
                                $userData[$i]['nameofentity'] = implode(",", $arrCountries);
                        } elseif($userLoginRole['LoginRole']['name'] == 'Global') {
                                $userData[$i]['nameofentity'] = 'Global';

                                $targetClients = $this->UserMailNotificationClient->find('list', array('conditions' => array('user_id' => $user['User']['id'])));
                                $arrTargetClients = array();
                                foreach($targetClients as $targetClient) {
                                        $arrTargetClients[] = $targetClient;
                                }
                                $userData[$i]['targetclients'] = implode(',', $arrTargetClients);

                                $adminLinks = $this->UserAdminAccess->find('list', array('fields' => array('id', 'admin_link_id'), 'conditions' => array('user_id' => $user['User']['id'])));
                                $arrAdminLinks = array();
                                foreach($adminLinks as $adminLink) {
                                        $arrAdminLinks[] = $adminLink;
                                }
                                $userData[$i]['adminlinks'] = $arrAdminLinks;
                        } elseif($userLoginRole['LoginRole']['name'] == 'Viewer') {
                                $userData[$i]['nameofentity'] = '/';
                        }

                        $userData[$i]['active'] = $user['User']['is_active'];
                        $userData[$i]['dailysyncmail'] = $user['User']['daily_sync_mail'];
                        $userData[$i]['weeklysummarymail'] = $user['User']['weekly_summary_mail'];
                        $userData[$i]['clientpitchmail'] = $user['User']['client_specific_mail'];

                        $i++;
                }
                echo json_encode($userData);
        }

        public function update_user() {
                $this->autoRender=false;

                $arrData = $this->request->data;

                $isActive = $arrData['activeflag'];
                if($isActive == null) {
                        $isActive = false;
                }

                if(isset($arrData['email'])) {
                        $userExists = $this->User->find('first', array('conditions' => array('User.email_id' => $arrData['email'])));
                        if(isset($userExists['User']['id']) && !empty($arrData['displayname'])) {
                                $this->User->id = $userExists['User']['id'];
                                $this->User->save(
                                        array(
                                                'User' => array(
                                                        'display_name' => $arrData['displayname'],
                                                        'title' => $arrData['title'],
                                                        'location' => $arrData['location'],
                                                        'is_active' => $isActive,
                                                        'daily_sync_mail' => $arrData['dailysyncmails'],
                                                        'weekly_summary_mail' => $arrData['weeklysummarymails'],
                                                        'client_specific_mail' => $arrData['clientpitchmails']
                                                )
                                        )
                                );
                        } else {
                                $result = array();
                                $result['success'] = false;
                                return json_encode($result);
                        }
                        $userId = $userExists['User']['id'];

                        if(!empty($arrData['permission'])) {
                                $this->UserLoginRole->deleteAll(array('user_id' => $userId));
                                $this->UserMarket->deleteAll(array('user_id' => $userId));
                                $this->UserMailNotificationClient->deleteAll(array('user_id' => $userId));
                                $this->UserAdminAccess->deleteAll(array('user_id' => $userId));

                                $loginRole = $this->LoginRole->findByName($arrData['permission']);
                                $loginRoleId = $loginRole['LoginRole']['id'];
                                $this->UserLoginRole->create();
                                $this->UserLoginRole->save(
                                        array(
                                                'UserLoginRole' => array(
                                                    'user_id' => $userId,
                                                    'role_id' => $loginRoleId,
                                                    'active' => $isActive
                                                )
                                        )
                                );

                                if($arrData['permission'] == 'Regional') {
                                        if(!empty($arrData['nameofentity'])) {
                                                $regions = $this->Region->find('all', array('conditions' => array('Region.region in (\'' . str_replace(",", "','", $arrData['nameofentity']) . '\')')));
                                                foreach($regions as $region) {
                                                        $this->UserMarket->create();
                                                        $this->UserMarket->save(
                                                                array(
                                                                        'UserMarket' => array(
                                                                            'user_id' => $userId,
                                                                            'market_id' => $region['Region']['id'],
                                                                            'active' => $isActive
                                                                        )
                                                                )
                                                        );
                                                }
                                        } else {
                                                $result = array();
                                                $result['success'] = false;
                                                return json_encode($result);
                                        }
                                }

                                if($arrData['permission'] == 'Country' || $arrData['permission'] == 'Country - Viewer') {
                                        if(!empty($arrData['nameofentity'])) {
                                                $countries = $this->Market->find('all', array('conditions' => array('Market.market in (\'' . str_replace(",", "','", $arrData['nameofentity']) . '\')')));
                                                foreach($countries as $country) {
                                                        $this->UserMarket->create();
                                                        $this->UserMarket->save(
                                                                array(
                                                                        'UserMarket' => array(
                                                                            'user_id' => $userId,
                                                                            'market_id' => $country['Market']['country_id'],
                                                                            'active' => $isActive
                                                                        )
                                                                )
                                                        );
                                                }
                                        } else {
                                                $result = array();
                                                $result['success'] = false;
                                                return json_encode($result);
                                        }
                                }

                                if($arrData['permission'] == 'Global' && $arrData['clientpitchmails'] === true && $arrData['targetclients'] != '') {
                                        $targetClients = explode(',', $arrData['targetclients']);
                                        foreach($targetClients as $targetClient) {
                                                $this->UserMailNotificationClient->create();
                                                $this->UserMailNotificationClient->save(
                                                        array(
                                                                'UserMailNotificationClient' => array(
                                                                    'user_id' => $userId,
                                                                    'client_name' => $targetClient
                                                                )
                                                        )
                                                );
                                        }
                                }

                                if($arrData['permission'] == 'Global' && isset($arrData['adminlinks'])) {
                                foreach($arrData['adminlinks'] as $adminLink) {
                                        $this->UserAdminAccess->create();
                                        $this->UserAdminAccess->save(
                                                array(
                                                        'UserAdminAccess' => array(
                                                            'user_id' => $userId,
                                                            'admin_link_id' => $adminLink
                                                        )
                                                )
                                        );
                                }
                        }
                        } else {
                                $result = array();
                                $result['success'] = false;
                                return json_encode($result);
                        }
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }
}
