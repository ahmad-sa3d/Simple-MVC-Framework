<?php
/**
 * App
 *
 * Application Service Container
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 */

namespace App\Library;

class App {
	
	/**
	 * Array Holds Application Instances
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Register An Application Instance
	 * @param  String $key      Instance Name
	 * @param  Object $instance Instance
	 * @return Null
	 */
	public static function register( $key, $instance )
	{
		if( !isset( self::$instances[ $key ] ) )
		{
			// Register
			self::$instances[ $key ] = $instance;
		}
	}

	/**
	 * Get An Registerd Instance
	 * @param  String $key Instance Name
	 * @return Mixed      Object If Found Otherwise Null
	 */
	public static function get( $key )
	{
		return self::has( $key ) ? self::$instances[ $key ] : null;
	}

	
	/**
	 * Check If An Instance Registered
	 * @param  String  $key Instance name
	 * @return boolean
	 */
	public static function has( $key )
	{
		return isset( self::$instances[ $key ] );
	}
	
}