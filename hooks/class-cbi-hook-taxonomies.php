<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBI_Hook_Taxonomies extends CBI_Hook_Base {

	public function hooks_created_edited_deleted_term( $term_id, $tt_id, $taxonomy, $deleted_term = null ) {
		if ( 'nav_menu' === $taxonomy )
			return;

		if ( 'delete_term' === current_filter() )
			$term = $deleted_term;
		else
			$term = get_term( $term_id, $taxonomy );

		if ( $term && ! is_wp_error( $term ) ) {
			if ( 'edited_term' === current_filter() ) {
				$action = 'updated';
			} elseif ( 'delete_term' === current_filter() ) {
				$action  = 'deleted';
				$term_id = '';
			} else {
				$action = 'created';
			}

			cbi_insert_log( array(
				'action'         => $action,
				'object_type'    => 'Taxonomies',
				'object_subtype' => $taxonomy,
				'object_id'      => $term_id,
				'object_name'    => $term->name,
			) );
		}
	}
	
	public function __construct() {
		add_action( 'created_term', array( &$this, 'hooks_created_edited_deleted_term' ), 10, 3 );
		add_action( 'edited_term', array( &$this, 'hooks_created_edited_deleted_term' ), 10, 3 );
		add_action( 'delete_term', array( &$this, 'hooks_created_edited_deleted_term' ), 10, 4 );

		parent::__construct();
	}

}