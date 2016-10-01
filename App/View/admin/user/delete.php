@extends( 'layouts.admin_master' )

@section( 'title' )
	Deleteing : {{ $this->user->username }}
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
					<a href="/admin/users/{{ $this->user->id }}" data-toggle="tooltip" title="Back">
						<span class="glyphicon glyphicon-arrow-right"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="panel-body text-center">
				<h4>Are You Sure To delete {{ $this->user->username }}</h4>
				
		</div>
		<div class="panel-footer">
			<form action="/admin/user/{{ $this->user->id }}" method="POST" class="form-horizontal">
				<input type="hidden" name="method" value="DELETE">
				<div class="row">
					<div class="col-xs-4 col-xs-offset-2 col-sm-3 col-sm-offset-3">
						<button type="submit" class="btn btn-danger btn-block padding-r10">Delete</button>
					</div>
					<div class="col-xs-4 col-sm-3">
						<a href="/admin/user/{{ $this->user->id }}" class="btn btn-primary btn-block padding-l10">back</a>
					</div>
				</div>					
			</form>
		</div>
@stop