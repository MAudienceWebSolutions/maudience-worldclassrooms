<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * MyWorldClass_Tour Class
 * @since 1.1
 * @version 1.0
 */
if ( ! class_exists( 'MyWorldClass_Tour' ) ) :
	class MyWorldClass_Tour {

		public $post_id;
		public $tour_code;
		public $db;

		public $post;
		public $status = '';

		public $cost;
		public $cost_adult;
		public $start_date;
		public $pay_day;
		public $attendees;

		public $prefs;
		public $date_format;

		public $signup_id;
		public $signup;
		public $submitted;
		public $is_signup = false;
		public $singup_step = 1;
		public $payment;
		public $payment_errors = array();

		/**
		 * Constructor
		 * @since 1.0
		 * @version 1.0
		 */
		function __construct( $post_id = NULL ) {

			$this->post_id = $post_id;

			if ( $post_id !== NULL ) {
				$this->load_post();
				$this->get_attendees();
			}

			global $wpdb;

			$this->db          = $wpdb->prefix . 'tour_signups';
			$this->prefs       = mywclass_get_settings();
			$this->date_format = get_option( 'date_format' );
			$this->user_id     = get_current_user_id();

		}

		/**
		 * Load Post
		 * @since 1.0
		 * @version 1.0
		 */
		public function load_post() {

			$this->post   = get_post( $this->post_id );
			if ( ! isset( $this->post->post_status ) ) {
				$this->post = false;
				return;
			}
			$this->status = $this->post->post_status;

			$this->cost = get_post_meta( $this->post_id, 'cost', true );
			if ( $this->cost == '' ) {
				$this->cost = 0.00;
				update_post_meta( $this->post_id, 'cost', $this->cost );
			}

			$this->minimum = get_post_meta( $this->post_id, 'minimum', true );
			if ( $this->minimum == '' ) {
				$this->minimum = 0.00;
				update_post_meta( $this->post_id, 'minimum', $this->minimum );
			}

			$this->cost_adult = get_post_meta( $this->post_id, 'cost_adult', true );
			if ( $this->cost_adult == '' ) {
				$this->cost_adult = 0.00;
				update_post_meta( $this->post_id, 'cost_adult', $this->cost_adult );
			}

			$this->sixty_days = get_post_meta( $this->post_id, 'sixty_days', true );
			if ( $this->sixty_days == '' ) {
				$this->sixty_days = 0.00;
				update_post_meta( $this->post_id, 'sixty_days', $this->sixty_days );
			}

			$this->school        = get_post_meta( $this->post_id, 'school', true );
			$this->tour_code     = get_post_meta( $this->post_id, 'tour_code', true );
			$this->location      = get_post_meta( $this->post_id, 'location', true );

			$this->teacher_name  = get_post_meta( $this->post_id, 'teacher_name', true );
			$this->teacher_email = get_post_meta( $this->post_id, 'teacher_email', true );

			$this->start_date    = get_post_meta( $this->post_id, 'start_date', true );
			$this->end_date      = get_post_meta( $this->post_id, 'end_date', true );
			$this->last_pay_date = get_post_meta( $this->post_id, 'last_pay_date', true );

			$this->downloads     = get_post_meta( $this->post_id, 'download_content', true );

		}

		/**
		 * Setup Signup
		 * Used either to create a new signup via the signup form
		 * or used to load a particular users signup for the tour.
		 * @since 1.0
		 * @version 1.0
		 */
		public function setup_signup( $signup_id = 'new' ) {

			if ( $signup_id == 'new' )
				$this->signup_id = $this->new_signup_id();

			else
				$this->signup_id = absint( $signup_id );

			$this->signup              = $this->setup_signup_data();
			$this->signup_step         = absint( $this->signup->step );

			$this->confirmed_name = false;
			if ( $this->signup_step > 1 )
				$this->confirmed_name = true;

			$this->attending_travelers = 1;

		}

		/**
		 * New Signup ID
		 * Will either create a new signup id or
		 * load a users signup id (as users can only signup for a tour once).
		 * @since 1.0
		 * @version 1.0
		 */
		public function new_signup_id() {

			global $wpdb;

			$give_cookie = false;
			$now         = current_time( 'timestamp' );

			// If we are logged in, lets check the database and see if we have a pending signup.
			// If there is one, we continue on with this signup instead of a new one.
			if ( is_user_logged_in() ) {

				$check = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->db} WHERE tour_id = %d AND user_id = %d;", $this->post_id, $this->user_id ) );

				if ( $check !== NULL )
					return $check;

			}

			// For visitors, we do the same thing as for members but we check for a cookie instead.
			// A cookie should exists for a visitor that has been here before.
			else {

				if ( isset( $_COOKIE['myworldclass-tour-' . $this->post_id ] ) ) {

					$check = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->db} WHERE id = %d AND tour_id = %d;", $_COOKIE['myworldclass-tour-' . $this->post_id ], $this->post_id ) );

					// If the signup has not yet expired, we continue on.
					if ( $check !== NULL ) {

						// Update time to prevent deletion if we are returning within one hour.
						$wpdb->update(
							$this->db,
							array( 'time' => $now ),
							array( 'id' => $check ),
							array( '%d' ),
							array( '%d' )
						);

						return $check;

					}

					// Signup expired, delete cookie
					if ( ! headers_sent() )
						setcookie( 'myworldclass-tour-' . $this->post_id, $wpdb->insert_id, time() - 3600, '/' );

				}

				// Ok so no cookie exists, lets try give the user one and create a new signup.
				else {
					$give_cookie = true;
				}

			}

			// Create a new signup entry that is valid for one hour if we have come this far.
			$wpdb->insert(
				$this->db,
				array(
					'tour_id' => $this->post_id,
					'user_id' => $this->user_id,
					'step'    => 1,
					'status'  => 'new',
					'time'    => $now
				)
			);

			// If needed, give the user a cookie
			if ( $give_cookie && ! headers_sent() )
				setcookie( 'myworldclass-tour-' . $this->post_id, $wpdb->insert_id, time() + 3600, '/' );

			return $wpdb->insert_id;

		}

		/**
		 * Update Signup
		 * @since 1.0
		 * @version 1.0
		 */
		public function update_signup( $step = 1 ) {

			if ( $step == 1 ) {

				$data = array( 'travelers' => serialize( $this->signup->travelers ), 'step' => $this->signup_step );
				$prep = array( '%s', '%d' );

			}

			elseif ( $step == 2 ) {

				$data = array( 'parents' => serialize( $this->signup->parents ), 'step' => $this->signup_step );
				$prep = array( '%s', '%d' );

			}

			elseif ( $step == 3 ) {

				$data = array( 'billing' => serialize( $this->signup->billing ), 'step' => $this->signup_step );
				$prep = array( '%s', '%d' );

			}

			elseif ( $step == 4 ) {

				$data = array( 'plan' => $this->signup->plan, 'step' => $this->signup_step );
				$prep = array( '%s', '%d' );

			}

			global $wpdb;

			$wpdb->update(
				$this->db,
				$data,
				array( 'id' => $this->signup_id ),
				$prep,
				array( '%d' )
			);

		}

		/**
		 * Get Default Setup Data
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_default_setup_data( $section = 'traveler' ) {

			$traveler = $parent = $billing = array(
				'first_name'  => '',
				'last_name'   => '',
				'middle_name' => '',
				'gender'      => '',
				'DOB'         => 0,
				'type'        => 'student',
				'user_email'  => '',
				'phone'       => '',
				'phone_type'  => '',
				'address1'    => '',
				'address2'    => '',
				'city'        => '',
				'state'       => '',
				'zip'         => ''
			);

			unset( $parent['DOB'] );
			unset( $parent['middle_name'] );
			unset( $parent['gender'] );

			$parent['relationship']   = '';
			$parent['same_as_travel'] = 0;
			$parent['type']           = 'parent';
			$parent['phone_alt']      = '';
			$parent['phone_alt_type'] = '';

			unset( $billing['DOB'] );
			unset( $billing['middle_name'] );
			unset( $billing['gender'] );
			unset( $billing['type'] );
			unset( $billing['phone'] );
			unset( $billing['phone_type'] );

			$billing['scholarship_code'] = '';

			$payment = array(
				'plan'   => 'auto',
				'manual' => '',
				'card'   => '',
				'name'   => '',
				'exp_mm' => '',
				'exp_yy' => '',
				'cvv'    => '',
				'terms'  => 0
			);

			$default = array(
				'traveler' => $traveler,
				'parent'   => $parent,
				'billing'  => $billing,
				'payment'  => $payment
			);

			if ( ! array_key_exists( $section, $default ) )
				return array();

			return $default[ $section ];

		}

		/**
		 * Setup Signup Data
		 * @since 1.0
		 * @version 1.0
		 */
		public function setup_signup_data() {

			global $wpdb;

			$traveler = $this->get_default_setup_data();
			$parent   = $this->get_default_setup_data( 'parent' );
			$signup   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->db} WHERE id = %d;", $this->signup_id ) );
			if ( ! isset( $signup->id ) ) return;

			$signup->travelers = (array) maybe_unserialize( $signup->travelers );
			if ( empty( $signup->travelers ) ) {

				$signup->travelers = array(
					0 => $traveler,
					1 => $traveler,
					2 => $traveler,
					3 => $traveler,
					4 => $traveler
				);
			}
			else {

				$_travelers = array();
				foreach ( $signup->travelers as $row_id => $travelers )
					$_travelers[ $row_id ] = wp_parse_args( $travelers, $traveler );

				$signup->travelers = $_travelers;

			}

			$signup->parents   = (array) maybe_unserialize( $signup->parents );
			if ( empty( $signup->parents ) ) {

				$signup->parents = array(
					0 => $parent
				);
			}
			else {

				$_parents = array();
				foreach ( $signup->parents as $row_id => $parents )
					$_parents[ $row_id ] = wp_parse_args( $parents, $parent );

				$signup->parents = $_parents;

			}

			$signup->billing = (array) maybe_unserialize( $signup->billing );

			return $signup;

		}

		/**
		 * Validate Signup Step
		 * @since 1.0
		 * @version 1.0
		 */
		public function validate_step( $data ) {

			$this->errors     = array();
			$this->step_valid = false;

			if ( $this->signup_step == 1 ) {

				$attendees = array();
				$this->attending_travelers = absint( $data['number_of_travelers'] );
				$default = $this->get_default_setup_data();

				for ( $loop = 0; $loop < $this->attending_travelers; $loop++ ) {

					$attendee = array();
					$traveler = $data['travelers'][ $loop ];

					if ( sanitize_text_field( $traveler['first_name'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-first-name'] = __( 'First name can not be empty.', 'myworldclass' );
					else
						$attendee['first_name'] = sanitize_text_field( $traveler['first_name'] );

					$attendee['middle_name'] = sanitize_text_field( $traveler['middle_name'] );

					if ( sanitize_text_field( $traveler['last_name'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-last-name'] = __( 'Last name can not be empty.', 'myworldclass' );
					else
						$attendee['last_name'] = sanitize_text_field( $traveler['last_name'] );

					if ( ! isset( $traveler['confirm_name'] ) || $traveler['confirm_name'] != 1 )
						$this->errors['tour-signup-' . $loop . '-confirm-name'] = __( 'Please confirm that the name you provided is correct.', 'myworldclass' );

					if ( sanitize_text_field( $traveler['gender'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-gender'] = __( 'Please select your gender.', 'myworldclass' );
					else
						$attendee['gender'] = sanitize_text_field( $traveler['gender'] );

					$date_of_birth = array();

					if ( sanitize_text_field( $traveler['dob_m'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-dob-month'] = __( 'Select month.', 'myworldclass' );
					else
						$date_of_birth[] = sanitize_text_field( $traveler['dob_m'] );

					if ( sanitize_text_field( $traveler['dob_d'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-dob-day'] = __( 'Select day.', 'myworldclass' );
					else
						$date_of_birth[] = sanitize_text_field( $traveler['dob_d'] );

					if ( sanitize_text_field( $traveler['dob_y'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-dob-year'] = __( 'Select year.', 'myworldclass' );
					else
						$date_of_birth[] = sanitize_text_field( $traveler['dob_y'] );

					if ( count( $date_of_birth ) == 3 ) {

						$dob = implode( '/', $date_of_birth );
						if ( (int) $date_of_birth[2] > 1970 && strtotime( $dob ) === false )
							$this->errors['tour-signup-' . $loop . '-dob-month'] = __( 'Invalid Date of Birth.', 'myworldclass' );
						else
							$attendee['DOB'] = implode( '/', $date_of_birth );

					}

					if ( sanitize_text_field( $traveler['type'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-type-student'] = __( 'Please select the traveler type.', 'myworldclass' );
					else
						$attendee['type'] = sanitize_text_field( $traveler['type'] );

					$user_email         = sanitize_text_field( $traveler['user_email'] );
					$user_email_confirm = sanitize_text_field( $traveler['user_email_confirm'] );
					if ( $user_email != '' && $user_email_confirm != '' ) {

						if ( ! is_email( $user_email ) )
							$this->errors['tour-signup-' . $loop . '-email'] = __( 'Invalid email address.', 'myworldclass' );
						elseif ( $user_email != $user_email_confirm )
							$this->errors['tour-signup-' . $loop . '-email-confirm'] = __( 'Please confirm the email address.', 'myworldclass' );
						else
							$attendee['user_email'] = $user_email;

					}

					$attendee['phone_type'] = sanitize_text_field( $traveler['phone_type'] );

					$attendee['phone'] = sanitize_text_field( $traveler['phone'] );

					if ( sanitize_text_field( $traveler['address1'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-address1'] = __( 'Please enter your address.', 'myworldclass' );
					else
						$attendee['address1'] = sanitize_text_field( $traveler['address1'] );

					$attendee['address2'] = sanitize_text_field( $traveler['address2'] );

					if ( sanitize_text_field( $traveler['city'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-city'] = __( 'Please enter your city.', 'myworldclass' );
					else
						$attendee['city'] = sanitize_text_field( $traveler['city'] );

					if ( sanitize_text_field( $traveler['state'] ) == '' )
						$this->errors['tour-signup-' . $loop . '-state'] = 1;
					else
						$attendee['state'] = sanitize_text_field( $traveler['state'] );

					$zip = sanitize_text_field( $traveler['zip'] );
					$zip = str_replace( ' ', '', $zip );
					if ( $zip == '' )
						$this->errors['tour-signup-' . $loop . '-zip'] = __( 'Enter zip code.', 'myworldclass' );
					elseif ( strlen( $zip ) != 5 )
						$this->errors['tour-signup-' . $loop . '-zip'] = __( 'Invalid zip.', 'myworldclass' );
					else
						$attendee['zip'] = $zip;

					$attendees[] = wp_parse_args( $attendee, $default );

				}

				$this->signup->travelers = $attendees;

			}

			elseif ( $this->signup_step == 2 ) {

				$parents = array();
				$default = $this->get_default_setup_data( 'parents' );

				for ( $loop = 0; $loop < 1; $loop++ ) {

					$parent = array();
					$traveler = $data['parents'][ $loop ];

					if ( sanitize_text_field( $traveler['relationship'] ) == '' )
						$this->errors['tour-signup-gardian-relationship'] = 1;
					else
						$attendee['relationship'] = sanitize_text_field( $traveler['relationship'] );

					if ( sanitize_text_field( $traveler['first_name'] ) == '' )
						$this->errors['tour-signup-gardian-first-name'] = __( 'First name can not be empty.', 'myworldclass' );
					else
						$attendee['first_name'] = sanitize_text_field( $traveler['first_name'] );

					if ( sanitize_text_field( $traveler['last_name'] ) == '' )
						$this->errors['tour-signup-gardian-last-name'] = __( 'Last name can not be empty.', 'myworldclass' );
					else
						$attendee['last_name'] = sanitize_text_field( $traveler['last_name'] );

					$attendee['type'] = 'parent';

					$user_email         = sanitize_text_field( $traveler['user_email'] );
					$user_email_confirm = sanitize_text_field( $traveler['user_email_confirm'] );
					if ( $user_email == '' )
						$this->errors['tour-signup-gardian-email'] = __( 'Email can not be empty.', 'myworldclass' );
					elseif ( ! is_email( $user_email ) )
						$this->errors['tour-signup-gardian-email'] = __( 'Invalid email address.', 'myworldclass' );
					elseif ( $user_email != $user_email_confirm )
						$this->errors['tour-signup-gardian-email-confirm'] = __( 'Please confirm the email address.', 'myworldclass' );
					elseif ( email_exists( $user_email ) )
						$this->errors['tour-signup-gardian-email'] = __( 'The email you provided is already in use.', 'myworldclass' );
					else
						$attendee['user_email'] = $user_email;

					if ( sanitize_text_field( $traveler['phone_type'] ) == '' )
						$this->errors['tour-signup-gardian-phone-type'] = 1;
					else
						$attendee['phone_type'] = sanitize_text_field( $traveler['phone_type'] );

					$phone_number = sanitize_text_field( $traveler['phone'] );
					$phone_check  = str_replace( array( '(', ')', '-', ' ', '+' ), '', $phone_number );
					if ( $phone_number == '' )
						$this->errors['tour-signup-gardian-phone'] = __( 'Phone number is required.', 'myworldclass' );
					elseif ( strlen( $phone_check ) < 10 )
						$this->errors['tour-signup-gardian-phone'] = __( 'Invalid phone number.', 'myworldclass' );
					else {

						$ph = str_split( $phone_check, 3 );
						$attendee['phone'] = '(' . $ph[0] . ') ' . $ph[1] . '-' . $ph[2] . $ph[3];

					}

					$alt_number_type = sanitize_text_field( $traveler['phone_alt_type'] );

					if ( $alt_number_type != '' ) {

						$attendee['phone_alt_type'] = $alt_number_type;

						$alt_phone_number = sanitize_text_field( $traveler['phone_alt'] );
						$alt_phone_check  = str_replace( array( '(', ')', '-', ' ', '+' ), '', $alt_phone_number );
						if ( $alt_phone_number == '' )
							$this->errors['tour-signup-gardian-second-phone'] = __( 'Phone number is required.', 'myworldclass' );
						elseif ( strlen( $alt_phone_check ) != 10 )
							$this->errors['tour-signup-gardian-second-phone'] = __( 'Invalid phone number.', 'myworldclass' );
						else {

							$ph = str_split( $alt_phone_check, 3 );
							$attendee['phone_alt'] = '(' . $ph[0] . ') ' . $ph[1] . '-' . $ph[2] . $ph[3];

						}

					}

					$same_as_traveler = false;
					if ( isset( $traveler['same_as_travel'] ) && $traveler['same_as_travel'] == 1 )
						$same_as_traveler = true;

					if ( ! $same_as_traveler ) {

						$attendee['same_as_travel'] = 0;
						if ( sanitize_text_field( $traveler['address1'] ) == '' )
							$this->errors['tour-signup-gardian-address1'] = __( 'Please enter your address.', 'myworldclass' );
						else
							$attendee['address1'] = sanitize_text_field( $traveler['address1'] );

						$attendee['address2'] = sanitize_text_field( $traveler['address2'] );

						if ( sanitize_text_field( $traveler['city'] ) == '' )
							$this->errors['tour-signup-gardian-city'] = __( 'Please enter your city.', 'myworldclass' );
						else
							$attendee['city'] = sanitize_text_field( $traveler['city'] );

						if ( sanitize_text_field( $traveler['state'] ) == '' )
							$this->errors['tour-signup-gardian-state'] = 1;
						else
							$attendee['state'] = sanitize_text_field( $traveler['state'] );

						$zip = sanitize_text_field( $traveler['zip'] );
						$zip = str_replace( ' ', '', $zip );
						if ( $zip == '' )
							$this->errors['tour-signup-gardian-zip'] = __( 'Enter zip code.', 'myworldclass' );
						elseif ( strlen( $zip ) != 5 )
							$this->errors['tour-signup-gardian-zip'] = __( 'Invalid zip.', 'myworldclass' );
						else
							$attendee['zip'] = $zip;

					}

					else {

						if ( isset( $this->signup->travelers[0] ) && array_key_exists( 'address1', $this->signup->travelers[0] ) ) {

							$attendee['same_as_travel'] = 1;

							$attendee['address1'] = $this->signup->travelers[0]['address1'];
							$attendee['address2'] = $this->signup->travelers[0]['address2'];
							$attendee['city']     = $this->signup->travelers[0]['city'];
							$attendee['state']    = $this->signup->travelers[0]['state'];
							$attendee['zip']      = $this->signup->travelers[0]['zip'];

						}

					}

					$parents[] = wp_parse_args( $attendee, $default );

				}

				$this->signup->parents = $parents;

			}

			elseif ( $this->signup_step == 3 ) {

				$billing = array();
				$default = $this->get_default_setup_data( 'billing' );

				if ( sanitize_text_field( $data['billing']['first_name'] ) == '' )
					$this->errors['tour-signup-billing-first-name'] = __( 'First name can not be empty.', 'myworldclass' );
				else
					$billing['first_name'] = sanitize_text_field( $data['billing']['first_name'] );

				if ( sanitize_text_field( $data['billing']['last_name'] ) == '' )
					$this->errors['tour-signup-billing-last-name'] = __( 'Last name can not be empty.', 'myworldclass' );
				else
					$billing['last_name'] = sanitize_text_field( $data['billing']['last_name'] );

				if ( sanitize_text_field( $data['billing']['address1'] ) == '' )
					$this->errors['tour-signup-billing-address1'] = __( 'Address can not be empty.', 'myworldclass' );
				else
					$billing['address1'] = sanitize_text_field( $data['billing']['address1'] );

				$billing['address2'] = sanitize_text_field( $data['billing']['address2'] );

				if ( sanitize_text_field( $data['billing']['city'] ) == '' )
					$this->errors['tour-signup-billing-city'] = __( 'City not be empty.', 'myworldclass' );
				else
					$billing['city'] = sanitize_text_field( $data['billing']['city'] );

				if ( sanitize_text_field( $data['billing']['state'] ) == '' )
					$this->errors['tour-signup-billing-state'] = __( 'Select State.', 'myworldclass' );
				else
					$billing['state'] = sanitize_text_field( $data['billing']['state'] );

				if ( $this->is_valid_scholarship_code( $data['billing']['scholarship_code'] ) )
					$billing['scholarship_code'] = sanitize_text_field( $data['billing']['scholarship_code'] );

				$zip = sanitize_text_field( $data['billing']['zip'] );
				$zip = str_replace( ' ', '', $zip );
				if ( $zip == '' )
					$this->errors['tour-signup-billing-zip'] = __( 'Enter zip code.', 'myworldclass' );
				elseif ( strlen( $zip ) != 5 )
					$this->errors['tour-signup-billing-zip'] = __( 'Invalid zip.', 'myworldclass' );
				else
					$billing['zip'] = $zip;

				$this->signup->billing = wp_parse_args( $billing, $default );
				if ( isset( $this->signup->billing['scholarship_code'] ) && $this->signup->billing['scholarship_code'] != '' )
					$this->signup->scholarship = $this->signup->billing['scholarship_code'];

			}

			elseif ( $this->signup_step == 4 ) {

				$payment = array();
				$default = $this->get_default_setup_data( 'payment' );

				$payment_plan = sanitize_text_field( $data['payment']['plan'] );
				if ( $payment_plan == '' )
					$this->errors['payment-plan-auto'] = __( 'Please select a payment plan.', 'myworldclass' );
				else
					$payment['plan'] = $payment_plan;

				$manual = sanitize_text_field( $data['payment']['manual'] );
				if ( $payment_plan == 'manual' && $manual == '' )
					$this->errors['payment-manual'] = __( 'Please enter the amount you want to pay.', 'myworldclass' );
				elseif ( $payment_plan == 'manual' && number_format( $manual, 2, '.', '' ) < $this->minimum )
					$this->errors['payment-manual'] = __( 'Please enter a higher amount.', 'myworldclass' );
				else
					$payment['manual'] = $manual;

				if ( $payment_plan != 'auto' ) {

					if ( sanitize_text_field( $data['payment']['card'] ) == '' )
						$this->errors['payment-card'] = __( 'Please enter a card number.', 'myworldclass' );
					else
						$payment['card'] = sanitize_text_field( $data['payment']['card'] );

					if ( sanitize_text_field( $data['payment']['name'] ) == '' )
						$this->errors['payment-name'] = __( 'Please enter the name on the card.', 'myworldclass' );
					else
						$payment['name'] = sanitize_text_field( $data['payment']['name'] );

					$expires_mm = sanitize_text_field( $data['payment']['exp_mm'] );
					$expires_yy = sanitize_text_field( $data['payment']['exp_yy'] );

					if ( $expires_mm == '' || $expires_yy == '' )
						$this->errors['payment-exp-mm'] = __( 'Enter the cards expiration date.', 'myworldclass' );
					elseif ( mktime( 0, 0, 0, (int) $expires_mm, date( 'd' ), (int) $expires_yy ) < current_time( 'timestamp' ) )
						$this->errors['payment-exp-mm'] = __( 'Invalid expiration date. ' . mktime( 0, 0, 0, (int) $expires_mm, date( 'd' ), (int) $expires_yy ) , 'myworldclass' );
					else {
						$payment['exp_mm'] = $expires_mm;
						$payment['exp_yy'] = $expires_yy;
					}

					if ( sanitize_text_field( $data['payment']['cvv'] ) == '' )
						$this->errors['payment-cvv'] = __( 'Please enter cards CVV number.', 'myworldclass' );
					else
						$payment['cvv'] = sanitize_text_field( $data['payment']['cvv'] );

				}

				$this->signup->plan = $payment['plan'];

				if ( empty( $this->errors ) ) {

					$charge = $this->charge_card( $payment );
					if ( $charge ) {

						$this->register_error = '';
						$user_id = $this->register_new_user();

						if ( $user_id !== false ) {

							update_user_meta( $user_id, 'tour_code', $this->tour_code );
							update_user_meta( $user_id, 'user_phone', $this->signup->parents[0]['phone'] );

							mywclass_update_users_balance( $user_id, $this->payment_amount );

							$payment = new MyWorldClass_Payments( $user_id );
							$payment->add_payment( $this->payment_id, 'Completed', array(
								'note'    => $this->payment_desc,
								'amount'  => $this->payment_amount,
								'tour_id' => $this->post_id,
								'type'    => 'card'
							) );

							$this->get_attendees( true );

						}
						else {

							$this->errors['charge'] = 1;
							$this->payment_errors = 'The following error occurred: ' . $this->register_error;

						}

					}
					else {

						$this->errors['charge'] = 1;

					}

				}

			}

		}

		/**
		 * Go to Step
		 * @since 1.0
		 * @version 1.0
		 */
		public function go_to_step( $step = 1 ) {

			if ( $step == 1 ) {

				$this->signup_step         = 1;
				$this->attending_travelers = count( $this->signup->travelers );
				$this->signup->step        = $this->signup_step;
				$this->confirmed_name      = true;

			}

			elseif ( $step == 2 ) {

				$this->signup_step         = 2;
				$this->attending_travelers = count( $this->signup->travelers );
				$this->signup->step        = $this->signup_step;

			}

			elseif ( $step == 3 ) {

				$this->signup_step         = 3;
				$this->attending_travelers = count( $this->signup->travelers );
				$this->signup->step        = $this->signup_step;

			}

			global $wpdb;

			$wpdb->update(
				$this->db,
				array( 'step' => $this->signup_step ),
				array( 'id' => $this->signup_id ),
				array( '%d' ),
				array( '%d' )
			);

		}

		/**
		 * Can Signup
		 * @since 1.0
		 * @version 1.0
		 */
		public function can_signup() {

			$signup = true;

			if ( is_user_logged_in() ) {

				$user_id = get_current_user_id();
				$user    = mywclass_get_userdata( $user_id );

				if ( $user->tour_code == $this->tour_code )
					$signup = false;

			}

			return $signup;

		}

		/**
		 * Get Travelers Details
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_travelers_details( $id = 0, $detail = '' ) {

			if ( isset( $this->signup->travelers[ $id ] ) && array_key_exists( $detail, $this->signup->travelers[ $id ] ) )
				return $this->signup->travelers[ $id ][ $detail ];

			elseif ( isset( $this->submitted['travelers'][ $id ] ) && array_key_exists( $detail, $this->submitted['travelers'] ) )
				return sanitize_text_field( $this->submitted['travelers'][ $detail ] );

			return '';

		}

		/**
		 * Get Parents Details
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_parents_details( $id = 0, $detail = '' ) {

			if ( isset( $this->signup->parents[ $id ] ) && array_key_exists( $detail, $this->signup->parents[ $id ] ) )
				return $this->signup->parents[ $id ][ $detail ];

			elseif ( isset( $this->submitted['parents'][ $id ] ) && array_key_exists( $detail, $this->submitted['parents'] ) )
				return sanitize_text_field( $this->submitted['parents'][ $detail ] );

			return '';

		}

		/**
		 * Get Billing Details
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_billing_details( $id = 0, $detail = '' ) {

			if ( isset( $this->signup->billing[ $id ] ) && is_array( $this->signup->billing[ $id ] ) && array_key_exists( $detail, $this->signup->billing[ $id ] ) )
				return $this->signup->billing[ $id ][ $detail ];

			elseif ( isset( $this->submitted['billing'][ $id ] ) && is_array( $this->submitted['billing'][ $id ] ) && array_key_exists( $detail, $this->submitted['billing'] ) )
				return sanitize_text_field( $this->submitted['billing'][ $detail ] );

			elseif ( isset( $this->signup->scholarship ) && $detail == 'scholarship_code' )
				return sanitize_text_field( $this->signup->scholarship );

			return '';

		}

		/**
		 * Get Payment Details
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_payment_details( $id = 0, $detail = '' ) {

			if ( $id == 'plan' ) {

				if ( isset( $this->submitted['payment']['plan'] ) )
					return $this->submitted['payment']['plan'];
				
				return $this->signup->plan;

			}

			elseif ( $id == 'manual' && isset( $this->submitted['payment']['manual'] ) )
				return $this->submitted['payment']['manual'];

			elseif ( isset( $this->submitted['payment'] ) && array_key_exists( $id,  $this->submitted['payment'] ) )
				return  $this->submitted['payment'][ $id ];

			return '';

		}

		/**
		 * Get Value
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_value( $section = NULL, $key = NULL, $subkey = NULL ) {

			if ( $section == 'travelers' )
				return $this->get_travelers_details( $key, $subkey );

			elseif ( $section == 'parents' )
				return $this->get_parents_details( $key, $subkey );

			elseif ( $section == 'billing' )
				return $this->get_billing_details( $key, $subkey );

			elseif ( $section == 'payment' )
				return $this->get_payment_details( $key, $subkey );

			return '';

		}

		/**
		 * Has Error
		 * @since 1.0
		 * @version 1.0
		 */
		public function has_error( $id = '' ) {

			if ( ! empty( $this->errors ) && array_key_exists( $id, $this->errors ) )
				echo ' has-error';

		}

		/**
		 * Display Signup Process
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_signup_progress() {

			$step1_img = plugins_url( 'assets/images/step1.png', MYWORLDCLASS );
			if ( $this->signup_step == 1 )
				$step1_img = str_replace( 'step1.png', 'step1c.png', $step1_img );

			$step2_img = plugins_url( 'assets/images/step2.png', MYWORLDCLASS );
			if ( $this->signup_step == 2 )
				$step2_img = str_replace( 'step2.png', 'step2c.png', $step2_img );

			$step3_img = plugins_url( 'assets/images/step3.png', MYWORLDCLASS );
			if ( $this->signup_step == 3 )
				$step3_img = str_replace( 'step3.png', 'step3c.png', $step3_img );

			$step4_img = plugins_url( 'assets/images/step4.png', MYWORLDCLASS );
			if ( $this->signup_step == 4 )
				$step4_img = str_replace( 'step4.png', 'step4c.png', $step4_img );

?>
<style type="text/css">
#payment-process-wrapper {
	display: block;
	float: none;
	clear: both;
	min-height: 100px;
}
#payment-process-wrapper .signstep {
	display: block;
	width: 25%;
	height: 100px;
	line-height: 100px;
	float: left;
}
#payment-process-wrapper .signstep a {
	display: block;
	cursor: pointer;
}
#payment-process-wrapper .signstep a.disabled {
	cursor: default;
}
#payment-process-wrapper .signstep.current a {
	color: #ff9900;
}
#payment-process-wrapper .signstep img {
	width: 100px;
	height: 100px;
	display: inline;
}
</style>
<div class="signstep<?php if ( $this->signup_step == 1 ) echo ' current'; ?>" id="current-step-1">
	<a class="go-back-in-signup<?php if ( $this->signup_step < 2 ) echo ' disabled'; ?>" data-to="1" data-tour-id="<?php echo $this->post_id; ?>" data-signup-id="<?php echo $this->signup_id; ?>"><img src="<?php echo $step1_img; ?>" alt="" /> <?php _e( 'Traveler Info', 'myworldclass' ); ?></a>
</div>
<div class="signstep<?php if ( $this->signup_step == 2 ) echo ' current'; ?>" id="current-step-2">
	<a class="go-back-in-signup<?php if ( $this->signup_step < 3 ) echo ' disabled'; ?>" data-to="2" data-tour-id="<?php echo $this->post_id; ?>" data-signup-id="<?php echo $this->signup_id; ?>"><img src="<?php echo $step2_img; ?>" alt="" /> <?php _e( 'Parent/Guardian', 'myworldclass' ); ?></a>
</div>
<div class="signstep<?php if ( $this->signup_step == 3 ) echo ' current'; ?>" id="current-step-3">
	<a class="go-back-in-signup<?php if ( $this->signup_step < 4 ) echo ' disabled'; ?>" data-to="3" data-tour-id="<?php echo $this->post_id; ?>" data-signup-id="<?php echo $this->signup_id; ?>"><img src="<?php echo $step3_img; ?>" alt="" /> <?php _e( 'Billing', 'myworldclass' ); ?></a>
</div>
<div class="signstep<?php if ( $this->signup_step == 4 ) echo ' current'; ?>" id="current-step-4">
	<img src="<?php echo $step4_img; ?>" alt="" /> <?php _e( 'Payment', 'myworldclass' ); ?>
</div>
<?php

		}

		/**
		 * Display Signup Form
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_signup_form() {

?>
<form method="post" action="" class="clear" autocomplete="off">
	<input type="hidden" name="signup_id" value="<?php echo $this->signup_id; ?>" />
	<input type="hidden" name="step" value="<?php echo $this->signup_step; ?>" />
	<input type="hidden" name="tour_id" value="<?php echo $this->post_id; ?>" />
<?php

		// Start - The students attending this tour
		if ( $this->signup_step == 1 ) :

			$traveler_options = array(
				1 => 1,
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5
			);

?>
	<h1 class="blue"><?php _e( 'Tell Us About The Traveler', 'myworldclass' ); ?></h1>
<?php

		if ( isset( $this->errors ) && ! empty( $this->errors ) )
			echo '<div class="alert alert-warning">' . __( 'Errors were found in your submission. Please correct them and try again.', '' ) . '</div>';

?>
	<div class="form-group">
		<label for="" class="blue"><?php _e( 'Travelers', 'myworldclass' ); ?></label>
		<div class="form-group-input">
			<select id="number-of-travelers" name="number_of_travelers">
<?php

			foreach ( $traveler_options as $value => $label ) {

				echo '<option value="' . $value . '"';
				if ( $this->signup_step > 1 && $this->attending_travelers == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';

			}

?>
			</select>
		</div>
	</div>
	<div class="clear">
<?php

		for ( $i = 0; $i < 5; $i++ ) {

			$count = $i;
			$count ++;

			$label = __( 'One', 'myworldclass' );
			if ( $i == 1 )
				$label = __( 'Two', 'myworldclass' );
			elseif ( $i == 2 )
				$label = __( 'Three', 'myworldclass' );
			elseif ( $i == 3 )
				$label = __( 'Four', 'myworldclass' );
			elseif ( $i == 4 )
				$label = __( 'Five', 'myworldclass' );

			$hide = false;
			if ( $i > $this->attending_travelers - 1 )
				$hide = true;

			$date_of_birth = array( 'm' => '', 'd' => '', 'y' => '' );
			$given_date_of_birth = $this->get_value( 'travelers', $i, 'DOB' );
			if ( $given_date_of_birth != '' && strlen( $given_date_of_birth ) == 10 ) {
				$dob = explode( '/', $given_date_of_birth );
				$date_of_birth['m'] = $dob[0];
				$date_of_birth['d'] = $dob[1];
				$date_of_birth['y'] = $dob[2];
			}

			$gender = 'male';
			$given_gender = $this->get_value( 'travelers', $i, 'gender' );
			if ( $given_gender != '' )
				$gender = $given_gender;

			$type = 'student';
			$given_type = $this->get_value( 'travelers', $i, 'type' );
			if ( $given_type != '' )
				$type = $given_type;

?>
	<div class="travelers"<?php if ( $hide ) echo ' style="display:none;"'; echo ' id="traveler' . $i . '" data-row="' . $i . '"'; ?>>
		<div class="traveler-info">
			<div class="traveler-label">
				<div class="form-group">
					<label class="blue"><?php printf( 'Traveler %s', $label ); ?></label>
					<div class="form-group-input">
						<p><strong><?php _e( 'Enter traveler\'s name as it appears on their passport, ID or birth certificate.', 'myworldclass' ); ?></strong></p>
					</div>
				</div>
			</div>
			<div class="col-half">
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-first-name' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-first-name"><?php _e( 'First Name', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][first_name]" id="tour-signup-<?php echo $i; ?>-first-name" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'travelers', $i, 'first_name' ); ?>" />
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-middle-name' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-middle-name"><?php _e( 'Middle Name', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][middle_name]" id="tour-signup-<?php echo $i; ?>-middle-name" placeholder="" value="<?php echo $this->get_value( 'travelers', $i, 'middle_name' ); ?>" />
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-last-name' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-last-name"><?php _e( 'Last Name', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][last_name]" id="tour-signup-<?php echo $i; ?>-last-name" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'travelers', $i, 'last_name' ); ?>" />
					</div>
				</div>
				<div class="form-group checkbox<?php $this->has_error( 'tour-signup-' . $i . '-confirm-name' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-confirm-name"><input type="checkbox" class="must-be-ticked" name="travelers[<?php echo $i; ?>][confirm_name]" id="tour-signup-<?php echo $i; ?>-confirm-name"<?php if ( $this->confirmed_name ) echo ' checked="checked"'; ?> value="1" /></label>
					<div class="form-group-input"><span><?php _e( 'I have double checked that this is the name that appears on the traveler\'s passport, ID or birth certificate.', 'myworldclass' ); ?></span></div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-gender' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-gender-male"><?php _e( 'Gender', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<label for="tour-signup-<?php echo $i; ?>-gender-male"><input autocomplete="off" type="radio" name="travelers[<?php echo $i; ?>][gender]" id="tour-signup-<?php echo $i; ?>-gender-male"<?php checked( $gender, 'male' ); ?> value="male" /> <?php _e( 'Male', 'myworldclass' ); ?></label>
						<label for="tour-signup-<?php echo $i; ?>-gender-female"><input autocomplete="off" type="radio" name="travelers[<?php echo $i; ?>][gender]" id="tour-signup-<?php echo $i; ?>-gender-female"<?php checked( $gender, 'female' ); ?> value="female" /> <?php _e( 'Female', 'myworldclass' ); ?></label>
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-dob-month' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-dob-month"><?php _e( 'Date of Birth', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<select autocomplete="off" name="travelers[<?php echo $i; ?>][dob_m]" class="select required" id="tour-signup-<?php echo $i; ?>-dob-month">
<?php

			$months = $this->get_months();

			foreach ( $months as $month => $label ) {
				echo '<option value="' . $month . '"';
				if ( $date_of_birth['m'] == $month ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
						</select>
						<select autocomplete="off" name="travelers[<?php echo $i; ?>][dob_d]" class="select required" id="tour-signup-<?php echo $i; ?>-dob-day">
<?php

			echo '<option value="">' . __( 'Day', 'myworldclass' ) . '</option>';
			foreach ( range( 1, 31 ) as $day ) {
				echo '<option value="' . $day . '"';
				if ( $date_of_birth['d'] == $day ) echo ' selected="selected"';
				echo '>' . $day . '</option>';
			}

?>
						</select>
						<select autocomplete="off" name="travelers[<?php echo $i; ?>][dob_y]" class="select required" id="tour-signup-<?php echo $i; ?>-dob-year">
<?php

			echo '<option value="">' . __( 'Year', 'myworldclass' ) . '</option>';
			foreach ( range( 1950, date( 'Y' ) ) as $year ) {
				echo '<option value="' . $year . '"';
				if ( $date_of_birth['y'] == $year ) echo ' selected="selected"';
				echo '>' . $year . '</option>';
			}

?>
						</select>
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-type-student' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-type-student"><?php _e( 'Traveler is a', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<label for="tour-signup-<?php echo $i; ?>-type-student"><input autocomplete="off" type="radio" name="travelers[<?php echo $i; ?>][type]" id="tour-signup-<?php echo $i; ?>-type-student"<?php checked( $type, 'student' ); ?> value="student" /> <?php _e( 'Student', 'myworldclass' ); ?></label>
						<label for="tour-signup-<?php echo $i; ?>-type-parent"><input autocomplete="off" type="radio" name="travelers[<?php echo $i; ?>][type]" id="tour-signup-<?php echo $i; ?>-type-parent"<?php checked( $type, 'parent' ); ?> value="parent" /> <?php _e( 'Parent/Adult', 'myworldclass' ); ?></label>
					</div>
				</div>
			</div>
			<div class="col-half last">
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-email' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-email"><?php _e( 'Traveler\'s Email', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][user_email]" id="tour-signup-<?php echo $i; ?>-email" class="" placeholder="<?php _e( 'Optional', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'travelers', $i, 'user_email' ); ?>" />
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-email-confirm' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-email-confirm"><?php _e( 'Verify Email', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][user_email_confirm]" id="tour-signup-<?php echo $i; ?>-email-confirm" class="" placeholder="<?php _e( 'Optional', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'travelers', $i, 'user_email' ); ?>" />
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-phone' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-phone-type"><?php _e( 'Phone', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<select autocomplete="off" name="travelers[<?php echo $i; ?>][phone_type]" class="select" id="tour-signup-<?php echo $i; ?>-phone-type">
<?php

			$phone_types = $this->get_phone_types();

			$given_phone_type = $this->get_value( 'travelers', $i, 'phone_type' );
			foreach ( $phone_types as $phone_type => $label ) {
				echo '<option value="' . $phone_type . '"';
				if ( $given_phone_type == $phone_type ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
						</select>
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][phone]" class="auto-width" size="18" id="tour-signup-<?php echo $i; ?>-phone" placeholder="ex. (123) 3456-7890" value="<?php echo $this->get_value( 'travelers', $i, 'phone' ); ?>" />
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-address1' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-address1"><?php _e( 'Address', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][address1]" id="tour-signup-<?php echo $i; ?>-address1" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'travelers', $i, 'address1' ); ?>" />
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-address2' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-address2"><?php _e( 'Address 2', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][address2]" id="tour-signup-<?php echo $i; ?>-address2" placeholder="<?php _e( 'Optional', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'travelers', $i, 'address2' ); ?>" />
					</div>
				</div>
				<div class="form-group<?php $this->has_error( 'tour-signup-' . $i . '-city' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-city"><?php _e( 'City', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][city]" id="tour-signup-<?php echo $i; ?>-city" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'travelers', $i, 'city' ); ?>" />
					</div>
				</div>
				<div class="form-group col-half<?php $this->has_error( 'tour-signup-' . $i . '-state' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-state"><?php _e( 'State', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<select autocomplete="off" name="travelers[<?php echo $i; ?>][state]" class="select required" id="tour-signup-<?php echo $i; ?>-state">
<?php

			$states = $this->get_states();

			echo '<option value="">' . __( 'State', 'myworldclass' ) . '</option>';
			$given_state = $this->get_value( 'travelers', $i, 'state' );
			foreach ( $states as $state ) {
				echo '<option value="' . $state . '"';
				if ( $given_state == $state ) echo ' selected="selected"';
				echo '>' . $state . '</option>';
			}

?>
						</select>
					</div>
				</div>
				<div class="form-group col-half last inline<?php $this->has_error( 'tour-signup-' . $i . '-zip' ); ?>">
					<label for="tour-signup-<?php echo $i; ?>-zip"><?php _e( 'Zip', 'myworldclass' ); ?></label>
					<div class="form-group-input">
						<input autocomplete="off" type="text" name="travelers[<?php echo $i; ?>][zip]" size="5" id="tour-signup-<?php echo $i; ?>-zip" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'travelers', $i, 'zip' ); ?>" />
					</div>
				</div>
			</div>
		</div>
		<div class="traveler-widget">
			<?php if ( $i == 0 ) { ?>
			<div class="signup-widget">
				<h3><?php _e( 'Tour Info', 'myworldclass' ); ?></h3>
				<ul>
					<li><strong><?php _e( 'Tour', 'myworldclass' ); ?>: </strong> <?php echo $this->post->post_title; ?></li>
					<li><strong><?php _e( 'Group Leader', 'myworldclass' ); ?>: </strong> <?php echo $this->teacher_name; ?></li>
					<li><strong><?php _e( 'Tour ID', 'myworldclass' ); ?>: </strong> <?php echo $this->tour_code; ?></li>
					<li><strong><?php _e( 'Tour Date', 'myworldclass' ); ?>: </strong> <?php echo $this->start_date; ?></li>
				</ul>
				<p class="view-tour"><a href="<?php echo get_permalink( $this->post_id ); ?>">View Tour Page</a></p>
			</div>
			<?php } ?>
		</div>
	</div>
<?php

		}

?>
	</div>
	<div id="submit-form-row" class="clear">
		<button type="submit" id="submit-tour-signup"><?php _e( 'Next', 'myworldclass' ); ?> <i class="fa fa-arrow-left"></i></button>
	</div>
<?php

		// Parents
		elseif ( $this->signup_step == 2 ) :

			$same_address = $this->get_value( 'parents', 0, 'same_as_travel' );

?>
	<h1 class="blue"><?php _e( 'Parent/Guardian Information', 'myworldclass' ); ?></h1>
<?php

		if ( isset( $this->errors ) && ! empty( $this->errors ) )
			echo '<div class="alert alert-warning">' . __( 'Errors were found in your submission. Please correct them and try again.', '' ) . '</div>';

?>
	<div class="clear">
		<div class="travelers" id="parent1" data-row="-1">
			<div class="traveler-info">
				<div class="col-half">
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-relationship' ); ?>">
						<label for="tour-signup-gardian-relationship"><?php _e( 'Relationship', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<select name="parents[0][relationship]" class="select required" id="tour-signup-gardian-relationship">
<?php

			$relationships = $this->get_relationships();

			$given_relationship = $this->get_value( 'parents', 0, 'relationship' );
			foreach ( $relationships as $relationship => $label ) {
				echo '<option value="' . $relationship . '"';
				if ( $given_relationship == $relationship ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
							</select>
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-first-name' ); ?>">
						<label for="tour-signup-gardian-first-name"><?php _e( 'First Name', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="parents[0][first_name]" id="tour-signup-gardian-first-name" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'first_name' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-last-name' ); ?>">
						<label for="tour-signup-gardian-last-name"><?php _e( 'Last Name', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="parents[0][last_name]" id="tour-signup-gardian-last-name" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'last_name' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-email' ); ?>">
						<label for="tour-signup-gardian-email"><?php _e( 'Email', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="parents[0][user_email]" id="tour-signup-gardian-email" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'user_email' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-email-confirm' ); ?>">
						<label for="tour-signup-gardian-email-confirm"><?php _e( 'Verify Email', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="parents[0][user_email_confirm]" id="tour-signup-gardian-email-confirm" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'user_email' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-phone' ); ?>">
						<label for="tour-signup-gardian-phone-type"><?php _e( 'Phone', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<select name="parents[0][phone_type]" class="select required" id="tour-signup-gardian-phone-type">
<?php

			$phone_types = $this->get_phone_types();

			$given_phone_type = $this->get_value( 'parents', 0, 'phone_type' );
			foreach ( $phone_types as $phone_type => $label ) {
				echo '<option value="' . $phone_type . '"';
				if ( $given_phone_type == $phone_type ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
							</select>
							<input type="text" name="parents[0][phone]" class="auto-width required" size="18" id="tour-signup-gardian-phone" placeholder="ex. (123) 3456-7890" value="<?php echo $this->get_value( 'parents', 0, 'phone' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-second-phone' ); ?>">
						<label for="tour-signup-gardian-second-phone-type"><?php _e( 'Secondary Phone', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<select name="parents[0][phone_alt_type]" class="select" id="tour-signup-gardian-second-phone-type">
<?php

			$given_secondary_phone_type = $this->get_value( 'parents', 0, 'phone_alt_type' );
			echo '<option value=""';
			if ( $given_secondary_phone_type == '' ) echo ' selected="selected"';
			echo '></option>';
			foreach ( $phone_types as $phone_type => $label ) {
				echo '<option value="' . $phone_type . '"';
				if ( $given_secondary_phone_type == $phone_type ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
							</select>
							<input type="text" name="parents[0][phone_alt]" class="auto-width" size="18" id="tour-signup-gardian-second-phone" placeholder="<?php _e( 'optional', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'phone_alt' ); ?>" />
						</div>
					</div>
				</div>
				<div class="col-half last">
					<div class="form-group checkbox<?php $this->has_error( 'tour-signup-gardian-traveller-address' ); ?>">
						<label for="tour-signup-gardian-traveller-address"><input type="checkbox" class="toggles-fields" data-toggle="guardian-address" name="parents[0][same_as_travel]" id="tour-signup-gardian-traveller-address"<?php checked( $same_address, 1 ); ?> value="1" /></label>
						<div class="form-group-input" style="padding-top: 6px;"><?php _e( 'Same address as traveler.', 'myworldclass' ); ?></div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-address1' ); ?>">
						<label for="tour-signup-gardian-address1"><?php _e( 'Address', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="parents[0][address1]" id="tour-signup-gardian-address1" class="guardian-address required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'address1' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-address2' ); ?>">
						<label for="tour-signup-gardian-address2"><?php _e( 'Address 2', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="parents[0][address2]" class="guardian-address optional" id="tour-signup-gardian-address2" placeholder="<?php _e( 'Optional', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'address2' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-gardian-city' ); ?>">
						<label for="tour-signup-gardian-city"><?php _e( 'City', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="parents[0][city]" id="tour-signup-gardian-city" class="guardian-address required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'city' ); ?>" />
						</div>
					</div>
					<div class="form-group col-half<?php $this->has_error( 'tour-signup-gardian-state' ); ?>">
						<label for="tour-signup-gardian-state"><?php _e( 'State', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<select name="parents[0][state]" class="guardian-address select required" id="tour-signup-gardian-state">
<?php

			$states = $this->get_states();

			echo '<option value="">' . __( 'State', 'myworldclass' ) . '</option>';
			$given_state = $this->get_value( 'parents', 0, 'state' );
			foreach ( $states as $state ) {
				echo '<option value="' . $state . '"';
				if ( $given_state == $state ) echo ' selected="selected"';
				echo '>' . $state . '</option>';
			}

?>
							</select>
						</div>
					</div>
					<div class="form-group col-half last inline<?php $this->has_error( 'tour-signup-gardian-zip' ); ?>">
						<label for="tour-signup-gardian-zip"><?php _e( 'Zip', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="parents[0][zip]" size="5" id="tour-signup-gardian-zip" class="guardian-address required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'parents', 0, 'zip' ); ?>" />
						</div>
					</div>
				</div>
			</div>
			<div class="traveler-widget">
				<div class="signup-widget">
					<h3><?php _e( 'Tour Info', 'myworldclass' ); ?></h3>
					<ul>
						<li><strong><?php _e( 'Tour', 'myworldclass' ); ?>: </strong> <?php echo $this->post->post_title; ?></li>
						<li><strong><?php _e( 'Group Leader', 'myworldclass' ); ?>: </strong> <?php echo $this->teacher_name; ?></li>
						<li><strong><?php _e( 'Trip ID', 'myworldclass' ); ?>: </strong> <?php echo $this->tour_code; ?></li>
						<li><strong><?php _e( 'Tour Date', 'myworldclass' ); ?>: </strong> <?php echo $this->start_date; ?></li>
					</ul>
					<p class="view-tour"><a href="<?php echo get_permalink( $this->post_id ); ?>">View Tour Page</a></p>
				</div>
			</div>
		</div>
	</div>
	<div id="submit-form-row" class="clear">
		<button type="button" class="go-back-in-signup" data-to="1" data-tour-id="<?php echo $this->post_id; ?>" data-signup-id="<?php echo $this->signup_id; ?>"><i class="fa fa-arrow-right"></i> <?php _e( 'Back', 'myworldclass' ); ?></button>
		<button type="submit" id="submit-tour-signup"><?php _e( 'Next', 'myworldclass' ); ?> <i class="fa fa-arrow-left"></i></button>
	</div>
<?php

		// Billing Details
		elseif ( $this->signup_step == 3 ) :

?>
	<h1 class="blue"><?php _e( 'Billing Information', 'myworldclass' ); ?></h1>
<?php

		if ( isset( $this->errors ) && ! empty( $this->errors ) )
			echo '<div class="alert alert-warning">' . __( 'Errors were found in your submission. Please correct them and try again.', '' ) . '</div>';

?>
	<div class="clear">
		<div class="travelers" id="parent1" data-row="-1">
			<div class="traveler-info">
				<div class="col-half">
					<div class="form-group<?php $this->has_error( 'tour-signup-billing-first-name' ); ?>">
						<label for="tour-signup-billing-first-name"><?php _e( 'First Name', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="billing[first_name]" id="tour-signup-billing-first-name" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'billing', 'first_name' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-billing-last-name' ); ?>">
						<label for="tour-signup-billing-last-name"><?php _e( 'Last Name', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="billing[last_name]" id="tour-signup-billing-last-name" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'billing', 'last_name' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-billing-address1' ); ?>">
						<label for="tour-signup-billing-address1"><?php _e( 'Address', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="billing[address1]" id="tour-signup-billing-address1" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'billing', 'address1' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-billing-address2' ); ?>">
						<label for="tour-signup-billing-address2"><?php _e( 'Address2', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="billing[address2]" id="tour-signup-billing-address2" placeholder="<?php _e( 'Optional', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'billing', 'address2' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'tour-signup-billing-city' ); ?>">
						<label for="tour-signup-billing-city"><?php _e( 'City', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="billing[city]" id="tour-signup-billing-city" class="required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'billing', 'city' ); ?>" />
						</div>
					</div>
					<div class="form-group col-half<?php $this->has_error( 'tour-signup-billing-state' ); ?>">
						<label for="tour-signup-billing-state"><?php _e( 'State', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<select name="billing[state]" class="guardian-address select required" id="tour-signup-billing-state">
<?php

			$states = $this->get_states();

			echo '<option value="">' . __( 'State', 'myworldclass' ) . '</option>';
			$given_state = $this->get_value( 'billing', 'state' );
			foreach ( $states as $state ) {
				echo '<option value="' . $state . '"';
				if ( $given_state == $state ) echo ' selected="selected"';
				echo '>' . $state . '</option>';
			}

?>
							</select>
						</div>
					</div>
					<div class="form-group col-half last inline<?php $this->has_error( 'tour-signup-billing-zip' ); ?>">
						<label for="tour-signup-billing-zip"><?php _e( 'Zip', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" name="billing[zip]" size="5" id="tour-signup-billing-zip" class="guardian-address required" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'billing', 'zip' ); ?>" />
						</div>
					</div>
				</div>
				<div class="col-half last">
					<div class="form-group">
						<label for=""><?php _e( 'Tour', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" readonly="readonly" class="readonly" value="<?php echo $this->post->post_title; ?>" />
						</div>
					</div>
					<div class="form-group">
						<label for=""><?php _e( 'Trip ID', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" readonly="readonly" class="readonly" value="<?php echo $this->tour_code; ?>" />
						</div>
					</div>
					<div id="scolarship-result">
						<div class="form-group">
							<label for=""><?php _e( 'Scholarship Code', 'myworldclass' ); ?></label>
							<div class="form-group-input">
								<input type="text" id="scholarship-code" name="billing[scholarship_code]" value="<?php echo $this->get_value( 'billing', 'scholarship_code' ); ?>" /> 
								<button type="button" id="apply-scholarship-code" data-tour-id="<?php echo $this->post_id; ?>" data-signup-id="<?php echo $this->signup_id; ?>">Apply Code</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="traveler-widget">
				<div class="signup-widget">
					<h3><?php _e( 'Tour Cost', 'myworldclass' ); ?></h3>
					<ul>
						<li><strong><?php _e( 'Travelers', 'myworldclass' ); ?>: </strong> <?php echo count( $this->signup->travelers ); ?></li>
						<li><strong><?php _e( 'Tour Price', 'myworldclass' ); ?>: </strong> $ <span id="tour-cost"><?php echo $this->get_cost(); ?></span></li>
						<li><strong><?php _e( 'Scholarship Code', 'myworldclass' ); ?>: </strong> $ <span id="discount"><?php echo $this->get_discount(); ?></span></li>
						<li><strong><?php _e( 'Your Price', 'myworldclass' ); ?>: </strong> $ <span id="final-price"><?php echo $this->get_total_due(); ?></span></li>
					</ul>
					<p class="view-tour"><a href="<?php echo get_permalink( $this->post_id ); ?>">View Tour Page</a></p>
				</div>
			</div>
		</div>
	</div>
	<div id="submit-form-row" class="clear">
		<button type="button" class="go-back-in-signup" data-to="2" data-tour-id="<?php echo $this->post_id; ?>" data-signup-id="<?php echo $this->signup_id; ?>"><i class="fa fa-arrow-right"></i> <?php _e( 'Back', 'myworldclass' ); ?></button>
		<button type="submit" id="submit-tour-signup"><?php _e( 'Next', 'myworldclass' ); ?> <i class="fa fa-arrow-left"></i></button>
	</div>
<?php

		// Payment Plan
		elseif ( $this->signup_step == 4 ) :

?>
	<h1 class="blue"><?php _e( 'Choose a Payment Plan', 'myworldclass' ); ?></h1>
<?php

			if ( isset( $this->errors['charge'] ) && ! empty( $this->payment_errors ) )
				echo '<div class="alert alert-warning">' . sprintf( __( 'Payment Failed. The following error was given: %s', '' ), $this->payment_errors ) . '</div>';
			elseif ( isset( $this->errors ) && ! empty( $this->errors ) )
				echo '<div class="alert alert-warning">' . __( 'Errors were found in your submission. Please correct them and try again.', '' ) . '</div>';

			$now          = current_time( 'timestamp' );
			$payment_date = strtotime( $this->last_pay_date );
			$days_left    = ( $payment_date - $now ) / DAY_IN_SECONDS;
			$months_left  = $days_left / 30;

			$final   = $this->get_total_due();
			$minimum = $this->minimum;

			$left_to_pay     = $final - $minimum;
			$monthly_payment = number_format( ( $left_to_pay / $months_left ), 2, '.', '' );

			$sixtyday      = $this->sixty_days;
			$selected_plan = $this->get_value( 'payment', 'plan' );

?>
	<div class="clear">
		<div class="travelers" id="parent1" data-row="-1">
			<div class="traveler-info">
				<div class="payment-plan">
					<div>
						<h1><?php _e( 'Automatic Monthly Installment Plan', 'myworldclass' ); ?></h1>
						<p><?php _e( 'It\'s Easy! Fill out the Enrollment Form enclosed in your invitation packet and submit by mail to World Classrooms or hand in at the Parent/Student Informational meeting. After your initial Enrollment Deposit of <strong>$99</strong>, small, manageable installments will be deducted on the <strong>25th of each month</strong> directly from your checking account or charged to your credit/debit card.', 'myworldclass' ); ?></p>
					</div>
				</div>
				<div class="payment-plan">
					<label for="payment-plan-manual">
						<input type="radio" name="payment[plan]"<?php checked( $selected_plan, 'manual' ); ?> id="payment-plan-manual" data-hide="no" value="manual" />
						<div>
							<h1><?php _e( 'Manual Payment Plan', 'myworldclass' ); ?></h1>
							<p><?php printf( __( 'Three payment deadlines apply to this plan. Upon enrollment a minimum %s deposit is due. A %s payment is due 60 days after your enrollment date. The remaining balance is due 110 days before trip departure.', 'myworldclass' ), '<strong>$ ' . $minimum . '</strong>', '<strong>$ ' . $sixtyday . '</strong>' ); ?></p>
							<div class="manual-input<?php $this->has_error( 'payment-manual' ); ?>">
								<label for=""><?php _e( 'Initial Deposit:', 'myworldclass' ); ?> <input type="text" size="15" placeholder="0.00" name="payment[manual]" id="payment-manual" value="<?php echo $this->get_value( 'payment', 'manual' ); ?>" /> <small>(<?php printf( __( 'Minimum %s', '' ), '<strong>$ ' . $minimum . '</strong>' ); ?>)</small></label>
							</div>
						</div>
					</label>
				</div>
				<div class="payment-plan">
					<label for="payment-plan-full">
						<input type="radio" name="payment[plan]"<?php checked( $selected_plan, 'full' ); ?> id="payment-plan-full" data-hide="no" value="full" />
						<div>
							<h1><?php _e( 'Pay In Full Today', 'myworldclass' ); ?></h1>
							<p><?php printf( __( 'I choose to pay the total cost of the tour in the amount of %s.', 'myworldclass' ), '<strong>$ ' . $final . '</strong>' ); ?></p>
						</div>
					</label>
				</div>
				<div class="clear clearfix"></div>
				<div id="credit-card-form"<?php if ( $selected_plan == 'auto' ) echo ' style="display:none;"'; ?>>
				<h4 class="blue" style="margin-bottom:12px;">Credit Card Details</h4>
				<div class="col-half">
					<div class="form-group<?php $this->has_error( 'payment-card' ); ?>">
						<label for="payment-card"><?php _e( 'Card Number', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" class="required" name="payment[card]" id="payment-card" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'payment', 'card' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'payment-exp-mm' ); ?>">
						<label for="payment-exp-mm"><?php _e( 'Expiration Date', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<select name="payment[exp_mm]" class="select required" id="payment-exp-mm">
<?php

			$months = array(
				''   => 'Month',
				'01' => '01',
				'02' => '02',
				'03' => '03',
				'04' => '04',
				'05' => '05',
				'06' => '06',
				'07' => '07',
				'08' => '08',
				'09' => '09',
				'10' => '10',
				'11' => '11',
				'12' => '12'
			);
			$selected_month = $this->get_value( 'payment', 'exp_mm' );
			foreach ( $months as $month => $label ) {
				echo '<option value="' . $month . '"';
				if ( $selected_month == $month ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
							</select> / 
							<select name="payment[exp_yy]" class="select required" id="payment-exp-yy">
<?php

			$until         = (int) date( 'Y' ) + 10;
			$years         = range( date( 'Y' ), $until );
			$selected_year = $this->get_value( 'payment', 'exp_yy' );
			echo '<option value="">Year</option>';
			foreach ( $years as $year ) {
				echo '<option value="' . $year . '"';
				if ( $selected_year == $year ) echo ' selected="selected"';
				echo '>' . $year . '</option>';
			}

?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-half last">
					<div class="form-group<?php $this->has_error( 'payment-card' ); ?>">
						<label for="payment-name"><?php _e( 'Name on Card', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" class="required" name="payment[name]" id="payment-name" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'payment', 'name' ); ?>" />
						</div>
					</div>
					<div class="form-group<?php $this->has_error( 'payment-cvv' ); ?>">
						<label for="payment-cvv"><?php _e( 'CVV', 'myworldclass' ); ?></label>
						<div class="form-group-input">
							<input type="text" class="required auto-width" name="payment[cvv]" id="payment-cvv" maxlength="5" size="8" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="" />
						</div>
					</div>
				</div>
				</div>
				<div class="clear clearfix"></div>
				<div id="terms">
					<label for="accept-terms"><input type="checkbox" id="accept-terms" name="payment[accept]" class="must-be-ticked" value="1" />I agree to the <a href="<?php echo esc_url( $this->prefs['booking_legal'] ); ?>" target="_blank">booking conditions</a> and the <a href="<?php echo esc_url( $this->prefs['terms_legal'] ); ?>" target="_blank">terms & conditions</a>.</label>
				</div>
			</div>
			<div class="traveler-widget">
				<div class="signup-widget">
					<h3><?php _e( 'Tour Cost', 'myworldclass' ); ?></h3>
					<ul>
						<li><strong><?php _e( 'Travelers', 'myworldclass' ); ?>: </strong> <?php echo count( $this->signup->travelers ); ?></li>
						<li><strong><?php _e( 'Tour Price', 'myworldclass' ); ?>: </strong> $ <span id="tour-cost"><?php echo $this->get_cost(); ?></span></li>
						<li><strong><?php _e( 'Scholarship Code', 'myworldclass' ); ?>: </strong> $ <span id="discount"><?php echo $this->get_discount(); ?></span></li>
						<li><strong><?php _e( 'Your Price', 'myworldclass' ); ?>: </strong> $ <span id="final-price"><?php echo $final; ?></span></li>
					</ul>
					<p class="view-tour"><a href="<?php echo get_permalink( $this->post_id ); ?>">View Tour Page</a></p>
				</div>
			</div>
		</div>
	</div>
	<div id="submit-form-row" class="clear">
		<button type="button" class="go-back-in-signup" data-to="3" data-tour-id="<?php echo $this->post_id; ?>" data-signup-id="<?php echo $this->signup_id; ?>"><i class="fa fa-arrow-right"></i> <?php _e( 'Back', 'myworldclass' ); ?></button>
		<button type="submit" id="submit-tour-signup" class="final"><?php _e( 'Submit', 'myworldclass' ); ?> <i class="fa fa-arrow-left"></i></button>
	</div>
<?php

		// Confirmation
		else :

?>
	<h1 class="blue"><?php _e( 'Enrollment Completed', 'myworldclass' ); ?></h1>
	<p><?php _e( 'Thank you for your payment! Your enrollment has now been completed and a confirmation email has been sent to you. You should also receive a payment receipt from our credit card manager Authorize.net. Feel free to contact us if you have any questions.', 'myworldclass' ); ?></p>
<?php

		endif;

?>	
</form>
<?php

		}

		/**
		 * Display Tour Code
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_tour_code() {

			if ( $this->tour_code != '' )
				return $this->tour_code;
			else
				return '-';

		}

		/**
		 * Display Cost
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_cost( $user_id = NULL ) {

			if ( $user_id === NULL )
				return '$ ' . number_format( $this->cost, 2, '.', ' ' );

			return '$ ' . mywclass_get_cost( $this->post_id, $user_id );

		}

		/**
		 * Display Notice
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_notice() {

			$notice = get_post_meta( $this->post_id, 'notice', true );
			if ( $notice == '' ) return '';

			$notice = wpautop( wptexturize( $notice ) );
			return '<div class="tour-notice">' . $notice . '</div>';

		}

		/**
		 * Display Downloads
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_downloads( $all = false ) {

			if ( ! isset( $this->prefs['common_downloads'] ) ) return;

			$content = $this->prefs['common_downloads'];

			if ( $all )
				$content .= '<hr id="general-tour-devider" />' . $this->downloads;

			return wpautop( wptexturize( $content ) );

		}

		/**
		 * Display Start Date
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_start_date() {

			if ( $this->start_date != '' )
				return date( $this->date_format, strtotime( $this->start_date ) );
			else
				return '-';

		}

		/**
		 * Display End Date
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_end_date() {

			if ( $this->end_date != '' )
				return date( $this->date_format, strtotime( $this->end_date ) );
			else
				return '-';

		}

		/**
		 * Display Last Payment Date
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_last_pay_date() {

			if ( $this->last_pay_date != '' )
				return date( $this->date_format, strtotime( $this->last_pay_date ) );
			else
				return '-';

		}

		/**
		 * Get Tour Length
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_tour_length() {

			if ( $this->start_date == '' || $this->start_date == 0 || $this->end_date == '' || $this->end_date == 0 )
				return '-';

			$start = strtotime( $this->start_date );
			$end   = strtotime( $this->end_date );
			$end   = $end + ( DAY_IN_SECONDS - 1 );

			$days  = ( $end - $start ) / DAY_IN_SECONDS;
			return ceil( $days );

		}

		/**
		 * Get Cost
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_cost() {

			$user_id = $this->user_id;
			if ( isset( $this->signup->user_id ) && $this->signup->user_id !== NULL )
				$user_id = $this->signup->user_id;

			$custom = get_user_meta( $user_id, 'custom_tour_cost', true );
			if ( $custom != '' )
				return number_format( $custom, 2, '.', '' );

			$cost = 0;

			foreach ( $this->signup->travelers as $row => $data ) {

				if ( $data['first_name'] == '' && $data['last_name'] == '' ) continue;

				if ( $data['type'] == 'student' )
					$cost = $cost + $this->cost;
				else
					$cost = $cost + $this->cost_adult;

			}

			return number_format( $cost, 2, '.', '' );

		}

		/**
		 * Get Discount
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_discount() {

			$code = false;
			if ( isset( $this->signup->billing['scholarship_code'] ) )
				$code = $this->signup->billing['scholarship_code'];

			if ( ! $code )
				return 0.00;

			$value = $this->get_scholarship_value( $code );
			return number_format( ( count( $this->signup->travelers ) * $value ), 2, '.', '' );

		}

		/**
		 * Get Total Due
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_total_due() {

			$cost = $this->get_cost();
			$cost = $cost - $this->get_discount();

			return number_format( $cost, 2, '.', '' );

		}

		/**
		 * Get Scholarships
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_scholarships() {

			$default = array(
				'code'  => '',
				'value' => ''
			);

			$scholarships = array();
			$_scholarships = (array) get_post_meta( $this->post_id, 'scholarships', true );
			$number = 2;
			for ( $i = 0; $i < $number; $i++ ) {

				if ( isset( $_scholarships[ $i ] ) )
					$scholarships[ $i ] = wp_parse_args( $_scholarships[ $i ], $default );

				else
					$scholarships[ $i ] = $default;

			}

			return $scholarships;

		}

		/**
		 * Is Valid Scholarship Code?
		 * @since 1.0
		 * @version 1.0
		 */
		public function is_valid_scholarship_code( $code = '' ) {

			$scholarships = $this->get_scholarships();

			$valid = false;
			if ( ! empty( $scholarships ) ) {
				foreach ( $scholarships as $row => $data ) {

					if ( $data['code'] != '' && $data['code'] == $code ) {
						$valid = true;
						break;
					}

				}
			}

			return $valid;

		}

		/**
		 * Get Scholarship Value
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_scholarship_value( $code = '' ) {

			if ( $code == '' ) return '-';
			$scholarships = $this->get_scholarships();

			$value = 0;
			if ( ! empty( $scholarships ) ) {
				foreach ( $scholarships as $row => $data ) {

					if ( $data['code'] != '' && $data['code'] == $code ) {
						$value = $data['value'];
						break;
					}

				}
			}

			return number_format( $value, 2, '.', '' );

		}

		/**
		 * Get Attendees
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_attendees( $recount = false ) {

			if ( ! $recount ) {

				$this->attendees          = (array) get_post_meta( $this->post_id, 'attendees', true );
				$this->attendees_signedup = (int) get_post_meta( $this->post_id, 'attendees_signedup', true );
				$this->attendee_count     = count( $this->attendees );

			}
			else {

				if ( $this->tour_code == '' ) return;

				global $wpdb;

				$this->attendees = $wpdb->get_col( "
					SELECT DISTINCT user_id 
					FROM {$wpdb->usermeta} 
					WHERE meta_key = 'tour_code' 
					AND meta_value = '{$this->tour_code}';" );

				update_post_meta( $this->post_id, 'attendees', $this->attendees );

				$signups = $wpdb->get_col( "
					SELECT travelers 
					FROM {$this->db} 
					WHERE tour_id = {$this->post_id} 
					AND status IN ( 'pending', 'paid' );" );

				$count = 0;
				foreach ( $signups as $travelers ) {

					$travelers = unserialize( $travelers );
					$count = $count + count( $travelers );

				}

				update_post_meta( $this->post_id, 'attendees_signedup', $count );

				$this->attendee_count = count( $this->attendees );

			}

		}

		/**
		 * Get States
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_states() {

			return array( 'AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY' );

		}

		/**
		 * Get Months
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_months() {

			return array(
				''   => __( 'Month', 'myworldclass' ),
				'01' => __( 'January', 'myworldclass' ),
				'02' => __( 'February', 'myworldclass' ),
				'03' => __( 'March', 'myworldclass' ),
				'04' => __( 'April', 'myworldclass' ),
				'05' => __( 'May', 'myworldclass' ),
				'06' => __( 'June', 'myworldclass' ),
				'07' => __( 'July', 'myworldclass' ),
				'08' => __( 'August', 'myworldclass' ),
				'09' => __( 'September', 'myworldclass' ),
				'10' => __( 'October', 'myworldclass' ),
				'11' => __( 'November', 'myworldclass' ),
				'12' => __( 'December', 'myworldclass' )
			);

		}

		/**
		 * Get Relationships
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_relationships() {

			return array(
				''       => __( 'Select Relationship', 'myworldclass' ),
				'father' => __( 'Father', 'myworldclass' ),
				'mother' => __( 'Mother', 'myworldclass' ),
				'legal'  => __( 'Legal Guardian', 'myworldclass' ),
				'other'  => __( 'Other', 'myworldclass' )
			);

		}

		/**
		 * Get Phone Types
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_phone_types() {

			return array(
				'cell' => __( 'Cell', 'myworldclass' ),
				'home' => __( 'Home', 'myworldclass' ),
				'Work' => __( 'Work', 'myworldclass' )
			);

		}

		/**
		 * Get Payment Plan Labels
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_plan_labels() {

			return array(
				'auto'   => __( 'Automatic Monthly Installment Plan', 'myworldclass' ),
				'manual' => __( 'Manual Payment Plan', 'myworldclass' ),
				'full'   => __( 'Paid in Full', 'myworldclass' )
			);

		}

		/**
		 * Display Payment Plan
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_payment_plan( $plan = '' ) {

			$plans = $this->get_plan_labels();
			if ( array_key_exists( $plan, $plans ) )
				return $plans[ $plan ];

			return $plans['manual'];

		}

		/**
		 * Display Attendee Count Admin
		 * @since 1.0
		 * @version 1.0
		 */
		public function display_attendee_count_admin() {

			$this->get_attendees( true );
			if ( $this->attendee_count == 0 )
				return 0;

			else {

				$url = add_query_arg( array( 'tour_code' => $this->tour_code ), admin_url( 'users.php' ) );
				return '<a href="' . $url . '">' . $this->attendee_count . '</a>';

			}

		}

		public function display_account_overview_box( $amount_owed = 0.00 ) {

			$user = get_userdata( get_current_user_id() );

			if ( isset( $this->signup->plan ) )
				$plan = $this->signup->plan;
			else
				$plan = 'manual';

?>
<div id="my-account-overview">
	<div class="half">
		<ul class="left">
			<li><strong>Name:</strong><span><?php echo $user->display_name; ?></span></li>
			<li><strong>Trip ID:</strong><span><?php echo $this->tour_code; ?></span></li>
		</ul>
		<ul class="left">
			<li class="box">
				<div>
					<small><?php _e( 'Payment Plan:', '' ); ?></small>
					<h4><?php echo $this->display_payment_plan(  ); ?></h4>
				</div>
			</li>
		</ul>
	</div>
	<div class="half last">
		<ul class="right">
			<li><strong>Total Trip Cost:</strong><span>$ <?php echo $this->get_total_due(); ?></span></li>
			<li><strong>Balance Remaining:</strong><span>$ <span id="balance-after-payment-top"><?php echo number_format( $amount_owed, 2, '.', '' ); ?></span></span></li>
		</ul>
		<?php if ( $this->signup->plan != 'full' ) : ?>
		<ul class="right">
			<li><strong></strong><span></span></li>
			<li><strong></strong><span></span></li>
		</ul>
		<?php endif; ?>
	</div>
	<div class="clear clearfix"></div>
</div>
<?php

		}

		public function display_manual_payment_form( $amount_owed = 0.00 ) {

			$amount_owed = (float) str_replace( ' ', '', $amount_owed );

			if ( ! isset( $this->signup->status ) || $this->signup->status == 'full' || $amount_owed == 0 )
				return;

			if ( isset( $this->signup->plan ) && $this->signup->plan == 'auto' )
				return;

?>
<form method="post" action="" id="make-manual-payment-form">
	<div id="amount-to-be-paid-wrapper" class="manual-input">
		<label for="payment-manual-amount"><?php _e( 'Payment Amount:', 'myworldclass' ); ?> <input type="text" size="15" placeholder="<?php echo $this->minimum; ?>" name="amount" max="<?php echo number_format( $amount_owed, 2, '.', '' ); ?>" class="auto-width" id="payment-manual-amount" value="" /></label>
	</div>
	<h4 class="blue" style="margin-bottom:12px;"><?php _e( 'Credit Card Details', 'myworldclass' ); ?></h4>
	<div class="col-half">
		<div class="form-group<?php $this->has_error( 'payment-card' ); ?>">
			<label for="payment-card"><?php _e( 'Card Number', 'myworldclass' ); ?></label>
			<div class="form-group-input">
				<input type="text" class="required" name="card" id="payment-card" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'payment', 'card' ); ?>" />
			</div>
		</div>
		<div class="form-group<?php $this->has_error( 'payment-exp-mm' ); ?>">
			<label for="payment-exp-mm"><?php _e( 'Expiration Date', 'myworldclass' ); ?></label>
			<div class="form-group-input">
				<select name="exp_mm" class="select required" id="payment-exp-mm">
<?php

			$months = array(
				''   => 'Month',
				'01' => '01',
				'02' => '02',
				'03' => '03',
				'04' => '04',
				'05' => '05',
				'06' => '06',
				'07' => '07',
				'08' => '08',
				'09' => '09',
				'10' => '10',
				'11' => '11',
				'12' => '12'
			);
			$selected_month = $this->get_value( 'payment', 'exp_mm' );
			foreach ( $months as $month => $label ) {
				echo '<option value="' . $month . '"';
				if ( $selected_month == $month ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
				</select> / <select name="exp_yy" class="select required" id="payment-exp-yy">
<?php

			$until         = (int) date( 'Y' ) + 10;
			$years         = range( date( 'Y' ), $until );
			$selected_year = $this->get_value( 'payment', 'exp_yy' );
			echo '<option value="">Year</option>';
			foreach ( $years as $year ) {
				echo '<option value="' . $year . '"';
				if ( $selected_year == $year ) echo ' selected="selected"';
				echo '>' . $year . '</option>';
			}

?>
				</select>
			</div>
		</div>
	</div>
	<div class="col-half last">
		<div class="form-group<?php $this->has_error( 'payment-card' ); ?>">
			<label for="payment-name"><?php _e( 'Name on Card', 'myworldclass' ); ?></label>
			<div class="form-group-input">
				<input type="text" class="required" name="name" id="payment-name" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="<?php echo $this->get_value( 'payment', 'name' ); ?>" />
			</div>
		</div>
		<div class="form-group<?php $this->has_error( 'payment-cvv' ); ?>">
			<label for="payment-cvv"><?php _e( 'CVV', 'myworldclass' ); ?></label>
			<div class="form-group-input">
				<input type="text" class="required auto-width" name="cvv" id="payment-cvv" maxlength="5" size="8" placeholder="<?php _e( 'Required', 'myworldclass' ); ?>" value="" />
			</div>
		</div>
	</div>
	<div id="submit-row">
		<input type="submit" id="submit-manual-payment" value="Submit Payment" />
	</div>
	<div class="clear clearfix"></div>
</form>
<?php

		}

		/**
		 * Charge Card
		 * @since 1.0
		 * @version 1.0
		 */
		public function charge_card( $payment, $save = true ) {

			global $wpdb;

			$now    = current_time( 'timestamp' );
			$result = false;

			// Auto Subscribe
			if ( $payment['plan'] == 'auto' ) {

				$payment_date = strtotime( $this->last_pay_date );
				$days_left    = ( $payment_date - $now ) / DAY_IN_SECONDS;
				$months_left  = $days_left / 30;
				$final        = $this->get_total_due();

				$amount_charged  = number_format( $this->minimum, 2, '.', '' );
				$monthly_payment = number_format( ( ( $final - $this->minimum ) / $months_left ), 2, '.', '' );

				// OVERIDE UNTIL CLIENT KNOWS WHAT TO DO WITH SUBSCRIPTIONS

				$this->payment_id     = 'autosubscribe';
				$this->payment_amount = 0.00;
				$this->payment_desc   = 'Monthly Installment Plan Signup';

				$data = array( 'payment_id' => 'autosubscribe', 'payment' => 0.00, 'status' => 'pending', 'plan' => $payment['plan'] );
				$prep = array( '%s', '%f', '%s', '%s' );

				$wpdb->update(
					$this->db,
					$data,
					array( 'id' => $this->signup_id ),
					$prep,
					array( '%d' )
				);

				return true;

				// END

				$address = $this->signup->billing['address1'];
				if ( $this->signup->billing['address2'] != '' )
					$address .= ', ' . $this->signup->billing['address2'];

				$subscription = mywclass_authorize_net_subscription( array(
					'first_name' => $this->signup->billing['first_name'],
					'last_name'  => $this->signup->billing['last_name'],
					'address1'   => $address,
					'city'       => $this->signup->billing['city'],
					'state'      => $this->signup->billing['state'],
					'zip'        => $this->signup->billing['zip'],
					'card'       => $payment['card'],
					'cvv'        => $payment['cvv'],
					'exp_mm'     => $payment['exp_mm'],
					'exp_yy'     => $payment['exp_yy'],
					'payment_id' => $this->signup_id
				), array(
					'description' => 'WorldClassrooms Monthly Tour Payment',
					'cost'        => $amount_charged,
					'occurences'  => absint( $months_left ),
					'trial'       => $this->minimum
				) );

				// Failed
				if ( isset( $subscription['errors'] ) ) {

					$this->payment_errors = $subscription['errors'];

				}

				else {

					$result     = true;

					$data = array( 'payment_id' => $subscription, 'payment' => $amount_charged, 'status' => 'pending', 'plan' => $payment['plan'] );
					$prep = array( '%s', '%f', '%s', '%s' );

					$this->payment_id     = $subscription;
					$this->payment_amount = $amount_charged;
					$this->payment_desc   = 'Enrollment Fee';

				}

			}

			// Manual Amount
			elseif ( $payment['plan'] == 'manual' ) {

				$amount_charged = number_format( $payment['manual'], 2, '.', '' );

				$partial_payment = mywclass_authorize_net_charge( array(
					'card'       => $payment['card'],
					'exp_mm'     => $payment['exp_mm'],
					'exp_yy'     => $payment['exp_yy'],
					'payment_id' => $this->signup_id
				), array( 'cost' => $amount_charged ) );

				// Failed
				if ( isset( $partial_payment['errors'] ) ) {

					$this->payment_errors = $partial_payment['errors'];

				}

				else {

					$result     = true;

					$data = array( 'payment_id' => $partial_payment, 'payment' => $amount_charged, 'status' => 'pending', 'plan' => $payment['plan'] );
					$prep = array( '%s', '%f', '%s', '%s' );

					$this->payment_id     = $partial_payment;
					$this->payment_amount = $amount_charged;
					$this->payment_desc   = 'Enrollment Fee';

				}

			}

			// Payment in full
			else {

				$final          = $this->get_total_due();
				$amount_charged = number_format( $final, 2, '.', '' );

				$payment_in_full = mywclass_authorize_net_charge( array(
					'card'       => $payment['card'],
					'exp_mm'     => $payment['exp_mm'],
					'exp_yy'     => $payment['exp_yy'],
					'payment_id' => $this->signup_id
				), array( 'cost' => $amount_charged ) );

				// Failed
				if ( isset( $payment_in_full['errors'] ) ) {

					$this->payment_errors = $payment_in_full['errors'];

				}

				else {

					$result     = true;

					$data = array( 'payment_id' => $payment_in_full, 'payment' => $amount_charged, 'status' => 'paid', 'plan' => $payment['plan'] );
					$prep = array( '%s', '%f', '%s', '%s' );

					$this->payment_id     = $payment_in_full;
					$this->payment_amount = $amount_charged;
					$this->payment_desc   = 'Tour payment in Full';

				}

			}

			if ( ! $result )
				return false;

			if ( $save )
				$wpdb->update(
					$this->db,
					$data,
					array( 'id' => $this->signup_id ),
					$prep,
					array( '%d' )
				);

			return true;

		}

		/**
		 * Register New User
		 * @since 1.0
		 * @version 1.0
		 */
		public function register_new_user() {

			if ( ! is_user_logged_in() ) {

				$new_password = wp_generate_password( 12, false, false );

				$new_login = str_replace( array( '@', '-', '_', '.', '.com' ), '', $this->signup->parents[0]['user_email'] );
				if ( $new_login == '' )
					$new_login = wp_generate_password( 12, false, false );

				$new_user = array(
					'first_name'   => $this->signup->parents[0]['first_name'],
					'last_name'    => $this->signup->parents[0]['last_name'],
					'display_name' => $this->signup->parents[0]['first_name'] . ' ' . $this->signup->parents[0]['last_name'],
					'user_email'   => $this->signup->parents[0]['user_email'],
					'user_pass'    => $new_password,
					'user_login'   => $new_login
				);

				$user_id = wp_insert_user( $new_user );
				if ( $user_id !== NULL && ! is_wp_error( $user_id ) ) {

					$this->send_email( 'enrolment', $new_user );

					global $wpdb;

					$wpdb->update(
						$this->db,
						array( 'user_id' => $user_id ),
						array( 'id' => $this->signup_id ),
						array( '%d' ),
						array( '%d' )
					);

					return $user_id;

				}
				elseif ( is_wp_error( $user_id ) )
					$this->register_error = $user_id->get_error_message();

				return false;

			}

			return $this->user_id;

		}

		/**
		 * Send Email
		 * @since 1.0
		 * @version 1.0
		 */
		public function send_email( $type = 'enrolment', $data = array() ) {

			$content = '';
			if ( $type == 'enrolment' ) {

				$content = $this->prefs['emails']['enrolment']['body'];
				$content = str_replace( '%first_name%',    $data['first_name'], $content );
				$content = str_replace( '%last_name%',     $data['last_name'], $content );
				$content = str_replace( '%user_email%',    $data['user_email'], $content );
				$content = str_replace( '%user_pass%',     $data['user_pass'], $content );

				$content = str_replace( '%tour_title%',    $this->post->post_title, $content );
				$content = str_replace( '%trip_id%',       $this->tour_code,        $content );
				$content = str_replace( '%start_date%',    $this->start_date,       $content );
				$content = str_replace( '%end_date%',      $this->end_date,         $content );
				$content = str_replace( '%teacher%',       $this->teacher_name,     $content );
				$content = str_replace( '%teacher_email%', $this->teacher_email,    $content );
				$content = str_replace( '%school%',        $this->school,           $content );
				$content = str_replace( '%location%',      $this->location,         $content );
				// $content = str_replace( '%%', $data[''], $content );
				// $content = str_replace( '%%', $data[''], $content );

				$subject = $this->prefs['emails']['enrolment']['subject'];

			}

			elseif ( $type == 'password' ) {

				$content = $this->prefs['emails']['password']['body'];
				$subject = $this->prefs['emails']['password']['subject'];

			}

			if ( $content == '' ) return;

			wp_mail( $data['user_email'], $subject, $content );
			wp_mail( get_option( 'admin_email' ), $subject, $content );

		}

	}
endif;

?>