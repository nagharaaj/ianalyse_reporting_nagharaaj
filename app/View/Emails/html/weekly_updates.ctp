<!-- Summary of weekly updates on client data and user asked questions -->
<div>
        Hi Global,
</div>
<br/>
<div>
        Please find below the summary of change logs on client data in last week and over last 30 days.
</div>
<br/>
<div>
        <div style="display: block">
                Below is the number for changes by country in last week,<br/>
                <table border="1" width="50%" cellspacing="0" cellpadding="0">
                        <thead>
                                <th>Sr No.</th>
                                <th>Country</th>
                                <th>No of changes (new entries/updates)</th>
                        </thead>
                        <tbody>
                <?php $srNo = 1; foreach($data['weeklyStats']['weekNoOfChanges'] as $weekNoOfChanges) { ?>
                                <tr>
                                        <td width="10%" align="right" nowrap><?php echo $srNo; ?></td>
                                        <td width="50%" nowrap><?php echo $weekNoOfChanges['Country']['country']; ?></td>
                                        <td width="40%" align="right" nowrap><?php echo $weekNoOfChanges[0]['no_of_changes']; ?></td>
                                </tr>
                <?php $srNo++; } ?>
                        </tbody>
                </table>
        </div>
        <br/>
        <div style="display: block">
                Below is the list of countries that have not done any changes in last week,<br/>
                <table border="1" width="50%" cellspacing="0" cellpadding="0">
                        <thead>
                                <th>Sr No.</th>
                                <th>Country</th>
                        </thead>
                        <tbody>
                <?php $srNo = 1; foreach($data['weeklyStats']['weekNoChangeCountries'] as $weekNoChangeCountry) { ?>
                                <tr>
                                        <td width="10%" align="right" nowrap><?php echo $srNo; ?></td>
                                        <td width="90%" nowrap><?php echo $weekNoChangeCountry; ?></td>
                                </tr>
                <?php $srNo++; } ?>
                        </tbody>
                </table>
        </div>
</div>
<br/><br/>
<div>
        <div style="display: block">
                Below is the number for changes by country in last 30 days,<br/>
                <table border="1" width="50%" cellspacing="0" cellpadding="0">
                        <thead>
                                <th>Sr No.</th>
                                <th>Country</th>
                                <th>No of changes (new entries/updates)</th>
                        </thead>
                        <tbody>
                <?php $srNo = 1; foreach($data['monthlyStats']['monthNoOfChanges'] as $monthNoOfChanges) { ?>
                                <tr>
                                        <td width="10%" align="right" nowrap><?php echo $srNo; ?></td>
                                        <td width="50%" nowrap><?php echo $monthNoOfChanges['Country']['country']; ?></td>
                                        <td width="40%" align="right" nowrap><?php echo $monthNoOfChanges[0]['no_of_changes']; ?></td>
                                </tr>
                <?php $srNo++; } ?>
                        </tbody>
                </table>
        </div>
        <br/>
        <div style="display: block">
                Below is the list of countries that have not done any changes in last 30 days,<br/>
                <table border="1" width="50%" cellspacing="0" cellpadding="0">
                        <thead>
                                <th>Sr No.</th>
                                <th>Country</th>
                        </thead>
                        <tbody>
                <?php $srNo = 1; foreach($data['monthlyStats']['monthNoChangeCountries'] as $monthNoChangeCountry) { ?>
                                <tr>
                                        <td width="10%" align="right" nowrap><?php echo $srNo; ?></td>
                                        <td width="90%" nowrap><?php echo $monthNoChangeCountry; ?></td>
                                </tr>
                <?php $srNo++; } ?>
                        </tbody>
                </table>
        </div>
</div>
<?php if(!empty($data['questions'])) { ?>
<br/><br/>
<div>
        <div>
                Below is the list of questions asked by users on Help section in last week,<br/>
                <table border="1" width="100%" cellspacing="0" cellpadding="0">
                        <thead>
                                <th>Sr No.</th>
                                <th>Question</th>
                                <th>Submitted by</th>
                        </thead>
                        <tbody>
                <?php foreach($data['questions'] as $userAskedQuestion) {
                        $srNo = 1;
                ?>
                                <tr>
                                        <td width="10%" align="right" nowrap><?php echo $srNo; ?></td>
                                        <td width="50%" nowrap><?php echo $userAskedQuestion['UserAskedQuestion']['question']; ?></td>
                                        <td width="40%" nowrap><?php echo $userAskedQuestion['UserAskedQuestion']['user_name']; ?></td>
                                </tr>
                <?php $srNo++; } ?>
                        </tbody>
                </table>
        </div>
</div>
<?php } ?>
<br/><br/>
<div>
        This is system generated message, please do not reply to this mail.
</div>
