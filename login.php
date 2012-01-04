<?php

/**
 * dbox :: login.php
 *
 * Login interface.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 */

/**
 * Define the path to the root directory, relative to this file.
 */
define( 'ROOT', './' );
define( 'THIS_FILE', 'login.php' );
define( 'OVERRIDE_LOGIN', TRUE );

/**
 * Specify the title of the page and the tab currently highlighted.
 */
$page_title = array( 'Login' );
$tab = 'login';

require( ROOT . 'inc/inc.main.php' );

/**
 * Include the style header, required for proper page output.
 */
include( ROOT . 'style/header.php' );

$dbox = new Base();

if( exists( $_REQUEST['logout'] ) )
{
	if( $Sessions->log_out() )
	{
		print_message( 'good', 'You may <a href="' . ROOT . 'index.php">return to the home page</a>.', 'Logout successful.' );
	}
	else
	{
		print_message( 'bad', 'That\'s weird. Try <a href="' . ROOT . 'index.php">returning to the home page</a>.', 'Logout failed.' );
	}
}
elseif( exists( $_POST['submit'] ) )
{
	$email_address = ( exists( $_POST['email_address'] ) ) ? $_POST['email_address'] : FALSE;
	$passphrase = ( exists( $_POST['passphrase'] ) ) ? $_POST['passphrase'] : FALSE;
	$secure = ( exists( $_POST['secure'] ) ) ? (bool) $_POST['secure'] : FALSE;

	if( $Sessions->log_in( $email_address, $passphrase, $secure ) )
	{
		// Login succeeded.
		$redirect = ( exists( $_REQUEST['redirect'] ) ) ? $_REQUEST['redirect'] : 'index.php';

		print_message( 'good', 'You may <a href="' . ROOT . htmlentities( $redirect, ENT_QUOTES, 'UTF-8' ) . '">proceed</a>.', 'Login successful.' );
	}
	else
	{
		// Login failed.
		print_message( 'bad', 'Please try again.', 'Login failed.' );

		$Sessions->print_login_form( $email_address, $passphrase, $secure );
	}
}
else
{
	$Sessions->print_login_form();
}

/**
 * Include the style footer, required for proper page output.
 */
include( ROOT . 'style/footer.php' );

?>