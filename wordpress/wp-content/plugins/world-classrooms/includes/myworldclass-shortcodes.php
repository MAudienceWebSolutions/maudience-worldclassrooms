<?php
if ( ! defined( 'MYWORLDCLASS_VERSION' ) ) exit;

/**
 * Shortcode: Find Tour
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mywclass_render_find_tour_form' ) ) :
	function mywclass_render_find_tour_form( $atts ) {

		extract( shortcode_atts( array(
			'title' => 'Enroll Now'
		), $atts ) );

		ob_start();

?>
<style type="text/css">
#fint-tour-via-code-form {
	display: block;
	width: 100%;
	margin: 0 0 0 0;
	padding: 0 0 0 0;
	text-align: center;
}
#fint-tour-via-code-form input {
	font-size: 16px;
	line-height: 32px;
	padding: 2px 6px;
	border: 1px solid white;
}
#fint-tour-via-code-form #submit-to-view-tour-id {
	border: 1px solid #ff9000;
	background-color: #ff9000;
	padding: 2px 12px;
	text-transform: uppercase;
	color: white;
}
</style>
<form method="post" action="" id="fint-tour-via-code-form">
	<h2><?php echo esc_attr( $title ); ?></h2>
	<input type="hidden" name="find" value="tour" />
	<label for="enter-trip-id">
		<input type="text" placeholder="Trip ID" name="trip_id" id="enter-trip-id" value="" /> <input type="submit" id="submit-to-view-tour-id" value="Go" />
	</label>
</form>
<?php

		$content = ob_get_contents();
		ob_end_clean();

		return $content;

	}
endif;

?>