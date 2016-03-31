<div>
        Ad-hoc data reconciliation between Connect and NBRT systems completed successfully. Please find detailed report below (the same is sent via mail),
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
                                <td>Total records checked:</td>
                                <td><?php echo $data['no_of_records_checked']; ?></td>
                        </tr>
                        <tr>
                                <td colspan="2">Total revenue values by level of reconciliation (all values in USD):</td>
                        </tr>
                        <tr><td colspan="2">
                <?php if(!empty($data['totals'])) {?>
                                <table border="1" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                                <td colspan="2">Level 1 : aggregate of all records</td>
                                        </tr>
                                        <tr>
                                                <td>Connect</td>
                                                <td align="right"><?php echo number_format($data['totals']['connect'], 2); ?></td>
                                        </tr>
                                        <tr>
                                                <td>NBR</td>
                                                <td align="right"><?php echo number_format($data['totals']['nbr'], 2); ?></td>
                                        </tr>
                                </table>
                <?php }?>
                <?php if(!empty($data['country_totals'])) {?>
                                <table border="1" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                                <td colspan="3">Level 2 : aggregate of records by country</td>
                                        </tr>
                                        <tr>
                                                <td><b>Country</b></td>
                                                <td><b>Value in Connect</b></td>
                                                <td><b>Value in NBR</b></td>
                                        </tr>
                <?php foreach($data['country_totals']['connect'] as $country => $revenueValueUSD) {?>
                                        <tr>
                                                <td><?php echo $country; ?></td>
                                                <td align="right"><?php echo number_format($revenueValueUSD, 2); ?></td>
                                                <td align="right"><?php echo number_format($data['country_totals']['nbr'][$country], 2); ?></td>
                                        </tr>
                <?php }?>
                                </table>
                <?php }?>
                <?php if(!empty($data['pitch_totals'])) {?>
                                <table border="1" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                                <td colspan="3">Level 3 : aggregate of records by pitch status</td>
                                        </tr>
                                        <tr>
                                                <td><b>Pitch Status</b></td>
                                                <td><b>Value in Connect</b></td>
                                                <td><b>Value in NBR</b></td>
                                        </tr>
                <?php foreach($data['pitch_totals']['connect'] as $pitchStatus => $revenueValueUSD) {?>
                                        <tr>
                                                <td><?php echo $pitchStatus; ?></td>
                                                <td align="right"><?php echo number_format($revenueValueUSD, 2); ?></td>
                                                <td align="right"><?php echo number_format($data['pitch_totals']['nbr'][$pitchStatus], 2); ?></td>
                                        </tr>
                <?php }?>
                                </table>
                <?php }?>
                        </td></tr>
                <?php if($data['no_of_records_corrected'] > 0 && !empty($data['records_corrected'])) {?>
                        <tr>
                                <td>Number of records corrected:</td>
                                <td><?php echo $data['no_of_records_corrected']; ?></td>
                        </tr>
                        <tr><td colspan="2">Details:
                                <table border="1" width="100%" cellspacing="0" cellpadding="2">
                                        <tr>
                                                <td><b>Country</b></td>
                                                <td><b>Client Name</b></td>
                                                <td><b>Pitch Status</b></td>
                                                <td><b>Services</b></td>
                                                <td><b>Revenue (USD)</b></td>
                                        </tr>
                <?php foreach($data['records_corrected'] as $record) {?>
                                        <tr>
                                                <td><?php echo $record['country']; ?></td>
                                                <td><?php echo $record['client_name']; ?></td>
                                                <td><?php echo $record['pitch_status']; ?></td>
                                                <td><?php echo $record['services']; ?></td>
                                                <td align="right"><?php echo number_format($record['revenue'], 2); ?></td>
                                        </tr>
                <?php }?>
                                </table>
                        </td></tr>
                <?php }?>
                </tbody>
        </table>
</div>

