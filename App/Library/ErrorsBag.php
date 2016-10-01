<?php

/**
 * Errors Bag
 *
 * Bag That Contains Validation Errors
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 */
namespace App\Library;

use Exception;
use App\Library\App;

class ErrorsBag
{
	/**
	 * Errors Array
	 * @var Array
	 */
	protected $errors;

	/**
	 * All Errors Count
	 * @var Integer
	 */
	protected $count;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->errors = [];
		$this->count = 0;
	}

	/**
	 * Add An Error
	 * @param string $key   Error Key, EX: Input Name
	 * @param Mixed $value  Error Content, [ rule => value ]
	 */
	public function add( $key, Array $value )
	{
		$this->errors[ $key ][ $value[0] ] = $value[1];

		$this->count++;
	}


	/**
	 * Check If There is Error For A Specifc Key 'INPUT'
	 * @param  String  $key Input Name
	 * @return boolean      Input Has Any Error Or Not
	 */
	public function has( $key )
	{
		return isset( $this->errors[ $key ] );
	}

	/**
	 * Check If There Are ny Error in Errors Array
	 * @return Boolean Errors Array Has any Error Or Not
	 */
	public function any()
	{
		return !empty( $this->errors );
	}

	/**
	 * Get First Error For A Specific Input
	 * @param  String $key Input name
	 * @return Mixed      First Error Message For Input Or Null if there isnot
	 */
	public function first( $key )
	{
		if( $this->has( $key ) )
		{
			$first = array_slice( $this->errors[ $key ], 0, 1 );
			return array_shift( $first );
		}
		else
			return null;
	}

	/**
	 * Get All Errors For Specific Input if Supplied, Otherwise Get All Errors In one Numeric Array
	 * @param  String $key Input Name | Optional
	 * @return Array      Numerical Based Array Of All Errors Or Empty Array If There Arenot
	 */
	public function all( $key = null )
	{
		if( $key != null )
		{
			if( $this->has( $key ) )
				return $this->errors[ $key ];
			else
				return [];
		}
		else
		{
			// Return all errors
			$all = [];
			foreach( $this->errors as $input => $erros )
			{
				foreach( $errors as $rule => $error )
					aray_push( $all, $input . ' : ' . $error );
			}

			return $all;
		}
	}

}