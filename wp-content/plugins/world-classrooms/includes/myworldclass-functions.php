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
			'payments_page_id'   => 247,
			'authorizenet'       => array(
				'mode'     => 'test',
				'test_api' => '',
				'test_key' => '',
				'live_api' => '',
				'live_key' => ''
			),
			'common_downloads' => '',
			'emails'             => array(
				'enrolment' => array(
					'subject'   => '',
					'body'      => ''
				),
				'password'  => array(
					'subject'   => '',
					'body'      => ''
				),
				'payremind'  => array(
					'subject'   => '',
					'body'      => ''
				)
			)
		);

		$settings = get_option( 'world_classroom_prefs', $default );
		return wp_parse_args( $settings, $default );

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
		$user->user_dob    = get_user_meta( $user_id, 'user_dob', true );
		if ( is_numeric( $user->user_dob ) && strlen( $user->user_dob ) != 10 )
			$user->user_dob = date( 'd/m/Y', $this->user->user_dob );

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
	function mywclass_update_users_balance( $user_id = NULL, $amount = 0, $add = true ) {

		$balance = mywclass_get_users_balance( $user_id );

		if ( $add )
			$new_balance = $balance + $amount;
		else
			$new_balance = $balance - $amount;

		update_user_meta( $user_id, 'mywclass_balance', $new_balance );

	}
endif;

/**
 * Get Amount Owed
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_get_amount_owed' ) ) :
	function mywclass_get_amount_owed( $user_id, $tour_id = NULL ) {

		if ( $tour_id === NULL ) {
			$tour_code = get_user_meta( $user_id, 'tour_code', true );
			$tour_id   = mywclass_get_tour_by_code( $tour_code );
		}

		$cost      = mywclass_get_cost( $tour_id, $user_id );
		$balance   = mywclass_get_users_balance( $user_id );

		$remaining = $cost;
		if ( $cost > 0 )
			$remaining = $cost - $balance;

		if ( $remaining < 0 )
			$remaining = 0;

		return number_format( $remaining, 2, '.', '' );

	}
endif;

/**
 * Get Cost
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_get_cost' ) ) :
	function mywclass_get_cost( $tour_id = NULL, $student_id = NULL ) {

		// Custom tour costs trumps all
		$custom_tour_cost = get_user_meta( $student_id, 'custom_tour_cost', true );
		if ( $custom_tour_cost != '' && is_numeric( $custom_tour_cost ) )
			return number_format( (float) $custom_tour_cost, 2, '.', '' );

		global $wpdb;

		// First check if this is a user with the 1.1 version type of setup.
		$table = $wpdb->prefix . 'tour_signups';
		$check = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE tour_id = %d AND user_id = %d;", $tour_id, $student_id ) );

		if ( isset( $check->id ) ) {

			$tour = new MyWorldClass_Tour( $tour_id );
			$tour->setup_signup( $check->id );

			return $tour->get_total_due();

		}

		// Otherwise we use the "old" system.
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
			MYWORLDCLASS_VERSION . '.3',
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

if ( ! function_exists( 'mywclass_is_user_attending' ) ) :
	function mywclass_is_user_attending( $post_id ) {

		if ( ! is_user_logged_in() ) return false;

		$user_id   = get_current_user_id();
		$tour_code = get_user_meta( $user_id, 'tour_code', true );
		if ( $tour_code == '' ) {

			global $wpdb;

			$table = $wpdb->prefix . 'tour_signups';
			$check = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE tour_id = %d AND user_id = %d AND status != 'new';", $post_id, $user_id ) );

			if ( $check !== NULL )
				return true;

			return false;

		}

		$tour_id = mywclass_get_tour_by_code( $tour_code );
		if ( $tour_id == $post_id )
			return true;

		return false;

	}
endif;

if ( ! function_exists( 'mywclass_show_attendee_message' ) ) :
	function mywclass_show_attendee_message( $post_id ) {

		$tour = new MyWorldClass_Tour( $post_id );

?>
<h4>Your Information</h4>
<div class="intro-information">
	<h6>TOUR NUMBER:</h6>
	<?php echo $tour->tour_code; ?>
	<h6>REQUESTED DEPARTURE DATE:</h6>
	<?php echo $tour->start_date; ?>
	<h6>REQUESTED DEPARTURE GATE:</h6>
	LAX
	<h6>GROUP LEADER:</h6>
	<?php echo $tour->teacher_name; ?>
	<h6>REQUESTED RETURN DATE:</h6>
	<?php echo $tour->end_date; ?>
</div>
<?php

	}
endif;

/**
 * WP Head
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_wp_head' ) ) :
	function mywclass_wp_head() {

		if ( is_post_type_archive( 'tour' ) || is_singular( 'tour' ) ) {

?>
<meta name="robots" content="noindex,nofollow">
<meta name="googlebot,googlebot-news,googlebot-image,bingbot,teoma" content="noindex,nofollow">
<?php

		}

	}
endif;

/**
 * Is Payment Page
 * Checks if the current page is the payment page.
 * Returns true or false. Should not be used in instances
 * earlier than template_redirect!
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_is_payment_page' ) ) :
	function mywclass_is_payment_page() {

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

		$methods['user_phone']  = __( 'Phone Number', 'myworldclass' );
		$methods['parent_name'] = __( 'Parent or Guardians Name', 'myworldclass' );
		return $methods;

	}
endif;

/**
 * Template Redirects
 * Handles front end profile updates along with blocking
 * of visitors for the payments page.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_temlate_redirects' ) ) :
	function mywclass_temlate_redirects() {

		// Account page updates by member
		if ( is_user_logged_in() && mywclass_is_account_pages() ) {

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

	}
endif;

/**
 * Templates Include
 * Overrides the theme to load our own custom page template
 * for the payments page.
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_template_includes' ) ) :
	function mywclass_template_includes( $template ) {

		if ( is_ajax() || is_admin() ) return $template;

		if ( mywclass_is_account_pages() )
			return MYWORLDCLASS_TEMPLATES_DIR . 'page-my-account.php';

		global $wp, $post;

		// Signup requsts loads the signup page
		if ( isset( $wp->query_vars['signup'] ) ) {

			// Populate the signup variable with the tour post ID
			if ( isset( $post->ID ) )
				$wp->query_vars['signup'] = $post->ID;

			return MYWORLDCLASS_TEMPLATES_DIR . 'page-payments.php';

		}

		if ( is_singular( 'tour' ) )
			return MYWORLDCLASS_TEMPLATES_DIR . 'single-tour.php';

		return $template;

	}
endif;

/**
 * Body Classes
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_body_classes' ) ) :
	function mywclass_body_classes( $classes ) {

		global $wp;

		if ( isset( $wp->query_vars['signup'] ) )
			$classes[] = 'signing-up';

		return $classes;

	}
endif;

/**
 * Theme Menu Adjustments
 * Inserts custom links to the top navigation.
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_theme_top_menu_items' ) ) :
	function mywclass_theme_top_menu_items( $items, $args ) {

		if ( $args->theme_location == 'navigation' ) {

			$prefs = mywclass_get_settings();
			if ( is_user_logged_in() ) {

				$items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-' . $prefs['my_account_page_id'] . '"><a href="' . esc_url( get_permalink( $prefs['my_account_page_id'] ) ) . '">My Account</a></li>';
				$items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-logout"><a href="' . esc_url( wp_logout_url( home_url( '/' ) ) ) . '" style="color:red;">Logout</a></li>';

			}

			else {

				$items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-login"><a href="' . esc_url( wp_login_url( get_permalink( $prefs['my_account_page_id'] ) ) ) . '" style="color:red;">Login</a></li>';

			}

		}

		return $items;

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
 * Allow Email Login
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_allow_email_login' ) ) :
	function mywclass_allow_email_login( $user, $username, $password ) {

	    if ( is_email( $username ) ) {
	        $user = get_user_by( 'email', $username );
	        if ( $user ) $username = $user->user_login;
	    }

	    return wp_authenticate_username_password( null, $username, $password );

	}
endif;

/**
 * Adjust Login Header
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_login_header' ) ) :
	function mywclass_login_header() {
?>
<style type="text/css">
	body.login #login h1 a {
		background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png);
		padding-bottom: 30px;
		background-size: contain;
		margin-bottom: 0;
		width: 100%;
		height: 50px;
	}
</style>
<?php
	}
endif;

/**
 * Clone Post
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'myworldclass_clone_post' ) ) :
	function myworldclass_clone_post() {

		if ( ! is_admin() || ! current_user_can( 'edit_users' ) || ! isset( $_REQUEST['do'] ) || $_REQUEST['do'] != 'clone' || ! isset( $_REQUEST['target'] ) ) return;

		$target = absint( $_REQUEST['target'] );
		$post   = get_post( $target );

		if ( ! isset( $post->post_type ) || ! in_array( $post->post_type, array( 'tour', 'tours' ) ) ) return;

		$new_post = array(
			'post_title'   => $post->post_title . ' Copy',
			'post_type'    => $post->post_type,
			'post_author'  => $post->post_author,
			'post_content' => $post->post_content,
			'post_status'  => 'draft'
		);

		$clone_id = wp_insert_post( $new_post );

		global $myworldclass_cloning;

		if ( $clone_id !== NULL && ! is_wp_error( $clone_id ) ) {

			$meta = get_post_meta( $target );

			if ( ! empty( $meta ) ) {
				foreach ( $meta as $meta_key => $meta_value ) {
					if ( $meta_key == 'tour_code' ) continue;
					foreach ( $meta_value as $value )
						add_post_meta( $clone_id, $meta_key, $value, true );
				}
			}

			$myworldclass_cloning = true;

		}
		else {

			$myworldclass_cloning = $clone_id->get_error_message();

		}

	}
endif;

/**
 * Clone Admin Notices
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_clone_admin_notices' ) ) :
	function mywclass_clone_admin_notices() {

		global $myworldclass_cloning;

		if ( $myworldclass_cloning === NULL || $myworldclass_cloning === false ) return;

		if ( $myworldclass_cloning === true )
			echo '<div class="updated"><p>Tour was successfully cloned.</p></div>';
		else
			echo '<div class="error"><p>Could not clone the tour. Reason given: ' . $myworldclass_cloning . '</p></div>';

	}
endif;

/**
 * Admin Enqueue
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_admin_enqueue' ) ) :
	function mywclass_admin_enqueue( $hook ) {

		if ( 'edit.php' != $hook && 'users.php' != $hook ) {
			return;
		}

		wp_enqueue_style( 'myworldclass-admin', plugins_url( 'assets/css/admin.css', MYWORLDCLASS ) );

	}
endif;

/**
 * Catch Tour Search
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_catch_tour_search' ) ) :
	function mywclass_catch_tour_search() {

		if ( isset( $_POST['find'] ) && $_POST['find'] == 'tour' && isset( $_POST['trip_id'] ) && $_POST['trip_id'] != '' ) {

			$trip_id = sanitize_text_field( $_POST['trip_id'] );
			if ( strlen( $trip_id ) > 3 ) {

				$post_id = mywclass_get_tour_by_code( $trip_id );
				if ( $post_id !== false ) {

					$url = get_permalink( $post_id );
					wp_redirect( $url );
					exit;

				}

			}

		}

	}
endif;

?>