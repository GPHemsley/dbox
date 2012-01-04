<?php

/**
 * dbox :: inc/lib/lib.home.php
 *
 * This contains all of the home functions.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 */

/**
 * Home
 *
 * Child class for tasks related to Home
 *
 * @package dbox
 */
class Home extends Base
{
	public function get_my_changes()
	{
		global $Database, $Changes, $User;

		$user_changes = $Changes->get_user_changes( $User->user_info['id'], 25 );

		print "\t<ul>\n";

		foreach( $user_changes as $user_change )
		{
			if( $user_change['item_type'] == 'user' )
			{
				continue;
			}

//			var_dump( $user_change );

			$old_value = @unserialize( $user_change['old_value'] );
			$new_value = @unserialize( $user_change['new_value'] );

			print "\t\t<li>";

			if( $user_change['change_type'] == 'add' )
			{
				switch( $user_change['item_type'] )
				{
					case 'record':
						print 'You added an elicitation entry for "' . htmlentities( $new_value['transcription'], ENT_QUOTES, 'UTF-8' ) . '" on ' . date( 'F j, Y', $user_change['timestamp'] ) . '.';
					break;

					case 'morpheme':
						print 'You added a dictionary entry for "' . htmlentities( $new_value['morpheme'], ENT_QUOTES, 'UTF-8' ) . '" on ' . date( 'F j, Y', $user_change['timestamp'] ) . '.';
					break;

					default:
						print 'You added a ' . $user_change['item_type'] . ' entry for "' . implode( ' ', $new_value ) . '" on ' . date( 'Y-m-d', $user_change['timestamp'] ) . '.';
					break;
				}
			}
			else
			{
				print 'You ' . $user_change['change_type'] . 'ed the ' . $user_change['item_type'] . ' entry for item ' . $user_change['item_id'] . ' on ' . date( 'F j, Y', $user_change['timestamp'] ) . '.';
			}

			print "</li>\n";
		}

		print "\t</ul>\n";
	}
}

?>