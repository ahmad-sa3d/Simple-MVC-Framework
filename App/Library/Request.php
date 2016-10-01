<?php
/**
 * App Request
 *
 * Resolve And Serve Application Request
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 * @property-read String $request_uri Current Request Full URI
 * @property-read Array $request_segments Request Uri parts 
 * @property-read Array $segments Request Uri parts execluding App prefixing keywords Like 'admin'
 * @property-read string $controller Controller name that will respond to request
 * @property-read string $method Controller method name that will respond to request
 * @property-read Array $parameters parameters that will passed to Controller method
 * @property-read Boolean $is_admin True If Requesting Admin Area
 * @property-read Boolean $is_ajax True If Request Was Made By Ajax
 * @property-read string $request_method Current Request Method get, post, put, patch, delete
 * @property-read string $root_url Application Root Url, If Application inside subdir in Document Root
 * @property-read string $request_result Result That Returned From Controller Method
 * @property-read string $inputs Request Input Array including post, get
 * @property-read string $files App\Library\UploadedFile Instance
 * 
 */

namespace App\Library;

use App\Library\MagicGetterTrait;
use App\Library\Response;
use App\Library\Url;
use App\Library\App;
use App\Library\UploadedFile;
use App\Controllers\IndexController;
use App\Library\Exceptions\FileNotFoundException;
use App\Library\Exceptions\MethodNotFoundException;

class Request
{
	use MagicGetterTrait;

	/**
	 * Request Full URI
	 * @var null
	 */
	protected $request_uri;
	
	/**
	 * Request Uri Parts
	 * @var array
	 */
	protected $request_segments = [];
	
	/**
	 * Request URI Parts Excluding prefixing keywords Like 'admin'
	 * @var array
	 */
	protected $segments = [];

	/**
	 * Controller name
	 * @var string
	 */
	protected $controller;
	
	/**
	 * Controller method
	 * @var string
	 */
	protected $method;
	
	/**
	 * Controller Method Parameters
	 * @var array
	 */
	protected $parameters = [];

	/**
	 * Is Requesting Admin Area
	 * @var boolean
	 */
	protected $is_admin = false;
	
	/**
	 * is Requested By Ajax
	 * @var boolean
	 */
	protected $is_ajax = false;
	
	/**
	 * Request Mathod
	 * @var string
	 */
	protected $request_method = 'get';

	/**
	 * App Root Url
	 * Useful if application hosted inside sub folder of document root
	 * @var string
	 */
	protected $root_url;

	/**
	 * Controller Method Returned Value
	 * @var null
	 */
	protected $request_result;

	/**
	 * Request Input Vars Post, Get vars
	 * @var array
	 */
	protected $inputs = [];

	/**
	 * File Instance
	 * @var \App\library\UploadedFile
	 */
	protected $files;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Register in Service Container
		App::register( 'request', $this );
		
		// Set Sub Path
		$this->root_url =  str_replace( 'index.php', '', $_SERVER['SCRIPT_NAME'] );

		if( isset( $_SERVER[ 'REDIRECT_URL' ] ) )
			$this->request_uri = substr( $_SERVER[ 'REDIRECT_URL' ], strlen( $this->root_url ) );
		else
		{
			// Remove query string if exists
			if( !empty( $_SERVER[ 'QUERY_STRING' ] ) )
				$this->request_uri = substr( str_replace( '?'.$_SERVER[ 'QUERY_STRING' ], '', $_SERVER[ 'REDIRECT_URL' ] ), strlen( $this->root_url ) );
			else
				$this->request_uri = substr( $_SERVER[ 'REDIRECT_URL' ], strlen( $this->root_url ) );
		
		}

		// Instantiate Url Instance
		new Url( $this->root_url );

		// Check Request Method
		if( $_SERVER[ 'REQUEST_METHOD' ] == 'GET' )
			$this->request_method = 'get';
		else
		{
			if( isset( $_POST[ 'method' ] ) )
			{
				$this->request_method = strtolower( $_POST[ 'method' ] );

				if( !in_array( $this->request_method, [ 'post', 'patch', 'put', 'update', 'delete' ] ) )
					return Response::view( 'errors.error', [ 'maessage' => 'Method Not allowed', 'title' => 'Not Allowd', 'code' => 405 ], 405 )->send();

				if( $this->request_method == 'patch' || $this->request_method == 'put' )
					// Patch, Put Request
					$this->request_method = 'update';
			}
			else
				$this->request_method = 'post';

		}

		$this->setInputs();

		// Check Ajax Request
		if( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] == 'XMLHttpRequest' )
			$this->is_ajax = true;

		$this->analyseRequest();

	}

	/**
	 * Analyse Request URI String
	 *
	 * Extract Controller Name, Method, Parameters
	 * @return Null
	 */
	protected function analyseRequest()
	{
		$this->segments = $this->request_segments = $this->request_uri ? explode( '/', rtrim( $this->request_uri, '/' ) ) : [];

		if( !empty( $this->request_uri ) )
		{
			// Check Admin
			if( $this->request_segments[0] == 'admin' )
			{
				$this->is_admin = true;
				unset( $this->segments[0] );
				$this->segments = array_values( $this->segments );
			}

		}

		// MiddleWares
		$gateway = new \App\Library\Gateway( $this );

		if( $gateway->passes )
		{
			$this->setController();
			
			// Load Controller
			$this->load();
		}
		else
		{
			// echo 'Sorry error';
			return Response::view( 'errors.error', [ 'message' => 'Un Authorized Access', 'title' => 'Sorry', 'code'=>'403' ], 403 )->send();
		}

	}

	/**
	 * Extract Controller Name, Method, Parameters
	 */
	protected function setController()
	{
		if( !empty( $this->segments ) )
		{
			$this->controller = ucfirst( $this->checkSegmentName( $this->segments[0] ) ) . 'Controller';
			
			// Check Controller
			if( $this->is_admin )
				$controller_path = CONT . DS . 'Admin' . DS . $this->controller . '.php';
			else
				$controller_path = CONT . DS . $this->controller . '.php';


			if( !file_exists( $controller_path ) )
				return Response::view( 'errors.404', [ 'code' => 404 ], 404 )->send();
			
			

			$count = count( $this->segments );

			if( $this->request_method == 'get' )
			{
				// ID Not Nescssary except for show, 
				if( $count == 1 )
					// Index, No Parameters
					$this->method = 'index';
				else if( $count == 2 )
				{
					// Numeric
					if( is_numeric( $this->segments[1] ) )
					{
						$this->method = 'show';
						$this->parameters = [ $this->resolveResource( $this->segments[1] ), $this ];
						return;
					}
					else
					{
						// Method
						// Check Known
						$this->method = $this->checkSegmentName( $this->segments[1] );

						if( $this->method == 'edit' || $this->method == 'delete' )
							return Response::view( 'errors.error', [ 'message' => 'Bad Request Method, Missing Resource ID', 'title' => 'Bad Request', 'code'=>'405' ], 405 )->send();
							
					}
				}
				else
				{
					// Parameters
					// product/edit/1
					$this->method = $this->checkSegmentName( $this->segments[1] );
					
					if( $this->method == 'edit' || $this->method == 'delete' )
					{
						// Delete And Edit View Require Resource Instance
						if ( $count == 3 )
						{
							$this->parameters = [ $this->resolveResource( $this->segments[2] ) ];
							return;
						}
						else
							return Response::view( 'errors.error', [ 'message' => 'Bad Request Method', 'title' => 'Bad Request', 'code'=>'405' ], 405 )->send();

					}
					else
					{
						if( $this->method == 'create' )
							return Response::view( 'errors.error', [ 'message' => 'Bad Request Method', 'title' => 'Bad Request', 'code'=>'405' ], 405 )->send();
							
						
						$this->parameters = array_slice( $this->segments, 2 );
						return;
					}
				
				}
			}
			else // POST, PUT, PATCH, DELETE Request
			{
				// CUD
				// We Must Have Instance ID
				if( $count == 1 )
				{
					if( $this->request_method == 'post' )
					{
						// Store Request, Creating New Record
						$this->method = 'store';
						$this->parameters = [ $this ];
						return;
					}
					else
						// Only Post Request, Error
						return Response::view( 'errors.error', [ 'title'=>'Bad Request', 'message' => 'No Resources To Work On', 'code'=>405 ], 405 )->send();

				}
				else if( $count == 2 )
				{
					// Patch Update OR Delete
					if( $this->request_method == 'update' )
						// Patch, Update Request
						$this->method = 'update';

					else if( $this->request_method == 'delete' )
						// Patch, Update Request
						$this->method = 'destroy';

					else
					{
						// Custom Request
						$this->method = $this->request_method . $this->checkSegmentName( $this->segments[1] );
						$this->parameters = [ $this ];
						return;
					}

					$this->parameters = [ $this->resolveResource( $this->segments[1] ), $this ];
					return;
				}
				else
				{
					if( $count == 3 && is_numeric( $this->segments[2] ) )
					{
						// Custom CUD Methods
						// product/upload/1
						$this->method = $this->request_method . $this->checkSegmentName( $this->segments[1] );
						$this->parameters = [ $this->resolveResource( $this->segments[2] ), $this ];
						return;
					}

					// Error, CUD Doesnot Need More Than 3 Segments
					return Response::view( 'errors.error', [ 'title'=>'Bad Request', 'message' => 'Bad Request Method', 'code'=>405 ], 405 )->send();
				}

			}
		}
		else
		{
			// FrontEnd Root OR Admin Root
			$this->controller = $this->is_admin ? 'DashboardController' : 'IndexController';
			$this->method = 'index';
		}
	}


	/**
	 * Load Controller
	 * @return Response App Response
	 */
	protected function load()
	{
		
		// Instantiate Controller
		$controller_name =  $this->is_admin ? 'App\Controllers\Admin\\' . $this->controller : 'App\Controllers\\' . $this->controller;
		$controller = new $controller_name();


		// Check Method
		if( !method_exists( $controller, $this->method ) )
			return Response::view( 'errors.404', [ 'code' => 404 ], 404 )->send();

		// Call Method
		$this->request_result = call_user_func_array( [ $controller, $this->method ], $this->parameters );

		// Make Response From Controller Result, and send it to Browser;
		Response::make( $this->request_result )->send();

	}


	/**
	 * Helper Method, To Check And reformate Controller name
	 * @param  String $name Controller Name
	 * @return String       Reformed Controller name
	 */
	protected function checkSegmentName( $name )
	{
		$name = strtolower( $name );
		
		$name = preg_replace_callback( '/\-([a-zA-Z]{1})/i', function( $arr ){
			return ucfirst( $arr[1] );
		},  $name );

		return $name;
	}
	

	/**
	 * Convert id Parameter To Instance Of Model
	 * @param  Numeric $id Model record Id
	 * @return Mixed     Model Or Error Response if Not Found
	 */
	protected function resolveResource( $id )
	{
		if( !is_numeric( $id ) )
			return Response::view( 'errors.error', [ 'title'=>'Bad Request', 'message' => ' Bad Request', 'code' => 405 ], 405 )->send();

		$model_name = str_replace( 'Controller', '', $this->controller );
		$model_class = 'App\Model\\' . $model_name;

		if( !file_exists( MODEL . DS . $model_name .'.php' ) )
			throw new FileNotFoundException( MODEL . DS . $model_name .'.php' );

		if( $model = $model_class::findById( $id ) )
			return $model;
		else
			return Response::view( 'errors.error', [ 'title'=>'Not Found', 'message' => $model_name . ' Not Found', 'code' => 404 ], 404 )->send();
		
	}


	/**
	 * Check If Current Request matches Given Uri
	 * @param  String $uri Uri To Match
	 * @return Boolean      True If matches
	 */
	public function match( $uri )
	{
		$uri = explode( '/', trim( $uri, '/' ) );
		
		if( ($c = count( $uri )) > count( $this->request_segments ) )
			return false;

		$match = true;

		foreach( $uri as $i => $seg )
		{
			if ( $seg != $this->request_segments[$i] )
			{
				$match = false;
				break;
			}
		};

		return $match;

	}



	/**
	 * Set Request Inputs array
	 */
	protected function setInputs()
	{
		foreach( $_GET as $input=>$value )
			$this->inputs[ $input ] = trim( $value );

		foreach( $_POST as $input=>$value )
			$this->inputs[ $input ] = trim( $value );

		// if( !empty( $_FILES ) )
		$this->files = UploadedFile::resolve();
	}


	/**
	 * Get Input value
	 * @param  String $key     Input name
	 * @param  Mixed $default Default value
	 * @return Mixed          Input Value Otherwise Default
	 */
	public function input( $key, $default = null )
	{
		// Get Input value
		if( $this->has( $key ) )
			return $this->inputs[ $key ];
		else
			return $default;
	}

	/**
	 * Get All Request Inputs
	 * @return Array All Request inputs
	 */
	public function all()
	{
		return $this->inputs;
	}

	/**
	 * Check If Request has Specific Input
	 * @param  String  $key Input Name
	 * @return boolean      true if exists
	 */
	public function has( $key )
	{
		return isset( $this->inputs[ $key ] );
	}

}