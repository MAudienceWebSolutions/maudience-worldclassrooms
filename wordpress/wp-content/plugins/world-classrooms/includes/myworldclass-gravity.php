<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Gravity Authorizenet Payments
 * This needs to be adjusted to work with Authorize net.
 * This function is called by Gforms when a payment capture is completed.
 *
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_gravity_authorizenet_payments' ) ) :
	function mywclass_gravity_authorizenet_payments( $amount, $entry, $form ) {
		global $gAuthorizePaymentAmount;
		
		$amount = $gAuthorizePaymentAmount;
		if ( ! is_user_logged_in() ) {
			global $mywclass_payments;

			$mywclass_payments = $amount;
			$GLOBALS['mywclass-amount-paid'] = $amount;
		}
		else {
			$user_id = get_current_user_id();
			if ( $user_id == 0 ) return;

			$balance = get_user_meta( $user_id, 'mywclass_balance', true );
			if ( $balance == '' )
				$balance = 0;

			$new_balance = $balance + abs( $amount );
			update_user_meta( $user_id, 'mywclass_balance', $new_balance );

			$payment = new MyWorldClass_Payments( $user_id );
			$payment->add_payment( $entry['transaction_id'], 'Completed', 'Completed Payment', $amount, '' );
		}

	}
endif;

if ( ! function_exists( 'mywclass_gravity_authorizenet_subscription' ) ) :
	function mywclass_gravity_authorizenet_subscription( $event, $subscription_id, $amount ) {

		// Currently not supported

	}
endif;

/**
 * Gravity PayPal Payments
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_gravity_paypal_payments' ) ) :
	function mywclass_gravity_paypal_payments( $amount, $entry, $form ) {

		if ( ! is_user_logged_in() ) {
			global $mywclass_payments;

			$mywclass_payments = $amount;
			$GLOBALS['mywclass-amount-paid'] = $amount;
		}
		else {
			$user_id = get_current_user_id();
			if ( $user_id == 0 ) return;

			$balance = get_user_meta( $user_id, 'mywclass_balance', true );
			if ( $balance == '' )
				$balance = 0;

			$new_balance = $balance + abs( $amount );
			update_user_meta( $user_id, 'mywclass_balance', $new_balance );

			$payment = new MyWorldClass_Payments( $user_id );
			$payment->add_payment( $entry['transaction_id'], 'Completed', 'Completed Payment', $amount, '' );
		}

	}
endif;

if ( ! function_exists( 'mywclass_gravity_paypal_subscription' ) ) :
	function mywclass_gravity_paypal_subscription( $event, $subscription_id, $amount ) {

		// Currently not supported

	}
endif;

/**
 * Gravity User Registration
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_capture_user_creation' ) ) :
	function mywclass_capture_user_creation( $user_id, $feed, $entry, $password ) {

		global $mywclass_payments;

		$amount = false;
		if ( is_numeric( $mywclass_payments ) && $mywclass_payments !== NULL )
			$amount = $mywclass_payments;
		elseif ( isset( $GLOBALS['mywclass-amount-paid'] ) )
			$amount = $GLOBALS['mywclass-amount-paid'];

		if ( $amount !== false ) {

			$balance = get_user_meta( $user_id, 'mywclass_balance', true );
			if ( $balance == '' )
				$balance = 0;

			$new_balance = $balance + abs( $amount );
			update_user_meta( $user_id, 'mywclass_balance', $new_balance );

			$payment = new MyWorldClass_Payments( $user_id );
			$payment->add_payment( $entry['transaction_id'], 'Completed', 'Completed registration payment', $amount, '' );

		}

	}
endif;
?>