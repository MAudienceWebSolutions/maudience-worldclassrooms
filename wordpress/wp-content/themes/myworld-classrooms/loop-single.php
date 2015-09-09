<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
			<article class="article article-single clear">
				<header class="article-head">
					<?php if ( is_page() ): ?>
						<?php $subtitle = carbon_get_the_post_meta('crb_subtitle'); ?>

						<?php if ($subtitle): ?>
							<h2 class="article-title">
								<?php echo apply_filters('the_title', $subtitle); ?>
							</h2><!-- /.article-title -->
						<?php endif ?>
					<?php else: ?>
						<?php the_title('<h2 class="article-title">', '</h2><!-- /.article-title -->'); ?>
					<?php endif ?>

					<?php get_template_part('fragments/post-meta'); ?>
				</header><!-- /.article-head -->
				
				<div class="article-body">
					<div class="article-entry">
						<?php the_content(); ?>
					</div><!-- /.article-entry -->
				</div><!-- /.article-body -->
			</article><!-- /.article article-single -->

			<?php if ( is_single() && get_post_type() === 'post' ): ?>
				<?php comments_template(); ?>
			<?php endif ?>
	<?php endwhile; ?>
<?php endif; ?>