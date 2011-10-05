<?php

/**
 * dbox :: index.php
 *
 * Home page, where the current tasks are shown.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Define the path to the root directory, relative to this file.
 */
define( 'ROOT', './' );
define( 'THIS_FILE', 'index.php' );

/**
 * Specify the title of the page and the tab currently highlighted.
 */
$page_title = array( 'Home' );
$tab = 'home';

require( ROOT . 'inc/inc.main.php' );

/**
 * Include the style header, required for proper page output.
 */
include( ROOT . 'style/header.php' );

require( ROOT . 'inc/lib/lib.home.php' );

$dbox = new Home();

?>

<?php

/**
 * Include the style footer, required for proper page output.
 */
include( ROOT . 'style/footer.php' );

?>