<h2>
<?php
if($status != 'analyse') {
?>
	<span>Client Data Import</span>
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
        if ((isset($unknownServices) && count($unknownServices))) {
                echo $this->Form->end();
        }
?>
<?php
if((!isset($unknownServices) || !count($unknownServices))):
        echo $this->Form->end('Submit');
endif;
?>
</div>
<?php if (isset($unknownServices) && count($unknownServices)) { ?>
<div id="analyseForm">
<?php
        echo $this->Form->create('ClientAnalyse');
        echo $this->Form->hidden('status', array('value' => $status));
        echo $this->Form->hidden('excel_file', array('value' => $excelFile));
        echo $this->Form->hidden('analyse_service', array('value' => 1));
?>
<div class="analysis">
        <span>
                Following services not found in system. Please select the existing service name for each row.
        </span>
</div>
<table class="result-set" border="1">
  <thead>
    <tr>
        <th>Sr No</th>
        <th>Service Name</th>
        <th></th>
    </tr>
  </thead>
  <tbody>
    <?php $cnt = 1; foreach ($unknownServices as $unknownService): ?>
    <tr>
        <td><?php echo $cnt; ?></td>
        <td><?php echo $unknownService; echo $this->Form->hidden('ClientAnalyse.'.$cnt.'.unknown_service', array('value' => $unknownService));?></td>
        <td><?php echo $this->Form->select('ClientAnalyse.ServiceMain.'.$cnt.'.service_id', $services_list, array('empty' => 'Select...')); ?></td>
    </tr>
    <?php $cnt++; endforeach;?>
  </tbody>
</table> 
<?php
        echo $this->Form->hidden('ClientAnalyse.unknown_service_count', array('value' => $cnt));
        echo $this->Form->end('Submit');
?>
</div>
<?php 
        } 
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