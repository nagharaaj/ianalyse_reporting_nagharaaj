<?php
App::uses('AppModel', 'Model');

class OfficeServiceContact extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
        public $belongsTo = array(
		'Service' => array(
			'className' => 'Service',
			'foreignKey' => 'service_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
        );
}
