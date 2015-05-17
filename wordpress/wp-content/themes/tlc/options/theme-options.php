<?php

function attach_main_options_page() {

	$title = "Theme Options";

	add_menu_page(

		$title,

		$title, 

		'edit_themes', 

	    basename(__FILE__),

		create_function('', '')

	);

}

add_action('admin_menu', 'attach_main_options_page');



$home_options = array();

$home_options[] = wp_option::factory('separator', 'home_page_options');

for ($i=1; $i < 7; $i++) { 

	$home_options[] = wp_option::factory('choose_page', 'home_choose_page_' . $i, 'Box ' . $i . ' : Choose Page');

	$home_options[] = wp_option::factory('image', 'home_image_' . $i, 'Box ' . $i . ' : Choose Image');

	$home_options[] = wp_option::factory('text', 'home_text_' . $i, 'Box ' . $i . ' : Description')->set_default_value('Quisque venenatis sollicitudin lacinia. Integer ac sapien quis magna gravida posuere');

}



$inner_options = new OptionsPage(array_merge($home_options, array(

	wp_option::factory('separator', 'other_options'),
	
	wp_option::factory('text', 'wcphone')->set_default_value('http://www.myworldclassrooms.com/'), 
	
	wp_option::factory('text', 'wclogin')->set_default_value('http://www.myworldclassrooms.com/'), 
	
	wp_option::factory('text', 'wcregister')->set_default_value('http://www.myworldclassrooms.com/'), 
	
	wp_option::factory('text', 'facebook')->set_default_value('http://www.facebook.com/'), 

	wp_option::factory('text', 'linkedin')->set_default_value('http://www.linkedin.com/'), 

	wp_option::factory('text', 'twitter')->set_default_value('http://www.twitter.com/'), 

    wp_option::factory('header_scripts', 'header_script'),

    wp_option::factory('footer_scripts', 'footer_script'),

)));

$inner_options->title = 'General';

$inner_options->file = basename(__FILE__);

$inner_options->parent = "theme-options.php";

$inner_options->attach_to_wp();



?>