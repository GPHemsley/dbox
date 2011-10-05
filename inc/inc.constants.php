<?php

/**
 * dbox :: inc/inc.constants.php
 *
 * Catalog of values that are constant.
 *
 * @package dbox
 * @copyright (C) 2006-2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Date and time constants
 */
define( 'DAT_SECOND', 1 );
define( 'DAT_MINUTE', DAT_SECOND * 60 );
define( 'DAT_HOUR', DAT_MINUTE * 60 );
define( 'DAT_DAY', DAT_HOUR * 24 );
define( 'DAT_WEEK', DAT_DAY * 7 );
define( 'DAT_MONTH', DAT_DAY * 30 );
define( 'DAT_YEAR', DAT_DAY * 365 );
define( 'DAT_LEAP_YEAR', DAT_DAY * 366 );

/**
 * User constants
 */
define( 'USER_ANONYMOUS', -1 );

/**
 * User types
 */
define( 'UT_ANONYMOUS', -1 );
define( 'UT_STUDENT', 0 );
define( 'UT_INSTRUCTOR', 10 );
define( 'UT_ADMIN', 100 );

?>