<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<section class="section-comments">
	<?php if ( have_comments() ) : ?>
		
		<h3><?php comments_number( __('No Responses', 'crb'), __('One Response', 'crb'), __('% Responses', 'crb') ); ?></h3>

		<ol class="commentlist">
			<?php wp_list_comments( array(
				'callback' => 'crb_render_comment'
			) );
			?>
		</ol>

		<?php if ( ( $max_pages = get_comment_pages_count() ) > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="paging">
				<?php global $cpage; ?>
				<?php 
				if ( !$cpage ) {
					$cpage = $max_pages;
				}
				?>
				<span class="paging-label">
					<?php printf( __("$cpage of $max_pages", 'crb') ); ?>
				</span>
				
				<a href="<?php the_permalink(); ?>comment-page-1/#comments" class="paging-first"></a>
				
				<?php $args = array(
					'prev_text'          => '&#8249',
					'next_text'          => '&#8250',
					'mid_size'           => 2,
				); ?>
				
				<?php echo paginate_comments_links( $args ); ?>

				<a href="<?php the_permalink(); ?>comment-page-<?php echo $max_pages; ?>/#comments" class="paging-last"></a>
			</div><!-- /.paging -->
		<?php endif; ?>

	<?php else : ?>
		
		<?php if ( ! comments_open() ) : ?>
			<p class="nocomments"><?php _e('Comments are closed.', 'crb'); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php 
	comment_form(array(
		'title_reply' => __('Leave a Reply', 'crb'),
		'comment_notes_after' => '',
	));
	?>
</section><!-- /.section -->
