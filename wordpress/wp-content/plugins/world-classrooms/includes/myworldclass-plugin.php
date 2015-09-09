<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Plugin Activation
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_mycars_plugin_activation' ) ) :
	function mywclass_mycars_plugin_activation() {

		mywclass_tour_signup_install_db();

		mywclass_register_tour_type();

		flush_rewrite_rules();

	}
endif;

/**
 * Plugin Deactivation
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_mycars_plugin_deactivation' ) ) :
	function mywclass_mycars_plugin_deactivation() {

		wp_clear_scheduled_hook( 'mywclass_hourly_task' );
		wp_clear_scheduled_hook( 'mywclass_daily_task' );

	}
endif;

/**
 * Plugin Database
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_signup_install_db' ) ) :
	function mywclass_tour_signup_install_db() {

		if ( get_option( 'mywclass_tour_signups_db', false ) != '1.0' ) {

			global $wpdb;

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$table = $wpdb->prefix . 'tour_signups';

			$wpdb->hide_errors();

			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				if ( ! empty( $wpdb->charset ) )
					$collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";
				if ( ! empty( $wpdb->collate ) )
					$collate .= " COLLATE {$wpdb->collate}";
			}

			// Log structure
			$sql = "
				id                  INT(11) NOT NULL AUTO_INCREMENT, 
				tour_id             INT(11) DEFAULT 0, 
				user_id             INT(11) DEFAULT 0, 
				step                INT(11) DEFAULT 1, 
				status              LONGTEXT DEFAULT '', 
				travelers           LONGTEXT DEFAULT '', 
				billing             LONGTEXT DEFAULT '', 
				parents             LONGTEXT DEFAULT '', 
				plan                LONGTEXT DEFAULT '', 
				payment             DECIMAL(22,2) DEFAULT 0, 
				payment_id          LONGTEXT DEFAULT '', 
				customer_id         LONGTEXT DEFAULT '', 
				scholarship         LONGTEXT DEFAULT '', 
				time                INT(11) DEFAULT 0, 
				traveler_count      INT(11) DEFAULT 0, 
				PRIMARY KEY  (id), 
				UNIQUE KEY id (id)"; 

			// Insert table
			dbDelta( "CREATE TABLE IF NOT EXISTS {$table} ( " . $sql . " ) $collate;" );

			update_option( 'mywclass_tour_signups_db', '1.0' );

		}

	}
endif;

?>