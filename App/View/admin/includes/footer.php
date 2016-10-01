<footer id="footer" class="hidden-print text-center">
	<div class="col-sm-6">
		Developed By Ahmed Saad &copy; 2012-2016<br />
		<small>a7mad.sa3d.2014@gmail.com</small><br />
		<cite title="telephone">Tel: +2  01011772100</cite>
	</div>
	<div class="col-sm-6">
	
		<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-sa/4.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.
	</div>

</footer>

<!-- Foot Scripts -->
<script type="text/javascript">
	
	// Fix sticky Footer
	function setFooterHeight(){
				// sticky Footer
		var footer = document.getElementById( 'footer' );

		var footerHeight = footer.scrollHeight,
			body = document.getElementsByTagName('body')[0];

		body.style.marginBottom = footerHeight + 'px';
	}

	setFooterHeight();

	window.onresize = function(){

		setFooterHeight();
	}


</script>