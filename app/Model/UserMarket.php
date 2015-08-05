<?php
App::uses('AppModel', 'Model');

class UserMarket extends AppModel {

        public $belongsTo = array(
            'User' => array(
                'className' => 'User',
                'foreignKey' => 'user_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            )
        );

        public function isMarketAccessAllowed($userId, $marketId) {
                return $this->field('id', array('market_id' => $marketId, 'user_id' => $userId));
        }

}
