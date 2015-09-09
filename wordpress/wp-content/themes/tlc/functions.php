<?php

function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function theme_init_theme() {

	# Enqueue jQuery

	wp_enqueue_script('jquery');

	

	# Enqueue Custom Scripts

	# @wp_enqueue_script attributes -- id, location, dependancies, version

	//wp_enqueue_script('custom-script', get_bloginfo('stylesheet_directory') . '/js/custom-script.js', array('jquery'), '1.0');

}

add_action('init', 'theme_init_theme');

add_action('after_setup_theme', 'theme_setup_theme');



# To override theme setup process in a child theme, add your own theme_setup_theme() to your child theme's

# functions.php file.

if ( ! function_exists( 'theme_setup_theme' ) ):

function theme_setup_theme() {

	include_once('lib/common.php');



	# Theme supports

	add_theme_support('automatic-feed-links');

	

	# Manually select Post Formats to be supported - http://codex.wordpress.org/Post_Formats

	// add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );



	# Register Theme Menu Locations



	add_theme_support('menus');

	register_nav_menus(array(

		'main-menu'=>__('Main Menu'),

	));





	# Register CPTs

	include_once('options/theme-post-types.php');

	

	# Attach custom widgets

	include_once('lib/custom-widgets/widgets.php');

	include_once('options/theme-widgets.php');

	

	# Add Actions

	add_action('widgets_init', 'theme_widgets_init');

	add_action('wp_loaded', 'attach_theme_options');

	add_action('wp_head', '_print_ie6_styles');



	# Add Filters

	

}

endif;



# Register Sidebars

# Note: In a child theme with custom theme_setup_theme() this function is not hooked to widgets_init

function theme_widgets_init() {

	register_sidebar(array(

		'name' => 'Footer Widgetized Area',

		'id' => 'footer-sidebar',

		'before_widget' => '<div id="%1$s" class="widget %2$s">',

		'after_widget' => '</div>',

		'before_title' => '<h3 class="widgettitle">',

		'after_title' => '</h3>',

	));
	
	register_sidebar( array(
		'name' => __( 'Side Sidebar', 'magazino' ),
		'id' => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );

}



function attach_theme_options() {

	# Attach theme options

	include_once('lib/theme-options/theme-options.php');

	

	include_once('options/theme-options.php');

	// include_once('options/other-options.php');

	

	# Theme Help needs to be after options/theme-options.php

	include_once('lib/theme-options/theme-readme.php');

	

	# Attach ECFs

	include_once('lib/enhanced-custom-fields/enhanced-custom-fields.php');

	include_once('options/theme-custom-fields.php');

}



/* Custom code goes below this line. */



function custom_excerpt_length( $length ) {

	return 25;

}

add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );



function new_excerpt_more($more) {

    global $post;

	return '<a href="'. get_permalink($post->ID) . '">... read more</a>';

}

add_filter('excerpt_more', 'new_excerpt_more');





/* Custom code goes above this line. */

?>