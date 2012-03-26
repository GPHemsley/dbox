<?php

/**
 * dbox :: export.php
 *
 * Export data.
 *
 * @package dbox
 * @copyright (C) 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 */

/**
 * Define the path to the root directory, relative to this file.
 */
define( 'ROOT', './' );
define( 'THIS_FILE', 'export.php' );

/**
 * Specify the title of the page and the tab currently highlighted.
 */
$page_title = array( 'Export' );
$tab = 'export';

require( ROOT . 'inc/inc.main.php' );
require( ROOT . 'inc/lib/lib.export.php' );

$dbox = new Export();

$mode = ( exists( $_REQUEST['mode'] ) ) ? $_REQUEST['mode'] : 'options';
$format = ( exists( $_REQUEST['format'] ) ) ? $_REQUEST['format'] : 'txt';
$export = ( exists( $_REQUEST['export'] ) ) ? $_REQUEST['export'] : 'elicitations';

switch( $mode )
{
	case 'view':
		$dbox->export_data( $format, $export, FALSE );
	break;

	case 'download':
		$dbox->export_data( $format, $export, TRUE );
	break;

	case 'options':
	default:
		/**
		 * Have the title reflect the mode.
		 */
		$page_title[] = 'Export Options';

		/**
		 * Include the style header, required for proper page output.
		 */
		include( ROOT . 'style/header.php' );

		print "\t" . '<div id="export">' . "\n";

		$dbox->view_export_options();

		print "\t" . '</div>' . "\n";

		/**
		 * Include the style footer, required for proper page output.
		 */
		include( ROOT . 'style/footer.php' );
	break;
}

?>