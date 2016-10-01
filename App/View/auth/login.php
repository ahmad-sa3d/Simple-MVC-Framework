@extends( 'layouts.master' )

@section( 'title' )
	Users Login
@stop

@section( 'content' )
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="panel-title">
						<span class="glyphicon glyphicon-log-in padding-r10"></span>
						Login
					</div>
				</div>
				<div class="panel-body">
					<form method="post" action="/auth/login" class="form-horizontal">
						<div class="form-group">
							<label for="email" class="control-label col-sm-3">E-Mail</label>
							<div class="col-sm-9">
								<input type="email" name="email" class="form-control" id="email" required validate autofocus=true>
							</div>
						</div>

						<div class="form-group">
							<label for="password" class="control-label col-sm-3">Password</label>
							<div class="col-sm-9">
								<input type="password" name="password" class="form-control" id="password" required validate autofocus=true>
							</div>
						</div>

						<div class="form-group">
							
							<div class="col-sm-9 col-sm-offset-3">
								<button class="btn btn-success">Log In</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
				
@stop