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
			'feeds'               => false,
		);
		$args = array(
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 11,
			'menu_icon'           => 'dashicons-welcome-learn-more',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
			'register_meta_box_cb' => 'mywclass_tour_meta_boxes'
		);
		register_post_type( 'tour', $args );

		add_rewrite_endpoint( 'signup', EP_PERMALINK | EP_PAGES );

		add_filter( 'query_vars',    'mywclass_add_signup_endpoint', 10 );
		add_action( 'parse_request', 'mywclass_parse_signup_endpoint', 10 );

	}
endif;

/**
 * Add Signup Endpoint
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_add_signup_endpoint' ) ) :
	function mywclass_add_signup_endpoint( $vars ) {

		if ( ! isset( $vars['signup'] ) )
			$vars['signup'] = 'signup';

		return $vars;

	}
endif;

/**
 * Parse Signup Endpoint
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_parse_signup_endpoint' ) ) :
	function mywclass_parse_signup_endpoint() {

		if ( ! is_singular( 'tour' ) ) return;

		global $wp;

		if ( isset( $_GET['signup'] ) ) {
			$wp->vars['signup'] = 'signup';
		}

		elseif ( isset( $wp->vars['signup'] ) ) {
			$wp->vars['signup'] = $wp->vars['signup'];
		}

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
			0  => __( 'Tour Updated.', 'myworldclass' ),
			1  => __( 'Tour Updated.', 'myworldclass' ),
			2  => __( 'Tour Updated.', 'myworldclass' ),
			3  => __( 'Tour Updated.', 'myworldclass' ),
			4  => __( 'Tour Updated.', 'myworldclass' ),
			5  => false,
			6  => __( 'Tour Enabled', 'myworldclass' ),
			7  => __( 'Tour Saved', 'myworldclass' ),
			8  => __( 'Tour Updated.', 'myworldclass' ),
			9  => __( 'Tour Updated.', 'myworldclass' ),
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

		$columns['tour-code']  = __( 'Trip ID', 'myworldclass' );
		$columns['tour-cost']  = __( 'Cost', 'myworldclass' );
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
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_tour_row_actions' ) ) :
	function mywclass_tour_row_actions( $actions, $post ) {

		if ( in_array( $post->post_type, array( 'tour', 'tours' ) ) && $post->post_status != 'trash' ) {

			unset( $actions['inline hide-if-no-js'] );

			if ( current_user_can( 'edit_users' ) )
				$actions['clone'] = '<a href="' . add_query_arg( array( 'do' => 'clone', 'target' => $post->ID ) ) . '">Clone</a>';

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
			'my-world-class-tour-tour-data',
			__( 'Tour Information', 'myworldclass' ),
			'mywclass_tour_metabox_tour_data',
			'tour',
			'normal',
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
			'my-world-class-tour-subscription',
			__( 'Scholarships', 'myworldclass' ),
			'mywclass_tour_metabox_tour_scholarships',
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

		add_meta_box(
			'my-world-class-tour-downloads',
			__( 'Downloads', 'myworldclass' ),
			'mywclass_tour_metabox_tour_downloads',
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
if ( ! function_exists( 'mywclass_tour_metabox_tour_costs' ) ) :
	function mywclass_tour_metabox_tour_costs( $post ) {

		$tour = new MyWorldClass_Tour( $post->ID ); ?>

<style type="text/css">
#my-world-class-tour-costs .inside { margin: 0 0 0 0; padding: 0 0 0 0; }
#costs-wrapper { float: none; clear: both; }
#costs-wrapper .costs { float: none; clear: both; min-height: 50px; padding-bottom: 12px; border-bottom: 1px solid #ddd; padding: 10px 12px 12px 12px; }
#costs-wrapper p { padding: 6px 12px; font-size: 10px; line-height: 12px; }
#costs-wrapper .costs .sc-code { padding-right: 12px; }
#costs-wrapper .costs > div { float: left; min-height: 50px; }
#costs-wrapper .costs label { display: block; font-weight: bold; }
</style>
<div id="costs-wrapper">
	<div class="costs">
		<div class="sc-code">
			<label for="my-tour-cost-student">Cost / Student</label>
			$ <input type="text" name="mytour[student_cost]" size="10" id="my-tour-cost-student" value="<?php echo $tour->cost; ?>" />
		</div>
		<div class="sc-value">
			<label for="my-tour-cost-parent">Cost / Parent</label>
			$ <input type="text" name="mytour[parent_cost]" size="10" id="my-tour-cost-parent" value="<?php echo $tour->cost_adult; ?>" />
		</div>
	</div>
	<div class="costs">
		<div class="sc-code">
			<label for="my-tour-cost-minimum">Min. Payment</label>
			$ <input type="text" name="mytour[minimum]" size="10" id="my-tour-cost-minimum" value="<?php echo $tour->minimum; ?>" placeholder="0.00" />
		</div>
		<div class="sc-value">
			<label for="my-tour-cost-sixty-days">60 day Payment</label>
			$ <input type="text" name="mytour[sixty_days]" size="10" id="my-tour-cost-sixty-days" value="<?php echo $tour->sixty_days; ?>" placeholder="0.00" />
		</div>
	</div>
	<div><p class="description">The 60 day payment amount is only used by the manual payment plan.</p></div>
	<div class="clear clearfix"></div>
</div>
<?php

	}
endif;

/**
 * Metabox: Tour Scholarships
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_metabox_tour_scholarships' ) ) :
	function mywclass_tour_metabox_tour_scholarships( $post ) {

		$tour = new MyWorldClass_Tour( $post->ID );

?>
<style type="text/css">
#Layout { display: none; }
#my-world-class-tour-subscription .inside { margin: 0 0 0 0; padding: 0 0 0 0; }
#scholarship-wrapper { float: none; clear: both; }
#scholarship-wrapper .scholarship { float: none; clear: both; min-height: 50px; padding-bottom: 12px; border-bottom: 1px solid #ddd; padding: 10px 12px 12px 12px; }
#scholarship-wrapper p { padding: 6px 12px; font-size: 10px; line-height: 12px; }
#scholarship-wrapper .scholarship .sc-code { padding-right: 12px; }
#scholarship-wrapper .scholarship > div { float: left; min-height: 50px; }
#scholarship-wrapper .scholarship label { display: block; font-weight: bold; }
</style>
<div id="scholarship-wrapper">
<?php

		$scholarships = $tour->get_scholarships();
		if ( ! empty( $scholarships ) ) {
			foreach ( $scholarships as $id => $data ) {
?>
	<div class="scholarship">
		<div class="sc-code">
			<label for="my-tour-<?php echo $id; ?>-code">Scholarship Code #<?php echo $id + 1; ?></label>
			<input type="text" name="mytour[scholarships][<?php echo $id; ?>][code]" size="15" id="my-tour-<?php echo $id; ?>-code" value="<?php echo $data['code']; ?>" />
		</div>
		<div class="sc-value">
			<label for="my-tour-<?php echo $id; ?>-value">Code Value</label>
			$ <input type="text" name="mytour[scholarships][<?php echo $id; ?>][value]" size="10" id="my-tour-<?php echo $id; ?>-value" value="<?php echo $data['value']; ?>" placeholder="0.00" />
		</div>
	</div>
<?php
			}
		}

?>
	<div><p class="description">Leave a field empty if no scholarship codes are accepted here.</p></div>
	<div class="clear clearfix"></div>
</div>
<?php

	}
endif;

if ( ! function_exists( 'mywclass_tour_metabox_tour_data' ) ) :
	function mywclass_tour_metabox_tour_data( $post ) {

		$tour = new MyWorldClass_Tour( $post->ID );

?>
<style type="text/css">
#my-world-class-tour-tour-data .inside { margin: 0 0 0 0; padding: 0 0 0 0; }
#info-wrapper { float: none; clear: both; }
#info-wrapper .info-box { float: left; width: 25%; min-height: 50px; border-bottom: 1px solid #ddd; padding: 0 0 0 0; }
#info-wrapper .info-box.half { width: 50%; }
#info-wrapper .info-box.third { width: 33%; }
#info-wrapper p { padding: 6px 12px; font-size: 10px; line-height: 12px; }
#info-wrapper .info-box .sc-code { padding-right: 12px; }
#info-wrapper .info-box > div { padding: 10px 12px; }
#info-wrapper .info-box label { display: block; font-weight: bold; }
#info-wrapper .info-box input { width: 100%; font-family: Consolas,Monaco,monospace; min-height: 29px; }
</style>
<div id="info-wrapper">
	<div class="info-box">
		<div class="padding">
			<label for="tour-detail-school">School Name</label>
			<div class="input-wrap">
				<input type="text" name="mytour[school]" id="tour-detail-school" class="" value="<?php echo $tour->school; ?>" />
			</div>
		</div>
	</div>
	<div class="info-box half">
		<div class="padding">
			<label for="tour-detail-tour-code">Trip ID Number</label>
			<div class="input-wrap">
				<input type="text" name="mytour[tour_code]" id="tour-detail-tour-code" class="" value="<?php echo $tour->tour_code; ?>" />
			</div>
		</div>
	</div>
	<div class="info-box">
		<div class="padding">
			<label for="tour-detail-location">Trip Location</label>
			<div class="input-wrap">
				<input type="text" name="mytour[location]" id="tour-detail-location" class="" value="<?php echo $tour->location; ?>" />
			</div>
		</div>
	</div>
	
	<div class="info-box third">
		<div class="padding">
			<label for="tour-detail-start-date">Trip Start Date</label>
			<div class="input-wrap">
				<input type="date" name="mytour[start_date]" id="tour-detail-start-date" value="<?php echo $tour->start_date; ?>" />
			</div>
		</div>
	</div>
	<div class="info-box third">
		<div class="padding">
			<label for="tour-detail-end-date">Trip End Date</label>
			<div class="input-wrap">
				<input type="date" name="mytour[end_date]" id="tour-detail-end-date" class="" value="<?php echo $tour->end_date; ?>" />
			</div>
		</div>
	</div>
	<div class="info-box third">
		<div class="padding">
			<label for="tour-detail-last-pay-date">Payment Due Date</label>
			<div class="input-wrap">
				<input type="date" name="mytour[last_pay_date]" id="tour-detail-last-pay-date" value="<?php echo $tour->last_pay_date; ?>" />
			</div>
		</div>
	</div>
	<div class="info-box half">
		<div class="padding">
			<label for="tour-detail-teacher-name">Teacher Name</label>
			<div class="input-wrap">
				<input type="text" name="mytour[teacher_name]" id="tour-detail-teacher-name" class="" value="<?php echo $tour->teacher_name; ?>" />
			</div>
		</div>
	</div>
	
	<div class="info-box half">
		<div class="padding">
			<label for="tour-detail-teacher-email">Teacher Email</label>
			<div class="input-wrap">
				<input type="text" name="mytour[teacher_email]" id="tour-detail-teacher-email" class="" value="<?php echo $tour->teacher_email; ?>" />
			</div>
		</div>
	</div>
	<div class="clear clearfix"></div>
</div>
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
		$notice = get_post_meta( $post->ID, 'notice', true );

?>
<p>Optional information to show to attendees on their "My Account" page.</p>
<textarea name="mytour[notice]" id="my-tour-notice" style="width: 97%;" cols="40" rows="3"><?php echo esc_attr( $notice ); ?></textarea>
<?php

	}
endif;

/**
 * Metabox: Tour Downloads
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_metabox_tour_downloads' ) ) :
	function mywclass_tour_metabox_tour_downloads( $post ) {

		$downloads = get_post_meta( $post->ID, 'download_content', true );

?>
<p>Optional download specific for this tour.</p>
<?php

		wp_editor( $downloads, 'tourspecificdownloads', array( 'textarea_name' => 'mytour[download_content]', 'textarea_rows' => 10 ) );

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

			$student_cost = sanitize_text_field( $_POST['mytour']['student_cost'] );
			if ( $student_cost != '' )
				$student_cost = number_format( $student_cost, 2, '.', '' );
			update_post_meta( $post_id, 'cost', $student_cost );

			$minimum = sanitize_text_field( $_POST['mytour']['minimum'] );
			if ( $minimum != '' )
				$minimum = number_format( $minimum, 2, '.', '' );
			update_post_meta( $post_id, 'minimum', $minimum );

			$parent_cost = sanitize_text_field( $_POST['mytour']['parent_cost'] );
			if ( $parent_cost != '' )
				$parent_cost = number_format( $parent_cost, 2, '.', '' );
			update_post_meta( $post_id, 'cost_adult', $parent_cost );

			$sixty_days = sanitize_text_field( $_POST['mytour']['sixty_days'] );
			if ( $sixty_days != '' )
				$sixty_days = number_format( $sixty_days, 2, '.', '' );
			update_post_meta( $post_id, 'sixty_days', $sixty_days );

			$notice = trim( $_POST['mytour']['notice'] );
			update_post_meta( $post_id, 'notice', $notice );

			$download_content = trim( $_POST['mytour']['download_content'] );
			update_post_meta( $post_id, 'download_content', $download_content );

			$scholarships = array();
			foreach ( $_POST['mytour']['scholarships'] as $row => $data ) {
				$scholarships[] = array(
					'code'  => sanitize_text_field( $data['code'] ),
					'value' => ( ( $data['value'] != '' ) ? number_format( $data['value'], 2, '.', '' ) : '' )
				);
			}
			update_post_meta( $post_id, 'scholarships', $scholarships );

			
			$school = sanitize_text_field( $_POST['mytour']['school'] );
			update_post_meta( $post_id, 'school', $school );

			$old_code  = get_post_meta( $post_id, 'tour_code', true );
			$tour_code = sanitize_text_field( $_POST['mytour']['tour_code'] );
			update_post_meta( $post_id, 'tour_code', $tour_code );
			if ( $tour_code != '' && $tour_code != $old_code ) {
				$tour = new MyWorldClass_Tour( $post_id );
				$tour->get_attendees( true );
			}

			$location = sanitize_text_field( $_POST['mytour']['location'] );
			update_post_meta( $post_id, 'location', $location );

			$start_date = sanitize_text_field( $_POST['mytour']['start_date'] );
			update_post_meta( $post_id, 'start_date', $start_date );

			$end_date = sanitize_text_field( $_POST['mytour']['end_date'] );
			update_post_meta( $post_id, 'end_date', $end_date );

			$last_pay_day = sanitize_text_field( $_POST['mytour']['last_pay_date'] );
			update_post_meta( $post_id, 'last_pay_date', $last_pay_day );

			$teacher_name = sanitize_text_field( $_POST['mytour']['teacher_name'] );
			update_post_meta( $post_id, 'teacher_name', $teacher_name );

			$teacher_email = sanitize_text_field( $_POST['mytour']['teacher_email'] );
			update_post_meta( $post_id, 'teacher_email', $teacher_email );

		}

	}
endif;
?>