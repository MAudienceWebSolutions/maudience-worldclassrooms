<?php 
/**
 * Template Name: Destination
 */
get_header();
$button_text = carbon_get_the_post_meta('crb_about_btn_text');
$button_link = carbon_get_the_post_meta('crb_about_btn_link');

$destinations = get_terms('destination');

?>

<div class="intro intro-secondary">
	<?php get_template_part('fragments/thumbnail'); ?>
	
	<?php get_template_part('fragments/title'); ?>
</div><!-- /.intro -->

<div class="main main-destinations">
	<article class="article article-destination">
		<div class="shell">
			<?php foreach ($destinations as $destination): ?>
				<header class="article-head">
					<h2 class="article-title">
						<?php echo $destination->name; ?>
					</h2><!-- /.article-title -->
				</header><!-- /.article-head -->
					
				<?php 
				$tours = new WP_Query(array(
					'post_type' => 'tour',
					'order' => 'ASC',
					'posts_per_page' => -1,
					'tax_query' => array(
						array(
							'taxonomy' => 'destination',
							'field'    => 'slug',
							'terms'    => $destination->slug,
						),
					),
				));
				?>
				<?php if ($tours->have_posts()) : ?>
					<div class="article-body">
						<div class="article-entry">
							<ul class="destinations">
								<?php while ($tours->have_posts()) : $tours->the_post(); ?>
									<?php 
									$meta = carbon_get_the_post_meta('crb_meta');
									$image = carbon_get_the_post_meta('crb_image'); 
									?>

									<li class="destination">
										<?php if ( $image ): ?>
											<div class="destination-image">
												<a href="<?php the_permalink(); ?>">
													<?php echo wp_get_attachment_image( $image , 'destination'); ?>
												</a>
											</div><!-- /.intro-image -->
										<?php endif ?>
										
										<div class="destination-content">
											<p class="destination-meta">
												<?php if ($meta): ?>
													<span><?php echo $meta ?></span>
												<?php endif ?>
												
												<a href="<?php the_permalink(); ?>" class="link-more"><?php _e('See Tour', 'crb') ?></a>
											</p><!-- /.destination-meta -->

											<?php crb_the_title('<h4><a href="' . get_the_permalink() . '">', '</a></h4>') ?>

											<?php the_excerpt(); ?>
										</div><!-- /.destination-content -->
									</li><!-- /.destination -->
								<?php endwhile; ?>
							</ul><!-- /.destinations -->
						</div><!-- /.article-entry -->
					</div><!-- /.article-body -->
				<?php endif; ?>
			<?php endforeach ?>
			<?php wp_reset_postdata(); ?>
		</div><!-- /.shell -->
	</article><!-- /.article article-single -->
	
	<?php get_template_part('fragments/bottom'); ?>
</div><!-- /.main -->

<?php get_footer(); ?>