<!-- when sync up to NBR is failed -->
<div>
        Hi Global,
</div>
<br/>
<div>
        This is to inform you that, data sync up to NBR system completed successfully. Below find detailed report below,
</div>
<br/>
<div>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tbody>
                        <tr>
                                <td>Date and Time of sync:</td>
                                <td nowrap><?php echo $data['date_n_time']; ?></td>
                        </tr>
                        <tr>
                                <td>Total records synced:</td>
                                <td><?php echo $data['no_of_records_sync']; ?></td>
                        </tr>
                        <tr>
                                <td colspan="2">Number of records synced by country:</td>
                        </tr>
                        <tr>
                <?php if(!empty($data['records_sync_by_country'])) {?>
                                <table border="1" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                                <td>Country</td>
                                                <td># of records</td>
                                        </tr>
                <?php foreach($data['records_sync_by_country'] as $country => $recordsSynced) {?>
                                        <tr>
                                                <td><?php echo $country; ?></td>
                                                <td align="right"><?php echo $recordsSynced; ?></td>
                                        </tr>
                <?php }?>
                                </table>
                <?php }?>
                        </tr>
                </tbody>
        </table>
</div>
<br/>
<div>
        This is system generated message, please do not reply to this mail.
</div>
