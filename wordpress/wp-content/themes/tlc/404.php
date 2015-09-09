<?php get_header(); ?>

    <div class="shell">
        <!-- Main -->
        <div id="main">
        	<!-- Content -->
            <div id="content" class="no-sidebar">
                <div class="post">
                   <h2 class="pagetitle"><?php _e('Error 404 - Not Found'); ?></h2>
					
					<p><?php printf(__('Please check the URL for proper spelling and capitalization. If you\'re having trouble locating a destination, try visiting the <a href="%1$s">home page</a>'), get_option('home')); ?></p>
                </div>
            </div>
            <!-- End Content -->
            <div class="cl">&nbsp;</div>
        </div>
        <!-- End Main -->

<?php get_footer(); ?>