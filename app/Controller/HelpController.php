<?php
App::uses('CakeEmail', 'Network/Email');

class HelpController extends AppController {
	public $helpers = array('Html', 'Form');

        public $components = array('RequestHandler');

        public $uses = array(
            'HelpChapter',
            'HelpQuestion',
            'UserAskedQuestion',
            'UserLoginRole'
        );

        public function beforeFilter() {
                
                $this->Auth->allow('login_help');

                $this->Auth->loginAction = array(
                  'controller' => 'users',
                  'action' => 'login'
                );
                $this->Auth->logoutRedirect = array(
                  'controller' => 'users',
                  'action' => 'login'
                );
                $this->Auth->loginRedirect = array(
                  'controller' => 'dashboard',
                  'action' => 'index'
                );
                $this->Auth->authError = array(
                  'controller' => 'users',
                  'action' => 'login'
                );
        }

        public function beforeRender() {
                if($this->Auth->user()) {
                        $this->set('admNavLinks', parent::generateNav($this->arrNav, $this->Auth->user()));
                }
        }

        public function index() {

                $this->set('userRole', $this->Auth->user('role'));
                $this->HelpQuestion->Behaviors->attach('Containable');
                $questions = $this->HelpQuestion->find('all', array('order' => 'HelpChapter.chapter_sequence, HelpQuestion.chapter_id, HelpQuestion.question_sequence'));
                $this->set('questions', $questions);

                $newQuestionsList = $this->UserAskedQuestion->find('all', array('order' => 'UserAskedQuestion.created_date DESC, UserAskedQuestion.user_name, UserAskedQuestion.id'));
                $this->set('newQuestionsList', $newQuestionsList);
        }

        public function save_user_question() {
                if ($this->request->isPost()) {
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                        }

                        $arrData = $this->request->data;
                        $this->UserAskedQuestion->create();
                        $this->UserAskedQuestion->save(
                                array(
                                        'UserAskedQuestion' => array(
                                                'question' => trim($arrData['Question']),
                                                'user_name' => trim($arrData['UserName']),
                                                'created_by' => $this->Auth->user('id'),
                                                'created_date' => date('Y-m-d')
                                        )
                                )
                        );
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }

        public function get_chapters_list() {
                $this->autoRender=false;

                $searchResult = array();
                $chapters = $this->HelpChapter->find('all', array('fields' => array('id', 'chapter_name'), 'order' => 'chapter_sequence, chapter_name ASC'));
                foreach ($chapters as $chapter) {
                        $searchResult[] = array('chapterId' => $chapter['HelpChapter']['id'], 'chapterName' => $chapter['HelpChapter']['chapter_name']);
                }
                return json_encode($searchResult);
        }

        public function save_new_chapter() {
                if ($this->request->isPost()) {
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                        }

                        $arrData = $this->request->data;

                        $chapterCnt = $this->HelpChapter->find('count');
                        $sequence = $chapterCnt + 1;

                        $this->HelpChapter->create();
                        $this->HelpChapter->save(
                                array(
                                        'HelpChapter' => array(
                                                'chapter_name' => trim($arrData['Chapter']),
                                                'description' => trim($arrData['Description']),
                                                'chapter_sequence' => $sequence,
                                                'created_by' => $this->Auth->user('id'),
                                                'created_date' => date('Y-m-d')
                                        )
                                )
                        );
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }

        public function save_new_question() {
                if ($this->request->isPost()) {
                        if($this->RequestHandler->isAjax()){
                                $this->autoRender=false;
                        }

                        $arrData = $this->request->data;

                        $questionCnt = $this->HelpQuestion->find('count', array('conditions' => array('HelpQuestion.chapter_id' => $arrData['ChapterId'])));
                        $sequence = $questionCnt + 1;

                        $this->HelpQuestion->create();
                        $this->HelpQuestion->save(
                                array(
                                        'HelpQuestion' => array(
                                                'chapter_id' => $arrData['ChapterId'],
                                                'question' => trim($arrData['Question']),
                                                'answer' => trim($arrData['Answer']),
                                                'question_sequence' => $sequence,
                                                'created_by' => $this->Auth->user('id'),
                                                'created_date' => date('Y-m-d')
                                        )
                                )
                        );
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }
        
        public function login_help() {
                if($this->RequestHandler->isAjax()){
                        $this->autoRender=false;
                }
                if ($this->request->isPost()) {
                        $arrData = $this->request->data;

                        $this->UserLoginRole->Behaviors->attach('Containable');
                        $globalUsers = $this->UserLoginRole->find('all', array('fields' => array('User.display_name', 'User.email_id'), 'contain' => array('User', 'LoginRole'), 'conditions' => array('LoginRole.name' => 'Global'), 'order' => 'User.display_name'));
                        $emailTo = array();
                        foreach($globalUsers as $globalUser) {
                                $emailTo[] = $globalUser['User']['email_id'];
                        }

                        $email = new CakeEmail('gmail');
                        $email->viewVars(array('title_for_layout' => 'Unable to connect', 'type' => 'Login failed', 'data' => $arrData));
                        $email->template('login_fail', 'default')
                            ->emailFormat('html')
                            ->to(array( 'ama.hughes@iprospect.com'))    //'mathilde.natier@iprospect.com',
                            ->from(array('connectiprospect@gmail.com' => 'Connect iProspect'))
                            ->subject('User unable to connect')
                            ->send();
                }
                $result = array();
                $result['success'] = true;
                return json_encode($result);
        }
}
