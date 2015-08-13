<?php
$sections = array(
	'travel' => 'Left bottom aligned text section',
	'customizable' => 'Section with background',
	'passion' => 'Left top aligned text section',
	'connected' => 'Dark background section',
	'limits' => 'List',
	'mountain' => 'Text with background',
);

$includes = array(
	'person' => 'Person',
	'road' => 'Road',
	'pork' => 'Pork',
	'bed' => 'Bed',
	'map' => 'Map',
	'hands2' => 'Hands',
	'money' => 'Money',
	'tickets' => 'Tickets',
	'police' => 'Police',
	'phone' => 'Phone',
);

$icon_labels = array(
	'plural_name'=>__('Icons','crb'),
	'singular_name'=>__('Icon','crb'),
);

$tour_labels = array(
	'plural_name'=>__('Tours','crb'),
	'singular_name'=>__('Tour','crb'),
);

$testimonial_labels = array(
	'plural_name'=>__('Testimonials','crb'),
	'singular_name'=>__('Testimonial','crb'),
);

$image_labels = array(
	'plural_name'=>__('Images','crb'),
	'singular_name'=>__('Image','crb'),
);

$section_labels = array(
	'plural_name'=>__('Sections','crb'),
	'singular_name'=>__('Section','crb'),
);

$item_labels = array(
	'plural_name'=>__('Items','crb'),
	'singular_name'=>__('Item','crb'),
);

$step_labels = array(
	'plural_name'=>__('Steps','crb'),
	'singular_name'=>__('Step','crb'),
);
Carbon_Container::factory('custom_fields', __('Home settings', 'crb'))
	->show_on_post_type('page')
	->show_on_template('templates/home.php')
	->add_tab(__('Header','crb'),array(
			Carbon_Field::factory('text', 'crb_form_title', __('Form Title', 'crb')),
			Carbon_Field::factory("gravity_form", "crb_gravity_form", "Select a Form")
		))
	->add_tab(__('World Class difference section', 'crb'), array(
			Carbon_Field::factory('text', 'crb_world_class_title', __('Title', 'crb')),
			Carbon_Field::factory('complex', 'crb_world_classes', '')
			->setup_labels($icon_labels)
			->add_fields(array(
				Carbon_Field::factory('attachment', 'crb_world_class_icon', __('Icon', 'crb'))
					->help_text(__('Recommended image size: 101px * 65px. Larger images will be resized automatically.','crb')),
				Carbon_Field::factory('text', 'crb_world_class_title', __('Title', 'crb')),
				Carbon_Field::factory('textarea', 'crb_world_class_content', __('Text', 'crb')),
				Carbon_Field::factory('text', 'crb_world_class_link', __('link', 'crb'))
					->set_required(true),
			)),
		))
	->add_tab(__('Popular education tours', 'crb'), array(
			Carbon_Field::factory('text', 'crb_education_tours_title', __('Title', 'crb')),
			Carbon_Field::factory('attachment', 'crb_education_tours_background', __('Background Image', 'crb')),
			Carbon_Field::factory('complex', 'crb_education_tours', '')
			->setup_labels($tour_labels)
			->add_fields(array(
				Carbon_Field::factory('attachment', 'crb_education_tour_icon', __('Image', 'crb'))
					->help_text(__('Recommended image size: 187px * 186px. Larger images will be resized automatically.','crb')),
				Carbon_Field::factory('text', 'crb_education_tour_title', __('Title', 'crb')),
				Carbon_Field::factory('text', 'crb_education_tour_link', __('link', 'crb'))
					->set_required(true),
			)),
		))
	->add_tab(__('Testimonials section', 'crb'), array(
			Carbon_Field::factory('text', 'crb_testimonial_title', __('Title', 'crb')),
			Carbon_Field::factory('complex', 'crb_testimonials', 'Quotes')
			->setup_labels($testimonial_labels)
			->add_fields(array(
				Carbon_Field::factory('textarea', 'crb_testimonial_quote', __('Quote', 'crb'))
					->set_rows(5),
				Carbon_Field::factory('text', 'crb_testimonial_author', __('Author', 'crb')),
				Carbon_Field::factory('text', 'crb_testimonial_author_position', __('Author Position', 'crb')),
			)),
		))
	->add_tab(__('Callout', 'crb'), array(
			Carbon_Field::factory('complex', 'crb_callout_images', '')
			->setup_labels($image_labels)
			->add_fields(array(
				Carbon_Field::factory('attachment', 'crb_callout_image', __('Image', 'crb')),
				Carbon_Field::factory('text', 'crb_callout_image_link', __('Link', 'crb'))
					->set_required(true),
			)),
			Carbon_Field::factory('text', 'crb_callout_message', __('Message', 'crb')),
			Carbon_Field::factory('text', 'crb_callout_btn_text', __('Button text', 'crb')),
			Carbon_Field::factory('text', 'crb_callout_link', __('Link', 'crb')),
		));

Carbon_Container::factory('custom_fields', __('Contact settings', 'crb'))
	->show_on_post_type('page')
	->show_on_template('templates/contact.php')
	->add_tab(__('Left Column', 'crb'), array(
			Carbon_Field::factory('text', 'crb_left_column_title', __('Title', 'crb')),
			Carbon_Field::factory('rich_text', 'crb_left_column_content', __('Text', 'crb')),
			Carbon_Field::factory('text', 'crb_left_column_phone', __('Phone', 'crb')),
			Carbon_Field::factory('text', 'crb_left_column_email', __('E-Mail', 'crb')),
		))
	->add_tab(__('Right Column', 'crb'), array(
			Carbon_Field::factory('text', 'crb_right_column_title', __('Title', 'crb')),
			Carbon_Field::factory('rich_text', 'crb_right_column_content', __('Text', 'crb')),
			Carbon_Field::factory('text', 'crb_right_column_phone', __('Phone', 'crb')),
			Carbon_Field::factory('text', 'crb_right_column_email', __('E-Mail', 'crb')),
		));
Carbon_Container::factory('custom_fields', __('Subtitle', 'crb'))
	->show_on_post_type('page')
	->hide_on_template( array('about', 'centered', 'destination', 'gallery', 'home'))
	->add_fields(array(
			Carbon_Field::factory('text', 'crb_subtitle', '')
	));

Carbon_Container::factory('custom_fields', __('About settings', 'crb'))
	->show_on_post_type('page')
	->show_on_template('templates/about.php')
	->add_tab(__('Main Settings','crb'),array(
			Carbon_Field::factory('text', 'crb_about_title', __('Title', 'crb'))
				->help_text(__('You can use (*text*) to make a red word.', 'crb')),
			Carbon_Field::factory('text', 'crb_about_subtitle', __('Subtitle', 'crb')),
			Carbon_Field::factory('text', 'crb_about_btn_text', __('Button Text', 'crb')),
			Carbon_Field::factory('text', 'crb_about_btn_link', __('Link', 'crb')),
		))
	->add_tab(__('Sections','crb'),array(
			Carbon_Field::factory('complex', 'crb_sections', '')
			->setup_labels($section_labels)
			->add_fields(array(
				Carbon_Field::factory('select', 'crb_section', __('Section Type', 'crb'))
					->set_options($sections),
				Carbon_Field::factory('text', 'crb_section_title', __('Title', 'crb'))
					->help_text(__('You can use (*text*) to make a red word.', 'crb')),
				Carbon_Field::factory('textarea', 'crb_section_content', __('Content', 'crb'))
					->set_rows(4),
				Carbon_Field::factory('attachment', 'crb_section_image', 'Image')
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => 'crb_section',
							'value' => array('limits'),
							'compare' => 'NOT IN',
						)
					)),
				Carbon_Field::factory('complex', 'crb_section_items', 'Items')
					->setup_labels($item_labels)
					->set_conditional_logic(array(
						'relation' => 'AND',
						array(
							'field' => 'crb_section',
							'value' => array('limits'),
							'compare' => 'IN',
						)
					))
					->add_fields(array(
						Carbon_Field::factory('text', 'crb_section_item_title', __('Title', 'crb')),
						Carbon_Field::factory('attachment', 'crb_section_item_image', __('Image', 'crb')),
					))
			)),
	));

Carbon_Container::factory('custom_fields', __('Layout', 'crb'))
	->show_on_post_type('tour')
	->add_fields(array(
		Carbon_Field::factory('radio', 'crb_layout', __('Layout Type', 'crb'))
			->set_default_value('normal')
			->set_options(array(
				'normal' => 'Normal',
				'private' => 'Private',
			)),
		Carbon_Field::factory('rich_text', 'crb_information', 'Information')
			->set_conditional_logic(array(
				'relation' => 'AND',
				array(
					'field' => 'crb_layout',
					'value' => 'private',
					'compare' => '=',
				)
			)),
	));

Carbon_Container::factory('custom_fields', __('Options', 'crb'))
	->show_on_post_type('tour')
	->add_tab(__('Preview Options','crb'),array(
			Carbon_Field::factory('text', 'crb_meta', 'Meta'),
			Carbon_Field::factory('attachment', 'crb_image', __('Image', 'crb')),
		))
	->add_tab(__('Header Options','crb'),array(
			Carbon_Field::factory('text', 'crb_title', 'Title')
				->help_text(__('You can use (*text*) to make a red word.', 'crb')),
			Carbon_Field::factory('text', 'crb_bar_text', 'Callout text'),
			Carbon_Field::factory('text', 'crb_bar_btn_text', 'Callout Button text'),
			Carbon_Field::factory('text', 'crb_bar_link', 'Callout link'),
		))
	->add_tab(__('Left Column', 'crb'), array(
			Carbon_Field::factory('text', 'crb_left_column_title', __('Title', 'crb')),
			Carbon_Field::factory('rich_text', 'crb_left_column_content', __('Text', 'crb')),
		))
	->add_tab(__('Right Column', 'crb'), array(
			Carbon_Field::factory('text', 'crb_right_column_title', __('Title', 'crb')),
			Carbon_Field::factory('rich_text', 'crb_right_column_content', __('Text', 'crb')),
			Carbon_Field::factory('text', 'crb_right_column_btn_text', __('Button text', 'crb')),
			Carbon_Field::factory('text', 'crb_right_column_link', __('Button Link', 'crb')),
		))
	->add_tab(__('Gallery', 'crb'), array(
			Carbon_Field::factory('complex', 'crb_gallery', '')
			->setup_labels($image_labels)
			->add_fields(array(
				Carbon_Field::factory('attachment', 'crb_gallery_image', __('Image', 'crb'))
					->set_required(true),
				Carbon_Field::factory('text', 'crb_gallery_image_link', __('Link', 'crb'))
					->set_required(true),
			)),
		))
	->add_tab(__('Includes', 'crb'), array(
			Carbon_Field::factory('text', 'crb_includes_title', __('Title', 'crb')),
			Carbon_Field::factory('complex', 'crb_includes', '')
			->setup_labels($icon_labels)
			->add_fields(array(
				Carbon_Field::factory('select', 'crb_include', __('Icon Type', 'crb'))
					->set_options($includes),
				Carbon_Field::factory('text', 'crb_include_name', __('Name', 'crb')),
				Carbon_Field::factory('text', 'crb_include_link', __('link', 'crb'))
					->set_required(true),
			)),
		))
	->add_tab(__('Bottom section','crb'),array(
			Carbon_Field::factory('text', 'crb_section_title', __('Title', 'crb')),
			Carbon_Field::factory('text', 'crb_section_btn_text', __('Button Text', 'crb')),
			Carbon_Field::factory('text', 'crb_section_link', __('Link', 'crb')),
	));

Carbon_Container::factory('custom_fields', __('Destininations settings', 'crb'))
	->show_on_post_type('page')
	->show_on_template('templates/destination.php')
	->add_tab(__('Bottom section','crb'),array(
			Carbon_Field::factory('text', 'crb_section_title', __('Title', 'crb')),
			Carbon_Field::factory('text', 'crb_section_btn_text', __('Button Text', 'crb')),
			Carbon_Field::factory('text', 'crb_section_link', __('Link', 'crb')),
		));


Carbon_Container::factory('custom_fields', __('Template settings', 'crb'))
	->show_on_post_type('page')
	->show_on_template('templates/centered.php')
	->add_tab(__('Steps','crb'),array(
		Carbon_Field::factory('text', 'crb_steps_title', __('Title', 'crb')),
		Carbon_Field::factory('complex', 'crb_steps', 'Steps')
		->setup_labels($step_labels)
		->add_fields(array(
			Carbon_Field::factory('text', 'crb_step_nav_title', __('Navigation Title', 'crb')),
			Carbon_Field::factory('text', 'crb_step_title', __('Title', 'crb')),
			Carbon_Field::factory('rich_text', 'crb_step_desc', __('Text', 'crb'))
				->set_required(true),
		))
	))
	->add_tab(__('Gallery', 'crb'), array(
		Carbon_Field::factory('complex', 'crb_gallery', '')
		->setup_labels($image_labels)
		->add_fields(array(
			Carbon_Field::factory('attachment', 'crb_gallery_image', __('Image', 'crb'))
				->set_required(true),
			Carbon_Field::factory('text', 'crb_gallery_image_link', __('Link', 'crb'))
				->set_required(true),
		)),
	))
	->add_tab(__('Includes', 'crb'), array(
		Carbon_Field::factory('text', 'crb_includes_title', __('Title', 'crb')),
		Carbon_Field::factory('complex', 'crb_includes', '')
		->setup_labels($icon_labels)
		->add_fields(array(
			Carbon_Field::factory('select', 'crb_include', __('Icon Type', 'crb'))
				->set_options($includes),
			Carbon_Field::factory('text', 'crb_include_name', __('Name', 'crb')),
			Carbon_Field::factory('text', 'crb_include_link', __('link', 'crb'))
				->set_required(true),
		)),
	))
	->add_tab(__('Bottom section','crb'),array(
		Carbon_Field::factory('text', 'crb_bottom_section_title', __('Title', 'crb')),
		Carbon_Field::factory('text', 'crb_bottom_section_btn_text', __('Button Text', 'crb')),
		Carbon_Field::factory('text', 'crb_bottom_section_link', __('Link', 'crb')),
	));
Carbon_Container::factory('custom_fields', __('Gallery settings', 'crb'))
	->show_on_post_type('page')
	->show_on_template('templates/gallery.php')
	->add_tab(__('Main Settings','crb'),array(
			Carbon_Field::factory('text', 'crb_gallery_title', __('Title', 'crb'))
				->help_text(__('You can use (*text*) to make a red word.', 'crb')),
		))
	->add_tab(__('Tours','crb'),array(
			Carbon_Field::factory('complex', 'crb_tours', '')
				->setup_labels($tour_labels)
				->add_fields(array(
					Carbon_Field::factory('text', 'crb_tour', __('Title', 'crb')),
					Carbon_Field::factory('text', 'crb_tour_link', __('Link', 'crb'))
						->set_required(true),
					Carbon_Field::factory('attachment', 'crb_tour_image', __('Image', 'crb')),
			))
	))
	->add_tab(__('Secondary Content','crb'),array(
		Carbon_Field::factory('text', 'crb_second_title', __('Title', 'crb')),
		Carbon_Field::factory('rich_text', 'crb_second_content', __('Content', 'crb')),
		Carbon_Field::factory('text', 'crb_btn_text', __('Button text', 'crb')),
		Carbon_Field::factory('text', 'crb_btn_link', __('Button link', 'crb')),
	))
	->add_tab(__('Bottom section','crb'),array(
		Carbon_Field::factory('text', 'crb_gallery_bottom_section_title', __('Title', 'crb')),
		Carbon_Field::factory('text', 'crb_gallery_bottom_section_btn_text', __('Button Text', 'crb')),
		Carbon_Field::factory('text', 'crb_gallery_bottom_section_link', __('Link', 'crb')),
	));