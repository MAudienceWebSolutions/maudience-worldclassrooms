<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Custom user Column Header
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_user_column_headers' ) ) :
	function mywclass_user_column_headers( $columns ) {

		$columns['balance'] = __( 'Balance', 'myworldclass' );
		$columns['tour_code'] = __( 'Tour Code', 'myworldclass' );
		$columns['tour_total'] = __( 'Tour Total', 'myworldclass' );
		$columns['high_school'] = __( 'High School', 'myworldclass' );
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
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_user_column_content' ) ) :
	function mywclass_user_column_content( $value, $column_name, $user_id ) {

		if ( $column_name == 'balance' ) {

			$student = new MyWorldClass_Student( $user_id );
			
			$tour_id = mywclass_get_tour_by_code( $student->user->tour_code );
			$cost = mywclass_get_cost( $tour_id, $user_id );

			if ( $cost > 0 )
				$remaining = $cost - $student->balance;

			return '$ ' . number_format( $remaining, 2, '.', ' ' );

		}
		elseif ( $column_name == 'tour_code' ) {

			$tour_code = get_user_meta( $user_id, 'tour_code', true );
			if ( $tour_code == '' )
				return '-';
			else
				return $tour_code;

		}
		elseif ( $column_name == 'tour_total' ) {

			$tour_code = get_user_meta( $user_id, 'tour_code', true );
			$cost = 0;
			$tour = new MyWorldClass_Tour( mywclass_get_tour_by_code( $tour_code ) );

			// If custom cost is set, then this is the only thing that matters
			$user_cost_override = get_user_meta( $user_id, 'custom_tour_cost', true );
			if ( strlen( $user_cost_override ) > 0 )
				return '$' . number_format( (float) $user_cost_override, 2, '.', '' );

			$students = 1;
			$extra_student = get_user_meta( $user_id, 'extra_student', true );
			if ( strtolower( $extra_student ) == 'yes' )
				$students = 2;

			$cost = $students * $tour->cost;

			$parents_attending = get_user_meta( $user_id, 'parents_attending', true );
			if ( strtolower( $parents_attending ) == 'yes' ) {
				$parents = get_user_meta( $user_id, 'no_of_parents', true );
				if ( $parents == '' || strtolower( $parents ) == 'one' )
					$parents = 1;
				else
					$parents = 2;
	
				$cost = ( $parents * $tour->cost_adult ) + $cost;
			}
			return '$ ' . $cost;

		}
		elseif ( $column_name == 'high_school' ) {

			$high_school = get_user_meta( $user_id, 'high_school', true );
			if ( $high_school == '' )
				return '-';
			else
				return $high_school;

		}

	}
endif;

/**
 * Admin Profile Edit
 * @since 1.0
 * @version 1.0.1
 */
if ( ! function_exists( 'mywclass_admin_user_profile_edit' ) ) :
	function mywclass_admin_user_profile_edit( $user ) {

		$student = new MyWorldClass_Student( $user->ID );
		$is_admin = current_user_can( 'edit_users' ); ?>

</table>
<h3><?php _e( 'World Classrooms', 'myworldclass' ); ?></h3>
<table class="form-table">
	<tr>
		<th scope="row"><?php _e( 'Current balance', 'myworldclass' ); ?></th>

		<?php if ( $is_admin ) : ?>

		<td><input type="text" class="regular-text code" name="balance" id="users-balance" value="<?php echo $student->balance; ?>" size="20" /></td>

		<?php else : ?>

		<td><h2 style="margin:0;padding:0;"><?php echo $student->display_balance(); ?></h2></td>

		<?php endif; ?>

	</tr>
	<tr>
		<th scope="row" for="date-of-birth"><?php _e( 'Date of Birth', 'myworldclass' ); ?></th>
		<td><input type="date" name="user_dob" id="date-of-birth" value="<?php echo $student->user->user_dob; ?>" /></td>
	</tr>

	<?php if ( $is_admin ) : $tour = new MyWorldClass_Tour( mywclass_get_tour_by_code( $student->user->tour_code ) ); ?>

	<tr>
		<th scope="row" for="tour-code"><?php _e( 'Tour Code', 'myworldclass' ); ?></th>
		<td><input type="text" class="regular-text code" name="tour_code" id="tour-code" value="<?php echo esc_attr( $student->user->tour_code ); ?>" size="20" /></td>
	</tr>
	<tr>
		<th scope="row" for="custom-tour-cost"><?php _e( 'Tour Cost', 'myworldclass' ); ?></th>
		<td><input type="text" class="regular-text" name="custom_tour_cost" id="custom-tour-cost" value="<?php echo get_user_meta( $user->ID, 'custom_tour_cost', true ); ?>" placeholder="<?php echo number_format( (float) $tour->cost, 2, '.', '' ); ?>" /><br /><span class="description">Leave empty if not used. If set, this amount will be used for the total cost of the tour for this user.</span></td>
	</tr>
	<tr>
		<th scope="row" for="high-school"><?php _e( 'High School', 'myworldclass' ); ?></th>
		<td><input type="text" class="regular-text" name="high_school" id="high-school" value="<?php echo esc_attr( $student->user->high_school ); ?>" /></td>
	</tr>

	<?php endif; ?>

<?php

	}
endif;

/**
 * Save User Details
 * @since 1.0
 * @version 1.0.1
 */
if ( ! function_exists( 'mywclass_save_user_change_in_admin' ) ) :
	function mywclass_save_user_change_in_admin( $user_id ) {

		$user_phone = sanitize_text_field( $_POST['user_phone'] );
		update_user_meta( $user_id, 'user_phone', $user_phone );

		$user_dob = sanitize_text_field( $_POST['user_dob'] );
		update_user_meta( $user_id, 'user_dob', $user_dob );

		$parent_name = sanitize_text_field( $_POST['parent_name'] );
		update_user_meta( $user_id, 'parent_name', $parent_name );

		if ( ! current_user_can( 'edit_user' ) ) return;

		$balance = sanitize_text_field( $_POST['balance'] );
		update_user_meta( $user_id, 'mywclass_balance', $balance );

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

		$tour_cost = sanitize_text_field( $_POST['custom_tour_cost'] );
		if ( strlen( $tour_cost ) > 0 && is_numeric( $tour_cost ) ) {
			$tour_cost = number_format( (float) $tour_cost, 2, '.', '' );
			update_user_meta( $user_id, 'custom_tour_cost', $tour_cost );
		}

		else delete_user_meta( $user_id, 'custom_tour_cost' );

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
<input type="text" size="10" name="tour_code" value="<?php if ( isset( $_GET['tour_code'] ) ) echo urldecode( $_GET['tour_code'] ); ?>" placeholder="<?php _e( 'Tour Code', 'myworldclass' ); ?>" />
<input type="text" size="20" name="high_school" value="<?php if ( isset( $_GET['high_school'] ) ) echo urldecode( $_GET['high_school'] ); ?>" placeholder="<?php _e( 'High School', 'myworldclass' ); ?>" />
<input id="filter-users-by-custom-meta" class="button" type="submit" value="Filter" />
<?php

	}
endif;
?>