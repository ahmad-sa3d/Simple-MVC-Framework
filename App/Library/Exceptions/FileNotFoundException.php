<?php
/**
 * FileNotFoundException
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 */

namespace App\Library\Exceptions;

use \Exception;

class FileNotFoundException extends Exception
{

	public function __construct( $filename = null, $code = null, Exception $previous = null )
	{
		parent::__construct( 'File "' . $filename .'" Not Found.', $code, $previous );
	}

	
	public function __toString()
	{
		return $this->getMessage();
	}


}