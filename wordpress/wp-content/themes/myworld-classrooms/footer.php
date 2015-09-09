<?php
$l_column_title = carbon_get_theme_option('crb_left_column_title'); 
$l_column_content = carbon_get_theme_option('crb_left_column_content'); 
$r_column_title = carbon_get_theme_option('crb_right_column_title');
$socials = carbon_get_theme_option('crb_right_column_socials', 'complex');
$copyright = carbon_get_theme_option('crb_copyright');
?>
		<footer class="footer">
			<div class="footer-content">
				<div class="shell">
					<?php if ( $l_column_content || $l_column_title ): ?>
						<div class="col col-size1">
							<?php if ( $l_column_title ): ?>
								<h6><?php echo apply_filters('the_title' , $l_column_title) ?></h6>
							<?php endif ?>
							
							<?php if ($l_column_content): ?>
								<p class="phone">
									<?php echo $l_column_content; ?>
								</p><!-- /.phone -->
							<?php endif ?>
						</div><!-- /.col col-size1 -->
					<?php endif ?>
					
					<?php if ( $r_column_title || $socials ): ?>
						<div class="col col-size2">
							<?php if ( $r_column_title ): ?>
								<h6><?php echo apply_filters('the_title', $r_column_title); ?></h6>
							<?php endif ?>
							
							<?php if ( $socials ): ?>
								<div class="socials">
									<ul>
										<?php foreach ($socials as $social): ?>
											<li>
												<a target="_blank" href="<?php echo esc_url($social['crb_social_link']); ?>">
													<?php echo wp_get_attachment_image( $social['crb_social_icon'] , 'soc'); ?>
												</a>
											</li>
										<?php endforeach ?>
									</ul>
								</div><!-- /.socials -->
							<?php endif ?>
						</div><!-- /.col col-size2 -->
					<?php endif ?>
					
					<?php 
					for ($i = 1; $i < 4; $i++) { 
						if ( has_nav_menu( 'footer-menu-' . $i ) ) {
							$locations = get_nav_menu_locations();
							$menu = get_term( $locations['footer-menu-' . $i], 'nav_menu' );
							?>
							<div class="col col-size3">
								<h6><?php echo $menu->name; ?></h6>
								<?php
									wp_nav_menu(array(
									'container' => 'ul',
									'menu_class' => 'list-links',
									'theme_location' => 'footer-menu-' . $i,
									'items_wrap' =>'<ul id="%1$s" class="%2$s">%3$s</ul>',
								));
								?>
							</div><!-- /.col col-size1 -->
							<?php
						}
					} 
					?>
				</div><!-- /.shell -->
			</div><!-- /.footer-content -->
			
			<?php if ($copyright): ?>
				<p class="copyright">
					<?php echo do_shortcode($copyright); ?>
				</p><!-- /.copyright -->
			<?php endif ?>
		</footer><!-- /.footer -->
	</div><!-- /.wrapper -->
	<?php wp_footer(); ?>
</body>
</html>