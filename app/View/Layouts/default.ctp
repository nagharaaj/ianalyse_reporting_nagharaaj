<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		//echo $this->Html->css('cake.generic');
                echo $this->Html->css('style');
                echo $this->Html->css('jqwidgets/styles/jqx.base');

                echo $this->Html->script('jquery-1.11.1.min.js');
                echo $this->Html->script('jqwidgets/jqxcore.js');
                echo $this->Html->script('jqwidgets/jqxmenu.js');
                echo $this->Html->script('jqwidgets/jqxdata.js');
                echo $this->Html->script('jqwidgets/jqxdata.export.js');
                echo $this->Html->script('jqwidgets/jqxdatatable.js');
                echo $this->Html->script('jqwidgets/jqxbuttons.js');
                echo $this->Html->script('jqwidgets/jqxscrollbar.js');
                echo $this->Html->script('jqwidgets/jqxmenu.js');
                echo $this->Html->script('jqwidgets/jqxcombobox.js');
                echo $this->Html->script('jqwidgets/jqxdropdownlist.js');
                echo $this->Html->script('jqwidgets/jqxcheckbox.js');
                echo $this->Html->script('jqwidgets/jqxlistbox.js');
                echo $this->Html->script('jqwidgets/jqxgrid.js');
                echo $this->Html->script('jqwidgets/jqxwindow.js');
                echo $this->Html->script('jqwidgets/jqxinput.js');
                echo $this->Html->script('jqwidgets/jqxnumberinput.js');
                echo $this->Html->script('jqwidgets/jqxcalendar.js');
                echo $this->Html->script('jqwidgets/jqxdatetimeinput.js');
                echo $this->Html->script('jqwidgets/jqxvalidator.js');
                echo $this->Html->script('jqwidgets/jqxpanel.js');
                echo $this->Html->script('jqwidgets/jqxgrid.edit.js');
                echo $this->Html->script('jqwidgets/jqxgrid.selection.js');
                echo $this->Html->script('jqwidgets/jqxgrid.filter.js');
                echo $this->Html->script('jqwidgets/jqxgrid.sort.js');
                echo $this->Html->script('jqwidgets/jqxgrid.pager.js');
                echo $this->Html->script('jqwidgets/jqxgrid.columnsresize.js');
                echo $this->Html->script('jqwidgets/jqxgrid.export.js');
	?>
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
</head>
<body>
	<div id="container">
		<div id="header">
                        <div id="header-copy">
                                <h1>
                                        <?php echo $this->Html->link("Title", "/"); ?>
                                </h1>
                        <?php
                                if($this->Session->check('loggedUser.displayName')) {
                        ?>
                                <div id="lnk-logout">
                                        <span>Welcome, <?php echo $this->Session->read('loggedUser.displayName'); ?>&nbsp;(<a href="/users/logout">Logout</a>)</span>
                                </div>
                        <?php
                                }
                        ?>
                        </div>
		</div>
                <div id="nav-menu" align="center">
                        <?php
                                if(isset($admNavLinks))
                                {
                                        foreach($admNavLinks as $navHead => $arrLinks)
                                        {
                                ?>
                        <div id="<?php echo str_replace("/", "-", $arrLinks); ?>" class="<?php echo ($navHead == "Help") ? "grey" : "green"; ?>">
                                <?php
                                                echo $this->Html->link
                                                (
                                                        $navHead,
                                                        $arrLinks
                                                );
                                ?>
                        </div>
                                <?php
                                        }
                                }
                        ?>
                </div>
<script type="text/javascript">
        $(document).ready(function() {
                $('#nav-menu div#-<?php echo $this->params['controller'].'-'.$this->params['action']; ?>').addClass('selected');
        });
</script>                
                <br/>
                <div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $content_for_layout; ?>

		</div>
		<div id="footer">
			
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>