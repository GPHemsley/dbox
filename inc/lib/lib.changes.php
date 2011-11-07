<?php

/**
 * dbox :: inc/lib/lib.changes.php
 *
 * This contains a class to track changes.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Changes
 *
 * Class for tracking changes.
 *
 * @package dbox
 */
class Changes
{
	/**
	 * Changes::__construct()
	 *
	 *
	 *
	 */
	function __construct()
	{
	}

	/**
	 * Changes::track_change()
	 *
	 * Track a single change.
	 *
	 * @param $user_id int User ID
	 * @param $item_type string Item type
	 * @param $item_id int Item ID
	 * @param $change_type string Change type
	 * @param $new_value (string|null) New or only value [default: NULL]
	 * @param $old_value (string|null) Old value [default: NULL]
	 * @return boolean Success of tracking change
	 */
	function track_change( $user_id, $item_type, $item_id, $change_type, $new_value = NULL, $old_value = NULL )
	{
		global $Database;

		$user_id = (int) $user_id;
		$item_id = (int) $item_id;

		$old_value = ( $old_value === NULL ) ? 'NULL' : "'" . $Database->escape( $old_value ) . "'";
		$new_value = ( $new_value === NULL ) ? 'NULL' : "'" . $Database->escape( $new_value ) . "'";

		$sql = 'INSERT INTO changes ( timestamp, user_id, item_type, item_id, change_type, old_value, new_value )
			VALUES ( ' . time() . ", $user_id, '" . $Database->escape( $item_type ) . "', $item_id, '" . $Database->escape( $change_type ) . "', $old_value, $new_value )";

		if( $result = $Database->query( $sql ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function get_user_changes( $user_id, $limit = NULL )
	{
		global $Database;

		$limit_sql = ( !empty( $limit ) ) ? 'LIMIT ' . (int) $limit : '';

		$sql = 'SELECT c.*
			FROM changes c
			WHERE c.user_id = ' . (int) $user_id . '
			ORDER BY c.timestamp DESC
			' . $limit_sql;

		$result = $Database->query( $sql );

		$user_changes = array();

		while( $row = $Database->fetch_assoc( $result ) )
		{
			$user_changes[] = $row;
		}

		$Database->free_result( $result );

		return $user_changes;
	}
}

?>