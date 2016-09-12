<!-- when pitch are live for more than 3 months -->
<div>
        Hi,
</div>
<br/>
<div>
        This is to inform you that, following pitch entries are live for more than 3 months. Please take appropriate action to close them at the earliest.
</div>
<br/>
<div>
        <table border="1" width="100%" cellspacing="0" cellpadding="0">
                <thead>
                        <th>Pitch Date</th>
                        <th>Pitch Status</th>
                        <th>Client</th>
                        <th>Parent Company</th>
                        <th>Client Category</th>
                        <th>Service</th>
                </thead>
                <tbody>
        <?php foreach($data as $livePitch) { ?>
                        <tr>
                                <td nowrap><?php echo $livePitch['pitch_date']; ?></td>
                                <td nowrap><?php echo $livePitch['pitch_stage']; ?></td>
                                <td nowrap><?php echo $livePitch['client_name']; ?></td>
                                <td nowrap><?php echo $livePitch['parent_company']; ?></td>
                                <td nowrap><?php echo $livePitch['category']; ?></td>
                                <td nowrap><?php echo $livePitch['service']; ?></td>
                        </tr>
        <?php } ?>
                </tbody>
        </table>
</div>
<br/>
<div>
        This is system generated message, please do not reply to this mail.
</div>
