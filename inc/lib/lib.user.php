<?php

/**
 * dbox :: inc/lib/lib.user.php
 *
 * This contains all of the user classes.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * User
 *
 * Base class for User
 *
 * @package dbox
 * @subpackage User
 */
class User
{
	/**
	 * User->user_info
	 *
	 * @var array $user_info Contains information about the current user
	 */
	var $user_info;

	/**
	 * User::__construct()
	 *
	 * Gets user information for the given user ID.
	 *
	 * @param int $user_id User ID for which to get information
	 * @return array User information
	 */
	function __construct( $user_id )
	{
		global $Database;
		global $config;

		$this->user_info = array(
			'id'					=>	USER_ANONYMOUS,
			'name'					=>	'Anonymous',
			'email'					=>	'null@localhost',
			'type'					=>	UT_ANONYMOUS
		);

		$sql = 'SELECT u.*
			FROM users u
			WHERE u.user_id = ' . (int) $user_id;

		$result = $Database->query( $sql );

		if( $Database->has_result( $result ) )
		{
			while( $user = $Database->fetch_assoc( $result ) )
			{
				$this->user_info['id'] = (int) $user['user_id'];
				$this->user_info['name'] = $user['name'];
				$this->user_info['email'] = $user['email_address'];
				$this->user_info['type'] = (int) $user['user_type'];
			}
		}

		$Database->free_result( $result );

		return $this->user_info;
	}

	/**
	 * User::_check_passphrase_strength()
	 *
	 * Checks whether the given passphrase is strong.
	 *
	 * @access protected
	 * @param string $passphrase Password to check
	 * @return bool Strength of passphrase
	 */
	protected function _check_passphrase_strength( $passphrase )
	{
		return (bool) preg_match( '/.{6,}/', $passphrase );
	}

	/**
	 * User::validate_email_address()
	 *
	 * Checks whether the given e-mail address is valid.
	 *
	 * @access protected
	 * @param string $email_address E-mail address to validate
	 * @return bool Validity of e-mail address
	 */
	protected function _validate_email_address( $email_address )
	{
		return (bool) preg_match( '/.+@(.+\.)+.+/', $email_address );
	}

	/**
	 * User::_check_email_address_availability()
	 *
	 * Checks whether the given e-mail address is already in use.
	 *
	 * @access protected
	 * @param string $email_address E-mail address to check
	 * @return bool Availability of e-mail address
	 */
	protected function _check_email_address_availability( $email_address )
	{
		global $Database;

		$sql = "SELECT u.email_address
			FROM users u
			WHERE u.email_address = '" . $Database->escape( $email_address ) . "'";

		$result = $Database->query( $sql );
		$email_address = $Database->fetch_assoc( $result );
		$Database->free_result( $result );

		if( !empty( $email_address ) )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * User::register_user()
	 *
	 * Creates a new user.
	 *
	 * @todo Add user type to the mix.
	 *
	 * @param string $name Name
	 * @param string $email_address E-mail address
	 * @param string $passphrase Password
	 * @param string $passphrase_confirm Confirm passphrase
	 * @return bool Was registration successful?
	 */
	function register_user( $name, $email_address, $passphrase, $passphrase_confirm )
	{
		global $dbox, $Database;

		$error = FALSE;

		// Make sure e-mail address is valid.
		if( empty( $email_address ) || !$this->_validate_email_address( $email_address ) )
		{
			print_message( 'bad', 'Please enter a valid e-mail address.', 'Invalid e-mail address.' );

			$error = TRUE;
		}

		// Make sure e-mail address is available.
		if( !empty( $email_address ) && !$this->_check_email_address_availability( $email_address ) )
		{
			print_message( 'bad', 'Sorry, that e-mail address is already in use. Please use a different e-mail address.', 'E-mail address in use.' );

			$error = TRUE;
		}

		// Make sure passphrases match.
		if( $passphrase !== $passphrase_confirm )
		{
			print_message( 'bad', 'Please ensure that both passphrases are the same.', 'Password mismatch.' );

			$error = TRUE;
		}

		// Make sure passphrase is strong enough.
		if( empty( $passphrase ) || !$this->_check_passphrase_strength( $passphrase ) )
		{
			print_message( 'bad', 'Please enter a stronger passphrase, with at least 6 characters. It is recommended that you use uppercase and lowercase letters, numbers, and symbols for the best security.', 'Weak passphrase.' );

			$error = TRUE;
		}

		// If there was an error, we can't continue.
		if( $error )
		{
			return FALSE;
		}

		$sql = "INSERT INTO users ( name, email_address, passphrase, registration_date, user_type )
			VALUES ( '" . $Database->escape( $name ) . "', '" . $Database->escape( $email_address ) . "', '" . sha1( $passphrase ) . "', " . time() . ', ' . UT_STUDENT . ' )';

		if( $result = $Database->query( $sql ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * User::print_registration_form()
	 *
	 * Prints user registration form.
	 *
	 * @param array $defaults Default values to pre-propagate the form
	 * @return void Prints user registration form
	 */
	function print_registration_form( $defaults = array() )
	{
		global $dbox;

		$form_data = array(
			array(
				'type'	=>	'header',
				'name'	=>	'registration-header',
				'label'	=>	'Register',
				'data'	=>	array(
					'level'		=>	2
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'name',
				'label'	=>	'Name',
				'data'	=>	array(
					'size'		=>	25,
					'maxlength'	=>	255,
					'value'		=>	@$defaults['name']
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'email_address',
				'label'	=>	'E-mail Address',
				'data'	=>	array(
					'size'		=>	30,
					'maxlength'	=>	320,
					'value'		=>	@$defaults['email_address']
				)
			),
			array(
				'type'	=>	'password',
				'name'	=>	'passphrase',
				'label'	=>	'Passphrase',
				'data'	=>	array(
					'size'		=>	25,
					'maxlength'	=>	255,
					'value'		=>	@$defaults['passphrase']
				)
			),
			array(
				'type'	=>	'password',
				'name'	=>	'passphrase_confirm',
				'label'	=>	'Confirm Passphrase',
				'data'	=>	array(
					'size'		=>	25,
					'maxlength'	=>	255,
					'value'		=>	@$defaults['passphrase_confirm']
				)
			),
			array(
				'type'	=>	'submit',
				'name'	=>	'submit',
				'data'	=>	array(
					'value'	=>	'Register'
				)
			)
		);

		print_message( NULL, 'dbox requires you to be a registered user in order to store your personal data. Please use the form below to register a new user.' );

		$dbox->create_form( 'registration-form', ROOT . 'register.php', $form_data );
	}
}

?>