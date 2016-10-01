@extends( 'layouts.admin_master' )

@section( 'title' )
	List Users
@stop

@section( 'content' )
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<div class="col-sm-8 col-xs-12">
					<h4 class="panel-title">
						Shop Users
					</h4>
				</div>
				<div class="col-sm-4 col-xs-12 text-right">
					<a href="/admin/user/create" data-toggle="tooltip" title="Create">
						<span class="glyphicon glyphicon-plus"></span>
					</a>
				</div>
			</div>
		</div>

		<? if( $this->users ): ?>
			@include( 'admin.user.includes.table' )
		<? else: ?>
			<div class="panel-body">
				<h3>No Users</h3>	
			</div>
		<? endif; ?>
		
	</div>
@stop