<?php 
$gallery = carbon_get_the_post_meta('crb_gallery', 'complex');
$div_class = '';
if ( is_page_template('templates/centered.php') ) {
	$div_class = 'gallery-columns-7';
} else if ( get_post_type() === 'tours') {
	$div_class = 'gallery-columns-4';
}
?>

<?php if ( $gallery ): ?>
	<div class="gallery <?php echo $div_class; ?>">
		<?php foreach ($gallery as $image): ?>
		    <figure class="gallery-item">
		        <div class="gallery-icon">
		            <a href="<?php echo esc_url($image['crb_gallery_image_link']) ?>">
		            	<?php echo wp_get_attachment_image( $image['crb_gallery_image'] , 'destination-gallery'); ?>
		            </a>
		        </div>
		    </figure>
		<?php endforeach ?>
	</div>
<?php endif ?>