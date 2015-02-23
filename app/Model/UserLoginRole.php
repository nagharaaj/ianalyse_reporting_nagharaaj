<?php
App::uses('AppModel', 'Model');

class UserLoginRole extends AppModel {
    
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'LoginRole' => array(
            'className' => 'LoginRole',
            'foreignKey' => 'role_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
