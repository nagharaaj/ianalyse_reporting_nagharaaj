<?php
App::uses('AppModel', 'Model');

class HelpQuestion extends AppModel {

    public $displayField = 'question';

//The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
    * belongsTo associations
    *
    * @var array
    */
    public $belongsTo = array(
        'HelpChapter' => array(
            'className' => 'HelpChapter',
            'foreignKey' => 'chapter_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
