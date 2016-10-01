<?php
/**
 * App Validator
 *
 * Validate Form Input
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 *  @property-read Array $inputs Inputs array
 *  @property-read Array $rules Input rules Array
 *  @property-read Array $required Required Fields Array
 *  @property-read App\Library\ErrorsBag $errors Validation error bag
 */

namespace App\Library;

use Exception;
use App\Library\App;
use App\Library\ErrorsBag;
use App\Library\MagicGetterTrait;

class Validator{

	use MagicGetterTrait;
	
	/**
	 * Inputs To validate
	 * @var Array
	 */
	protected $inputs;
	

	/**
	 * Input Rules To Perform Validation
	 * @var Array
	 */
	protected $rules;
	

	/**
	 * Required Fields Array
	 * @var Array
	 */
	protected $required;
	

	/**
	 * Validation ErrorsBag
	 * @var App\Library\ErrorsBag
	 */
	protected $errors;

	/**
	 * Validation Custom Messages
	 * @var Array
	 */
	protected $messages;

	
	/**
	 * Constructor
	 * 
	 * @param Array $inputs 	 		Inputs Array
	 * @param Array $rules  			Inputs rules To Validate According To
	 * @param Array $custom_messages   	Custom Error Messages
	 */
	public function __construct( Array $inputs, Array $rules, Array $custom_messages = null )
	{
		if( !is_array( $inputs ) )
			throw new Exception( __METHOD__ . 'First Argument represents inputs must be Array.' );

		if( !is_array( $rules ) )
			throw new Exception( __METHOD__ . 'Second Argument represents rules must be Array.' );

		// Set inputs
		$this->inputs = $inputs;

		// $this->errors = [];
		$this->errors = new ErrorsBag();
		$this->required = [];

		// Set Up Custom Messages
		$this->messages = [];

		if( $custom_messages )
		{
			foreach( $custom_messages as $input_rule => $message )
			{
				// Get Input, Rule
				$parts = explode( '.', $input_rule );
				$input = $parts[0];
				$rule = isset( $parts[1] ) ? $parts[1] : '*';

				if( isset( $this->messages[ $input ] ) )
					$this->messages[ $input ][ $rule ] = $message;
				else
					$this->messages[ $input ] = [ $rule => $message ];
			}
		}


		// Set Rules
		foreach( $rules as $input => $rule )
		{
			if( is_array( $rule ) )
				$this->rules[ $input ] = $rule;
			else
			{
				$rule = explode(  '|', trim( $rule, '|' ) );
				$this->rules[ $input ] = $rule;
			}

			// Get Required
			if( in_array( 'required', $this->rules[ $input ] ) )
				array_push( $this->required, $input );

			// Check Options
			foreach( $this->rules[ $input ] as &$r )
			{
				$parts = explode( ':', $r );
				
				if( count( $parts ) > 1 )
				{
					// Has Options
					$r = [ $parts[0] => explode( ',', $parts[1] ) ];

					// Validate
					$options = $r[ $parts[0] ];
					$this->{$parts[0]}( $input, $options );
				}
				else
					// validate
					$this->$r( $input );

			}
		}
		
		// echo '<pre />';
		// print_r( $this );
	}

	/**
	 * Instantiate Validator Instance
	 * 
	 * @param Array $inputs  Inputs Array
	 * @param Array $rules   Inputs rules To Validate According To
	 * @param Array $custom_messages   	Custom Error Messages
	 * 
	 * @return Validator         Instance
	 */
	public static function make( Array $inputs, Array $rules, Array $custom_messages = null )
	{
		return new self( $inputs, $rules, $custom_messages );
	}

	
	/**
	 * Check If Input Exists In Inputs array
	 * 
	 * @param  string  $input Input name
	 * @return boolean        True If Exists Otherwise False
	 */
	public function hasInput( $input )
	{
		return isset( $this->inputs[ $input ] );
	}

	
	/**
	 * CHECK REQUIRED RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function required( $input )
	{
		if( !$this->hasInput( $input ) )
			$this->errors->add( $input, [ 'required' , $this->getMessage( $input, 'required', 'Required Filed.' ) ] );
	}

	
	/**
	 * CHECK FILLED RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function filled( $input )
	{
		if( $this->mustCheck( $input ) )
		{
			if( empty( $this->inputs[ $input ] ) )
				$this->errors->add( $input, [ 'filled' , $this->getMessage( $input, 'filled', 'Required And Not Empty Field.' ) ] );
		}
	}

	
	/**
	 * CHECK Numeric RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function numeric( $input )
	{
		if( $this->mustCheck( $input ) )
		{
			if(  !$this->hasInput( $input ) || !is_numeric( $this->inputs[ $input ] ) )
				$this->errors->add( $input, [ 'numeric' , $this->getMessage( $input, 'numeric', 'Must Be Numeric Value.' ) ] );
		}
	}

	
	/**
	 * CHECK INTEGER RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function integer( $input )
	{
		if( $this->mustCheck( $input ) )
		{
			if(  !$this->hasInput( $input ) || filter_var( $this->inputs[ $input ], FILTER_VALIDATE_INT ) === false )
				$this->errors->add( $input, [ 'integer' , $this->getMessage( $input, 'integer', 'Must Be Integer Value.' ) ] );
		}
	}

	
	/**
	 * CHECK STRING RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function string( $input )
	{
		if( $this->mustCheck( $input ) )
		{
			if(  !$this->hasInput( $input ) || is_numeric( $this->inputs[ $input ] ) )
				$this->errors->add( $input, [ 'string' , $this->getMessage( $input, 'string', 'Must Be String Value.' ) ] );
		}
	}

	
	/**
	 * CHECK MIN RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function min( $input, $options )
	{
		if( empty( $options ) )
			throw new Exception( __METHOD__ . ' Rule Must Have Min Value' );

		if( $this->mustCheck( $input ) )
		{
			if( !$this->hasInput( $input ) )
				// $this->errors->add( $input, [ 'min' , 'Must Be Greater Than '. $options[0] ] );
				$this->errors->add( $input, [ 'min', $this->getMessage( $input, 'min' , 'Must Be Greater Than '. $options[0] ) ]  );
			else
			{
				if( is_numeric( $this->inputs[ $input ] ) )
				{
					if( $this->inputs[ $input ] < $options[0] )
						$this->errors->add( $input, [ 'min', $this->getMessage( $input, 'min' , 'Must Be Greater Than '. $options[0] ) ]  );
						// $this->errors->add( $input, [ 'min' , 'Must Be Greater Than '. $options[0] ] );
				}
				else if( strlen( $this->inputs[ $input ] ) < $options[0] )
					$this->errors->add( $input, [ 'min', $this->getMessage( $input, 'min' , 'Must Be Greater Than '. $options[0] ) ] );
					// $this->errors->add( $input, [ 'min' , 'Length Must Be Greater Than '. $options[0] ] );
				
			}
		}

	}

	
	/**
	 * CHECK MAX RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function max( $input, $options )
	{
		if( empty( $options ) )
			throw new Exception( __METHOD__ . ' Rule Must Have Max Value' );

		if( $this->mustCheck( $input ) )
		{
			if( !$this->hasInput( $input ) )
				$this->errors->add( $input, [ 'max' , $this->getMessage( $input, 'max', 'Must Be Less Than '. $options[0] ) ] );
			else
			{
				if( is_numeric( $this->inputs[ $input ] ) )
				{
					if( $this->inputs[ $input ] > $options[0] )
						$this->errors->add( $input, [ 'max' , $this->getMessage( $input, 'max', 'Must Be Less Than '. $options[0] ) ] );
				}
				else if( strlen( $this->inputs[ $input ] ) > $options[0] )
					$this->errors->add( $input, [ 'max' , $this->getMessage( $input, 'max', 'Length Must Be Less Than '. $options[0] ) ] );
			}
		}

	}

	
	/**
	 * CHECK BETWEEN RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function between( $input, Array $options )
	{
		if( count( $options ) < 2 )
			throw new Exception( __METHOD__ . ' Rule Must Have Min and Max' );
		
		if( $options[1] > $options[0] )
		{
			$this->min( $input, [ $options[0] ] );
			$this->max( $input, [ $options[1] ] );
		}
		else
		{
			$this->max( $input, [ $options[0] ] );
			$this->min( $input, [ $options[1] ] );
		}
				
	}

	
	/**
	 * CHECK EMAIL RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function email( $input )
	{
		if( $this->mustCheck( $input ) )
		{
			if( !$this->hasInput( $input ) || filter_var( $this->inputs[ $input ], FILTER_VALIDATE_EMAIL ) === false )
				$this->errors->add( $input, [ 'email' , $this->getMessage( $input, 'email', 'Not Valid Email Address' ) ] );
		}
	}

	
	/**
	 * CHECK BOOLEAN RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function boolean( $input )
	{
		if( $this->mustCheck( $input ) )
		{
			if( !$this->hasInput( $input ) || filter_var( $this->inputs[ $input ], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) === null )
				$this->errors->add( $input, [ 'boolean' , $this->getMessage( $input, 'boolean', 'Must Be Boolean Value '. $options[0] ) ] );
		}
	}

	/**
	 * CHECK Array RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function array( $input )
	{
		if( $this->mustCheck( $input ) )
		{
			if( !$this->hasInput( $input ) || !is_array( $this->inputs[ $input ] ) )
				$this->errors->add( $input, [ 'array' , $this->getMessage( $input, 'array', 'Must Be Of Type Array' ) ] );
		}
	}

	
	/**
	 * CHECK UNIQUE RULE
	 * 
	 * @param  string  $input Input name
	 */
	public function unique( $input, $options )
	{
		if( $this->mustCheck( $input ) )
		{
			$count = count( $options );
			if( $count < 2 )
				throw new Exception( __METHOD__ . ' Requires Table name And Column, And Optionally ID To Ignore From Uniqness' );

			$pdo = App::get('DBH' );

			$sql = 'SELECT * FROM `' . $options[0] . '` WHERE (`' . $options[1] . '` = :val)';
			
			if( $count > 2 )
			{
				$ids = join( ',', array_slice( $options, 2 ) );
				// $ids = join( ' OR `id` <> ', array_slice( $options, 2 ) ) . ')';

				$sql .= ' AND `id` NOT IN (' . $ids . ')';
				// $sql .= ' AND ( `id` <>' . $ids;
				// $sql .= ' WHERE `id` NOT IN ( SELECT `id` FROM `' . $options[0] . '` WHERE `id` IN (' . $ids . '))';
			}

			// echo $sql;
			// echo '<hr>';

			$stm = $pdo->prepare( $sql );
			$stm->execute( [ 'val' => $this->inputs[ $input ] ] );

			if( $r = $stm->fetch() )
			{
				// echo '<pre />';
				// print_r( $r );
				// Exists
				$this->errors->add( $input, [ 'unique' , $this->getMessage( $input, 'unique', 'Already Exists On ' . ucfirst( $options[0] ) ) ] );
			}
		}
	}

	
	/**
	 * Check If Rule Must Be Checked Or Can Be Neglected
	 * 
	 * @param  string  $input Input name
	 * @return Boolean        True For Yes False For no
	 */
	public function mustCheck( $input )
	{
		// we can iginore rules if field not required and doesnot exists
		if( $this->isRequired( $input ) || $this->hasInput( $input ) )
			return true;
		else
			return false;
	}

	
	/**
	 * Check If Field Is Required, Has Required rule
	 * 
	 * @param  string  $input Input name
	 * @return boolean        True For Yes False For No
	 */
	public function isRequired( $input )
	{
		// we can iginore rules if field not required and doesnot exists
		return in_array( 'required', $this->required );
	}

	/**
	 * Validation Passes Without errors
	 * @return Boolean True For No Errors Otherwise False
	 */
	public function passes()
	{
		// if( empty( $this->errors ) )
		if( !$this->errors->any() )
			return true;
		else
		{
			// Flash Inputs, Errors To Session
			App::get( 'session' )->flash( 'input', $this->inputs )->flash( 'errors', $this->errors );
			
			return false;
		}
	}

	
	/**
	 * Validation Fails ( Has Errors )
	 * 
	 * @return Boolean True For Errors Otherwise False
	 */
	public function fails()
	{
		return !$this->passes();
	}

	/**
	 * Get Error Message If There is Custom Message, Otherwise Return Default Error Message
	 * @param  String $input   Input name
	 * @param  String $rule    Rule Name
	 * @param  string $default Default Error Message
	 * @return string          Custrom Error Message Othertwise Default Error Message
	 */
	protected function getMessage( $input , $rule = null, $default = null )
	{
		$rule = $rule ?: '*';

		if( isset( $this->messages[ $input ][ $rule ] ) )
			return $this->messages[ $input ][ $rule ];
		
		else if( isset( $this->messages[ $input ][ '*' ] ) )
			return $this->messages[ $input ][ '*' ];
		
		else
			return $default;

	}

}

// الحمد لله
// Ahmed saad Fri, 30 Sep 2016 11:24 PM