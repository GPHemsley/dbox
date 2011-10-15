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
	 * @param $change_type string Change type
	 * @param $user_id int User ID
	 * @param $item_id int Item ID
	 * @param $new_value string New or only value
	 * @param $old_value (string|null) Old value [default: NULL]
	 * @return boolean Success of tracking change
	 */
	function track_change( $change_type, $user_id, $item_id, $new_value, $old_value = NULL )
	{
		global $Database;

		$old_value = ( $old_value === NULL ) ? 'NULL' : "'" . $Database->escape( $old_value ) . "'";

		$sql = "INSERT INTO changes ( change_type, user_id, item_id, new_value, old_value, timestamp )
			VALUES ( '" . $Database->escape( $change_type ) . "', " . $Database->escape( $user_id ) . ", " . $Database->escape( $item_id ) . ", '" . $Database->escape( $new_value ) . "', $old_value, " . time() . " )";

		if( $result = $Database->query( $sql ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}

?>