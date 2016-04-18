<h2>Login Roles List</h2>

<p class="add-item">
	<button onclick="window.location='/login_roles/add_login_role'">Add Login Role</button>
</p>

 <table class="result-set" border="1">
	<thead>
        <tr>
            <th>Role Name</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($loginRoles as $loginRole): ?>
    	<tr>
    		<td><?php echo $loginRole['LoginRole']['name']; ?></td>
    		<td><a href="/login_roles/edit_login_role/<?php echo $loginRole['LoginRole']['id']; ?>" title="Edit"><img class="edit" src="/img/layout/edit.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;
    		<a href="/login_roles/delete_login_role/<?php echo $loginRole['LoginRole']['id']; ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this record?')"><img class="delete" src="/img/layout/delete.png"></a></td>
    	</tr>
    <?php endforeach; ?>   
    </tbody>
</table>
