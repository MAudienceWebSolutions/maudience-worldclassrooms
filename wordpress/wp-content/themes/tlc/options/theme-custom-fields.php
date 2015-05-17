<?php

/*

$panel =& new ECF_Panel('custom-data', 'Custom Data', 'page', 'normal', 'high');

$panel->add_fields(array(

	// ECF_Field::factory('text', 'my_data')->multiply()->help_text('lorem'),

	// ECF_Field::factory('map', 'location')->set_position(37.423156, -122.084917, 14),

	ECF_Field::factory('image', 'img'),

	ECF_Field::factory('file', 'pdf'),

));

*/



$slide_settings_panel =& new ECF_Panel('slide_settings_panel', 'Slide Settings', 'slide', 'normal', 'high');

$slide_settings_panel->add_fields(array(

	ECF_Field::factory('image', 'slide_image')->set_size(717, 304), 

));



?>