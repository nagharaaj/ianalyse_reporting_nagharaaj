<?php
App::uses('AppModel', 'Model');

class OfficeLanguage extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
        public $belongsTo = array(
		'Language' => array(
			'className' => 'Language',
			'foreignKey' => 'language_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
        );
}
