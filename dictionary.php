<?php

/**
 * dbox :: dictionary.php
 *
 * Display and edit morphemes.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
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
$morpheme = ( exists( $_REQUEST['morpheme'] ) ) ? trim( $_REQUEST['morpheme'] ) : NULL;
$morpheme_id = ( exists( $_REQUEST['morpheme_id'] ) ) ? (int) $_REQUEST['morpheme_id'] : FALSE;

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

	case 'delete':
		$page_title[] = 'Delete Morpheme';
	break;

	case 'view':
	default:
		$page_title[] = 'View Dictionary';
	break;
}

// TODO: This works even in 'view' mode, which currently always displays the full dictionary.
if( $morpheme )
{
	$page_title[] = $morpheme;
}
// TODO: Get actual morpheme from morpheme ID.
elseif( $morpheme_id )
{
	$page_title[] = 'Morpheme ' . $morpheme_id;
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

	case 'delete':
		$dbox->delete_morpheme( $morpheme_id );
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