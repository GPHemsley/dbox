<?php

/**
 * dbox :: inc/inc.main.php
 *
 * Do the required stuff.
 *
 * @package dbox
 * @copyright (C) 2006-2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Set error reporting settings.
 */
error_reporting( E_ALL | E_STRICT );

$config = array();

/**
 * Get default settings.
 */
require( ROOT . 'config/config.default.php' );

/**
 * Get custom settings.
 */
if( file_exists( ROOT . 'config/config.custom.php' ) )
{
	require( ROOT . 'config/config.custom.php' );
}

require( ROOT . 'inc/inc.constants.php' );
require( ROOT . 'inc/inc.misc.php' );
require( ROOT . 'inc/database/database.' . $config['db']['type'] . '.php' );

$Database = new Database( $config['db']['server'], $config['db']['port'], $config['db']['username'], $config['db']['password'], $config['db']['database'] );

require( ROOT . 'inc/lib/lib.sessions.php' );
require( ROOT . 'inc/lib/lib.user.php' );

// Get this user a session!
$Sessions = new Sessions();

// Who is this user, anyway?
$User = new User( $Sessions->get_user() );

require( ROOT . 'inc/lib/lib.base.php' );

// If user is not logged in, override page and display login form.
// Just make sure that they're not already trying to log in.
if( ( $User->user_info['id'] === USER_ANONYMOUS ) && !( defined( 'OVERRIDE_LOGIN' ) && OVERRIDE_LOGIN ) )
{
	include( ROOT . 'style/header.php' );

	$dbox = new Base();

	$Sessions->print_login_form();

	include( ROOT . 'style/footer.php' );

	// Stop parsing of the page. We're done.
	exit;
}

// Get user data again, in case things have changed.
$User = new User( $Sessions->get_user() );

?>