<form action="<?php echo home_url('/'); ?>" class="search-form" method="get" role="search">
    <label>
        <span class="screen-reader-text"><?php _e('Search for:', 'crb'); ?></span>
		
        <input type="search" title="Search for:" name="s" value="" placeholder="" class="search-field">
    </label>
    <input type="submit" value="<?php echo esc_attr(__('Search', 'crb')); ?>" class="search-submit screen-reader-text">
</form>