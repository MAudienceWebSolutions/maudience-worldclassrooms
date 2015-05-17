<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Get Plugin Settings
 * @since 1.0
 * @version 1.0
 * 414 / 444
 */
if ( ! function_exists( 'mywclass_get_settings' ) ) :
	function mywclass_get_settings() {

		$default = array(
			'redirect_login'     => 1,
			'my_account_page_id' => 444,
			'paypal' => array(
				'sandbox' => 0,
				'email' => '',
				'item'  => 'Partial Payment for Tour %tour_code%',
				'min'   => 0
			)
		);

		$settings = get_option( 'world_classroom_prefs', $default );
		return $settings;

	}
endif;

/**
 * Get Userdata
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_get_userdata' ) ) :
	function mywclass_get_userdata( $user_id = NULL ) {

		$user = get_userdata( $user_id );
		if ( $user === false ) return false;

		$user->user_phone  = get_user_meta( $user_id, 'user_phone', true );
		$user->dob         = get_user_meta( $user_id, 'user_dob', true );
		$user->parent_name = get_user_meta( $user_id, 'parent_name', true );
		$user->tour_code   = get_user_meta( $user_id, 'tour_code', true );
		$user->high_school = get_user_meta( $user_id, 'high_school', true );

		return $user;

	}
endif;

/**
 * Get Users Balance
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_get_users_balance' ) ) :
	function mywclass_get_users_balance( $user_id = NULL ) {

		$balance = get_user_meta( $user_id, 'mywclass_balance', true );
		if ( $balance == '' ) {
			$balance = 0.00;
			update_user_meta( $user_id, 'mywclass_balance', $balance );
		}
		return $balance;

	}
endif;

/**
 * Update Users Balance
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_update_users_balance' ) ) :
	function mywclass_update_users_balance( $user_id = NULL, $amount = 0 ) {

		$balance = mywclass_get_users_balance( $user_id );
		$new_balance = $balance + $amount;
		update_user_meta( $user_id, 'mywclass_balance', $new_balance );

	}
endif;


/**
 * Get Cost
 * @since 1.0.1
 * @version 1.0.1
 */
if ( ! function_exists( 'mywclass_get_cost' ) ) :
	function mywclass_get_cost( $tour_id = NULL, $student_id = NULL ) {

		$cost = 0;
		$tour = new MyWorldClass_Tour( $tour_id );

		$students = 1;
		$extra_student = get_user_meta( $student_id, 'extra_student', true );
		if ( strtolower( $extra_student ) == 'yes' )
			$students = 2;

		// If custom cost is set, then this is the only thing that matters
		$user_cost_override = get_user_meta( $student_id, 'custom_tour_cost', true );
		if ( strlen( $user_cost_override ) > 0 )
			return number_format( (float) $user_cost_override, 2, '.', '' );

		$cost = $students * $tour->cost;

		$parents_attending = get_user_meta( $student_id, 'parents_attending', true );
		if ( strtolower( $parents_attending ) == 'yes' ) {
			$parents = get_user_meta( $student_id, 'no_of_parents', true );
			if ( $parents == '' || strtolower( $parents ) == 'one' )
				$parents = 1;
			else
				$parents = 2;

			$cost = ( $parents * $tour->cost_adult ) + $cost;
		}

		return $cost;

	}
endif;

/**
 * Enqueue Front Scripts & Styles
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_enqueue_front_scripts' ) ) :
	function mywclass_enqueue_front_scripts() {

		// Register Front End Profile Styling
		wp_register_style(
			'mywclass-my-account',
			plugins_url( 'assets/css/my-account.css', MYWORLDCLASS ),
			false,
			MYWORLDCLASS_VERSION . '.1',
			'all'
		);

		// Only load this file on the appropriate pages
		if ( mywclass_is_account_pages() )
			wp_enqueue_style( 'mywclass-my-account' );

	}
endif;

/**
 * Is Account Pages
 * Checks if the current page is the profile page or any of it's
 * children. Returns true or false. Should not be used in instances
 * earlier than template_redirect!
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_is_account_pages' ) ) :
	function mywclass_is_account_pages() {

		if ( is_page() ) {

			global $post;

			$prefs = mywclass_get_settings();
			if ( is_page( $prefs['my_account_page_id'] ) || $post->post_parent == $prefs['my_account_page_id'] )
				return true;

		}
		return false;

	}
endif;

/**
 * Add Custom Contact Methods
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_add_custom_contact_methods' ) ) :
	function mywclass_add_custom_contact_methods( $methods ) {

		$methods['user_phone'] = __( 'Phone Number', 'myworldclass' );
		$methods['parent_name'] = __( 'Parent or Guardians Name', 'myworldclass' );
		return $methods;

	}
endif;

/**
 * Front End Profile Updates
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_front_end_profile_updates' ) ) :
	function mywclass_front_end_profile_updates() {

		if ( ! is_user_logged_in() || ! mywclass_is_account_pages() ) return;

		$cui = get_current_user_id();

		// Nothing to do?
		if ( ! isset( $_POST['myaccount']['nonce'] ) || ! wp_verify_nonce( $_POST['myaccount']['nonce'], 'world-classes-edit-profile' . $cui ) ) return;

		// Lets act
		$user = new MyWorldClass_Student( $cui );

		$errors = false;

		// First we validate our profile fields
		$first_name = sanitize_text_field( $_POST['myaccount']['first_name'] );
		if ( $first_name == '' )
			$errors['first_name'] = __( 'First name can not be empty', 'myworldclass' );

		$last_name = sanitize_text_field( $_POST['myaccount']['last_name'] );
		if ( $last_name == '' )
			$errors['last_name'] = __( 'Last name can not be empty', 'myworldclass' );

		$user_email = sanitize_text_field( $_POST['myaccount']['user_email'] );
		if ( $user_email == '' )
			$errors['user_email'] = __( 'Email address can not be empty', 'myworldclass' );
		elseif ( ! is_email( $user_email ) )
			$errors['user_email'] = __( 'Invalid email address', 'myworldclass' );

		$user_phone = sanitize_text_field( $_POST['myaccount']['user_phone'] );
		if ( $user_phone == '' )
			$errors['user_phone'] = __( 'Phone number can not be empty', 'myworldclass' );

		$user_dob = sanitize_text_field( $_POST['myaccount']['user_dob'] );
		if ( $user_dob == '' )
			$errors['user_dob'] = __( 'Date of birth can not be empty', 'myworldclass' );

		$parent_name = sanitize_text_field( $_POST['myaccount']['parent_name'] );
		if ( $parent_name == '' )
			$errors['parent_name'] = __( 'First name can not be empty', 'myworldclass' );

		// No errors
		if ( empty( $errors ) ) {

			// First update the user object
			$user = wp_update_user( array(
				'ID'         => $cui,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'user_email' => $user_email
			) );

			// Next we update the custom user meta
			update_user_meta( $cui, 'user_phone', $user_phone );
			update_user_meta( $cui, 'user_dob', $user_dob );
			update_user_meta( $cui, 'parent_name', $parent_name );

			$url = add_query_arg( array( 'updated' => 1 ) );
			wp_redirect( $url );
			exit;

		}

		// Errors
		else {

			$url = add_query_arg( array( 'updated' => 0 ) );
			wp_redirect( $url );
			exit;

		}

	}
endif;

/**
 * Login Redirect
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_login_redirect' ) ) :
	function mywclass_login_redirect( $redirect_to, $request, $user ) {

		if ( is_wp_error( $user ) ) return $redirect_to;

		$prefs = mywclass_get_settings();
		if ( $prefs['redirect_login'] != 1 ) return $redirect_to;

		if ( user_can( $user->ID, 'edit_users' ) ) return $redirect_to;
		return get_permalink( $prefs['my_account_page_id'] );

	}
endif;


/**
 * Handle PayPal Callbacks
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_handle_paypal_callbacks' ) ) :
	function mywclass_handle_paypal_callbacks() {

		if ( ! isset( $_REQUEST['partial_payment'] ) ) return;

		update_option( 'catch_paypal_ipn', array(
			'get' => $_GET,
			'post' => $_POST
		) );

		if ( mywclass_is_valid_paypal_call() ) {

			$user_id = absint( $_POST['custom'] );

			$student = new MyWorldClass_Student( $user_id );
			if ( ! isset( $student->user->tour_code ) ) return;

			$error = '';
			$new_balance = false;
			$transaction_id = $_POST['txn_id'];
			$amount_paid = $_POST['mc_gross'];
			$status = $_POST['payment_status'];

			$payment = new MyWorldClass_Payments( $student->student_id );

			switch ( $status ) {

				case 'Completed' :
				case 'Canceled_Reversal' :

					$entry = 'Completed Payment';
					$new_balance = $student->balance + $amount_paid;

				break;

				case 'Refunded' :
				case 'Reversed' :

					$entry = 'Refunded Payment';
					$new_balance = $student->balance - $amount_paid;

					if ( $status == 'Reversed' )
						$error = $_POST['ReasonCode'];

				break;

				case 'Pending' :

					$entry = 'Pending Payment';

				break;

				case 'Denied' :
				case 'Failed' :
				case 'Expired' :

					$entry = 'Failed Payment';

					if ( $status == 'Denied' )
						$error = $_POST['ReasonCode'];

				break;

			}

			if ( $new_balance !== false )
				update_user_meta( $student->student_id, 'mywclass_balance', $new_balance );

			$payment->add_payment( $transaction_id, $status, $entry, $amount_paid, $error );

		}

	}
endif;

/**
 * Validate PayPal Call
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_is_valid_paypal_call' ) ) :
	function mywclass_is_valid_paypal_call() {

		$prefs = mywclass_get_settings();
		$prefs = $prefs['paypal'];
		
		// PayPal Host
		if ( $prefs['sandbox'] )
			$host = 'www.sandbox.paypal.com';
		else
			$host = 'www.paypal.com';

		$data = array();
		foreach ( $_POST as $key => $value ) {
			$data[ $key ] = stripslashes( $value );
		}

		// Prep Respons
		$request = 'cmd=_notify-validate';
		$get_magic_quotes_exists = false;
		if ( function_exists( 'get_magic_quotes_gpc' ) )
			$get_magic_quotes_exists = true;

		foreach ( $data as $key => $value ) {
			if ( $get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1 )
				$value = urlencode( stripslashes( $value ) );
			else
				$value = urlencode( $value );

			$request .= "&$key=$value";
		}

		// Call PayPal
		$curl_attempts = 3;
		$attempt = 1;
		$result = '';
		// We will make a x number of curl attempts before finishing with a fsock.
		do {

			$call = curl_init( "https://$host/cgi-bin/webscr" );
			curl_setopt( $call, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
			curl_setopt( $call, CURLOPT_POST, 1 );
			curl_setopt( $call, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $call, CURLOPT_POSTFIELDS, $request );
			curl_setopt( $call, CURLOPT_SSL_VERIFYPEER, 1 );
			curl_setopt( $call, CURLOPT_CAINFO, MYWORLDCLASS_INC_DIR . '/cacert.pem' );
			curl_setopt( $call, CURLOPT_SSL_VERIFYHOST, 2 );
			curl_setopt( $call, CURLOPT_FRESH_CONNECT, 1 );
			curl_setopt( $call, CURLOPT_FORBID_REUSE, 1 );
			curl_setopt( $call, CURLOPT_HTTPHEADER, array( 'Connection: Close' ) );
			$result = curl_exec( $call );

			// End on success
			if ( $result !== false ) {
				curl_close( $call );
				break;
			}

			curl_close( $call );

			// Final try
			if ( $attempt == $curl_attempts ) {
				$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
				$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$header .= "Content-Length: " . strlen( $request ) . "\r\n\r\n";
				$fp = fsockopen( 'ssl://' . $host, 443, $errno, $errstr, 30 );
				if ( $fp ) {
					fputs( $fp, $header . $request );
					while ( ! feof( $fp ) ) {
						$result = fgets( $fp, 1024 );
					}
					fclose( $fp );
				}
			}
			$attempt++;

		} while ( $attempt <= $curl_attempts );
			
		if ( strcmp( $result, "VERIFIED" ) == 0 ) {
			return true;
		}

		return false;

	}
endif;

?>