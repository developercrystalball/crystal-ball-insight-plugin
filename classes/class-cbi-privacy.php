<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CBI_Privacy
 * @since 1.0.0
 */
class CBI_Privacy {

	public function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
	}

	public function register_exporter( $exporters ) {
		$exporters['crystal-ball'] = array(
			'exporter_friendly_name' => __( 'Crystal Ball Plugin', 'crystal-ball-insight' ),
			'callback' => array( $this, 'wp_exporter' ),
		);
		return $exporters;
	}

	public function wp_exporter( $email_address, $page = 1 ) {
		$number = 500;
		$page = (int) $page;

		$export_items = array();

		$user = get_user_by( 'email', $email_address );

		if ( ! $user ) {
			return array(
				'data' => array(),
				'done' => true,
			);
		}

		global $wpdb;

		$items = $wpdb->get_results( $wpdb->prepare(
			"SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->crystal_ball
			WHERE `user_id` = %d
			ORDER BY `hist_time` ASC
			LIMIT %d, %d;",
			$user->ID, $page, $number
		) );

		$found_rows = $wpdb->get_var( 'SELECT FOUND_ROWS();' );

		$group_id = 'crystal-ball';
		$group_label = __( 'Crystal Ball', 'crystal-ball-insight' );

		foreach ( $items as $item ) {
			$item_id = "crystal-ball-{$item->histid}";
			$created = date( 'Y-m-d H:i:s', $item->hist_time );
			$data = array(
				array(
					'name' => __( 'Time', 'crystal-ball-insight' ),
					'value' => get_date_from_gmt( $created, 'Y/m/d h:i:s A' ),
				),
				array(
					'name' => __( 'Action', 'crystal-ball-insight' ),
					'value' => $this->get_action_label( $item->action ),
				),
				array(
					'name' => __( 'Topic', 'crystal-ball-insight' ),
					'value' => $item->object_type,
				),
				array(
					'name' => __( 'Context', 'crystal-ball-insight' ),
					'value' => $item->object_subtype,
				),
				array(
					'name' => __( 'Meta', 'crystal-ball-insight' ),
					'value' => $item->object_name,
				),
				array(
					'name' => __( 'IP', 'crystal-ball-insight' ),
					'value' => $item->hist_ip,
				),
			);

			$export_items[] = array(
				'group_id' => $group_id,
				'group_label' => $group_label,
				'item_id' => $item_id,
				'data' => $data,
			);
		}

		$done = $found_rows < $number;
		return array(
			'data' => $export_items,
			'done' => $done,
		);
	}

	public function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content = sprintf( __( 'If you are a registered user, we save your content activity like create/update/delete posts and comments.', 'crystal-ball-insight' ) );

		wp_add_privacy_policy_content(
			__( 'Crystal Ball', 'crystal-ball-insight' ),
			wp_kses_post( wpautop( $content, false ) )
		);
	}

	public function get_action_label( $action ) {
		return ucwords( str_replace( '_', ' ', __( $action, 'crystal-ball-insight' ) ) );
	}
}
