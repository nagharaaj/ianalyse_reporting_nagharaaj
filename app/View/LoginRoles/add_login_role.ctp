<h2><span>Add Login Role</span></h2>

<form class="edit-form" method="post">
	<?php echo $this->Form->input('LoginRole.name'); ?>
	<p>
		<?php echo $this->Form->submit(); ?>
	</p>
<?php echo $this->Form->end(); ?>
