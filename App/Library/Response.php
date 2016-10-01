<?php
/**
 * App Response
 *
 * Serve Application response
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 * @property-read string $content_type response Content type
 * @property-read Integer $status_code response Status code
 * @property-read Array $ststus_text Status Code Corresponding Text
 * @property-read string $header_status response status header string
 * @property-read string $content response Content
 */

namespace App\Library;

use App\Library\Request;
use App\Library\View;
use App\Library\App;


class Response
{
	/**
	 * Server Protocol HTTP/1.1 OR HTTP/1.0
	 * @var string
	 */
	protected static $server_protocol;
	
	/**
	 * Response Content Type
	 * @var string
	 */
	protected $content_type = 'text/html';
	
	/**
	 * Response Status Code
	 * @var integer
	 */
	protected $status_code = 200;

	/**
	 * Status code Corresponding text
	 * @var Array
	 */
	protected $status_text = [
		200 => 'OK',
		301 => 'Permanentaly Redirect',
		301 => 'Permanentaly Redirect',
		403 => 'Un Authorized',
		404 => 'Not Found',
		405 => 'Method Not allowed',
	];

	/**
	 * Status Header String, HTTP/1.1 200 OK
	 * @var String
	 */
	protected $header_status;

	/**
	 * Response Content
	 * @var string
	 */
	protected $content = '';

	/**
	 * Constructor
	 * 
	 * @param Mixed     $content Content To Make Response
	 * @param integer    $code    Status Code
	 * @param Array|null $headers Response Headers
	 */
	public function __construct( $content, $code = 200, Array $headers = null )
	{
		// Register in Service Container
		App::register( 'response', $this );
		
		// Controller May Instantiate Response, then send instance to Request as A Result
		static::$server_protocol = $_SERVER["SERVER_PROTOCOL"];

		$this->content = $content;

		$this->status_code = $code;

		$this->header_status = static::$server_protocol . ' ' . $this->status_code . ' ' . @$this->status_text[ $this->status_code ] ;
		
		$this->setHeader( $this->header_status );

		if( is_array( $headers ) )
		{
			foreach( $headers as $key => $value )
				$this->setHeader( $key, $value );
		}

	}

	/**
	 * Make Response From Raw text
	 * 
	 * @param  String     	$raw_string String To Make response
	 * @param  integer     	$code       Status Code
	 * @param  Array|array 	$headers    Headers
	 * @return Response                 Response Instance
	 */
	public static function raw( $raw_string, $code = 200, Array $headers = [] )
	{
		return new self(
				$raw_string,
				$code,
				array_merge( [ 'Content-Type' => 'text/html' ], $headers )
			);

	}

	/**
	 * Make Json Response From Array
	 * 
	 * @param  Array|Object      $array 	Array Or Object To make Response
	 * @param  integer     		$code    	Status Code
	 * @param  Array|array		$headers    Headers
	 * @return Response                  	Response Instance
	 */
	public static function json( $array, $code = 200, Array $headers = [] )
	{
		self::checkArrayValuesForJson( $array );

		return new self(
				is_array( $array ) ? json_encode( $array ) : $array,
				$code,
				array_merge( [ 'Content-Type' => 'application/json' ], $headers )
			);
	}

	/**
	 * Make Json Response From View File
	 * 
	 * @param  String      		$view 		view file
	 * @param  Array 			$data 		View data
	 * @param  integer     		$code    	Status Code
	 * @param  Array|array		$headers    Headers
	 * @return Response                  	Response Instance
	 */
	public static function view( $view, $data = [], $code = 200, Array $headers = [] )
	{
		// Make View
		$view = View::make( $view, $data );

		return new self(
				$view->content,
				$code,
				array_merge( [ 'Content-Type' => 'text/html' ], $headers )
			);

	}

	/**
	 * Make Response From Data
	 *
	 * THIS METHOD WILL DETECET DATA TYPE AND GUESS SUITABLE RESPONSE
	 * 
	 * @param  Mixed 		    $array 		DATA TO MAKE RESPONSE From
	 * @param  integer     		$code    	Status Code
	 * @param  Array|array		$headers    Headers
	 * @return Response                  	Response Instance
	 */
	public static function make( $data, $code = 200, Array $headers = [] )
	{
		if( $data instanceof self )
			return $data;
		
		if( is_array( $data ) || is_object( $data ) )
			// Json
			return self::json( $data, $code, $headers );
		
		else
			// Plain Content
			return self::raw( $data, $code, $headers );

	}

	/**
	 * Set Header Value
	 * 		
	 * @param String 		$key   Header Key
	 * @param String|Null 	$value Key Value To Set
	 */
	protected function setHeader( $key, $value = null )
	{
		$key = ( !$value ) ? $key : $key . ': ' . $value; 
		
		ob_start();
		@header( $key );
	}

	/**
	 * Helper To Check Array Values 
	 *
	 * This Method Will Jsonify Array Values If was Objects
	 * Note That: Those Objects Shoud Uses trait MagicGetterTrait
	 * 
	 * @param  [type] &$array [description]
	 * @return [type]         [description]
	 */
	protected static function checkArrayValuesForJson( &$array )
	{
		if( is_array( $array ) )
		{
			foreach( $array as &$element )
			{
				if( is_object( $element ) )
					$element = json_decode( (string) $element, true );
				else if( is_array( $element ) )
					self::checkArrayValuesForJson( $element );
			}
		}
	}

	/**
	 * Send Respanse To Client, Then Exit Execution
	 * 
	 * @return Null
	 */
	public function send()
	{
		// ob_end_clean();

		echo $this->content;

		flush();

		exit;
	}
}