
<?php
class UsersController extends AppController
{
        function login()
        {
                ########## Google Settings.. Client ID, Client Secret #############
                $google_client_id = '514535783917-b043bcai6a7cj5ksim7r99qg0q1uhun7.apps.googleusercontent.com';
                $google_client_secret = '22qyRz_pTSmWNHBU99uoo8F6';
                $google_redirect_url = 'http://localhost/ianalyse_reporting/users/login';
                $google_developer_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

                //include google api files
                require_once 'src/Google_Client.php';
                require_once 'src/contrib/Google_Oauth2Service.php';

                $gClient = new Google_Client();
                $gClient->setApplicationName('Login to iProspect');
                $gClient->setClientId($google_client_id);
                $gClient->setClientSecret($google_client_secret);
                $gClient->setRedirectUri($google_redirect_url);
                $gClient->setDeveloperKey($google_developer_key);

                $google_oauthV2 = new Google_Oauth2Service($gClient);

                //If user wish to log out, we just unset Session variable
                if (isset($_REQUEST['reset']))
                {
                        $this->set('msg', 'Logout');
                        //unset($_SESSION['token']);
                        $this->Session->delete('token');
                        $gClient->revokeToken();
                        header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
                }

                //Redirect user to google authentication page for code, if code is empty.
                //Code is required to aquire Access Token from google
                //Once we have access token, assign token to session variable
                //and we can redirect user back to page and login.
                if (isset($_REQUEST['code']))
                {
                        $gClient->authenticate($_REQUEST['code']);
                        $this->Session->write('token', $gClient->getAccessToken());
                        $this->redirect(filter_var($google_redirect_url, FILTER_SANITIZE_URL), null, false);
                        //header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
                        return;
                }

                if ($this->Session->read('token'))
                {
                        $gClient->setAccessToken($this->Session->read('token'));
                }

                if ($gClient->getAccessToken())
                {
                        //Get user details if user is logged in
                        $user = $google_oauthV2->userinfo->get();
                        $user_id = $user['id'];
                        $user_name = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
                        $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
                        $profile_url = filter_var($user['link'], FILTER_VALIDATE_URL);
                        $profile_image_url = filter_var($user['picture'], FILTER_VALIDATE_URL);
                        $personMarkup = "$email<div><img src='$profile_image_url?sz=50'></div>";
                        $this->Session->write('token', $gClient->getAccessToken());
                }
                else
                {
                        //get google login url
                        $authUrl = $gClient->createAuthUrl();
                }

                if(isset($authUrl)) //user is not logged in, show login button
                {
                        $this->set('authUrl', $authUrl);
                }
                else // user logged in
                {
                        /*$result = $this->User->find('count', array('conditions' => array('google_id' => $user_id)));
                        if($result > 0)
                        {
                                $msg = 'Welcome back '.$user_name.'!<br />';
                                $msg .= '<br />';
                                $msg .= '<img src="'.$profile_image_url.'" width="100" align="left" hspace="10" vspace="10" />';
                                $msg .= '<br />';
                                $msg .= '&nbsp;Name: '.$user_name.'<br />';
                                $msg .= '&nbsp;Email: '.$email.'<br />';
                                $msg .= '<br />';
                                $this->set('msg', $msg);
                        }
                        else
                        {
                                $msg1 = 'Hi '.$user_name.', Thanks for Registering!';
                                $msg1 .= '<br />';
                                $msg1 .= '<img src="'.$profile_image_url.'" width="100" align="left" hspace="10" vspace="10" />';
                                $msg1 .= '<br />';
                                $msg1 .= '&nbsp;Name: '.$user_name.'<br />';
                                $msg1 .= '&nbsp;Email: '.$email.'<br />';
                                $msg1 .= '<br />';
                                $this->set('msg', $msg1);
                                $this->User->query("INSERT INTO google_users (google_id, google_name, google_email, google_link, google_picture_link) VALUES ($user_id, '$user_name', '$email', '$profile_url', '$profile_image_url')");
                        }*/
                        $this->User->query("INSERT INTO google_users (google_id, google_name, google_email, google_link, google_picture_link) VALUES ($user_id, '$user_name', '$email', '$profile_url', '$profile_image_url')");
                        $this->redirect("/reports");
                }
        }
}
