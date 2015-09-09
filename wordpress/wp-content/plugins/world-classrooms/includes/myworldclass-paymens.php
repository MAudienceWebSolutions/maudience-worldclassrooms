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
			'add_new_item'        => __( 'Add Payment', 'myworldclass' ),
			'add_new'             => __( 'Add Payment', 'myworldclass' ),
			'edit_item'           => __( 'Edit Tour Payment', 'myworldclass' ),
			'update_item'         => __( 'Update Tour Payment', 'myworldclass' ),
			'search_items'        => __( 'Search tour payments', 'myworldclass' ),
			'not_found'           => __( 'No tour payments found', 'myworldclass' ),
			'not_found_in_trash'  => __( 'No tour payments found in the trash', 'myworldclass' ),
		);
		$args = array(
			'labels'              => $labels,
			'supports'            => array( 'title' ),
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
			'capability_type'     => 'page',
			'register_meta_box_cb' => 'mywclass_tour_payment_metaboxes'
		);
		register_post_type( 'tour_payment', $args );

	}
endif;

?>