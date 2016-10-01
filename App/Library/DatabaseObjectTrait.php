<?php

/**
 * Database Object trait
 *
 * Trait That Performs Most Model database Operations
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 */

namespace App\Library;

use App\Library\App;
use PDO;

trait DatabaseObjectTrait
{
	
	/**
	 * PDO Instance
	 * @var Object
	 */
	protected static $DBH;
	
	/**
	 * Start Application Database Connection
	 * @return Object PDO Instance
	 */
	public static function openConnection(  )
	{
		try
		{
			if( !isset( self::$DBH ) || !(self::$DBH instanceof PDO) )
			{
				self::$DBH = new PDO( 'mysql:host=' . DB_HOSTNAME . ';dbname=' . DB_NAME, DB_USERNAME, DB_USERPWD );

				self::$DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

				// Register in Service Container
				App::register( 'DBH', self::$DBH );
			}
			
			return self::$DBH;

		}
		catch( PDOException $e )
		{
			die( 'Database Error: ' . $e->getMessage() );
		}
		# Method End
	}

	/**
	 * Close Application Database Connection
	 * @return Null
	 */
	public static function closeConnection()
	{
		if( isset( self::$DBH ) )
			self::$DBH = null;
		
		# Method End
	}


	/**
	 * Get Database PDO Instance
	 * @return Object PDO Instance OR Null
	 */
	public static function getDBH()
	{
		if( !self::$DBH )
			throw new Exception( 'there are no openned database connection' );

		return self::$DBH;

		# Method End
	}


	#################################################################
	

	/**
	 * Count All Records For Current Model
	 * @return Integer Records Count
	 */
	public static function countAll()
	{
		$sql = 'SELECT COUNT(*) FROM `' . static::$table_name . '`';
		
		$result = self::findBySql( $sql, null, array( 'fetch_all' => false, 'fetch_mode' => PDO::FETCH_NUM ) );
		
		return array_shift( $result );

		# Method End
	}

	/**
	 * Get All Model Records
	 * @param  array  						$options 	Query Options
	 * @return Array || Object || False          		Query Result
	 */
	public static function all( Array $options = array() )
	{
		#$fields = ( isset( $options['fields'] ) ) ? $options['fields'] : '*';

		$sql = 'SELECT * FROM `' . static::$table_name . '`';

		// self::completeQueryString( $sql, $options );
		
		return self::findBySql( $sql, null, $options );

		# Method End
	}

	/**
	 * Get Model Record By Its Id
	 * @param  Integer 					$id record id
	 * @return Array || Object || false     query result
	 */
	public static function findById( $id, Array $options = array() )
	{
		$options['fetch_all'] = false;
		// $options['fetch_mode'] = PDO::FETCH_CLASS;

		$sql = 'SELECT * FROM `' . static::$table_name . '` WHERE `id` = :id LIMIT 1';
		
		return self::findBySql( $sql, array( 'id' => $id ), $options );

		# Method End
	}

	/**
	 * Get Model Records That Matches Supplied Conditions
	 * @param  Array  						$options Options
	 * @return Array || Object || Boolean 	Query Result
	 */
	public static function findWhere( Array $field_value, Array $options = array() )
	{
		$options = array_merge(
			array( 'fetch_all' => false ),
			$options
			);
		
		$conditions = array();
		
		foreach( $field_value as $field => $value )
			$conditions[] = " `$field` = :$field";
		
		
		$join_operator = ( isset( $options['operator'] ) && $options['operator'] == 'OR' ) ? ' OR' : ' AND';
		
		unset( $options['operator'] );
		
		$conditions = join( $conditions, $join_operator );
		
		$sql = 'SELECT * FROM `' . static::$table_name . '` WHERE' . $conditions;

		// self::completeQueryString( $sql, $options );
		
		return self::findBySql( $sql, $field_value, $options );
		
		# Method End
	}

	/**
	 * Get Model Records By Specific SQL Statement
	 * @param  string 	$sql            	full query string
	 * @param  array 	$prepare_values  	values to bind to query string
	 * @param  array  	$options        	Query options contains fetch_all, fetch_mode
	 * @return Array || Object || Boolean   return query result
	 */
	public static function findBySql( $sql, $prepare_values = null, Array $options = array() )
	{
		$options = array_merge(
			array( 'fetch_mode' => PDO::FETCH_CLASS, 'fetch_all' => true ),
			$options
			);

		self::completeQueryString( $sql, $options );
		
		if( $prepare_values )
		{
			# use prepare method
			$query = self::$DBH->prepare( $sql );

			$query->execute( $prepare_values );
		}
		else
			# use query method
			$query = self::$DBH->query( $sql );
		

		# Get Result
		
		if( $options['fetch_mode'] == ( $options['fetch_mode'] | PDO::FETCH_CLASS )  )
		{
			# Class Fetch
			$query->setFetchMode( $options['fetch_mode'], static::$class );

			$fetch_class_mode = true;
		}
		else
		{
			$query->setFetchMode( $options['fetch_mode'] );

			$fetch_class_mode = true;
		}

		
		$set = ( $options['fetch_all'] ) ? $query->fetchAll() : $query->fetch();

		return ( $fetch_class_mode && $set ) ? self::checkCasting( $set ) : $set;
	
		# Method End
	}

	
	/**
	 * Helper Method To Complete SQL Statement 
	 * @param  string $sql     query string
	 * @param  array  $options query options
	 */
	private static function completeQueryString( &$sql, &$options )
	{
		
		if( isset( $options['order_by'] ) )
		{
			$sql .= ' ORDER BY ' . $options['order_by'];
			$sql .= ( isset( $options['order'] ) ) ? ' ' . $options['order'] : null;
		}
		

		$sql .= ( isset( $options['limit'] ) ) ? ' LIMIT ' . $options['limit'] : null;

		$sql .= ( isset( $options['offset'] ) ) ? ' OFFSET ' . $options['offset'] : null;

		unset( $options[ 'limit' ], $options[ 'offset' ], $options[ 'order_by' ] );

		# Method End
	}


	#################################################################
	
	/**
	 * Choose the correct action method of create or update
	 * @return Integer number of inserted or updated rows
	 */
	public function save()
	{
		return isset( $this->id ) ? $this->update() : $this->create();
	}

	/**
	 * Insert record
	 * @return Integer number of inserted rows
	 */
	public function create()
	{
		$this->castForSave();
		
		$field_value = array();
		
		foreach( static::$db_fields as $field )
			$field_value[ $field ] = $this->$field;

		
		$sql = 'INSERT INTO `' . static::$table_name . '` ( `' . join( static::$db_fields, '`, `' ) . '` ) VALUES ( :' . join( static::$db_fields, ', :' ) . ' )';

		$query = self::$DBH->prepare( $sql );

		$query->execute( $field_value );

		$this->id = self::$DBH->lastInsertId();

		return ( $query->errorCode() + 0 ) ? false : $query->rowCount() ;

	} 
	
	/**
	 * Update record
	 * @return Integer number of updated rows, 0 mean no change happens 'may be updated but to the same values, so with no changes'
	 */
	public function update()
	{
		$this->castForSave();

		$field_field = $field_value = array();

		foreach( static::$db_fields as $field )
		{
			$field_field[] = "`$field` = :$field";

			$field_value[ $field ] = $this->$field;
		}

		
		$sql = 'UPDATE `' . static::$table_name . '` SET ' . join( $field_field, ' , ' ) . ' WHERE `id` = :id LIMIT 1';

		$query = self::$DBH->prepare( $sql );

		$query->execute( $field_value );

		return ( $query->errorCode() + 0 ) ? false : $query->rowCount() ;

		# Method End
	}


	/**
	 * Delete record
	 * @return Integer number of deleted rows
	 */
	public function delete()
	{
		$sql = 'DELETE FROM `' . static::$table_name . '` WHERE `id` = :id';

		$query = self::$DBH->prepare( $sql );

		$query->execute( array( 'id' => $this->id ) );

		return $query->rowCount();
	}

	/**
	 * Delete record
	 * @return Integer number of deleted rows
	 */
	public static function checkCasting( $set )
	{
		if( is_array( $set ) )
		{
			foreach( $set as &$instance )
				$instance->cast();
		}
		else
			$set->cast();

		return $set;
	}

	# Class End الحمد لله
}

?>