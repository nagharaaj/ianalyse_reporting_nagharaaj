<?php
App::import('Vendor', 'Ldap', array('file' => 'Ldap' . DS . 'Ldap.class.php'));

class UsersController extends AppController
{
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
            'UserMarket'
        );

        var $name = 'Users';
 
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
                  'controller' => 'dashboard',
                  'action' => 'index'
                );
                $this->Auth->authError = array(
                  'controller' => 'dashboard',
                  'action' => 'index'
                );
        }
        
        public function beforeRender() {
                if($this->Auth->user()) {
                        $this->set('admNavLinks', parent::generateNav($this->arrNav, $this->Auth->user()));
                }
        }
 
        public function login() {
                $domain     = null;
                
                $this->set('title_for_layout', 'Log In');
                
                if ($this->request->is('post')) {
                        $username = $this->request->data['User']['username'];
						$password = $this->request->data['User']['password'];
                        
                        $ldap = new CLdapLogin('AMDC2DCM05.media.global.loc', '3268', $domain, $username, $password);
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
                foreach ($markets as $market)
                {
                        $arrMarkets[$market['Market']['market']] = $market['Market']['market'];
                }
                $this->set('markets', json_encode($arrMarkets));
                $this->set('regions', json_encode($this->Region->find('list', array('order' => 'Region.region Asc'))));
                $this->set('loginRoles', json_encode($this->LoginRole->find('list', array('order' => 'LoginRole.id Asc'))));
        }
        
        function search_user() {
                
                $this->autoRender=false;
                
                $user_name 	= 'sysamdc2web02ldap@media.global.loc';
                $domain         = null;
                $password 	= 'Neyo48pu39';
                
                $ldap = new CLdapLogin('AMDC2DCM05.media.global.loc', '3268', $domain, $user_name, $password);
                
                if($this->request->data['name_startsWith']) {
                        $name_startsWith = $this->request->data['name_startsWith'];
                }
                if (false == $ldap->login()) {
                        echo '<div class="alert alert-danger">Failed to login with UserName :: <u>' . $ldap->getUserName() . '</u></div>';
                } else {
                        //echo '<div class="alert alert-success">Loged In with UserName :: <u>' . $ldap->getUserName(true) . '</u></div><br/>';
                        //echo '<h1>Group Name::' . (($ldap->getGroupName()) ? $ldap->getGroupName() : '<i>group details not avalible.</i>' ) . '</h1>';

                        $ldap->getAllUserInfo($name_startsWith); // This code is used to display the user name and group associated to that user.
                }
        }
        
        function save_user() {
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
                                        'is_active' => $isActive
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
                        
                        if($arrData['permission'] == 'Regional') {
                                if(isset($arrData['nameofentity'])) {
                                        $region = $this->Region->findByRegion($arrData['nameofentity']);
                                        $regionId = $region['Region']['id'];
                                        $this->UserMarket->create();
                                        $this->UserMarket->save(
                                                array(
                                                        'UserMarket' => array(
                                                            'user_id' => $userId,
                                                            'market_id' => $regionId,
                                                            'active' => $isActive
                                                        )
                                                )
                                        );
                                }
                        }
                        
                        if($arrData['permission'] == 'Country') {
                                if(isset($arrData['nameofentity'])) {
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
                        }
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }
        
        function get_users() {
                $this->autoRender=false;
                
                $usersData = array();
                $i = 0;
                $users = $this->User->find('all', array('order' => 'User.display_name Asc'));
                foreach($users as $user) {
                        $userData[$i]['userid'] = $user['User']['id'];
                        $userData[$i]['displayname'] = $user['User']['display_name'];
                        $userData[$i]['title'] = $user['User']['title'];
                        $userData[$i]['location'] = $user['User']['location'];
                        $userData[$i]['email'] = $user['User']['email_id'];
                        
                        $userLoginRole = $this->UserLoginRole->find('first', array('conditions' => array('UserLoginRole.user_id' => $user['User']['id'])));
                        $userData[$i]['permission'] = $userLoginRole['LoginRole']['name'];
                        
                        if($userLoginRole['LoginRole']['name'] == 'Regional') {
                                $userMarket = $this->UserMarket->find('first', array('conditions' => array('UserMarket.user_id' => $user['User']['id'])));
                                $region = $this->Region->find('first', array('conditions' => array('Region.id' => $userMarket['UserMarket']['market_id'])));
                                $userData[$i]['nameofentity'] = $region['Region']['region'];
                        } else if($userLoginRole['LoginRole']['name'] == 'Country') {
                                $arrCountries = array();
                                $userMarkets = $this->UserMarket->find('all', array('conditions' => array('UserMarket.user_id' => $user['User']['id'])));
                                foreach($userMarkets as $userMarket) {
                                        $country = $this->Market->find('first', array('conditions' => array('Market.country_id' => $userMarket['UserMarket']['market_id'])));
                                        $arrCountries[] = $country['Market']['market'];
                                }
                                $userData[$i]['nameofentity'] = implode(",", $arrCountries);
                        } else if($userLoginRole['LoginRole']['name'] == 'Global') {
                                $userData[$i]['nameofentity'] = 'Global';
                        } else if($userLoginRole['LoginRole']['name'] == 'Viewer') {
                                $userData[$i]['nameofentity'] = '/';
                        }
                        
                        $userData[$i]['active'] = $user['User']['is_active'];
                        
                        $i++;
                }
                echo json_encode($userData);
        }
        
        function update_user() {
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
                                                        'is_active' => $isActive
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
                                                $region = $this->Region->findByRegion($arrData['nameofentity']);
                                                $regionId = $region['Region']['id'];
                                                $this->UserMarket->create();
                                                $this->UserMarket->save(
                                                        array(
                                                                'UserMarket' => array(
                                                                    'user_id' => $userId,
                                                                    'market_id' => $regionId,
                                                                    'active' => $isActive
                                                                )
                                                        )
                                                );
                                        } else {
                                                $result = array();
                                                $result['success'] = false;
                                                return json_encode($result);
                                        }
                                }

                                if($arrData['permission'] == 'Country') {
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
