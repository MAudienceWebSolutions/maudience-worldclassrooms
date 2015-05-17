<?php get_header(); ?>
	
    <div class="shell">
        <!-- Main -->
        <div id="main">
        	<!-- Content -->
            <div id="content" class="no-sidebar">
                
                <?php get_template_part( 'loop', 'single' ) ?>
                
            </div>
            <!-- End Content -->
            <div class="cl">&nbsp;</div>
        </div>
        <!-- End Main -->

<?php get_footer(); ?>