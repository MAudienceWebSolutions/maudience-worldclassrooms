<?php get_header(); ?>
	
    <div class="shell">
        <!-- Main -->
        <div id="main">
        	<!-- Content -->
            <div id="content" class="no-sidebar">
                
                <h2 class="pagetitle">Search Results for &quot;<?php echo get_search_query(); ?>&quot;</h2>
	
				<?php get_template_part('loop', 'search') ?>
                
            </div>
            <!-- End Content -->
            <div class="cl">&nbsp;</div>
        </div>
        <!-- End Main -->

<?php get_footer(); ?>