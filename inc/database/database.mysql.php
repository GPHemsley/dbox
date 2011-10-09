<?php

/**
 * Database Abstraction Layer (MySQL)
 *
 * Functions to use when manipulating a MySQL database.
 *
 * @package DBAL
 * @copyright (C) 2003-2007 CMSformE, 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

if( !defined( 'ROOT' ) )
{
	exit;
}

/**
 * Assign DBMS-specific constants
 */
define( 'DB_ARRAY_NUM', MYSQLI_NUM );
define( 'DB_ARRAY_ASSOC', MYSQLI_ASSOC );
define( 'DB_ARRAY_BOTH', MYSQLI_BOTH );

/**
 * Database
 *
 * Class for manipulating the database
 *
 * @package DBAL
 */
class Database
{
	var $ident_link, $connected;
	var $query_count = 0;
	var $results = array();
	var $free_count = 0;
	var $errors = array();

	/**
	 * Database::__construct()
	 *
	 * Set up class with proper variables.
	 */
	function __construct( $server = 'localhost', $port = '3306', $username = 'root', $password = '', $database = 'dbox' )
	{
		$this->server = $server;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;

		$this->connect();

		$this->query( 'SET NAMES utf8' );
	}

	/**
	 * Database::__destruct()
	 *
	 * Ensure loose ends are tied up.
	 */
	function __destruct()
	{
		if( count( $this->results ) != $this->free_count )
		{
			print '<pre>' . "\n";
			print 'WARNING: NOT ALL RESULTS HAVE BEEN FREED!' . "\n";
			print 'RESULTS RETURNED: ' . count( $this->results ) . '; RESULTS FREED: ' . $this->free_count . "\n";
			var_dump( $this->error( TRUE ) );
			print '</pre>' . "\n";
		}

		if( $this->connected )
		{
			$this->disconnect();
		}
	}

	/**
	 * Database::verbose_name()
	 *
	 * Get verbose name of database
	 */
	function verbose_name()
	{
		return 'MySQL';
	}

	/**
	 * Database::connect()
	 *
	 * Connect to database
	 */
	function connect()
	{
		if( !$this->ident_link )
		{
			$connection = mysqli_connect( $this->server, $this->username, $this->password, $this->database, $this->port );

			if( $connection )
			{
				$this->ident_link = $connection;
				$this->connected = TRUE;
			}
			else
			{
				$this->connected = FALSE;

				return FALSE;
			}
		}

		return $this->ident_link;
	}

	/**
	 * Database::disconnect()
	 *
	 * Disconnect from database
	 */
	function disconnect()
	{
		if( $this->ident_link !== NULL )
		{
			$this->ident_link = NULL;
		}

		$this->connected = FALSE;

		return TRUE;
	}

	/**
	 * Database::query()
	 *
	 * Send query to database
	 */
	function query( $query )
	{
		if( !$this->connected )
		{
			$db = $this->connect();
		}
		else
		{
			$db = $this->ident_link;
		}

		if( !$db )
		{
			return FALSE;
		}
		else
		{
			if( is_array( $query ) )
			{
				foreach( $query as $single_query )
				{
						$queries[] = $single_query;
				}
			}
			else
			{
				$queries = array( $query );
			}

			$results = array();

			foreach( $queries as $query )
			{
				if( !empty( $query ) )
				{
					$result = $db->query( $query );

					$this->query_count++;

					if( !$result )
					{
						$this->error();
					}

					$results[$this->query_count] = $result;

					if( !is_bool( $result ) )
					{
						$this->results[$this->query_count] = $result;
					}
				}
			}

			return ( ( count( $results ) > 1 ) ? $results : $results[$this->query_count] );
		}
	}

	/**
	 * Database::num_rows()
	 *
	 * Count number of rows in result
	 */
	function num_rows( $result )
	{
		return mysqli_num_rows( $result );
	}

	/**
	 * Database::fetch_array()
	 *
	 * Fetch result row as array
	 */
	function fetch_array( $result, $array_type = DB_ARRAY_BOTH )
	{
		return mysqli_fetch_array( $result, $array_type );
	}

	/**
	 * Database::fetch_assoc()
	 *
	 * Fetch result row as associative array
	 */
	function fetch_assoc( $result )
	{
		return mysqli_fetch_assoc( $result );
	}

	/**
	 * Database::fetch_row()
	 *
	 * Fetch result row as enumerated array
	 */
	function fetch_row( $result )
	{
		return mysqli_fetch_row( $result );
	}

	/**
	 * Database::has_result()
	 *
	 * Determine if a result was returned.
	 */
	function has_result( $result )
	{
		if( $result && ( $this->num_rows( $result ) > 0 ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Database::reset_result()
	 *
	 * Reset result pointer to beginning of results.
	 */
	function reset_result( $result )
	{
		return mysqli_data_seek( $result, 0 );
	}

	/**
	 * Database::affected_rows()
	 *
	 * Get number of affected rows in previous operation
	 */
	function affected_rows()
	{
		return mysqli_affected_rows( $this->ident_link );
	}

	/**
	 * Database::escape()
	 *
	 * Escape string
	 */
	function escape( $string )
	{
		return mysqli_real_escape_string( $this->ident_link, $string );
	}

	/**
	 * Database::free_result()
	 *
	 * Free up memory
	 */
	function free_result( $result )
	{
		$this->free_count++;

		return @mysqli_free_result( $result );
	}

	/**
	 * Database::insert_id()
	 *
	 * Get last insert id
	 */
	function insert_id()
	{
		return @mysqli_insert_id( $this->ident_link );
	}

	/**
	 * Database::version()
	 *
	 * Get version number
	 */
	function version()
	{
		return mysqli_get_server_info( $this->ident_link );
	}

	/**
	 * Database::error()
	 *
	 * Report errors
	 */
	function error( $all_errors = FALSE, $query_number = FALSE )
	{
		$this->errors[$this->query_count]['code'] = mysqli_errno( $this->ident_link );
		$this->errors[$this->query_count]['message'] = mysqli_error( $this->ident_link );

		if( $all_errors )
		{
			return $this->errors;
		}
		elseif( $query_number )
		{
			return $this->errors[$query_number];
		}
		else
		{
			return $this->errors[$this->query_count];
		}
	}
}

?>