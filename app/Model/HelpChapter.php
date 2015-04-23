<?php
App::uses('AppModel', 'Model');

class HelpChapter extends AppModel {

    public $displayField = 'chapter_name';

//The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
    * hasMany associations
    *
    * @var array
    */
    public $hasMany = array(
        'HelpQuestion' => array(
                'className' => 'HelpQuestion',
                'foreignKey' => 'chapter_id',
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
