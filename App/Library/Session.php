<?php
/**
 * App Session
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 * @property-read App\Model\User $user User Instance
 */
namespace App\Library;

use RuntimeException;
use App\Library\App;
use App\Model\User;
use App\Library\MagicGetterTrait;

class Session
{
	use MagicGetterTrait;
	
	/**
	 * Loged In User Instance
	 * @var App\Model\User | Null
	 */
	protected $user = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		if( App::has( 'session' ) )
			throw new RuntimeException( __METHOD__ . ' Canot Instantiate Another Session Instance, There Is Already Instantiated One, Call Session::getInstance()' );
		
		@session_start();

		// Clear Expired Flashed 
		$this->clearFlashed();

		// Login User
		if( $this->has( 'user_id' ) )
			$this->loginUsingId( $this->get( 'user_id' ) );

		// Register
		App::register( 'session', $this );

	}
	
	/**
	 * Save Value in Session
	 * @param string $key   Key To associate Value With
	 * @param Mixed $value 	Value To save
	 */
	public function set( $key , $value )
	{
		$_SESSION[ $key ] = $value;

		return $this;
	}

	/**
	 * Save Value in Session For Only One Request
	 * After That It Will Be Cleared Automatically

	 * @param  string $key   Value Key
	 * @param  Mixed $value Value To Save
	 * @return Session        Current Session Instance
	 */
	public function flash( $key , $value )
	{
		$this->set( $key, $value );
		
		if( $this->has( 's_flashing' ) )
			array_push( $_SESSION[ 's_flashing' ], $key );
		else
			$this->set( 's_flashing', [ $key ] );

		// Check if existed in flashed, remove from
		if( $this->has( 's_flashed' ) && ($i = array_search( $key , $_SESSION['s_flashed'] )) !== false )
			unset( $_SESSION['s_flashed'][$i] );

		return $this;
	}

	/**
	 * Clear Expired Flashed Values
	 */
	protected function clearFlashed()
	{
		// Delete Expired
		if( $this->has( 's_flashed' ) )
		{
			foreach( $this->get('s_flashed') as $key )
			{
				$this->forget( $key );
			}

			$this->forget( 's_flashed' );
		}

		// Move Current Flashed To Flashed
		if( $this->has( 's_flashing' ) )
		{
			foreach( $this->get('s_flashing') as $i=>$key )
			{
				unset( $_SESSION[ 'flashing' ][$i] );
				
				if( $this->has( 's_flashed' ) )
					array_push( $_SESSION[ 's_flashed' ], $key );
				else
					$this->set( 's_flashed', [ $key ] );
			}

			$this->forget( 's_flashing' );
		}

	}

	/**
	 * Check If Session has A Specific key
	 *
	  * * 'test' => [ 'subkey' => 'value' ]
	 * 		$session->has( 'test' ) ==> true
	 * 		$session->has( 'test', 'subkey' ) ==> true
	 * 		$session->has( 'test', 'subkey2' ) ==> false
	 * 
	 * @param  string  $key       Key To Check If Stored Or Not
	 * @param  string  $child_key Sub Key Of Previos key
	 * @return boolean            True if Exists Otherwise False
	 */
	public function has( $key, $child_key = null )
	{
		if( $child_key )
			return isset( $_SESSION[ $key ][ $child_key ] );
		else
			return isset( $_SESSION[ $key ] );
	}

	/**
	 * Get Key Value Stored In Session
	 *
	 * * 'test' => [ 'subkey' => 'value' ]
	 * 		$session->get( 'test' ) ==> [ 'subkey' => 'value' ]
	 * 		$session->get( 'test', 'subkey' ) ==> 'value'
	 * 
	 * @param  string $key     Value Key
	 * @param  string $default Sub Key
	 * @return [type]          [description]
	 */
	public function get( $key, $default = null )
	{
		return $this->has( $key ) ? $_SESSION[ $key ] : $default;
	}

	/**
	 * Get Session Value, Then Forget It
	 * 	
	 * @param  string $key 		Value key
	 * @param  string $default 	Default Value
	 * @return Mixed      		Value | Default
	 */
	public function pull( $key, $default = null )
	{
		if( $val = $this->get( $key ) )
		{
			$this->forget( $key );

			return $val;
		}

		return $default;
	}

	/**
	 * Delete Key From session
	 * 
	 * @param  String $key Key To Forget
	 * @return Session      Current Session instance
	 */
	public function forget( $key )
	{
		if( $this->has( $key ) )
			unset( $_SESSION[ $key ] );

		return $this;
	}
	
	/**
	 * Get Current Session Instance
	 * 
	 * @return Session Instance
	 */
	public static function getInstance()
	{
		if( App::has( 'session' ) )
			return App::get( 'session' );
		else
			return new self();
	}
	
	/**
	 * LOGIN USER
	 * 
	 * @param  User   $user User Instance
	 * @return Session       Current session Instance
	 */
	public function login( User $user )
	{
		$this->set( 'user_id', $user->id );
		$this->user = $user;

		return $this;
		# Method End
	}

	/**
	 * Login User By Id
	 * 
	 * @param  Integer $id User Id
	 * @return App\Model\User     User Instance
	 */
	public function loginUsingId( $id )
	{
		if( is_numeric( $id ) && $id > 0 )
		{
			$this->user = User::findById( $id );
			
			if( $this->user )
				$this->set( 'user_id', $id );
			else
				 $this->user = null;

			return $this->user;
		}
		else
			throw new RuntimeException( __METHOD__ . ' Require One Parameter To Be Integer and Greater Than Zero' );
		# Method End
	}

	/**
	 * Check if There are Logged In User
	 * 
	 * @return boolean True If There is Otherwise False
	 */
	public function isLoggedIn()
	{
		return $this->has( 'user_id' ) && $this->user;
		# Method End
	}
	
	
	/**
	 * Logout User
	 * 
	 * @return Session Current session Instance
	 */
	public function logout()
	{
		$this->forget( 'user_id' );
		$this->user = null;

		return $this;
		# Method End
	}
	
	
	/**
	 * Destroy Session
	 */
	public function destroy()
	{
		$this->logout();
		unset( $_SESSION ); // note that this will delete any saved messages, notifications, etc
		session_destroy();
		# Method End
	}

}

// الحمد لله
// 25/9/2016 2:00 AM