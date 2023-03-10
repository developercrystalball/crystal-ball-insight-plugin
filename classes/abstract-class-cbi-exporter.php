<?php
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class CBI_Exporter {
	/**
	 * Exporter name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Exporter unique identifier
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Output formatted data for download
	 *
	 * @param array $data Array of data to output.
	 * @param array $columns Column names included in data set.
	 * @return void
	 */
	abstract public function write( $data, $columns );
}
