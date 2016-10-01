<div class="table-responsive">
	<table class="table table-hover table-stripped table-borderedw">
		<thead>
			<tr>
				<th class="col-xs-1">#</th>
				<th>User Name</th>
				<th>User Email</th>
				<th>is Admin</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<? foreach( $this->users as $i=>$user ): ?>
				<tr>
					<td><? echo $i; ?></td>
					<td><? echo $user->username; ?></td>
					<td><? echo $user->email; ?></td>
					<td><? echo $user->admin ? 1 : 0; ?></td>
					<td class="text-right">
						<a href="/admin/user/<? echo $user->id; ?>" class="no-decoration " data-toggle="tooltip" title="Show">
							<span class="glyphicon glyphicon-user"></span>
						</a>
						<a href="/admin/user/edit/<? echo $user->id; ?>" class="no-decoration margin-0-10" data-toggle="tooltip" title="Edit">
							<span class="glyphicon glyphicon-edit"></span>
						</a>
						<a href="/admin/user/delete/<? echo $user->id; ?>" class="no-decoration " data-toggle="tooltip" title="Delete">
							<span class="glyphicon glyphicon-trash text-danger"></span>
						</a>
					</td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</div>