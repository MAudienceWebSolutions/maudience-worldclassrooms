<?php get_header(); ?>

<?php if ( is_search() || get_post_type() != 'post' ): ?>
	<?php 
	$classes = "intro";
	
	if ( has_post_thumbnail() ) {
		$classes .= ' intro-teritary';
	} else {
		$classes .= ' intro-secondary';
	}
	?>
	<div <?php post_class($classes) ?>>
		<?php get_template_part('fragments/thumbnail'); ?>
		
		<div class="intro-content">
			<div class="shell">
				<?php crb_the_title('<h2 class="intro-title">', '</h2><!-- /.intro-title -->'); ?>
			</div><!-- /.shell -->
		</div><!-- /.intro-content -->
	</div><!-- /.intro -->
<?php endif ?>

<div class="main">
	<div class="shell">
		<?php get_sidebar(); ?>

		<div class="content">
			<?php
			if ( is_single() || is_page() ) {
				get_template_part('loop','single');
			} else {
				get_template_part('loop');
			}
			?>
		</div><!-- /.content -->
	</div><!-- /.shell -->
</div><!-- /.main -->

<?php get_footer(); ?>