<?php 
/**
 * Template Name: FAQ
 */
get_header();

$categories = get_terms( 'faq-category' );
$subtitle = carbon_get_the_post_meta('crb_subtitle');

?>

<div class="intro intro-secondary">
	<?php get_template_part('fragments/thumbnail'); ?>

	<?php get_template_part('fragments/title'); ?>
</div><!-- /.intro -->

<div class="main">
	<div class="shell">
		<article class="article article-faq">
			<header class="article-head">
				<?php if ($subtitle): ?>
					<h2 class="article-title">
						<?php echo apply_filters('the_title', $subtitle) ?>
					</h2><!-- /.article-title -->
				<?php endif ?>

				<div class="widget-faq widget_search">
				   	<form action="" class="search-form" method="get" role="search">
					    <label>
					        <span class="screen-reader-text"><?php _e('Search for:', 'crb'); ?></span>
							
					        <input type="search" title="Search for:" name="faq_search" value="" placeholder="" class="search-field">
					    </label>
					    <input type="submit" value="<?php echo esc_attr(__('Search', 'crb')); ?>" class="search-submit screen-reader-text">
					</form>
				</div><!-- /.widget widget_search -->
			</header><!-- /.article-head -->
			
			<?php if ( $categories ): ?>
				<div class="article-body">
					<div class="article-entry">
						<div class="accordion">
							<?php foreach ($categories as $category): ?>
								<div class="accordion-section">
									<div class="accordion-head">
										<h3><?php echo apply_filters('the_title', $category->name ) ?></h3>
									</div><!-- /.accordion-head -->
									
									<?php 
									$args = array(
										'post_type' => 'faq',
										'orderby' => 'menu_order',
										'posts_per_page' => -1,
										'tax_query' => array(
											array(
												'taxonomy' => 'faq-category',
												'field'    => 'slug',
												'terms'    => $category->slug,
											),
										),
									);
									if ( isset($_GET[ 'faq_search' ]) ) {
										$args['s'] = $_GET[ 'faq_search' ];
									}

									$faqs = new WP_Query($args);
									?>

									<?php if ($faqs->have_posts()) : ?>
										<div class="accordion-body">
											<?php while ($faqs->have_posts()) : $faqs->the_post(); ?>
												<?php the_title('<h5>', '</h5>') ?>

												<?php the_content(); ?>
											<?php endwhile; ?>
										</div>
									<?php endif; ?>
								</div><!-- /.accordion-section -->
							<?php endforeach ?>
						</div><!-- /.accordion -->
					</div><!-- /.article-entry -->
				</div><!-- /.article-body -->
			<?php endif ?>
		</article><!-- /.article article-contact -->
	</div><!-- /.shell -->
</div><!-- /.main -->


<?php get_footer(); ?>