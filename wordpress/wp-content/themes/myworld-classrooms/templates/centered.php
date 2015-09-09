<?php 
/**
 * Template Name: Centered
 */
$steps = carbon_get_the_post_meta('crb_steps', 'complex');
$steps_title = carbon_get_the_post_meta('crb_steps_title');

get_header();
?>
<div class="intro intro-teritary intro-handle">
	<?php get_template_part('fragments/thumbnail'); ?>
	
	<?php get_template_part('fragments/title'); ?>
</div><!-- /.intro -->

<div class="main main-handle">
	<?php if ( $steps || $steps_title || carbon_get_the_post_meta('crb_gallery', 'complex') ): ?>
		<section class="section section-steps">
			<?php if ( $steps_title ): ?>
				<h3 class="section-title">
					<?php echo apply_filters('the_title', $steps_title) ?>
				</h3><!-- /.section-title -->
			<?php endif ?>
			
			<?php if ( $steps ): ?>
				<div class="section-content">
					<div class="shell">
						<?php $count = 1; ?>
						<?php foreach ($steps as $step): ?>
							<div class="col col-1of4">
								<div class="step">
									<a href="#step<?php echo $count; ?>">
										<span><?php echo $count++; ?></span>
										
										<Strong><?php echo $step['crb_step_nav_title'] ?></Strong>
									</a>
								</div><!-- /.step -->
							</div><!-- /.col col-1of4 -->
						<?php endforeach ?>
					</div><!-- /.shell -->
				</div><!-- /.section-content -->
			<?php endif ?>
			
			<?php get_template_part('fragments/gallery'); ?>
		</section><!-- /.section section-steps -->
	<?php endif ?>
	
	<?php if ( $steps ): ?>
		<?php $count = 1; ?>
		<?php foreach ($steps as $step): ?>
			<section class="section section-step" id="step<?php echo $count; ?>">
				<div class="shell">
					<span><?php printf( __("Step %s", 'crb'), $count++ ); ?></span>
					
					<?php if ( $step['crb_step_title'] ): ?>
						<h3 class="section-title">
							<?php echo apply_filters('the_title', $step['crb_step_title']) ?>
						</h3><!-- /.section-title -->
					<?php endif ?>
					
					<?php if ($step['crb_step_desc']): ?>
						<?php echo apply_filters('the_content', $step['crb_step_desc']); ?>
					<?php endif ?>
				</div><!-- /.shell -->
			</section><!-- /.section section-step -->
		<?php endforeach ?>
	<?php endif ?>
	
	<?php get_template_part('fragments/includes'); ?>
	
	<?php get_template_part('fragments/bottom'); ?>
</div><!-- /.main -->

<?php get_footer(); ?>