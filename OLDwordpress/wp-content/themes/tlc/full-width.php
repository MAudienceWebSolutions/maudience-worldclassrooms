<?php 

/*

Template Name: Full Width

*/

get_header(); 

?>

<?php if (have_posts()) : ?>

	<?php while (have_posts()) : the_post(); ?>

	    <div class="shell">

	        <!-- Main -->

	        <div id="main">

	            <!-- Content -->

	            <div id="full-width">

	                <div class="post">

	                    <?php the_content(); ?>

	                </div>

	            </div>
        </div>    
	            <!-- End Content -->	

	        <!-- End Main -->

	<?php endwhile; ?>

<?php endif; ?>



<?php get_footer(); ?>