<?php

/**
 * Magic Getter Trait
 *
 * Trait To Allow Magic Getter Method And __toString
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 */
namespace App\Library;

trait MagicGetterTrait
{
	/**
	 * Magic Getter
	 *
	 * Get Protected Properties As Readonly
	 * @param  string $property property name
	 * @return mixed           Property value
	 */
	public function __get($property)
	{
		if( property_exists( $this, $property ) )
			return $this->$property;
		else
			return null;
		// else
		// 	throw new \RuntimeException( __CLASS__ . '::class accessing undefined property "' . $property . '"' );
	}

		/**
	 * Magic Getter
	 *
	 * Get Protected Properties As Readonly
	 * @param  string $property property name
	 * @return mixed           Property value
	 */
	public function __toString()
	{
		$arr = [];

		foreach( get_object_vars( $this ) as $p => $value )
		{
			if( ( property_exists( $this, 'hidden' ) && in_array( $p, $this->hidden ) ) || in_array( $p, ['hidden', 'casts'] ) )
				continue;

			$arr[ $p ] = $value;
		}

		// return $arr;
		return json_encode( $arr );
	}
	
}