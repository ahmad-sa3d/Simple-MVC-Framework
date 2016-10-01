<?php
/**
 * MethodNotFoundException
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 */

namespace App\Library\Exceptions;

use \Exception;

class MethodNotFoundException extends Exception
{

	public function __construct( $method = null, $code = null, Exception $previous = null )
	{
		parent::__construct( $method . ' Method Not Found.', $code, $previous );
	}

	
	public function __toString()
	{
		return $this->getMessage();
	}


}