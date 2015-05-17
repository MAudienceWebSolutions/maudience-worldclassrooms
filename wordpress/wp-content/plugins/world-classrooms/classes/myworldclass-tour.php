<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * MyWorldClass_Tour Class
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'MyWorldClass_Tour' ) ) :
	class MyWorldClass_Tour {

		public $post_id;
		public $tour_code;

		public $post;
		public $status = '';

		public $cost;
		public $cost_adult;
		public $start_date;
		public $pay_day;
		public $attendees;

		public $prefs;
		public $date_format;

		function __construct( $post_id = NULL ) {

			$this->post_id = $post_id;

			if ( $post_id !== NULL ) {
				$this->load_post();
				$this->get_attendees();
			}

			$this->prefs = mywclass_get_settings();
			$this->date_format = get_option( 'date_format' );

		}

		public function load_post() {

			$this->post = get_post( $this->post_id );
			$this->status = $this->post->post_status;

			$this->cost = get_post_meta( $this->post_id, 'cost', true );
			if ( $this->cost == '' ) {
				$this->cost = 0.00;
				update_post_meta( $this->post_id, 'cost', $this->cost );
			}

			$this->cost_adult = get_post_meta( $this->post_id, 'cost_adult', true );
			if ( $this->cost_adult == '' ) {
				$this->cost_adult = 0.00;
				update_post_meta( $this->post_id, 'cost_adult', $this->cost_adult );
			}

			$this->tour_code = get_post_meta( $this->post_id, 'tour_code', true );
			$this->start_date = get_post_meta( $this->post_id, 'start_date', true );
			$this->last_pay_date = get_post_meta( $this->post_id, 'last_pay_date', true );

		}

		public function get_attendees( $recount = false ) {

			if ( ! $recount ) {

				$this->attendees = (array) get_post_meta( $this->post_id, 'attendees', true );
				$this->attendee_count = count( $this->attendees );

			}
			else {

				if ( $this->tour_code == '' ) return;

				global $wpdb;
				$this->attendees = $wpdb->get_col( "
					SELECT user_id 
					FROM {$wpdb->usermeta} 
					WHERE meta_key = 'tour_code' 
					AND meta_value = '{$this->tour_code}';" );

				$this->attendee_count = count( $this->attendees );
				update_post_meta( $this->post_id, 'attendees', $this->attendees );

			}

		}

		public function display_tour_code() {

			if ( $this->tour_code != '' )
				return $this->tour_code;
			else
				return '-';

		}

		public function display_cost( $user_id = NULL ) {

			if ( $user_id === NULL )
				return '$ ' . number_format( $this->cost, 2, '.', ' ' );

			return '$ ' . mywclass_get_cost( $this->post_id, $user_id );

		}

		public function display_notice() {

			$notice = get_post_meta( $this->post_id, 'notice', true );
			if ( $notice == '' ) return '';
			
			$notice = wpautop( wptexturize( $notice ) );
			return '<div class="tour-notice">' . $notice . '</div>';

		}

		public function display_start_date() {

			if ( $this->start_date != '' )
				return date( $this->date_format, strtotime( $this->start_date ) );
			else
				return '-';

		}

		public function display_last_pay_date() {

			if ( $this->last_pay_date != '' )
				return date( $this->date_format, strtotime( $this->last_pay_date ) );
			else
				return '-';

		}

		public function display_attendee_count_admin() {

			$this->get_attendees( true );
			if ( $this->attendee_count == 0 )
				return 0;

			else {

				$url = add_query_arg( array( 'tour_code' => $this->tour_code ), admin_url( 'users.php' ) );
				return '<a href="' . $url . '">' . $this->attendee_count . '</a>';

			}

		}

	}
endif;

?>