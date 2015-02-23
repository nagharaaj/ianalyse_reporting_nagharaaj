<h2>
	<span>Log in</span>
</h2>
<?php if (isset($complete) && !($complete)): ?>
<div class="failure">
	<span>Invalid username or password</span>
</div>
<?php endif; ?>
<div id='uploadForm'>
<?php 
echo $this->Form->create('User', array('action' => 'login'));
echo $this->Form->input('username', array('label' => 'Username', 'autocomplete' => 'off'));
echo $this->Form->input('password', array('type' => 'password', 'label' => 'Password')); 
echo $this->Form->end('Login');
?>
</div>
<script type="text/javascript">
$(document).ready(function () {
        $('#UserLoginForm').jqxValidator({ position: 'right', rules: [
                { input: '#UserUsername', message: 'Username is required!', action: 'blur', rule: 'required' },
                { input: '#UserPassword', message: 'Password is required!', action: 'blur', rule: 'required' }
            ]
        });
        
        $("input[type=submit]").click(function (event) {
                if(!$('#UserLoginForm').jqxValidator('validate')) {
                        event.preventDefault();
                        return false;
                }
        });
        $('#UserUsername').focus();
});
</script>
