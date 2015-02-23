<h2>
<?php
if($status != 'analyse') {
?>
	<span>Office Data Import</span>
<?php
} else if($status == 'analyse') {
?>
	<span>Analyze data before import</span>
<?php
}
?>
</h2>
<?php if (isset($complete) && $complete): ?>
<div class="success">
	<h3>Import Complete</h3>
</div>
<?php endif; ?>
<?php if (isset($failure) && $failure): ?>
<div class="failure">
	<span>Could not upload the file. Please provide .xls (Microsoft Excel 97-2003) file only and try again...</span>
</div>
<?php endif; ?>
<div id='uploadForm' <?php if($status == 'analyse') {?>style="width:97%"<?php }?>>
<?php 
echo $this->Form->create('MarketImport', array('enctype' => 'multipart/form-data'));
echo $this->Form->hidden('status', array('value' => $status));
if($status != 'analyse') {
        echo $this->Form->input('excel_file', array('type' => 'file', 'label' => 'Select file to upload (.xls file only): ', 'accept' => '.xls')); 
        echo $this->Form->end('Upload');
} else if($status == 'analyse') {
        echo $this->Form->hidden('excel_file', array('value' => $excelFile));
?>
</div>
<table class="result-set" border="1">
  <thead>
    <tr>
        <th rowspan="2">Sr No</th>
        <th colspan="5">General Information</th>
        <th colspan="5">Contact Details</th>
        <th colspan="14">Key Contacts</th>
        <th colspan="<?php echo ((count($services_list)-1)*2); ?>">Service Key Contacts</th>
        <th colspan="3">Other info</th>
    </tr>
    <tr>
        <th>Region</th>
        <th>Country</th>
        <th>City</th>
        <th>Year Established</th>
        <th>Employee Count</th>
        <th>Address</th>
        <th>Telephone</th>
        <th>Email</th>
        <th>Website</th>
        <th>Twitter</th>
        <th>Executive Head</th>
        <th># / FTE Employee</th>
        <th>Finance Head</th>
        <th># / FTE Employee</th>
        <th>Products & Services Head</th>
        <th># / FTE Employee</th>
        <th>Head of Strategy</th>
        <th># / FTE Employee</th>
        <th>Head of Client Services</th>
        <th># / FTE Employee</th>
        <th>New Business</th>
        <th># / FTE Employee</th>
        <th>Marketing</th>
        <th># / FTE Employee</th>
<?php
        foreach($services_list as $service) {
                if($service != 'Other') {
?>
        <th><?php echo $service;?></th>
        <th># / FTE Employee</th>
<?php
                }
        }
?>
        <th>Languages Supported</th>
        <th>Recent Awards</th>
        <th>News</th>
    </tr>
  </thead>
  <tbody>
    <?php $cnt = 1; foreach ($officeDetails as $officeDetail): ?>
    <tr>
        <td><?php echo $cnt; ?></td>
        <td><?php echo $regions[$officeDetail['regionId']]; ?></td>
        <td><?php echo $countries[$officeDetail['countryId']]; ?></td>
        <td><?php echo $cities[$officeDetail['cityId']]; ?></td>
        <td><?php echo $officeDetail['yearEstablished']; ?></td>
        <td><?php echo $officeDetail['employeeCount']; ?></td>
        <td><?php echo $officeDetail['address']; ?></td>
        <td><?php echo implode("<br/>", $officeDetail['telephones']); ?></td>
        <td><?php echo implode("<br/>", $officeDetail['emails']); ?></td>
        <td><?php echo implode("<br/>", $officeDetail['websites']); ?></td>
        <td><?php echo implode("<br/>", $officeDetail['socialAccounts']); ?></td>
        <td><?php echo implode("<br/>", $officeDetail['keyContacts']['executive']); ?></td>
        <td><?php echo $officeDetail['deptEmpCount']['executive']; ?></td>
        <td><?php echo implode("<br/>", $officeDetail['keyContacts']['finance_head']); ?></td>
        <td><?php echo $officeDetail['deptEmpCount']['finance_head']; ?></td>
        <td><?php echo implode("<br/>", $officeDetail['keyContacts']['product_head']); ?></td>
        <td><?php echo $officeDetail['deptEmpCount']['product_head']; ?></td>
        <td><?php echo implode("<br/>", $officeDetail['keyContacts']['strategy_head']); ?></td>
        <td><?php echo $officeDetail['deptEmpCount']['strategy_head']; ?></td>
        <td><?php echo implode("<br/>", $officeDetail['keyContacts']['client_head']); ?></td>
        <td><?php echo $officeDetail['deptEmpCount']['client_head']; ?></td>
        <td><?php echo implode("<br/>", $officeDetail['keyContacts']['business_head']); ?></td>
        <td><?php echo $officeDetail['deptEmpCount']['business_head']; ?></td>
        <td><?php echo implode("<br/>", $officeDetail['keyContacts']['marketing_head']); ?></td>
        <td><?php echo $officeDetail['deptEmpCount']['marketing_head']; ?></td>
<?php
        foreach($services_list as $service) {
                if($service != 'Other') {
?>
        <td><?php echo implode("<br/>", $officeDetail['servicesContacts'][$service]);?></td>
        <td><?php echo $officeDetail['deptEmpCount'][$service]; ?></td>
<?php
                }
        }
?>
        <td><?php echo implode(", ", $officeDetail['supportedLanguages']); ?></td>
        <td><?php echo $officeDetail['awards']; ?></td>
        <td><?php echo $officeDetail['news']; ?></td>
        <!--<td><?php echo $officeDetail['']; ?></td>
        <td><?php echo $officeDetail['']; ?></td>-->
    </tr>
    <?php $cnt++; endforeach;?>
  </tbody>
</table> 
<?php 
        echo $this->Form->end('Submit');
}
?>
<script lang="javascript">
$(document).ready(function () {
        $('form').bind('submit', function(e) {
                if($('#MarketImportImportMarketDataForm')) {
                        $('#MarketImportImportMarketDataForm div').remove('.error-message');
                }
                if($('#MarketImportExcelFile')) {
                        if(!$('#MarketImportExcelFile').val()) {
                                e.preventDefault();
                                $('#MarketImportExcelFile').parent().append('<div class="error-message">This field can not be empty</div>');
                                return false;
                        }
                }
        });
});
</script>