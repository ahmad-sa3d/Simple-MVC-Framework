<? if( $this->notification ): ?>
	
	<div class="container">
		<div class="row">
		
			<div class="col-sm-8 col-sm-offset-2 col-lg-6 col-lg-offset-3">
				<div class="alert alert-{{ $this->notification['type'] }} alert-dismissable text-center">
						{!! $this->notification[ 'message' ] !!}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
			</div>
		</div>
	</div>
	
<? endif; ?>