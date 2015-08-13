<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Register Tour Payment Type
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_register_tour_payment_type' ) ) :
	function mywclass_register_tour_payment_type() {

		$labels = array(
			'name'                => _x( 'Tour Payments', 'Post Type General Name', 'myworldclass' ),
			'singular_name'       => _x( 'Tour Payment', 'Post Type Singular Name', 'myworldclass' ),
			'menu_name'           => __( 'Tour Payments', 'myworldclass' ),
			'parent_item_colon'   => '',
			'all_items'           => __( 'Payments', 'myworldclass' ),
			'view_item'           => __( 'View Tour Payment', 'myworldclass' ),
			'add_new_item'        => '',
			'add_new'             => '',
			'edit_item'           => __( 'Edit Tour Payment', 'myworldclass' ),
			'update_item'         => __( 'Update Tour Payment', 'myworldclass' ),
			'search_items'        => __( 'Search tour payments', 'myworldclass' ),
			'not_found'           => __( 'No tour payments found', 'myworldclass' ),
			'not_found_in_trash'  => __( 'No tour payments found in the trash', 'myworldclass' ),
		);
		$args = array(
			'labels'              => $labels,
			'supports'            => array( 'title', 'custom-fields' ),
			'hierarchical'        => true,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=tour',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page'
		);
		register_post_type( 'tour_payment', $args );

	}
endif;

/**
 * 
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_update_messages' ) ) :
	function mywclass_tour_payment_update_messages( $messages ) {

		$messages['tour_payment'] = array(
			0 => '',
			1 => __( 'Tour Payment Updated.', 'myworldclass' ),
			2 => '',
			3 => '',
			4 => __( 'Tour Payment Updated.', 'myworldclass' ),
			5 => false,
			6 => __( 'Tour Payment Enabled', 'myworldclass' ),
			7 => __( 'Tour Payment Saved', 'myworldclass' ),
			8 => __( 'Tour Payment Updated.', 'myworldclass' ),
			9 => __( 'Tour Payment Updated.', 'myworldclass' ),
			10 => __( 'Tour Payment Updated.', 'myworldclass' )
		);
		return $messages;

	}
endif;

/**
 * Column Headers
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_column_headers' ) ) :
	function mywclass_tour_payment_column_headers( $columns ) {

		unset( $columns['cb'] );
		unset( $columns['title'] );
		unset( $columns['date'] );
		unset( $columns['author'] );

		$columns['title'] = __( 'Transaction ID', 'myworldclass' );
		$columns['status'] = __( 'Status', 'myworldclass' );
		$columns['author'] = __( 'Student', 'myworldclass' );
		$columns['amount'] = __( 'Amount', 'myworldclass' );
		$columns['note'] = __( 'Notes', 'myworldclass' );
		return $columns;

	}
endif;

/**
 * Column Content
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_column_content' ) ) :
	function mywclass_tour_payment_column_content( $column_name, $post_id ) {

		switch ( $column_name ) {

			case 'status' :

				echo '<code>' . get_post_meta( $post_id, 'status', true ) . '</code>';

			break;
			
			case 'amount' :

				$amount = get_post_meta( $post_id, 'amount', true );
				echo '$ ' . number_format( $amount, 2, '.', ' ' );

			break;
			
			case 'note' :

				echo '<em>' . get_post_meta( $post_id, 'note', true ) . '</em>';

			break;

		}

	}
endif;

/**
 * Row Actions
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_row_actions' ) ) :
	function mywclass_tour_payment_row_actions( $actions, $post ) {

		if ( $post->post_type == 'tour_payment' ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;

	}
endif;

/**
 * Filter Payments
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_filter_tour_payments' ) ) :
	function mywclass_filter_tour_payments( $query ) {

		if ( is_admin() && isset( $query->query['post_type'] ) && $query->query['post_type'] == 'tour_payment' ) {

			$qv = &$query->query_vars;

			if ( isset( $_GET['student_id'] ) )
				$qv['author'] = absint( $_GET['student_id'] );

			if ( isset( $_GET['transaction_id'] ) )
				$qv['name'] = absint( $_GET['transaction_id'] );

		}

	}
endif;

/**
 * Filter Payments Options
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_filter_tour_payments_option' ) ) :
	function mywclass_filter_tour_payments_option() {

		$screen = get_current_screen();
		if ( $screen->id != 'edit-tour_payment' ) return;

?>
<style type="text/css">div.bulkactions { display: none; } .wrap .add-new-h2 { display: none; }</style>
<input type="text" size="20" name="transaction_id" value="<?php if ( isset( $_GET['transaction_id'] ) ) echo urldecode( $_GET['transaction_id'] ); ?>" placeholder="Transaction ID" /> <input type="text" size="15" name="student_id" value="<?php if ( isset( $_GET['student_id'] ) ) echo urldecode( $_GET['student_id'] ); ?>" placeholder="Student ID" />
<?php
	}
endif;

?>