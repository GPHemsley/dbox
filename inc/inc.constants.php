<?php

/**
 * dbox :: inc/inc.constants.php
 *
 * Catalog of values that are constant.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
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

/***
 * Forms
 */
define( 'YES', 1 );
define( 'NO', 0 );

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

/***
 * Changes
 */
define( 'NO_ITEM_ID', 0 );

/**
 * Grammaticality judgements
 */
define( 'GR_VAL_GOOD', 2 );
define( 'GR_VAL_OKAY', 1 );
define( 'GR_VAL_MARGINAL', 0 );
define( 'GR_VAL_NOTSOBAD', -1 );
define( 'GR_VAL_BAD', -2 );

define( 'GR_SYM_GOOD', '' );
define( 'GR_SYM_OKAY', '?' );
define( 'GR_SYM_MARGINAL', '%' );
define( 'GR_SYM_NOTSOBAD', '*?' );
define( 'GR_SYM_BAD', '*' );

?>