<?php get_header(); ?>
	
    <div class="shell">
        <!-- Main -->
        <div id="main">
        	<!-- Content -->
            <div id="content" class="no-sidebar">
                
                <h2 class="pagetitle">
					<?php if (is_category()) { ?>
						Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category
					<?php } elseif( is_tag() ) { ?>
						Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;
					<?php } elseif (is_day()) { ?>
						Archive for <?php the_time('F jS, Y'); ?>
					<?php } elseif (is_month()) { ?>
						Archive for <?php the_time('F, Y'); ?>
					<?php } elseif (is_year()) { ?>
						Archive for <?php the_time('Y'); ?>
					<?php } elseif (is_author()) { ?>
						Author Archive
					<?php } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
						Blog Archives
					<?php } ?>
				</h2>

				<?php get_template_part('loop', 'archive') ?>	
                
            </div>
            <!-- End Content -->
            <div class="cl">&nbsp;</div>
        </div>
        <!-- End Main -->

<?php get_footer(); ?>