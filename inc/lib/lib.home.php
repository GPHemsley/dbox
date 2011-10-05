<?php

/**
 * dbox :: inc/lib/lib.home.php
 *
 * This contains all of the home functions.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
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
	function __construct()
	{
		global $Changes, $User;

		$Changes->track_change( 'test_home', $User->user_info['id'], -1, 'Test', NULL );
	}
}

?>