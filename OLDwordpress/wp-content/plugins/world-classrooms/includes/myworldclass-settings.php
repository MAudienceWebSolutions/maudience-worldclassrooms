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
			'users.php',
			'Profile Settings',
			'Profile Settings',
			'read',
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
	<h2>Profile Settings</h2>

<?php

		if ( isset( $_GET['settings-updated'] ) )
			echo '<div class="updated"><p>Settings Updated</p></div>';

?>

	<p>Here you can edit your user profile settings.</p>
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
		</table>
		
		<h3>Partial Payments</h3>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="paypal-email">PayPal Email Address</label></th>
				<td><input type="text" class="regular-text" name="world_classroom_prefs[paypal][email]" id="paypal-email" value="<?php echo $prefs['paypal']['email']; ?>" /></td>
			</tr>
			<tr>
				<th scope="row">Sandbox Mode</th>
				<td><label for="paypal-sandbox"><input type="checkbox"<?php checked( $prefs['paypal']['sandbox'], 1 ); ?> name="world_classroom_prefs[paypal][sandbox]" id="paypal-sandbox" value="1" /> Enable "Sandbox" mode for test payments.</label></td>
			</tr>
			<tr>
				<th scope="row"><label for="paypal-email">Item Description</label></th>
				<td><input type="text" class="regular-text" name="world_classroom_prefs[paypal][item]" id="paypal-item" value="<?php echo $prefs['paypal']['item']; ?>" /><br /><span class="description">This is used on the PayPal checkout page describing what the user is paying for.</span></td>
			</tr>
			<tr>
				<th scope="row"><label for="paypal-email">Minimum</label></th>
				<td>$ <input type="text" size="4" name="world_classroom_prefs[paypal][min]" id="paypal-min" value="<?php echo $prefs['paypal']['min']; ?>" /><br /><span class="description">The minimum amount to accept for partial payments.</span>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Update Settings', 'myworldclass' ), 'primary large', 'submit' ); ?>

		<h3>Shortcodes</h3>
		<table class="form-table">
			<tr>
				<th scope="row">View Profile</th>
				<td><code>[myworldclass_profile]</code></td>
			</tr>
			<tr>
				<th scope="row">Edit Profile</th>
				<td><code>[myworldclass_edit_profile]</code></td>
			</tr>
			<tr>
				<th scope="row">Partial Payments</th>
				<td><code>[myworldclass_partial_payment]</code></td>
			</tr>
			<tr>
				<th scope="row">Payment History</th>
				<td><code>[myworldclass_payment_history]</code></td>
			</tr>
		</table>
	</form>
</div>
<?php
	}
endif;

/**
 * Sanitize Plugin Settings
 * @since 1.0
 * @version 1.0
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

			$new_settings['paypal'] = array(
				'email' => sanitize_text_field( $_POST['world_classroom_prefs']['paypal']['email'] ),
				'item'  => sanitize_text_field( $_POST['world_classroom_prefs']['paypal']['item'] ),
				'min'   => abs( $_POST['world_classroom_prefs']['paypal']['min'] )
			);

			if ( ! isset( $_POST['world_classroom_prefs']['paypal']['sandbox'] ) )
				$new_settings['paypal']['sandbox'] = 0;
			else
				$new_settings['paypal']['sandbox'] = 1;

			update_option( 'world_classroom_prefs', $new_settings );

		}
		return $prefs;

	}
endif;


?>