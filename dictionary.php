<?php

/**
 * dbox :: dictionary.php
 *
 * Display and edit morphemes.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Define the path to the root directory, relative to this file.
 */
define( 'ROOT', './' );
define( 'THIS_FILE', 'dictionary.php' );

/**
 * Specify the title of the page and the tab currently highlighted.
 */
$page_title = array( 'Dictionary' );
$tab = 'dictionary';

require( ROOT . 'inc/inc.main.php' );
require( ROOT . 'inc/lib/lib.dictionary.php' );

$dbox = new Dictionary();

$mode = ( exists( $_REQUEST['mode'] ) ) ? $_REQUEST['mode'] : 'view';
$morpheme_id = ( exists( $_REQUEST['morpheme_id'] ) ) ? (int) $_REQUEST['morpheme_id'] : FALSE;

/*if( $mode == 'export' )
{
	$dbox->export_records();

	exit;
}*/

/**
 * Have the title reflect the mode.
 */
switch( $mode )
{
	case 'add':
		$page_title[] = 'Add Morpheme';
	break;

	case 'edit':
		$page_title[] = 'Edit Morpheme';
	break;

	case 'view':
	default:
		$page_title[] = 'View Dictionary';
	break;
}

/**
 * Include the style header, required for proper page output.
 */
include( ROOT . 'style/header.php' );

$sub_nav = array(
	'view'	=>	array(
		'title'	=>	'View Dictionary',
		'url'	=>	ROOT . 'dictionary.php?mode=view'
	),
	'add'	=>	array(
		'title'	=>	'Add Morpheme',
		'url'	=>	ROOT . 'dictionary.php?mode=add'
	),
/*	'export'	=>	array(
		'title'	=>	'Export Records',
		'url'	=>	ROOT . 'dictionary.php?mode=export'
	),*/
);

?>
	<div id="dictionary">
<?php

$dbox->print_sub_navigation( $sub_nav );

switch( $mode )
{
	case 'add':
		$dbox->add_morpheme();
	break;

	case 'edit':
		$dbox->edit_morpheme( $morpheme_id );
	break;

	case 'view':
	default:
		$dbox->view_dictionary();
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