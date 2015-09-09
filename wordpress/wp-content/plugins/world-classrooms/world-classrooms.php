<?php
/**
 * Plugin Name: World Classrooms
 * Plugin URI: http://www.myworldclassrooms.com/
 * Description: Customizations to allow users to have a front-end profile and payment balance / history.
 * Version: 1.1
 * Tags: balance, user, profile, world classrooms
 * Author: Gabriel S Merovingi
 * Author URI: http://www.merovingi.com
 * Author Email: info@merovingi.com
 * Requires at least: WP 3.9
 * Tested up to: WP 4.3
 * Text Domain: myworldclass
 * Domain Path: /lang
 * License: Copyrighted
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
define( 'MYWORLDCLASS_VERSION',       '1.1' );
define( 'MYWORLDCLASS',               __FILE__ );
define( 'MYWORLDCLASS_ROOT',          plugin_dir_path( MYWORLDCLASS ) );
define( 'MYWORLDCLASS_CLASSES_DIR',   MYWORLDCLASS_ROOT . 'classes/' );
define( 'MYWORLDCLASS_INC_DIR',       MYWORLDCLASS_ROOT . 'includes/' );
define( 'MYWORLDCLASS_TEMPLATES_DIR', MYWORLDCLASS_ROOT . 'templates/' );
define( 'MYWORLDCLASS_GATEWAYS_DIR',  MYWORLDCLASS_ROOT . 'gateways/' );

/**
 * Load Required Files
 */
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-functions.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-tours.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-shortcodes.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-settings.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-paymens.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-ajax.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-cron.php' );

require_once( MYWORLDCLASS_INC_DIR . 'admin/myworldclass-users.php' );
require_once( MYWORLDCLASS_INC_DIR . 'admin/myworldclass-tour-payments.php' );
require_once( MYWORLDCLASS_INC_DIR . 'admin/myworldclass-tour-signups.php' );

require_once( MYWORLDCLASS_GATEWAYS_DIR . 'authorize-net/myworldclass-authorize-net.php' );

require_once( MYWORLDCLASS_CLASSES_DIR . 'myworldclass-student.php' );
require_once( MYWORLDCLASS_CLASSES_DIR . 'myworldclass-tour.php' );
require_once( MYWORLDCLASS_CLASSES_DIR . 'myworldclass-payments.php' );
require_once( MYWORLDCLASS_CLASSES_DIR . 'myworldclass-query-signups.php' );

require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-plugin.php' );

register_activation_hook(   MYWORLDCLASS, 'mywclass_mycars_plugin_activation' );
register_deactivation_hook( MYWORLDCLASS, 'mywclass_mycars_plugin_deactivation' );

/**
 * Load Translation
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_load_translation' ) ) :
	function mywclass_load_translation() {

		// Load Translation
		$locale = apply_filters( 'plugin_locale', get_locale(), 'myworldclass' );
		load_textdomain( 'myworldclass', WP_LANG_DIR . "/world-classrooms/world-classrooms-$locale.mo" );
		load_plugin_textdomain( 'myworldclass', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		add_filter( 'wp_nav_menu_items', 'mywclass_theme_top_menu_items', 10, 2 );
		add_filter( 'authenticate',      'mywclass_allow_email_login', 20, 3 );

	}
endif;
add_action( 'plugins_loaded', 'mywclass_load_translation' );

/**
 * Init Plugin
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_init_plugin' ) ) :
	function mywclass_init_plugin() {

		global $myworldclass_cloning;

		$myworldclass_cloning = false;

		mywclass_register_tour_type();
		mywclass_register_tour_payment_type();

		add_shortcode( 'myworldclass_profile',                  'mywclass_render_my_account_shortcode' );
		add_shortcode( 'myworldclass_edit_profile',             'mywclass_render_edit_my_account_shortcode' );
		add_shortcode( 'myworldclass_payment_history',          'mywclass_render_my_payment_history_shortcode' );
		add_shortcode( 'myworldclass_partial_payment',          'mywclass_render_partial_payment_shortcode' );
		add_shortcode( 'myworld_find_tour_form',                'mywclass_render_find_tour_form' );

		add_filter( 'enter_title_here',                         'mywclass_change_title_text' );
		add_action( 'wp_head',                                  'mywclass_wp_head' );
		add_action( 'login_enqueue_scripts',                    'mywclass_login_header' );
		add_filter( 'body_class',                               'mywclass_body_classes' );
		add_action( 'admin_menu',                               'mywclass_add_plugin_settings_page', 15 );
		add_action( 'admin_menu',                               'mywclass_add_signup_admin_page', 10 );
		add_action( 'wp_enqueue_scripts',                       'mywclass_enqueue_front_scripts' );
		add_action( 'template_redirect',                        'mywclass_temlate_redirects' );
		add_filter( 'template_include',                         'mywclass_template_includes' );
		add_filter( 'login_redirect',                           'mywclass_login_redirect', 90, 3 );
		add_filter( 'user_contactmethods',                      'mywclass_add_custom_contact_methods' );
		add_action( 'admin_notices',                            'mywclass_clone_admin_notices' );
		add_action( 'admin_enqueue_scripts',                    'mywclass_admin_enqueue' );

		add_action( 'wp_ajax_nopriv_signup-for-tour',           'mywclass_ajax_signup_for_tour' );
		add_action( 'wp_ajax_signup-for-tour',                  'mywclass_ajax_signup_for_tour' );

		add_action( 'wp_ajax_nopriv_validate-scholarship-code', 'mywclass_ajax_validate_scholarship_code' );
		add_action( 'wp_ajax_validate-scholarship-code',        'mywclass_ajax_validate_scholarship_code' );

		add_action( 'wp_ajax_update-my-account',                'mywclass_ajax_update_front_end_profile' );
		add_action( 'wp_ajax_make-manual-payment',              'mywclass_ajax_make_manual_payment' );
		add_action( 'wp_ajax_update-signup-details',            'mywclass_ajax_edit_signup_details' );

		mywclass_catch_tour_search();

		add_action( 'mywclass_hourly_task', 'mywclass_cron_run_hourly_tasks' );
		add_action( 'mywclass_daily_task',  'mywclass_cron_run_daily_tasks' );

		if ( ! wp_next_scheduled( 'mywclass_hourly_task' ) )
			wp_schedule_event( time(), 'hourly', 'mywclass_hourly_task' );

		if ( ! wp_next_scheduled( 'mywclass_daily_task' ) )
			wp_schedule_event( time() + 600, 'daily', 'mywclass_daily_task' );

	}
endif;
add_action( 'init', 'mywclass_init_plugin' );

/**
 * After Theme Setup
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_remove_admin_bar' ) ) :
	function mywclass_remove_admin_bar( $content ) {

		return ( current_user_can( 'administrator' ) ) ? $content : false;

	}
endif;
add_filter( 'show_admin_bar', 'mywclass_remove_admin_bar', 9999 );

/**
 * Admin Init Plugin
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_admin_init_plugin' ) ) :
	function mywclass_admin_init_plugin() {

		// Restrict admin access
		if ( ! is_ajax() && ! current_user_can( 'publish_posts' ) ) {

			wp_safe_redirect( home_url( '/' ) );
			exit;

		}

		myworldclass_clone_post();

		require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-help.php' );

		register_setting( 'world_classroom_prefs', 'mywclass-settings', 'mywclass_sanitize_plugin_settings' );

		add_filter( 'manage_users_columns',                    'mywclass_user_column_headers' );
		add_action( 'manage_users_custom_column',              'mywclass_user_column_content', 1, 3 );

		add_action( 'personal_options',                        'mywclass_admin_user_profile_edit' );
		add_action( 'personal_options_update',                 'mywclass_save_user_change_in_admin' );
		add_action( 'edit_user_profile_update',                'mywclass_save_user_change_in_admin' );

		add_filter( 'pre_user_query',                          'mywclass_filter_users_in_admin' );
		add_action( 'restrict_manage_users',                   'mywclass_filter_users_in_admin_options' );

		add_filter( 'manage_tour_posts_columns',               'mywclass_tour_column_headers' );
		add_action( 'manage_tour_posts_custom_column',         'mywclass_tour_column_content', 10, 2 );

		add_filter( 'page_row_actions',                        'mywclass_tour_row_actions', 10, 2 );
		add_filter( 'post_updated_messages',                   'mywclass_tour_update_messages' );
		add_action( 'save_post',                               'mywclass_tour_save_details' );
		add_action( 'save_post',                               'mywclass_save_tour_payment_details' );

		add_filter( 'manage_tour_payment_posts_columns',       'mywclass_tour_payment_column_headers' );
		add_action( 'manage_tour_payment_posts_custom_column', 'mywclass_tour_payment_column_content', 10, 2 );

		add_filter( 'parse_query',                             'mywclass_filter_tour_payments' );
		add_action( 'restrict_manage_posts',                   'mywclass_filter_tour_payments_option' );
		add_filter( 'bulk_actions-edit-tour_payment',          '__return_empty_array' );
		add_filter( 'page_row_actions',                        'mywclass_tour_payment_row_actions', 20, 2 );
		add_filter( 'post_updated_messages',                   'mywclass_tour_payment_update_messages' );

	}
endif;
add_action( 'admin_init', 'mywclass_admin_init_plugin' );

if ( ! function_exists( 'is_ajax' ) ) :
	function is_ajax() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return true;

		return false;

	}
endif;

?>