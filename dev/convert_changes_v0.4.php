<?php

/**
 * dbox :: dev/convert_changes.php
 *
 * Convert changes table to new table structure (v0.4).
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
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
$page_title = array( 'Development', 'Convert Changes (v0.4)' );
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
	$sql = 'SELECT c.*
		FROM changes c
		ORDER BY c.timestamp ASC';

	$result = $Database->query( $sql );

	if( !$Database->has_result( $result ) )
	{
		var_dump( $Database->error( TRUE ) );
	}

	while( $row = $Database->fetch_assoc( $result ) )
	{
		$sql2 = '';

		$user_id = (int) $row['user_id'];
		$item_id = (int) $row['item_id'];
		$new_value = $row['new_value'];
		$old_value = $row['old_value'];
		$timestamp = (int) $row['timestamp'];

		switch( $row['change_type'] )
		{
			case 'user_login':
				$item_type = 'user';
				$change_type = 'login';
				$item_id = $user_id;
			break;

			case 'user_logout':
				$item_type = 'user';
				$change_type = 'logout';
				$item_id = $user_id;
			break;

			case 'user_register':
				$item_type = 'user';
				$change_type = 'register';
				$item_id = $user_id;
			break;

			case 'add_record':
				$item_type = 'record';
				$change_type = 'add';
			break;

			case 'edit_record':
				$item_type = 'record';
				$change_type = 'edit';
			break;

			case 'add_morpheme':
				$item_type = 'morpheme';
				$change_type = 'add';
			break;

			case 'edit_morpheme':
				$item_type = 'morpheme';
				$change_type = 'edit';
			break;

			default:
				print_message( 'bad', '<code style="white-space: pre;">' . print_r( $row, TRUE ) . '</code>', 'Unknown change type' );
				continue 2;
			break;
		}

		$old_value = ( empty( $old_value ) ) ? 'NULL' : "'" . $Database->escape( $old_value ) . "'";
		$new_value = ( empty( $new_value ) ) ? 'NULL' : "'" . $Database->escape( $new_value ) . "'";

		$sql2 = "INSERT INTO changes_new ( timestamp, user_id, item_type, item_id, change_type, old_value, new_value )
			VALUES ( $timestamp, $user_id, '$item_type', $item_id, '$change_type', $old_value, $new_value )";

		$result2 = $Database->query( $sql2 );

		if( !$result2 )
		{
			var_dump( $Database->error( FALSE, $Database->query_count ) );
		}
		else
		{
			print_message( 'good', '<code style="white-space: pre;">' . print_r( $row, TRUE ) . '</code>', 'Change added' );
		}
	}

	$Database->free_result( $result );
}

/**
 * Include the style footer, required for proper page output.
 */
include( ROOT . 'style/footer.php' );

?>