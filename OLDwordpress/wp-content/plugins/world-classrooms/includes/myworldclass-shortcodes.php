<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Shortcode: View Profile
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_render_my_account_shortcode' ) ) :
	function mywclass_render_my_account_shortcode( $atts ) {

		if ( ! is_user_logged_in() ) return '<p>' . __( 'Please login to view your profile.', 'myworldclass' ) . '</p>';
		$cui = get_current_user_id();
		$student = new MyWorldClass_Student( $cui );

		$tour_id = mywclass_get_tour_by_code( $student->user->tour_code );
		$tour = new MyWorldClass_Tour( $tour_id );

		ob_start(); ?>

<div id="profile-name"><?php echo $student->display_name(); ?><div class="my-balance-label"><?php _e( 'Total Paid', 'myworldclass' ); ?></div><span><?php echo $student->display_balance(); ?></span></div>
<h3 id="account-type"><?php echo $student->display_user_type(); ?></h3>
<?php echo $tour->display_notice(); ?>
<h2><?php _e( 'Your Profile Details', 'myworldclass' ); ?></h2>
<table class="table" id="profile-details">
	<tr>
		<th scope="row"><?php _e( 'First Name', 'myworldclass' ); ?></th>
		<td><?php echo $student->user->first_name; ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Last Name', 'myworldclass' ); ?></th>
		<td><?php echo $student->user->last_name; ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Email Address', 'myworldclass' ); ?></th>
		<td><?php echo $student->user->user_email; ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Date of Birth', 'myworldclass' ); ?></th>
		<td><?php echo $student->display_dob(); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Parent or Guardians Name', 'myworldclass' ); ?></th>
		<td><?php echo $student->user->parent_name; ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Tour Code', 'myworldclass' ); ?></th>
		<td><?php echo $student->user->tour_code; ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'High School', 'myworldclass' ); ?></th>
		<td><?php echo $student->user->high_school; ?></td>
	</tr>
</table>
<?php

		$content = ob_get_contents();
		ob_end_clean();
		return $content;

	}
endif;

/**
 * Shortcode: Edit Profile
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_render_edit_my_account_shortcode' ) ) :
	function mywclass_render_edit_my_account_shortcode( $atts ) {

		if ( ! is_user_logged_in() ) return '<p>' . __( 'Please login to edit your profile.', 'myworldclass' ) . '</p>';
		$cui = get_current_user_id();
		$student = new MyWorldClass_Student( $cui );

		ob_start(); ?>

<?php if ( isset( $_GET['updated'] ) && $_GET['updated'] == 1 ) : ?>

	<div id="update-notice" class="success"><?php _e( 'Profile details updated', 'myworldclass' ); ?></div>

<?php elseif ( isset( $_GET['updated'] ) && $_GET['updated'] == 0 ) : ?>

	<div id="update-notice" class="error"><?php _e( 'Your profile details contains errors', 'myworldclass' ); ?></div>

<?php endif; ?>

<div id="profile-name"><?php echo $student->display_name(); ?><div class="my-balance-label"><?php _e( 'Total Paid', 'myworldclass' ); ?></div><span><?php echo $student->display_balance(); ?></span></div>
<h3 id="account-type"><a href="<?php echo get_permalink( $student->prefs['my_account_page_id'] ); ?>"><?php _e( 'View Profile', 'myworldclass' ); ?></a></h3>
<h2><?php _e( 'Your Profile Details', 'myworldclass' ); ?></h2>
<form method="post" action="">
	<table class="table" id="profile-details">
		<tr>
			<th scope="row"><?php _e( 'First Name', 'myworldclass' ); ?> <span>*</span></th>
			<td><input type="text" name="myaccount[first_name]" id="myaccount-first_name" value="<?php echo $student->user->first_name; ?>" aria-required="true" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Last Name', 'myworldclass' ); ?> <span>*</span></th>
			<td><input type="text" name="myaccount[last_name]" id="myaccount-last_name" value="<?php echo $student->user->last_name; ?>" aria-required="true" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Email Address', 'myworldclass' ); ?> <span>*</span></th>
			<td><input type="text" name="myaccount[user_email]" id="myaccount-user_email" value="<?php echo $student->user->user_email; ?>" aria-required="true" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Phone', 'myworldclass' ); ?> <span>*</span></th>
			<td><input type="text" name="myaccount[user_phone]" class="short" id="myaccount-user_phone" value="<?php echo $student->user->user_phone; ?>" aria-required="true" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Date of Birth', 'myworldclass' ); ?> <span>*</span></th>
			<td><input type="date" name="myaccount[user_dob]" class="short" id="myaccount-user_dob" value="<?php echo $student->user->user_dob; ?>" aria-required="true" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Parent or Guardians Name', 'myworldclass' ); ?> <span>*</span></th>
			<td><input type="text" name="myaccount[parent_name]" id="myaccount-parent_name" value="<?php echo $student->user->parent_name; ?>" aria-required="true" /></td>
		</tr>
	</table>
	<p id="action-row">
		<input type="hidden" name="myaccount[nonce]" value="<?php echo wp_create_nonce( 'world-classes-edit-profile' . $student->student_id ); ?>" />
		<input type="submit" id="submit-account-update" value="<?php _e( 'Save Profile Details', 'myworldclass' ); ?>" />
	</p>
	<p id="required-explain"><em><?php _e( '* indicates a required field.', 'myworldclass' ); ?></em></p>
</form>
<?php

		$content = ob_get_contents();
		ob_end_clean();
		return $content;

	}
endif;

/**
 * Shortcode: Payment History
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_render_my_payment_history_shortcode' ) ) :
	function mywclass_render_my_payment_history_shortcode( $atts ) {

		if ( ! is_user_logged_in() ) return '<p>' . __( 'Please login to view your payment history.', 'myworldclass' ) . '</p>';

		$cui = get_current_user_id();
		$payments = new MyWorldClass_Payments( $cui );

		ob_start();

		if ( ! empty( $payments->payment_history ) ) : ?>

<div id="profile-name"><?php _e( 'Payment History', 'myworldclass' ); ?></div>
<h3 id="account-type"><a href="<?php echo get_permalink( $payments->prefs['my_account_page_id'] ); ?>"><?php _e( 'View Profile', 'myworldclass' ); ?></a></h3>
<table class="table" style="width: 100%;">
	<thead>
		<tr>
			<th style="width: 25%;text-align:left;"><?php _e( 'Date', 'myworldclass' ); ?></th>
			<th style="width: 25%;text-align:left;"><?php _e( 'Transaction ID', 'myworldclass' ); ?></th>
			<th style="width: 25%;text-align:left;"><?php _e( 'Status', 'myworldclass' ); ?></th>
			<th style="width: 25%;text-align:left;"><?php _e( 'Amount', 'myworldclass' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php

			foreach ( $payments->payment_history as $time => $entry ) {

?>

		<tr>
			<td><?php echo date_i18n( $payments->date_format, $time ); ?></td>
			<td><?php echo $entry['txt_id']; ?></td>
			<td><?php echo $entry['status']; ?></td>
			<td>$ <?php echo number_format( $entry['amount'], 2, '.', ' ' ); ?></td>
		</tr>
<?php

			}

?>
	</tbody>
</table>
<?php

		else :

			echo '<p>' . __( 'You do not have a payment history.', 'myworldclass' ) . '</p>';

		endif;

		$content = ob_get_contents();
		ob_end_clean();
		return $content;

	}
endif;

/**
 * Shortcode: Partial Payment
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_render_partial_payment_shortcode' ) ) :
	function mywclass_render_partial_payment_shortcode( $atts ) {

		if ( ! is_user_logged_in() ) return '';

		extract( shortcode_atts( array(
			'form_id' => '',
			'show'    => 'both',
			'page_id' => ''
		), $atts ) );

		$cui = get_current_user_id();
		$student = new MyWorldClass_Student( $cui );

		if ( $student->user->tour_code == '' ) return '<p>' . __( 'No tour connected with this account.', 'myworldclass' ) . '</p>';

		$tour_id = mywclass_get_tour_by_code( $student->user->tour_code );
		$tour = new MyWorldClass_Tour( $tour_id );

		if ( strtotime( $tour->last_pay_date ) < time() )
			return '<p>' . sprintf( __( 'Payments closed for this tour on %s', 'myworldclass' ), $tour->display_last_pay_date() ) . '</p>';

		$cost = mywclass_get_cost( $tour->post_id, $cui );
		$remaining = 0.00;
		if ( $cost > 0 )
			$remaining = $cost - $student->balance;

		if ( $remaining <= 0 )
			return '<p>' . __( 'You have paid this tour in full.', 'myworldclass' ) . '</p>';

		$cancel_return = $return = get_permalink( $student->prefs['my_account_page_id'] );
		$notify_url = add_query_arg( array( 'partial_payment' => 1 ), home_url( '/' ) );

		$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
		if ( $student->prefs['paypal']['sandbox'] == 1 )
			$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

		$item_name = $student->prefs['paypal']['item'];
		$item_name = str_replace( '%tour_code%', $student->user->tour_code, $item_name );

		ob_start(); ?>

<div id="tour-name">Tour: <?php echo $tour->post->post_title; ?><div class="my-balance-label"><?php _e( 'Total Cost of Tour', 'myworldclass' ); ?></div><span><?php echo '$ ' . $cost; ?></span></div>
<h3 id="remaining-payment"><?php printf( __( 'Amount Due by %s', 'myworldclass' ), $tour->display_last_pay_date() ); ?><span>$ <?php echo number_format( $remaining, 2, '.', ' ' ); ?></span></h3>
<?php if ( $show == 'both' ) : ?>
<div id="partial-payment-options">
	<div class="<?php if ( $page_id != '' ) echo 'half'; else echo 'full'; ?>">
		<form action="<?php echo $paypal_url; ?>" mathod="post">
			<input type="hidden" name="cmd" value="_xclick" />
			<input type="hidden" name="business" value="<?php echo $student->prefs['paypal']['email']; ?>" />
			<input type="hidden" name="item_name" value="<?php echo $item_name; ?>" />
			<input type="hidden" name="quantity" value="1" />
			<input type="hidden" name="currency_code" value="USD" />
			<input type="hidden" name="no_shipping" value="1" />
			<input type="hidden" name="custom" value="<?php echo $cui; ?>" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>" />
			<input type="hidden" name="rm" value="2" />
			<input type="hidden" name="cbt" value="<?php printf( __( 'Return to %s', 'myworldclass' ), get_bloginfo( 'name' ) ); ?>" />
			<input type="hidden" name="cancel_return" value="<?php echo $cancel_return; ?>" />

		</form>
	</div>
	<div class="half">
		<p><a href="<?php echo get_permalink( $page_id ); ?>" class="button button-primary btn btn-primary">Pay using Credit Card</a></p>
	</div>
	<div class="clear clearfix"></div>
</div>
<?php elseif ( $show == 'paypal' ) : ?>
<form action="<?php echo $paypal_url; ?>" mathod="post">
	<input type="hidden" name="cmd" value="_xclick" />
	<input type="hidden" name="business" value="<?php echo $student->prefs['paypal']['email']; ?>" />
	<input type="hidden" name="item_name" value="<?php echo $item_name; ?>" />
	<input type="hidden" name="quantity" value="1" />
	<input type="hidden" name="currency_code" value="USD" />
	<input type="hidden" name="no_shipping" value="1" />
	<input type="hidden" name="custom" value="<?php echo $cui; ?>" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>" />
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="cbt" value="<?php printf( __( 'Return to %s', 'myworldclass' ), get_bloginfo( 'name' ) ); ?>" />
	<input type="hidden" name="cancel_return" value="<?php echo $cancel_return; ?>" />
	<p>$ <input type="text" size="10" value="<?php if ( $student->prefs['paypal']['min'] > 0 ) echo $student->prefs['paypal']['min']; ?>" name="amount" placeholder="10.00" /> <input type="submit" value="<?php _e( 'Pay using PayPal', 'myworldclass' ); ?>" /></p>
</form>
<?php elseif ( $show == 'cc' ) : ?>
<?php echo do_shortcode( '[gravityform title="false" description="false" id="' . $form_id . '"]' ); ?>
<?php endif; ?>
<?php

		$content = ob_get_contents();
		ob_end_clean();
		return $content;

	}
endif;
?>