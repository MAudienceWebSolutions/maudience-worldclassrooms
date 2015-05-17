<?php  
/*
register_post_type('custom-type', array(
	'labels' => array(
		'name'	 => 'Custom Types',
		'singular_name' => 'Custom Type',
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Add new Custom Type' ),
		'view_item' => 'View Custom Type',
		'edit_item' => 'Edit Custom Type',
	    'new_item' => __('New Custom Type'),
	    'view_item' => __('View Custom Type'),
	    'search_items' => __('Search Custom Types'),
	    'not_found' =>  __('No custom types found'),
	    'not_found_in_trash' => __('No custom types found in Trash'),
	),
	'public' => true,
	'exclude_from_search' => false,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => true,
	'_edit_link' =>  'post.php?post=%d',
	'rewrite' => array(
		"slug" => "custom-type",
		"with_front" => false,
	),
	'query_var' => true,
	'supports' => array('title', 'editor', 'page-attributes'),
));
*/

register_post_type('slide', array(
	'labels' => array(
		'name'	 => 'Slides',
		'singular_name' => 'Slide',
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Add new Slide' ),
		'edit_item' => __('Edit Slide'),
	    'new_item' => __('New Slide'),
	    'view_item' => __('View Slide'),
	    'search_items' => __('Search Slides'),
	    'not_found' =>  __('No Slides found'),
	    'not_found_in_trash' => __('No Slides found in Trash'),
	),
	'public' => true,
	'exclude_from_search' => false,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'_edit_link' =>  'post.php?post=%d',
	'rewrite' => false,
	'query_var' => true,
	'supports' => array('title', 'page-attributes'),
)); 

register_post_type('testimonial', array(
	'labels' => array(
		'name'	 => 'Testimonials',
		'singular_name' => 'Testimonial',
		'add_new' => __( 'Add New' ),
		'add_new_item' => __( 'Add new Testimonial' ),
		'edit_item' => __('Edit Testimonial'),
	    'new_item' => __('New Testimonial'),
	    'view_item' => __('View Testimonial'),
	    'search_items' => __('Search Testimonials'),
	    'not_found' =>  __('No Testimonials found'),
	    'not_found_in_trash' => __('No Testimonials found in Trash'),
	),
	'public' => true,
	'exclude_from_search' => false,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'_edit_link' =>  'post.php?post=%d',
	'rewrite' => false,
	'query_var' => true,
	'supports' => array('title', 'editor'),
)); 

?>