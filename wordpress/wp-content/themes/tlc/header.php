<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>



<head profile="http://gmpg.org/xfn/11">

	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="google-site-verification" content="ZKGnxoRXz3iYVpFtZYOoqnk7UCTptrii88p08H_M76Q" />


	<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>



	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon.ico" />

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/flexslider.css" type="text/css" media="all" />



	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>



	<?php wp_head(); ?>



	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.flexslider.js"></script>

	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/functions.js"></script>

</head>

<body <?php body_class(); ?>>

	<div id="wrapper">

            <div id="login-bar">
              <div class="shell">
              	<div class="topnav">
                	<ul>
                    	<li><span>Phone: <?php echo get_option('wcphone'); ?></span></li>
                  		<?php if ( ! is_user_logged_in() ) : ?>
                  		<li><a target="_self" href="<?php echo get_option('wclogin'); ?>">Login</a></li>
                  		<li><a target="_self" href="<?php echo get_option('wcregister'); ?>">Register for a Tour</a></li>
                  		<?php else : ?>
                  		<li><a target="_self" href="<?php echo home_url( '/my-account/' ); ?>">My Account</a></li>
                  		<li><a target="_self" href="<?php echo wp_logout_url( home_url() ); ?>" title="Logout">Logout</a></li>
                  		<?php endif; ?>
                    </ul>                    
                </div>
              </div>
            </div>
	    <!-- Header -->

	    <div id="header">

	        <div class="shell">

	            <h1 id="logo"><a href="<?php echo home_url('/'); ?>"><?php bloginfo('name') ?></a></h1>

	            <!-- Navigation -->

	            <?php wp_nav_menu('theme_location=main-menu&container_id=navigation&link_before=<span>&link_after=</span>&fallback_cb='); ?>

	            <div class="cl">&nbsp;</div>

	            <!-- End Navigation -->

<!--	            <div class="socials">

	                <a target="_blank" href="<?php echo get_option('facebook'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/f.png" alt="" /></a>

	                <a target="_blank" href="<?php echo get_option('linkedin'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/in.png" alt="" /></a>

	                <a target="_blank" href="<?php echo get_option('twitter'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/t.png" alt="" /></a>

	            </div>
-->
	        </div>

	    </div>

	    <!-- End Header -->