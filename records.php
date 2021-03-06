<?php

/**
 * dbox :: records.php
 *
 * Display and edit records.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
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
$morpheme = ( exists( $_REQUEST['morpheme'] ) ) ? trim( $_REQUEST['morpheme'] ) : NULL;
$record_id = ( exists( $_REQUEST['record_id'] ) ) ? (int) $_REQUEST['record_id'] : FALSE;

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

if( $morpheme )
{
	$page_title[] = $morpheme;
}

// TODO: Get actual record from record ID.
if( $record_id )
{
	$page_title[] = 'Record ' . $record_id;
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