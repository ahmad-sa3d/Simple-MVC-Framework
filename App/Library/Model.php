<?php
/**
 * App Model Class
 *
 * Application Model That Will inherited By All Models
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 * @property-read Array $hidden Model Hidden Attributes
 * @property-read Array $timestamps Model Timestamps Attributes
 */

namespace App\Library;

use App\Library\DatabaseObjectTrait;
use App\Library\MagicGetterTrait;
use App\Library\SimpleDateTime;

abstract class Model
{
	use DatabaseObjectTrait, MagicGetterTrait;
	
	/**
	 * Model Hidden Attributes
	 * @var array
	 */
	protected $hidden = [];
	
	/**
	 * Timestamps Attributes
	 * @var Array
	 */
	protected $timestamps = [ 'created_at', 'updated_at' ];

	/**
	 * Cast Attributes To Defined Types To be Used In Model Instance
	 * @return Null
	 */
	public function cast()
	{
		if( isset( $this->casts )   )
		{
			foreach( $this->casts as $field => $type )
			{
				if( $type == 'array' )
				{
					$this->$field = json_decode( $this->$field, true );

					if( !is_array( $this->$field ) )
						$this->$field = isset( $this->$field ) ? [ $this->$field ] : [];
				}
				else if( $type == 'boolean' )
				{
					$this->$field = $this->$field ? true : false;
				}
				else if( $type == 'integer' || $type == 'numeric' )
				{
					$this->$field = $this->$field + 0;
				}
				else
					// $this->$field = settype( $this->$field, $type );
					throw New Exception( 'Casting To Type ' . $type . ' Not supported.' );
			}
		}

		// Time Stamps
		if( isset( $this->timestamps ) )
		{
			foreach( $this->timestamps as $timestamp )
			{
				if( property_exists( $this, $timestamp ) )
				{
					if( $this->$timestamp )
						$this->$timestamp = SimpleDateTime::parse( $this->$timestamp );
					else
					{
						$this->$timestamp = new SimpleDateTime();
						$this->save();
					}
				}
			}
		}
	}

	/**
	 * Cast Back Properties To Its Actual Type To Bes Inserted Ito Database
	 * @return Null
	 */
	public function castForSave()
	{
		if( isset( $this->casts )   )
		{
			foreach( $this->casts as $field => $type )
			{
				if( property_exists( $this, $field ) )
				{
					if( $type == 'array' )
					{
						if( !is_array( $this->$field ) )
							$this->$field = isset( $this->$field ) ? [ $this->$field ] : [];

						$this->$field = json_encode( $this->$field );
					}
					else if( $type == 'boolean' )
						$this->$field = $this->$field ? 1 : 0;

					else if( $type == 'integer' || $type == 'numeric' )
						$this->$field = $this->$field + 0;

					else
						// $this->$field = settype( $this->$field, $type );
						throw New Exception( 'Casting To Type ' . $type . ' Not supported.' );
				}
					
			}
		}

		// Time Stamps
		if( isset( $this->timestamps ) )
		{
			foreach( $this->timestamps as $timestamp )
			{
				if( property_exists( $this, $timestamp ) )
				{
					if( $this->$timestamp instanceof SimpleDateTime )
						$this->$timestamp = $this->$timestamp->getMySQLDateTime();
					
					else if( !empty( $this->$timestamp ) )
						$this->$timestamp = SimpleDateTime::parse( $this->$timestamp )->getMySQLDateTime();
					
					else
						$this->$timestamp = (new SimpleDateTime())->getMySQLDateTime();
				}
			}
		}

	}
	
}

?>