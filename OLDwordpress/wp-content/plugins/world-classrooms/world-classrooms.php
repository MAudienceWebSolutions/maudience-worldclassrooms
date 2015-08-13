<?php
/**
 * Plugin Name: World Classrooms
 * Plugin URI: http://www.myworldclassrooms.com/
 * Description: Customizations to allow users to have a front-end profile and payment balance / history.
 * Version: 1.0.1
 * Tags: balance, user, profile, world classrooms
 * Author: Gabriel S Merovingi
 * Author URI: http://www.merovingi.com
 * Author Email: info@merovingi.com
 * Requires at least: WP 3.9
 * Tested up to: WP 3.9.2
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
define( 'MYWORLDCLASS_VERSION',     '1.0.1' );
define( 'MYWORLDCLASS',             __FILE__ );
define( 'MYWORLDCLASS_ROOT',        plugin_dir_path( MYWORLDCLASS ) );
define( 'MYWORLDCLASS_CLASSES_DIR', MYWORLDCLASS_ROOT . 'classes/' );
define( 'MYWORLDCLASS_INC_DIR',     MYWORLDCLASS_ROOT . 'includes/' );

/**
 * Load Required Files
 */
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-functions.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-tours.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-tour-payments.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-shortcodes.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-settings.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-admin.php' );
require_once( MYWORLDCLASS_INC_DIR . 'myworldclass-gravity.php' );

require_once( MYWORLDCLASS_CLASSES_DIR . 'myworldclass-student.php' );
require_once( MYWORLDCLASS_CLASSES_DIR . 'myworldclass-tour.php' );
require_once( MYWORLDCLASS_CLASSES_DIR . 'myworldclass-payments.php' );

/**
 * Load Translation
 * @since 1.0
 * @version 1.0
 */
add_action( 'plugins_loaded', 'mywclass_load_translation' );
if ( ! function_exists( 'mywclass_load_translation' ) ) :
	function mywclass_load_translation() {

		// Load Translation
		$locale = apply_filters( 'plugin_locale', get_locale(), 'myworldclass' );
		load_textdomain( 'myworldclass', WP_LANG_DIR . "/world-classrooms/world-classrooms-$locale.mo" );
		load_plugin_textdomain( 'myworldclass', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		//add_action( 'gform_paypalpaymentspro_post_capture',               'mywclass_gravity_paypal_payments', 1, 3 );
		//add_action( 'gform_paypalpaymentspro_after_subscription_payment', 'mywclass_gravity_paypal_subscription', 1, 3 );
		add_action( 'gform_authorizenet_post_capture',               'mywclass_gravity_authorizenet_payments', 1, 3 );
		add_action( 'gform_authorizenet_after_subscription_payment', 'mywclass_gravity_authorizenet_subscription', 1, 3 );
		add_action( 'gform_user_registered', 'mywclass_capture_user_creation', 999, 4 );

	}
endif;

/**
 * Init Plugin
 * @since 1.0
 * @version 1.0
 */
add_action( 'init', 'mywclass_init_plugin' );
if ( ! function_exists( 'mywclass_init_plugin' ) ) :
	function mywclass_init_plugin() {

		mywclass_register_tour_type();
		mywclass_register_tour_payment_type();
		mywclass_handle_paypal_callbacks();

		add_shortcode( 'myworldclass_profile',         'mywclass_render_my_account_shortcode' );
		add_shortcode( 'myworldclass_edit_profile',    'mywclass_render_edit_my_account_shortcode' );
		add_shortcode( 'myworldclass_payment_history', 'mywclass_render_my_payment_history_shortcode' );
		add_shortcode( 'myworldclass_partial_payment', 'mywclass_render_partial_payment_shortcode' );

		add_action( 'admin_menu', 'mywclass_add_plugin_settings_page' );
		add_action( 'wp_enqueue_scripts', 'mywclass_enqueue_front_scripts' );
		add_action( 'template_redirect',  'mywclass_front_end_profile_updates' );
		add_filter( 'login_redirect',     'mywclass_login_redirect', 90, 3 );
		add_filter( 'user_contactmethods', 'mywclass_add_custom_contact_methods' );

	}
endif;

/**
 * Admin Init Plugin
 * @since 1.0
 * @version 1.0
 */
add_action( 'admin_init', 'mywclass_admin_init_plugin' );
if ( ! function_exists( 'mywclass_admin_init_plugin' ) ) :
	function mywclass_admin_init_plugin() {

		register_setting( 'world_classroom_prefs', 'mywclass-settings', 'mywclass_sanitize_plugin_settings' );

		add_filter( 'manage_users_columns',       'mywclass_user_column_headers' );
		add_action( 'manage_users_custom_column', 'mywclass_user_column_content', 1, 3 );

		add_action( 'personal_options', 'mywclass_admin_user_profile_edit' );
		add_action( 'personal_options_update',  'mywclass_save_user_change_in_admin' );
		add_action( 'edit_user_profile_update', 'mywclass_save_user_change_in_admin' );

		add_filter( 'pre_user_query',        'mywclass_filter_users_in_admin' );
		add_action( 'restrict_manage_users', 'mywclass_filter_users_in_admin_options' );

		add_filter( 'manage_tour_posts_columns',       'mywclass_tour_column_headers' );
		add_action( 'manage_tour_posts_custom_column', 'mywclass_tour_column_content', 10, 2 );

		add_filter( 'page_row_actions',           'mywclass_tour_row_actions', 10, 2 );
		add_filter( 'post_updated_messages',      'mywclass_tour_update_messages' );

		add_action( 'add_meta_boxes_tour', 'mywclass_tour_meta_boxes' );
		add_action( 'save_post',           'mywclass_tour_save_details' );

		add_filter( 'manage_tour_payment_posts_columns',       'mywclass_tour_payment_column_headers' );
		add_action( 'manage_tour_payment_posts_custom_column', 'mywclass_tour_payment_column_content', 10, 2 );

		add_filter( 'parse_query',                    'mywclass_filter_tour_payments' );
		add_action( 'restrict_manage_posts',          'mywclass_filter_tour_payments_option' );
		add_filter( 'bulk_actions-edit-tour_payment', '__return_empty_array' );
		add_filter( 'page_row_actions',               'mywclass_tour_payment_row_actions', 20, 2 );
		add_filter( 'post_updated_messages',          'mywclass_tour_payment_update_messages' );

	}
endif;

?>