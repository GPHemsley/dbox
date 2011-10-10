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

	public function add_record()
	{
		global $Database, $Changes, $User;

		$submit = ( exists( $_POST['submit'] ) ) ? TRUE : FALSE;

		if( $submit )
		{
			$transcription = ( exists( $_POST['transcription'] ) ) ? $_POST['transcription'] : NULL;
			$translation = ( exists( $_POST['translation'] ) ) ? $_POST['translation'] : NULL;
			$comments = ( exists( $_POST['comments'] ) ) ? $_POST['comments'] : NULL;
			$grammaticality = ( exists( $_POST['grammaticality'] ) ) ? (int) $_POST['grammaticality'] : 2; // TODO: Use a constant here!

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

					print_message( 'good', 'Record for  &quot;' . htmlentities( $transcription, ENT_QUOTES, 'UTF-8' ) . '&quot; added successfully.', 'Addition succeeded.' );
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
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'transcription',
				'label'	=>	'Transcription',
				'data'	=>	array(
					'size'	=>	30,
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
			$grammaticality = ( exists( $_POST['grammaticality'] ) ) ? (int) $_POST['grammaticality'] : 2; // TODO: Use a constant here!

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

					print_message( 'good', 'Record for  &quot;' . htmlentities( $transcription, ENT_QUOTES, 'UTF-8' ) . '&quot; update successfully.', 'Update succeeded.' );

					$this->view_records();

					return;
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
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'transcription',
				'label'	=>	'Transcription',
				'data'	=>	array(
					'size'	=>	30,
					'value'	=>	$record['transcription']
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

/*		if( !$language_where )
		{
			$columns[1] = array(
//				'style'	=>	'width: 7.5%;'
			);
			$headers[1] = array(
				'content'	=>	'Language'
			);
		}*/

		$columns[2] = array(
			'style'	=>	'width: 22.5%;'
		);
		$headers[2] = array(
			'content'	=>	'Transcription'
		);

		$columns[3] = array(
			'style'	=>	'width: 22.5%;'
		);
		$headers[3] = array(
			'content'	=>	'Translation'
		);

		$columns[4] = array(
			'style'	=>	'width: 20%;'
		);
		$headers[4] = array(
			'content'	=>	'Comments'
		);

		$columns[5] = array(
			'style'	=>	'width: 10%;'
		);
		$headers[5] = array(
			'content'	=>	'Creator'
		);

		$columns[6] = array(
			'style'	=>	'width: 10%;'
		);
		$headers[6] = array(
			'content'	=>	'Creation Time'
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
			$rows[] = array(
				'class'		=>	( $row['grammaticality'] < 1 ) ? 'bad' : ( ( $row['grammaticality'] > 1 ) ? 'good' : 'marginal' ),
				'content'	=>	array(
					0	=>	array(
						'class'		=>	'edit',
						'content'	=>	'<a href="' . ROOT . 'records.php?mode=edit&amp;record_id=' . $row['record_id'] . '">edit</a>'
					),
					1	=>	/*( $language_where ) ? array() :*/ array(
//						'content'	=>	$this->format_language( $row['language'] )
					),
					2	=>	array(
						'content'	=>	$this->convert_newlines( htmlentities( $row['transcription'], ENT_QUOTES, 'UTF-8' ) )
					),
					3	=>	array(
						'content'	=>	$this->convert_newlines( htmlentities( $row['translation'], ENT_QUOTES, 'UTF-8' ) )
					),
					4	=>	array(
						'content'	=>	$this->convert_newlines( htmlentities( $row['comments'], ENT_QUOTES, 'UTF-8' ) )
					),
					5	=>	array(
						'content'	=>	$row['name']
					),
					6	=>	array(
						'content'	=>	$this->format_date( $row['creation_time'], 'F j, Y<\b\r />g:i A' )
					)
				)
			);
		}

		$Database->free_result( $result );

		$this->print_list_table( ROOT . 'records.php', $columns, $headers, $rows );
	}
}

?>