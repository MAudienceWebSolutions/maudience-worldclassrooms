<?php
$sections = carbon_get_the_post_meta('crb_sections', 'complex');
if ( !$sections) {
	return;
}

$sections_with_images = array(
	'travel',
	'passion',
	'connected'
);

$sections_with_background = array(
	'customizable',
	'mountain',
);

$sections_with_title_class = array(
	'travel',
	'passion',
	'connected',
	'limits'
);
foreach ($sections as $section) {
	$title_class = '';
	$section_background = '';
	if (in_array($section['crb_section'], $sections_with_title_class)) {
		$title_class = 'class="section-title"';
	}

	if (in_array($section['crb_section'], $sections_with_background)) {
		$src = wp_get_attachment_url($section['crb_section_image']);
		$section_background = 'style="background-image: url(\'' . $src . '\');"';
	}
	?>
	<section <?php echo $section_background; ?> class="section section-<?php echo $section['crb_section'] ?>">
		<div class="shell">
			<?php if ( in_array($section['crb_section'], $sections_with_images) && $section['crb_section_image']): ?>
				<div class="section-image">
					<?php echo wp_get_attachment_image( $section['crb_section_image'] , 'about'); ?>
				</div><!-- /.section-image -->
			<?php endif ?>
			
			<?php if ($section['crb_section_title'] || $section['crb_section_content']): ?>
				<div class="section-content">
					<?php if ( $section['crb_section_title'] ): ?>
						<h2 <?php echo $title_class; ?>>
							<?php echo crb_colorize_word($section['crb_section_title']); ?>
						</h2>
					<?php endif ?>
					
					<?php if ($section['crb_section_content']): ?>
						<?php echo apply_filters('the_content', $section['crb_section_content']) ?>
					<?php endif ?>
				</div><!-- /.section-content -->
			<?php endif ?>

			<?php if (($section['crb_section'] == 'limits') && $section['crb_section_items'] ): ?>
				<div class="section-inner">
					<?php foreach ($section['crb_section_items'] as $item): ?>
						<div class="col col-1of3">
							<div class="section-image">
								<?php if ($item['crb_section_item_image']): ?>
									<?php echo wp_get_attachment_image( $item['crb_section_item_image'] , 'about_list'); ?>
								<?php endif ?>
								
								<?php if ($item['crb_section_item_title']): ?>
									<span class="section-caption"><?php echo apply_filters('the_title', $item['crb_section_item_title']); ?></span>
								<?php endif ?>
							</div><!-- /.section-image -->
						</div><!-- /.col col-1of3 -->
					<?php endforeach ?>
				</div><!-- /.section-inner -->
			<?php endif ?>
		</div><!-- /.shell -->
	</section><!-- /.section section-travel -->
	<?php
}