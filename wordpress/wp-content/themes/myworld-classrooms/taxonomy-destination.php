<?php get_header();
$button_text = carbon_get_the_post_meta('crb_about_btn_text');
$button_link = carbon_get_the_post_meta('crb_about_btn_link');

?>

<div class="intro intro-secondary">
	<?php get_template_part('fragments/thumbnail'); ?>
	
	<div class="intro-content">
		<div class="shell">
			<h2 class="intro-title"><?php echo single_term_title( '', false ) . ' Destinations'; ?></h2><!-- /.intro-title -->
		</div><!-- /.shell -->
	</div><!-- /.intro-content -->
</div><!-- /.intro -->

<div class="main main-destinations">
	<article class="article article-destination">
		<div class="shell">
			<header class="article-head">
				<h2 class="article-title">
					<?php echo single_term_title( '', false ); ?>
				</h2><!-- /.article-title -->
			</header><!-- /.article-head -->
			<?php if ( have_posts() ) : ?>
			<div class="article-body">
				<div class="article-entry">
					<ul class="destinations">
						<?php while ( have_posts() ) : the_post(); ?>
<?php 

							$meta = carbon_get_the_post_meta('crb_meta');
							$image = carbon_get_the_post_meta('crb_image'); 

?>
						<li class="destination">
							<?php if ( $image ) : ?>
							<div class="destination-image">
								<a href="<?php the_permalink(); ?>">
									<?php echo wp_get_attachment_image( $image , 'destination' ); ?>
								</a>
							</div><!-- /.intro-image -->
							<?php endif; ?>
							<div class="destination-content">
								<p class="destination-meta">
									<?php if ( $meta ) : ?>
									<span><?php echo $meta ?></span>
									<?php endif; ?>
									<a href="<?php the_permalink(); ?>" class="link-more"><?php _e('See Tour', 'crb') ?></a>
								</p><!-- /.destination-meta -->

								<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

								<?php the_excerpt(); ?>
							</div><!-- /.destination-content -->
						</li><!-- /.destination -->
						<?php endwhile; ?>
					</ul><!-- /.destinations -->
				</div><!-- /.article-entry -->
			</div><!-- /.article-body -->
			<?php endif; ?>
		</div><!-- /.shell -->
	</article><!-- /.article article-single -->
	
	<?php get_template_part('fragments/bottom'); ?>
</div><!-- /.main -->

<?php get_footer(); ?>