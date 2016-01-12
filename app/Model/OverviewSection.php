<?php
App::uses('AppModel', 'Model');

class OverviewSection extends AppModel {

    public $displayField = 'section_title';

    
    public $hasMany = array(
                'OverviewSectionBrand' => array(
			'className' => 'OverviewSectionBrand',
			'foreignKey' => 'section_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
    );
}
