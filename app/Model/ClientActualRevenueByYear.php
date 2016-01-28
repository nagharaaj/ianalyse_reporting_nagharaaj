<?php
App::uses('AppModel', 'Model');

class ClientActualRevenueByYear extends AppModel {

    public $displayField = 'actual_revenue';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ClientRevenueByService' => array(
			'className' => 'ClientRevenueByService',
			'foreignKey' => 'client_service_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
