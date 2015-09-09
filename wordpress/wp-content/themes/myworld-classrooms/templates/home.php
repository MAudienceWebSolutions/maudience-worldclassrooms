<?php 
/**
 * Template Name: Home
 */
$form_title = carbon_get_the_post_meta('crb_form_title');
$form = carbon_get_the_post_meta('crb_gravity_form');
$world_class_title = carbon_get_the_post_meta('crb_world_class_title');
$world_classes = carbon_get_the_post_meta('crb_world_classes', 'complex');
$tours_title = carbon_get_the_post_meta('crb_education_tours_title');
$tours_background = carbon_get_the_post_meta('crb_education_tours_background');
$tours = carbon_get_the_post_meta('crb_education_tours', 'complex');
$tour_style = '';
if ( $tours_background ) {
	$src = wp_get_attachment_url($tours_background);
	$tour_style = 'style="background-image: url(\'' . $src . '\');"';
}
$testimonials_title = carbon_get_the_post_meta('crb_testimonial_title');
$testimonials = carbon_get_the_post_meta('crb_testimonials', 'complex');
$callout_message = carbon_get_the_post_meta('crb_callout_message');
$callout_btn_text = carbon_get_the_post_meta('crb_callout_btn_text');
$callout_link = carbon_get_the_post_meta('crb_callout_link');
$callout_images = carbon_get_the_post_meta('crb_callout_images', 'complex');

get_header();
?>

<?php if ( has_post_thumbnail() ): ?>
	<div class="intro intro-primary">
		<div class="intro-image">
			<?php the_post_thumbnail(); ?>
		</div><!-- /.intro-image -->
		
		<?php if ($form || $form_title ): ?>
			<div class="intro-content">
				

				<?php echo do_shortcode( '[myworld_find_tour_form]' ); ?>
			</div><!-- /.intro-content -->
		<?php endif ?>
	</div><!-- /.intro -->
<?php endif ?>

<div class="main">
	<?php if ( $world_classes || $world_class_title ): ?>
		<section class="section section-difference">
			<div class="shell">
				<?php if ( $world_class_title ): ?>
					<h3 class="section-title">
						<?php echo apply_filters('the_title', $world_class_title); ?>
					</h3><!-- /.section-title -->
				<?php endif ?>
				
				<?php if ( $world_classes ): ?>
					<div class="section-content">
						<?php foreach ($world_classes as $class): ?>
							<div class="col col-1of4">
								<div class="widget widget_feature">
									<?php if ( $class['crb_world_class_icon']): ?>
										<div class="img">
											<?php echo wp_get_attachment_image( $class['crb_world_class_icon'] , 'world_class_icon'); ?>
										</div><!-- /.img -->
									<?php endif ?>
									
									<?php if ($class['crb_world_class_title']): ?>
										<h4 class="widget-title">
											<a href="<?php echo esc_url($class['crb_world_class_link']) ?>">
												<?php echo apply_filters('the_title', $class['crb_world_class_title']) ?>
											</a>
										</h4><!-- /.widget-title -->
									<?php endif ?>
									
									<?php if ($class['crb_world_class_content']): ?>
										<?php echo wpautop($class['crb_world_class_content']) ?>
									<?php endif ?>
									
									<a class="link-more" href="<?php echo esc_url($class['crb_world_class_link']) ?>"><?php echo _e('Learn More', 'crb'); ?></a> 
								</div><!-- /.widget widget_feature -->
							</div><!-- /.col col-1of4 -->
						<?php endforeach ?>
					</div><!-- /.section-content -->
				<?php endif ?>
			</div><!-- /.shell -->
		</section><!-- /.section section-difference -->
	<?php endif ?>
	
	<?php if ( $tours_title || $tours ): ?>	
		<section class="section section-tours" <?php echo $tour_style; ?>>
			<div class="shell">
				<?php if ( $tours_title ): ?>
					<h3 class="section-title">
						<?php echo apply_filters('the_title', $tours_title); ?>
					</h3><!-- /.section-title -->
				<?php endif ?>
				
				<?php if ( $tours ): ?>
					<div class="section-content">
						<?php foreach ($tours as $tour ): ?>
							<div class="col col-1of4">
								<div class="widget widget_tour">
									<?php if ( $tour['crb_education_tour_icon']): ?>
										<a href="<?php echo esc_url($tour['crb_education_tour_link']); ?>">
											<?php echo wp_get_attachment_image( $tour['crb_education_tour_icon'] , 'tours'); ?>
										</a>
									<?php endif ?>
									
									<?php if ($tour['crb_education_tour_title']): ?>
										<h4 class="widget-title">
											<a href="<?php echo esc_url($tour['crb_education_tour_link']); ?>">
												<?php echo apply_filters('the_title', $tour['crb_education_tour_title'] ); ?>
											</a>
										</h4><!-- /.widget-title -->
									<?php endif ?>
									
									<a href="<?php echo esc_url($tour['crb_education_tour_link']); ?>" class="link-more"><?php _e('Learn More', 'crb') ?></a>
								</div><!-- /.widget widget_tour -->
							</div><!-- /.col col-1of4 -->
						<?php endforeach ?>
					</div><!-- /.section-content -->
				<?php endif ?>
			</div><!-- /.shell -->
		</section><!-- /.section section-tours -->
	<?php endif ?>
	
	<?php if ( $testimonials || $testimonials_title ): ?>
		<section class="section section-testimonials">
			<div class="shell">
				<?php if ( $testimonials_title ): ?>
					<h3 class="section-title">
						<?php echo apply_filters('the_title', $testimonials_title) ?>
					</h3><!-- /.section-title -->
				<?php endif ?>
				
				<?php if ( $testimonials ): ?>
					<div class="section-content">
						<?php foreach ($testimonials as $testimonial): ?>
							<div class="col col-1of3">
								<div class="widget widget_testimonial">
									<?php if ( $testimonial['crb_testimonial_quote'] ): ?>
										<blockquote>“<?php echo $testimonial['crb_testimonial_quote'] ?>”</blockquote>
									<?php endif ?>
									
									<?php if ( $testimonial['crb_testimonial_author'] ): ?>
										<h6><?php echo $testimonial['crb_testimonial_author']; ?></h6>
									<?php endif ?>
									
									<?php if ($testimonial['crb_testimonial_author_position']): ?>
										<span><?php echo $testimonial['crb_testimonial_author_position'] ?></span>
									<?php endif ?>
								</div><!-- /.widget widget_testimonial -->
							</div><!-- /.col col-1of3 -->
						<?php endforeach ?>
				<?php endif ?>
			</div><!-- /.shell -->
		</section><!-- /.section section-testimonials -->
	<?php endif ?>
	
	<?php if ( $callout_images || $callout_message || $callout_btn_text ): ?>
		<div class="callout">
			<?php if ( $callout_images ): ?>
				<div class="gallery gallery-columns-4">
					<?php foreach ($callout_images as $callout_image): ?>
					    <figure class="gallery-item">
					        <div class="gallery-icon">
					            <a href="<?php echo esc_url($callout_image['crb_callout_image_link']) ?>">
					                <?php echo wp_get_attachment_image( $callout_image['crb_callout_image'] , 'callout_gallery', 0 , 'class="attachment-thumbnail"'); ?>
					            </a>
					        </div>
					    </figure>
					<?php endforeach ?>
				</div>		
			<?php endif ?>
			
			<?php if ( $callout_message || $callout_btn_text ): ?>
				<div class="callout-content">
					<div class="shell">
						<?php if ( $callout_message ): ?>
							<h3 class="callout-title">
								<?php echo apply_filters('the_title', $callout_message ); ?>
							</h3><!-- /.callout-title -->
						<?php endif ?>
						
						<?php if ( $callout_btn_text && $callout_link ): ?>
							<a href="<?php echo esc_url($callout_link); ?>" class="btn"><?php echo $callout_btn_text; ?></a>
						<?php endif ?>
					</div><!-- /.shell -->
				</div><!-- /.callout-content -->
			<?php endif ?>
		</div><!-- /.callout -->
	<?php endif ?>
</div><!-- /.main -->

<?php get_footer(); ?>