<?php 

/*

Template Name: 2 Column

*/

get_header(); 

?>

<?php if (have_posts()) : ?>

	<?php while (have_posts()) : the_post(); ?>

	    <div class="shell">

	        <!-- Main -->

	        <div id="main">

	            <!-- Content -->

	            <div id="content-2col">

	                <div class="post">

	                    <?php the_content(); ?>

						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>

	                </div>

	            </div>

	            <!-- End Content -->
				
                <div id="sidebar-2col">
                <div class="widget-title">Learn More</div>
                <nav class="sidebar-icons">
                	<ul>
                    	<li class="peace-of-mind"><a href="http://www.myworldclassrooms.com/peace-of-mind/">Peace of Mind</a></li>
                        <li class="financial-aid"><a href="http://www.myworldclassrooms.com/financial-aid/">Financial Aid</a></li>
                        <li class="faqs"><a href="http://www.myworldclassrooms.com/request-a-quote/">Frequently Asked Questions</a></li>
                        <li class="general-information"><a href="http://www.myworldclassrooms.com/request-consultation/">General Information</a></li>
                     </ul>
                </nav>
                	            <?php dynamic_sidebar('Side Sidebar'); ?>
				</div>
                
	            <div class="cl">&nbsp;</div>

	        </div>

	        <!-- End Main -->

	<?php endwhile; ?>

<?php endif; ?>



<?php get_footer(); ?>