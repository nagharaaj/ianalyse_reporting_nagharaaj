<?php
//HTML page start
echo '<h1>Login with Google</h1>';
if(isset($authUrl)) //user is not logged in, show login button
{
        echo '<a class="login" href="'.$authUrl.'"><img src="'.WEB_ROOT.'google-login-button.png" /></a>';
}
else
{
        echo $msg;
        echo '<p><a class="logout" href="?reset=1">Logout</a></p>';
}
?>