<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CBI_Hook_Options extends CBI_Hook_Base {

	public function hooks_updated_option( $option, $oldvalue, $_newvalue ) {
		$whitelist_options = apply_filters( 'cbi_whitelist_options', array(
			'blogname',
			'blogdescription',
			'siteurl',
			'home',
			'admin_email',
			'users_can_register',
			'default_role',
			'WPLANG',
			'timezone_string',
			'date_format',
			'time_format',
			'start_of_week',

			'use_smilies',
			'use_balanceTags',
			'default_category',
			'default_post_format',
			'mailserver_url',
			'mailserver_login',
			'mailserver_pass',
			'default_email_category',
			'ping_sites',

			'show_on_front',
			'page_on_front',
			'page_for_posts',
			'posts_per_page',
			'posts_per_rss',
			'rss_use_excerpt',
			'blog_public',

			'default_pingback_flag',
			'default_ping_status',
			'default_comment_status',
			'require_name_email',
			'comment_registration',
			'close_comments_for_old_posts',
			'close_comments_days_old',
			'thread_comments',
			'thread_comments_depth',
			'page_comments',
			'comments_per_page',
			'default_comments_page',
			'comment_order',
			'comments_notify',
			'moderation_notify',
			'comment_moderation',
			'comment_whitelist',
			'comment_max_links',
			'moderation_keys',
			'blacklist_keys',
			'show_avatars',
			'avatar_rating',
			'avatar_default',

			'thumbnail_size_w',
			'thumbnail_size_h',
			'thumbnail_crop',
			'medium_size_w',
			'medium_size_h',
			'large_size_w',
			'large_size_h',
			'uploads_use_yearmonth_folders',

			'permalink_structure',
			'category_base',
			'tag_base',

			'wp_page_for_privacy_policy',

			'sidebars_widgets',

			'logs_lifespan',
		) );

		if ( ! in_array( $option, $whitelist_options ) ) {
			return;
		}

		$this->insert_log( $option );
	}

	private function insert_log( $option_name, $context = '' ) {
		cbi_insert_log( array(
			'action' => 'updated',
			'object_type' => 'Options',
			'object_name' => $option_name,
			'object_subtype' => $context,
		) );
	}

	public function hooks_cbi_options( $old_values, $new_values ) {
		foreach ( (array) $old_values as $option_key => $old_value ) {
			$is_new_value = ! empty( $new_values[ $option_key ] ) && $new_values[ $option_key ] !== $old_value;

			if ( ! $is_new_value ) {
				continue;
			}

			$this->insert_log( $option_key, 'Crystal Ball' );
		}
	}

	public function __construct() {
		add_action( 'updated_option', array( &$this, 'hooks_updated_option' ), 10, 3 );
		add_action( 'update_option_log_settings', array( $this, 'hooks_cbi_options' ), 10, 2 );

		parent::__construct();
	}

}