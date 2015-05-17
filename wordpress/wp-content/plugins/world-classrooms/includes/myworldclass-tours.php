<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Get Tour by Code
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_get_tour_by_code' ) ) :
	function mywclass_get_tour_by_code( $code = NULL ) {

		if ( $code === NULL ) return false;

		global $wpdb;
		$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'tour_code' AND meta_value = %s", $code ) );
		if ( $post_id === NULL )
			return false;

		return $post_id;

	}
endif;

/**
 * Register Tour Type
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_register_tour_type' ) ) :
	function mywclass_register_tour_type() {

		$labels = array(
			'name'                => _x( 'Tours', 'Post Type General Name', 'myworldclass' ),
			'singular_name'       => _x( 'Tour', 'Post Type Singular Name', 'myworldclass' ),
			'menu_name'           => __( 'Tours', 'myworldclass' ),
			'parent_item_colon'   => __( 'Parent Tour:', 'myworldclass' ),
			'all_items'           => __( 'All Tours', 'myworldclass' ),
			'view_item'           => __( 'View Tour', 'myworldclass' ),
			'add_new_item'        => __( 'Add New Tour', 'myworldclass' ),
			'add_new'             => __( 'Add New Tour', 'myworldclass' ),
			'edit_item'           => __( 'Edit Tour', 'myworldclass' ),
			'update_item'         => __( 'Update Tour', 'myworldclass' ),
			'search_items'        => __( 'Search tours', 'myworldclass' ),
			'not_found'           => __( 'No tours found', 'myworldclass' ),
			'not_found_in_trash'  => __( 'No tours found in the trash', 'myworldclass' ),
		);
		$rewrite = array(
			'slug'                => 'tours',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 11,
			'menu_icon'           => 'dashicons-welcome-learn-more',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);
		register_post_type( 'tour', $args );

	}
endif;

/**
 * Row Actions
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_update_messages' ) ) :
	function mywclass_tour_update_messages( $messages ) {

		$messages['tour'] = array(
			0 => '',
			1 => __( 'Tour Updated.', 'myworldclass' ),
			2 => '',
			3 => '',
			4 => __( 'Tour Updated.', 'myworldclass' ),
			5 => false,
			6 => __( 'Tour Enabled', 'myworldclass' ),
			7 => __( 'Tour Saved', 'myworldclass' ),
			8 => __( 'Tour Updated.', 'myworldclass' ),
			9 => __( 'Tour Updated.', 'myworldclass' ),
			10 => __( 'Tour Updated.', 'myworldclass' )
		);
		return $messages;

	}
endif;

/**
 * Column Headers
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_column_headers' ) ) :
	function mywclass_tour_column_headers( $columns ) {

		unset( $columns['date'] );

		$columns['tour-code'] = __( 'Tour Code', 'myworldclass' );
		$columns['tour-cost'] = __( 'Cost', 'myworldclass' );
		$columns['tour-start'] = __( 'Start Date', 'myworldclass' );
		$columns['tour-users'] = __( 'Attendees', 'myworldclass' );
		$columns['tour-close'] = __( 'Payment Due', 'myworldclass' );
		return $columns;

	}
endif;

/**
 * Column Content
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_column_content' ) ) :
	function mywclass_tour_column_content( $column_name, $post_id ) {

		$tour = new MyWorldClass_Tour( $post_id );
		switch ( $column_name ) {

			case 'tour-code' :

				echo '<code>' . $tour->display_tour_code() . '</code>';

			break;
			
			case 'tour-cost' :

				echo $tour->display_cost();

			break;
			
			case 'tour-start' :

				echo $tour->display_start_date();

			break;
			
			case 'tour-close' :

				echo $tour->display_last_pay_date();

			break;

			case 'tour-users' :

				echo $tour->display_attendee_count_admin();

			break;

		}

	}
endif;

/**
 * Row Actions
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_row_actions' ) ) :
	function mywclass_tour_row_actions( $actions, $post ) {

		if ( $post->post_type == 'tour' ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );
		}
		return $actions;

	}
endif;

/**
 * Add Metaboxes
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_meta_boxes' ) ) :
	function mywclass_tour_meta_boxes() {

		add_meta_box(
			'my-world-class-tour-details',
			__( 'Tour Setup', 'myworldclass' ),
			'mywclass_tour_metabox_tour_details',
			'tour',
			'side',
			'high'
		);

		add_meta_box(
			'my-world-class-tour-costs',
			__( 'Costs', 'myworldclass' ),
			'mywclass_tour_metabox_tour_costs',
			'tour',
			'side',
			'high'
		);

		add_meta_box(
			'my-world-class-tour-notice',
			__( 'Notice for Students', 'myworldclass' ),
			'mywclass_tour_metabox_tour_notice',
			'tour',
			'normal',
			'high'
		);

	}
endif;

/**
 * Metabox: Tour Details
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_metabox_tour_details' ) ) :
	function mywclass_tour_metabox_tour_details( $post ) {

		$tour = new MyWorldClass_Tour( $post->ID ); ?>

<style type="text/css">
#my-world-class-tour-details p label { display: block; font-weight: bold; }
#my-world-class-tour-details input { width: 100%; }
</style>
<p>
	<label for="my-tour-tour_code">Tour Code</label>
	<input type="text" name="mytour[tour_code]" id="my-tour-tour_code" value="<?php echo $tour->tour_code; ?>" />
</p>
<p>
	<label for="my-tour-start_date">Start Date</label>
	<input type="date" name="mytour[start_date]" id="my-tour-start_date" value="<?php echo $tour->start_date; ?>" />
</p>
<p>
	<label for="my-tour-last_pay_date">Payments Due By</label>
	<input type="date" name="mytour[last_pay_date]" id="my-tour-last_pay_date" value="<?php echo $tour->last_pay_date; ?>" />
</p>
<?php

	}
endif;

/**
 * Metabox: Tour Details
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_metabox_tour_costs' ) ) :
	function mywclass_tour_metabox_tour_costs( $post ) {

		$tour = new MyWorldClass_Tour( $post->ID ); ?>

<style type="text/css">
#my-world-class-tour-details p label { display: block; font-weight: bold; }
#my-world-class-tour-details input { width: 100%; }
</style>
<p>
	<label for="my-tour-cost">Tour Cost - Parent</label>
	<input type="text" name="mytour[cost_adult]" id="my-tour-cost-adult" value="<?php echo $tour->cost_adult; ?>" />
</p>
<p>
	<label for="my-tour-cost">Tour Cost - Student</label>
	<input type="text" name="mytour[cost]" id="my-tour-cost" value="<?php echo $tour->cost; ?>" />
</p>
<?php

	}
endif;

/**
 * Metabox: Tour Details
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_metabox_tour_notice' ) ) :
	function mywclass_tour_metabox_tour_notice( $post ) {

		$tour = new MyWorldClass_Tour( $post->ID );
		$notice = get_post_meta( $post->ID, 'notice', true ); ?>

<textarea name="mytour[notice]" id="my-tour-notice" style="width: 97%;" cols="40" rows="3"><?php echo esc_attr( $notice ); ?></textarea>
<?php

	}
endif;

/**
 * Save Tour Details
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_save_details' ) ) :
	function mywclass_tour_save_details( $post_id ) {

		if ( isset( $_POST['mytour'] ) && current_user_can( 'edit_users' ) ) {

			$old_code = get_post_meta( $post_id, 'tour_code', true );
			$tour_code = sanitize_text_field( $_POST['mytour']['tour_code'] );
			update_post_meta( $post_id, 'tour_code', $tour_code );
			if ( $tour_code != '' && $tour_code != $old_code ) {
				$tour = new MyWorldClass_Tour( $post_id );
				$tour->get_attendees( true );
			}

			$cost = sanitize_text_field( $_POST['mytour']['cost'] );
			update_post_meta( $post_id, 'cost', $cost );

			$cost_adult = sanitize_text_field( $_POST['mytour']['cost_adult'] );
			update_post_meta( $post_id, 'cost_adult', $cost_adult );

			$start_date = sanitize_text_field( $_POST['mytour']['start_date'] );
			update_post_meta( $post_id, 'start_date', $start_date );

			$last_pay_day = sanitize_text_field( $_POST['mytour']['last_pay_date'] );
			update_post_meta( $post_id, 'last_pay_date', $last_pay_day );

			$notice = trim( $_POST['mytour']['notice'] );
			update_post_meta( $post_id, 'notice', $notice );

		}

	}
endif;
?>