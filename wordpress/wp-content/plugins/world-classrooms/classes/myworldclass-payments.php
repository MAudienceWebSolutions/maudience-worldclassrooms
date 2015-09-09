<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * MyWorldClass_Payments Class
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'MyWorldClass_Payments' ) ) :
	class MyWorldClass_Payments {

		public $student_id;
		public $tour_code = false;
		public $status = '';

		public $user;
		public $cost;
		public $balance;
		public $remaining;

		public $history;

		function __construct( $user_id = NULL ) {

			$this->student_id = $user_id;

			if ( $user_id !== NULL ) {
				$this->load_user();
				$this->load_tour();
				$this->load_history();
			}

			$this->prefs = mywclass_get_settings();
			$this->date_format = get_option( 'date_format' );

		}

		public function load_user() {

			$this->user    = mywclass_get_userdata( $this->student_id );
			$this->balance = mywclass_get_users_balance( $this->student_id );

			if ( isset( $this->user->tour_code ) )
				$this->tour_code = $this->user->tour_code;

			if ( user_can( $this->student_id, 'edit_users' ) )
				$this->account = 'staff';

		}

		public function load_tour() {

			if ( ! isset( $this->user->tour_code ) ) return;

			$tour_id = mywclass_get_tour_by_code( $this->user->tour_code );

			$this->cost = mywclass_get_cost( $tour_id, $this->student_id );

			$this->remaining = 0.00;
			if ( $this->cost > 0 )
				$this->remaining = $this->cost - $this->balance;

		}

		public function load_history() {

			global $wpdb;
			$posts = $wpdb->get_results( "
				SELECT *  
				FROM {$wpdb->posts} 
				WHERE post_type = 'tour_payment' 
				AND post_author = {$this->student_id} 
				AND post_status != 'trash' 
				ORDER BY post_date ASC;" );

			$history = array();
			if ( ! empty( $posts ) ) {

				foreach ( $posts as $post ) {

					$history[ strtotime( $post->post_date ) ] = array(
						'txt_id' => $post->post_title,
						'amount' => get_post_meta( $post->ID, 'amount', true ),
						'status' => get_post_meta( $post->ID, 'status', true ),
						'note'   => get_post_meta( $post->ID, 'note', true ),
						'errors' => get_post_meta( $post->ID, 'errors', true ),
						'tour_id' => get_post_meta( $post->ID, 'tour_id', true ),
						'type' => get_post_meta( $post->ID, 'type', true )
					);

				}

			}

			$this->payment_history = $history;

		}

		public function get_transaction( $transaction_id ) {

			$post = get_page_by_title( $transaction_id, OBJECT, 'tour_payment' );
			if ( ! isset( $post->ID ) )
				return false;

			return $post->ID;

		}

		public function add_payment( $transaction_id, $status, $data = array() ) {

			$data = wp_parse_args( $data, array(
				'note'    => '',
				'amount'  => 0.00,
				'errors'  => '',
				'tour_id' => '',
				'type'    => ''
			) );

			$post_id = $this->get_transaction( $transaction_id );
			if ( $post_id === false ) {

				$post_id = wp_insert_post( array(
					'post_type'   => 'tour_payment',
					'post_author' => $this->student_id,
					'post_title'  => $transaction_id,
					'post_status' => 'publish'
				) );

				if ( $post_id !== NULL && ! is_wp_error( $post_id ) ) {

					add_post_meta( $post_id, 'status',  $status, true );
					add_post_meta( $post_id, 'note',    $data['note'], true );
					add_post_meta( $post_id, 'amount',  $data['amount'], true );
					add_post_meta( $post_id, 'errors',  $data['errors'], true );
					add_post_meta( $post_id, 'tour_id', $data['tour_id'], true );
					add_post_meta( $post_id, 'type',    $data['type'], true );

				}

			}
			else {

				update_post_meta( $post_id, 'status',  $status );
				update_post_meta( $post_id, 'note',    $data['note'] );
				update_post_meta( $post_id, 'amount',  $data['amount'] );
				update_post_meta( $post_id, 'errors',  $data['errors'] );
				update_post_meta( $post_id, 'tour_id', $data['tour_id'] );
				update_post_meta( $post_id, 'type',    $data['type'] );

			}

		}

		public function get_types() {

			return array(
				''       => 'Unknown',
				'card'   => 'Credit Card',
				'check'  => 'Check/Cash',
				'credit' => 'Credit'
			);

		}

	}
endif;

?>