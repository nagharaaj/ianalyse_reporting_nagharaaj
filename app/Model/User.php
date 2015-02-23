<?php
App::uses('AppModel', 'Model');

class User extends AppModel {

    public $displayField = 'display_name';
    
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            )
        )
    );
}
