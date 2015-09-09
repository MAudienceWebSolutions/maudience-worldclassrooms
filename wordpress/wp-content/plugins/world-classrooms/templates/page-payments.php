<?php get_header(); ?>
<style type="text/css">
body.signing-up .article {
	padding-top: 0;
}
body.signing-up .article .article-head {
	float: right;
}
#payment-body {
	padding: 24px 0 48px 0;
	min-height: 700px;
}
#payment-body.autohight {
	min-height: auto;
	height: auto;
}
#payment-body .text-center {
	text-align: center;
}
#payment-body h1.blue {
	color: #1a83c5;
	text-transform: uppercase;
	font-weight: 100;
	border-color: #1a83c5;
	border-bottom: 1px solid #ded3d6;
	margin-bottom: 24px;
}
#payment-body .blue, 
#payment-body ul li strong {
	color: #1a83c5;
	font-weight: bold;
}
#payment-body ul {
	padding: 22px 22px 0 22px;
	list-style-type: none;
}
#payment-body ul li {
	font-size: 12px;
	line-height: 18px;
	margin-bottom: 12px;
}
#payment-body form {
	padding: 24px;
	background-color: #fafafa;
	border: 1px solid #2b3948;
}
#payment-body form p strong {
	color: #2b3948;
}
#payment-body form input, 
#payment-body form select {
	border: 1px solid #2b3948;
}
#payment-body form .form-group {
	display: block;
	float: none;
	min-height: 45px;
}
#payment-body form .form-group.has-error label, 
#payment-body form .manual-input.has-error input, 
#payment-body form .form-group.has-error .form-group-input, 
#payment-body form .form-group.has-error .form-group-input input, 
#payment-body form .form-group.has-error .form-group-input select, 
#payment-body form .form-group.has-error .form-group-input span {
	color: red;
	border-color: red;
}
#payment-body form .form-group > label {
	display: block;
	float: left;
	width: 95px;
	min-height: 30px;
	font-size: 12px;
	padding-top: 6px;
}
#payment-body form .form-group.checkbox > label {
	text-align: right;
	line-height: inherit;
}
#payment-body form .form-group.inline > label {
	text-align: center;
	width: 80px;
}
#payment-body form .form-group.inline .form-group-input {
	margin-left: 80px;
}
#payment-body form .form-group.checkbox > label input {
	margin-right: 12px;
}
#payment-body form .form-group .form-group-input {
	margin-left: 95px;
}
#payment-body form .form-group.checkbox .form-group-input, 
#payment-body form .form-group .form-group-input label {
	font-size: 12px;
	line-height: 18px;
}
#payment-body form .form-group.checkbox .form-group-input {
	margin-bottom: 12px;
}
#payment-body form .form-group .form-group-input span {
	color: #aeb1b5;
	font-style: italic;
}
#payment-body form .form-group .form-group-input label {
	width: 50%;
	float: left;
	line-height: 30px;
}
#payment-body form .form-group .form-group-input label input[type="radio"] {
	margin-right: 6px;
}
#payment-body form .form-group .form-group-input p, 
#payment-body form .form-group .select {
	font-size: 12px;
	line-height: 30px;
}
#payment-body form .form-group .select {
	height: 30px;
}
#payment-body form .form-group .form-group-input > input, 
#payment-body form .payment-plan > label div.manual-input input {
	width: 100%;
	height: 30px;
	font-size: 12px;
	padding: 2px 6px;
}
#payment-body form .form-group .form-group-input > input.auto-width {
	width: auto;
	float: right;
}
#payment-body form .travelers {
	display: block;
	float: none;
	clear: both;
}
#payment-body form .travelers > div {
	margin: 0 0 0 0;
	padding: 0 0 0 0;
}
#payment-body form .travelers .traveler-info {
	float: left;
	width: 70%;
}
#payment-body form .travelers .traveler-info .col-half {
	float: left;
	width: 49%;
	margin-right: 2%;
}
#payment-body form .travelers .traveler-info .col-half.last {
	margin-right: 0;
}
#payment-body form .travelers .traveler-widget {
	float: right;
	width: 30%;
}
#payment-body form .travelers .traveler-widget .signup-widget {
	background-color: #ebebeb;
	margin-left: 24px;
}
#payment-body .view-tour {
	text-align: right;
	padding-right: 12px;
	padding-bottom: 8px;
	font-size: 12px;
	line-height: 24px;
}
#payment-body .view-tour a {
	text-decoration: underline;
}
#payment-body form .travelers .traveler-widget h3 {
	padding: 0 12px;
	background-color: #404d5a;
	color: white;
	text-transform: uppercase;
	font-weight: 100;
	line-height: 48px;
}
#payment-body form #submit-form-row {
	text-align: right;
	padding: 6px 0;
	border-top: 1px solid #ded3d6;
	margin-top: 24px;
}
#payment-body form #submit-form-row button, 
#payment-body .btn {
	background-color: #1a83c5;
	border: 1px solid #1a83c5;
	color: white;
	font-size: 20px;
	line-height: 32px;
	padding: 6px 24px;
}
#payment-body form #submit-form-row button.final {
	background-color: #ff9900;
	border: 1px solid #ff9900;
}
#payment-body form #submit-form-row button.go-back-in-signup {
	float: left;
}
#payment-body form input[readonly="readonly"], 
#payment-body form select[readonly="readonly"] {
	background-color: #fafafa;
	border-color: #ded3d6;
}
#payment-body form #scholarship-wrapper {
	padding-top: 68px;
}
#payment-body form #scholarship-wrapper strong {
	display: block;
}
#payment-body form #scholarship-wrapper .form-group-input {
	margin-right: 125px;
}
#payment-body form #scholarship-wrapper .form-group-input input {
	width: 100%;
}
#payment-body form #scholarship-code {
	height: 30px;
	font-size: 12px;
	padding: 2px 6px;
}
#payment-body form #apply-scholarship-code {
	background-color: #ff9900;
	border: 1px solid #ff9900;
	color: white;
	font-size: 12px;
	line-height: 16px;
	padding: 6px 24px;
	float: right;
	width: 100%;
	margin-top: 12px;
}
.alert {
	border: 1px solid #ebccd1;
	background-color: #f2dede;
	color: #a94442;
	padding: 6px 12px;
	font-size: 12px;
	line-height: 32px;
	margin-bottom: 24px;
}
#payment-body form #terms input {
	margin-right: 24px;
}
#payment-body form #terms a {
	color: #1a83c5;
	text-decoration: underline;
}
#payment-body form .payment-plan > label input[type="radio"] {
	float: left;
	margin: 10px 24px 24px 24px;
}
#payment-body form .payment-plan > label div {
	margin-left: 62px;
}
#payment-body form .payment-plan > label div.manual-input {
	margin-left: 0;
	margin-bottom: 24px;
}
#payment-body form .payment-plan h1 {
	margin-bottom: 12px;
}
#payment-body form .payment-plan p {
	font-size: 16px;
	line-height: 20px;
	margin-bottom: 24px;
	font-weight: 100;
}
#payment-body form .payment-plan > label div.manual-input input {
	width: auto;
	margin-left: 24px;
	margin-right: 24px;
}
#payment-body form .payment-plan p strong {
	white-space: nowrap;
}
</style>
<?php

	

	

?>
<div class="main">
	<div class="shell">
		<article class="article article-faq">
			<header class="article-head">
				<h2 class="article-title"><?php _e( 'Tour Enrollment', 'myworldclass' ); ?></h2>
			</header>
			<div class="article-body">
				<div class="article-entry">
<?php

	if ( have_posts() ) : while ( have_posts() ) : the_post();

		$tour = new MyWorldClass_Tour( get_the_ID() );

		// Can not signup
		if ( ! $tour->can_signup() ) {

?>
					<div id="payment-process-wrapper">
<style type="text/css">
#payment-process-wrapper {
	display: block;
	float: none;
	clear: both;
	min-height: 100px;
}
#payment-process-wrapper .step {
	display: block;
	width: 25%;
	height: 100px;
	line-height: 100px;
	float: left;
}
#payment-process-wrapper .step.current {
	color: #ff9900;
}
#payment-process-wrapper .step img {
	width: 100px;
	height: 100px;
	display: inline;
}
</style>
						<div class="stepcurrent">
							<img src="<?php echo plugins_url( 'assets/images/step4c.png', MYWORLDCLASS ); ?>" alt="" /> <?php _e( 'Enrollment Completed', '' ); ?>
						</div>
					</div>
					<div id="payment-body" class="autohight">
						<form>
							<h1 class="blue"><?php _e( 'You have already enrolled to this tour.', 'myworldclass' ); ?></h1>
							<p><?php _e( 'Visit your profile for more information about this tour.', 'myworldclass' ); ?></p>
							<a class="btn" href="<?php echo get_permalink( $tour->prefs['my_account_page_id'] ); ?>">View Your Profile</a>
							<div class="clear clearfix"></div>
						</form>
					</div>
<?php

		}

		// New signup
		else {

			$tour->setup_signup();

?>
					<!-- /. Payment Process Header -->
					<div id="payment-process-wrapper">

						<?php $tour->display_signup_progress(); ?>

					</div>

					<!-- /. Payment Body -->
					<div id="payment-body">

						<?php $tour->display_signup_form(); ?>

					</div>

<script type="text/javascript">
jQuery(function($) {

	$(document).ready(function(){

		var attending_travelers = 1;
		var currentstep = <?php echo $tour->signup_step; ?>;
		var progressbox = $( '#payment-process-wrapper' );

		myworldclass_update_progress = function( newstep ) {

			if ( $( '#current-step-' + currentstep + ' a' ).hasClass( 'disabled' ) )
				$( '#current-step-' + currentstep + ' a' ).removeClass( 'disabled' );

			var currentimg = $( '#current-step-' + currentstep + ' a img' ).attr( 'src' );

			console.log( 'Current Step: ' + currentstep );
			console.log( 'Current Step Image Before: ' + currentimg );
			currentimg = currentimg.replace( 'step' + currentstep + 'c.png', 'step' + currentstep + '.png' );
			console.log( 'Current Step Image After: ' + currentimg );

			$( '#current-step-' + currentstep + ' a img' ).attr( 'src', currentimg );

			$( '#payment-process-wrapper .current' ).removeClass( 'current' );
			$( '#current-step-' + newstep ).addClass( 'current' );

			if ( $( '#current-step-' + newstep + ' a' ).hasClass( 'disabled' ) )
				$( '#current-step-' + newstep + ' a' ).removeClass( 'disabled' );

			var newimg = $( '#current-step-' + newstep + ' a img' ).attr( 'src' );

			console.log( 'New Step: ' + newstep );
			console.log( 'New Step Image Before: ' + newimg );
			newimg = newimg.replace( 'step' + newstep + '.png' , 'step' + newstep + 'c.png' );
			console.log( 'New Step Image After: ' + newimg );

			$( '#current-step-' + newstep + ' a img' ).attr( 'src', newimg );

			currentstep = newstep;

		};

		myworldclass_is_checked = function() {

			var parenttravel = '';
			var all_checked  = true;
			$( '.must-be-ticked' ).each(function(){

				parenttravel = $(this).closest( '.travelers' ).data( 'row' );
				if ( parseInt( parenttravel ) < attending_travelers ) {
					console.log( 'input.required - Parent wrapper ID: #traveler' + parenttravel );
					if ( ! $(this).is( ':checked' ) ) {
						all_checked = false;
						$(this).parent().parent().addClass( 'has-error' );
						console.log( 'The following check element is not checked: #' + $(this).attr( 'id' ) );
					}
					else {
						$(this).parent().parent().removeClass( 'has-error' );
					}
				}

			});

			return all_checked;

		};

		myworldclass_fields_required = function(){

			var parenttravel = '';
			var all_required = true;
			var req_count = 0;
			$( '#payment-body form' ).find( 'input.required' ).each(function( index, element ){

				parenttravel = $( element ).closest( '.travelers' ).data( 'row' );
				if ( parenttravel != '-1' && parseInt( parenttravel ) < attending_travelers ) {

					if ( $( element ).val() == '' ) {
						all_required = false;
						$( element ).parent().parent().addClass( 'has-error' );
					}
					else {
						$( element ).parent().parent().removeClass( 'has-error' );
					}

				}

				else if ( parenttravel == '-1' ) {

					if ( $( element ).val() == '' ) {
						all_required = false;
						$( element ).parent().parent().addClass( 'has-error' );
					}
					else {
						$( element ).parent().parent().removeClass( 'has-error' );
					}

				}

			});

			$( '#payment-body form' ).find( 'select.required' ).each(function( index, element ){

				parenttravel = $( element ).closest( '.travelers' ).data( 'row' );
				if ( parenttravel != '-1' && parseInt( parenttravel ) < attending_travelers ) {

					if ( $( element ).find( ':selected' ).val() == '' ) {
						all_required = false;
						$( element ).parent().parent().addClass( 'has-error' );
					}
					else {
						$(this).parent().parent().removeClass( 'has-error' );
					}

				}

				else if ( parenttravel == '-1' ) {

					if ( $( element ).find( ':selected' ).val() == '' ) {
						all_required = false;
						$( element ).parent().parent().addClass( 'has-error' );
					}
					else {
						$( element ).parent().parent().removeClass( 'has-error' );
					}

				}

			});

			return all_required;

		};

		$( '#payment-body' ).on( 'submit', 'form', function(e){

			e.preventDefault();

			console.log( 'Attendees: ' + attending_travelers );
			if ( ! myworldclass_fields_required() ) {

				alert( '<?php _e( 'Please make sure you have filled out all the required fields', 'myworldclass' ); ?>' );
				return false;

			}

			if ( ! myworldclass_is_checked() ) {

				alert( '<?php _e( 'Please make sure you have checked all required checkboxes!', 'myworldclass' ); ?>' );
				return false;

			}

			var signup_form = $(this);

			$.ajax({
				url        : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type       : 'POST',
				dataType   : 'HTML',
				data       : {
					action    : 'signup-for-tour',
					key       : '<?php echo wp_create_nonce( 'signup-for-tour-' . $tour->post_id ); ?>',
					form      : signup_form.serialize()
				},
				beforeSend : function() {

					signup_form.fadeOut();
					$( 'html, body' ).animate({
						scrollTop: $( 'html body' ).offset().top
					});

				},
				success    : function( data ) {

					if ( data == 0 || data == '-1' ) {
						alert( '<?php _e( 'Session Timeout. Please reload this page and try again.', 'myworldclass' ); ?>' );
						signup_form.fadeIn();
					}
					else {

						signup_form.empty().html( data ).fadeIn();
						myworldclass_update_progress( currentstep+1 );

					}

				}
			});

			return false;

		});

		$( '.article-body' ).on( 'click', '.go-back-in-signup', function(e){

			e.preventDefault();

			if ( $(this).hasClass( 'disabled' ) ) {
				return false;
			}

			if ( confirm( '<?php _e( 'Are you sure you want to go back? Your current entry will not be saved.', 'myworldclass' ); ?>' ) ) {

				var signup_form = $( '#payment-body form' );
				var gotostep    = $(this).data( 'to' );

				console.log( 'Go back click ' + gotostep );
				$.ajax({
					url        : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					type       : 'POST',
					dataType   : 'HTML',
					data       : {
						action    : 'signup-for-tour',
						key       : '<?php echo wp_create_nonce( 'signup-for-tour-' . $tour->post_id ); ?>',
						goto      : gotostep,
						tour_id   : $(this).data( 'tour-id' ),
						signup_id : $(this).data( 'signup-id' )
					},
					beforeSend : function() {

						signup_form.fadeOut();
						$( 'html, body' ).animate({
							scrollTop: $( 'html body' ).offset().top
						});

					},
					success    : function( data ) {

						if ( data == 0 || data == '-1' ) {
							alert( '<?php _e( 'Session Timeout. Please reload this page and try again.', 'myworldclass' ); ?>' );
							signup_form.fadeIn();
						}
						else {

							signup_form.empty().html( data ).fadeIn();
							myworldclass_update_progress( gotostep );
							
						}

					}
				});

			}

		});

		$( '#payment-body form' ).on( 'change', '#number-of-travelers', function(){

			attending_travelers = parseInt( $(this).find( ':selected' ).val() );
			var attcounter = 0;

			$( '.travelers' ).hide().each(function(e){

				if ( attcounter < attending_travelers )
					$( '#traveler' + attcounter ).show();

				attcounter++;

			});

		});

		$( '#payment-body form' ).on( 'change', '.payment-plan input[type="radio"]', function(){

			var creditcardform = $( '#credit-card-form' );

			if ( $(this).data( 'hide' ) == 'yes' ) {

				creditcardform.hide();
				$( '#payment-card' ).removeClass( 'required' );
				$( '#payment-name' ).removeClass( 'required' );
				$( '#payment-cvv' ).removeClass( 'required' );
				$( '#payment-exp-mm' ).removeClass( 'required' );
				$( '#payment-exp-yy' ).removeClass( 'required' );

			}
			else {

				creditcardform.show();
				if ( ! $( '#payment-card' ).hasClass( 'required' ) ) {
					$( '#payment-card' ).addClass( 'required' );
					$( '#payment-name' ).addClass( 'required' );
					$( '#payment-cvv' ).addClass( 'required' );
					$( '#payment-exp-mm' ).addClass( 'required' );
					$( '#payment-exp-yy' ).addClass( 'required' );
				}

			}

		});

		$( '#payment-body form' ).on( 'click', '.toggles-fields', function(){

			var fieldstotoggle = $(this).data( 'toggle' );
			$( '#payment-body form' ).find( '.' + fieldstotoggle ).each(function( index, element ){

				if ( $( element ).hasClass( 'readonly' ) ) {
					$( element ).removeAttr( 'readonly' ).removeClass( 'readonly' );
					if ( ! $( element ).hasClass( 'optional' ) )
						$( element ).addClass( 'required' );
				}
				else {
					$( element ).attr( 'readonly', 'readonly' ).addClass( 'readonly' ).removeClass( 'required' );
					if ( $( element ).parent().parent().hasClass( 'has-error' ) )
						$( element ).parent().parent().removeClass( 'has-error' );
				}

			});

		});

		$( '#payment-body form' ).on( 'click', '#apply-scholarship-code', function(){

			var validatebutton = $(this);
			var buttonlabel    = validatebutton.text();
			var scholarship    = $( '#scholarship-code' );

			if ( scholarship.val() == '' ) {

				scholarship.parent().parent().addClass( 'has-error' );
				return;

			}
			else {

				scholarship.parent().parent().removeClass( 'has-error' );

			}

			$.ajax({
				url        : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				type       : 'POST',
				dataType   : 'JSON',
				data       : {
					action    : 'validate-scholarship-code',
					key       : '<?php echo wp_create_nonce( 'validate-scholarship' ); ?>',
					tour_id   : validatebutton.data( 'tour-id' ),
					signup_id : validatebutton.data( 'signup-id' ),
					code      : scholarship.val()
				},
				beforeSend : function() {

					validatebutton.text( '<?php _e( 'Validating ...', 'myworldclass' ); ?>' );

				},
				success    : function( response ) {

					if ( response == 0 || response == '-1' ) {

						alert( '<?php _e( 'Session Timeout. Please reload this page and try again.', 'myworldclass' ); ?>' );
						validatebutton.text( buttonlabel );

					}
					else {

						if ( response.success ) {

							alert( response.data.message );
							$( '#discount' ).empty().text( response.data.discount );
							$( '#final-price' ).empty().text( response.data.final );
							validatebutton.remove();
							scholarship.attr( 'readonly', 'readonly' );

						}
						else {

							alert( '<?php _e( 'Invalid Scholarship Code.', 'myworldclass' ); ?>' );
							scholarship.val( '' );
							scholarship.parent().parent().addClass( 'has-error' );

						}

					}

				}
			});

		});

	});

});
</script>
<?php

		}

	endwhile; else :
?>
	<p>Tour Not Found</p>
<?php

	endif;

?>
				</div>
			</div>
		</article>
	</div>
</div>
<?php get_footer(); ?>
