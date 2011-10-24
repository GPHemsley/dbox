<?php

/**
 * dbox :: inc/lib/lib.dictionary.php
 *
 * This contains all of the dictionary functions.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011 Gordon P. Hemsley
 * @license docs/LICENSE BSD License
 */

/**
 * Dictionary
 *
 * Child class for tasks related to Dictionary
 *
 * @package dbox
 */
class Dictionary extends Base
{
/*	function __construct()
	{
		global $Changes, $User;

		parent::__construct();

//		$Changes->track_change( 'test_dictionary', $User->user_info['id'], -1, NULL, NULL );
	}*/

	public function add_morpheme()
	{
		global $Database, $Changes, $User;

		$submit = ( exists( $_POST['submit'] ) ) ? TRUE : FALSE;

		$morpheme = ( exists( $_REQUEST['morpheme'] ) ) ? trim( $_REQUEST['morpheme'] ) : NULL;

		if( $submit )
		{
			$gloss = ( exists( $_POST['gloss'] ) ) ? trim( $_POST['gloss'] ) : NULL;
			$comments = ( exists( $_POST['comments'] ) ) ? trim( $_POST['comments'] ) : NULL;
//			$morpheme_type = ( isset( $_POST['morpheme_type'] ) ) ? (int) $_POST['morpheme_type'] : GR_VAL_GOOD;

//			else
			{
				$sql = "INSERT INTO dictionary ( morpheme, gloss, comments )
					VALUES ( '" . $Database->escape( $morpheme ) . "', '" . $Database->escape( $gloss ) . "', '" . $Database->escape( $comments ) . "' )";

				$result = $Database->query( $sql );

				if( $result )
				{
					$new_value = array(
						'morpheme'		=>	$morpheme,
						'gloss'			=>	$gloss,
						'comments'		=>	$comments,
//						'morpheme_type'	=>	$morpheme_type,
					);

					$Changes->track_change( 'add_morpheme', $User->user_info['id'], $Database->insert_id(), serialize( $new_value ) );

					print_message( 'good', 'Morpheme &quot;' . htmlentities( $morpheme, ENT_QUOTES, 'UTF-8' ) . '&quot; added successfully.', 'Addition succeeded.' );
				}
			}
		}

		$form_data = array(
			array(
				'type'	=>	'header',
				'label'	=>	'Add Morpheme',
				'data'	=>	array(
					'level'	=>	2,
				)
			),
/*			array(
				'type'	=>	'radio',
				'name'	=>	'morpheme_type',
				'label'	=>	'Morpheme Type',
				'data'	=>	array(
					'values'	=>	array(
						GR_VAL_GOOD	=>	'Grammatical',
//						GR_VAL_OKAY	=>	'Okay',
						GR_VAL_MARGINAL	=>	'Marginal',
//						GR_VAL_NOTSOBAD	=>	'Not so bad',
						GR_VAL_BAD	=>	'Ungrammatical',
					)
				)
			),*/
			array(
				'type'	=>	'text',
				'name'	=>	'morpheme',
				'label'	=>	'Morpheme',
				'data'	=>	array(
					'size'		=>	30,
					'autofocus'	=>	( ( !empty( $morpheme ) ) ? FALSE : TRUE ),
					'value'		=>	$morpheme,
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'gloss',
				'label'	=>	'Gloss',
				'data'	=>	array(
					'size'		=>	30,
					'autofocus'	=>	( ( !empty( $morpheme ) ) ? TRUE : FALSE ),
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

		Forms::create_form( 'add-morpheme', ROOT . 'dictionary.php?mode=add', $form_data );
	}

	public function edit_morpheme( $morpheme_id )
	{
		global $Database, $Changes, $User;

		if( !$morpheme_id )
		{
			print_message( 'bad', 'Please specify which morpheme you want to edit.', 'Morpheme ID not specified.' );

			return FALSE;
		}

		$morpheme_id = (int) $morpheme_id;

		$sql = 'SELECT d.*
			FROM dictionary d
			WHERE d.morpheme_id = ' . $morpheme_id;

		$result = $Database->query( $sql );
		$entry = $Database->fetch_assoc( $result );
		$Database->free_result( $result );

		$submit = ( exists( $_POST['submit'] ) ) ? TRUE : FALSE;

		if( $submit )
		{
			$morpheme = ( exists( $_POST['morpheme'] ) ) ? trim( $_POST['morpheme'] ) : NULL;
			$gloss = ( exists( $_POST['gloss'] ) ) ? trim( $_POST['gloss'] ) : NULL;
			$comments = ( exists( $_POST['comments'] ) ) ? trim( $_POST['comments'] ) : NULL;
//			$morpheme_type = ( isset( $_POST['morpheme_type'] ) ) ? (int) $_POST['morpheme_type'] : GR_VAL_GOOD;

//			else
			{
				$sql = "UPDATE dictionary d
					SET d.morpheme = '" . $Database->escape( $morpheme ) . "', d.gloss = '" . $Database->escape( $gloss ) . "', d.comments = '" . $Database->escape( $comments ) . "'
					WHERE d.morpheme_id = " . $morpheme_id;

				$result = $Database->query( $sql );

				if( $result )
				{
					$new_value = array(
						'morpheme'		=>	$morpheme,
						'gloss'			=>	$gloss,
						'comments'		=>	$comments,
//						'morpheme_type'	=>	$morpheme_type,
					);

					$Changes->track_change( 'edit_morpheme', $User->user_info['id'], $morpheme_id, serialize( $new_value ), serialize( $entry ) );

					print_message( 'good', 'Morpheme &quot;' . htmlentities( $morpheme, ENT_QUOTES, 'UTF-8' ) . '&quot; update successfully.', 'Update succeeded.' );

					$this->view_dictionary();

					return;
				}
			}
		}

		$form_data = array(
			array(
				'type'	=>	'header',
				'label'	=>	'Edit Morpheme',
				'data'	=>	array(
					'level'	=>	2,
				)
			),
/*			array(
				'type'	=>	'radio',
				'name'	=>	'morpheme_type',
				'label'	=>	'Morpheme Type',
				'data'	=>	array(
					'checked'	=>	$entry['morpheme_type'],
					'values'	=>	array(
						GR_VAL_GOOD	=>	'Grammatical',
//						GR_VAL_OKAY	=>	'Okay',
						GR_VAL_MARGINAL	=>	'Marginal',
//						GR_VAL_NOTSOBAD	=>	'Not so bad',
						GR_VAL_BAD	=>	'Ungrammatical',
					)
				)
			),*/
			array(
				'type'	=>	'text',
				'name'	=>	'morpheme',
				'label'	=>	'Morpheme',
				'data'	=>	array(
					'size'		=>	30,
					'value'		=>	$entry['morpheme'],
				)
			),
			array(
				'type'	=>	'text',
				'name'	=>	'gloss',
				'label'	=>	'Gloss',
				'data'	=>	array(
					'size'	=>	30,
					'value'	=>	$entry['gloss']
				)
			),
			array(
				'type'	=>	'textarea',
				'name'	=>	'comments',
				'label'	=>	'Comments',
				'data'	=>	array(
					'rows'	=>	3,
					'cols'	=>	50,
					'value'	=>	$entry['comments']
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

		Forms::create_form( 'edit-morpheme', ROOT . 'dictionary.php?mode=edit&amp;morpheme_id=' . $morpheme_id, $form_data );
	}

	public function view_dictionary()
	{
		global $Database, $User;

		$columns = $headers = $rows = array();

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
			'content'	=>	'Morpheme'
		);

		$columns[4] = array(
			'style'	=>	'width: 30%;'
		);
		$headers[4] = array(
			'content'	=>	'Comments'
		);

		$sql = 'SELECT d.*
			FROM dictionary d
			ORDER BY d.morpheme ASC, d.parent_morpheme ASC, d.gloss ASC'; // TODO: Change this once better morpheme relationship tracking is implemented.

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
				'content'	=>	array(
					0	=>	array(
						'class'		=>	'edit',
						'content'	=>	'<a href="' . ROOT . 'dictionary.php?mode=edit&amp;morpheme_id=' . $row['morpheme_id'] . '">edit</a>'
					),
					1	=>	array(
						'content'	=>	'(' . $row['morpheme_id'] . ')'
					),
					2	=>	/*( $language_where ) ? array() :*/ array(
//						'content'	=>	$this->format_language( $row['language'] )
					),
					3	=>	array(
						'content'	=>	'<span class="transcription"><a href="' . ROOT . 'records.php?mode=view&amp;morpheme=' . htmlentities( $row['morpheme'], ENT_QUOTES, 'UTF-8' ) . '">' . $this->convert_newlines( htmlentities( $row['morpheme'], ENT_QUOTES, 'UTF-8' ) ) . '</a></span><br /><span class="gloss">' . $this->convert_newlines( htmlentities( $row['gloss'], ENT_QUOTES, 'UTF-8' ) ) . '</span>'
					),
					4	=>	array(
						'content'	=>	$this->convert_newlines( htmlentities( $row['comments'], ENT_QUOTES, 'UTF-8' ) )
					),
				)
			);
		}

		$Database->free_result( $result );

		$this->print_list_table( ROOT . 'dictionary.php', $columns, $headers, $rows );
	}

/*	public function export_records()
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
	}*/
}

?>