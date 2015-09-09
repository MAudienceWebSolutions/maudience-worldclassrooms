<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
	
		<article class="article article-single">
			<header class="article-head">
				<h2 class="article-title">
					<a href="<?php echo the_permalink() ?>">
						<?php the_title(); ?>
					</a>
				</h2><!-- /.article-title -->
			</header><!-- /.article-head -->

			<div class="article-body">
				<div class="article-entry">
					<?php the_excerpt(); ?>
				</div><!-- /.article-entry -->
			</div><!-- /.article-body -->
		</article><!-- /.article article-single -->

	<?php endwhile; ?>

	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		<div class="pagination">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries', 'crb')); ?></div>
			<div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;', 'crb')); ?></div>
		</div>
	<?php endif; ?>
	
<?php else : ?>
	<article class="article article-single">
		<header class="article-head">
			<h2 class="article-title">
				<?php if ( is_category() ) { // If this is a category archive
					printf( __("Sorry, but there aren't any posts in the %s category yet.", 'crb'), single_cat_title('',false) );
				} else if ( is_date() ) { // If this is a date archive
					_e("Sorry, but there aren't any posts with this date.", 'crb');
				} else if ( is_author() ) { // If this is a category archive
					$userdata = get_user_by('id', get_queried_object_id());
					printf( __("Sorry, but there aren't any posts by %s yet.", 'crb'), $userdata->display_name );
				} else if ( is_search() ) { // If this is a search
					_e('No posts found. Try a different search?', 'crb');
				} else {
					_e('No posts found.', 'crb');
				} ?>
			</h2>
		</header><!-- /.article-head -->
		
		<div class="article-body">
			<div class="article-entry">
				<?php get_search_form(); ?>
			</div><!-- /.article-entry -->
		</div><!-- /.article-body -->
	</article>
<?php endif; ?>