<!-- when reconciliation between Connect & NBR is failed -->
<div>
        Hi Global,
</div>
<br/>
<div>
        This is to inform you that, data reconciliation between Connect and NBRT systems failed. Below find detailed report below,
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
                                <td>Next schedule for reconciliation:</td>
                                <td nowrap><?php echo $data['date_n_time']; ?></td>
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
