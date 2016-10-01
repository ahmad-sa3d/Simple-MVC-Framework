<?php

/**
 * App Simple Date Time
 *
 * @package Simple Framework
 * @author  Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license https://creativecommons.org/licenses/by-sa/4.0/legalcode.txt CC-BY-SA-4.0 Creative Commons Attribution Share Alike 4.0
 *
 * @property-read Integer $_year Instance Year
 * @property-read Integer $_month Instance Month
 * @property-read Integer $_day Instance Day
 */

namespace App\Library;
use App\Library\MagicGetterTrait;
use \DateTime;
use \Exception;


class SimpleDateTime extends DateTime
{
	use MagicGetterTrait;

	/**
	 * Instance Year
	 * @var Integer
	 */
	protected $_year;
	
	/**
	 * Instance Month
	 * @var Integer
	 */
	protected $_month;
	
	/**
	 * Instance Day
	 * @var Integer
	 */
	protected $_day;

	/**
	 * Locals Translation Array
	 * @var Array
	 */
	protected static $_translator = [];
	
	/**
	 * Locle Language
	 * @var string
	 */
	protected static $_local = 'en';

	/**
	 * Array Of Defined Locals
	 * @var array
	 */
	protected static $_defined_locals = [];
	
	
	/**
	 * Constructor
	 * @param \DateTimeZone $TimeZone Time Zone Instance
	 */
	public function __construct(  DateTimeZone $timezone = NULL )
	{
		if( $timezone )
			parent::__construct( 'now', $timezone );
		
		else
			parent::__construct( 'now' );
		
		$this->_year = ( int ) $this->format( 'Y' );
		$this->_month = ( int ) $this->format( 'n' );
		$this->_day = ( int ) $this->format( 'j' );
	}
	
	
	/**
	 * Set Time
	 * @param Integer  $houre  Hour
	 * @param Integer  $minute Minute
	 * @param integer $second Second
	 *
	 * @return SimpleDateTime urrent Instance
	 * 
	 */
	public function setTime( $houre, $minute, $second = 0 )
	{
		if( !is_numeric( $houre ) || !is_numeric( $minute ) || !is_numeric( $second ) )
			throw new Exception( "Date::setTime() excpects three numeric arguments the first two are requird and the third are optional, there are 'houre, minute, second' in the same order" );
		
		if( $houre < 0 || $houre > 23 || $minute < 0 || $minute > 59 || $second < 0 || $second > 59 )
			throw new Exception( 'invalid time' );
		
		parent::setTime( $houre, $minute, $second );

		return $this;

	}
	
	
	/**
	 * Set Date
	 * @param Integer  $year  Year
	 * @param Integer  $month Month
	 * @param integer $day Day
	 *
	 * @return SimpleDateTime urrent Instance
	 * 
	 */
	public function setDate( $year, $month, $day )
	{
		if( !is_numeric( $year ) || !is_numeric( $month ) || !is_numeric( $day ) )
			throw new Exception( __METHOD__ . " excpects three numeric arguments all are requird , there are 'year, month, day' in the same order" );
		
		if( !checkdate( $month, $day, $year ) )
			throw new Exception( __METHOD__ . 'i nvalid date : none existing date' );
		
		parent::setDate( $year, $month, $day );
		$this->_year = ( int )$year;
		$this->_month = ( int )$month;
		$this->_day = ( int )$day;

		return $this;
	}
	
	
	/**
	 * Disable Modify Method
	 */
	public function modify( $arg = '' )
	{
		throw new Exception( __METHOD__ . ' has been disabled.' );
	}
	
	
	/**
	 * Set Date As DMY
	 * @param String $dmy_date Date as d-m-Y, d/m/Y, d_m_Y, d:m:Y, d.m.Y
	 *
	 * @return  Simple dateTime Current instance
	 */
	public function setDMY( $dmy_date )
	{
		$date = preg_split( '/[-_ .:\/]{1,5}/', $dmy_date );
		
		if( !is_array( $date ) || count( $date ) !=3 )
			throw new Exception( __METHOD__ . 'invalid provided date format, require DD/MM/YYYY' );
		
		$this->setDate( $date[2], $date[1], $date[0] );

		return $this;
	}
	
	/**
	 * Set Date As MDY
	 * @param String $mdy_date Date as d-m-Y, d/m/Y, d_m_Y, d:m:Y, d.m.Y
	 *
	 * @return  Simple dateTime Current instance
	 */
	public function setMDY( $mdy_date )
	{
		$date = preg_split( '/[-_ .:\/]{1,5}/', $mdy_date );
		
		if( !is_array( $date ) || count( $date ) !=3 )
			throw new Exception( __METHOD__ . 'invalid provided date format, require MM/DD/YYYY' );
		
		$this->setDate( $date[2], $date[0], $date[1] );

		return $this;
	}
	
	
	
	/**
	 * Set Date Time From MySql Date Format
	 * 
	 * @param String $mysql_date Mysql DateFormat 'Y-m-d'
	 *
	 * @return  SimpleDateTime
	 */
	public function setMySQLDate( $mysql_date )
	{
		$date = preg_split( '/[-]{1}/', $mysql_date );
		
		if( !is_array( $date ) || count( $date ) !=3 )
			throw new Exception( __METHOD__ . 'invalid provided date format, Date::setMySQLDate() require YYYY-MM-DD' );
		
		$this->setDate( $date[0], $date[1], $date[2] );

		return $this;
	}
	
	
	/**
	 * Set Date Time From MySql DateTime Format
	 * 
	 * @param String $mysql_date Mysql DateTime Format 'Y-m-d H:i:s'
	 *
	 * @return  SimpleDateTime
	 */
	public function setMySQLDateTime( $mysql_datetime )
	{
		$date = preg_split( '/[-: ]{1}/', $mysql_datetime );
		
		if( !is_array( $date ) || count( $date ) !=6 )
			throw new Exception( __METHOD__ . 'invalid provided date format, Date::setMySQLDateTime() require YYYY-MM-DD H:i:s' );
		
		$this->setDate( $date[0], $date[1], $date[2] );
		$this->setTime( $date[3], $date[4], $date[5] );

		return $this;
	}
	
	
	/**
	 * Get Date In DMY Format
	 *
	 * @param  String $seperator Seperator Between day, monthy, year
	 * @return String date
	 */
	public function getDMY()
	{
		$args = $this->argumentAnalysis( func_get_args() );
		
		$seperator = $args['seperator'];
		$zeroes = $args['zeroes'];
		
		$format = ( $zeroes ) ? 'd' . $seperator . 'm' . $seperator . 'Y' : 'j' . $seperator . 'n' . $seperator . 'Y';
		
		return $this->format( $format );
	}
	
	
	/**
	 * Get Date In MDY Format
	 *
	 * @param  String $seperator Seperator Between monthy, day, year
	 * @return String date
	 */
	public function getMDY()
	{
		$args = $this->argumentAnalysis( func_get_args() );
		
		$seperator = $args['seperator'];
		$zeroes = $args['zeroes'];
		
		$format = ( $zeroes ) ? 'm' . $seperator . 'd' . $seperator . 'Y' : 'n' . $seperator . 'j' . $seperator . 'Y';
		
		return $this->format( $format );
	}
	
	
	/**
	 * Get Date In Mysql date Format
	 *
	 * @return String date
	 */
	public function getMySQLDate()
	{
		return $this->format( 'Y-m-d' );
	}
	
	
	/**
	 * Get Date In Mysql Date Time Format
	 *
	 * @return String date
	 */
	public function getMySQLDateTime()
	{
		return $this->format( 'Y-m-d H:i:s' );
	}

	
	/**
	 * Get day Number
	 * 
	 * @param  boolean $zeroes With Left Zero Like 01, 02, .., 09 or not
	 * @return Integer          Number
	 */
	public function getDay( $zeroes = false )
	{
		return ( $zeroes ) ? $this->format( 'd' ) : $this->format( 'j' );
	}
	
	
	/**
	 * Get Numeric Day Ordinal Lile 1st, 2nd, 3rd
	 * 
	 * @return String Ordinal day
	 */
	public function getDayOrdinal()
	{
		return $this->format( 'jS' );
	}
	
	
	/**
	 * Get Full Day Name
	 * 
	 * @return String Full Day name, Friday, ....
	 */
	public function getDayName()
	{
		if( self::$_local != 'en' )
			return self::translate( 'day', $this->getDayAbbr() );
			
		else
			return $this->format( 'l' );
			
	}
	
	
	/**
	 * Get day Short name, Fri
	 * 
	 * @return String Day Abbreviation
	 */
	public function getDayAbbr()
	{
		return $this->Format( 'D' );
	}

	
	/**
	 * Get Month Number
	 * 
	 * @param  boolean $zeroes With Left zero 01, ..., 09
	 * @return Integer          Month Number
	 */
	public function getMonth( $zeroes = false )
	{
		return ( $zeroes ) ? $this->format( 'm' ) : $this->format( 'n' );
	}
	
	
	/**
	 * Get Month Full Name
	 * 
	 * @return String Full Month name, April, ..
	 */
	public function getMonthName()
	{
		return ( self::$_local != 'en' ) ? self::translate( 'month', $this->getMonthAbbr() ) : $this->format( 'F' );
	}
	
	
	/**
	 * Get Month Short Name ( Abbreviation )
	 * 
	 * @return String Abbreviation Month name, Sep, ..
	 */
	public function getMonthAbbr()
	{
		return $this->format( 'M' );
	}
	
	
	/**
	 * Get Full Year
	 * 
	 * @return Integer Full Year, 2012, .., 2016
	 */
	public function getFullYear()
	{
		return $this->format( 'Y' );
	}
	
	
	/**
	 * Get Year in Two Numeric Representation
	 * 
	 * @return Integer Year, Like 12, ... , 16
	 */
	public function getYear()
	{
		return $this->format( 'y' );
	}


	/**
	 * Get Hour
	 * @param  integer $format 12 Or 24 Format
	 * @return Integer          Hour
	 */
	public function getHour( $format = 24 )
	{
		return ( $format == 12 ) ? $this->format( 'g' ) : $this->format( 'H' );
	}
	
	
	/**
	 * Get Minute
	 * 
	 * @return Integer Minuite
	 */
	public function getMinuite()
	{
		return $this->format( 'i' );
	}
	
	/**
	 * Get Second
	 * 
	 * @return Integer Second
	 */
	public function getSecond()
	{
		return $this->format( 's' );
	}
	
	
	/**
	 * Get Period am | pm
	 * 
	 * @return String am | pm
	 */
	public function getPeriod()
	{
		return ( self::$_local != 'en' ) ? self::translate( 'period', $this->format( 'A' ) ) : $this->format( 'A' );
	}
	
	
	/**
	 * Get Time
	 * 9:30 AM
	 * 
	 * @return String Time 
	 */
	public function getTime()
	{
		return ( self::$_local != 'en' ) ? $this->format( 'g:i ' ) . $this->getPeriod( self::$_local ) : $this->format( 'g:i A' );
	}
	
	/**
	 * Get Time
	 * 9:30:20 AM
	 * 
	 * @return String Time 
	 */
	public function getFullTime()
	{
		return ( self::$_local != 'en' ) ? $this->format( 'g:i:s' ) . ' ' . $this->getPeriod( self::$_local ) : $this->format( 'g:i:s A' );
	}
	

	/**
	 * Get Timezone Offset From GMT
	 * 
	 * @return Integer Offset Hours
	 */
	public function getTimeZoneOffset()
	{
		return $this->getTimeZone()->getOffset( $this ) / ( 60 * 60 );
	}
	
	
	/**
	 * Add Days
	 * 
	 * @param Integer $numDays Days To add
	 * @return SimpleDateTime Instance
	 */
	public function addDays( $numDays )
	{
		if( !is_numeric( $numDays ) || $numDays < 1 )
			throw new Exception( __METHOD__ . ' expects positive integer' );
		
		parent::modify( '+' . intval( $numDays ) . 'days' );

		return $this;
	}
	
	
	/**
	 * Sub Days
	 * 
	 * @param Integer $numDays Days To Subtract
	 * @return SimpleDateTime Instance
	 */
	public function subDays( $numDays )
	{
		if( !is_numeric( $numDays ) )
			throw new Exception( __METHOD__ . ' expects integer' );
		
		parent::modify( '-' . abs( intval( $numDays ) ) . 'days' );

		return $this;
	}
	
	
	/**
	 * Add weeks
	 * 
	 * @param Integer $numWeeks Weeks To add
	 * @return SimpleDateTime instance
	 */
	public function addWeeks( $numWeeks )
	{
		if( !is_numeric( $numWeeks ) || $numWeeks < 1 )
			throw new Exception( __METHOD__ . ' expects positive integer' );
		
		parent::modify( '+' . intval( $numWeeks ) . 'weeks' );
	}
	
	
	/**
	 * Subtract Weeks
	 * 
	 * @param Integer $numWeeks Weeks To Subtract
	 * @return SimpleDateTime instance
	 */
	public function subWeeks( $numWeeks )
	{
		if( !is_numeric( $numWeeks ) )
			throw new Exception( __METHOD__ . ' expects integer' );
		
		parent::modify( '-' . abs( intval( $numWeeks ) ) . 'weeks' );
		# re assighn _year, _month, _day values
		return $this;
	}
	
	
	/**
	 * Add Months
	 * 
	 * @param Integer $numMonths Months To add
	 * @return SimpleDateTime instance
	 */
	public function addMonths( $numMonths )
	{
		if( !is_numeric( $numMonths ) || $numMonths < 1 )
			throw new Exception( __METHOD__ . ' expects positive integer' );
		
		$newValue = $this->_month + intval( $numMonths );
		
		if( $newValue <= 12 )
		{
			// in the same year && $newValue is the new month
			$this->_month = $newValue;
		}
		else
		{
			// calculating the new month and the new year
			$notDecember = $newValue % 12;
			
			if( $notDecember )
			{
				// the new month is $notDecember && the new year is the round down of the division of $newValue by 12
				$this->_month = $notDecember;
				$this->_year  += floor( $newValue / 12 );
			}
			else
			{
				// new month is december && the new year is as th above but subtracting 1 year
				$this->_month = 12;
				$this->_year  = ( floor( $newValue / 12 ) ) - 1;
			}
		}
		
		$this->checkLastDayOfMonth();
		
		$this->setDate( $this->_year, $this->_month, $this->_day );

		return $this;

	}
	
	/**
	 * Subtract Months
	 * 
	 * @param Integer $numMonths Months To Subtract
	 * @return SimpleDateTime instance
	 */
	public function subMonths( $numMonths )
	{
		
		if( !is_numeric( $numMonths ) )
			throw new Exception( __METHOD__ . ' expects integer' );
		
		$newValue = $this->_month - ( abs( intval( $numMonths ) ) );
		
		if( $newValue > 0 )
		{
			// then we still in the same year and the month will be $newValue
			$this->_month = $newValue;
		}
		else
		{
			// we back to a prvious year
			$newValue = abs( $newValue );
			$months = range( 12, 1 );
			
			// calculating the month
			$monthPosition = $newValue % 12;
			$this->_month = $months[ $monthPosition ];
			
			// calculating the year
			// this will depnds on if w are in decmber or not
			// in december $monthPosition will be 0
			if( $monthPosition )
				// not decmber
				$this->_year -= ceil( $newValue / 12 );
			
			else
				// december
				$this->_year -= ceil( $newValue / 12 ) + 1;
			
		}
		
		$this->CheckLastDayOfMonth();
		$this->setDate( $this->_year, $this->_month, $this->_day );

		return $this;
	}
	
	
	/**
	 * Add Years
	 * 
	 * @param Integer $numMonths Years To add
	 * @return SimpleDateTime instance
	 */
	public function addYears( $numYears )
	{
		if( !is_numeric( $numYears ) || $numYears < 1 )
			throw new Exception( __METHOD__ . ' Expects positive integer' );
		
		$this->_year += intval( $numYears );
		$this->CheckLastDayOfMonth();
		$this->setDate( $this->_year, $this->_month, $this->_day );

		return $this;
	}
	
	
	/**
	 * Subtract Years
	 * 
	 * @param Integer $numMonths Years To Subtract
	 * @return SimpleDateTime instance
	 */
	public function subYears( $numYears )
	{
		if( !is_numeric( $numYears ) )
			throw new Exception( __METHOD__ . ' Expects an integer' );
		
		$this->_year -= abs( intval( $numYears ) );
		$this->CheckLastDayOfMonth();
		$this->setDate( $this->_year, $this->_month, $this->_day );

		return $this;
	}
	
	
	/**
	 * Get Date Difference Between Current instance And Given Date Object
	 * @param  Date   $date date To Get Difference With
	 * @return Array       Difference As Array Of Days, Years, Months, Hours, Minuites, seconds
	 */
	public function getDateDiff( Date $date )
	{
		
		# Assuming That Current Date instance '$this' is Older than the given Date Object
		
		# Get Relationship between two dates
		
		if( $this->getTimeStamp() >= $date->getTimeStamp() )
		{
			# current instance is greater than the given date
			$greater =& $this;
			$smaller =& $date;

			$difference['current_to_given'] = 'after';
		}
		else
		{
			$greater =& $date;
			$smaller =& $this;

			$difference['current_to_given'] = 'before';
		}

		# Total Seconds Differences
		$difference['total_seconds'] = $greater->getTimeStamp() - $smaller->getTimeStamp();

		
		# Seconds
		$diff = $greater->getSecond() - $smaller->getSecond();

		if( $diff < 0 )
		{	
			$difference['seconds'] = 60 - abs( $diff );
			$backwords_strip = 1;
		}
		else
		{		
			$difference['seconds'] = abs( $diff );
			$backwords_strip = 0;
		}

		# Minuites
		$diff = $greater->getMinuite() - $smaller->getMinuite()- $backwords_strip;

		if( $diff < 0 )
		{
			$difference['minutes'] = 60 - abs( $diff );
			$backwords_strip = 1;
		}
		else
		{
			$difference['minutes'] = abs( $diff );
			$backwords_strip = 0;
		}
		
		# Hours
		$diff = $greater->getHour() - $smaller->getHour() - $backwords_strip;

		if( $diff < 0 )
		{
			$difference['hours'] = 24 - abs( $diff );
			$backwords_strip = 1;
		}
		else
		{
			$difference['hours'] = abs( $diff );
			$backwords_strip = 0;
		}

		# Days
		$diff = $greater->getDay() - $smaller->getDay()- $backwords_strip;

		if( $diff < 0 )
		{
			$difference['days'] = 30 - abs( $diff );
			$backwords_strip = 1;
		}
		else
		{
			$difference['days'] = abs( $diff );
			$backwords_strip = 0;
		}

		# Months
		$diff = $greater->getMonth() - $smaller->getMonth()- $backwords_strip;

		if( $diff < 0 )
		{
			$difference['months'] = 12 - abs( $diff );
			$backwords_strip = 1;
		}
		else
		{
			$difference['months'] = abs( $diff );
			$backwords_strip = 0;
		}

		# Years
		$diff = $greater->getFullYear() - $smaller->getFullYear() - $backwords_strip;

		$difference['years'] = ( $diff ) ? abs( $diff ) : 0;
	
		return $difference; 
		
	}
	
	
	/**
	 * Get Human Readable Date Difference Relative To current Time (now)]
	 *
	 * EX: 2 days ago
	 * 
	 * @return String Difference
	 */
	public function getHummanDiff()
	{
		
		# $diff = $this->getDateDiff( new self );
		
		$current = new self;
		
		$diff = $current->getTimeStamp() - $this->getTimeStamp();
		
		$relativity = (  $diff >= 0  ) ? 'ago' : 'later';

		$diff = abs( $diff );

		if( $diff < 11 )
		{
			# 0 - 10 seconds
			return 'few seconds ' . $relativity;
		}
		else if(  $diff < 60 )
		{
			# 11 - 59 seconds
			return 'about ' . $diff . ' seconds ' . $relativity;
		}
		else if( ( $minutes = round( $diff / 60 ) ) <  60 )
		{
			# 1 - 59 minutes
			if( $minutes == 1 )
				return 'about a minute ' . $relativity;
			else
				return $minutes . ' minutes ' . $relativity;
		}
		else if( ( $hours = round( $diff / 3600 ) ) < 24 )
		{
			# 1 - 23 hours
			if( $hours == 1 )
				return 'about an hour ' . $relativity;
			else
				return $hours . ' hours ' . $relativity;
		}
		else if( round( $diff / 86400 ) < 7 )
		{
			# 1 - 7 Days
			# Past Thursday at 5:45 pm
			$relativity = ( $relativity == 'ago' )? 'past' : 'next';
			
			return $relativity . ' ' . $this->getDayName() . ' at ' . strtolower( $this->getTime() ) ;
		}
		else
		{
			# Month Day, Year at 3:10 pm
			return $this->getMonthName() . ' ' . $this->getDay() . ', ' . $this->getFullYear() . ' at ' . strtolower( $this->getTime() );
		}
	}

	
	/**
	 * Check If Year Is Leap Year
	 * 
	 * @return boolean True If Leap Year Other wise False
	 */
	public function isLeap()
	{
		// in leap year february has 29 days
		// leap year is divisible by 400 or ( devisible by 4 && not divisible by 100 )
		if( $this->_year % 400 == 0 || ( $this->_year % 4 == 0 && $this->_year %100 != 0  ) )
			return true;
		
		else
			return false;
	}


	/**
	 * Check Last day Of Month If Valid day Or Not
	 * 
	 * @return Integer Correct last day
	 */
	final protected function checkLastDayOfMonth()
	{
		// if the date doesn't exists that's will be due to last day of the month 
		if( !checkdate( $this->_month, $this->_day, $this->_year ) )
		{
			$use30 = array( 4, 6, 9, 11 );
			
			if( in_array( $this->_month, $use30 ) )
			
				// these months alwayes uses 30 days
				$this->_day = 30;
			
			else
			
				// month must be february so last day will be 28 but in case of leap years it will be 29
				$this->_day = $this->isLeap() ? 29 : 28;
			
		}
	}
	
	
	/**
	 * Instantiate Instance And Set It From Mysql dateTime Formate
	 * 
	 * @param String $mysql_datetime Mysql dateTime Format
	 *
	 * @return  SimpleDateTime Instance
	 */
	public static function setFromMySQLDateTime( $mysql_datetime )
	{
		// note that setMySQLDateTime() method will do the checking on $mysql_datetim
		$time = new self;
		
		$time->setMySQLDateTime( $mysql_datetime );
		
		return $time;
	}

	/**
	 * Instantiate And Set Instance From Date String
	 * 
	 * @param  String $string Any valis string That Works with strtotime()
	 * @return SimpledateTime         Instance
	 */
	public static function parse( $string )
	{
		$timestamp = strtotime( $string );

		return self::setFromTimeStamp( $timestamp );
	}
	
	/**
	 * Instantiate instance And Set From Timestamp
	 * 
	 * @param Integer $time_stamp Timestamp
	 *
	 * @return  SimpledateTime Instance
	 */
	public static function setFromTimeStamp( $time_stamp )
	{
		
		if( !is_numeric( $time_stamp ) )
			throw new Exception( __METHOD__ . ' method needs one argumnet represents unix timestamp.' );
		
		$time = new self;
		
		$time->setTimeStamp( $time_stamp );
		
		return $time;
	}

	/**
	 * Set dateTime Local language
	 * 
	 * @param string $value Local name
	 */
	public static function setLocal( $value = 'en' )
	{
		if( empty( self::$_translator ) )
			self::prepareTranslator();
			
		if( in_array( self::$_defined_locals, $value ) )
			static::$_local = $value;
	}

	/**
	 * Set default Php TimeZone
	 * @param String $timezone Timezone String
	 */
	public static function setDefaultTimeZone( $timezone )
	{
		ini_set( 'date.timezone', $timezone );
	}
	
	
	/**
	 * Magic Methods
	 * 
	 * @return string 	String Representration Of Instance
	 */
	public function __toString()
	{
		if( self::$_local == 'ar' )
			return $this->getDay() . ' ' . $this->getMonthName() . ' ' .  $this->getFullYear() . ' ' . $this->getTime();
		else
			return $this->getMonthName() . ' ' . $this->getDay() . ', ' . $this->getFullYear() . ' ' . $this->getTime();
	}
			
	
	/**
	 * Helper :
	 * Analayze Arguments, To Set Zeroes And Seperator
	 * 	
	 * @param  Array $args Arguments
	 * @return Array       Array Containing Zeroes and Seperator
	 */
	private function argumentAnalysis( $args )
	{
		
		if( count( $args ) > 2 )
			throw new Exception( "Date::getDMY(), Date::getMDY(), Date::getMySQLDate(), Date::getMySQLDateTime() don't requires more than two arguments the seprator,  zeroes." );
		
		// default values
		$seperator = '/';
		$zeroes = false;
		
		$setted_sprator = false;
		$setted_zeroes = false;
		
		foreach( $args as $arg )
		{
			if( preg_match( '/^( [-_.: \/]{1,3} )$/', $arg ) )
			{
				if( $setted_sprator )
				{
					continue; // go to next $arg
				}
				else
				{
					$seperator = $arg;
					$setted_sprator = true;
					continue;
				}
			}
			
			if( $arg === true || $arg === false )
			{
				if( $setted_zeroes )
				{
					continue;
				}
				else
				{
					$zeroes = $arg;
					$setted_zeroes = true;
				}
			}
		}
		
		return array( 
					'seperator'	=> $seperator,
					'zeroes'	=> $zeroes
					 );
		
	}

	
	/**
	 * Helper: Translate Date Tranlatable Part
	 * 
	 * @param  String $type      Date Type One of  month, day, period, rel
	 * @param  String $date_part Part To Translate
	 * @return String            Translated String Or Original If not Fount In Traslation array
	 */
	private function translate( $type, $date_part )
	{
		// 1- Prepare languages array
		if( empty( self::$_translator ) )
			self::prepareTranslator();

		return isset( self::$_translator[ self::$_local ][ $type ][ $date_part ] ) ? self::$_translator[ self::$_local ][ $type ][ $date_part ] : $date_part;
	}
	
	/**
	 * Helper: 
	 * Prepare Translation Array For Translator
	 */
	private static function prepareTranslator()
	{
		// defining corresponding languages values
		self::$_translator = require_once( CONF . DS . 'simpleDateTimeLocals.php' );
		self::$_defined_locals = array_keys( self::$_translator );
	}

}

// الحمد لله
// Updated 30 Sep 10:25 PM