@extends( 'layouts.admin_master' )

@section( 'title' )
	Editing : {{ $this->user->username }}
@stop

@section( 'styles' )
	<link rel="stylesheet" type="text/css" href="/assets/css/rcswitcher.min.css">
@stop

@section( 'scripts' )
	<script src="/assets/js/rcswitcher.min.js"></script>
	<script>
		$('[type=radio], [type=checkbox]').rcSwitcher();
	</script>
@stop

@section( 'content' )
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<div class="col-sm-8 col-xs-12">
					<h4 class="panel-title">
						<span class="glyphicon glyphicon-edit padding-r10"></span>
						{{ $this->user->username }}
					</h4>
				</div>
				<div class="col-sm-4 col-xs-12 text-right">
					<a href="/admin/user/create" data-toggle="tooltip" title="Create">
						<span class="glyphicon glyphicon-plus"></span>
					</a>
					<a href="/admin/user/{{ $this->user->id }}" data-toggle="tooltip" title="Create" class="margin-l10">
						<span class="glyphicon glyphicon-arrow-right"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="panel-body">
				<form action="/admin/user/{{ $this->user->id }}" method="POST" class="form-horizontal">
					<input type="hidden" name="method" value="PATCH">
					<div class="form-group">
						<label for="username" class="control-label col-sm-3 col-lg-2">User Name</label>
						<div class="col-sm-9 col-lg-10">
							<input type="text" class="form-control" id="username" value="{{ $this->user->username }}" name="username" required minLength="3">
						</div>
					</div>

					<div class="form-group">
						<label for="user-email" class="control-label col-sm-3 col-lg-2">E-Mail</label>
						<div class="col-sm-9 col-lg-10">
							<input type="email" class="form-control" id="user-email" value="{{ $this->user->email }}" name="email" min="0" step="1">
						</div>
					</div>

					<div class="form-group">
						<label for="user-password" class="control-label col-sm-3 col-lg-2">Password</label>
						<div class="col-sm-9 col-lg-10">
							<input type="password" class="form-control" id="user-password" value="" name="password" min="0" step=".25">
						</div>
					</div>

					<div class="form-group">
						<label for="user-admin" class="control-label col-sm-3 col-lg-2">Is Admin</label>
						<div class="col-sm-9 col-lg-10">
							<input type="checkbox" class="form-control" id="user-admin" value="{{ $this->user->admin }}" name="admin" min="0" step="1" {{ $this->user->admin ? 'checked' : '' }} >
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
							<button class="btn btn-success"> Save </button>
						</div>
					</div>
				</form>
		</div>
@stop