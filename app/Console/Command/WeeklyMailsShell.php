<?php
App::uses('CakeEmail', 'Network/Email');

class WeeklyMailsShell extends AppShell {
        public $uses = array(
            'Country',
            'ClientRevenueByService',
            'Market',
            'User',
            'UserAskedQuestion',
            'UserLoginRole'
        );

        public function main() {

                $weeklyStats = $this->weeklyChanges();
                $monthlyStats = $this->monthlyChanges();
                $questions = $this->weeklyUserQuestions();
                
                $arrData = array(
                    'weeklyStats' => $weeklyStats,
                    'monthlyStats' => $monthlyStats,
                    'questions' => $questions
                );

                /*$this->UserLoginRole->Behaviors->attach('Containable');
                $globalUsers = $this->UserLoginRole->find('all', array('fields' => array('User.display_name', 'User.email_id'), 'contain' => array('User', 'LoginRole'), 'conditions' => array('LoginRole.name' => 'Global'), 'order' => 'User.display_name'));

                $emailTo = array();
                foreach($globalUsers as $globalUser) {
                        $emailTo[] = $globalUser['User']['email_id'];
                }*/

                $email = new CakeEmail('gmail');
                $email->viewVars(array('title_for_layout' => 'Weekly change log summary', 'type' => 'Client data', 'data' => $arrData));
                $email->template('weekly_updates', 'default')
                    ->emailFormat('html')
                    ->to(array('helena.snowdon@iprospect.com', 'mathilde.natier@iprospect.com'))
                    ->from(array('connectiprospect@gmail.com' => 'Connect iProspect'))
                    ->subject('Weekly change log summary')
                    ->send();
        }

        public function weeklyChanges() {

                $currDt = date('Y-m-d');
                $lastWeekDt = date('Y-m-d', strtotime('-7 days'));

                $weekNoOfChanges = $this->ClientRevenueByService->find('all', array('fields' => array('COUNT(ClientRevenueByService.id) as no_of_changes', 'Country.country'), 'conditions' => ('ClientRevenueByService.created between \'' . $lastWeekDt . '\' and \'' . $currDt . '\' or ClientRevenueByService.modified between \'' . $lastWeekDt . '\' and \'' . $currDt . '\''), 'group' => array('Country.country'), 'order' => 'no_of_changes DESC'));
                //echo '<pre>'; print_r($weekNoOfChanges);

                $arrCountries = array();
                foreach($weekNoOfChanges as $weekNoOfChange) {
                        $arrCountries[] = $weekNoOfChange['Country']['country'];
                }

                $weekNoChangeCountries = $this->Market->find('list', array('conditions' => array('market NOT IN (\'' . implode('\',\'', $arrCountries) . '\')')));
                //echo '<pre>'; print_r($weekNoChangeCountries);
                
                return array('weekNoOfChanges' => $weekNoOfChanges, 'weekNoChangeCountries' => $weekNoChangeCountries);

        }

        public function monthlyChanges() {
                
                $currDt = date('Y-m-d');
                $lastMonthDt = date('Y-m-d', strtotime('-30 days'));

                $monthNoOfChanges = $this->ClientRevenueByService->find('all', array('fields' => array('COUNT(ClientRevenueByService.id) as no_of_changes', 'Country.country'), 'conditions' => ('ClientRevenueByService.created between \'' . $lastMonthDt . '\' and \'' . $currDt . '\' or ClientRevenueByService.modified between \'' . $lastMonthDt . '\' and \'' . $currDt . '\''), 'group' => array('Country.country'), 'order' => 'no_of_changes DESC'));
                //echo '<pre>'; print_r($monthNoOfChanges);

                $arrCountries = array();
                foreach($monthNoOfChanges as $monthNoOfChange) {
                        $arrCountries[] = $monthNoOfChange['Country']['country'];
                }

                $monthNoChangeCountries = $this->Market->find('list', array('conditions' => array('market NOT IN (\'' . implode('\',\'', $arrCountries) . '\')')));
                //echo '<pre>'; print_r($monthNoChangeCountries);

                return array('monthNoOfChanges' => $monthNoOfChanges, 'monthNoChangeCountries' => $monthNoChangeCountries);
        }

        public function weeklyUserQuestions() {
                
                $currDt = date('Y-m-d');
                $lastWeekDt = date('Y-m-d', strtotime('-7 days'));

                $userAskedQuestions = $this->UserAskedQuestion->find('all', array('conditions' => array('UserAskedQuestion.created_date between \'' . $lastWeekDt . '\' and \'' . $currDt . '\'')));
                //echo '<pre>'; print_r($userAskedQuestions);

                return $userAskedQuestions;
        }
}
