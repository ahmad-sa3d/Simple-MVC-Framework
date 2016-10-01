@extends( 'layouts.admin_master' )

@section( 'title' )
	User | {{ $this->user->username }}
@stop

@section( 'scripts' )
	
@stop

@section( 'content' )
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<div class="col-sm-8 col-xs-12">
					<h4 class="panel-title">
						{{ $this->user->username }}
					</h4>
				</div>
				<div class="col-sm-4 col-xs-12 text-right">
					<a href="/admin/user/create" data-toggle="tooltip" title="Create" class="no-decoration">
						<span class="glyphicon glyphicon-plus"></span>
					</a>
					<a href="/admin/user/edit/{{ $this->user->id }}" data-toggle="tooltip" title="Edit" class="no-decoration margin-0-5">
						<span class="glyphicon glyphicon-edit"></span>
					</a>
					<a href="/admin/user/delete/{{ $this->user->id }}" data-toggle="tooltip" title="Delete" class="no-decoration text-danger">
						<span class="glyphicon glyphicon-trash"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="panel-body">
				<dl>
					<div class="data-row">
						<dt class="data-name">User Name</dt>
						<dt class="data-value">{{ $this->user->username }}</dt>
					</div>

					<div class="data-row">
						<dt class="data-name">User ID</dt>
						<dt class="data-value">{{ $this->user->id }}</dt>
					</div>

					<div class="data-row">
						<dt class="data-name">User Admin</dt>
						<dt class="data-value">{{ $this->user->admin ? 'Yes' : 'No' }}</dt>
					</div>
					
					<div class="data-row">
						<dt class="data-name">Last Update Date</dt>
						<dt class="data-value">{{ $this->user->updated_at }}</dt>
					</div>

					<div class="data-row">
						<dt class="data-name">Created Date</dt>
						<dt class="data-value">{{ $this->user->created_at }}</dt>
					</div>

				</dl>

		</div>
@stop