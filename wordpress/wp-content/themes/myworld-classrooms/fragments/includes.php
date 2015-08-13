<?php 
$includes = carbon_get_the_post_meta('crb_includes', 'complex');
$includes_title = carbon_get_the_post_meta('crb_includes_title');
?>

<?php if ( $includes || $includes_title ): ?>
	<section class="section section-included">
		<div class="shell">
			<?php if ( $includes_title ): ?>
				<h3 class="section-title">
					<?php echo apply_filters('the_title', $includes_title); ?>
				</h3><!-- /.section-title -->
			<?php endif ?>
			
			<?php if ( $includes ): ?>
				<div class="section-content">
					<?php foreach ($includes as $include): ?>
						<div class="col col-1of5">
							<div class="feature">
								<a href="<?php echo esc_url($include['crb_include_link']) ?>">
									<i class="ico-<?php echo $include['crb_include'] ?>"></i>
									
									<?php if ($include['crb_include_name']): ?>
										<span>
											<?php echo $include['crb_include_name'] ?>
										</span>
									<?php endif ?>
								</a>
							</div><!-- /.feature -->
						</div><!-- /.col col-1of5 -->
					<?php endforeach ?>
				</div><!-- /.section-content -->
			<?php endif ?>
		</div><!-- /.shell -->
	</section><!-- /.section section-included -->
<?php endif ?>