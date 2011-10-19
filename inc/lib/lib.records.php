<?php

/**
 * dbox :: inc/lib/lib.records.php
 *
 * This contains all of the records functions.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Records
 *
 * Child class for tasks related to Records
 *
 * @package dbox
 */
class Records extends Base
{
/*	function __construct()
	{
		global $Changes, $User;

		parent::__construct();

//		$Changes->track_change( 'test_records', $User->user_info['id'], -1, 'Test', NULL );
	}*/

	private function _csv_escape( $string )
	{
		return str_replace( '"', '""', $string );
	}

	private function _parse_record( $record )
	{
		global $Database;

		$gloss = array();

		foreach( explode( ' ', $record ) as $word_id => $word )
		{
			$gloss[$word] = array();

			foreach( explode( '-', $word ) as $morpheme_id => $morpheme )
			{
				$gloss[$word][$morpheme] = array();

				$sql = "SELECT d.*
					FROM dictionary d
					WHERE d.morpheme = '" . $Database->escape( $morpheme ) . "'";

				$result = $Database->query( $sql );

				if( !$Database->has_result( $result ) )
				{
					// No gloss available.
				}

				while( $row = $Database->fetch_assoc( $result ) )
				{
					$gloss[$word][$morpheme][] = $row['gloss'];
				}

				$Database->free_result( $result );
			}
		}

		return $gloss;
	}

	private function _generate_gloss_table( $record )
	{
		$gloss = $this->_parse_record( $record );

		$table = '';
		$transcription_cells = $gloss_cells = array();
		$transcription_row = $gloss_row = array();

		$word_boundary = "\t\t" . '<td class="boundary-word">&nbsp;</td>' . "\n";
		$morpheme_boundary = "\t\t" . '<td class="boundary-morpheme">-</td>' . "\n";

		foreach( $gloss as $word => $morphemes )
		{
			foreach( $morphemes as $morpheme => $morpheme_gloss )
			{
				$transcription_class = '';

				if( count( $morpheme_gloss ) < 1 )
				{
					// No gloss
					$gloss_cells[$word][] = "\t\t" . '<td class="unglossed">***</td>' . "\n";
					$transcription_class = ' class="unglossed"';
				}
				elseif( count( $morpheme_gloss ) > 1 )
				{
					// Multiple glosses
					$gloss_cells[$word][] = "\t\t" . '<td class="multiple">' . htmlentities( implode( '/', $morpheme_gloss ), ENT_QUOTES, 'UTF-8' ) . '</td>' . "\n";
					$transcription_class = ' class="multiple"';
				}
				else
				{
					// Just one gloss
					$gloss_cells[$word][] = "\t\t" . '<td>' . htmlentities( $morpheme_gloss[0], ENT_QUOTES, 'UTF-8' ) . '</td>' . "\n";
				}

				$transcription_cells[$word][] = "\t\t" . '<td' . $transcription_class . '>' . htmlentities( $morpheme, ENT_QUOTES, 'UTF-8' ) . '</td>' . "\n";
			}

			$transcription_row[] = implode( $morpheme_boundary, $transcription_cells[$word] );
			$gloss_row[] = implode( $morpheme_boundary, $gloss_cells[$word] );
		}

		$transcription_row = implode( $word_boundary, $transcription_row );
		$gloss_row = implode( $word_boundary, $gloss_row );

		$table .= '<table class="gloss">' . "\n";
		$table .= "\t" . '<tr class="transcription">' . "\n";
		$table .= $transcription_row;
		$table .= "\t" . '</tr>' . "\n";
		$table .= "\t" . '<tr class="gloss">' . "\n";
		$table .= $gloss_row;
		$table .= "\t" . '</tr>' . "\n";
		$table .= '</table>' . "\n";

		return $table;
	}

	public function add_record()
	{
		global $Database, $Changes, $User;

		$submit = ( exists( $_POST['submit'] ) ) ? TRUE : FALSE;

		if( $submit )
		{
			$transcription = ( exists( $_POST['transcription'] ) ) ? $_POST['transcription'] : NULL;
			$translation = ( exists( $_POST['translation'] ) ) ? $_POST['translation'] : NULL;
			$comments = ( exists( $_POST['comments'] ) ) ? $_POST['comments'] : NULL;
			$grammaticality = ( isset( $_POST['grammaticality'] ) ) ? (int) $_POST['grammaticality'] : G_GOOD;

//			else
			{
				$sql = "INSERT INTO records ( transcription, translation, comments, grammaticality, creator_id, creation_time )
					VALUES ( '" . $Database->escape( $transcription ) . "', '" . $Database->escape( $translation ) . "', '" . $Database->escape( $comments ) . "', $grammaticality, " . $User->user_info['id'] . ', ' . time() . ' )';

				$result = $Database->query( $sql );

				if( $result )
				{
					$new_value = array(
						'transcription'		=>	$transcription,
						'translation'		=>	$translation,
						'comments'			=>	$comments,
						'grammaticality'	=>	$grammaticality,
					);

					$Changes->track_change( 'add_record', $User->user_info['id'], $Database->insert_id(), serialize( $new_value ) );

					print_message( 'good', 'Record for &quot;' . htmlentities( $transcription, ENT_QUOTES, 'UTF-8' ) . '&quot; added successfully.', 'Addition succeeded.' );
				}
			}
		}

		$form_data = array(
			array(
				'type'	=>	'header',
				'label'	=>	'Add Record',
				'data'	=>	array(
					'level'	=>	2,
				)
			),
			array(
				'type'	=>	'radio',
				'name'	=>	'grammaticality',
				'label'	=>	'Grammaticality',
				'data'	=>	array(
					'checked'	=>	G_GOOD,
					'values'	=>	array(
						G_GOOD	=>	'Grammatical',
//						G_OKAY	=>	'Okay',
						G_MARGINAL	=>	'Marginal',
//						G_NOTSOBAD	=>	'Not so bad',
						G_BAD	=>	'Ungrammatical',
					)
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'transcription',
				'label'	=>	'Transcription',
				'data'	=>	array(
					'size'		=>	30,
					'autofocus'	=>	TRUE,
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'translation',
				'label'	=>	'Translation',
				'data'	=>	array(
					'size'	=>	30,
				)
			),
			array(
				'type'	=>	'textarea',
				'name'	=>	'comments',
				'label'	=>	'Comments',
				'data'	=>	array(
					'rows'	=>	3,
					'cols'	=>	50
				)
			),
			array(
				'type'	=>	'submit',
				'name'	=>	'submit',
				'data'	=>	array(
					'value'	=>	'Submit'
				)
			)
		);

		Forms::create_form( 'add-record', ROOT . 'records.php?mode=add', $form_data );
	}

	public function edit_record( $record_id )
	{
		global $Database, $Changes, $User;

		if( !$record_id )
		{
			print_message( 'bad', 'Please specify which record you want to edit.', 'Record ID not specified.' );

			return FALSE;
		}

		$record_id = (int) $record_id;

		$sql = 'SELECT r.transcription, r.translation, r.comments, r.grammaticality
			FROM records r
			WHERE r.record_id = ' . $record_id;

		$result = $Database->query( $sql );
		$record = $Database->fetch_assoc( $result );
		$Database->free_result( $result );

		$submit = ( exists( $_POST['submit'] ) ) ? TRUE : FALSE;

		if( $submit )
		{
			$transcription = ( exists( $_POST['transcription'] ) ) ? $_POST['transcription'] : NULL;
			$translation = ( exists( $_POST['translation'] ) ) ? $_POST['translation'] : NULL;
			$comments = ( exists( $_POST['comments'] ) ) ? $_POST['comments'] : NULL;
			$grammaticality = ( isset( $_POST['grammaticality'] ) ) ? (int) $_POST['grammaticality'] : G_GOOD;

//			else
			{
				$sql = "UPDATE records r
					SET transcription = '" . $Database->escape( $transcription ) . "', translation = '" . $Database->escape( $translation ) . "', comments = '" . $Database->escape( $comments ) . "', grammaticality = $grammaticality
					WHERE r.record_id = " . $record_id;

				$result = $Database->query( $sql );

				if( $result )
				{
					$new_value = array(
						'transcription'		=>	$transcription,
						'translation'		=>	$translation,
						'comments'			=>	$comments,
						'grammaticality'	=>	$grammaticality,
					);

					$Changes->track_change( 'edit_record', $User->user_info['id'], $record_id, serialize( $new_value ), serialize( $record ) );

					print_message( 'good', 'Record for &quot;' . htmlentities( $transcription, ENT_QUOTES, 'UTF-8' ) . '&quot; update successfully.', 'Update succeeded.' );

					$this->view_records();

					return;
				}
			}
		}

		$form_data = array(
			array(
				'type'	=>	'header',
				'label'	=>	'Edit Record',
				'data'	=>	array(
					'level'	=>	2,
				)
			),
			array(
				'type'	=>	'radio',
				'name'	=>	'grammaticality',
				'label'	=>	'Grammaticality',
				'data'	=>	array(
					'checked'	=>	$record['grammaticality'],
					'values'	=>	array(
						G_GOOD	=>	'Grammatical',
//						G_OKAY	=>	'Okay',
						G_MARGINAL	=>	'Marginal',
//						G_NOTSOBAD	=>	'Not so bad',
						G_BAD	=>	'Ungrammatical',
					)
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'transcription',
				'label'	=>	'Transcription',
				'data'	=>	array(
					'size'		=>	30,
					'value'		=>	$record['transcription'],
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'translation',
				'label'	=>	'Translation',
				'data'	=>	array(
					'size'	=>	30,
					'value'	=>	$record['translation']
				)
			),
			array(
				'type'	=>	'textarea',
				'name'	=>	'comments',
				'label'	=>	'Comments',
				'data'	=>	array(
					'rows'	=>	3,
					'cols'	=>	50,
					'value'	=>	$record['comments']
				)
			),
			array(
				'type'	=>	'submit',
				'name'	=>	'submit',
				'data'	=>	array(
					'value'	=>	'Submit'
				)
			)
		);

		Forms::create_form( 'edit-record', ROOT . 'records.php?mode=edit&amp;record_id=' . $record_id, $form_data );
	}

	public function view_records()
	{
		global $Database, $User;

		$columns = $headers = $rows = array();

//		$language_where = ( $this->language ) ? "AND r.language = '{$this->language}'" : '';

		$columns[0] = array(
			'style'	=>	'width: 5%; border-right: 2px solid black;'
		);
		$headers[0] = array();

		$columns[1] = array(
			'style'	=>	'width: 5%;'
		);
		$headers[1] = array(
			'content'	=>	'No.'
		);

/*		if( !$language_where )
		{
			$columns[2] = array(
//				'style'	=>	'width: 7.5%;'
			);
			$headers[2] = array(
				'content'	=>	'Language'
			);
		}*/

		$columns[3] = array(
//			'style'	=>	'width: 42.5%;'
		);
		$headers[3] = array(
			'content'	=>	'Elicitation'
		);

		$columns[4] = array(
			'style'	=>	'width: 30%;'
		);
		$headers[4] = array(
			'content'	=>	'Comments'
		);

		$columns[5] = array(
			'style'	=>	'width: 15%;'
		);
		$headers[5] = array(
			'content'	=>	'Transcriber'
		);

		$sql = 'SELECT r.*, u.name
			FROM records r
			LEFT JOIN ( users u )
				ON ( r.creator_id = u.user_id )
			ORDER BY r.creation_time ASC';

		$result = $Database->query( $sql );

		if( !$Database->has_result( $result ) )
		{
			$rows[] = array(
				'content'	=>	array(
					0	=>	array(
						'colspan'	=>	count( $headers ),
						'content'	=>	'<strong>No records returned.</strong>'
					)
				)
			);
		}

		while( $row = $Database->fetch_assoc( $result ) )
		{
			switch( $row['grammaticality'] )
			{
				case G_BAD:
					$g_class = 'bad';
					$g_symbol = '*';
				break;

				case G_NOTSOBAD:
//					$g_class = 'notsobad';
					$g_class = 'marginal';
					$g_symbol = '*?';
				break;

				case G_MARGINAL:
					$g_class = 'marginal';
					$g_symbol = '%';
				break;

				case G_OKAY:
//					$g_class = 'okay';
					$g_class = 'marginal';
					$g_symbol = '?';
				break;

				case G_GOOD:
				default:
					$g_class = 'good';
					$g_symbol = '';
				break;

			}

			$rows[] = array(
				'class'		=>	$g_class,
				'content'	=>	array(
					0	=>	array(
						'class'		=>	'edit',
						'content'	=>	'<a href="' . ROOT . 'records.php?mode=edit&amp;record_id=' . $row['record_id'] . '">edit</a>'
					),
					1	=>	array(
						'content'	=>	'(' . $row['record_id'] . ')'
					),
					2	=>	/*( $language_where ) ? array() :*/ array(
//						'content'	=>	$this->format_language( $row['language'] )
					),
					3	=>	array(
						'content'	=>	'<span class="g-symbol">' . $g_symbol . '</span>' . "\n" . $this->_generate_gloss_table( $row['transcription'] ) . '<br /><span class="translation">' . $this->convert_newlines( htmlentities( $row['translation'], ENT_QUOTES, 'UTF-8' ) ) . '</span>'
					),
					4	=>	array(
						'content'	=>	$this->convert_newlines( htmlentities( $row['comments'], ENT_QUOTES, 'UTF-8' ) )
					),
					5	=>	array(
						'content'	=>	'<span class="creator">' . $row['name'] . '</span><br /><span class="creation-time">' . $this->format_date( $row['creation_time'], 'F j, Y' ) . '</span>'
					),
				)
			);

			$gloss = $this->_generate_gloss_table( $row['transcription'] );
		}

		$Database->free_result( $result );

		$this->print_list_table( ROOT . 'records.php', $columns, $headers, $rows );
	}

	public function export_records()
	{
		global $Database, $User;

		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename=dbox.csv' );

		$sql = 'SELECT r.*, u.name
			FROM records r
			LEFT JOIN ( users u )
				ON ( r.creator_id = u.user_id )
			ORDER BY r.creation_time ASC';

		$result = $Database->query( $sql );

		if( !$Database->has_result( $result ) )
		{
			// Oh well.
		}

		print 'record_id,transcription,translation,comments,grammaticality,creator_id,creation_time' . "\n";

		while( $row = $Database->fetch_assoc( $result ) )
		{
			print $row['record_id'] . ',"' . $this->_csv_escape( $row['transcription'] ) . '","' . $this->_csv_escape( $row['translation'] ) . '","' . $this->_csv_escape( $row['comments'] ) . '",' . $row['grammaticality'] . ',' . $row['creator_id'] . ',' . $row['creation_time'] . "\n";
		}

		$Database->free_result( $result );
	}
}

?>