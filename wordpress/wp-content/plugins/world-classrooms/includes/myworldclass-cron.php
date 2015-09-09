<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Cron: Hourly Tasks
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_cron_run_hourly_tasks' ) ) :
	function mywclass_cron_run_hourly_tasks() {

		global $wpdb;

		$table = $wpdb->prefix . 'tour_signups';
		$limit = (int) current_time( 'timestamp' ) - 3600;
		$expired = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$table} WHERE time < %d AND status = 'new';", $limit ) );

		if ( ! empty( $expired ) ) {
			foreach ( $expired as $entry_id ) {
				$wpdb->delete(
					$table,
					array( 'id' => $entry_id ),
					array( '%d' )
				);
			}
		}

	}
endif;

/**
 * Cron: Daily Tasks
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_cron_run_daily_tasks' ) ) :
	function mywclass_cron_run_daily_tasks() {
	
	}
endif;

?>