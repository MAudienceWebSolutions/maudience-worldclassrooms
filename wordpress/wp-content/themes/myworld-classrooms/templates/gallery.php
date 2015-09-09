<?php 
/**
 * Template Name: Gallery
 */
get_header();
the_post();
$title = carbon_get_the_post_meta('crb_gallery_title');
$second_title = carbon_get_the_post_meta('crb_second_title');
$second_content = carbon_get_the_post_meta('crb_second_content');
$tours = carbon_get_the_post_meta('crb_tours', 'complex');
?>

<div class="intro intro-teritary intro-destinations">
	<?php get_template_part('fragments/thumbnail'); ?>
	
	<?php if ( $title ): ?>
		<div class="intro-content">
			<div class="shell">
				<h2 class="intro-title">
					<?php echo crb_colorize_word($title); ?>
				</h2><!-- /.intro-title -->
			</div><!-- /.shell -->
		</div><!-- /.intro-content -->
	<?php endif ?>
</div><!-- /.intro -->

<div class="main main-destinations">
	<article class="article article-gallery">
		<div class="shell">
			<?php the_title('<header class="article-head"><h2 class="article-title">', '</h2><!-- /.article-title --></header><!-- /.article-head -->') ?>
							
			<div class="article-body">
				<div class="article-entry">
					<?php the_content(); ?>
				</div><!-- /.article-entry -->
			</div><!-- /.article-body -->
		</div><!-- /.shell -->
	</article><!-- /.article article-tour -->
	
	<?php if ( $tours ): ?>
		<section class="section section-destinations">
			<div class="shell">
				<?php foreach ($tours as $tour): ?>
					<div class="col col-1of3">
						<div class="widget widget_destination">
							<?php if ( $tour['crb_tour_image'] ): ?>	
								<a href="<?php echo esc_url($tour['crb_tour_link']) ?>">
									<?php echo wp_get_attachment_image( $tour['crb_tour_image'] , 'tours-gallery'); ?>
								</a>
							<?php endif ?>
							
							<?php if ($tour['crb_tour']): ?>
								<h5>
									<a href="<?php echo esc_url($tour['crb_tour_link']) ?>"><?php echo $tour['crb_tour']; ?></a>
								</h5>
							<?php endif ?>
							
							<a href="<?php echo esc_url($tour['crb_tour_link']) ?>"><?php _e('Learn More', 'crb'); ?></a>
						</div><!-- /.widget widget_destination -->
					</div><!-- /.col col-1of3 -->
				<?php endforeach ?>
			</div><!-- /.shell -->
		</section><!-- /.section section-destinations -->
	<?php endif ?>
	
	<?php if ( $second_content || $second_title ): ?>
		<article class="article article-gallery">
			<div class="shell">
				<?php if ( $second_title ): ?>
					<header class="article-head">
						<h2 class="article-title">
							<?php echo $second_title ?>
						</h2><!-- /.article-title -->
					</header><!-- /.article-head -->
				<?php endif ?>
				
				<?php if ( $second_content ): ?>		
					<div class="article-body">
						<div class="article-entry">
							<?php echo apply_filters('the_content', $second_content ) ?>
							
							<?php if ( ($btn = carbon_get_the_post_meta('crb_btn_text')) && ($link = carbon_get_the_post_meta('crb_btn_link')) ): ?>
								<a href="<?php echo esc_url($link); ?>" class="link-more"><?php echo $btn ?></a>
							<?php endif ?>
						</div><!-- /.article-entry -->
					</div><!-- /.article-body -->
				<?php endif ?>		
			</div><!-- /.shell -->
		</article><!-- /.article article-tour -->
	<?php endif ?>

	<?php get_template_part('fragments/bottom'); ?>
</div><!-- /.main -->

<?php get_footer(); ?>