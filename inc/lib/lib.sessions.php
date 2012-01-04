<?php

/**
 * dbox :: inc/lib/lib.sessions.php
 *
 * This contains all of the sessions classes.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 */

/**
 * Sessions
 *
 * Base class for Sessions
 *
 * @todo Standardize whether $secure variables are int or bool.
 *
 * @package dbox
 * @subpackage Sessions
 */
class Sessions
{
	/**
	 * Sessions->logged_in
	 *
	 * @access protected
	 * @var bool $logged_in Whether the user is logged in (default: FALSE)
	 */
	protected $logged_in = FALSE;
	/**
	 * Sessions->user_id
	 *
	 * @access protected
	 * @var int $user_id The user ID for the current user (default: USER_ANONYMOUS)
	 */
	protected $user_id = USER_ANONYMOUS;

	/**
	 * Sessions::__construct()
	 *
	 * Acts like a cron job, running preliminary housekeeping before beginning a session.
	 */
	function __construct()
	{
		$this->expire_sessions();
		$this->get_session();
	}

	/**
	 * Sessions::generate_hash()
	 *
	 * Generate a session hash based on the given user ID.
	 *
	 * @access protected
	 * @param int $user_id User ID to generate hash for (default: USER_ANONYMOUS)
	 * @return string Session hash
	 */
	protected function generate_hash( $user_id = USER_ANONYMOUS )
	{
		global $Database;

		$hash_pieces = array();

		// Randomly decide what will come first in the hash array.
		$first = rand( 0, 1 );

		$sql = 'SELECT email_address
			FROM users
			WHERE user_id = ' . (int) $user_id;

		$result = $Database->query( $sql );

		if( !$Database->has_result( $result ) )
		{
			$Database->free_result( $result );

			return FALSE;
		}

		while( $row = $Database->fetch_assoc( $result ) )
		{
			// Compile the hash array using the e-mail address and a random number.
			// This method could easily be changed without affecting existing hashes.
			$hash_pieces[$first] = $row['email_address'];
			$hash_pieces[1 - $first] = rand();
		}

		$Database->free_result( $result );

		return sha1( implode( $hash_pieces ) );
	}

	/**
	 * Sessions::validate_hash()
	 *
	 * Ensure the given string is a valid 40-character hash.
	 *
	 * @access protected
	 * @param string Session hash to validate
	 * @return bool Validity of session hash
	 */
	final protected function validate_hash( $session_hash )
	{
		return (bool) preg_match( '/[a-z0-9]{40}/', $session_hash );
	}

	/**
	 * Sessions::create_session()
	 *
	 * Create a new session.
	 *
	 * @access protected
	 * @param int $user_id User ID to create session for (default: USER_ANONYMOUS)
	 * @param int $secure Whether the new session is a secure session (default: 0)
	 * @return string Session hash of new session
	 */
	final protected function create_session( $user_id = USER_ANONYMOUS, $secure = 0 )
	{
		global $Database;
		global $config;

		// A secure session requires expiration of older, existing sessions.
		if( $secure )
		{
			$this->expire_sessions( $user_id );
		}

		$session_hash = $this->generate_hash();

		// Check to make sure we actually got a good hash.
		if( empty( $session_hash ) || !$this->validate_hash( $session_hash ) )
		{
			// If we don't have a good hash, try again.
			$session_hash = $this->generate_hash();

			// If we still don't have a good hash, don't attempt to create the session.
			if( empty( $session_hash ) || !$this->validate_hash( $session_hash ) )
			{
				return FALSE;
			}
		}

		$time_now = time();
		$time_then = ( $user_id <= USER_ANONYMOUS ) ? $time_now + DAT_WEEK : $time_now + DAT_MONTH;

		$sql = "INSERT INTO sessions ( session_hash, user_id, creation_date, expiration_date, secure )
			VALUES ( '$session_hash', $user_id, $time_now, $time_then, $secure )";

		if( $result = $Database->query( $sql ) )
		{
//			@setcookie( 'dbox_session_hash', $session_hash, $time_then, $config['server']['path'], $config['server']['domain'], FALSE, TRUE );
			@setcookie( 'dbox_session_hash', $session_hash, $time_then, $config['server']['path'], '', FALSE, TRUE );

			$this->user_id = $user_id;
			$this->logged_in = ( $user_id > USER_ANONYMOUS ) ? TRUE : FALSE;

			return $session_hash;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Sessions::update_session()
	 *
	 * Update a given session, usually to change the user ID associated with it.
	 *
	 * @todo Allow update of secure session choice.
	 *
	 * @access protected
	 * @param string $session_hash Session hash to update
	 * @param int $user_id User ID to update session for
	 * @param bool $secure Whether the new session is a secure session (default: FALSE)
	 * @return bool Success of session update
	 */
	final protected function update_session( $session_hash, $user_id, $secure = FALSE )
	{
		global $Database;
		global $config;

		if( !$this->validate_hash( $session_hash ) )
		{
			return FALSE;
		}

		// A secure session requires expiration of older, existing sessions.
		if( $secure )
		{
			$this->expire_sessions( $user_id );
		}

		$time_now = time();
		$time_then = ( $user_id <= USER_ANONYMOUS ) ? $time_now + DAT_WEEK : $time_now + DAT_MONTH;

		$sql = "UPDATE sessions
			SET user_id = $user_id, expiration_date = $time_then
			WHERE session_hash = '$session_hash'";

		if( $result = $Database->query( $sql ) )
		{
//			@setcookie( 'dbox_session_hash', $session_hash, $time_then, $config['server']['path'], $config['server']['domain'], FALSE, TRUE );
			@setcookie( 'dbox_session_hash', $session_hash, $time_then, $config['server']['path'], '', FALSE, TRUE );

			$this->user_id = $user_id;
			$this->logged_in = ( $user_id > USER_ANONYMOUS ) ? TRUE : FALSE;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Sessions::get_session()
	 *
	 * Get the current session. If one does not exist, create a new one.
	 *
	 * @access protected
	 * @return string Session hash of current session
	 */
	final protected function get_session()
	{
		global $Database;

		// Check for a valid session hash in a cookie, or else create a new one.
		if( exists( $_COOKIE['dbox_session_hash'] ) && $this->validate_hash( $_COOKIE['dbox_session_hash'] ) )
		{
			$session_hash = $_COOKIE['dbox_session_hash'];
		}
		else
		{
			$session_hash = $this->create_session();
		}

		$sql = "SELECT user_id
			FROM sessions
			WHERE session_hash = '$session_hash'";

		$result = $Database->query( $sql );

		if( !$Database->has_result( $result ) )
		{
			$this->user_id = USER_ANONYMOUS;
			$this->logged_in = FALSE;

			$Database->free_result( $result );

			return $this->create_session();
		}

		while( $row = $Database->fetch_assoc( $result ) )
		{
			$this->user_id = (int) $row['user_id'];
			$this->logged_in = ( $row['user_id'] > USER_ANONYMOUS ) ? TRUE : FALSE;
		}

		$Database->free_result( $result );

		return $session_hash;
	}

	/**
	 * Sessions::expire_sessions()
	 *
	 * Expire old sessions.
	 *
	 * @access public
	 * @param int|bool Only expire sessions for given user ID (default: FALSE)
	 * @return bool Success of session expiration
	 */
	public function expire_sessions( $user_id = FALSE )
	{
		global $Database;

		// Anonymous users have no right to expire other anonymous users.
		if( $user_id === USER_ANONYMOUS )
		{
			return FALSE;
		}

		$time_now = time();

		if( $user_id )
		{
			// Delete all sessions for the given user.
			$where_sql = 'user_id = ' . $user_id;
		}
		else
		{
			// Delete sessions that are expired or really old.
			$where_sql = 'expiration_date < ' . $time_now . '
				OR ( creation_date + ' . DAT_YEAR . ' ) < ' . $time_now;
		}

		$sql = 'DELETE FROM sessions
			WHERE ' . $where_sql;

		if( $result = $Database->query( $sql ) )
		{
			// TODO: Apparently this doesn't work on MEMORY tables. Unneeded?
			//$Database->query( 'OPTIMIZE TABLE sessions' );

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Sessions::get_user()
	 *
	 * Get the user ID of the current user.
	 *
	 * @access public
	 * @return int User ID of current user.
	 */
	public function get_user()
	{
		return $this->user_id;
	}

	/**
	 * Sessions::log_in()
	 *
	 * Perform the required processing to consider the user logged in.
	 *
	 * @access public
	 * @param string $email_address E-mail address of user attempting to log in
	 * @param string $passphrase Passphrase of user attempting to log in
	 * @param int|bool $secure Log in using a secure session
	 * @return bool Success of login
	 */
	public function log_in( $email_address, $passphrase, $secure )
	{
		global $dbox;
		global $Database, $Changes;

		$sql = "SELECT user_id, email_address, passphrase
			FROM users
			WHERE email_address = '" . $Database->escape( strtolower( $email_address ) ) . "'";

		$result = $Database->query( $sql );

		if( !$Database->has_result( $result ) )
		{
			$Database->free_result( $result );

			return FALSE;
		}

		while( $row = $Database->fetch_assoc( $result ) )
		{
			// Hash of given passphrase must match passphrase hash in user table.
			if( sha1( $passphrase ) === $row['passphrase'] )
			{
				// The current user's session must be updated to match their new user ID.
				if( $this->update_session( $this->get_session(), (int) $row['user_id'], (bool) $secure ) )
				{
					$Changes->track_change( $this->user_id, 'user', $this->user_id, 'login' );

					$this->logged_in = TRUE;
				}
			}
		}

		$Database->free_result( $result );

		return $this->logged_in;
	}

	/**
	 * Sessions::log_out()
	 *
	 * Perform the required processing to consider the user logged out.
	 *
	 * @access public
	 * @return bool Success of logout
	 */
	public function log_out()
	{
		global $Changes;

		$old_user_id = $this->user_id;

		// Reset current session to belong to an anonymous user.
		if( $this->update_session( $this->get_session(), USER_ANONYMOUS ) )
		{
			$Changes->track_change( $old_user_id, 'user', $old_user_id, 'logout' );

			$this->logged_in = FALSE;
			$this->user_id = USER_ANONYMOUS;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Sessions::print_login_form()
	 *
	 * Perform the required processing to consider the user logged in.
	 *
	 * @access public
	 * @param string|bool $email_address Previous value of 'email_address' field (default: FALSE)
	 * @param string|bool $passphrase Previous value of 'passphrase' field (default: FALSE)
	 * @param int|bool $secure Previous value of 'secure' field (default: FALSE)
	 * @return void Prints login form
	 */
	public function print_login_form( $email_address = FALSE, $passphrase = FALSE, $secure = FALSE )
	{
		global $dbox;

		$redirect = '';

		// Ensure that we (safely) direct the user back to whatever page they were on.
		// But only if they're attempting to use a protected page.
		// Otherwise, they probably don't want to go back there.
		if( exists( $_REQUEST['redirect'] ) && ( substr( $_REQUEST['redirect'], 0, 1 ) != '.' ) )
		{
			$redirect = '?redirect=' . $_REQUEST['redirect'];
		}
		elseif( defined( 'THIS_FILE' ) && !defined( 'OVERRIDE_LOGIN' ) )
		{
			$redirect = '?redirect=' . THIS_FILE;
		}

		$form_data = array(
			array(
				'type'	=>	'header',
				'name'	=>	'login-header',
				'label'	=>	'Log In',
				'data'	=>	array(
					'level'		=>	2
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'email_address',
				'label'	=>	'E-mail Address',
				'data'	=>	array(
					'size'		=>	40,
					'maxlength'	=>	320,
					'autofocus'	=>	TRUE,
					'value'		=>	$email_address
				)
			),
			array(
				'type'	=>	'password',
				'name'	=>	'passphrase',
				'label'	=>	'Passphrase',
				'data'	=>	array(
					'size'		=>	40,
					'maxlength'	=>	255,
					'value'		=>	$passphrase
				)
			),
			array(
				'type'	=>	'submit',
				'name'	=>	'submit',
				'data'	=>	array(
					'value'	=>	'Log In'
				)
			)
		);

		print_message( NULL, 'dbox requires you to be authenticated in order to access your personal data. Please use the form below to log in. Cookies are required beyond this point.' );

		$dbox->create_form( 'login-form', ROOT . 'login.php' . htmlentities( $redirect, ENT_QUOTES, 'UTF-8' ), $form_data );
	}

	/**
	 * Sessions::is_logged_in()
	 *
	 * Get logged-in status of the current user.
	 *
	 * @access public
	 * @return bool Is the user logged in?
	 */
	public function is_logged_in()
	{
		return $this->logged_in;
	}
}

?>