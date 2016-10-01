<nav class="navbar navbar-default">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/">Home</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			
			<ul class="nav navbar-nav navbar-right">
				
				<? if( $this->session->isLoggedIn() ): ?>
					<li>
						<a href="/auth/logout">
							<span class="glyphicon glyphicon-log-out padding-r5"></span>
							Logout
						</a>
					</li>
					<? if( $this->session->user->admin ): ?>
						<li>
							<a href="/admin">
								<span class="glyphicon glyphicon-cog padding-r5"></span>
								Admin
							</a>
						</li>
					<? endif; ?>

				<? else: ?>
					<li>
						<a href="/auth/login">
							<span class="glyphicon glyphicon-log-in padding-r5"></span>
							Login
						</a>
					</li>
				<? endif; ?>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>