<?php

/**
 * dbox :: register.php
 *
 * Registration interface.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Define the path to the root directory, relative to this file.
 */
define( 'ROOT', './' );
define( 'THIS_FILE', 'register.php' );
define( 'OVERRIDE_LOGIN', TRUE );

/**
 * Specify the title of the page and the tab currently highlighted.
 */
$page_title = array( 'Register' );
$tab = 'register';

require( ROOT . 'inc/inc.main.php' );

/**
 * Include the style header, required for proper page output.
 */
include( ROOT . 'style/header.php' );

$dbox = new Base();

if( exists( $_POST['submit'] ) )
{
	if( $User->register_user( $_POST['name'], $_POST['email_address'], $_POST['passphrase'], $_POST['passphrase_confirm'] ) )
	{
		// Registration succeeded.
		print_message( 'good', 'You may now <a href="' . ROOT . 'login.php">log in</a>.', 'Registration successful.' );
	}
	else
	{
		// Registration failed.
		print_message( 'bad', 'Please note any errors above and try again.', 'Registration failed.' );

		$User->print_registration_form( $_POST );
	}
}
else
{
	$User->print_registration_form();
}

/**
 * Include the style footer, required for proper page output.
 */
include( ROOT . 'style/footer.php' );

?>