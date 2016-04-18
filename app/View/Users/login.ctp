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
<div align="center">
        <a id="needHelp" href="#" style="text-decoration: none; color: #455560" onmouseover='this.style.color="#6e0000"' onmouseout='this.style.color="#455560"'>Can't connect? Click here for help</a>
</div>

<div id="popupWindow" style="display: none">
    <div>Unable to connect...</div>
    <div style="overflow: hidden;">
    <div id="divSetting">
        <div style="padding-bottom: 7px;">We sorry to see you're having trouble CONNECTING. We need to ask you a couple question to get a better understanding of the situation, and we'll follow up very shortly.</div>
        <form id="testForm" action="./">
            <div>Your name please?</div>
            <div id="userName" class="login-help-questions">
                    <input type="text" id="loggedUserName" name="logged_user_name" placeholder="Enter your name" size="25">
            </div>
            <div>Have you ever logged in before?</div>
            <div id="loggedBefore" class="login-help-questions">
                    <input id="loggedBeforeYes" type="radio" name="has_logged_before" value="Yes"><label for="loggedBeforeYes">Yes</label>
                    <input id="loggedBeforeNo" type="radio" name="has_logged_before" value="No"><label for="loggedBeforeNo">No</label>
            </div>
            <div>Have you recently reset your DAN password?</div>
            <div id="changedPassword" class="login-help-questions">
                    <input id="changedPasswordYes" type="radio" name="has_changed_password" value="Yes"><label for="changedPasswordYes">Yes</label>
                    <input id="changedPasswordNo" type="radio" name="has_changed_password" value="No"><label for="changedPasswordNo">No</label>
            </div>
            <div>Do you get an invalid password message?</div>
            <div id="invalidPassword" class="login-help-questions">
                    <input id="invalidPasswordYes" type="radio" name="is_invalid_password" value="Yes"><label for="invalidPasswordYes">Yes</label>
                    <input id="invalidPasswordNo" type="radio" name="is_invalid_password" value="No"><label for="invalidPasswordNo">No</label>
            </div>
            <div>Which browser are you using?</div>
            <div id="browserInUse" style="height: 50px;" class="login-help-questions">
                    <select id="SelectbrowserInUse" name="browser_in_use" style="margin: 5px 0px;">
                            <option value="">Select</option>
                            <option value="Google Chrome">Google Chrome</option>
                            <option value="Internet Explorer">Internet Explorer</option>
                            <option value="Mozilla Firefox">Mozilla Firefox</option>
                            <option value="Netscape">Netscape</option>
                            <option value="Opera">Opera</option>
                            <option value="Safari">Safari</option>
                            <option value="Other">Other</option>
                    </select>
                    <br/><input type="text" id="otherBrowserName" name="other_browser_name" placeholder="Enter browser name" style="display: none">
            </div>
            <div style="padding-top: 10px;" align="right"><button style="margin-right: 15px;" id="SubmitReport">SUBMIT</button></div>
        </form>
    </div>
    <div id="divLoader" align="center" style="display: none; padding-top: 140px; padding-left: 220px;">
            <div class="jqx-grid-load" style="float: left; overflow: hidden; width: 32px; height: 32px;"></div>
            <span style="margin-top: 10px; float: left; display: block; margin-left: 5px;">Please wait...</span>
    </div>
    </div>
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

        var theme = 'base';
        $("#popupWindow").jqxWindow({
            width: 550, resizable: false, isModal: true, autoOpen: false, maxWidth: 600, maxHeight: 750, showCloseButton: true
        });
        $("#needHelp").click(function () {
                $('#divSetting').show();
                $('#divLoader').hide();
                $('#UserLoginForm').jqxValidator('hide');
                $("#popupWindow").jqxWindow({ position: { x: 'center', y: 'top' }, height: "360px", isModal: true });
                $('#SelectbrowserInUse').val('');
                $('#otherBrowserName').hide();
                // show the popup window.
                $("#popupWindow").jqxWindow('open');

        });
        $("#SubmitReport").jqxButton({ theme: theme });

        $('#SelectbrowserInUse').change(function () {
                if($(this).val() == 'Other') {
                        $('#otherBrowserName').show();
                } else {
                        $('#testForm').jqxValidator('hideHint', '#otherBrowserName');
                        $('#otherBrowserName').hide();
                }
        });

        $('#testForm').jqxValidator({
                position: 'left',
                rules: [
                        { input: '#loggedUserName', message: 'Please enter your name', action: 'keyup, blur', rule: 'required' },
                        { input: '#loggedBeforeYes', message: 'Please select Yes or No', action: 'change', rule: function () {
                                var checked = $("#loggedBeforeYes").is(":checked") || $("#loggedBeforeNo").is(":checked");
                                return checked;
                            }
                        },
                        { input: '#changedPasswordYes', message: 'Please select Yes or No', action: 'change', rule: function () {
                                var checked = $("#changedPasswordYes").is(":checked") || $("#changedPasswordNo").is(":checked");
                                return checked;
                            }
                        },
                        { input: '#invalidPasswordYes', message: 'Please select Yes or No', action: 'change', rule: function () {
                                var checked = $("#invalidPasswordYes").is(":checked") || $("#invalidPasswordNo").is(":checked");
                                return checked;
                            }
                        },
                        { input: '#browserInUse', message: 'Please select browser', action: 'change', rule: function () {
                                if(!$("#browserInUse :selected").val()) {
                                        return false;
                                }
                                return true;
                            }
                        },
                        { input: '#otherBrowserName', message: 'Please enter browser name', action: 'keyup, blur', rule: function (input) {
                                var browserInUse = $("#browserInUse :selected").val();
                                if(browserInUse == 'Other' && !input.val()) {
                                        return false;
                                }
                                return true;
                            }
                        }
                ]
        });

        $('#SubmitReport').click(function (event) {
                event.preventDefault();
                if(!$('#testForm').jqxValidator('validate')) {
                        return false;
                }
                $("#SubmitReport").attr('disabled', true);
                $('#divSetting').hide();
                $('#divLoader').show();
                $.ajax({
                    type: "POST",
                    url: "/help/login_help/",
                    data: JSON.stringify($('#testForm').serializeObject()),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success : function(result) {
                        if(result.success == true) {
                            $("#SubmitReport").attr('disabled', false);
                            $("#popupWindow").jqxWindow('hide');
                        } else {
                            alert(result.errors);
                            return false;
                        }
                    }
                });
        });
});
</script>
