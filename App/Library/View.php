<?php
/**
 * App View
 *
 * Handle Views And Templating
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 *  @property-read String $content View File Parsed Content
 */

namespace App\Library;

use App\Library\App;
use RuntimeException;
use App\Library\Exceptions\FileNotFoundException;
use App\Library\Session;
use App\Library\ErrorsBag;

class View {
	
	use MagicGetterTrait;

	/**
	 * View File Name
	 * @var string
	 */
	protected $view_file;
	
	/**
	 * View File Full Path
	 * @var String
	 */
	protected $view_path;

	/**
	 * View Parsed Content
	 * @var String
	 */
	protected $content = '';
	

	/**
	 * View Sections
	 * @var array
	 */
	protected static $sections = [];
	

	/**
	 * View Shared Properties, Containg All Shared variables
	 * Accessible As Properties Like, errors, notification, url, session and view related variables
	 * 
	 * @var Array
	 */
	protected static $shared = null;

	
	/**
	 * Constructor
	 * 
	 * @param String    $view_file View File
	 * @param Array 	$data      View Variables
	 */
	public function __construct( $view_file, Array $data = [] ){

		if( self::$shared === null )
		{
			// Register
			App::register( 'view', $this );

			// Share Session And Its Notification, Error
			self::$shared[ 'session' ] = App::get( 'session' );

			// Share url instance
			self::$shared[ 'url' ] = App::get( 'url' );

			// Errors
			if( $this->session->has( 'errors' ) )
				self::$shared[ 'errors' ] = $this->session->pull( 'errors' );
			else
				self::$shared[ 'errors' ] = new ErrorsBag();

			// Notification
			if( $this->session->has( 'notification' ) )
				self::$shared[ 'notification' ] = $this->session->pull( 'notification' );
			else
				self::$shared[ 'notification' ] = false;
			
			// Share Data As a Property
			foreach( $data as $key => $value )
			{
				if( !isset( self::$shared[ $key ] ) )
					self::$shared[ $key ] = $value;
				else
					throw new RuntimeException( 'View variable $' . $key . '" Is Preserved By Framework, Please use another variable name rather than this name.' );
			}

		}

		$this->view_file = $view_file;
		
		$this->render();

	}

	
	/**
	 * Instantiate And Return View File
	 * 
	 * @param String    $view_file View File
	 * @param Array 	$data      View Variables
	 * @return View                 Instance
	 */
	public static function make( $view_file, Array $data=[] )
	{
		return new self( $view_file, $data );
	}


	/**
	 * Load View File And Parse Content
	 */
	protected function render()
	{
		$this->view_file = str_replace( '.' , DS, str_replace( '.php', '', $this->view_file ) ) . '.php';

		$this->view_path = VIEW . DS . $this->view_file;

		if( !file_exists( $this->view_path ) )
			throw new FileNotFoundException( $this->view_path );


		$this->pre = ob_get_clean();
		
		ob_start();

		include( $this->view_path );
		
		$this->content = ob_get_clean();

		$this->parse();

		// ob_start();
	}

	
	/**
	 * Parse View Data
	 * Here Will Be The Logic For Templating
	 */
	protected function parse()
	{
		$this->content = preg_replace_callback( '/@section\(\s*([\'\"])([\w\-\d]+)\1\s*\)(.*?)\@stop/si', function( $match )
		{
			static::$sections[ $match[2] ] = $match[3];
			return '';
		},
		$this->content );

		// Check if has Parent View
		$this->content = preg_replace_callback( '/@extends\(\s*([\'\"])([\w\.\-\d]+)\1\s*\)/si', function( $match )
		{
			$parent_view = new self( $match[2] );
			$parent_view->content = preg_replace_callback( '/@yield\(\s*([\'\"])([\w\-\d]*)\1\s*\)/si', function( $match )
			{
				if( isset( static::$sections[ $match[2] ] ) )
					return static::$sections[ $match[2] ];
				else
					return null;
			},
			$parent_view->content );
			return $parent_view->content;
		},
		$this->content );


		// {{  }} Syntax
		$this->content = preg_replace_callback( '/\{\{(.*?)\}\}/', function( $match )
		{
			return htmlspecialchars( eval( 'return ' . $match[1] . ';' ) );
		},
		$this->content );

		// {!!  !!}
		$this->content = preg_replace_callback( '/\{!!(.*?)\!!\}/', function( $match )
		{
			return eval( 'return ' . $match[1] . ';' );
		},
		
		$this->content );

		// @include
		$this->content = preg_replace_callback( '/@include\(\s([\'\"])([\w\.]+?)\1\s\)/si', function( $match )
		{
			$parent_view = new self( $match[2] );
			return $parent_view->content;
		},
		$this->content );

		// @if @else
		//											i/if 	       		2/true only 	3/else true 4/else false
		$this->content = preg_replace_callback( '/@if\((.*?)\) (?: (?!.*?@else)(.*?) | (.*?)@else(.*?))@endif/xsi', function( $match )
		{
			if( eval( 'return ' . $match[1] .';' ) )
				return count($match) > 3 ? $match[3] : $match[2];
			else
				return count($match) > 3 ? $match[4] : '';			
		},
		$this->content );

		// Fix Links, Srcs, Actions
		if( $this->url->must_check )
		{
			$this->content = preg_replace_callback( '/(href|src|action)=(["\'])(.*?)\2/xsi', function( $match )
			{
				return $match[1] . '=' . $match[2] . $this->url->check( $match[3] ) . $match[2];
			},
			$this->content );
		}
			
		
		// Add Previous Application Buffered Content If Exists
		if(  $this->pre )
			$this->content = $this->pre . $this->content;	

	}

	
	/**
	 * Get Old Input Data
	 * 
	 * @param  String $input_name Input name
	 * @param  Mixed $default    Default value
	 * @return Mixed             Input Old value If Exists Otherwise Default Value
	 */
	public function old( $input_name , $default = null )
	{
		return $this->session->has( 'input', $input_name ) ? $this->session->get( 'input' )[ $input_name ] : $default;
	}

	
	/**
	 * Magic Getter
	 * @param  String $property Accessed Property
	 * @return Mixed           Property Value Or Exception
	 */
	public function __get( $property )
	{	
		if( property_exists( $this, $property) )
			return $this->$property;
		
		else if( isset( static::$shared[ $property ] ) )
			return static::$shared[ $property ];
		
		else
			throw new RuntimeException( 'View, Undefined property "' . $property . '"' );
	}

}

// الحمد لله
// Ahmed saad, 1, Oct 2016 12:05 AM