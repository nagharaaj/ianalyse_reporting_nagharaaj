<?php
App::uses('CakeEmail', 'Network/Email');

class OverviewNotificationShell extends AppShell {
        public $uses = array(
            'Market',
            'Office',
            'OfficeKeyContact',
            'Region',
            'ClientRevenueByService',
            'User',
            'UserLoginRole',
            'UserMarket',
            'OverviewAnnouncement',
            'OverviewSection',
            'OverviewSectionBrand',
            'OverviewNotification'
        );

        /*
         * main function to generate mails
         */
        public function main() {

                $newNotifications = $this->newNotifications();

                $market = null;
                $arrNotifications = array();
                if(!empty($newNotifications)) {
                        foreach($newNotifications as $notification) {
                                if($market != $notification['OverviewNotification']['market']) {
                                        $market = strtoupper($notification['OverviewNotification']['market']);
                                }
                                $arrNotifications[$market][] = array(
                                    'section' => $notification['OverviewNotification']['section'],
                                    'brand' => $notification['OverviewNotification']['brand'],
                                    'services' => $notification['OverviewNotification']['services']
                                );
                        }
                }
                
                if(!empty($arrNotifications)) {
                        foreach($arrNotifications as $market => $notifications) {
                                $emailTo = array();
                                $arrData = array();
                                $offices = array();
                                $marketOffices = $this->Office->find('all', array('fields' => array('Office.id'), 'conditions' => array('Country.country' => $market)));
                                foreach($marketOffices as $marketOffice) {
                                      $offices[] =  $marketOffice['Office']['id'];
                                }
                                $marketContacts = $this->OfficeKeyContact->find('all', array('fields' => array('contact_name', 'contact_email'), 'conditions' => array('contact_type IN (\'executive\',\'business_head\')', 'office_id IN ('.implode(',', $offices).')')));
                                foreach($marketContacts as $marketContact) {
                                       $emailTo[] =  $marketContact['OfficeKeyContact']['contact_email'];
                                }
                                $emailTo = array_unique($emailTo);
                                $arrData['market'] = $market;
                                $arrData['data'] = $notifications;

                                if(!empty($emailTo)) {
                                        $email = new CakeEmail('test');
                                        $email->viewVars(array('title_for_layout' => 'Client & New Business data', 'type' => 'Client data', 'data' => $arrData));
                                        $email->template('market_mentioned_notification', 'default')
                                            ->emailFormat('html')
                                            ->to($emailTo)
                                            ->from(array('siddharthk@evolvingsols.com' => 'Connect iProspect'))
                                            ->subject('Market ' . $market . ' was mentioned on Global Strategy');

                                        if($email->send()) {
                                                $this->OverviewNotification->updateAll(
                                                        array('OverviewNotification.mail_sent' => 1),
                                                        array('OverviewNotification.market' => $market, 'OverviewNotification.mail_sent' => 0)
                                                );
                                        }
                                }
                        }
                }
        }

        /*
         * function to fetch new notifications in past week
         */
        public function newNotifications() {

                $newNotifications = $this->OverviewNotification->find('all', array('conditions' => array('OverviewNotification.mail_sent' => 0), 'order' => 'OverviewNotification.market'));
                //echo '<pre>'; print_r($newNotifications); echo '</pre>';

                return $newNotifications;
        }
}
