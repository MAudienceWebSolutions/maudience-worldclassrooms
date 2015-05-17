<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div <?php post_class() ?>>
		<h2><?php the_title(); ?></h2>
		<small><?php the_time('F jS, Y') ?></small>
		<div class="entry">
			<?php the_content(); ?>
			
			<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		</div><!-- /div.entry -->
	</div> <!-- /div.post -->
	
	<?php comments_template(); ?>

	<div class="navigation">
		<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
		<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
	</div>
	
<?php endwhile; ?>
<?php endif; ?>