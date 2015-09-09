<?php 
$button_text = carbon_get_the_post_meta('crb_about_btn_text');
$button_link = carbon_get_the_post_meta('crb_about_btn_link');
$destinations = get_terms('destination');

$title = carbon_get_the_post_meta('crb_title');
$callout_text = carbon_get_the_post_meta('crb_bar_text');
$callout_link = carbon_get_the_post_meta('crb_bar_link');
$callout_btn_text = carbon_get_the_post_meta('crb_bar_btn_text');

$column = array('left', 'right');
$columns = array();
foreach ($column as $key) {
	$columns[$key]['title'] = carbon_get_the_post_meta('crb_' . $key . '_column_title');
	$columns[$key]['content'] = carbon_get_the_post_meta('crb_' . $key . '_column_content');
}

$empty = array_filter($columns);

$private = carbon_get_the_post_meta('crb_information', 'crb');
if ( $private ) {
	$classes = 'intro-private';
} else {
	$classes = 'intro-tour';
}

get_header();

global $post;
?>
<div class="intro intro-teritary <?php echo $classes; ?>">
	<?php get_template_part('fragments/thumbnail'); ?>
	
	<?php if ( $title ): ?>	
		<div class="intro-content">
			<div class="shell">
				<h2 class="intro-title">
					<?php echo crb_colorize_word($title); ?>
				</h2><!-- /.intro-title -->
				
				<?php if ( mywclass_is_user_attending( $post->ID ) ): ?>
					<div class="intro-inner">
						<?php mywclass_show_attendee_message( $post->ID ); ?>
					</div><!-- /.intro-inner -->
				<?php endif ?>
			</div><!-- /.shell -->
		</div><!-- /.intro-content -->
	<?php endif ?>
	
		<div class="intro-callout">
			<div class="shell">
				<p>Join Us!</p>
				<a href="<?php echo esc_url( get_permalink( $post->ID ) . 'signup' ) ?>" class="btn btn-quarinary">Enroll Now</a>
			</div><!-- /.shell -->
		</div><!-- /.intro-callout -->
</div><!-- /.intro -->

<div class="main main-tour">
	<?php if ( !empty($empty) ): ?>
		<article class="article article-tour">
			<div class="shell">
				<?php foreach ($columns as $key => $column): ?>

					<?php if ($key === 'left'): ?>
						<div class="article-content">
					<?php else: ?>
						<aside class="article-aside">
					<?php endif ?>

						<?php if ( $column['content'] || $column['title'] ): ?>
								<?php if ( $column['title'] ): ?>
									<header class="article-head">
										<h2 class="article-title">
											<?php echo apply_filters('the_title', $column['title']) ?>
										</h2><!-- /.article-title -->
									</header><!-- /.article-head -->
								<?php endif ?>
								
								<?php if ( $column['content'] ): ?>
									<div class="article-body">
										<div class="article-entry">
											<?php echo apply_filters('the_content', $column['content']); ?>

											<?php if ( $key === 'right'): ?>
												<?php if ( ($btn_text = carbon_get_the_post_meta('crb_right_column_btn_text')) && ($btn_link = carbon_get_the_post_meta('crb_right_column_link')) ): ?>
													<a href="<?php echo esc_url($btn_link); ?>" class="btn btn-primary"><?php echo $btn_text ?></a>
												<?php endif ?>
											<?php endif ?>
										</div><!-- /.article-entry -->
									</div><!-- /.article-body -->
								<?php endif ?>
						<?php endif ?>

					<?php if ($key === 'left'): ?>
						</div><!-- /.article-content -->
					<?php else: ?>
						</aside>
					<?php endif ?>

				<?php endforeach ?>
			</div><!-- /.shell -->
		</article><!-- /.article article-tour -->
	<?php endif ?>
	
	<?php get_template_part('fragments/gallery'); ?>
	
	<?php get_template_part('fragments/includes'); ?>
	
	<?php get_template_part('fragments/bottom'); ?>
</div><!-- /.main -->

<?php get_footer(); ?>