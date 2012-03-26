<?php

/**
 * dbox :: config.default.php
 *
 * Default configuration file.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 */

/**
 * Database connection information
 */
$config['db']['type'] = 'mysql';

$config['db']['server'] = 'localhost';
$config['db']['port'] = '3306';

$config['db']['username'] = 'root';
$config['db']['password'] = '';

$config['db']['database'] = 'dbox';

/**
 * Information about the server setup.
 */
$config['server']['domain'] = 'localhost';
$config['server']['path'] = '/';

/**
 * Global message, displayed on every page.
 */
$config['message'] = array(
	array(
		'type'		=>	'',
		'title'		=>	'',
		'message'	=>	''
	)
);

/**
 * Version information.
 */

define( 'DBOX_VERSION', '0.5-dev' );

?>