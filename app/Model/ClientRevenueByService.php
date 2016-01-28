<?php
App::uses('AppModel', 'Model');

class ClientRevenueByService extends AppModel {

    public $displayField = 'client_name';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
                'ClientCategory' => array(
			'className' => 'ClientCategory',
			'foreignKey' => 'category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
                'LeadAgency' => array(
			'className' => 'LeadAgency',
			'foreignKey' => 'Agency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
                'Service' => array(
			'className' => 'Service',
			'foreignKey' => 'service_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
                'Currency' => array(
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
        public $hasMany = array(
                'ClientActualRevenueByYear' => array(
			'className' => 'ClientActualRevenueByYear',
			'foreignKey' => 'client_service_id',
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
