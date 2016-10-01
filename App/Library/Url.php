<?php
/**
 * App Url
 *
 * Helps To get Right Url paths To Application
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 * @property-read String $root_uri App Root rel Path
 * @property-read Boolean $must_check If Url Must Check given path ( it will Be true if there are Root_Rel path )
 * 
 */

namespace App\Library;
use App\Library\App;
use App\Library\Redirect;
use App\Library\MagicGetterTrait;

class Url{

	use MagicGetterTrait;
	
	/**
	 * Application Root Relative Path
	 * @var String
	 */
	protected $root_uri;
	
	/**
	 * If There Must Check Paths
	 *
	 * it will Be true if Root_Rel path Not '/'
	 * 
	 * @var Boolean
	 */
	protected $must_check;


	/**
	 * Constructor
	 * 
	 * @param String $root_uri Root relative path
	 */
	public function __construct( $root_uri )
	{
		if( App::has( 'uri' ) )
			throw new RuntimeException( __METHOD__ . ' Canot Instantiate Another Session Instance, There Is Already Instantiated One, Call App::get( "ur" )' );
		
		$this->root_uri = $root_uri;

		$this->must_check = ( $this->root_uri == '/' ) ? false : true;

		App::register( 'url', $this );

		// Set Instance On Url Class
		Redirect::setUrlInstance( $this );

	}

	/**
	 * Get Right Url path for given Uri path
	 * 
	 * @param  string $uri Uri path
	 * @return string      Right Uri Path
	 */
	public function to( $uri='' )
	{
		return ( $this->must_check ) ? $this->root_uri . ltrim( $uri, '/' ) : $uri;
	}

	/**
	 * Prefix Path For assets Directory
	 * 
	 * @param  string $uri Uri path
	 * @return string      Right Uri Path
	 */
	public function assets( $uri='' )
	{
		return $this->root_uri . 'assets/' . ltrim( $uri, '/' );
	}

	
	/**
	 * Prefix Path For Css Directory
	 * 
	 * @param  string $css_file Uri path
	 * @return string      Right Uri Path
	 */
	public function style( $css_file='' )
	{
		return $this->root_uri . 'assets/css/' . ltrim( $css_file, '/' );
	}

	
	/**
	 * Geth Full Html Link element For Given css File
	 * 
	 * @param  string $csss_file Css File
	 * @return string      Html Link element
	 */
	public function htmlLink( $csss_file='' )
	{
		return '<link href="' . $this->style( $csss_file ) . '" type="text/css" rel="stylesheet" />';
	}

	
	/**
	 * Prefix Path For Js Directory
	 * 
	 * @param  string $js_file Uri path
	 * @return string      Right Uri Path
	 */
	public function script( $js_file='' )
	{
		return $this->root_uri . 'assets/js/' . ltrim( $js_file, '/' );
	}

	
	/**
	 * Geth Full Html Script element For Given Js File
	 * 
	 * @param  string $js_file Css File
	 * @return string      Html Script Element
	 */
	public function htmlScript( $js_file='' )
	{
		return '<script src="' . $this->script( $js_file ) . '" type="text/javascript" ></script>';
	}

	
	/**
	 * Check Given Path If Needed To Be Prefixed By rooteRel Or Not
	 * 
	 * @param  string $uri Uri Path
	 * @return string      Right Uri Path
	 */
	public function check( $uri='' )
	{
		if( $uri == '#' || ( strpos( $uri, $this->root_uri ) === 0 ) )
			return $uri;
		else
			return $this->root_uri . ltrim( $uri, '/' );
	}

}