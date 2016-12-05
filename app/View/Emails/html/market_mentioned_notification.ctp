<!-- when market is mentioned in overview page brands -->
<div>
        Hi,
</div>
<br/>
<div>
        This is to inform you that <?php echo $data['market']; ?> was mentioned on Global Strategy under following Section(s),
</div>
<br/>
<div>
        <table border="1" width="100%" cellspacing="0" cellpadding="0">
                <thead>
                        <th>#</th>
                        <th>Section</th>
                        <th>Brand</th>
                        <th>Services</th>
                </thead>
                <tbody>
        <?php $cnt = 1; foreach($data['data'] as $marketData) { ?>
                        <tr>
                                <td nowrap align="right"><?php echo $cnt; ?></td>
                                <td nowrap><?php echo $marketData['section']; ?></td>
                                <td nowrap><?php echo $marketData['brand']; ?></td>
                                <td nowrap><?php echo $marketData['services']; ?></td>
                        </tr>
        <?php $cnt++; } ?>
                </tbody>
        </table>
</div>
<br/>
<div>
        For more details, visit ‘Global Strategy’ on <a href="http://www.connectiprospect.com" target="_blank">www.connectiprospect.com</a>
</div>
<br/>
<br/>
<br/>
<div>
        This is system generated message, please do not reply to this mail.
</div>
