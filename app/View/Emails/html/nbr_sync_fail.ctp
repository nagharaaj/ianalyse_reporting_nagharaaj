<!-- when sync up to NBR is failed -->
<div>
        Hi Global,
</div>
<br/>
<div>
        This is to inform you that, data sync up to NBR system failed. Below find detailed report below,
</div>
<br/>
<div>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tbody>
                        <tr>
                                <td>Date:</td>
                                <td nowrap><?php echo $data['date_n_time']; ?></td>
                        </tr>
                        <tr>
                                <td>Next schedule for sync up:</td>
                                <td nowrap><?php echo $data['next_scheduled_time']; ?></td>
                        </tr>
                        <tr>
                                <td>Reason:</td>
                                <td><?php echo $data['reason']; ?></td>
                        </tr>
                </tbody>
        </table>
</div>
<br/>
<div>
        This is system generated message, please do not reply to this mail.
</div>
