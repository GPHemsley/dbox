<?php

/**
 * dbox :: inc/lib/lib.base.php
 *
 * This contains the functions common to all child classes of dbox.
 *
 * @package dbox
 * @copyright (C) 2006-2010 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 * @version $Id: lib.base.php 93 2011-10-04 19:51:20Z gphemsley $
 */

/**
 * Base
 *
 * Base class for dbox
 *
 * @todo Consolidate Base::_create_*_type_selector() functions
 *
 * @package Base
 */
class Base
{
	/**
	 * Base::__construct()
	 *
	 * Sets up dbox class for use.
	 *
	 * @access public
	 * @param int $user User id
	 * @return void Initializes dbox class
	 */
	public function __construct()
	{
		global $Database, $User;
		global $config;

		// Ensure that forms can be properly generated.
		require_once( ROOT . 'inc/lib/lib.forms.php' );
	}

	/**
	 * Base::convert_newlines()
	 *
	 * Convert newline characters to XHTML line breaks.
	 *
	 * @access public
	 * @param string $text Text with newline characters
	 * @return string Text with XHTML line breaks
	 */
	public function convert_newlines( $text )
	{
		$text = str_replace( array( "\r\n", "\n\r", "\r" ), "\n", $text );
		$text = str_replace( "\n", "<br />", $text );

		return $text;
	}

	/**
	 * Base::convert_hour()
	 *
	 * Convert between 12-hour and 24-hour format.
	 *
	 * @access public
	 * @param bool $to_24 Convert to/from 24-hour time (to: TRUE; from: FALSE)
	 * @param int $hour Hour to be converted
	 * @param array $time_parts Array containing time information (default: array())
	 * @return array Array containing time information
	 */
	final public function convert_hour( $to_24, $hour, $time_parts = array() )
	{
		if( $to_24 )
		{
			if( isset( $time_parts['meridiem'] ) && ( $time_parts['meridiem'] == 'PM' ) )
			{
				if( $hour == 12 )
				{
					$time_parts['hour'] = '12';
				}
				else
				{
					$time_parts['hour'] = (string) ( $hour + 12 );
				}

				$time_parts['meridiem'] = FALSE;
			}
			elseif( isset( $time_parts['meridiem'] ) && ( $time_parts['meridiem'] == 'AM' ) )
			{
				if( $hour == 12 )
				{
					$time_parts['hour'] = '00';
				}
				else
				{
					$time_parts['hour'] = str_pad( $hour, 2, '0', STR_PAD_LEFT );
				}

				$time_parts['meridiem'] = FALSE;
			}
			else
			{
				// Assume $hour is 24-hour format
				$time_parts['hour'] = str_pad( $hour, 2, '0', STR_PAD_LEFT );
				$time_parts['meridiem'] = FALSE;
			}
		}
		else
		{
			if( isset( $time_parts['meridiem'] ) && ( $time_parts['meridiem'] != FALSE ) )
			{
				// $hour is already in 12-hour format
				$time_parts['hour'] = str_pad( $hour, 2, '0', STR_PAD_LEFT );
			}
			else
			{
				if( ( $hour == 0 ) || ( $hour == 24 ) )
				{
					$time_parts['hour'] = '12';
					$time_parts['meridiem'] = 'AM';
				}
				elseif( $hour == 12 )
				{
					$time_parts['hour'] = '12';
					$time_parts['meridiem'] = 'PM';
				}
				elseif( $hour > 12 )
				{
					$time_parts['hour'] = (string) ( $hour - 12 );
					$time_parts['meridiem'] = 'PM';
				}
				else
				{
					$time_parts['hour'] = (string) $hour;
					$time_parts['meridiem'] = 'AM';
				}
			}
		}

		return $time_parts;
	}

	/**
	 * Base::format_date()
	 *
	 * Format date. Mostly just a surrogate for PHP's date().
	 *
	 * @access public
	 * @param int $date UNIX timestamp
	 * @param string $format Date format to use (default: 'F j, Y')
	 * @return string Formatted date
	 */
	final public function format_date( $date, $format = 'F j, Y' )
	{
		return date( $format, $date );
	}

	/**
	 * Base::format_time()
	 *
	 * Format time according to given input and output settings.
	 *
	 * @access public
	 * @param mixed $time Time to format
	 * @param bool $twelve_hour Use AM/PM format
	 * @param bool|string $input Input type (possible values: array, timestamp, 24-hour)
	 * @param string $output Output type (possible values: hour, 24-hour, text, html; default: html)
	 * @return string Formatted time
	 */
	final public function format_time( $time, $twelve_hour = TRUE, $input = FALSE, $output = 'html' )
	{
		$time_parts = array();

		if( !$input )
		{
			if( is_array( $time ) && ( count( $time ) == 3 ) )
			{
				$input = 'array';
			}
			elseif( (int) $time )
			{
				if( @strlen( $time ) == 4 )
				{
					$input = '24-hour';
				}
				else
				{
					$input = 'timestamp';
				}
			}
		}

		switch( $input )
		{
			case 'array':
				$time_parts['hour'] = str_pad( $time['hour'], 2, '0', STR_PAD_LEFT );
				$time_parts['minute'] = str_pad( $time['minute'], 2, '0', STR_PAD_LEFT );
				$time_parts['meridiem'] = $time['meridiem'];
			break;

			case 'timestamp':
				$time_parts['hour'] = ( ( $twelve_hour ) ? date( 'h', $time ) : date( 'H', $time ) );
				$time_parts['minute'] = date( 'i', $time );
				$time_parts['meridiem'] = ( ( $twelve_hour ) ? date( 'A', $time ) : FALSE );
			break;

			case '24-hour':
			default:
				$time_split = str_split( str_pad( $time, 4, '0', STR_PAD_LEFT ), 2 );

				$time_parts['hour'] = $time_split[0];
				$time_parts['minute'] = $time_split[1];
				$time_parts['meridiem'] = FALSE;

				if( $twelve_hour )
				{
					$time_parts = $this->convert_hour( FALSE, (int) $time_split[0], $time_parts );
				}
			break;
		}

		switch( $output )
		{
			case 'hour':
				return $this->convert_hour( TRUE, $time_parts['hour'], $time_parts );
			break;

			case '24-hour':
				if( $time_parts['meridiem'] )
				{
					if( $time_parts['meridiem'] == 'PM' )
					{
						$time_parts['meridiem'] = FALSE;

						if( $time_parts['hour'] < 12 )
						{
							$time_parts['hour'] += 12;
						}
					}
					elseif( $time_parts['hour'] == 12 )
					{
						$time_parts['hour'] = '00';
					}
				}

				return $time_parts['hour'] . $time_parts['minute'];
			break;

			case 'text':
				if( $time_parts['meridiem'] )
				{
					return $time_parts['hour'] . ':' . $time_parts['minute'] . ' ' . $time_parts['meridiem'];
				}
				else
				{
					return $time_parts['hour'] . ':' . $time_parts['minute'];
				}
			break;

			case 'html':
			default:
				if( $time_parts['meridiem'] )
				{
					return $time_parts['hour'] . ':' . $time_parts['minute'] . '&nbsp;' . $time_parts['meridiem'];
				}
				else
				{
					return $time_parts['hour'] . ':' . $time_parts['minute'];
				}
			break;
		}
	}

	/**
	 * Base::print_sub_navigation()
	 *
	 * Print page-specific navigation.
	 *
	 * @access public
	 * @param array $sub_nav Navigation link data.
	 * @return bool Status of printing sub-navigation.
	 */
	public function print_sub_navigation( $sub_nav )
	{
		if( empty( $sub_nav ) || !is_array( $sub_nav ) )
		{
			return FALSE;
		}

		print "\t\t" . '<div id="sub-nav">' . "\n";
		print "\t\t\t" . '<ul>' . "\n";

		foreach( $sub_nav as $link_id => $link )
		{
			print "\t\t\t\t" . '<li><a href="' . htmlentities( $link['url'], ENT_QUOTES, 'UTF-8' ) . '">' . htmlentities( $link['title'], ENT_QUOTES, 'UTF-8' ) . '</a></li>' . "\n";
		}

		print "\t\t\t" . '</ul>' . "\n";
		print "\t\t" . '</div>' . "\n";

		return TRUE;
	}

	/**
	 * Base::print_list_table()
	 *
	 * Print table for lists.
	 *
	 * @access public
	 * @param string $nav_file Path to use for navigation
	 * @param array $columns Data for column structure
	 * @param array $headers Data for headers and footers
	 * @param array $rows Data for rows and cells
	 * @return bool Status of printing list table
	 */
	public function print_list_table( $nav_file, $columns, $headers, $rows )
	{
		print "\t\t" . '<table class="list">' . "\n";

		print "\t\t\t" . '<colgroup>' . "\n";

		foreach( $columns as $col )
		{
			print "\t\t\t\t" . '<col';

			if( exists( $col['span'] ) )
			{
				print ' span="' . $col['span'] . '"';
			}

			if( exists( $col['class'] ) )
			{
				print ' class="' . $col['class'] . '"';
			}

			if( exists( $col['style'] ) )
			{
				print ' style="' . $col['style'] . '"';
			}

			print ' />' . "\n";
		}

		print "\t\t\t" . '</colgroup>' . "\n";

		for( $top = 1; $top >= 0; $top-- )
		{
			if( $top )
			{
				print "\t\t\t" . '<thead style="border-bottom: 3px solid black;">' . "\n";
			}
			else
			{
				print "\t\t\t" . '<tfoot style="border-top: 3px solid black;">' . "\n";
			}

			print "\t\t\t\t" . '<tr>' . "\n";

			foreach( $headers as $head )
			{
				if( does_not_exist( $head['content'] ) )
				{
					$head['content'] = '&nbsp;';
				}

				print "\t\t\t\t\t" . '<th';

				if( exists( $head['colspan'] ) )
				{
					print ' colspan="' . $head['colspan'] . '"';
				}

				if( exists( $head['class'] ) )
				{
					print ' class="' . $head['class'] . '"';
				}

				if( exists( $head['style'] ) )
				{
					print ' style="' . $head['style'] . '"';
				}

				print '>' . $head['content'] . '</th>' . "\n";
			}

			print "\t\t\t\t" . '</tr>' . "\n";

			if( $top )
			{
				print "\t\t\t" . '</thead>' . "\n";
			}
			else
			{
				print "\t\t\t" . '</tfoot>' . "\n";
			}
		}

		print "\t\t\t" . '<tbody>' . "\n";

		foreach( $rows as $row_id => $row )
		{
			print "\t\t\t\t" . '<tr';

			if( exists( $row['class'] ) )
			{
				print ' class="' . $row['class'] . '"';
			}

			if( exists( $row['style'] ) )
			{
				print ' style="' . $row['style'] . '"';
			}

			print '>' . "\n";

			foreach( $row['content'] as $cell )
			{
				if( empty( $cell ) )
				{
					continue;
				}

				if( does_not_exist( $cell['content'] ) )
				{
					$cell['content'] = '&nbsp;';
				}

				print "\t\t\t\t\t" . '<td';

				if( exists( $cell['colspan'] ) )
				{
					print ' colspan="' . $cell['colspan'] . '"';
				}

				if( exists( $cell['class'] ) )
				{
					print ' class="' . $cell['class'] . '"';
				}

				if( exists( $cell['style'] ) )
				{
					print ' style="' . $cell['style'] . '"';
				}

				print '>' . $cell['content'] . '</td>' . "\n";
			}

			print "\t\t\t\t" . '</tr>' . "\n";
		}
		print "\t\t\t" . '</tbody>' . "\n";

		print "\t\t" . '</table>' . "\n";

		return TRUE;
	}

	/**
	 * Base::create_form()
	 *
	 * Create a form using Forms::create_form().
	 *
	 * @access public
	 * @param string $form_name Form name
	 * @param string $form_action URL to send form data
	 * @param array $form_data Parameters used to propagate form
	 * @return void Prints a complete HTML form
	 */
	public function create_form( $form_name, $form_action, $form_data )
	{
		return Forms::create_form( $form_name, $form_action, $form_data );
	}
}

?>