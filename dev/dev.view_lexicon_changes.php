<?php

/**
 * dbox :: dev/dev.view_lexicon_changes.php
 *
 * View changes to the lexicon.
 *
 * @package dbox
 * @copyright (C) 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 */

/**
 * Change this to TRUE to use the tool.
 */
define( 'DEV_TOOL_ON', FALSE );

/**
 * Define the path to the root directory, relative to this file.
 */
define( 'ROOT', '../' );
define( 'THIS_FILE', 'index.php' );

/**
 * Specify the title of the page and the tab currently highlighted.
 */
$page_title = array( 'Development', 'View Lexicon Changes' );
$tab = 'dev';

require( ROOT . 'inc/inc.main.php' );

/**
 * Include the style header, required for proper page output.
 */
include( ROOT . 'style/header.php' );

$dbox = new Base();

if( !DEV_TOOL_ON )
{
	print_message( 'bad', 'You must change <code>DEV_TOOL_ON</code> to <code>TRUE</code> in order to use this tool.' );
}
else
{
	$columns[0] = array(
		'style'	=>	'width: 5%;'
	);
	$headers[0] = array(
		'content'	=>	'No.'
	);

	$columns[1] = array(
		'style'	=>	'width: 5%;'
	);
	$headers[1] = array(
		'content'	=>	'Type'
	);

	$columns[2] = array(
			'style'	=>	'width: 37.5%;'
	);
	$headers[2] = array(
		'content'	=>	'Old Value'
	);

	$columns[3] = array(
		'style'	=>	'width: 37.5%;'
	);
	$headers[3] = array(
		'content'	=>	'New Value'
	);

	$columns[4] = array(
		'style'	=>	'width: 15%;'
	);
	$headers[4] = array(
		'content'	=>	'User'
	);

	$sql = "SELECT c.*, u.name
		FROM changes c
		LEFT JOIN ( users u )
			ON ( c.user_id = u.user_id )
		WHERE c.item_type = 'morpheme'
		ORDER BY c.timestamp DESC";

	$result = $Database->query( $sql );

	if( !$Database->has_result( $result ) )
	{
		$rows[] = array(
			'content'	=>	array(
				0	=>	array(
					'colspan'	=>	count( $headers ),
					'content'	=>	'<strong>No records returned.</strong>'
				)
			)
		);
	}

	while( $row = $Database->fetch_assoc( $result ) )
	{
		$old_value = unserialize( $row['old_value'] );
		$new_value = unserialize( $row['new_value'] );

		$rows[] = array(
			'content'	=>	array(
				0	=>	array(
					'content'	=>	'(' . $row['item_id'] . ')'
				),
				1	=>	array(
					'content'	=>	$row['change_type']
				),
				2	=>	array(
					'content'	=>	'<span class="transcription"><a href="' . ROOT . 'records.php?mode=view&amp;morpheme=' . htmlentities( $old_value['morpheme'], ENT_QUOTES, 'UTF-8' ) . '">' . $dbox->convert_newlines( htmlentities( $old_value['morpheme'], ENT_QUOTES, 'UTF-8' ) ) . '</a></span><br /><span class="gloss">' . $dbox->convert_newlines( htmlentities( $old_value['gloss'], ENT_QUOTES, 'UTF-8' ) ) . '</span><p>' . $dbox->convert_newlines( htmlentities( $old_value['comments'], ENT_QUOTES, 'UTF-8' ) ) . '</p>'
				),
				3	=>	array(
					'content'	=>	'<span class="transcription"><a href="' . ROOT . 'records.php?mode=view&amp;morpheme=' . htmlentities( $new_value['morpheme'], ENT_QUOTES, 'UTF-8' ) . '">' . $dbox->convert_newlines( htmlentities( $new_value['morpheme'], ENT_QUOTES, 'UTF-8' ) ) . '</a></span><br /><span class="gloss">' . $dbox->convert_newlines( htmlentities( $new_value['gloss'], ENT_QUOTES, 'UTF-8' ) ) . '</span><p>' . $dbox->convert_newlines( htmlentities( $new_value['comments'], ENT_QUOTES, 'UTF-8' ) ) . '</p>'
				),
				4	=>	array(
					'content'	=>	'<a href="' . ROOT . 'records.php?mode=view&amp;creator_id=' . $row['user_id'] . '" class="creator">' . htmlentities( $row['name'], ENT_QUOTES, 'UTF-8' ) . '</a><br /><span class="creation-time">' . $dbox->format_date( $row['timestamp'], 'F j, Y' ) . '</span>'
				),
			)
		);
	}

	$Database->free_result( $result );

	$dbox->print_list_table( ROOT . 'dev/view_lexicon_changes.php', $columns, $headers, $rows );
}

/**
 * Include the style footer, required for proper page output.
 */
include( ROOT . 'style/footer.php' );

?>