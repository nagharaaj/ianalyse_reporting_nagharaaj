<?php
App::uses('CakeEmail', 'Network/Email');

class PitchLiveNotificationShell extends AppShell {
        public $uses = array(
            'City',
            'Country',
            'ClientCategory',
            'ClientRevenueByService',
            'Market',
            'Service',
            'Office',
        );

        /*
         * main function to generate mails to markets about pitches live for more than 3 months
         */
        public function main() {
                $currDate = date('Y-m-d');
                $currMonth = date('n');
                $pastThreeMonth = date('Y-m-d', strtotime('-3 months'));
                if($currMonth <= 3) {
                        $startDate = date('Y-m-d', strtotime('-6 months'));
                } else {
                        $startDate = date('Y') . '-01-01';
                }
                $countries = $this->Country->find('list', array('fields' => array('Country.id', 'Country.country'), 'order' => 'Country.country Asc'));
                $cities = $this->City->find('list', array('fields' => array('City.id', 'City.city'), 'order' => 'City.city Asc'));

                $livePitches = $this->ClientRevenueByService->find('all', array('fields' => array('country_id', 'city_id', 'pitch_date', 'pitch_stage', 'client_name', 'parent_company', 'ClientCategory.category', 'Service.service_name'), 'conditions' => array('ClientRevenueByService.pitch_stage LIKE \'Live%\'', 'ClientRevenueByService.pitch_date between \'' . $startDate . '\' and \'' . $pastThreeMonth . '\''), 'order' => 'country_id, city_id, ClientRevenueByService.client_name'));
                //echo '<pre>'; print_r($livePitches); echo '</pre>';
                $mailData = array();
                $countryData = array();
                $cityData = array();
                $countryId = null;
                $cityId = null;
                foreach($livePitches as $livePitch) {
                        if($countryId != $livePitch['ClientRevenueByService']['country_id']) {
                                if($countryId != null) {
                                        $countryData[$cityId] = $cityData;
                                        $mailData[$countryId] = $countryData;
                                        $countryData = array();
                                        $cityData = array();
                                }
                                $countryId = $livePitch['ClientRevenueByService']['country_id'];
                                $cityId = $livePitch['ClientRevenueByService']['city_id'];
                        }
                        if($cityId != $livePitch['ClientRevenueByService']['city_id']) {
                                $countryData[$cityId] = $cityData;
                                $cityData = array();
                                $cityId = $livePitch['ClientRevenueByService']['city_id'];
                        }
                        $cityData[] = array(
                            'pitch_date' => date('m/Y', strtotime($livePitch['ClientRevenueByService']['pitch_date'])),
                            'pitch_stage' => $livePitch['ClientRevenueByService']['pitch_stage'],
                            'client_name' => $livePitch['ClientRevenueByService']['client_name'],
                            'parent_company' => $livePitch['ClientRevenueByService']['parent_company'],
                            'category' => $livePitch['ClientCategory']['category'],
                            'service' => $livePitch['Service']['service_name']
                        );
                }
                echo '<pre>'; print_r($mailData); echo '</pre>';
                
                foreach($mailData as $countryId => $countryData) {
                        foreach($countryData as $cityId => $cityData) {
                                $this->Office->Behaviors->attach('Containable');
                                $officeContacts = $this->Office->find('first', array('conditions' => array('Office.country_id' => $countryId, 'Office.city_id' => $cityId), 'order' => 'Region.region Asc, Country.country, City.city'));
                                $emailTo = array();
                                if(!empty($officeContacts['OfficeKeyContact'])) {
                                        foreach($officeContacts['OfficeKeyContact'] as $officeContact) {
                                                if(in_array($officeContact['contact_type'], array('executive', 'business_head'))) {
                                                        $emailTo[] = trim($officeContact['contact_email']);
                                                }
                                        }

                                        if(!empty($emailTo)) {
                                                $email = new CakeEmail('gmail');
                                                $email->viewVars(array('title_for_layout' => 'Client & New Business data', 'type' => 'Client data', 'data' => $cityData));
                                                $email->template('live_pitch_notification', 'default')
                                                    ->emailFormat('html')
                                                    ->to($emailTo)
                                                    ->from(array('connectiprospect@gmail.com' => 'Connect iProspect'))
                                                    ->subject('Pitch live for more than 3 months in market ' . $cities[$cityId] . ',' . $countries[$countryId])
                                                    ->send();
                                        }
                                }
                        }
                }
        }
}
