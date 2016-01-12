<?php
App::uses('AppModel', 'Model');

class OverviewSectionBrand extends AppModel {

    public $displayField = 'brand_name';
    
    
    public $belongsTo = array(
        'OverviewSection' => array(
            'className' => 'OverviewSection',
            'foreignKey' => 'section_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
