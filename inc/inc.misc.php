<?php

/**
 * dbox :: inc/inc.misc.php
 *
 * Miscellaneous functions.
 *
 * @package dbox
 * @copyright (C) 2006-2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * exists()
 *
 * Ensures that the given variable is set and not empty.
 *
 * @param mixed &$variable Variable to check
 * @return bool Existence of variable
 */
function exists( &$variable )
{
	return ( isset( $variable ) && !empty( $variable ) );
}

/**
 * does_not_exist()
 *
 * Ensures that the given variable is not set or is empty.
 *
 * @param mixed &$variable Variable to check
 * @return bool Non-existence of variable
 */
function does_not_exist( &$variable )
{
	return ( !isset( $variable ) || empty( $variable ) );
}

/**
 * print_message()
 *
 * Prints a message with special formatting.
 *
 * @param bool|string $type Type of message (values: good, bad, NULL)
 * @param string $message Message to be printed
 * @param string $title Title of message to be printed (default: '')
 * @param int $indent Number of tabs to indent
 * @return void Prints formatted message
 */
function print_message( $type, $message, $title = '', $indent = 1 )
{
	switch( $type )
	{
		case 'good':
			$type_class = ' good';
		break;

		case 'bad':
			$type_class = ' bad';
		break;

		case 'system':
			$type_class = ' system';
		break;

		case NULL:
		default:
			$type_class = '';
		break;
	}

	print str_repeat( "\t", $indent ) . '<p class="message' . $type_class . '">';

	if( $title )
	{
		print '<strong>' . $title . '</strong> ';
	}

	print $message . '</p>' . "\n";
}

?>