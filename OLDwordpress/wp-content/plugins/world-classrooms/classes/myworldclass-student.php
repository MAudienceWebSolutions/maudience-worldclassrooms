<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * MyWorldClass_Student Class
 * @since 1.0
 * @version 1.0
 */
if ( ! class_exists( 'MyWorldClass_Student' ) ) :
	class MyWorldClass_Student {

		public $student_id;
		public $user;
		public $account = 'student';

		public $prefs;

		public $balance;
		public $payment_history = array();

		function __construct( $user_id = NULL ) {

			$this->student_id = $user_id;

			if ( $user_id !== NULL )
				$this->load_user();

			$this->prefs = mywclass_get_settings();

		}

		public function load_user() {

			$this->user = mywclass_get_userdata( $this->student_id );
			$this->balance = mywclass_get_users_balance( $this->student_id );

			if ( user_can( $this->student_id, 'edit_users' ) )
				$this->account = 'staff';

		}

		public function display_name() {

			return $this->user->first_name . ' ' . $this->user->last_name;

		}

		public function display_dob() {

			if ( $this->user->user_dob != '' )
				return date( get_option( 'date_format' ), strtotime( $this->user->user_dob ) );
			else
				return '-';

		}

		public function display_user_type() {

			if ( $this->account == 'staff' )
				return 'Staff';
			else
				return 'Student';

		}

		public function display_balance() {

			return '$ ' . number_format( $this->balance, 2, '.', ' ' );

		}

	}
endif;

?>