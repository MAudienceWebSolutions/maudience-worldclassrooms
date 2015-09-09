<?php 
$middle = '';
if ( is_page_template('templates/centered.php') ) {
	$middle = 'bottom_section';
	$section_class = 'section-callout';
	$btn_class = 'btn-teritary';
} else if ( in_array( get_post_type(), array( 'tour', 'tours' ) ) ) {
	$middle = 'section';
	$section_class = 'section-callout-secondary';
	$btn_class = 'btn-primary';

} else if ( is_page_template('templates/destination.php') ) {
	$middle = 'destination';
	$section_class = 'section-callout';
	$btn_class = 'btn-quinary';
} else if ( is_page_template('templates/gallery.php') ) {
	$middle = 'gallery_bottom_section';
	$section_class = 'section-callout';
	$btn_class = 'btn-teritary';
}
$bottom_title = carbon_get_the_post_meta('crb_' . $middle . '_title');
$bottom_btn = carbon_get_the_post_meta('crb_' . $middle . '_btn_text');
$bottom_link = carbon_get_the_post_meta('crb_' . $middle . '_link');
?>

<?php if ($bottom_title || $bottom_btn ): ?>
	<section class="section <?php echo $section_class; ?>">
		<div class="shell">
			<?php if ( $bottom_title ): ?>
				<h3 class="section-title">
					<?php echo apply_filters('the_title', $bottom_title) ?>
				</h3><!-- /.section-title -->
			<?php endif ?>
			
			<?php if ( $bottom_btn && $bottom_link ): ?>
				<a href="<?php echo esc_url($bottom_link) ?>" class="btn <?php echo $btn_class; ?>"><?php echo $bottom_btn ?></a>
			<?php endif ?>
		</div><!-- /.shell -->
	</section><!-- /.section section-callout -->
<?php endif ?>