<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div class="wrapper">
		<header class="header">
			<div class="shell">
				<a href="<?php echo home_url( '/' ); ?>" class="logo"><?php bloginfo( 'name' ); ?></a>

				<div class="header-content">
					<?php if ( has_nav_menu( 'navigation' ) ): ?>
						<nav class="nav-access">
							<?php 
							wp_nav_menu(array(
								'container' => 'ul',
								'container_class' => 'menu',
								'theme_location' => 'navigation',
							));
							?>
						</nav><!-- /.nav-access -->
					<?php endif ?>

					<?php if ( has_nav_menu( 'main-menu' ) ): ?>
						<nav class="nav">
							<a href="#" class="btn-menu"><span></span></a>
							<?php 
							wp_nav_menu(array(
								'container' => 'ul',
								'container_class' => 'menu',
								'theme_location' => 'main-menu',
							));
							?>
						</nav><!-- /.nav -->
					<?php endif ?>
				</div><!-- /.header-content -->
			</div><!-- /.shell -->
		</header><!-- /.header -->