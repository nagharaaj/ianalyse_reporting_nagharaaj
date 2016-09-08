<!-- when user creates a new pitch -->
<div>
        Hi Global,
</div>
<br/>
<div>
        This is to inform you that, following pitch entries has been added/updated in past hour.
</div>
<br/>
<div>
<?php if(!empty($data['newPitches'])) { ?>
        <h2>New added pitches</h2>
        <table border="1" width="100%" cellspacing="0" cellpadding="0">
                <thead>
                        <th>Pitch Date</th>
                        <th>Pitch Status</th>
                        <th>Client</th>
                        <th>Parent Company</th>
                        <th>Client Category</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Service</th>
                        <th>Active Markets</th>
                </thead>
                <tbody>
        <?php foreach($data['newPitches'] as $newPitch) { ?>
                        <tr>
                                <td nowrap><?php echo $newPitch['ClientRevenueByService']['pitch_date']; ?></td>
                                <td nowrap><?php echo $newPitch['ClientRevenueByService']['pitch_stage']; ?></td>
                                <td nowrap><?php echo $newPitch['ClientRevenueByService']['client_name']; ?></td>
                                <td nowrap><?php echo $newPitch['ClientRevenueByService']['parent_company']; ?></td>
                                <td nowrap><?php echo $newPitch['ClientCategory']['category']; ?></td>
                                <td nowrap><?php echo $newPitch['City']['city']; ?></td>
                                <td nowrap><?php echo $newPitch['Country']['country']; ?></td>
                                <td nowrap><?php echo $newPitch['Service']['service_name']; ?></td>
                                <td><?php echo $newPitch['ClientRevenueByService']['active_markets']; ?></td>
                        </tr>
        <?php } ?>
                </tbody>
        </table>
        <br/>
<?php } ?>
<?php if(!empty($data['updatedPitches'])) { ?>
        <h2>Updated pitches</h2>
        <table border="1" width="100%" cellspacing="0" cellpadding="0">
                <thead>
                        <th>Pitch Date</th>
                        <th>Pitch Status</th>
                        <th>Client</th>
                        <th>Parent Company</th>
                        <th>Client Category</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Service</th>
                        <th>Active Markets</th>
                </thead>
                <tbody>
        <?php foreach($data['updatedPitches'] as $updatedPitch) { ?>
                        <tr>
                                <td nowrap><?php echo $updatedPitch['UpdatePitchNotification']['pitch_date']; ?></td>
                                <td nowrap><?php echo $updatedPitch['UpdatePitchNotification']['pitch_status']; ?></td>
                                <td nowrap><?php echo $updatedPitch['UpdatePitchNotification']['client_name']; ?></td>
                                <td nowrap><?php echo $updatedPitch['UpdatePitchNotification']['parent_company']; ?></td>
                                <td nowrap><?php echo $updatedPitch['UpdatePitchNotification']['client_category']; ?></td>
                                <td nowrap><?php echo $updatedPitch['UpdatePitchNotification']['city']; ?></td>
                                <td nowrap><?php echo $updatedPitch['UpdatePitchNotification']['country']; ?></td>
                                <td nowrap><?php echo $updatedPitch['UpdatePitchNotification']['service']; ?></td>
                                <td><?php echo $updatedPitch['UpdatePitchNotification']['active_markets']; ?></td>
                        </tr>
        <?php } ?>
                </tbody>
        </table>
<?php } ?>
</div>
<br/>
<div>
        This is system generated message, please do not reply to this mail.
</div>
