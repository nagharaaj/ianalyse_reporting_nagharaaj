<?php
App::uses('CakeEmail', 'Network/Email');

class HourlyPitchNotificationShell extends AppShell {
        public $uses = array(
            'City',
            'Country',
            'ClientCategory',
            'ClientRevenueByService',
            'Market',
            'Service',
            'User',
            'UserLoginRole',
            'UserMailNotificationClient',
            'UpdatePitchNotification'
        );

        /*
         * main function to generate mails
         */
        public function main() {

                $newPitches = $this->newPitches();
                $updatedPitches = $this->updatedPitches();

                if(!empty($newPitches) || !empty($updatedPitches)) {
                        $arrData = array(
                            'newPitches' => $newPitches,
                            'updatedPitches' => $updatedPitches,
                        );

                        $this->UserLoginRole->Behaviors->attach('Containable');
                        $globalUsers = $this->UserLoginRole->find('all', array('fields' => array('User.display_name', 'User.email_id'), 'contain' => array('User', 'LoginRole'), 'conditions' => array('LoginRole.name' => 'Global', 'User.client_specific_mail' => 1), 'order' => 'User.display_name'));

                        $emailTo = array();
                        foreach($globalUsers as $globalUser) {
                                $isSpecificClientAlert = $this->UserMailNotificationClient->find('count', array('conditions' => array('user_id' => $globalUser['User']['id'])));
                                if($isSpecificClientAlert == 0) {
                                        $emailTo[] = $globalUser['User']['email_id'];
                                }
                        }

                        $email = new CakeEmail('gmail');
                        $email->viewVars(array('title_for_layout' => 'Client & New Business data', 'type' => 'Client data', 'data' => $arrData));
                        $email->template('pitch_notification', 'default')
                            ->emailFormat('html')
                            ->to($emailTo)
                            ->from(array('connectiprospect@gmail.com' => 'Connect iProspect'))
                            ->subject('Hourly new/update pitches log')
                            ->send();
                }
        }

        /*
         * function to fetch new created pitches in past one hour
         */
        public function newPitches() {

                $currTime = date('Y-m-d H:i:s');
                $pastHrTime = date('Y-m-d H:i:s', strtotime('-1 hour'));

                $newPitches = $this->ClientRevenueByService->find('all', array('fields' => array('pitch_date', 'pitch_stage', 'client_name', 'parent_company', 'ClientCategory.category', 'City.city', 'Country.country', 'Service.service_name', 'active_markets'), 'conditions' => ('ClientRevenueByService.created between \'' . $pastHrTime . '\' and \'' . $currTime . '\''), 'order' => 'Country.country, ClientRevenueByService.client_name'));
                //echo '<pre>'; print_r($newPitches); echo '</pre>';

                return $newPitches;
        }

        /*
         * function to fetch updated pitches as won, lost, declined or cancelled in last one hour
         */
        public function updatedPitches() {

                $currTime = date('Y-m-d H:i:s');
                $pastHrTime = date('Y-m-d H:i:s', strtotime('-1 hour'));

                $updatedPitches = $this->UpdatePitchNotification->find('all', array('conditions' => ('UpdatePitchNotification.updated_date between \'' . $pastHrTime . '\' and \'' . $currTime . '\''), 'order' => 'country, client_name'));
                //echo '<pre>'; print_r($updatedPitches); echo '</pre>';

                return $updatedPitches;
        }
}
