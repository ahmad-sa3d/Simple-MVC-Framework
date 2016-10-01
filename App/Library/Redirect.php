<?php
/**
 * App Redirect
 *
 * Application Redirect For Redirecting
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 */
namespace App\Library;

class Redirect{

	/**
	 * Application Url Instance
	 * @var Object
	 */
	protected static $url;
	
	/**
	 * Redirect To Specific Location
	 * @param  String $url Path
	 * @return Null
	 */
	public static function to( $url )
	{
		header( 'Location: ' . static::$url->to( $url ) );
		exit;
	}

	/**
	 * set Application Url Instance
	 * @param \App\Library\Url $url Url Instance
	 */
	public static function setUrlInstance( \App\Library\Url $url )
	{
		static::$url = $url;
	}
}