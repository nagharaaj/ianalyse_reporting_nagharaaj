<?php
App::uses('AppModel', 'Model');

class LoginRole extends AppModel {
    
    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A name is required'
            )
        )
    );
}
