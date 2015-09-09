<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Custom user Column Header
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_user_column_headers' ) ) :
	function mywclass_user_column_headers( $default ) {

		$columns                      = array();
		$columns['cb']                = $default['cb'];
		$columns['trip_id']           = __( 'Trip ID', 'myworldclass' );
		$columns['username']          = $default['username'];
		$columns['first_name']        = __( 'First Name', 'myworldclass' );
		$columns['last_name']         = __( 'Last Name', 'myworldclass' );
		$columns['payment_plan']      = __( 'Payment Plan', 'myworldclass' );
		$columns['trip_cost']         = __( 'Trip Cost', 'myworldclass' );
		$columns['discount_used']     = __( 'Discount Used', 'myworldclass' );
		$columns['total_paid']        = __( 'Total Paid', 'myworldclass' );
		$columns['remaining_balance'] = __( 'Remaining', 'myworldclass' );

		return $columns;

	}
endif;

/**
 * Custom Sortable user Columns
 * @since 1.0
 * @version 1.0
 */
add_filter( 'manage_users_sortable_columns', 'mywclass_sortable_user_columns' );
if ( ! function_exists( 'mywclass_sortable_user_columns' ) ) :
	function mywclass_sortable_user_columns( $columns ) {

		$columns['balance'] = __( 'Balance', 'myworldclass' );

		return $columns;

	}
endif;

/**
 * Sort Custom Sortable user Columns
 * @since 1.0
 * @version 1.0
 */
add_action( 'pre_user_query', 'mywclass_sort_sortable_columns' );
if ( ! function_exists( 'mywclass_sort_sortable_columns' ) ) :
	function mywclass_sort_sortable_columns( $query ) {

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! function_exists( 'get_current_screen' ) ) return;
		$screen = get_current_screen();
		if ( $screen === NULL || $screen->id != 'users' ) return;

		if ( isset( $query->query_vars['orderby'] ) && $query->query_vars['orderby'] == 'Balance' ) {
			global $wpdb;

			$order = 'ASC';
			if ( isset( $query->query_vars['order'] ) )
				$order = $query->query_vars['order'];

			$query->query_from .= "
				LEFT JOIN {$wpdb->usermeta} 
				ON ({$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key = 'mywclass_balance')";

			$query->query_orderby = "ORDER BY {$wpdb->usermeta}.meta_value+0 {$order} ";

		}

	}
endif;

/**
 * User Column Content
 * @since 1.0.1
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_user_column_content' ) ) :
	function mywclass_user_column_content( $value, $column_name, $user_id ) {

		if ( $column_name == 'balance' ) {

			return '$ ' . mywclass_get_amount_owed( $user_id );

		}
		elseif ( $column_name == 'trip_id' ) {

			$tour_code = get_user_meta( $user_id, 'tour_code', true );
			if ( $tour_code == '' )
				return '-';
			else
				return $tour_code;

		}
		elseif ( $column_name == 'first_name' ) {

			$user = get_userdata( $user_id );
			if ( $user->first_name != '' )
				return $user->first_name;

		}
		elseif ( $column_name == 'last_name' ) {

			$user = get_userdata( $user_id );
			if ( $user->last_name != '' )
				return $user->last_name;

		}
		elseif ( $column_name == 'payment_plan' ) {

			$tour_code = get_user_meta( $user_id, 'tour_code', true );
			if ( $tour_code == '' )
				return '-';

			$tour_id   = mywclass_get_tour_by_code( $tour_code );
			$tour      = new MyWorldClass_Tour( $tour_id );

			$tour->user_id = $user_id;
			$tour->setup_signup();

			$plan = '';
			if ( isset( $tour->signup->plan ) )
				$plan = $tour->signup->plan;

			return $tour->display_payment_plan( $plan );

		}
		elseif ( $column_name == 'trip_cost' ) {

			$tour_code = get_user_meta( $user_id, 'tour_code', true );
			if ( $tour_code == '' )
				return '-';

			$tour_id   = mywclass_get_tour_by_code( $tour_code );
			$cost      = mywclass_get_cost( $tour_id, $user_id );

			return '$ ' . number_format( $cost, 2, '.', '' );

		}
		elseif ( $column_name == 'discount_used' ) {

			$tour_code = get_user_meta( $user_id, 'tour_code', true );
			if ( $tour_code == '' )
				return '-';

			$tour_id   = mywclass_get_tour_by_code( $tour_code );
			$tour      = new MyWorldClass_Tour( $tour_id );

			$tour->user_id = $user_id;
			$tour->setup_signup();

			if ( $tour->signup->scholarship == '' )
				return 'no';

			return $tour->signup->scholarship;

		}
		elseif ( $column_name == 'total_paid' ) {

			$balance = mywclass_get_users_balance( $user_id );
			$url = '';
			if ( $balance != 0 )
				$url = '<br /><a href="' . add_query_arg( array( 'post_type' => 'tour_payment', 'author' => $user_id ), admin_url( 'edit.php' ) ) . '">View Payments</a>';

			return '$ ' . $balance . $url;

		}
		elseif ( $column_name == 'remaining_balance' ) {

			$tour_code = get_user_meta( $user_id, 'tour_code', true );
			if ( $tour_code == '' )
				return '-';

			$tour_id   = mywclass_get_tour_by_code( $tour_code );
			if ( $tour_id === false )
				return '-';

			$owed      = mywclass_get_amount_owed( $user_id, $tour_id );

			return '$ ' . number_format( $owed, 2, '.', '' );

		}

	}
endif;

/**
 * Admin Profile Edit
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_admin_user_profile_edit' ) ) :
	function mywclass_admin_user_profile_edit( $user ) {

		global $wpdb;

		$table    = $wpdb->prefix . 'tour_signups';
		$check    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d AND status != 'new';", $user->ID ) );

		if ( ! empty( $check ) ) :

?>
	<tr>
		<th scope="row">Account Balance</th>
		<td>
			<input type="text" class="regular-text code" name="balance" id="users-balance" value="<?php echo number_format( mywclass_get_users_balance( $user->ID ), 2, '.', '' ); ?>" size="20" />
		</td>
	</tr>
	<tr>
		<th scope="row">Custom Tour Cost</th>
		<td>
			$ <input type="text" placeholder="0.00" name="custom_tour_cost" id="users-tour-price" value="" size="20" /><br />
			<span class="description">If set, the user will need to pay this amount instead of the cost set in the tour.</span>
		</td>
	</tr>
<?php

			foreach ( $check as $signup ) {

				$edit_signup = add_query_arg( array( 'post_type' => 'tour', 'page' => 'mywclass-signups', 'action' => 'edit', 'signup_id' => $signup->id ), admin_url( 'edit.php' ) );

?>
	<tr>
		<th scope="row">Tour Signup</th>
		<td>
			<strong><?php echo get_the_title( $signup->tour_id ); ?></strong>
			<p><a href="<?php echo $edit_signup; ?>">View Signup</a></p>
		</td>
	</tr>
<?php

			}

		else :

			$student = new MyWorldClass_Student( $user->ID );
			if ( $student->user->tour_code == '' ) return;
			$tour_id = mywclass_get_tour_by_code( $student->user->tour_code );
			$cost    = mywclass_get_cost( $tour_id, $user->ID );

?>
</table>
<h3><?php _e( 'World Classrooms - Old Signup', 'myworldclass' ); ?></h3>
<table class="form-table">
	<tr>
		<th scope="row"><?php _e( 'Current balance', 'myworldclass' ); ?></th>
		<td><input type="text" class="regular-text code" name="balance" id="users-balance" value="<?php echo $student->balance; ?>" size="20" /></td>
	</tr>
	<tr>
		<th scope="row" for="date-of-birth"><?php _e( 'Date of Birth', 'myworldclass' ); ?></th>
		<td><input type="date" name="user_dob" id="date-of-birth" value="<?php echo $student->user->user_dob; ?>" /></td>
	</tr>
	<tr>
		<th scope="row" for="tour-code"><?php _e( 'Trip ID', 'myworldclass' ); ?></th>
		<td><input type="text" class="regular-text code" name="tour_code" id="tour-code" value="<?php echo esc_attr( $student->user->tour_code ); ?>" size="20" /></td>
	</tr>
	<tr>
		<th scope="row" for="custom-tour-cost"><?php _e( 'Tour Cost', 'myworldclass' ); ?></th>
		<td><input type="text" class="regular-text" name="custom_tour_cost" id="custom-tour-cost" value="<?php echo get_user_meta( $user->ID, 'custom_tour_cost', true ); ?>" placeholder="<?php echo number_format( $cost, 2, '.', '' ); ?>" /><br /><span class="description">Leave empty if not used. If set, this amount will be used for the total cost of the tour for this user.</span></td>
	</tr>
	<tr>
		<th scope="row" for="high-school"><?php _e( 'High School', 'myworldclass' ); ?></th>
		<td><input type="text" class="regular-text" name="high_school" id="high-school" value="<?php echo esc_attr( $student->user->high_school ); ?>" /></td>
	</tr>
<?php

		endif;

	}
endif;

/**
 * Save User Details
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_save_user_change_in_admin' ) ) :
	function mywclass_save_user_change_in_admin( $user_id ) {

		$user_phone = sanitize_text_field( $_POST['user_phone'] );
		update_user_meta( $user_id, 'user_phone', $user_phone );

		$parent_name = sanitize_text_field( $_POST['parent_name'] );
		update_user_meta( $user_id, 'parent_name', $parent_name );

		if ( ! current_user_can( 'edit_user' ) ) return;

		$balance = sanitize_text_field( $_POST['balance'] );
		update_user_meta( $user_id, 'mywclass_balance', $balance );

		$custom_tour_cost = sanitize_text_field( $_POST['custom_tour_cost'] );
		if ( $custom_tour_cost != '' )
			$custom_tour_cost = number_format( (float) $custom_tour_cost, 2, '.', '' );
		update_user_meta( $user_id, 'custom_tour_cost', $custom_tour_cost );

		if ( ! isset( $_POST['tour_code'] ) ) return;

		$user_dob = sanitize_text_field( $_POST['user_dob'] );
		update_user_meta( $user_id, 'user_dob', $user_dob );

		$old_code = get_user_meta( $user_id, 'tour_code', true );
		$tour_code = sanitize_text_field( $_POST['tour_code'] );
		update_user_meta( $user_id, 'tour_code', $tour_code );

		if ( $tour_code != '' && $tour_code != $old_code ) {
			$post_id = mywclass_get_tour_by_code( $tour_code );
			if ( $post_id !== false ) {
				$tour = new MyWorldClass_Tour( $post_id );
				$tour->get_attendees( true );
			}
		}

		$high_school = sanitize_text_field( $_POST['high_school'] );
		update_user_meta( $user_id, 'high_school', $high_school );

	}
endif;

/**
 * Filter Users in Admin Area
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_filter_users_in_admin' ) ) :
	function mywclass_filter_users_in_admin( $query ) {

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! function_exists( 'get_current_screen' ) ) return;
		$screen = get_current_screen();
		if ( $screen === NULL || $screen->id != 'users' ) return;

		global $wpdb;

		if ( isset( $_GET['tour_code'] ) && $_GET['tour_code'] != '' ) {

			$tour_code = sanitize_text_field( urldecode( $_GET['tour_code'] ) );
			$query->query_from .= "
			LEFT JOIN {$wpdb->usermeta} tour
				ON ({$wpdb->users}.ID = tour.user_id AND tour.meta_key = 'tour_code')";

			$query->query_where .= " AND tour.meta_value = '{$tour_code}'";

		}

		if ( isset( $_GET['high_school'] ) && $_GET['high_school'] != '' ) {

			$high_school = sanitize_text_field( urldecode( $_GET['high_school'] ) );
			$query->query_from .= "
			LEFT JOIN {$wpdb->usermeta} hschool
				ON ({$wpdb->users}.ID = hschool.user_id AND hschool.meta_key = 'high_school')";

			$query->query_where .= " AND hschool.meta_value = '{$tour_code}'";

		}

		if ( isset( $_GET['first_name'] ) && $_GET['first_name'] != '' ) {

			$first_name = sanitize_text_field( urldecode( $_GET['first_name'] ) );
			$query->query_from .= "
			LEFT JOIN {$wpdb->usermeta} fname
				ON ({$wpdb->users}.ID = fname.user_id AND fname.meta_key = 'first_name')";

			$query->query_where .= " AND fname.meta_value = '{$first_name}'";

		}

		if ( isset( $_GET['last_name'] ) && $_GET['last_name'] != '' ) {

			$last_name = sanitize_text_field( urldecode( $_GET['last_name'] ) );
			$query->query_from .= "
			LEFT JOIN {$wpdb->usermeta} lname
				ON ({$wpdb->users}.ID = lname.user_id AND lname.meta_key = 'last_name')";

			$query->query_where .= " AND lname.meta_value = '{$last_name}'";

		}

	}
endif;

/**
 * Filter Users Options
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_filter_users_in_admin_options' ) ) :
	function mywclass_filter_users_in_admin_options() {
?>
<input type="text" size="15" name="first_name" value="<?php if ( isset( $_GET['first_name'] ) ) echo urldecode( $_GET['first_name'] ); ?>" placeholder="<?php _e( 'First Name', 'myworldclass' ); ?>" />
<input type="text" size="15" name="last_name" value="<?php if ( isset( $_GET['last_name'] ) ) echo urldecode( $_GET['last_name'] ); ?>" placeholder="<?php _e( 'Last Name', 'myworldclass' ); ?>" />
<input type="text" size="10" name="tour_code" value="<?php if ( isset( $_GET['tour_code'] ) ) echo urldecode( $_GET['tour_code'] ); ?>" placeholder="<?php _e( 'Trip ID', 'myworldclass' ); ?>" />
<input type="text" size="20" name="high_school" value="<?php if ( isset( $_GET['high_school'] ) ) echo urldecode( $_GET['high_school'] ); ?>" placeholder="<?php _e( 'High School', 'myworldclass' ); ?>" />
<input id="filter-users-by-custom-meta" class="button" type="submit" value="Filter" />
<?php

	}
endif;
?>