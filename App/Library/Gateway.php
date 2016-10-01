<?php
/**
 * App Gateway
 *
 * Application Guard, As Middleware To Check Application Urls Access Conditions
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 * @property-read bool $passes If Condition Satisfies Request
 * @property-read Object $request App Request Instance
 * @property-read Object $session App Session Instance
 * @property-read Array $config App Defined Access Configurations
 */

namespace App\Library;

use App\Library\Request;
use App\Library\App;
use App\Library\Redirect;
use App\Library\MagicGetterTrait;


class Gateway{

	use MagicGetterTrait;

	/**
	 * Represents Satisfaction Status
	 * @var boolean
	 */
	protected $passes = true;

	/**
	 * App Request Instance
	 * @var Object
	 */
	protected $request;

	/**
	 * Application Session Instance
	 * @var Object
	 */
	protected $session;

	/**
	 * Application Defined Access Configuration
	 * @var Array
	 */
	protected $config;
	
	
	/**
	 * Constructor
	 * @param Request $request Application Request Instance
	 */
	public function __construct( Request $request )
	{
		$this->request = $request;
		$this->session = App::get( 'session' );
		App::register( 'gateway', $this );

		$this->config = include CONF . DS . 'gateway.php';
		
		return $this->check();
	}

	/**
	 * Check Defined Request Conditions
	 * @return boolean If Passess Or Fails
	 */
	protected function check()
	{
		foreach( $this->config as $uri => $closure )
		{
			if( $this->request->match( $uri ) )
			{
				if( !call_user_func( $closure ) )
				{
					$this->passes = false;
					break;
				}
			}
		}

		return $this->passes;
	}
}