<?php
register_taxonomy(
	'faq-category', 
	array('faq'),
	array(
		'labels'            => array(
			'name'                       => __('Categories', 'crb'),
			'singular_name'              => __('Category', 'crb'),
			'search_items'               => __('Search Categories', 'crb'),
			'popular_items'              => __('Popular Categories', 'crb'),
			'all_items'                  => __('All Categories', 'crb'),
			'view_item'                  => __('View Category', 'crb'),
			'edit_item'                  => __('Edit Category', 'crb'),
			'update_item'                => __('Update Category', 'crb'),
			'add_new_item'               => __('Add New Category', 'crb'),
			'new_item_name'              => __('New Category Name', 'crb'),
			'separate_items_with_commas' => __('Separate Categories with commas', 'crb'),
			'add_or_remove_items'        => __('Add or remove Categories', 'crb'),
			'choose_from_most_used'      => __('Choose from the most used Categories', 'crb'),
			'not_found'                  => __('No Categories found.', 'crb'),
			'menu_name'                  => __('Categories', 'crb'),
		),
		'hierarchical'          => true,
		'show_ui'               => true,
		'public'				=> false,
		'exclude_from_search' 	=> true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => false,
	)
);

register_taxonomy(
	'destination', 
	array('tour'),
	array(
		'labels'            => array(
			'name'                       => __('Destinations', 'crb'),
			'singular_name'              => __('Destination', 'crb'),
			'search_items'               => __('Search Destinations', 'crb'),
			'popular_items'              => __('Popular Destinations', 'crb'),
			'all_items'                  => __('All Destinations', 'crb'),
			'view_item'                  => __('View Destination', 'crb'),
			'edit_item'                  => __('Edit Destination', 'crb'),
			'update_item'                => __('Update Destination', 'crb'),
			'add_new_item'               => __('Add New Destination', 'crb'),
			'new_item_name'              => __('New Destination Name', 'crb'),
			'separate_items_with_commas' => __('Separate Destinations with commas', 'crb'),
			'add_or_remove_items'        => __('Add or remove Destinations', 'crb'),
			'choose_from_most_used'      => __('Choose from the most used Destinations', 'crb'),
			'not_found'                  => __('No Destinations found.', 'crb'),
			'menu_name'                  => __('Destinations', 'crb'),
		),
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'destination' ),
	)
);
