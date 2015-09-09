<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Add Settings Page
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_add_plugin_settings_page' ) ) :
	function mywclass_add_plugin_settings_page() {

		add_submenu_page(
			'edit.php?post_type=tour',
			'Settings',
			'Settings',
			'edit_users',
			'mywclass-settings',
			'mywclass_plugin_settings_screen'
		);

	}
endif;

/**
 * Settings Page
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_plugin_settings_screen' ) ) :
	function mywclass_plugin_settings_screen() {

		// Security
		if ( ! current_user_can( 'edit_users' ) )
			wp_die( __( 'Access Denied', 'myworldclass' ) );

		$prefs = mywclass_get_settings();

?>
<div class="wrap list" id="mywclass-settings">
	<h2>Tour Settings</h2>
<?php

		if ( isset( $_GET['settings-updated'] ) )
			echo '<div class="updated"><p>Settings Updated</p></div>';

?>
	<form method="post" action="options.php">

		<?php settings_fields( 'world_classroom_prefs' ); ?>

		<h3>General</h3>
		<table class="form-table">
			<tr>
				<th scope="row">Login Redirect</th>
				<td><label for="redirect_login"><input type="checkbox"<?php checked( $prefs['redirect_login'], 1 ); ?> name="world_classroom_prefs[redirect_login]" id="redirect_login" value="1" /> Redirect non administrator logins to the "My Account" page.</label></td>
			</tr>
			<tr>
				<th scope="row"><label for="my_account_page_id">My Account Page</label></th>
				<td><?php wp_dropdown_pages( array(
				
					'selected' => $prefs['my_account_page_id'],
					'name'     => 'world_classroom_prefs[my_account_page_id]',
					'id'       => 'my_account_page_id'
				
				) ); ?></td>
			</tr>
			<tr>
				<th scope="row"><label for="booking-legal-pdf">Booking Conditions PDF</label></th>
				<td><input type="text" class="regular-text" name="world_classroom_prefs[booking_legal]" id="booking-legal-pdf" value="<?php echo $prefs['booking_legal']; ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="terms-legal-pdf">Terms & Conditions PDF</label></th>
				<td><input type="text" class="regular-text" name="world_classroom_prefs[terms_legal]" id="terms-legal-pdf" value="<?php echo $prefs['terms_legal']; ?>" /></td>
			</tr>
		</table>

		<h3>Authorize.net</h3>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="authorizenet-mode">Mode</label></th>
				<td>
					<select name="world_classroom_prefs[authorizenet][mode]" id="authorizenet-mode"><?php

			$options = array(
				'test' => 'Test Mode',
				'live' => 'Live Mode'
			);
			foreach ( $options as $value => $label ) {
				echo '<option value="' . $value . '"';
				if ( $prefs['authorizenet']['mode'] == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

					?></select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="authorize-live-api">API ID</label></th>
				<td><input type="text" class="regular-text" name="world_classroom_prefs[authorizenet][live_api]" id="authorize-live-api" value="<?php echo $prefs['authorizenet']['live_api']; ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="authorize-live-key">API Key</label></th>
				<td><input type="text" class="regular-text" name="world_classroom_prefs[authorizenet][live_key]" id="authorize-live-key" value="<?php echo $prefs['authorizenet']['live_key']; ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="authorize-test-api">TEST API ID</label></th>
				<td><input type="text" class="regular-text" name="world_classroom_prefs[authorizenet][test_api]" id="authorize-test-api" value="<?php echo $prefs['authorizenet']['test_api']; ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="authorize-test-key">TEST API Key</label></th>
				<td><input type="text" class="regular-text" name="world_classroom_prefs[authorizenet][test_key]" id="authorize-test-key" value="<?php echo $prefs['authorizenet']['test_key']; ?>" /></td>
			</tr>
		</table>

		<h3>Common Downloads</h3>
		<p>This content is shown in the "Downloads" section of all user profiles.</p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="tourscommondownload">Content</label></th>
				<td>
					<?php wp_editor( $prefs['common_downloads'], 'tourscommondownload', array( 'textarea_name' => 'world_classroom_prefs[common_downloads]', 'textarea_rows' => 15 ) ); ?>
				</td>
			</tr>
		</table>

		<h3>Email Templates</h3>
		<h4>Tour Enrollments</h4>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="email-template-enrolment-subject">Email Subject</label></th>
				<td>
					<input type="text" class="regular-text" name="world_classroom_prefs[emails][enrolment][subject]" id="email-template-enrolment-subject" value="<?php echo esc_attr( $prefs['emails']['enrolment']['subject'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="email-template-enrolment-body">Email Template</label></th>
				<td>
					<textarea name="world_classroom_prefs[emails][enrolment][body]" id="email-template-enrolment-body" class="regular-text code widefat" rows="10"><?php echo esc_attr( stripslashes( $prefs['emails']['enrolment']['body'] ) ); ?></textarea>
				</td>
			</tr>
		</table>
		<h4>Account Password Change</h4>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="email-template-password-subject">Email Subject</label></th>
				<td>
					<input type="text" class="regular-text" name="world_classroom_prefs[emails][password][subject]" id="email-template-password-subject" value="<?php echo esc_attr( $prefs['emails']['password']['subject'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="email-template-password-body">Email Template</label></th>
				<td>
					<textarea name="world_classroom_prefs[emails][password][body]" id="email-template-password-body" class="regular-text code widefat" rows="10"><?php echo esc_attr( stripslashes( $prefs['emails']['password']['body'] ) ); ?></textarea>
				</td>
			</tr>
		</table>
		<h4>Payment Reminder Manual Plan</h4>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="email-template-payremind-subject">Email Subject</label></th>
				<td>
					<input type="text" class="regular-text" name="world_classroom_prefs[emails][payremind][subject]" id="email-template-payremind-subject" value="<?php echo esc_attr( stripslashes( $prefs['emails']['payremind']['subject'] ) ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="email-template-payremind-body">Email Template</label></th>
				<td>
					<textarea name="world_classroom_prefs[emails][payremind][body]" id="email-template-payremind-body" class="regular-text code widefat" rows="10"><?php echo esc_attr( stripslashes( $prefs['emails']['payremind']['body'] ) ); ?></textarea>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Update Settings', 'myworldclass' ), 'primary large', 'submit' ); ?>
	</form>
</div>
<?php
	}
endif;

/**
 * Sanitize Plugin Settings
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'mywclass_sanitize_plugin_settings' ) ) :
	function mywclass_sanitize_plugin_settings( $prefs = '' ) {

		if ( isset( $_POST['world_classroom_prefs'] ) ) {

			$new_settings = array();
			if ( ! isset( $_POST['world_classroom_prefs']['redirect_login'] ) )
				$new_settings['redirect_login'] = 0;
			else
				$new_settings['redirect_login'] = 1;

			$new_settings['my_account_page_id'] = absint( $_POST['world_classroom_prefs']['my_account_page_id'] );

			$new_settings['authorizenet'] = array(
				'mode'     => sanitize_text_field( $_POST['world_classroom_prefs']['authorizenet']['mode'] ),
				'live_api' => sanitize_text_field( $_POST['world_classroom_prefs']['authorizenet']['live_api'] ),
				'live_key' => sanitize_text_field( $_POST['world_classroom_prefs']['authorizenet']['live_key'] ),
				'test_api' => sanitize_text_field( $_POST['world_classroom_prefs']['authorizenet']['test_api'] ),
				'test_key' => sanitize_text_field( $_POST['world_classroom_prefs']['authorizenet']['test_key'] )
			);

			$new_settings['booking_legal'] = sanitize_text_field( $_POST['world_classroom_prefs']['booking_legal'] );
			$new_settings['terms_legal']   = sanitize_text_field( $_POST['world_classroom_prefs']['terms_legal'] );

			$new_settings['common_downloads'] = stripslashes( $_POST['world_classroom_prefs']['common_downloads'] );
			$new_settings['emails'] = $_POST['world_classroom_prefs']['emails'];

			update_option( 'world_classroom_prefs', $new_settings );

		}
		return $prefs;

	}
endif;


?>