<?php

/**
 * dbox :: inc/lib/lib.export.php
 *
 * This contains all of the export functions.
 *
 * @package dbox
 * @copyright (C) 2006-2010, 2011-2012 Gordon P. Hemsley
 * @license docs/LICENSE Mozilla Public License, v. 2.0
 */

/**
 * Export
 *
 * Child class for tasks related to exporting
 *
 * @package dbox
 */
class Export extends Base
{
/*	function __construct()
	{
		global $Changes, $User;

		parent::__construct();
	}*/

	private function _csv_escape( $string )
	{
		return '"' . str_replace( '"', '""', $string ) . '"';
	}

	private function _send_export_headers( $format, $export, $download )
	{
		$content_type = $file_ext = '';

		switch( $format )
		{
			case 'toolbox':

			break;

			case 'csv':
				$content_type = 'text/csv; charset=UTF-8; header=present';
				$file_ext = '.csv';
			break;

			case 'txt':
			default:
				$content_type = 'text/plain; charset=UTF-8';
				$file_ext = '.txt';
			break;
		}

		$content_disposition = ( $download ) ? 'attachment' : 'inline';

		header( 'Content-Type: ' . $content_type );
		header( 'Content-Disposition: ' . $content_disposition . '; filename="dbox_' . $export . $file_ext . '"' );
	}

	private function _get_export_data( $format, $export, $download )
	{
		global $Database;

		$export_data = array();

		switch( $export )
		{
			case 'lexicon':
				$sql = 'SELECT d.*
					FROM dictionary d
					ORDER BY d.morpheme ASC, d.parent_morpheme ASC, d.gloss ASC';
			break;

			case 'elicitations':
			default:
				$sql = 'SELECT r.*, u.name
					FROM records r
					LEFT JOIN ( users u )
						ON ( r.creator_id = u.user_id )
					ORDER BY r.creation_time ASC';
			break;
		}

		$result = $Database->query( $sql );

		if( !$Database->has_result( $result ) )
		{
			// Oh well.
		}

		while( $row = $Database->fetch_assoc( $result ) )
		{
			$export_data[] = $row;
		}

		$Database->free_result( $result );

		return $export_data;
	}

	private function _format_export_data( $export_data, $format, $export, $download )
	{
		if( empty( $export_data ) || !is_array( $export_data ) )
		{
			return FALSE;
		}

		switch( $format )
		{
			case 'toolbox':

			break;

			case 'csv':
			case 'txt':
			default:
				print implode( ',', array_keys( $export_data[0] ) ) . "\r\n";

				foreach( $export_data as $row )
				{
					print implode( ',', array_map( 'Export::_csv_escape', $row ) ) . "\r\n";
				}
			break;
		}
	}

	public function export_data( $format = 'txt', $export = 'elicitations', $download = FALSE )
	{
		$this->_send_export_headers( $format, $export, $download );

		$export_data = $this->_get_export_data( $format, $export, $download );

		$this->_format_export_data( $export_data, $format, $export, $download );

		exit;
	}

	public function print_export_table( $export )
	{
		$formats = array(
			array(
				'format'		=>	'txt',
				'description'	=>	'Plain Text',
			),
			array(
				'format'		=>	'csv',
				'description'	=>	'Comma-Separated Values',
			),
			/*array(
				'type'	=>	'toolbox',
				'name'	=>	'Toolbox',
			),*/
		);

		$columns = $headers = $rows = array();

		$columns[0] = array(
			'style'	=>	'width: 40%; border-right: 2px solid black;'
		);
/*		$headers[0] = array(
		);*/

		$columns[1] = array(
			'style'	=>	'width: 30%;'
		);
/*		$headers[1] = array(
		);*/

		$columns[2] = array(
			'style'	=>	'width: 30%;'
		);
/*		$headers[2] = array(
		);*/

		foreach( $formats as $format )
		{
			$rows[] = array(
				'content'	=>	array(
					0	=>	array(
						'content'	=>	htmlentities( $format['description'], ENT_QUOTES, 'UTF-8' )
					),
					1	=>	array(
						'content'	=>	'<a href="' . ROOT . 'export.php?mode=view&amp;format=' . $format['format'] . '&amp;export=' . htmlentities( rawurlencode( $export ), ENT_QUOTES, 'UTF-8' ) . '" target="_blank">View</a>'
					),
					2	=>	array(
						'content'	=>	'<a href="' . ROOT . 'export.php?mode=download&amp;format=' . $format['format'] . '&amp;export=' . htmlentities( rawurlencode( $export ), ENT_QUOTES, 'UTF-8' ) . '" target="_blank">Download</a>'
					),
				)
			);
		}

		$this->print_list_table( ROOT . 'export.php', $columns, $headers, $rows );
	}

	public function view_export_options()
	{
		print "\t" . '<h2>Elicitations</h2>' . "\n";

		$this->print_export_table( 'elicitations' );

		print "\t" . '<h2>Lexicon</h2>' . "\n";

		$this->print_export_table( 'lexicon' );
	}
}

?>