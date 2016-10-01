<?php
/**
 * Gateway Configuration
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 */

use App\Library\Redirect;

return [
	'admin' => function(){
		if( !$this->session->isLoggedIn() )
			return Redirect::to( '/auth/login' );

		return ( $this->session->user->admin );
	},

	'auth/login' => function(){
		return 1;
	},

];