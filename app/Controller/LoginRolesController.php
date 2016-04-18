<?php

class LoginRolesController extends AppController {

	public $layout = 'default_new';
	public $uses = array(
                'User',
                'LoginRole',
                'UserLoginRole'
        );

        public function beforeFilter() {
                parent::beforeFilter();
        }

        public function add_login_role() {
                if ($this->request->is('post')) {
                        $this->LoginRole->create();
                        if ($this->LoginRole->save($this->request->data)) {
                                $this->Session->setFlash(__('The login role has been saved'));
                                return $this->redirect(array('action' => 'list_login_roles'));
                        }
                        $this->Session->setFlash(
                                __('The login role could not be saved. Please, try again.')
                        );
                }
        }

        public function list_login_roles() {
		$loginRoles = $this->LoginRole->find('all', array('order' => 'LoginRole.name ASC'));

		$this->set('loginRoles', $loginRoles);
        }

        public function delete_login_role($id) {
		$loginRole = $this->LoginRole->findById($id);

		if($this->LoginRole->delete($id)) {
                        $this->UserLoginRole->deleteAll(array('UserLoginRole.role_id' => $id));
                        $this->redirect('list_login_roles');
                }
	}

	public function edit_login_role($id) {
		$loginRole = $this->LoginRole->findById($id);

		if ($this->request->isPost()) {
                        $this->LoginRole->id = $id;
			$this->LoginRole->set($this->request->data);

			if ($this->LoginRole->save()) {
                                $this->redirect('list_login_roles');
			}
		} else {
			$this->data = $loginRole;
		}

		$this->set('id', $id);
	}
}
