<?php 
$size = '';
if (is_search() || is_page_template('templates/faq.php') || is_page_template('templates/destination.php') || is_page_template('templates/contact.php') ) {
	$size = 'banner';
}
?>


	<div class="intro-image">
		<?php if (has_post_thumbnail()): ?>
			<?php the_post_thumbnail($size); ?>
		<?php else: ?>
			<?php $default_image = carbon_get_theme_option('crb_default_img'); ?> 
			<?php if ($default_image): ?>
				<?php echo wp_get_attachment_image( $default_image , 'banner'); ?>
			<?php endif ?>
		<?php endif ?>
	</div><!-- /.intro-image -->
