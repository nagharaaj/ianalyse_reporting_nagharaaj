<h2><span>Add Login Role</span></h2>

<form class="edit-form" method="post" action="/login_roles/edit_login_role/<?php echo $id;?>">
	<?php echo $this->Form->input('LoginRole.name'); ?>
	<p>
		<?php echo $this->Form->submit(); ?>
	</p>
<?php echo $this->Form->end(); ?>
