<?php 
/**
 * Template Name: About
 */
get_header();

$title = carbon_get_the_post_meta('crb_about_title');
$subtitle = carbon_get_the_post_meta('crb_about_subtitle');
$button_text = carbon_get_the_post_meta('crb_about_btn_text');
$button_link = carbon_get_the_post_meta('crb_about_btn_link');
?>

<div class="intro intro-teritary intro-about">
	<?php get_template_part('fragments/thumbnail'); ?>
	
	<?php if ( $title || $subtitle ): ?>
		<div class="intro-content">
			<div class="shell">
				<?php if ( $title ): ?>
					<h2 class="intro-title">
						<?php echo crb_colorize_word($title); ?>
					</h2><!-- /.intro-title -->
				<?php endif ?>

				<?php if ($subtitle): ?>
					<?php echo wpautop($subtitle); ?>
				<?php endif ?>
			</div><!-- /.shell -->
		</div><!-- /.intro-content -->
	<?php endif ?>
</div><!-- /.intro -->

<div class="main main-about">
	<?php get_template_part( 'fragments/sections' ); ?>
	
	<?php the_post(); ?>
	<article class="article article-about">
		<div class="shell">
			<header class="article-head">
				<?php the_title('<h2 class="article-title">', '</h2><!-- /.article-title -->'); ?>
			</header><!-- /.article-head -->

			<div class="article-body">
				<div class="article-entry">
					<?php the_content(); ?>
					
					<?php if ($button_link && $button_text): ?>
						<a href="<?php echo esc_url($button_link); ?>" class="btn"><?php echo $button_text; ?></a>
					<?php endif ?>
				</div><!-- /.article-entry -->
			</div><!-- /.article-body -->
		</div><!-- /.shell -->
	</article><!-- /.article article-about -->
</div><!-- /.main -->

<?php get_footer(); ?>