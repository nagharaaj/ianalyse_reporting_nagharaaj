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
            'UserMarket'
        );

        public function beforeRender() {
                if($this->Auth->user()) {
                        $this->set('admNavLinks', parent::generateNav($this->arrNav, $this->Auth->user()));
                }
        }

        public function index() {

        }

        public function global_growth() {
                $this->set('loggedUser', $this->Auth->user());
                if($this->Auth->user('role') == 'Regional') {
                        $userRegion = $this->UserMarket->find('first', array('conditions' => array('UserMarket.user_id' => $this->Auth->user('id'))));
                        $region = $this->Region->findById($userRegion['UserMarket']['market_id']);
                        $this->set('userRegion', $region['Region']['region']);
                }
        }

        public function local_growth() {

        }
}
