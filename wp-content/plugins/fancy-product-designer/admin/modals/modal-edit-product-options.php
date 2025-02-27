<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-edit-product-options',
	__('Product Options', 'radykal'),
	''
);

?>

<table class="form-table">
	<tbody>

		<?php

		radykal_output_option_item( array(
				'id' => 'stageWidth',
				'title' => 'Canvas Width',
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => __('Canvas width from UI Layout', 'radykal'),
				'description' => __('For the best performance keep it under 4000px.', 'radykal'),
			)
		);

		radykal_output_option_item( array(
				'id' => 'stageHeight',
				'title' => 'Canvas Height',
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => __('Canvas height from UI Layout', 'radykal'),
				'description' => __('For the best performance keep it under 4000px.', 'radykal'),
			)
		);

		$fpd_products = FPD_Product::get_products( array(
			'cols' => "ID, title",
			'order_by' 	=> "ID ASC",
		) );

		foreach( $fpd_products as $fpd_product) {
			$fpd_layout_options[$fpd_product->ID] = '#' . $fpd_product->ID . ' - ' . $fpd_product->title;
		}

		radykal_output_option_item( array(
				'id' => 'layouts_product_id',
				'title' => 'Layouts',
				'type' 		=> 'select',
				'css' => 'width: 100%',
				'class' => 'radykal-select2',
				'placeholder' => __('Select a product', 'radykal'),
				'description' => __('The views of the selected product will be used as layout items in the layouts module.', 'radykal'),
				'options' => $fpd_layout_options
			)
		);

		?>

	</tbody>
</table>

<?php
	FPD_Admin_Modal::output_footer(
		__('Set', 'radykal')
	);
?>
