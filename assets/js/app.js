$('[data-toggle=tooltip]').tooltip();

function setDefaultImage( event )
{
	img = event.target;

	src = img.src;
	src = src.replace( /([\w\d-_]+(\.\w{3,4})?)$/g, 'default.png' );
	img.src = src;
	$(img).off( 'error' );
}

// Fix Broken Images
$('img').on( 'error', setDefaultImage );