<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBI_Exporter_csv {
    /**
	 * Exporter name
	 *
	 * @var string
	 */
	public $name = 'CSV';

	/**
	 * Exporter ID
	 *
	 * @var string
	 */
	public $id = 'csv';

	const FORMULAS_START_CHARACTERS = [ '=', '-', '+', '@', "\t", "\r" ];

	/**
	 * Writes CSV data for download
	 *
	 * @param array $data Array of data to output.
	 * @param array $columns Column names included in data set.
	 * @return void
	 */
	public function write( $data, $columns ) {
		$is_test_mode_off = ! defined( 'CBI_TESTMODE' ) || ( defined( 'CBI_TESTMODE' ) && ! CBI_TESTMODE );

		if ( $is_test_mode_off ) {
			header( 'Content-type: text/csv' );
			header( 'Content-Disposition: attachment; filename="crystal-ball-export.csv"' );
		}

		$fp = fopen( 'php://output', 'w' );

		fputcsv( $fp, $columns );

		foreach ( $data as $row ) {
			$encoded_row = $this->get_encoded_row( $row );
			fputcsv( $fp, $encoded_row );
		}

		fclose( $fp );

		if ( $is_test_mode_off ) {
			exit;
		}
	}

	private function get_encoded_row( $row ) {
		$result = [];

		foreach ( $row as $key => $value ) {
			$encoded_value = $value;
			if ( in_array( substr( (string) $value, 0, 1 ), self::FORMULAS_START_CHARACTERS, true ) ) {
				$encoded_value = "'" . $value;
			}

			$result[ $key ] = $encoded_value;
		}

		return $result;
	}
}
