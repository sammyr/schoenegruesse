<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-templates-library',
	__('Templates Library', 'radykal'),
	__('Browse the large templates library and create ready-to-use products from our pre-made templates with just one click. We offer at least one product from each category for free. If you want to use the other premium products, you have to buy the whole category set.', 'radykal')
);

$lib_templates = FPD_Template::get_templates('library');

?>

<div id="fpd-templates-categories">
<?php

foreach($lib_templates as $key => $templates) {
	echo '<a data-target="'.$key.'" href="#">'.$templates->name.'</a>';
}

?>
</div>

<div id="fpd-templates-right-col">

	<div class="fpd-tabs-wrapper">
	<?php
	foreach($lib_templates as $key => $templates) {

		?>
		<div data-tab="<?php esc_attr_e( $key); ?>">

			<?php if( isset($templates->filters) && sizeof($templates->filters) > 0 ): ?>
			<div class="fpd-nav-filters">

				<a href="#" data-filter="all" class="fpd-active"><?php _e('All', 'radykal'); ?></a>
				<?php
				foreach($templates->filters as $filter) {
					echo '<a href="#" data-filter="'. esc_attr( $filter ) .'">'. esc_html( $filter ) .'</a>';
				}
				?>

			</div>
			<?php endif; ?>
			<div class="fpd-templates-grid">
			<?php
			foreach($templates->templates as $template) {

				$total_product_templates++;

				$item_class = '';
				$item_label = __('Free', 'radykal');
				$item_url = '';
				$button_text = __('Create', 'radykal');
				if( isset($template->free) ) {
					$item_url = $template->file_path;
				}
				else {

					if( $template->installed ) {

						$item_label = __('Premium', 'radykal');
						$item_url = $template->file_path;

					}
					else {

						$item_class .= 'fpd-unavailable';
						$item_label = __('Not Installed', 'radykal');
						$button_text = __('Buy Set', 'radykal');
						$item_url = $templates->purchase_url;

					}

				}

				$first_img = is_array($template->images) ? $template->images[0] : $template->images;
				$preview_images = is_array($template->images) ? $template->images : array($template->images);

				if(sizeof($preview_images) > 1) {
					$item_class .= ' fpd-multi-images';
				}

				$item_filter = isset( $template->filter ) ? implode(',', $template->filter) : '';

				echo '<div class="'.$item_class.'" data-filter="'.$item_filter.'" data-label="'.$item_label.'" data-url="'. esc_attr($item_url).'" data-images="'.esc_attr( json_encode($preview_images) ).'" style="background-image: url('. $first_img .');"><label>'.$template->name.'</label><span class="fpd-button">'.$button_text.'</span><div class="fpd-images-nav"><span class="dashicons dashicons-arrow-left-alt2" data-page="prev"></span><span class="dashicons dashicons-arrow-right-alt2" data-page="next"></span></div></div>';
			}

			?>
			</div><!-- .fpd-templates-grid -->

			<?php if($templates->purchase_url): ?>
			<p><a href="<?php esc_attr_e( $templates->purchase_url ); ?>" class="button-primary" target="_blank"><?php  printf(__('Buy %s set', 'radykal'), $templates->name); ?></a></p>
			<?php endif; ?>

		</div><!-- data-tab -->
	<?php
	}
	?>
	</div><!-- .fpd-tabs-wrapper -->

	<p class="description"><?php _e('Need another product template? <a href="https://surveys.hotjar.com/s?siteId=166800&surveyId=125814" target="_blank">Let us know</a> or <a href="https://fancyproductdesigner.com/customization-request/" target="_blank">hire us</a>!', 'radykal'); ?></p>

</div>

<div class="fpd-ui-blocker"></div>

<?php

	FPD_Admin_Modal::output_footer();

?>
