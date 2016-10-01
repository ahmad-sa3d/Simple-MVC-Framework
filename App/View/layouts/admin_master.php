<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	{!! $this->url->htmlLink( 'bootstrap.min.css') !!}
	{!! $this->url->htmlLink( 'bootstrap-theme.min.css') !!}
	{!! $this->url->htmlLink( 'main.css') !!}

	@yield( 'styles' )

	{!! $this->url->htmlScript( 'jquery-213.min.js' ) !!}
	{!! $this->url->htmlScript( 'bootstrap.min.js' ) !!}

	<script>
		var baseUrl = '{{ $this->url->to('/') }}';
	</script>
</head>
	<body>
		@include( 'admin.includes.nav' )

		@include( 'admin.includes.notification' )

		<div class="container">
			@yield( 'content' )
		</div>

		@include( 'admin.includes.footer' )

		<!-- Foot Scripts -->
		@yield( 'scripts' )
	</body>
</html>