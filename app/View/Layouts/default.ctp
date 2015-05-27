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
                echo $this->Html->script('google-analytics');
                echo $this->Html->script('jstz.min.js');
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
                echo $this->Html->script('jqwidgets/jqxeditor.js');
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
                                                if($navHead == 'CLIENT & NEW BUSINESS DATA') {
                                                        $className = 'grey';
                                                } else if ($navHead == 'OFFICE DATA') {
                                                        $className = 'orange';
                                                } else if ($navHead == 'PERMISSIONS') {
                                                        $className = 'blue';
                                                } else if ($navHead == 'HELP') {
                                                        $className = 'red';
                                                } else if ($navHead == 'GLOBAL STRATEGY') {
                                                        $className = 'light-grey';
                                                } else if ($navHead == 'GLOBAL GROWTH') {
                                                        $className = 'light-torquoise';
                                                } else if ($navHead == 'LOCAL GROWTH') {
                                                        $className = 'dark-torquoise';
                                                } else {
                                                        $className = 'green';
                                                }
                                ?>
                        <div id="<?php echo str_replace("/", "-", $arrLinks); ?>" class="<?php echo $className; ?>">
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
                
                var $window = $(window),
                $document = $(document),
                button = $('#btn-backstage');

                $window.on('scroll', function () {
                    if (($window.scrollTop() + $window.height()) == $document.height()) {
                        button.stop(true).fadeOut( 'slow' );
                    } else {
                        button.stop(true).fadeIn( 'slow' )
                    }
                });
        });
</script>                
                <br/>
                <div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $content_for_layout; ?>

		</div>
		<div id="footer">
                <?php if ($this->params['controller'].'-'.$this->params['action'] != 'users-login') { ?>
                        <div id="btn-backstage" style="position: fixed;right: 0;bottom: 0; border: 2px solid; text-align: center; padding: 5px 10px; background-color: #ffffff; z-index: 1000">
                                <a href="http://team.aemedia.com/sites/NewBusiness/iProspect/default.aspx" target="blank" style="text-decoration: none; color: #000000">
                                        <div style="display: inline-block;margin-right: 10px;">
                                                <div style="font-weight: bold;">iPROSPECT NEW BUSINESS</div>
                                                <div style="border: 1px solid #444e53; text-align: center;letter-spacing: 3px;line-height: 14px;">BACKSTAGE</div>
                                        </div>
                                        <div style="display: inline-block;width: 200px; color: #CC2C88; font-size: 15px;">Click here for Pitch library, FAQ, Region leads and more content</div>
                                </a>
                        </div>
                <?php } ?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>