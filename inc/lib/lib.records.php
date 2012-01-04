<?php

/**
 * dbox :: inc/lib/lib.records.php
 *
 * This contains all of the records functions.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
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
	}*/

	private function _csv_escape( $string )
	{
		return str_replace( '"', '""', $string );
	}

	private function _get_grammaticality_symbol( $value )
	{
		switch( $value )
		{
			case GR_VAL_BAD:
				$g_symbol = GR_SYM_BAD;
			break;

			case GR_VAL_NOTSOBAD:
				$g_symbol = GR_SYM_NOTSOBAD;
			break;

			case GR_VAL_MARGINAL:
				$g_symbol = GR_SYM_MARGINAL;
			break;

			case GR_VAL_OKAY:
				$g_symbol = GR_SYM_OKAY;
			break;

			case GR_VAL_GOOD:
			default:
				$g_symbol = GR_SYM_GOOD;
			break;

		}

		return $g_symbol;
	}

	private function _parse_record( $record )
	{
		global $Database;

		$gloss = array();

		foreach( explode( ' ', $record ) as $word_id => $word )
		{
			$gloss[$word_id] = array(
				'word'		=>	$word,
				'morphemes'	=>	array(),
			);

			foreach( explode( '-', $word ) as $morpheme_id => $morpheme )
			{
				$gloss[$word_id]['morphemes'][$morpheme_id] = array(
					'morpheme'	=>	$morpheme,
					'glosses'	=>	array(),
				);

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
					$gloss[$word_id]['morphemes'][$morpheme_id]['glosses'][] = $row['gloss'];
				}

				$Database->free_result( $result );
			}
		}

		return $gloss;
	}

	private function _generate_gloss_table( $record, $grammaticality = GR_VAL_GOOD )
	{
		$creator_id = ( exists( $_REQUEST['creator_id'] ) ) ? (int) $_REQUEST['creator_id'] : NULL;

		$gloss = $this->_parse_record( $record );

		$g_symbol = $this->_get_grammaticality_symbol( $grammaticality );

		$table = '';
		$transcription_cells = $gloss_cells = array();
		$transcription_row = $gloss_row = array();

		$word_boundary = "\t\t" . '<td class="boundary-word">&nbsp;</td>' . "\n";
		$morpheme_boundary = "\t\t" . '<td class="boundary-morpheme">-</td>' . "\n";

		foreach( $gloss as $word_id => $word_data )
		{
			foreach( $word_data['morphemes'] as $morpheme_id => $morpheme_data )
			{
				$morpheme = $morpheme_data['morpheme'];
				$morpheme_gloss = $morpheme_data['glosses'];

				$transcription_class = '';

				if( count( $morpheme_gloss ) < 1 )
				{
					// No gloss
					$gloss_cells[$word_id][] = "\t\t" . '<td class="unglossed"><a href="' . ROOT . 'dictionary.php?mode=add&amp;morpheme=' . $morpheme . '">***</a></td>' . "\n";
					$transcription_class = ' class="unglossed"';
				}
				elseif( count( $morpheme_gloss ) > 1 )
				{
					// Multiple glosses
					$gloss_cells[$word_id][] = "\t\t" . '<td class="multiple">' . htmlentities( implode( '/', $morpheme_gloss ), ENT_QUOTES, 'UTF-8' ) . '</td>' . "\n";
					$transcription_class = ' class="multiple"';
				}
				else
				{
					// Just one gloss
					$gloss_cells[$word_id][] = "\t\t" . '<td>' . htmlentities( $morpheme_gloss[0], ENT_QUOTES, 'UTF-8' ) . '</td>' . "\n";
				}

				$transcription_cells[$word_id][] = "\t\t" . '<td' . $transcription_class . '><a href="' . ROOT . 'records.php?mode=view&amp;morpheme=' . $morpheme . /*( ( $creator_id ) ? '&amp;creator_id=' . $creator_id : '' ) .*/ '">' . htmlentities( $morpheme, ENT_QUOTES, 'UTF-8' ) . '</a></td>' . "\n";
			}

			$transcription_row[] = implode( $morpheme_boundary, $transcription_cells[$word_id] );
			$gloss_row[] = implode( $morpheme_boundary, $gloss_cells[$word_id] );
		}

		$transcription_row = implode( $word_boundary, $transcription_row );
		$gloss_row = implode( $word_boundary, $gloss_row );

		$table .= '<table class="gloss">' . "\n";
		$table .= "\t" . '<tr class="transcription">' . "\n";
		$table .= "\t\t" . '<td rowspan="2" class="g-symbol">' . $g_symbol . '</td>' . "\n";
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
			$transcription = ( exists( $_POST['transcription'] ) ) ? trim( $_POST['transcription'] ) : NULL;
			$translation = ( exists( $_POST['translation'] ) ) ? trim( $_POST['translation'] ) : NULL;
			$comments = ( exists( $_POST['comments'] ) ) ? trim( $_POST['comments'] ) : NULL;
			$grammaticality = ( isset( $_POST['grammaticality'] ) ) ? (int) $_POST['grammaticality'] : GR_VAL_GOOD;

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

					$Changes->track_change( $User->user_info['id'], 'record', $Database->insert_id(), 'add', serialize( $new_value ) );

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
					'checked'	=>	GR_VAL_GOOD,
					'values'	=>	array(
						GR_VAL_GOOD		=>	'Grammatical',
//						GR_VAL_OKAY		=>	'Okay',
						GR_VAL_MARGINAL	=>	'Marginal',
//						GR_VAL_NOTSOBAD	=>	'Not so bad',
						GR_VAL_BAD		=>	'Ungrammatical',
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
			$transcription = ( exists( $_POST['transcription'] ) ) ? trim( $_POST['transcription'] ) : NULL;
			$translation = ( exists( $_POST['translation'] ) ) ? trim( $_POST['translation'] ) : NULL;
			$comments = ( exists( $_POST['comments'] ) ) ? trim( $_POST['comments'] ) : NULL;
			$grammaticality = ( isset( $_POST['grammaticality'] ) ) ? (int) $_POST['grammaticality'] : GR_VAL_GOOD;

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

					$Changes->track_change( $User->user_info['id'], 'record', $record_id, 'edit', serialize( $new_value ), serialize( $record ) );

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
						GR_VAL_GOOD		=>	'Grammatical',
//						GR_VAL_OKAY		=>	'Okay',
						GR_VAL_MARGINAL	=>	'Marginal',
//						GR_VAL_NOTSOBAD	=>	'Not so bad',
						GR_VAL_BAD		=>	'Ungrammatical',
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

	public function delete_record( $record_id )
	{
		global $Database, $Changes, $User;

		if( !$record_id )
		{
			print_message( 'bad', 'Please specify which record you want to delete.', 'Record ID not specified.' );

			return FALSE;
		}

		$record_id = (int) $record_id;

		$sql = 'SELECT r.*
			FROM records r
			WHERE r.record_id = ' . $record_id;

		$result = $Database->query( $sql );
		$entry = $Database->fetch_assoc( $result );
		$Database->free_result( $result );

		if( empty( $entry ) )
		{
			print_message( 'bad', 'There is no record matching that ID.', 'Unknown Record ID.' );

			$this->view_records();

			return;
		}

		$submit = ( exists( $_POST['submit'] ) ) ? TRUE : FALSE;

		if( $submit )
		{
			$confirm = ( isset( $_POST['delete_record_confirm'] ) ) ? (bool) $_POST['delete_record_confirm'] : FALSE;

			if( $confirm )
			{
				$sql = 'DELETE FROM records
					WHERE record_id = ' . $record_id;

				$result = $Database->query( $sql );

				if( $result )
				{
					$Changes->track_change( $User->user_info['id'], 'record', $record_id, 'delete', NULL, serialize( $entry ) );

					print_message( 'good', 'Record &quot;' . htmlentities( $entry['transcription'], ENT_QUOTES, 'UTF-8' ) . '&quot; deleted successfully.', 'Deletion succeeded.' );

					$this->view_records();

					return;
				}
			}
			else
			{
				print_message( 'bad', 'Record &quot;' . htmlentities( $entry['transcription'], ENT_QUOTES, 'UTF-8' ) . '&quot; not deleted.', 'Deletion aborted.' );

				$this->view_records();

				return;
			}
		}

		$form_data = array(
			array(
				'type'	=>	'header',
				'label'	=>	'Delete Record',
				'data'	=>	array(
					'level'	=>	2,
				)
			),
			array(
				'type'	=>	'radio',
				'name'	=>	'delete_record_confirm',
				'label'	=>	'Are you sure you want to delete the elicitation entry for "' . htmlentities( $entry['transcription'], ENT_QUOTES, 'UTF-8' ) . '"?',
				'data'	=>	array(
					'checked'	=>	NO,
					'values'	=>	array(
						YES	=>	'Yes',
						NO	=>	'No'
					)
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

		Forms::create_form( 'delete-record', ROOT . 'records.php?mode=delete&amp;record_id=' . $record_id, $form_data );
	}

	public function view_records( $morpheme = NULL, $creator_id = NULL )
	{
		global $Database, $User;

		$columns = $headers = $rows = array();

		$morpheme = ( !empty( $morpheme ) ) ? $morpheme : ( ( exists( $_REQUEST['morpheme'] ) ) ? trim( $_REQUEST['morpheme'] ) : NULL );
		$creator_id = ( !empty( $creator_id ) ) ? $creator_id : ( ( exists( $_REQUEST['creator_id'] ) ) ? (int) $_REQUEST['creator_id'] : NULL );

		$sql_wheres = array();

		if( !empty( $morpheme ) )
		{
			$sql_wheres[] = "r.transcription REGEXP '^(.*[[:space:]-])?" . $Database->escape( $morpheme ) . "([[:space:]-].*)?$'";
		}

		if( !empty( $creator_id ) )
		{
			$sql_wheres[] = 'r.creator_id = ' . $creator_id;
		}

		$sql_where = ( !empty( $sql_wheres ) ) ? 'WHERE ' . implode( $sql_wheres, ' AND ' ) : '';

//		$language_where = ( $this->language ) ? "AND r.language = '{$this->language}'" : '';

		$columns[0] = array(
			'style'	=>	'width: 5%; border-right: 2px solid black;'
		);
		$headers[0] = array(
			'class'	=>	'edit'
		);

		$columns[1] = array(
			'style'	=>	'width: 5%;'
		);
		$headers[1] = array(
			'content'	=>	'<a href="' . ROOT . 'records.php?mode=view' . ( ( $creator_id ) ? '&amp;creator_id=' . $creator_id : '' ) . ( ( $morpheme ) ? '&amp;morpheme=' . $morpheme : '' ) . '&amp;sort=id">No.</a>'
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
			'content'	=>	'<a href="' . ROOT . 'records.php?mode=view' . ( ( $creator_id ) ? '&amp;creator_id=' . $creator_id : '' ) . ( ( $morpheme ) ? '&amp;morpheme=' . $morpheme : '' ) . '&amp;sort=transcriber">Transcriber</a>'
		);

		$sort_key = 'u.name';

		if( exists( $_REQUEST['sort'] ) )
		{
			switch( $_REQUEST['sort'] )
			{
				case 'id':
					$sort_key = 'r.record_id';
				break;

				case 'transcriber':
				default:
					$sort_key = 'u.name';
				break;
			}
		}

		$sql = 'SELECT r.*, u.name, u.email_address
			FROM records r
			LEFT JOIN ( users u )
				ON ( r.creator_id = u.user_id )
			' . $sql_where . '
			ORDER BY ' . $sort_key . ' ASC, r.creation_time ASC'; // TODO: Remove this once tags are implemented.

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
				case GR_VAL_BAD:
					$g_class = 'bad';
				break;

				case GR_VAL_NOTSOBAD:
//					$g_class = 'notsobad';
					$g_class = 'marginal';
				break;

				case GR_VAL_MARGINAL:
					$g_class = 'marginal';
				break;

				case GR_VAL_OKAY:
//					$g_class = 'okay';
					$g_class = 'marginal';
				break;

				case GR_VAL_GOOD:
				default:
					$g_class = 'good';
				break;

			}

			$rows[] = array(
				'class'		=>	$g_class,
				'content'	=>	array(
					0	=>	array(
						'class'		=>	'edit',
						'content'	=>	'<a href="' . ROOT . 'records.php?mode=edit&amp;record_id=' . $row['record_id'] . '">edit</a><br /><a href="' . ROOT . 'records.php?mode=delete&amp;record_id=' . $row['record_id'] . '">delete</a>'
					),
					1	=>	array(
						'content'	=>	'(' . $row['record_id'] . ')'
					),
					2	=>	/*( $language_where ) ? array() :*/ array(
//						'content'	=>	$this->format_language( $row['language'] )
					),
					3	=>	array(
						'content'	=>	"\n" . $this->_generate_gloss_table( $row['transcription'], $row['grammaticality'] ) . '<br /><span class="translation">' . $this->convert_newlines( htmlentities( $row['translation'], ENT_QUOTES, 'UTF-8' ) ) . '</span>'
					),
					4	=>	array(
						'content'	=>	$this->convert_newlines( htmlentities( $row['comments'], ENT_QUOTES, 'UTF-8' ) )
					),
					5	=>	array(
						'content'	=>	/*'<a href="mailto:' . str_replace( '%40', '&#x0040;', rawurlencode( $row['email_address'] ) ) . '?subject=' . rawurlencode( '[dbox] Elicitation ' . $row['record_id'] . ': ' . $row['transcription'] ) . '" class="creator">'*/ '<a href="' . ROOT . 'records.php?mode=view&amp;creator_id=' . $row['creator_id'] . ( ( $morpheme ) ? '&amp;morpheme=' . $morpheme : '' ) . '" class="creator">' . htmlentities( $row['name'], ENT_QUOTES, 'UTF-8' ) . '</a><br /><span class="creation-time">' . $this->format_date( $row['creation_time'], 'F j, Y' ) . '</span>' // TODO: Include sort.
					),
				)
			);
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