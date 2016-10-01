<?php
/**
 * Base Controller
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 * @property-read App\Library\Session $session Session Application session Instance
 * 
 */

namespace App\Controllers;

use App\Library\App;
use App\Library\MagicGetterTrait;

class Controller
{
	use MagicGetterTrait;

	/**
	 * Application session instance
	 * @var App\Library\Session
	 */
	protected $session;
	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Register
		App::register( 'controller', $this );

		$this->session = App::get( 'session' );
	}

	/**
	 * Make Session notification
	 * @param  String $type    Notification Type: success, danger, warning
	 * @param  String $message Notification Message
	 * @param  String $link    Notification Provided Link
	 */
	public function makeNotification( $type, $message, $link = null )
	{
		$this->session->set( 'notification', compact( 'type', 'message', 'link' ) );
	}

	
	/**
	 * Make Session Succress notification
	 * @param  String $message Notification Message
	 * @param  String $link    Notification Provided Link
	 */
	public function makeSuccessNotification( $message, $link = null )
	{
		$type = 'success';
		$this->session->set( 'notification', compact( 'type', 'message', 'link' ) );
	}

	
	/**
	 * Make Session Error notification
	 * @param  String $message Notification Message
	 * @param  String $link    Notification Provided Link
	 */
	public function makeErrorNotification( $message, $link = null )
	{
		$type = 'danger';
		$this->session->set( 'notification', compact( 'type', 'message', 'link' ) );
	}

	/**
	 * Hash Password
	 * @param  String|Integer $value Password
	 * @return String        Hashed Password
	 */
	public function hashMake( $value )
	{
		return password_hash( $value, PASSWORD_DEFAULT );
	}

	/**
	 * Verify Hashed Password
	 * @param  String|Integer 	$value  Password
	 * @param  String 			$hash   Hashed Password
	 * @return Boolean        			True If Verified, Otherwise false
	 */
	public function hashVerify( $value, $hash )
	{
		return password_verify( $value, $hash );
	}

}

// الحمد لله
// Ahmed saad, 1 Oct 2016 12:15 AM