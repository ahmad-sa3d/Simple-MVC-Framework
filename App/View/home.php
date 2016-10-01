@extends( 'layouts.master' )

@section( 'scripts' )

	<script>
		console.log( 'App Working Great!' );
	</script>
		
@stop

@section( 'content' )
	<div class="panel panel-default">
		<div class="panel-body text-center">
			<h3>Welcome To Simple FrameWork</h3>
			<h5>Login As Admin:</h5>
			<div class="key-value">
				<span class="key">Email</span>
				<span class="value">user@gmail.com</span>
			</div>
			<div class="key-value">
				<span class="key">password</span>
				<span class="value">123456</span>
			</div>

			<h5>Login As User:</h5>
			<div class="key-value">
				<span class="key">Email</span>
				<span class="value">user2@test.com</span>
			</div>
			<div class="key-value">
				<span class="key">password</span>
				<span class="value">123456</span>
			</div>
		</div>
	</div>
@stop