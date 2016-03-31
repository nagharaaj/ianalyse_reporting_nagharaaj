<?php
App::uses('AppModel', 'Model');

class UserAdminAccess extends AppModel {

    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'AdministrationLink' => array(
            'className' => 'AdministrationLink',
            'foreignKey' => 'admin_link_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
