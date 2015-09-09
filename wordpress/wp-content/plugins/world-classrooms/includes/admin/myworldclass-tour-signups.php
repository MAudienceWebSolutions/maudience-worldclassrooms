<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Add Signup Page
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_add_signup_admin_page' ) ) :
	function mywclass_add_signup_admin_page() {

		$pages = array();

		$pages[] = add_submenu_page(
			'edit.php?post_type=tour',
			'Tour Signups',
			'Tour Signups',
			'edit_users',
			'mywclass-signups',
			'mywclass_tour_signups_screen'
		);

		foreach ( $pages as $page ) {
			add_action( 'admin_print_styles-' . $page, 'mywclass_admin_screen_styles' );
			add_action( 'load-' . $page,               'mywclass_admin_load' );
		}

	}
endif;

/**
 *
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_admin_screen_styles' ) ) :
	function mywclass_admin_screen_styles() {

		if ( $_GET['page'] == 'mywclass-signups' ) {

?>
<style type="text/css">
th#signup-id { width: 80px; }
th#user { width: auto; }
th#tour { width: 25%; }
th#date { width: 15%; }
th#status { width: 10%; }
th#payments { width: 10%; }
th#travelers { width: 80px; }

.form-group-section { display: block; float: none; clear: both; min-height: 50px; }
.form-group-section .form-group { display: block; float: left; width: 49%; margin-right: 2%; min-height: 50px; margin-bottom: 12px; }
.form-group-section .form-group.last, 
.form-group-section .form-group.fourth.last { margin-right: 0; }
.form-group-section .form-group.fourth { width: 23.5%; }
.form-group-section .form-group label { font-weight: bold; }
.form-group-section .form-group .input-group { width: 100%; }
.form-group-section .form-group .input-group input, 
.form-group-section .form-group .input-group select { width: 100%; }

#dashboard-widgets-wrap .action-row {
	min-height: 50px;
	margin-top: 12px;
	padding: 0 8px;
	text-align: right;
	float: none;
	clear: both;
}
#dashboard-widgets-wrap .action-row input {
	float:right;
	margin-left: 24px;
}
#dashboard-widgets-wrap .action-row select {
	margin-left: 24px;
}
table thead tr th { text-align: left; }
</style>
<?php

		}

	}
endif;

/**
 *
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_admin_load' ) ) :
	function mywclass_admin_load() {

		if ( $_GET['page'] == 'mywclass-signups' ) {

			// Delete Signup
			if ( isset( $_GET['action'] ) && isset( $_GET['signup_id'] ) && strlen( $_GET['signup_id'] ) > 0 && $_GET['action'] == 'delete' ) {

				$entry_id = absint( $_GET['signup_id'] );

				global $wpdb;

				$table   = $wpdb->prefix . 'tour_signups';
				$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$table} WHERE id = %d;", $entry_id ) );
				if ( $user_id !== NULL )
					delete_user_meta( $user_id, 'tour_code' );

				$wpdb->delete(
					$table,
					array( 'id' => $entry_id ),
					array( '%d' )
				);

				$url = remove_query_arg( array( 'action', 'signup_id' ) );
				$url = add_query_arg( array( 'deleted' => 1 ), $url );
				wp_safe_redirect( $url );
				exit;

			}

			// Resend email
			if ( isset( $_GET['action'] ) && isset( $_GET['signup_id'] ) && strlen( $_GET['signup_id'] ) > 0 && $_GET['action'] == 'resend' ) {

				global $wpdb;

				$table   = $wpdb->prefix . 'tour_signups';
				$signup  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d;", $_GET['signup_id'] ) );
				if ( isset( $signup->id ) ) {

					$tour = new MyWorldClass_Tour( $signup->tour_id );
					$tour->setup_signup( $signup->id );

					$user = mywclass_get_userdata( $signup->user_id );

					$new_password = wp_generate_password( 12 );
					$update = wp_update_user( array(
						'ID'        => $signup->user_id,
						'user_pass' => $new_password
					) );

					$tour->send_email( 'enrolment', array(
						'first_name' => $user->first_name,
						'last_name'  => $user->last_name,
						'user_email' => $user->user_email,
						'user_pass'  => $new_password
					) );

					$url = remove_query_arg( array( 'action', 'signup_id' ) );
					$url = add_query_arg( array( 'resent' => 1 ), $url );
					wp_safe_redirect( $url );
					exit;

				}

			}

			// Bulk Action - Delete Signups
			if ( isset( $_GET['action'] ) && $_GET['action'] != '-1' && isset( $_GET['signups'] ) && ! empty( $_GET['signups'] ) ) {

				global $wpdb;

				$act     = sanitize_key( $_GET['action'] );
				$signups = array();
				$done    = 0;
				$table   = $wpdb->prefix . 'tour_signups';

				foreach ( $_GET['signups'] as $signup_id ) {
					if ( $signup_id == '' || $signup_id == 0 ) continue;
					$signups[] = absint( $signup_id );
				}

				if ( ! empty( $signups ) ) {

					if ( $act == 'delete' ) {

						foreach ( $signups as $sid ) {

							$wpdb->delete(
								$table,
								array( 'id' => $sid ),
								array( '%d' )
							);

							$done++;

						}

						$url = remove_query_arg( array( 'action', 'signups' ) );
						$url = add_query_arg( array( 'deleted' => 1, 'multi' => $done ), $url );
						wp_safe_redirect( $url );
						exit;

					}

				}

			}

			// Update signups to show per page
			if ( isset( $_REQUEST['wp_screen_options']['option'] ) && isset( $_REQUEST['wp_screen_options']['value'] ) ) {
			
				if ( $_REQUEST['wp_screen_options']['option'] == 'mywclass_signups_per_page' ) {
					$value = absint( $_REQUEST['wp_screen_options']['value'] );
					update_user_meta( get_current_user_id(), 'mywclass_signups_per_page', $value );
				}

			}

			if ( ! isset( $_GET['action'] ) ) {

				$args = array(
					'label'   => __( 'Signups', 'myworldclass' ),
					'default' => 10,
					'option'  => 'mywclass_signups_per_page'
				);
				add_screen_option( 'per_page', $args );

			}

		}

		if ( isset( $_GET['signup_id'] ) && $_GET['signup_id'] != '' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' )
			myworldclass_help_signup_editor();
		else
			myworldclass_help_signup_list();

	}
endif;

/**
 * Signup Page Screen
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_signups_screen' ) ) :
	function mywclass_tour_signups_screen() {

		// Security
		if ( ! current_user_can( 'edit_users' ) )
			wp_die( __( 'Access Denied', 'myworldclass' ) );

		// View Signup Mode
		if ( isset( $_GET['signup_id'] ) && $_GET['signup_id'] != '' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) :

			myworldclass_help_signup_editor();

			global $wpdb;

			$table  = $wpdb->prefix . 'tour_signups';
			$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d;", $_GET['signup_id'] ) );

?>
<div class="wrap">
<?php

			// Valid signups
			if ( isset( $signup->id ) && $signup->id == $_GET['signup_id'] ) {

				$user      = get_userdata( $signup->user_id );
				$travelers = maybe_unserialize( $signup->travelers );
				$parents   = maybe_unserialize( $signup->parents );
				$billing   = maybe_unserialize( $signup->billing );

				$date_format = get_option( 'date_format' );
				$tour = new MyWorldClass_Tour( $signup->tour_id );

?>
	<h1>Edit Signup #<?php echo $signup->id; ?> for <strong><?php echo $tour->post->post_title; ?></strong></h1>
	<div class="updated notice notice-success is-dismissible below-h2" id="editor-message" style="display:none;"><p>Updating Signup</p></div>
	<form id="dashboard-widgets-wrap" method="post" action="">
		<div class="action-row">
			<input type="submit" class="button button-primary" value="Update Signup" />
			<input type="button" class="button button-secondary" data-id="<?php echo $signup->id; ?>" value="Delete" />
			<select name="edit_signup[status]" id="signup-status">
<?php

				$status_options = array(
					'new'     => 'New Signup',
					'pending' => 'Pending Payment',
					'paid'    => 'Paid in Full'
				);
				foreach ( $status_options as $status => $label ) {
					echo '<option value="' . $status . '"';
					if ( $signup->status == $status ) {
						echo ' selected="selected"';
						echo '>Current Status: ' . $label . '</option>';
						
					}
					else {
						echo '>' . $label . '</option>';
					}
				}

?>
			</select>
			<select name="edit_signup[plan]" id="signup-status">
<?php

				$payment_plans = $tour->get_plan_labels();
				foreach ( $payment_plans as $plan => $label ) {
					echo '<option value="' . $plan . '"';
					if ( $signup->plan == $plan ) {
						echo ' selected="selected"';
						echo '>Current Payment Plan: ' . $label . '</option>';
					}
					else {
						echo '>' . $label . '</option>';
					}
				}

?>
			</select>
		</div>
		<input type="hidden" name="mode" value="admin" />
		<div id="dashboard-widgets" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables">

					<div id="tour-signup-details" class="postbox " >
						<h3 class='hndle'><span>Travelers</span></h3>
						<div class="inside">
<?php

				$genders = array(
					'male'   => 'Male',
					'female' => 'Female'
				);
				$traveler_types = array(
					'student' => 'Student',
					'parent'  => 'Parent'
				);
				$yes_or_no = array(
					0 => 'No',
					1 => 'Yes'
				);

				$tour = new MyWorldClass_Tour( $signup->tour_id );

				$default = $tour->get_default_setup_data();
				if ( ! empty( $travelers ) ) {
					foreach ( $travelers as $number => $traveler ) {

						$travelers = wp_parse_args( $traveler, $default );

?>
							<h2>Traveler #<?php echo $number + 1; ?></h2>
							<div class="form-group-section" id="traveler-<?php echo $number; ?>">
								<div class="form-wrapper">
									<div class="form-group">
										<label for="signup-traveler-<?php echo $number; ?>-first_name">First Name</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][first_name]" id="signup-traveler-<?php echo $number; ?>-first_name" value="<?php echo esc_attr( $traveler['first_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-traveler-<?php echo $number; ?>-middle_name">Middle Name</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][middle_name]" id="signup-traveler-<?php echo $number; ?>-middle_name" value="<?php echo esc_attr( $traveler['middle_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group">
										<label for="signup-traveler-<?php echo $number; ?>-last_name">Last Name</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][last_name]" id="signup-traveler-<?php echo $number; ?>-last_name" value="<?php echo esc_attr( $traveler['last_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-traveler-<?php echo $number; ?>-gender">Gender</label>
										<div class="input-group">
											<select name="edit_signup[travelers][<?php echo $number; ?>][gender]" id="signup-traveler-<?php echo $number; ?>-gender">
<?php

					foreach ( $genders as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $traveler['gender'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

					$dobcheck = explode( '/', $traveler['DOB'] );
					if ( count( $dobcheck ) != 3 )
						$traveler['DOB'] = date( 'd/m/Y', $traveler['DOB'] );

?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="signup-traveler-<?php echo $number; ?>-DOB">Date of Birth</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][DOB]" id="signup-traveler-<?php echo $number; ?>-DOB" value="<?php echo $traveler['DOB']; ?>" placeholder="mm/dd/yyyy" />
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-traveler-<?php echo $number; ?>-type">Type</label>
										<div class="input-group">
											<select name="edit_signup[travelers][<?php echo $number; ?>][type]" id="signup-traveler-<?php echo $number; ?>-type">
<?php

					foreach ( $traveler_types as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $traveler['type'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="signup-traveler-<?php echo $number; ?>-user_email">Email Address</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][user_email]" id="signup-traveler-<?php echo $number; ?>-user_email" value="<?php echo esc_attr( $traveler['user_email'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth">
										<label for="signup-traveler-<?php echo $number; ?>-phone">Phone</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][phone]" id="signup-traveler-<?php echo $number; ?>-phone" value="<?php echo esc_attr( $traveler['phone'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth last">
										<label for="signup-traveler-<?php echo $number; ?>-phone_type">Type</label>
										<div class="input-group">
											<select name="edit_signup[travelers][<?php echo $number; ?>][phone_type]" id="signup-traveler-<?php echo $number; ?>-phone_type">
<?php

					foreach ( $tour->get_phone_types() as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $traveler['phone_type'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="signup-traveler-<?php echo $number; ?>-address1">Address Line 1</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][address1]" id="signup-traveler-<?php echo $number; ?>-address1" value="<?php echo esc_attr( $traveler['address1'] ); ?>" />
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-traveler-<?php echo $number; ?>-address2">Address Line 2</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][address2]" id="signup-traveler-<?php echo $number; ?>-address2" value="<?php echo esc_attr( $traveler['address2'] ); ?>" />
										</div>
									</div>
									<div class="form-group">
										<label for="signup-traveler-<?php echo $number; ?>-city">City</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][city]" id="signup-traveler-<?php echo $number; ?>-city" value="<?php echo esc_attr( $traveler['city'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth">
										<label for="signup-traveler-<?php echo $number; ?>-state">State</label>
										<div class="input-group">
											<select name="edit_signup[travelers][<?php echo $number; ?>][state]" id="signup-traveler-<?php echo $number; ?>-state">
<?php

					foreach ( $tour->get_states() as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $traveler['state'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

?>
											</select>
										</div>
									</div>
									<div class="form-group fourth last">
										<label for="signup-traveler-<?php echo $number; ?>-zip">Zip</label>
										<div class="input-group">
											<input type="text" name="edit_signup[travelers][<?php echo $number; ?>][zip]" id="signup-traveler-<?php echo $number; ?>-zip" value="<?php echo esc_attr( $traveler['zip'] ); ?>" />
										</div>
									</div>
								</div>
								<div class="clear clearfix"></div>
							</div>
<?php

					}
				}
				else {
					echo '<p>No travelers found.</p>';
				}

?>
						</div>
					</div>

				</div>
			</div>
			<div id="postbox-container-2" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables">

					<div id="tour-signup-travelers" class="postbox " >
						<h3 class='hndle'><span>Parent/Guardian Details</span></h3>
						<div class="inside">
<?php

				$default = $tour->get_default_setup_data( 'parents' );
				if ( ! empty( $parents ) ) {
					foreach ( $parents as $number => $parent ) {

						$parent = wp_parse_args( $parent, $default );

?>
							<div class="form-group-section" id="parent-<?php echo $number; ?>">
								<div class="form-wrapper">
									<div class="form-group">
										<label for="signup-parent-<?php echo $number; ?>-first_name">First Name</label>
										<div class="input-group">
											<input type="text" name="edit_signup[parents][<?php echo $number; ?>][first_name]" id="signup-parent-<?php echo $number; ?>-first_name" value="<?php echo esc_attr( $parent['first_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-parent-<?php echo $number; ?>-last_name">Last Name</label>
										<div class="input-group">
											<input type="text" name="edit_signup[parents][<?php echo $number; ?>][last_name]" id="signup-parent-<?php echo $number; ?>-last_name" value="<?php echo esc_attr( $parent['last_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group">
										<label for="signup-parent-<?php echo $number; ?>-relationship">Relationship</label>
										<div class="input-group">
											<select name="edit_signup[parents][<?php echo $number; ?>][relationship]" id="signup-parent-<?php echo $number; ?>-relationship">
<?php

					foreach ( $tour->get_relationships() as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $parent['relationship'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

?>
											</select>
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-parent-<?php echo $number; ?>-user_email">Email Address</label>
										<div class="input-group">
											<input type="text" name="edit_signup[parents][<?php echo $number; ?>][user_email]" id="signup-parent-<?php echo $number; ?>-user_email" value="<?php echo esc_attr( $parent['user_email'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth">
										<label for="signup-parent-<?php echo $number; ?>-phone">Phone</label>
										<div class="input-group">
											<input type="text" name="edit_signup[parents][<?php echo $number; ?>][phone]" id="signup-parent-<?php echo $number; ?>-phone" value="<?php echo esc_attr( $parent['phone'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth">
										<label for="signup-parent-<?php echo $number; ?>-phone_type">Type</label>
										<div class="input-group">
											<select name="edit_signup[parents][<?php echo $number; ?>][phone_type]" id="signup-parent-<?php echo $number; ?>-phone_type">
<?php

					foreach ( $tour->get_phone_types() as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $parent['phone_type'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

?>
											</select>
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-parent-<?php echo $number; ?>-same_as_travel">Same Address as Traveler?</label>
										<div class="input-group">
											<select name="edit_signup[parents][<?php echo $number; ?>][same_as_travel]" id="signup-parent-<?php echo $number; ?>-phone_type">
<?php

					foreach ( $yes_or_no as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $parent['same_as_travel'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="signup-parent-<?php echo $number; ?>-address1">Address Line 1</label>
										<div class="input-group">
											<input type="text" name="edit_signup[parents][<?php echo $number; ?>][address1]" id="signup-parent-<?php echo $number; ?>-address1" value="<?php echo esc_attr( $parent['address1'] ); ?>" />
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-parent-<?php echo $number; ?>-address2">Address Line 2</label>
										<div class="input-group">
											<input type="text" name="edit_signup[parents][<?php echo $number; ?>][address2]" id="signup-parent-<?php echo $number; ?>-address2" value="<?php echo esc_attr( $parent['address2'] ); ?>" />
										</div>
									</div>
									<div class="form-group">
										<label for="signup-parent-<?php echo $number; ?>-city">City</label>
										<div class="input-group">
											<input type="text" name="edit_signup[parents][<?php echo $number; ?>][city]" id="signup-parent-<?php echo $number; ?>-city" value="<?php echo esc_attr( $parent['city'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth">
										<label for="signup-parent-<?php echo $number; ?>-state">State</label>
										<div class="input-group">
											<select name="edit_signup[parents][<?php echo $number; ?>][state]" id="signup-parent-<?php echo $number; ?>-state">
<?php

					foreach ( $tour->get_states() as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $parent['state'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

?>
											</select>
										</div>
									</div>
									<div class="form-group fourth last">
										<label for="signup-parent-<?php echo $number; ?>-zip">Zip</label>
										<div class="input-group">
											<input type="text" name="edit_signup[parents][<?php echo $number; ?>][zip]" id="signup-parent-<?php echo $number; ?>-zip" value="<?php echo esc_attr( $parent['zip'] ); ?>" />
										</div>
									</div>
								</div>
								<div class="clear clearfix"></div>
							</div>
<?php

					}
				}
				else {
					
				}

				$default = $tour->get_default_setup_data( 'billing' );
				$billing = wp_parse_args( $billing, $default );

?>
						</div>
					</div>

					<div id="tour-signup-details" class="postbox " >
						<h3 class='hndle'><span>Billing Details</span></h3>
						<div class="inside">
							<div class="form-group-section" id="billing-details">
								<div class="form-wrapper">
									<div class="form-group">
										<label for="signup-billing-first_name">First Name</label>
										<div class="input-group">
											<input type="text" name="edit_signup[billing][first_name]" id="signup-billing-first_name" value="<?php echo esc_attr( $billing['first_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-billing-last_name">Last Name</label>
										<div class="input-group">
											<input type="text" name="edit_signup[billing][last_name]" id="signup-billing-last_name" value="<?php echo esc_attr( $billing['last_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group">
										<label for="signup-billing-address1">Address Line 1</label>
										<div class="input-group">
											<input type="text" name="edit_signup[billing][address1]" id="signup-billing-address1" value="<?php echo esc_attr( $billing['address1'] ); ?>" />
										</div>
									</div>
									<div class="form-group last">
										<label for="signup-billing-address2">Address Line 2</label>
										<div class="input-group">
											<input type="text" name="edit_signup[billing][address2]" id="signup-billing-address2" value="<?php echo esc_attr( $billing['address2'] ); ?>" />
										</div>
									</div>
									<div class="form-group">
										<label for="signup-billing-city">City</label>
										<div class="input-group">
											<input type="text" name="edit_signup[billing][city]" id="signup-billing-city" value="<?php echo esc_attr( $billing['city'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth">
										<label for="signup-billing-state">State</label>
										<div class="input-group">
											<select name="edit_signup[billing][state]" id="signup-billing-state">
<?php

					foreach ( $tour->get_states() as $value => $label ) {
						echo '<option value="' . $value . '"';
						if ( $billing['state'] == $value ) echo ' selected="selected"';
						echo '>' . $label . '</option>';
					}

?>
											</select>
										</div>
									</div>
									<div class="form-group fourth last">
										<label for="signup-billing-zip">Zip</label>
										<div class="input-group">
											<input type="text" name="edit_signup[billing][zip]" id="signup-billing-zip" value="<?php echo esc_attr( $billing['zip'] ); ?>" />
										</div>
									</div>
									<div class="form-group">
										<label for="signup-billing-scholarship_code">Scholarship Code</label>
										<div class="input-group">
											<input type="text" name="edit_signup[billing][scholarship_code]" id="signup-billing-scholarship_code" value="<?php echo esc_attr( $billing['scholarship_code'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth">
										<label for="signup-billing-last_name">Code Value / Traveler</label>
										<div class="input-group">
											<input type="text" readonly="readonly" class="readonly" id="signup-billing-last_name" value="<?php echo $tour->get_scholarship_value( $billing['scholarship_code'] ); ?>" />
										</div>
									</div>
									<div class="form-group fourth last">
										<label for="signup-users-balance">Account Balance</label>
										<div class="input-group">
											<input type="text" name="balance" id="signup-users-balance" value="<?php echo number_format( mywclass_get_users_balance( $signup->user_id ), 2, '.', '' ); ?>" />
										</div>
									</div>
								</div>
								<div class="clear clearfix"></div>
							</div>
						</div>
					</div>

					<div id="tour-signup-details" class="postbox " >
						<h3 class='hndle'><span>Payments</span></h3>
						<div class="inside">
<?php

				$payments = new MyWorldClass_Payments( $signup->user_id );
				if ( ! empty( $payments->payment_history ) ) {

?>
							<table class="table" style="width: 100%;">
								<thead>
									<tr>
										<th class="history-col-count">&nbsp;</th>
										<th class="history-col-date"><?php _e( 'Date', 'myworldclass' ); ?></th>
										<th class="history-col-desc"><?php _e( 'Description', 'myworldclass' ); ?></th>
										<th class="history-col-amount"><?php _e( 'Amount', 'myworldclass' ); ?></th>
									</tr>
								</thead>
							<tbody>
<?php

					$counter = $total = 0;
					foreach ( $payments->payment_history as $time => $entry ) {

						$counter ++;
						$paid  = number_format( $entry['amount'], 2, '.', ' ' );
						$total = $total + $paid;

?>

								<tr class="border">
									<td class="history-col-count"><?php echo zeroise( $counter, 2 ); ?></td>
									<td class="history-col-date"><?php echo date_i18n( $payments->date_format, $time ); ?></td>
									<td class="history-col-desc"><?php echo $entry['note']; ?></td>
									<td class="history-col-amount">$ <?php echo $paid; ?></td>
								</tr>
<?php

					}

?>
								<tr class="footer">
									<td class="history-col-count">&nbsp;</td>
									<td class="history-col-date">&nbsp;</td>
									<td class="history-col-desc"><h2 style="padding-right: 12px; text-align: right;">Total Paid: </h2></td>
									<td class="history-col-amount"><h2>$ <?php echo number_format( $total, 2, '.', '' ); ?></h2></td>
								</tr>
							</tbody>
						</table>
<?php

				}
				else {
					echo '<p>No payment records exists for this user.</p>';
				}

?>
						</div>
					</div>

				</div>
			</div>
			<br class="clear" />
		</div>
		<div class="action-row">
			<input type="submit" class="button button-primary" value="Update Signup" />
			<input type="button" class="button button-secondary" data-id="<?php echo $signup->id; ?>" value="Delete" />
		</div>
	</form>
<script type="text/javascript">
jQuery(function($) {

	$(document).ready(function(){

		$( '#dashboard-widgets-wrap' ).on( 'submit', function(e){

			var notificationbox = $( '#editor-message' );
			var submitbutton    = $( '.button-primary' );
			var deletebutton    = $( '.button-secondary' );
			var formboxes       = $( '#dashboard-widgets' );

			notificationbox.hide().empty();

			e.preventDefault();

			$.ajax({
				url        : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type       : 'POST',
				dataType   : 'JSON',
				data       : {
					action    : 'update-signup-details',
					key       : '<?php echo wp_create_nonce( 'edit-signup-details' ); ?>',
					signupid  : <?php echo $signup->id; ?>,
					form      : $(this).serialize()
				},
				beforeSend : function() {

					formboxes.fadeOut();

					$( 'html, body' ).animate({
						scrollTop: $( 'html' ).offset().top
					});

					submitbutton.val( 'Updating ...' ).attr( 'disabled', 'disabled' );
					deletebutton.attr( 'disabled', 'disabled' );

				},
				success    : function( response ) {

					console.log( response );
					if ( response == 0 || response == '-1' ) {
						alert( '<?php _e( 'Session Timeout. Please reload this page and try again.', 'myworldclass' ); ?>' );
					}
					else {

						if ( response.success ) {
							notificationbox.removeClass( 'notice-error error' ).addClass( 'notice-success updated' ).html( '<p>' + response.data + '<\/p>' ).fadeIn();
						}
						else {
							notificationbox.removeClass( 'notice-success updated' ).addClass( 'notice-error error' ).html( '<p>' + response.data + '<\/p>' ).fadeIn();
						}

					}

					submitbutton.val( 'Update Signup' ).removeAttr( 'disabled' );
					deletebutton.removeAttr( 'disabled' );

					formboxes.fadeIn();

				}

			});

			return false;

		});

	});

});
</script>
<?php

			}

			else {

?>
	<h2>Edit Signup</h2>
	<p>Signup could not be found. Please check the signup ID you provided.</p>
<?php

			}

?>
</div>
<?php

		// List Mode
		else :

			myworldclass_help_signup_list();

			$args = array();

			$number = get_user_meta( get_current_user_id(), 'mywclass_signups_per_page', true );
			if ( $number != '' )
				$args['number'] = absint( $number );

			if ( isset( $_GET['status'] ) )
				$args['status'] = sanitize_key( $_GET['status'] );

			if ( isset( $_GET['user_id'] ) )
				$args['user_id'] = absint( $_GET['user_id'] );

			if ( isset( $_GET['paged'] ) )
				$args['paged'] = absint( $_GET['paged'] );

			$signups = new Query_Tour_Signups( $args );

?>
<div class="wrap">
	<h2>Tour Signups</h2>
<?php

		if ( isset( $_GET['deleted'] ) && $_GET['deleted'] == 1 )
			echo '<div id="message" class="error"><p>' . ( ( isset( $_GET['multi'] ) ? sprintf( _n( 'Signup was successfully deleted.', '%d Signups were successfully deleted.', $_GET['multi'], '' ), $_GET['multi'] ) : 'Signup was successfully deleted.' ) ) . '</p></div>';

		elseif ( isset( $_GET['edited'] ) && $_GET['edited'] == 1 )
			echo '<div id="message" class="updated"><p>Signup saved.</p></div>';

		elseif ( isset( $_GET['edited'] ) && $_GET['edited'] == 0 )
			echo '<div id="message" class="error"><p>Failed to save Signup.</p></div>';

		elseif ( isset( $_GET['resent'] ) && $_GET['resent'] == 1 )
			echo '<div id="message" class="updated"><p>Email resent for this signup.</p></div>';

?>
	<?php $signups->status_filter(); ?>

	<form id="signup-list" method="get" action="edit.php">
		<input type="hidden" name="post_type" value="tour" />
		<input type="hidden" name="page" value="mywclass-signups" />
		<div class="tablenav top">

			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
				<select name="action" id="bulk-action-selector-top">
					<option value="-1">Bulk Actions</option>
					<option value="delete">Delete</option>
				</select>
				<input type="submit" id="doaction" class="button action" value="Apply" />
			</div>

			<div class="tablenav-pages">

				<?php $signups->pagination(); ?>

				<br class="clear" />
			</div>

		</div>
		<table class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<td scope="col" id="cb" class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-1" /></td>
					<th scope="col" id="signup-id" class="manage-column column-id id-column">Signup ID</th>
					<th scope="col" id="user" class="manage-column column-user user-column">User</th>
					<th scope="col" id="tour" class="manage-column column-tour tour-column">Tour</th>
					<th scope="col" id="date" class="manage-column column-date date-column">Last Updated</th>
					<th scope="col" id="status" class="manage-column column-status status-column">Status</th>
					<th scope="col" id="payments" class="manage-column column-payments payments-column">Payments</th>
					<th scope="col" id="travelers" class="manage-column column-travelers travelers-column">Travelers</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all-0" /></td>
					<th scope="col" class="manage-column column-id id-column">Signup ID</th>
					<th scope="col" class="manage-column column-user user-column">User</th>
					<th scope="col" class="manage-column column-tour tour-column">Tour</th>
					<th scope="col" class="manage-column column-date date-column">Last Updated</th>
					<th scope="col" class="manage-column column-status status-column">Status</th>
					<th scope="col" class="manage-column column-payments payments-column">Payments</th>
					<th scope="col" class="manage-column column-travelers travelers-column">Travelers</th>
				</tr>
			</tfoot>
			<tbody>
<?php

		if ( $signups->have_entries() ) {

			$date_format = get_option( 'date_format' );
			$base        = add_query_arg( array( 'post_type' => 'tour', 'page' => 'mywclass-signups' ), admin_url( 'edit.php' ) );

			foreach ( $signups->results as $signup ) {

				$user      = get_userdata( $signup->user_id );
				$travelers = maybe_unserialize( $signup->travelers );
				$parents   = maybe_unserialize( $signup->parents );
				$billing   = maybe_unserialize( $signup->billing );

				$edit_user_link = esc_url( add_query_arg( array( 'action' => 'edit', 'signup_id' => $signup->id ), $base ) );

?>
				<tr id="<?php echo $signup->id; ?>">
					<th scope="row" class="check-column"><input type="checkbox" id="signup-<?php echo $signup->id; ?>" name="signups[]" value="<?php echo $signup->id; ?>" /></th>
					<td scope="col" class="manage-column column-id id-column">#<?php echo $signup->id; ?></td>
					<td class="user-column">
						<?php if ( isset( $user->display_name ) ) : ?>
						<strong><a href="<?php echo $edit_user_link; ?>"><?php echo $user->display_name; ?></a></strong>
						<?php elseif ( isset( $travelers[0]['first_name'] ) && $travelers[0]['first_name'] != '' ) : ?>
						<strong><?php echo $travelers[0]['first_name'] . ' ' . $travelers[0]['last_name']; ?></strong>
						<?php else : ?>
						<strong>Unknown</strong>
						<?php endif; ?>
						<?php echo $signups->row_actions( $signup ); ?>
					</td>
					<td class="tour-column"><?php echo get_the_title( $signup->tour_id ); ?></td>
					<td class="date-column"><?php echo human_time_diff( $signup->time ); ?> ago</td>
					<td class="status-column">
						<strong><?php echo $signups->show_status( $signup->status ); ?></strong>
						<?php if ( $signup->status == 'new' ) : ?>
						<br /><small>On step: <?php echo $signup->step; ?></small>
						<?php endif; ?>
					</td>
					<td class="payments-column">$ <?php echo number_format( $signup->payment, 2, '.', '' ); ?></td>
					<td class="travelers-column"><?php echo count( $travelers ); ?></td>
				</tr>
<?php

			}

		}

		else {

?>
				<tr>
					<td colspan="8">No signups found.</td>
				</tr>
<?php

		}

?>
			</tbody>
		</table>
		<div class="tablenav bottom">
			<div class="tablenav-pages">
				<?php $signups->pagination( 'bottom' ); ?>
			</div>
		</div>
	</form>
</div>
<?php

		endif;

	}
endif;

?>