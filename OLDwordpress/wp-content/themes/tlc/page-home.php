<?php 

/*

Template Name: Home

*/

get_header(); 

?>



<?php if (have_posts()) : ?>

	<?php while (have_posts()) : the_post(); ?>

	    <div class="slider-holder">

	        <div class="slider-bg">

	            <div class="shell">

	            	<?php query_posts( 'orderby=menu_order&order=ASC&post_type=slide&meta_key=_slide_image&posts_per_page=-1' ); ?>

	                <!-- Slideshow -->

	                <?php if (have_posts()) : ?>

		                <div class="flexslider">

		            	    <ul class="slides">

		            	    	<?php while (have_posts()) : the_post(); ?>

		            	    		<?php if ($image = get_meta('_slide_image')) : ?>	

				            	    	<li>

				            	    		<img src="<?php echo ecf_get_image_url($image) ?>" alt="" />

				            	    	</li>

		            	    		<?php endif ?>

			            	    <?php endwhile ?>

		            	    </ul>

		            	</div>

	            	<?php endif; ?>

	            	<!-- End Slideshow -->

	            	<?php wp_reset_query(); ?>

	            	<!-- Welcome -->

	                <div class="welcome">

	                    <?php the_content(); ?>

	                </div>

	                <!-- End Welcome -->

	                <div class="cl">&nbsp;</div>

	            </div>

	        </div>

	    </div>

	    <div class="shell">

	        <!-- Service -->

	        <div class="service">

	        	<?php for ($i=1; $i < 7; $i++) : 

		        	$box_page = get_option('home_choose_page_' . $i);

		        	$box_image = get_option('home_image_' . $i);

		        	?>

	        		<?php 
					
					if ($box_page && $box_image) : ?>

			            <div class="box">

			                <a href="<?php echo get_permalink($box_page) ?>">

			                	<img src="<?php echo $box_image ?>" alt="" />

			                	<span class="<?php echo "color-" . $i; ?>"><?php echo get_the_title($box_page) ?></span>

			                </a>  

			                <p><?php echo get_option('home_text_' . $i) ?></p>

			            </div>

	        		<?php endif ?>

	        	<?php endfor ?>

	            <div class="cl">&nbsp;</div>

	        </div>

	        <!-- Service -->

	<?php endwhile; ?>

<?php endif; ?>



<?php get_footer(); ?>