<?php
/**
 * App UploadedFile
 *
 * This Class Handle Uploading Process, All Uploaded File Will Be Instance Of This Class
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 *  @property-read String $name File name
 *  @property-read String $type File Mime-name
 *  @property-read Numeric $size File Size
 *  @property-read String $tmp_name File Temporary Uploaded Full path
 *  @property-read String $tmp_name File Temporary Uploaded Full path
 *  @property-read Integer $error File Upload Error Code
 *  @property-read Boolean $valid if Valid Uploaded File ( Has No errors )
 */

namespace App\Library;

use App\Library\MagicGetterTraiat;

class UploadedFile
{
	use MagicGetterTrait;


	/**
	 * All Files Instances
	 * @var array
	 */
	protected static $instances = [];
	
	/**
	 * All Files Uploading Errors
	 * @var array
	 */
	protected static $errors = [];
	
	/**
	 * Upload Errors Corresponding Text
	 * @var Array
	 */
	protected static $_error_message = [
								UPLOAD_ERR_OK			=> 'No Errors',
								UPLOAD_ERR_INI_SIZE		=> 'File is larger than php max file size',
								UPLOAD_ERR_FORM_SIZE	=> 'File is larger than form UPLOAD_MAX_SIZE',
								UPLOAD_ERR_PARTIAL		=> "Upload didn't compleated 'uploaded partially'",
								UPLOAD_ERR_NO_FILE		=> 'No files to upload',
								UPLOAD_ERR_NO_TMP_DIR	=> 'No defined temporary directory',
								UPLOAD_ERR_CANT_WRITE	=> "Don't have permission to write on disk",
								UPLOAD_ERR_EXTENSION	=> "Couldn't upload due to file extension problem"
							 ];

	/**
	 * All Uploaded Files Names
	 * @var array
	 */
	protected static $uploaded_files_names = [];

	/**
	 * Uploaded File name
	 * @var String
	 */
	protected $name;
	
	/**
	 * Uploaded File Mime-Type
	 * @var String
	 */
	protected $type;
	
	/**
	 * Uploaded Filesize
	 * @var Numeric
	 */
	protected $size;
	
	/**
	 * Uploaded File Temporary Path
	 * @var String
	 */
	protected $tmp_name;
	
	/**
	 * Uploaded File Errors
	 * @var Integer
	 */
	protected $error;
	
	/**
	 * True If Is Valid uploaded File, Uplaoded Without errors
	 * @var Boolean
	 */
	protected $valid;

	
	/**
	 * Constructor
	 * 
	 * @param Array $file  Uploaded File Info array
	 * @param String  $input File Input name
	 */
	public function __construct( Array $file = null, $input = null )
	{

		if( $input )
		{
			$this->name = $file[ 'name' ];
			$this->type = $file[ 'type' ];
			$this->tmp_name = $file[ 'tmp_name' ];
			$this->size = $file[ 'size' ];
			$this->error = $file[ 'error' ];

			if( !empty( static::$instances[ $input ] ) )
				array_push( static::$instances[ $input ], $this );
			
			else
				static::$instances[ $input ] = [ $this ];

			// Set Validity Status
			$this->valid = $this->isValid();
		}
		
	}

	/**
	 * Resolve $_FILES Super Global And Instantiate Instance For each File
	 * 
	 * @return UploadedFile Instance
	 */
	public static function resolve()
	{
		if( empty( $_FILES ) )
			return new self();

		foreach( $_FILES as $input_name => $file )
		{
			if( !is_array( $file['name'] ) )
			{
				new self( $file, $input_name );
			}
				
			else
			{
				foreach( $file['name'] as $i => $value )
				{
					$f = [];
					$f[ 'name' ] = $file[ 'name' ][ $i ];
					$f[ 'type' ] = $file[ 'type' ][ $i ];
					$f[ 'size' ] = $file[ 'size' ][ $i ];
					$f[ 'tmp_name' ] = $file[ 'tmp_name' ][ $i ];
					$f[ 'error' ] = $file[ 'error' ][ $i ];
					
					new self( $f, $input_name );
				}
			}
		}

		return new self();
	}

	/**
	 * Get Array Of All Uplaoded Files Instances
	 * 
	 * @return Array Array Of All instances
	 */
	public function all()
	{
		$arr = [];

		foreach( static::$instances as $input => $instances )
		{			
			$set = $this->get( $input );

			if( !empty( $set ) )
				$arr = array_merge( $arr, $set );
		}

		return $arr;
	}

	/**
	 * Get Instances For aspecific Input
	 * 
	 * @param  String $input Input name
	 * @return Array        Array Of instances
	 */
	public function get( $input )
	{
		return ( $this->has( $input ) ) ? self::$instances[ $input ] : null;
	}

	
	/**
	 * Check if There Are Files For A Specific Input name
	 * 
	 * @param  String  $input Input name
	 * @return boolean        true If There Are files Otherwise False
	 */
	public function has( $input )
	{			
		return isset( static::$instances[ $input ] );
	}

	
	/**
	 * Check If Instance Is Valid Uploaded file
	 * 
	 * @return boolean  	True If Valid Otherwise False
	 */
	public function isValid()
	{			
		if( $this->error != 0 )
		{
			array_push( static::$errors, $this->name . ' ' . self::$_error_message[ $this->error ]  );
			return false;
		}
		else
			return true;
	}

	
	/**
	 * Save Uploaded File To Specific Path With name
	 * 
	 * @param  string $full_path Save Full Path Including Flename
	 * @return Boolean            True On success False On fail
	 */
	public function saveTo( $full_path )
	{			
		if( $this->valid )
		{
			if( move_uploaded_file( $this->tmp_name, $full_path ) )
			{
				// Success
				array_push( self::$uploaded_files_names, $this->name );

				return true;
			}
			else
			{
				array_push( static::$errors, 'Couldnot Move Uploaded File, Check save Destination Permissions.' );

				return false;
			}
		}

		return false;
	}

	
	/**
	 * Check If There Are Errors For Any Of Upload Files
	 * 
	 * @return boolean True If has errors Otherwise false
	 */
	public function hasErrors()
	{
		return !empty( static::$errors );
	}

	
	/**
	 * Get errors Array
	 * 
	 * @return Array Errors Array
	 */
	public function getErrors()
	{
		return static::$errors;
	}

	/**
	 * Get Array of All Uplaoded Files Names
	 * 
	 * @return Array Uplaoded Files name array
	 */
	public function getUploadedNames()
	{
		return static::$uploaded_files_names;
	}

	
	/**
	 * Set File Save name
	 * 
	 * @param String|Function $name Callback to Set name Or String Represents name
	 *
	 * @return  UploadedFile Instance
	 */
	public function setSaveName( $name )
	{
		if( is_callable( $name ) )
		{
			 $this->name = call_user_func( $name, $this->name );
		}
		else
			$this->name = $name;

		return $this;
	}

}

// الحمد لله
// Ahmed Saad, Updated at Fri 30, Sep 2016