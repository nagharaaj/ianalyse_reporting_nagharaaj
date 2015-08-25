<!-- when user creates a new pitch -->
<div>
        Hi Global,
</div>
<br/>
<div>
        This is to inform you that, following user is unable to login.
</div>
<br/>
<div>
        <table border="1" width="100%" cellspacing="0" cellpadding="2">
                <tbody>
                        <tr>
                                <td>Name</td>
                                <td nowrap><?php echo $data['logged_user_name']; ?></td>
                        </tr>
                        <tr>
                                <td>Have I ever logged in before?</td>
                                <td nowrap><?php echo $data['has_logged_before']; ?></td>
                        </tr>
                        <tr>
                                <td>Have I recently reset my DAN password?</td>
                                <td nowrap><?php echo $data['has_changed_password']; ?></td>
                        </tr>
                        <tr>
                                <td>Do I get an invalid password message?</td>
                                <td nowrap><?php echo $data['is_invalid_password']; ?></td>
                        </tr>
                        <tr>
                                <td>Which browser am I using?</td>
                                <td nowrap><?php echo (($data['browser_in_use'] == 'Other' && isset($data['other_browser_name'])) ? $data['other_browser_name'] : $data['browser_in_use']); ?></td>
                        </tr>
                </tbody>
        </table>
</div>
<br/>
<div>
        This is system generated message, please do not reply to this mail.
</div>
