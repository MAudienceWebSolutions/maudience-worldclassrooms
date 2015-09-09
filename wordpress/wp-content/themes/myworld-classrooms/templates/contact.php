<?php 
/**
 * Template Name: Contact
 */
get_header();

$subtitle = carbon_get_the_post_meta('crb_subtitle');
$keys = array('left', 'right');
$columns = array();
foreach ($keys as $key) {
	$columns[$key]['title'] = carbon_get_the_post_meta('crb_' . $key . '_column_title');
	$columns[$key]['content'] = carbon_get_the_post_meta('crb_' . $key . '_column_content');
	$columns[$key]['phone'] = carbon_get_the_post_meta('crb_' . $key . '_column_phone');
	$columns[$key]['email'] = carbon_get_the_post_meta('crb_' . $key . '_column_email');
}
$empty = array_filter($columns);

the_post();
?>

<div class="intro intro-secondary">
	<?php get_template_part('fragments/thumbnail'); ?>

	<?php get_template_part('fragments/title'); ?>
</div><!-- /.intro -->

<div class="main">
	<div class="shell">
		<article class="article article-contact">
			<?php if ($subtitle): ?>
				<header class="article-head">
					<h2 class="article-title">
						<?php echo apply_filters('the_title', $subtitle) ?>
					</h2><!-- /.article-title -->
				</header><!-- /.article-head -->
			<?php endif ?>

			<div class="article-body">
				<div class="article-entry">
					
					<?php the_content(); ?>
					
					<?php if ( !empty($empty) ): ?>
						<div class="article-inner">
							<?php foreach ($columns as $column): ?>
								<?php $column_empty = array_filter($column); ?>

								<?php if ( !empty( $column_empty ) ): ?>
									<div class="col col-1of2">
										<div class="widget widget_contact">
											<?php if ( $column['title'] ): ?>
												<h3 class="widget-title">
													<?php echo apply_filters('the_title', $column['title']) ?>
												</h3><!-- /.widget-title -->
											<?php endif ?>
											
											<?php if ( $column['content'] ): ?>
												<?php echo apply_filters('the_content', $column['content']); ?>
											<?php endif ?>
											
											<?php if ($column['phone'] || $column['email']): ?>
												<div class="widget-inner">
													<?php if ($column['phone']): ?>
														<p>
															<span><?php _e('Phone:', 'crb') ?></span>

															<a href="tel:<?php echo $column['phone'] ?>"><?php echo $column['phone'] ?></a>
														</p>
													<?php endif ?>
													
													<?php if ($column['email']): ?>
														<p>
															<span><?php _e('Email:', 'crb'); ?></span>

															<a href="mailto:<?php echo antispambot($column['email']); ?>"><?php echo antispambot($column['email']); ?></a>
														</p>
													<?php endif ?>
												</div><!-- /.widget-inner -->
											<?php endif ?>
										</div><!-- /.widget widget_contact -->
									</div><!-- /.col col-1of2 -->
								<?php endif ?>
							<?php endforeach ?>
						</div><!-- /.article-inner -->
					<?php endif ?>
				</div><!-- /.article-entry -->
			</div><!-- /.article-body -->
		</article><!-- /.article article-contact -->
	</div><!-- /.shell -->
</div><!-- /.main -->

<?php get_footer(); ?>