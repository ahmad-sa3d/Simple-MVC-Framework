@extends( 'layouts.admin_master' )

@section( 'title' )
	Create New User
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
						Register New User
					</h4>
				</div>
				<div class="col-sm-4 col-xs-12 text-right">
					<a href="/admin/user/" data-toggle="tooltip" title="Back">
						<span class="glyphicon glyphicon-arrow-right"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="panel-body">
				<form action="/admin/user/" method="POST" class="form-horizontal" autocomplete="off">
					<div class="form-group{{ $this->errors->has('username') ? ' has-error' : '' }}">
						<label for="username" class="control-label col-sm-3 col-lg-2">User Name</label>
						<div class="col-sm-9 col-lg-10">
							<input type="text" class="form-control" id="username" value="{{ $this->old( 'username' ) }}" name="username" required minLength="3" autocomplete="off">
							<? if( $this->errors->has('username') ) : ?>
								<p class="help-block">
									{{ $this->errors->first( 'username' ) }}
								</p>
							<? endif; ?>
						</div>
					</div>

					<div class="form-group{{ $this->errors->has('email') ? ' has-error' : '' }}">
						<label for="user-email" class="control-label col-sm-3 col-lg-2">E-Mail</label>
						<div class="col-sm-9 col-lg-10">
							<input type="email" class="form-control" id="user-email" value="{{ $this->old( 'email' ) }}" name="email" required autocomplete="off">
							<? if( $this->errors->has('email') ) : ?>
								<p class="help-block">
									{{ $this->errors->first( 'email' ) }}
								</p>
							<? endif; ?>
						</div>
					</div>

					<div class="form-group{{ $this->errors->has('password') ? ' has-error' : '' }}">
						<label for="user-password" class="control-label col-sm-3 col-lg-2">Password</label>
						<div class="col-sm-9 col-lg-10">
							<input type="password" class="form-control" id="user-password" value="" name="password" required autocomplete="off">
							<? if( $this->errors->has('password') ) : ?>
								<p class="help-block">
									{{ $this->errors->first( 'password' ) }}
								</p>
							<? endif; ?>
						</div>
					</div>

					<div class="form-group">
						<label for="user-admin" class="control-label col-sm-3 col-lg-2">Is Admin</label>
						<div class="col-sm-9 col-lg-10">
							<input type="checkbox" class="form-control" id="user-admin" value="1" name="admin" min="0" step="1">
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
							<button class="btn btn-success" autocomplete="off"> Save </button>
						</div>
					</div>
				</form>
		</div>
@stop