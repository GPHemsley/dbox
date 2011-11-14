<?php

/**
 * dbox :: records.php
 *
 * Display and edit records.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Define the path to the root directory, relative to this file.
 */
define( 'ROOT', './' );
define( 'THIS_FILE', 'records.php' );

/**
 * Specify the title of the page and the tab currently highlighted.
 */
$page_title = array( 'Records' );
$tab = 'records';

require( ROOT . 'inc/inc.main.php' );
require( ROOT . 'inc/lib/lib.records.php' );

$dbox = new Records();

$mode = ( exists( $_REQUEST['mode'] ) ) ? $_REQUEST['mode'] : 'view';
$record_id = ( exists( $_REQUEST['record_id'] ) ) ? (int) $_REQUEST['record_id'] : FALSE;

if( $mode == 'export' )
{
	$dbox->export_records();

	exit;
}

/**
 * Have the title reflect the mode.
 */
switch( $mode )
{
	case 'add':
		$page_title[] = 'Add Record';
	break;

	case 'edit':
		$page_title[] = 'Edit Record';
	break;

	case 'delete':
		$page_title[] = 'Delete Record';
	break;

	case 'view':
	default:
		$page_title[] = 'View Records';
	break;
}

/**
 * Include the style header, required for proper page output.
 */
include( ROOT . 'style/header.php' );

$sub_nav = array(
	'view'	=>	array(
		'title'	=>	'View Records',
		'url'	=>	ROOT . 'records.php?mode=view'
	),
	'add'	=>	array(
		'title'	=>	'Add Record',
		'url'	=>	ROOT . 'records.php?mode=add'
	),
	'export'	=>	array(
		'title'	=>	'Export Records',
		'url'	=>	ROOT . 'records.php?mode=export'
	),
);

?>
	<div id="records">
<?php

$dbox->print_sub_navigation( $sub_nav );

switch( $mode )
{
	case 'add':
		$dbox->add_record();
	break;

	case 'edit':
		$dbox->edit_record( $record_id );
	break;

	case 'delete':
		$dbox->delete_record( $record_id );
	break;

	case 'view':
	default:
		$dbox->view_records();
	break;
}

?>
	</div>
<?php

/**
 * Include the style footer, required for proper page output.
 */
include( ROOT . 'style/footer.php' );

?>