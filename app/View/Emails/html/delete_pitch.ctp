<!-- when user creates a new pitch -->
<div>
        Hi,
</div>
<br/>
<div>
        This is to inform you that, following pitch entry is deleted.
</div>
<br/>
<div>
        <table border="1" width="100%" cellspacing="0" cellpadding="0">
                <thead>
                        <th>Client</th>
                        <th>Parent Company</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Service</th>
                        <th>Pitch Status</th>
                        <th>Deleted by</th>
                </thead>
                <tbody>
                        <tr>
                                <td nowrap><?php echo $data['ClientRevenueByService']['client_name']; ?></td>
                                <td nowrap><?php echo $data['ClientRevenueByService']['parent_company']; ?></td>
                                <td nowrap><?php echo $data['City']['city']; ?></td>
                                <td nowrap><?php echo $data['Country']['country']; ?></td>
                                <td nowrap><?php echo $data['Service']['service_name']; ?></td>
                                <td nowrap><?php echo $data['ClientRevenueByService']['pitch_stage']; ?></td>
                                <td nowrap><?php echo $data['loggedUser']['display_name']; ?></td>
                        </tr>
                </tbody>
        </table>
</div>
<br/>
<div>
        This is system generated message, please do not reply to this mail.
</div>
