<?php get_header(); ?>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
	    <div class="shell">
	        <!-- Main -->
	        <div id="main">
	            <!-- Sidebar -->
	            <?php if ($post->post_parent) {
					$sub_pages = get_pages('sort_column=menu_order&parent=' . $post->post_parent . '&child_of=' . $post->post_parent);
				} else {
					$sub_pages = get_pages('sort_column=menu_order&parent=' . $post->ID . '&child_of=' . $post->ID);
				} ?>
				<?php  ?>
	            <div id="sidebar">
	            	<?php if ($sub_pages) : $count = 0; ?>
		                <div class="side-nav">
		                    <ul>
		                    	<?php foreach ($sub_pages as $sub_page) : $count++; ?>
		                      	  <li><a href="<?php echo get_permalink($sub_page->ID); ?>" class="subnav-<?php echo $count; echo ($sub_page->ID == $post->ID) ? ' active' : '' ?>"><?php echo $sub_page->post_title ?></a></li>
		                        <?php endforeach ?>
		                    </ul>
		                </div>
		            <?php endif ?>
	            </div>
	            <!-- End Sidebar -->
	            <!-- Content -->
	            <div id="content">
	                <div class="post">
	                    <?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
	                </div>
	            </div>
	            <!-- End Content -->
	            <div class="cl">&nbsp;</div>
	        </div>
	        <!-- End Main -->
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>