<?php

/**
 * dbox :: inc/lib/lib.forms.php
 *
 * This contains the methods required to generate a form.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Forms
 *
 * Form generator. Must be called from within an instance of Base or its descendant.
 *
 * @todo Consolidate Forms::_create_*_type_selector() methods
 *
 * @package Forms
 */
class Forms
{
	/**
	 * Forms::_create_date_selector()
	 *
	 * Create date selector form controls.
	 *
	 * @access private
	 * @param string $selector_id Form control id for selector
	 * @param bool|int $current_date UNIX timestamp to be selected by default
	 * @return void Prints form controls
	 */
	final private static function _create_date_selector( $selector_id, $current_date = FALSE )
	{
		$current_date = ( $current_date ) ? $current_date : time();

		print "\t" . '<select id="' . $selector_id . ':month" name="' . $selector_id . '[month]">' . "\n";

		for( $i = 1; $i <= 12; $i++ )
		{
			switch( $i )
			{
				case 1:
					$month = 'January';
				break;

				case 2:
					$month = 'February';
				break;

				case 3:
					$month = 'March';
				break;

				case 4:
					$month = 'April';
				break;

				case 5:
					$month = 'May';
				break;

				case 6:
					$month = 'June';
				break;

				case 7:
					$month = 'July';
				break;

				case 8:
					$month = 'August';
				break;

				case 9:
					$month = 'September';
				break;

				case 10:
					$month = 'October';
				break;

				case 11:
					$month = 'November';
				break;

				case 12:
					$month = 'December';
				break;
			}

			$selected = ( $i == date( 'n', $current_date ) ) ? ' selected="selected"' : '';

			print "\t\t" . '<option value="' . $i . '"' . $selected . '>' . $month . '</option>' . "\n";
		}

		print "\t" . '</select>' . "\n\n";

		print "\t" . '<select id="' . $selector_id . ':day" name="' . $selector_id . '[day]">' . "\n";

		for( $i = 1; $i <= 31; $i++ )
		{
			$selected = ( $i == date( 'j', $current_date ) ) ? ' selected="selected"' : '';

			print "\t\t" . '<option value="' . $i . '"' . $selected . '>' . $i . '</option>' . "\n";
		}

		print "\t" . '</select>' . "\n\n";

		print "\t" . '<select id="' . $selector_id . ':year" name="' . $selector_id . '[year]">' . "\n";

		$year = date( 'Y', $current_date );

		for( $i = $year - 2; $i <= $year + 2; $i++ )
		{
			$selected = ( $i == $year ) ? ' selected="selected"' : '';

			print "\t\t" . '<option value="' . $i . '"' . $selected . '>' . $i . '</option>' . "\n";
		}

		print "\t" . '</select>' . "\n";
	}

	/**
	 * Forms::_create_time_selector()
	 *
	 * Create time selector form controls.
	 *
	 * @access private
	 * @param string $selector_id Form control id for selector
	 * @param bool|int $current_date UNIX timestamp to be selected by default
	 * @return void Prints form controls
	 */
	final private static function _create_time_selector( $selector_id, $current_date = FALSE )
	{
		$current_date = ( $current_date ) ? $current_date : time();

		if( strlen( $current_date ) == 4 )
		{
			list( $hour, $minute ) = str_split( $current_date, 2 );

			$current_date = mktime( $hour, $minute, 0 );
		}

		print "\t\t" . '<select id="' . $selector_id . ':hour" name="' . $selector_id . '[hour]">' . "\n";

		for( $i = 1; $i <= 12; $i++ )
		{
			$selected = ( $i == date( 'g', $current_date ) ) ? ' selected="selected"' : '';

			print "\t\t\t" . '<option value="' . $i . '"' . $selected . '>' . str_pad( $i, 2, 0, STR_PAD_LEFT ) . '</option>' . "\n";
		}

		print "\t\t" . '</select>' . "\n\n";

		print "\t\t" . '<select id="' . $selector_id . ':minute" name="' . $selector_id . '[minute]">' . "\n";

		for( $i = 0; $i < 60; $i++ )
		{
			$selected = ( str_pad( $i, 2, 0, STR_PAD_LEFT ) == date( 'i', $current_date ) ) ? ' selected="selected"' : '';

			print "\t\t\t" . '<option value="' . $i . '"' . $selected . '>' . str_pad( $i, 2, 0, STR_PAD_LEFT ) . '</option>' . "\n";
		}

		print "\t\t" . '</select>' . "\n\n";

		print "\t\t" . '<select id="' . $selector_id . ':meridiem" name="' . $selector_id . '[meridiem]">' . "\n";

		foreach( array( 'AM', 'PM' ) as $meridiem )
		{
			$selected = ( $meridiem == date( 'A', $current_date ) ) ? ' selected="selected"' : '';

			print "\t\t\t" . '<option value="' . $meridiem . '"' . $selected . '>' . $meridiem . '</option>' . "\n";
		}

		print "\t\t" . '</select>' . "\n";

	}

	/**
	 * Forms::create_form()
	 *
	 * Create a complete HTML form using the given parameters.
	 *
	 * @access public
	 * @param string $form_name Form name
	 * @param string $form_action URL to send form data
	 * @param array $form_data Parameters used to propagate form
	 * @param bool $form_multipart Multipart form?
	 * @return void Prints a complete HTML form
	 */
	public static function create_form( $form_name, $form_action, $form_data, $form_multipart = FALSE )
	{
		global $dbox;

		if( !is_array( $form_data ) )
		{
			return FALSE;
		}

		$enctype = ( $form_multipart ) ? ' enctype="multipart/form-data"' : '';

		print "\t" . '<form id="' . $form_name . '" action="' . $form_action . '" method="post"' . $enctype . ' accept-charset="UTF-8" style="text-align: left;">' . "\n";

		foreach( $form_data as $id => $id_data )
		{
			if( empty( $id_data ) )
			{
				continue;
			}

			$form_data[$id]['name'] = $id_data['name'] = ( isset( $id_data['name'] ) ) ? $id_data['name'] : 'r_' . md5( rand() . microtime() );
			$form_data[$id]['id'] = $id_data['id'] = ( isset( $id_data['id'] ) ) ? $id_data['id'] : $id_data['name']; //'r_' . md5( rand() . microtime() );

			switch( $id_data['type'] )
			{
				case 'header':
					print "\t\t<h";
					print ( $id_data['data']['level'] && ( $id_data['data']['level'] < 6 ) ) ? $id_data['data']['level'] : 2;
					print ' id="' . $id_data['id'] . '">' . htmlentities( $id_data['label'], ENT_QUOTES, 'UTF-8' ) . '</h';
					print ( $id_data['data']['level'] && ( $id_data['data']['level'] < 6 ) ) ? $id_data['data']['level'] : 2;
					print ">\n";
				break;

				case 'text':
				case 'password':
					print "\t\t<p>";

					if( isset( $id_data['label'] ) )
					{
						print '<label>' . htmlentities( $id_data['label'], ENT_QUOTES, 'UTF-8' ) . ':<br />';
					}

					print '<input id="' . $id_data['id'] . '" name="' . $id_data['name'] . '" type="' . $id_data['type'] . '"';

					if( isset( $id_data['data']['value'] ) )
					{
						print ' value="' . htmlentities( $id_data['data']['value'], ENT_QUOTES, 'UTF-8' ) . '"';
					}

					if( isset( $id_data['data']['size'] ) )
					{
						print ' size="' . (int) $id_data['data']['size'] . '"';
					}

					if( isset( $id_data['data']['maxlength'] ) )
					{
						print ' maxlength="' . (int) $id_data['data']['maxlength'] . '"';
					}

					print ' />';

					if( isset( $id_data['label'] ) )
					{
						print '</label>';
					}

					print "</p>\n";
				break;

				case 'upload':
				case 'file':
					print "\t\t<p>";

					if( isset( $id_data['label'] ) )
					{
						print '<label>' . htmlentities( $id_data['label'], ENT_QUOTES, 'UTF-8' ) . ':<br />';
					}

					print '<input id="' . $id_data['id'] . '" name="' . $id_data['name'] . '" type="file"';

					if( isset( $id_data['data']['accept'] ) )
					{
						print ' accept="' . htmlentities( implode( $id_data['data']['accept'], ',' ), ENT_QUOTES, 'UTF-8' ) . '"';
					}

					if( isset( $id_data['data']['multiple'] ) && $id_data['data']['multiple'] )
					{
						print ' multiple="multiple"';
					}

					print ' />';

					if( isset( $id_data['label'] ) )
					{
						print '</label>';
					}

					print "</p>\n";
				break;

				case 'hidden':
					print "\t\t<p>";

					if( preg_match( '#(\[|\])#', $id_data['name'] ) && ( $id_data['name'] == $id_data['id'] ) )
					{
						$id_data['id'] = str_replace( '[', ':', $id_data['id'] );
						$id_data['id'] = str_replace( ']', '', $id_data['id'] );

						$form_data[$id]['id'] = $id_data['id'];
					}

					print '<input id="' . $id_data['id'] . '" name="' . $id_data['name'] . '" type="hidden"';

					if( isset( $id_data['data']['value'] ) )
					{
						print ' value="' . htmlentities( $id_data['data']['value'], ENT_QUOTES, 'UTF-8' ) . '"';
					}

					print ' />';
					print "</p>\n";
				break;

				case 'submit':
					print "\t\t<p>";

					print '<input id="' . $id_data['id'] . '" name="' . $id_data['name'] . '" type="submit"';

					if( isset( $id_data['data']['value'] ) )
					{
						print ' value="' . htmlentities( $id_data['data']['value'], ENT_QUOTES, 'UTF-8' ) . '"';
					}

					print ' />';
					print "</p>\n";
				break;

				case 'textarea':
					print "\t\t<p>";

					if( isset( $id_data['label'] ) )
					{
						print '<label>' . htmlentities( $id_data['label'], ENT_QUOTES, 'UTF-8' ) . ':<br />';
					}

					print '<textarea id="' . $id_data['id'] . '" name="' . $id_data['name'] . '"';

					if( isset( $id_data['data']['rows'] ) )
					{
						print ' rows="' . (int) $id_data['data']['rows'] . '"';
					}

					if( isset( $id_data['data']['cols'] ) )
					{
						print ' cols="' . (int) $id_data['data']['cols'] . '"';
					}

					print '>';

					if( isset( $id_data['data']['value'] ) )
					{
						print htmlentities( $id_data['data']['value'], ENT_QUOTES, 'UTF-8' );
					}

					print '</textarea>';

					if( isset( $id_data['label'] ) )
					{
						print '</label>';
					}

					print "</p>\n";
				break;

				case 'radio':
					print "\t\t<p>\n";

					foreach( $id_data['data']['values'] as $value => $label )
					{
						$checked = ( isset( $id_data['data']['checked'] ) && ( $id_data['data']['checked'] == $value ) ) ? ' checked' : '';

						print "\t\t\t" . '<label><input id="' . $id_data['id'] . ':' . ( $value + 3 ) . '" name="' . $id_data['name'] . '" type="radio" value="' . $value . '"' . $checked . ' />' . htmlentities( $label, ENT_QUOTES, 'UTF-8' ) . '</label><br />' . "\n";
					}

					print "\t\t</p>\n";
				break;

				case 'boolean':
					print "\t\t<p>";
					print '<label><input id="' . $id_data['id'] . '" name="' . $id_data['name'] . '" type="checkbox" value="1" />' . htmlentities( $id_data['label'], ENT_QUOTES, 'UTF-8' ) . '</label>';
					print "</p>\n";
				break;

				case 'date':
					print "\t\t" . '<p>' . htmlentities( $id_data['label'], ENT_QUOTES, 'UTF-8' ) . ': ' . "\n";
					Forms::_create_date_selector( $id_data['name'], @$id_data['data'][0] );
					print "\t\t" . '</p>' . "\n";
				break;

				case 'time':
					print "\t\t" . '<p>' . htmlentities( $id_data['label'], ENT_QUOTES, 'UTF-8' ) . ': ' . "\n";
					Forms::_create_time_selector( $id_data['name'], @$id_data['data'][0] );
					print "\t\t" . '</p>' . "\n";
				break;

				case 'date_time':
					print "\t\t" . '<p>' . htmlentities( $id_data['label'], ENT_QUOTES, 'UTF-8' ) . ': ' . "\n";
					Forms::_create_date_selector( $id_data['name'], @$id_data['data'][0] );
					Forms::_create_time_selector( $id_data['name'], @$id_data['data'][0] );
					print "\t\t" . '</p>' . "\n";
				break;
			}
		}

		print "\t</form>\n";
	}
}

?>