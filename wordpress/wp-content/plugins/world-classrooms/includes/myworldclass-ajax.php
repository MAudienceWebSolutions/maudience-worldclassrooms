<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * AJAX: Tour Signup
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_ajax_signup_for_tour' ) ) :
	function mywclass_ajax_signup_for_tour() {

		// Security
		$key = sanitize_text_field( $_POST['key'] );

		// Skipp to a different step in the signup process
		if ( isset( $_POST['goto'] ) ) {

			$back = absint( $_POST['goto'] );
			$tour = new MyWorldClass_Tour( absint( $_POST['tour_id'] ) );

			if ( $tour->post === false )
				die;

			if ( ! $tour->can_signup() )
				die ( '<p>' . __( 'You can not signup for this tour.', 'myworldclass' ) . '</p>' );

			$tour->setup_signup( absint( $_POST['signup_id'] ) );

			$tour->go_to_step( $back );

			$tour->display_signup_form();
			die;

		}

		// Get the form
		parse_str( $_POST['form'], $post );

		// Verify nonce for members
		if ( is_user_logged_in() && ! wp_verify_nonce( $key, 'signup-for-tour-' . $post['tour_id'] ) )
			die( 0 );

		$post_id = absint( $post['tour_id'] );
		$tour    = new MyWorldClass_Tour( $post_id );

		// Bad tour id
		if ( $tour->post === false )
			die;

		// User can not signup
		if ( ! $tour->can_signup() )
			die ( '<p>' . __( 'You can not signup for this tour.', 'myworldclass' ) . '</p>' );

		// Setup signup
		$tour->setup_signup( $post['signup_id'] );

		// Validate submission
		$tour->submitted = $post;
		$tour->validate_step( $post );

		// Error management
		if ( empty( $tour->errors ) ) {

			$tour->signup_step ++;
			$tour->update_signup( $post['step'] );

		}

		// Return the form
		$tour->display_signup_form();
		die;

	}
endif;

/**
 * AJAX: Validate Scholarship
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_ajax_validate_scholarship_code' ) ) :
	function mywclass_ajax_validate_scholarship_code() {

		// Security
		if ( ! wp_verify_nonce( $_POST['key'], 'validate-scholarship' ) )
			die( 0 );

		// Get post
		$post_id = absint( $_POST['tour_id'] );
		$code    = sanitize_text_field( $_POST['code'] );

		$tour    = new MyWorldClass_Tour( $post_id );

		// This should never happen unless someone is abusing
		if ( $tour->post === false )
			die;

		// Setup the signup
		$tour->setup_signup( $_POST['signup_id'] );

		// Not a valid scholarship code
		if ( ! $tour->is_valid_scholarship_code( $code ) )
			wp_send_json_error();

		$value    = $tour->get_scholarship_value( $code );
		$cost     = $tour->get_cost();
		$discount = number_format( ( count( $tour->signup->travelers ) * $value ), 2, '.', '' );
		$final    = $cost - $discount;

		// Report the good news
		wp_send_json_success( array(
			'message'  => sprintf( __( '%s has been deducted from your total cost.', 'myworldclass' ), $discount ),
			'discount' => number_format( $value, 2, '.', '' ),
			'cost'     => $cost,
			'final'    => number_format( $final, 2, '.', '' )
		) );

	}
endif;

/**
 * AJAX: Update Front End Profile
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_ajax_update_front_end_profile' ) ) :
	function mywclass_ajax_update_front_end_profile() {

		// Security
		check_ajax_referer( 'update-my-front-end-profile', 'key' );

		// Parse the form variable which contains the submitted forms data
		parse_str( $_POST['form'], $post );

		$user_id = absint( $post['user_id'] );

		// Admins should be able to edit other users profiles.
		if ( $user_id != get_current_user_id() && ! current_user_can( 'edit_users' ) )
			$user_id = get_current_user_id();

		$user = mywclass_get_userdata( $user_id );

		$object = $meta = array();

		// First name
		$first_name = sanitize_text_field( $post['first_name'] );
		if ( $first_name != '' )
			$object['first_name'] = $first_name;
		else
			wp_send_json_error( __( 'First Name can not be empty.', 'myworldclass' ) );

		// Last name
		$last_name = sanitize_text_field( $post['last_name'] );
		if ( $last_name != '' )
			$object['last_name'] = $last_name;
		else
			wp_send_json_error( __( 'Last Name can not be empty.', 'myworldclass' ) );

		// If we want to change email
		$user_email = sanitize_text_field( $post['user_email'] );
		if ( $user_email != $user->user_email ) {

			// Can not be empty
			if ( $user_email == '' )
				wp_send_json_error( __( 'Email can not be empty.', 'myworldclass' ) );

			// Invalid format
			if ( ! is_email( $user_email ) )
				wp_send_json_error( __( 'Invalid email. Please check and try again.', 'myworldclass' ) );

			// Make sure the email does not belong to someone else already
			$check = email_exists( $user_email );
			if ( $check !== false && $check != $user_id )
				wp_send_json_error( __( 'This email can not be used as it already belongs to another user.', 'myworldclass' ) );

			// Add to object
			$object['user_email'] = $user_email;

		}

		// Phone number
		$user_phone = sanitize_text_field( $post['user_phone'] );
		if ( $user_phone != '' )
			$meta['user_phone'] = $user_phone;

		// Date of birth
		$user_dob = sanitize_text_field( $post['user_dob'] );
		if ( $user_dob != '' )
			$meta['user_dob'] = $user_dob;

		// If we want to change password
		$changed_password = false;
		if ( $post['new_pwd'] != '' && $post['new_pwd_confirm'] != '' ) {

			$new_pwd         = sanitize_text_field( $post['new_pwd'] );
			$new_pwd_confirm = sanitize_text_field( $post['new_pwd_confirm'] );

			// Too short
			if ( strlen( $new_pwd ) < 6 || strlen( $new_pwd_confirm ) < 6 )
				wp_send_json_error( __( 'The new password is too short. It must be at least 6 characters long.', 'myworldclass' ) );

			// Mismatch
			if ( $new_pwd != $new_pwd_confirm )
				wp_send_json_error( __( 'Password confirmation mismatch. Please try again.', 'myworldclass' ) );

			global $wp_hasher;

			if ( empty( $wp_hasher ) ) {

				require_once ABSPATH . 'wp-includes/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );

			}

			// Same as current password
			if ( $wp_hasher->CheckPassword( $new_pwd, $user->user_pass ) )
				wp_send_json_error( __( 'You can not use this password.', 'myworldclass' ) );

			// Too easy
			if ( in_array( $new_pwd, array( 'password', 'pa$$word', '123456', '987654', '012345', '000000', '111111', '222222', '333333', '444444', '555555', '666666', '777777', '888888', '999999', $user->first_name, $user->last_name ) ) )
				wp_send_json_error( __( 'Your password is too simple.', 'myworldclass' ) );

			// Fine, add it to the object
			$object['user_pass'] = $new_pwd;
			$changed_password    = true;

		}

		// Update user object first as this can result in errors
		if ( ! empty( $object ) ) {

			$object['ID'] = $user_id;
			$update = wp_update_user( $object );

			// Yep, something went wrong.
			if ( is_wp_error( $update ) )
				wp_send_json_error( $update->get_error_message() );

			// If we changed password, fire of an email just in case the owner did not do this.
			if ( $changed_password ) {

				$prefs = mywclass_get_settings();

				if ( $prefs['emails']['password']['body'] != '' ) {

					$subject = $prefs['emails']['password']['subject'];
					$content = $prefs['emails']['password']['body'];

					$content = str_replace( '%first_name%',    $user->first_name, $content );
					$content = str_replace( '%last_name%',     $user->last_name, $content );
					$content = str_replace( '%user_email%',    $user->user_email, $content );
					$content = str_replace( '%newpassword%',   $new_pwd, $content );

					if ( $content != '' )
						wp_mail( $user->user_email, $subject, $content );

				}

			}

		}

		// Finally we save all the user meta
		if ( ! empty( $meta ) ) {

			foreach ( $meta as $meta_key => $meta_value )
				update_user_meta( $user_id, $meta_key, $meta_value );

		}

		// Report the good news
		wp_send_json_success( __( 'Your account has been updated.', 'myworldclass' ) );

	}
endif;

/**
 * AJAX: Make Manual Payments
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_ajax_make_manual_payment' ) ) :
	function mywclass_ajax_make_manual_payment() {

		// Security
		check_ajax_referer( 'make-manual-payment', 'key' );

		$user_id = get_current_user_id();
		$tour_id = absint( $_POST['tour_id'] );

		parse_str( $_POST['form'], $post );

		$tour = new MyWorldClass_Tour( $tour_id );

		if ( $tour->post === false )
			die;

		$tour->setup_signup();

		$amount = sanitize_text_field( $post['amount'] );
		$amount = number_format( $amount, 2, '.', '' );

		$max    = mywclass_get_amount_owed( $user_id, $tour_id );

		// If we are attempting to pay less then the minimum set
		if ( $amount < $tour->minimum )
			wp_send_json_error( array( 'message' => sprintf( __( 'Minimum payments are $ %s.', '' ), $tour->minimum ) ) );

		// If we are attempting to pay to much, enforce the amount that is due instead
		elseif ( $amount > $max )
			$amount = $max;

		$charge = array(
			'plan'   => 'manual',
			'card'   => sanitize_text_field( $post['card'] ),
			'name'   => sanitize_text_field( $post['name'] ),
			'exp_mm' => sanitize_text_field( $post['exp_mm'] ),
			'exp_yy' => sanitize_text_field( $post['exp_yy'] ),
			'cvv'    => sanitize_text_field( $post['cvv'] ),
			'manual' => $amount
		);

		if ( $tour->charge_card( $charge, false ) ) {

			mywclass_update_users_balance( $user_id, $tour->payment_amount );

			$payment = new MyWorldClass_Payments( $user_id );
			$payment->add_payment( $tour->payment_id, 'Completed', array(
				'note'    => 'Manual Payment',
				'amount'  => $tour->payment_amount,
				'tour_id' => $tour->post_id,
				'type'    => 'card'
			) );

			wp_send_json_success( array( 'message' => sprintf( __( '$ %s has been successfully credited to your account.', '' ), $tour->payment_amount ), 'amount' => $tour->payment_amount ) );

		}

		wp_send_json_error( array( 'message' => $tour->payment_errors ) );

	}
endif;

/**
 * AJAX: Edit Signup Details
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_ajax_edit_signup_details' ) ) :
	function mywclass_ajax_edit_signup_details() {

		// Security
		check_ajax_referer( 'edit-signup-details', 'key' );

		$signup_id = absint( $_POST['signupid'] );

		parse_str( $_POST['form'], $post );

		$mode      = $post['mode'];
		$submitted = $post['edit_signup'];
		$balance   = number_format( $post['balance'], 2, '.', '' );

		global $wpdb;

		$table  = $wpdb->prefix . 'tour_signups';
		$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d;", $signup_id ) );

		if ( ! isset( $signup->id ) )
			wp_send_json_error( 'Unable to find the requested signup. Please reload the page to make sure someone has not deleted it.' );

		$tour = new MyWorldClass_Tour( $signup->tour_id );

		$signup->travelers = maybe_unserialize( $signup->travelers );
		$signup->parents   = maybe_unserialize( $signup->travelers );
		$signup->billing   = maybe_unserialize( $signup->travelers );

		$updated   = false;
		$errors    = array();
		$new       = array();

		if ( $mode == 'admin' ) {

			update_user_meta( $signup->user_id, 'mywclass_balance', $balance );

			// We start with the travelers
			$travelers_default = $tour->get_default_setup_data();

			$travelers = array();
			foreach ( $submitted['travelers'] as $row => $data ) {

				if ( $row > 0 && sanitize_text_field( $data['first_name'] ) == '' && sanitize_text_field( $data['last_name'] ) == '' )
					continue;

				$clean_data = array();
				foreach ( $data as $key => $value )
					$clean_data[ $key ] = sanitize_text_field( $value );

				$default = $travelers_default;
				if ( isset( $signup->travelers[ $row ] ) )
					$default = $signup->travelers[ $row ];

				$travelers[] = wp_parse_args( $clean_data, $default );

			}

			$new['travelers'] = $travelers;

			// Next we do parents
			$parents_default = $tour->get_default_setup_data( 'parents' );

			$parents = array();
			foreach ( $submitted['parents'] as $row => $data ) {

				if ( $row > 0 && sanitize_text_field( $data['first_name'] ) == '' && sanitize_text_field( $data['last_name'] ) == '' )
					continue;

				$clean_data = array();
				foreach ( $data as $key => $value )
					$clean_data[ $key ] = sanitize_text_field( $value );

				$default = $parents_default;
				if ( isset( $signup->parents[ $row ] ) )
					$default = $signup->parents[ $row ];

				$parents[] = wp_parse_args( $clean_data, $default );

			}

			$new['parents'] = $parents;

			// Next we do billing
			$billing_default = $tour->get_default_setup_data( 'billing' );

			$billing = array();
			$clean_data = array();
			foreach ( $submitted['billing'] as $key => $value )
				$clean_data[ $key ] = sanitize_text_field( $value );

			$default = $billing_default;
			if ( is_array( $signup->billing ) )
				$default = $signup->billing;

			$new['billing'] = wp_parse_args( $clean_data, $default );

			// Plan
			$plan              = $signup->plan;
			$submitted['plan'] = sanitize_text_field( $submitted['plan'] );
			if ( $submitted['plan'] != '' && $signup->plan != $submitted['plan'] ) {

				if ( $submitted['plan'] == 'new' ) {
				
				}
				elseif ( $submitted['plan'] == 'pending' ) {
				
				}
				elseif ( $submitted['plan'] == 'paid' ) {
				
				}

				$plan = $submitted['plan'];

			}

			// Status
			$status              = $signup->status;
			$submitted['status'] = sanitize_text_field( $submitted['status'] );
			if ( $submitted['status'] != '' && $signup->plan != $submitted['status'] ) {

				if ( $submitted['status'] == 'auto' ) {
				
				}
				elseif ( $submitted['status'] == 'manual' ) {
				
				}
				elseif ( $submitted['status'] == 'full' ) {
				
				}

				$status = $submitted['status'];

			}

			$wpdb->update(
				$table,
				array(
					'travelers' => serialize( $new['travelers'] ),
					'parents'   => serialize( $new['parents'] ),
					'billing'   => serialize( $new['billing'] ),
					'plan'      => $plan,
					'status'    => $status,
					'time'      => current_time( 'timestamp' )
				),
				array( 'id' => $signup->id ),
				array( '%s', '%s', '%s' ),
				array( '%d' )
			);

			$updated = true;

		}
		
		else {

			// Make sure we are editing our own signup and not someone elses.
			$user_id = get_current_user_id();
			if ( $signup->user_id != $user_id )
				wp_send_json_error( 'Signup ID mismatch.' );

			// Allow adjustments to travelers
			$travelers_default = $tour->get_default_setup_data();

			$travelers = array();
			foreach ( $submitted['travelers'] as $row => $data ) {

				if ( sanitize_text_field( $data['first_name'] ) == '' && sanitize_text_field( $data['last_name'] ) == '' )
					continue;

				$clean_data = array();
				foreach ( $data as $key => $value )
					$clean_data[ $key ] = sanitize_text_field( $value );

				$default = $travelers_default;
				if ( isset( $signup->travelers[ $row ] ) )
					$default = $signup->travelers[ $row ];

				$travelers[] = wp_parse_args( $clean_data, $default );

			}

			$new['travelers'] = $travelers;

			$wpdb->update(
				$table,
				array(
					'travelers' => serialize( $new['travelers'] )
				),
				array( 'id' => $signup->id ),
				array( '%s' ),
				array( '%d' )
			);

			$updated = true;

		}

		if ( $updated )
			wp_send_json_success( 'Signup successfully updated.' );

		wp_send_json_error( 'Unable to update the signup. Please try reloading the page.' );

	}
endif;

?>