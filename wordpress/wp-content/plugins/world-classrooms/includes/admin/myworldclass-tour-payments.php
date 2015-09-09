<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Tour Payment Admin Notifications
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_update_messages' ) ) :
	function mywclass_tour_payment_update_messages( $messages ) {

		$messages['tour_payment'] = array(
			0  => __( 'Tour Payment Updated.', 'myworldclass' ),
			1  => __( 'Tour Payment Updated.', 'myworldclass' ),
			2  => __( 'Tour Payment Updated.', 'myworldclass' ),
			3  => __( 'Tour Payment Updated.', 'myworldclass' ),
			4  => __( 'Tour Payment Updated.', 'myworldclass' ),
			5  => __( 'Tour Payment Updated.', 'myworldclass' ),
			6  => __( 'Tour Payment Saved', 'myworldclass' ),
			7  => __( 'Tour Payment Saved', 'myworldclass' ),
			8  => __( 'Tour Payment Updated.', 'myworldclass' ),
			9  => __( 'Tour Payment Updated.', 'myworldclass' ),
			10 => __( 'Tour Payment Updated.', 'myworldclass' )
		);
		return $messages;

	}
endif;

/**
 * Change Enter Title Here
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_change_title_text' ) ) :
	function mywclass_change_title_text( $title ) {

		$screen = get_current_screen();
		if  ( 'tour_payment' == $screen->post_type )
			return 'Enter a Transaction ID';

		return $title;

	}
endif;

/**
 * Column Headers
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_column_headers' ) ) :
	function mywclass_tour_payment_column_headers( $default ) {

		$columns = array();
		$columns['title']          = __( 'Transaction ID', 'myworldclass' );
		$columns['payment-tour']   = __( 'Tour', 'myworldclass' );
		$columns['date']           = __( 'Date', 'myworldclass' );
		$columns['author']         = __( 'Student', 'myworldclass' );
		$columns['payment-amount'] = __( 'Amount', 'myworldclass' );
		$columns['payment-type']   = __( 'Type', 'myworldclass' );

		return $columns;

	}
endif;

/**
 * Column Content
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_column_content' ) ) :
	function mywclass_tour_payment_column_content( $column_name, $post_id ) {

		switch ( $column_name ) {

			case 'payment-status' :

				echo '<code>' . get_post_meta( $post_id, 'status', true ) . '</code>';

			break;
			
			case 'payment-amount' :

				$amount = get_post_meta( $post_id, 'amount', true );
				if ( $amount == '' )
					echo '-';
				else
					echo '$ ' . number_format( $amount, 2, '.', ' ' );

			break;
			
			case 'payment-tour' :

				$tour = get_post_meta( $post_id, 'tour_id', true );
				if ( $tour == '' ) {
					$post = get_post( $post_id );
					if ( isset( $post->post_author ) ) {
						$tour_code = get_user_meta( $post->post_author, 'tour_code', true );
						$tour_id   = mywclass_get_tour_by_code( $tour_code );
						if ( $tour_id !== false ) {
							update_post_meta( $post_id, 'tour_id', $tour_id );
							$tour = $tour_id;
						}
					}
				}

				if ( $tour != '' )
					echo get_the_title( $tour );
				else
					echo '-';

			break;
			
			case 'payment-type' :

				$payment = new MyWorldClass_Payments();
				$type = get_post_meta( $post_id, 'type', true );
				$types = $payment->get_types();
				if ( $type != '' && array_key_exists( $type, $types ) )
					echo $types[ $type ];
				else {
					update_post_meta( $post_id, 'type', 'card' );
					echo $types['card'];
				}

			break;

		}

	}
endif;

/**
 * Row Actions
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_row_actions' ) ) :
	function mywclass_tour_payment_row_actions( $actions, $post ) {

		if ( $post->post_type == 'tour_payment' ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;

	}
endif;

/**
 * Filter Payments
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_filter_tour_payments' ) ) :
	function mywclass_filter_tour_payments( $query ) {

		if ( is_admin() && isset( $query->query['post_type'] ) && $query->query['post_type'] == 'tour_payment' ) {

			$qv = &$query->query_vars;

			if ( isset( $_GET['student_id'] ) )
				$qv['author'] = absint( $_GET['student_id'] );

			if ( isset( $_GET['transaction_id'] ) )
				$qv['name'] = absint( $_GET['transaction_id'] );

		}

	}
endif;

/**
 * Filter Payments Options
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_filter_tour_payments_option' ) ) :
	function mywclass_filter_tour_payments_option() {

		$screen = get_current_screen();
		if ( $screen->id != 'edit-tour_payment' ) return;

?>
<input type="text" size="20" name="transaction_id" value="<?php if ( isset( $_GET['transaction_id'] ) ) echo urldecode( $_GET['transaction_id'] ); ?>" placeholder="Transaction ID" /> <input type="text" size="15" name="student_id" value="<?php if ( isset( $_GET['author'] ) ) echo urldecode( $_GET['author'] ); elseif ( isset( $_GET['student_id'] ) ) echo urldecode( $_GET['student_id'] ); ?>" placeholder="User ID" />
<?php
	}
endif;

/**
 * Metaboxes
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_metaboxes' ) ) :
	function mywclass_tour_payment_metaboxes() {

		add_meta_box(
			'payment-details',
			'Details',
			'mywclass_tour_payment_metabox_detail',
			'tour_payment',
			'normal',
			'core'
		);

	}
endif;

/**
 * Metaboxes
 * @since 1.1
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_tour_payment_metabox_detail' ) ) :
	function mywclass_tour_payment_metabox_detail( $post ) {

		$payments = new MyWorldClass_Payments();

		$type    = get_post_meta( $post->ID, 'type', true );
		$tour_id = get_post_meta( $post->ID, 'tour_id', true );
		$amount  = get_post_meta( $post->ID, 'amount', true );
		$note    = get_post_meta( $post->ID, 'note', true );

		global $wpdb;
		
		$tours = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} WHERE post_type = 'tour' AND post_status = 'publish' ORDER BY post_title ASC;" );

?>
<style type="text/css">
#payment-details .inside { margin: 0 0 0 0; padding: 0 0 0 0; }
#info-wrapper { float: none; clear: both; }
#info-wrapper .info-box { float: left; width: 25%; min-height: 50px; border-bottom: 1px solid #ddd; padding: 0 0 0 0; }
#info-wrapper .info-box.short { width: 15%; }
#info-wrapper .info-box.fourth { width: 55%; }
#info-wrapper .info-box.full { width: 100%; }
#info-wrapper p { padding: 6px 12px; font-size: 10px; line-height: 12px; }
#info-wrapper .info-box .sc-code { padding-right: 12px; }
#info-wrapper .info-box > div { padding: 10px 12px; }
#info-wrapper .info-box label { display: block; font-weight: bold; }
#info-wrapper .info-box input { width: 100%; font-family: Consolas,Monaco,monospace; min-height: 29px; }
#minor-publishing-actions { display: none; }
</style>
<div id="info-wrapper">
	<div class="info-box short">
		<div class="padding">
			<label for="tour-payment-type">Type</label>
			<div class="input-wrap">
				<select name="tour_payment[type]" id="tour-payment-type">
<?php

		foreach ( $payments->get_types() as $value => $label ) {
			echo '<option value="' . $value . '"';
			if ( $type == $value ) echo ' selected="selected"';
			echo '>' . $label . '</option>';
		}

?>
				</select>
			</div>
		</div>
	</div>
	<div class="info-box fourth">
		<div class="padding">
			<label for="tour-detail-trip-id">Trip</label>
			<div class="input-wrap">
				<select name="tour_payment[trip_id]" id="tour-payment-trip-id">
<?php

		foreach ( $tours as $the_tour ) {
			echo '<option value="' . $the_tour->ID . '"';
			if ( $tour_id == $the_tour->ID ) echo ' selected="selected"';
			echo '>' . $the_tour->post_title . '</option>';
		}

?>
				</select>
			</div>
		</div>
	</div>
	<div class="info-box short">
		<div class="padding">
			<label for="tour-payment-amount">Amount</label>
			<div class="input-wrap">
				<input type="text" name="tour_payment[amount]" placeholder="0.00" id="tour-payment-amount" class="" value="<?php echo $amount; ?>" />
			</div>
		</div>
	</div>
	
	<div class="info-box short">
		<div class="padding">
			<label for="tour-payment-user-id">User ID</label>
			<div class="input-wrap">
				<input type="text" name="tour_payment[user_id]" id="tour-payment-user-id" value="<?php echo $post->post_author; ?>" />
			</div>
		</div>
	</div>
	<div class="info-box full">
		<div class="padding">
			<label for="tour-payment-note">Description</label>
			<div class="input-wrap">
				<input type="text" name="tour_payment[note]" placeholder="Shown to the student in their profile" id="tour-payment-note" value="<?php echo esc_attr( $note ); ?>" />
			</div>
		</div>
	</div>
	<div class="clear clearfix"></div>
</div>
<?php

	}
endif;

/**
 * Save Tour Details
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_save_tour_payment_details' ) ) :
	function mywclass_save_tour_payment_details( $post_id ) {

		if ( ! isset( $_POST['tour_payment'] ) || ! current_user_can( 'edit_users' ) ) return;

		$data = $_POST['tour_payment'];

		$type = sanitize_key( $data['type'] );
		update_post_meta( $post_id, 'type', $type );

		if ( isset( $data['trip_id'] ) && absint( $data['trip_id'] ) != '' )
			update_post_meta( $post_id, 'tour_id', absint( $data['trip_id'] ) );

		$amount = sanitize_text_field( $data['amount'] );
		update_post_meta( $post_id, 'amount', number_format( $amount, 2, '.', '' ) );

		$note = sanitize_text_field( $data['note'] );
		update_post_meta( $post_id, 'note', $note );

		$user_id = get_current_user_id();
		if ( $data['user_id'] != '' )
			$user_id = absint( $data['user_id'] );

		global $wpdb;

		$wpdb->update(
			$wpdb->posts,
			array( 'post_author' => $user_id ),
			array( 'ID' => $post_id ),
			array( '%d' ),
			array( '%d' )
		);

	}
endif;

?>