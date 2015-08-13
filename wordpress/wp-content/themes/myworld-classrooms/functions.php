<?php
define('CRB_THEME_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

# Load the debug functions early so they're available for all theme code
include_once(CRB_THEME_DIR . 'lib/debug.php');

# Enqueue JS and CSS assets on the front-end
add_action('wp_enqueue_scripts', 'crb_wp_enqueue_scripts');
function crb_wp_enqueue_scripts() {
	$template_dir = get_template_directory_uri();

	# Enqueue jQuery
	wp_enqueue_script('jquery');

	# Enqueue Custom JS files
	# @crb_enqueue_script attributes -- id, location, dependencies, in_footer = false
	crb_enqueue_script('theme-functions', $template_dir . '/js/functions.js', array('jquery'));

	# Enqueue Custom CSS files
	# @crb_enqueue_style attributes -- id, location, dependencies, media = all
	crb_enqueue_style('theme-googlefonts', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800');
	crb_enqueue_style('theme-formreset', $template_dir . '/css/formreset.min.css');
	crb_enqueue_style('theme-readyclass', $template_dir . '/css/readyclass.min.css');
	crb_enqueue_style('theme-formsmain', $template_dir . '/css/formsmain.min.css');
	crb_enqueue_style('theme-styles', $template_dir . '/style.css');

	# Enqueue Comments JS file
	if (is_singular()) {
		wp_enqueue_script('comment-reply');
	}
}

# Enqueue JS and CSS assets on admin pages
add_action('admin_enqueue_scripts', 'crb_admin_enqueue_scripts');
function crb_admin_enqueue_scripts() {
	$template_dir = get_template_directory_uri();

	# Enqueue Scripts
	# @crb_enqueue_script attributes -- id, location, dependencies, in_footer = false
	# crb_enqueue_script('theme-admin-functions', $template_dir . '/js/admin-functions.js', array('jquery'));
	
	# Enqueue Styles
	# @crb_enqueue_style attributes -- id, location, dependencies, media = all
	# crb_enqueue_style('theme-admin-styles', $template_dir . '/css/admin-style.css');
}

# Attach Custom Post Types and Custom Taxonomies
add_action('init', 'crb_attach_post_types_and_taxonomies', 0);
function crb_attach_post_types_and_taxonomies() {
	# Attach Custom Post Types
	include_once(CRB_THEME_DIR . 'options/post-types.php');

	# Attach Custom Taxonomies
	include_once(CRB_THEME_DIR . 'options/taxonomies.php');
}

add_action('after_setup_theme', 'crb_setup_theme');

# To override theme setup process in a child theme, add your own crb_setup_theme() to your child theme's
# functions.php file.
if (!function_exists('crb_setup_theme')) {
	function crb_setup_theme() {
		# Make this theme available for translation.
		load_theme_textdomain( 'crb', get_template_directory() . '/languages' );

		# Common libraries
		include_once(CRB_THEME_DIR . 'lib/common.php');
		include_once(CRB_THEME_DIR . 'lib/carbon-fields/carbon-fields.php');
		include_once(CRB_THEME_DIR . 'lib/carbon-validator/carbon-validator.php');
		include_once(CRB_THEME_DIR . 'lib/admin-column-manager/carbon-admin-columns-manager.php');

		# Additional libraries and includes
		include_once(CRB_THEME_DIR . 'includes/comments.php');
		include_once(CRB_THEME_DIR . 'includes/title.php');
		include_once(CRB_THEME_DIR . 'includes/gravity-forms.php');
		
		# Theme supports
		add_theme_support('automatic-feed-links');
		add_theme_support('post-thumbnails');
		add_theme_support('title-tag');
		add_theme_support('menus');

		# Manually select Post Formats to be supported - http://codex.wordpress.org/Post_Formats
		// add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

		# Register Theme Menu Locations
		register_nav_menus(array(
			'main-menu'=>__('Main Menu', 'crb'),
			'navigation'=>__('Navigation Menu', 'crb'),
			'footer-menu-1'=>__('Footer menu 1', 'crb'),
			'footer-menu-2'=>__('Footer menu 2', 'crb'),
			'footer-menu-3'=>__('Footer menu 3', 'crb'),
		));
		
		# Attach custom widgets
		include_once(CRB_THEME_DIR . 'options/widgets.php');

		# Attach custom shortcodes
		include_once(CRB_THEME_DIR . 'options/shortcodes.php');

		# Add image sizes
		add_image_size('soc', 25, 25, 0);
		add_image_size('world_class_icon', 110, 77, 0);
		add_image_size('tours', 187, 186, 0);
		add_image_size('tours-gallery', 216, 216, 0);
		add_image_size('callout_gallery', 0, 250, 0);
		add_image_size('banner', 1200, 73, 1);
		add_image_size('about', 481, 323, 0);
		add_image_size('about-list', 299, 222, 0);
		add_image_size('destination', 301, 201, 1);
		add_image_size('destination-gallery', 0, 250, 0);

		# Attach custom columns
		include_once(CRB_THEME_DIR . 'options/admin-columns.php');
		
		# Add Actions
		add_action('widgets_init', 'crb_widgets_init');

		add_action('carbon_register_fields', 'crb_attach_theme_options');
		add_action('carbon_after_register_fields', 'crb_attach_theme_help');

		# Add Filters
		add_filter('excerpt_more', 'crb_excerpt_more');
		add_filter('excerpt_length', 'crb_excerpt_length', 999);
	}
}

# Register Sidebars
# Note: In a child theme with custom crb_setup_theme() this function is not hooked to widgets_init
function crb_widgets_init() {
	$sidebar_options = array_merge(crb_get_default_sidebar_options(), array(
		'name' => 'Default Sidebar',
		'id'   => 'default-sidebar',
	));
	
	register_sidebar($sidebar_options);
}

# Sidebar Options
function crb_get_default_sidebar_options() {
	return array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	);
}

function crb_attach_theme_options() {
	# Attach fields
	include_once(CRB_THEME_DIR . 'options/theme-options.php');
	include_once(CRB_THEME_DIR . 'options/custom-fields.php');
}

function crb_attach_theme_help() {
	# Theme Help needs to be after options/theme-options.php
	include_once(CRB_THEME_DIR . 'lib/theme-help/theme-readme.php');
}

function crb_excerpt_more() {
	return '...';
}

function crb_excerpt_length() {
	return 55;
}

function crb_colorize_word($title) {
	$title = str_replace('(*', '<span>', $title);
	$title = str_replace('*)', '</span>', $title);

	return $title;
}
	