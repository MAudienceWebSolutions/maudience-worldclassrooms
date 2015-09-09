<?php get_header(); ?>
<?php

	$user_id = get_current_user_id();
	$student = new MyWorldClass_Student( $user_id );

	$tour_id = mywclass_get_tour_by_code( $student->user->tour_code );
	$tour    = new MyWorldClass_Tour( $tour_id );

	$now         = current_time( 'timestamp' );

	if ( $student->user->tour_code != '' ) {

		$title          = $tour->post->post_title;

		$payment_date   = strtotime( $tour->start_date ) - ( 110 * DAY_IN_SECONDS );
		$days_left      = ( $payment_date - $now ) / DAY_IN_SECONDS;

		$tour_date      = strtotime( $tour->start_date );
		$tour_days_left = ( $tour_date - $now ) / DAY_IN_SECONDS;

		$length         = $tour->get_tour_length();
		$amount_owed    = mywclass_get_amount_owed( $user_id, $tour_id );

		$all_downloads  = true;

	}
	else {

		$title         = 'My Account';
		$amount_owed   = 0.00;

		$all_downloads = false;

	}

	$classes = "intro";

	if ( has_post_thumbnail() )
		$classes .= ' intro-teritary';
	else
		$classes .= ' intro-secondary';

?>
<div <?php post_class($classes) ?>>

	<?php get_template_part('fragments/thumbnail'); ?>

	<div class="intro-content">
		<div class="shell">
			<h2 class="intro-title"><?php echo $title; ?></h2>
		</div><!-- /.shell -->
	</div><!-- /.intro-content -->
</div><!-- /.intro -->
<div class="main">
	<div class="shell">
		<div id="profile-sidebar">
			<h2 id="students-name"><?php echo $student->display_name(); ?></h2>
			<div id="payment-due">
				<?php if ( $student->user->tour_code != '' ) : ?>
					<?php if ( $amount_owed > 0 ) : ?>
					<h4>Payment Due In</h4>
					<div id="payment-due-countdown"><?php printf( _n( '1 Day', '%d Days', $days_left, '' ), $days_left ); ?></div>
					<?php else : ?>
					<h4>Tour Begins In</h4>
					<div id="payment-due-countdown"><?php printf( _n( '1 Day', '%d Days', $tour_days_left, '' ), $tour_days_left ); ?></div>
					<?php endif; ?>
				<?php else : ?>
				<h4>No Tour Found</h4>
				<?php endif; ?>
			</div>
			<h2 id="profile-navigation">My Account</h2>
			<ul id="toggle-tabs">
				<li class="current"><a href="javascript:void(0);" data-toggle="details">Tour Details</a></li>
				<li><a href="javascript:void(0);" data-toggle="editaccount">Edit Account</a></li>
				<?php if ( $student->user->tour_code != '' ) : ?>
				<li><a href="javascript:void(0);" data-toggle="travelers">Traveler Details</a></li>
				<?php endif; ?>
				<?php if ( $student->user->tour_code != '' && $amount_owed != 0 ) : ?>
				<li><a href="javascript:void(0);" data-toggle="make-payment">Make a Payment</a></li>
				<?php endif; ?>
				<li><a href="javascript:void(0);" data-toggle="payment-history">Payment History</a></li>
				<li><a href="javascript:void(0);" data-toggle="downloads">Downloads & Forms</a></li>
			</ul>
		</div>
		<div id="profile-content">
			<div class="tab tab-active" id="tab-details" style="display:block;">
				<div style="margin: 24px 0 24px 0; border: 1px solid #1A83C5; background-color: #1A83C5; color: white; padding: 24px;">
					<h4>System Maintenance / Update</h4>
					<p>We are updating our website and some account information or payment records might be missing from your account. All records will become available as soon as our records have been updated. Apologies for any inconvenience and thank you for your patience.</p>
				</div>
				<?php if ( $student->user->tour_code == '' ) : ?>

				<h2 class="content-subheader">Tour Details</h2>
				<p>It seems you have not yet sound up for one of our tours.</p>

				<?php else : ?>

				<?php echo $tour->display_notice(); ?>

				<h2 class="content-subheader">Tour Details</h2>
				<ul>
					<li><strong>Tour</strong> <?php echo $tour->post->post_title; ?></li>
					<li><strong>Group Leader</strong> <?php if ( $tour->teacher_name != '' ) echo $tour->teacher_name; else echo '-'; ?></li>
					<li><strong>Payment Plan</strong> Manual Payment Plan</li>
					<li><strong>Trip Balance</strong> <?php if ( $amount_owed != 0 ) echo '$ ' . $amount_owed; else echo 'Paid in Full'; ?></li>
					<li><strong>Trip ID Number</strong> <?php echo $tour->tour_code; ?></li>
					<li><strong>Tour Start Date</strong> <?php echo $tour->display_start_date(); ?></li>
					<li><strong>Tour End Date</strong> <?php echo $tour->display_end_date(); ?></li>
				</ul>

				<?php endif; ?>
			</div>
			<div class="tab" id="tab-editaccount" style="display:none;">
				<h2 class="content-subheader">Account</h2>
				<form id="update-my-account-form" method="post" action="">
					<input type="hidden" name="user_id" value="<?php echo absint( $user_id ); ?>" />
					<div class="col-half">
						<div class="form-group">
							<label for="new-account-first-name">First Name</label>
							<div class="form-group-input">
								<input type="text" name="first_name" id="new-account-first-name" value="<?php echo esc_attr( $student->user->first_name ); ?>" placeholder="<?php _e( 'Required', '' ); ?>" />
							</div>
						</div>
					</div>
					<div class="col-half last">
						<div class="form-group">
							<label for="new-account-last-name">Last Name</label>
							<div class="form-group-input">
								<input type="text" name="last_name" id="new-account-last-name" value="<?php echo esc_attr( $student->user->last_name ); ?>" placeholder="<?php _e( 'Required', '' ); ?>" />
							</div>
						</div>
					</div>
					<div class="col-half">
						<div class="form-group">
							<label for="new-account-user-email">Email Address</label>
							<div class="form-group-input">
								<input type="text" name="user_email" id="new-account-user-email" value="<?php echo esc_attr( $student->user->user_email ); ?>" placeholder="<?php _e( 'Required', '' ); ?>" />
							</div>
						</div>
					</div>
					<div class="col-half last">
						<div class="form-group">
							<label for="new-account-user-name">Username</label>
							<div class="form-group-input">
								<input type="text" readonly="readonly" id="new-account-user-name" value="<?php echo esc_attr( $student->user->user_login ); ?>" />
							</div>
						</div>
					</div>
					<div class="col-half">
						<div class="form-group">
							<label for="new-account-user-phone">Phone</label>
							<div class="form-group-input">
								<input type="text" name="user_phone" id="new-account-user-phone" value="<?php echo esc_attr( $student->user->user_phone ); ?>" placeholder="<?php _e( 'Required', '' ); ?>" />
							</div>
						</div>
					</div>
					<div class="col-half last">
						<div class="form-group">
							<label for="new-account-user-dob">&nbsp;</label>
							<div class="form-group-input">
								&nbsp;
							</div>
						</div>
					</div>
					<h4>Change Account Password</h4>
					<div class="col-half">
						<div class="form-group">
							<label for="">New Password</label>
							<div class="form-group-input">
								<input type="password" name="new_pwd" id="new-account-pwd" value="" />
							</div>
						</div>
					</div>
					<div class="col-half last">
						<div class="form-group">
							<label for="">Confirm</label>
							<div class="form-group-input">
								<input type="password" name="new_pwd_confirm" id="new-account-pwd-confirm" value="" />
							</div>
						</div>
					</div>
					<div style="text-align:right;">
						<button type="submit" id="change-account-pwd-btn">Save</button>
					</div>
					<div class="clear clearfix"></div>
				</form>
			</div>
			<?php if ( $student->user->tour_code != '' ) : ?>
			<div class="tab" id="tab-travelers" style="display:none;">
				<h2 class="content-subheader">Travelers</h2>
<?php

		$tour->setup_signup();

		if ( ! empty( $tour->signup->travelers ) && $tour->signup->travelers[0]['first_name'] != '' ) {
			foreach ( $tour->signup->travelers as $id => $traveler ) {

				$dobcheck = explode( '/', $traveler['DOB'] );
				if ( count( $dobcheck ) != 3 )
					$dobcheck = date( 'd/m/Y', $traveler['DOB'] );

?>
				<h4><?php printf( __( 'Traveler #%d', '' ), $id + 1 ); ?></h4>
				<ul>
					<li><strong>First Name</strong> <?php echo esc_attr( $traveler['first_name'] ); ?></li>
					<li><strong>Middle Name</strong> <?php if ( $traveler['middle_name'] != '' ) echo esc_attr( $traveler['middle_name'] ); else echo '-'; ?></li>
					<li><strong>Last Name</strong> <?php echo esc_attr( $traveler['last_name'] ); ?></li>
					<li><strong>Traveler Type</strong> <?php if ( $traveler['type'] == 'student' ) _e( 'Student', '' ); else _e( 'Parent', '' ); ?></li>
					<li><strong>Gender</strong> <?php if ( $traveler['gender'] == 'male' ) echo __( 'Male', '' ); else echo __( 'Female', '' ); ?></li>
					<li><strong>Date of Birth</strong> <?php echo $traveler['DOB']; ?></li>
					<li><strong>Address</strong> <?php echo esc_attr( $traveler['address1'] ); ?></li>
					<li><strong>City</strong> <?php echo esc_attr( $traveler['city'] ); ?></li>
					<li><strong>State</strong> <?php echo esc_attr( $traveler['state'] ); ?></li>
					<li><strong>Zip</strong> <?php echo esc_attr( $traveler['zip'] ); ?></li>
				</ul>
<?php

			}
		}
		else {

?>
				<h4><?php _e( 'Traveler', '' ); ?></h4>
				<ul>
					<li><strong>First Name</strong> <?php echo $student->user->first_name; ?></li>
					<li><strong>Last Name</strong> <?php echo $student->user->last_name; ?></li>
					<li><strong>Type</strong> <?php _e( 'Student', '' ); ?></li>
					<li><strong>Date of Birth</strong> <?php echo $student->user->user_dob; ?></li>
				</ul>
<?php

		}

?>
			</div>
			<div class="tab" id="tab-make-payment" style="display:none;">
				<h2 class="content-subheader">Make Payment</h2>

				<?php $tour->display_account_overview_box( $amount_owed ); ?>

				<?php $tour->display_manual_payment_form( $amount_owed ); ?>

			</div>
			<?php endif; ?>
			<div class="tab" id="tab-payment-history" style="display:none;">
				<h2 class="content-subheader">Payment History</h2>

				<?php if ( $student->user->tour_code != '' ) : ?>
				<?php $tour->display_account_overview_box( $amount_owed ); ?>
				<?php endif; ?>
<?php

		$payments = new MyWorldClass_Payments( $user_id );

		if ( ! empty( $payments->payment_history ) ) {

?>
				<table class="table" style="width: 100%;">
					<thead>
						<tr>
							<th class="history-col-count">&nbsp;</th>
							<th class="history-col-date"><?php _e( 'Date', 'myworldclass' ); ?></th>
							<th class="history-col-desc"><?php _e( 'Description', 'myworldclass' ); ?></th>
							<th class="history-col-amount"><?php _e( 'Amount', 'myworldclass' ); ?></th>
						</tr>
					</thead>
					<tbody>
<?php

			$counter = $total = 0;
			foreach ( $payments->payment_history as $time => $entry ) {

				$counter ++;
				$paid  = number_format( $entry['amount'], 2, '.', ' ' );
				$total = $total + $paid;

?>

						<tr class="border">
							<td class="history-col-count"><?php echo zeroise( $counter, 2 ); ?></td>
							<td class="history-col-date"><?php echo date_i18n( $payments->date_format, $time ); ?></td>
							<td class="history-col-desc"><?php echo $entry['note']; ?></td>
							<td class="history-col-amount">$ <?php echo $paid; ?></td>
						</tr>
<?php

			}

?>
						<tr class="footer">
							<td class="history-col-count">&nbsp;</td>
							<td class="history-col-date">&nbsp;</td>
							<td class="history-col-desc"><h2 style="padding-right: 12px; text-align: right;">Total Paid: </h2></td>
							<td class="history-col-amount"><h2>$ <?php echo number_format( $total, 2, '.', '' ); ?></h2></td>
						</tr>
					</tbody>
				</table>
<?php

		}
		else {

?>
				<p><?php _e( 'No payment history found.', '' ); ?></p>
<?php

		}

?>
			</div>
			<div class="tab" id="tab-downloads" style="display:none;">
				<h2 class="content-subheader">Downloads</h2>
				<?php echo $tour->display_downloads( $all_downloads ); ?>
			</div>
		</div>
	</div><!-- /.shell -->
</div><!-- /.main -->
<script type="text/javascript">
jQuery(function($) {

	$(document).ready(function(){

		myworldclass_fields_required = function(){

			var parenttravel = '';
			var all_required = true;
			var req_count = 0;
			$( '#make-manual-payment-form' ).find( 'input.required' ).each(function( index, element ){

				if ( $( element ).val() == '' ) {
					all_required = false;
					$( element ).parent().parent().addClass( 'has-error' );
				}
				else {
					$( element ).parent().parent().removeClass( 'has-error' );
				}

			});

			$( '#make-manual-payment-form' ).find( 'select.required' ).each(function( index, element ){

				if ( $( element ).find( ':selected' ).val() == '' ) {
					all_required = false;
					$( element ).parent().parent().addClass( 'has-error' );
				}
				else {
					$(this).parent().parent().removeClass( 'has-error' );
				}

			});

			return all_required;

		};

		$( '#make-manual-payment-form' ).on( 'submit', function(e){

			e.preventDefault();

			if ( ! myworldclass_fields_required() ) {

				alert( '<?php _e( 'Please make sure you have filled out all the required fields', 'myworldclass' ); ?>' );
				return false;

			}

			var payment_form = $(this);
			var balance_after_payment = $( '#balance-after-payment' ).text();
			var balance_remaining = $( '#balance-after-payment-top' ).text();
			var amountchanged = 0;

			$.ajax({
				url        : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type       : 'POST',
				dataType   : 'JSON',
				data       : {
					action    : 'make-manual-payment',
					key       : '<?php echo wp_create_nonce( 'make-manual-payment' ); ?>',
					tour_id   : '<?php echo $tour->post_id; ?>',
					form      : payment_form.serialize()
				},
				beforeSend : function() {

					payment_form.fadeOut();

				},
				success    : function( response ) {

					if ( response == 0 || response == '-1' ) {
						alert( '<?php _e( 'Session Timeout. Please reload this page and try again.', 'myworldclass' ); ?>' );
						payment_form.fadeIn();
					}
					else {

						alert( response.data.message );

						if ( response.success ) {

							amountchanged = parseFloat( response.data.amount );

							balance_after_payment = parseFloat( balance_after_payment );
							balance_after_payment = parseFloat( balance_after_payment - amountchanged ).toFixed(2);

							$( '#balance-after-payment' ).text( balance_after_payment );

							balance_remaining     = parseFloat( balance_remaining );
							balance_remaining = parseFloat( balance_remaining - amountchanged ).toFixed(2);

							$( '#balance-after-payment-top' ).text( balance_remaining );

						}

						payment_form.fadeIn();
						payment_form.reset;

					}

				}
			});

			return false;

		});

		$( 'ul#toggle-tabs' ).on( 'click', 'li a', function(e){

			var selectedtab = $(this).data( 'toggle' );

			$( '#profile-content .tab' ).hide();
			$( '#profile-content #tab-' + selectedtab ).show();

			$( 'ul#toggle-tabs li' ).removeClass( 'current' );
			$(this).parent().addClass( 'current' );

		});

		$( '#profile-content' ).on( 'submit', '#update-my-account-form', function(e){

			e.preventDefault();
			var pwdform = $(this);
			var submitbutton = $( '#change-account-pwd-btn' );
			var buttonlabel  = submitbutton.text();

			$.ajax({
				url        : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type       : 'POST',
				dataType   : 'JSON',
				data       : {
					action    : 'update-my-account',
					key       : '<?php echo wp_create_nonce( 'update-my-front-end-profile' ); ?>',
					form      : pwdform.serialize()
				},
				beforeSend : function() {

					submitbutton.text( 'Updating ...' );

				},
				success    : function( response ) {

					console.log( response );
					if ( response == 0 || response == '-1' ) {
						alert( '<?php _e( 'Session Timeout. Please reload this page and try again.', 'myworldclass' ); ?>' );
					}
					else {
						alert( response.data );
					}

					$( '#new-account-pwd' ).val( '' );
					$( '#new-account-pwd-confirm' ).val( '' );

					submitbutton.text( buttonlabel );

				}
			});

		});

	});

});
</script>
<?php get_footer(); ?>