<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-edit-view-options',
	__('View Options', 'radykal')
);

?>

<table class="form-table radykal-settings-form">
	<tbody>

		<?php

		radykal_output_option_item( array(
				'type' => 'section-title',
				'title' => '<span class="fpd-admin-app-tip">Print-Ready Export</span>',
				'description' => __('Define the necessary information for the print-ready export in the <a href="https://admin.fancyproductdesigner.com" target="_blank">ADMIN solution</a>. When setting a print size, a rectangular printing box with the same aspect-ratio will be added to the canvas. When exporting the order with the print-ready export in the ADMIN solution, only the content in the printing box will be visible in the exported file!', 'radykal'),
			)
		);

		radykal_output_option_item( array(
				'id' 			=> 'output_format',
				'title' 		=> 'Print Size',
				'type' 			=> 'select',
				'class'			=> 'radykal-select2',
				'css' 		=> 'width: 100%;',
				'description' 	=> __('Select from predefined print sizes or enter a custom size.', 'radykal'),
				'default'		=> '',
				'options' 		=> array(
					'' => __('None', 'radykal'),
					'man' => __('Set Custom Size', 'radykal'),
					'841x1189' => 'A0 (841x1189 mm ; 33.11x46.81 in)',
					'594x841' => 'A1 (594x841 mm ; 23.39x33.11 in)',
					'420x594' => 'A2 (420x594 mm ; 16.54x23.39 in)',
					'297x420' => 'A3 (297x420 mm ; 11.69x16.54 in)',
					'210x297' => 'A4 (210x297 mm ; 8.27x11.69 in)',
					'148x210' => 'A5 (148x210 mm ; 5.83x8.27 in)',
					'105x148' => 'A6 (105x148 mm ; 4.13x5.83 in)',
					'74x105' => 'A7 (74x105 mm ; 2.91x4.13 in)',
					'52x74' => 'A8 (52x74 mm ; 2.05x2.91 in)',
					'37x52' => 'A9 (37x52 mm; 1.46x2.05 in)',
					'26x37' => 'A10 (26x37 mm ; 1.02x1.46 in)',
					'18x26' => 'A11 (18x26 mm ; 0.71x1.02 in)',
					'13x18' => 'A12 (13x18 mm ; 0.51x0.71 in)',
					'89x51' => __('Standard US & Canada Business Card (89x51 mm ; 3.5x2 in)', 'radykal'),
					'85x55' => __('Standard EU Business Card (85x55 mm ; 3.35x2.2 in)', 'radykal'),
					'76x25' => __('Micro Business Card (76x25 mm ; 3x1 in)', 'radykal'),
					'57x57' => __('Square Business Card (57x57 mm ; 2.25x2.25 in)', 'radykal'),
					'83x51' => __('Folded Business Card (83x51 mm ; 3.25x2 in)', 'radykal'),
					'140x216' => __('Half Letter (140x216 mm ; 5.5x8.5  in)', 'radykal'),
					'216x279' => __('Letter (216x279 mm ; 8.5x11  in)', 'radykal'),
					'216x356 ' => __('Legal (216x356  mm ; 8.5x14  in)', 'radykal'),
					'127x203' => __('Junior Legal (127x203 mm ; 5x8  in)', 'radykal'),
					'279x432' => __('Ledger/Tabloid	 (279x432 mm ; 11x17  in)', 'radykal'),
				)
			)
		);

		radykal_output_option_item( array(
				'id' => 'output_width',
				'title' => __('Width in MM', 'radykal'),
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => '',
				'custom_attributes' => array(
					'min' 	=> 0,
					'step' 	=> 1
				)
			)
		);

		radykal_output_option_item( array(
				'id' => 'output_height',
				'title' => __('Height in MM', 'radykal'),
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => '',
				'custom_attributes' => array(
					'min' 	=> 0,
					'step' 	=> 1
				)
			)
		);

		radykal_output_option_item( array(
				'id' => 'bleed',
				'title' => __('Bleed in MM', 'radykal'),
				'description' 	=> __('The print size will be increased by the bleed.', 'radykal'),
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => '0',
				'custom_attributes' => array(
					'min' 	=> 0,
					'step' 	=> 1
				)
			)
		);

		radykal_output_option_item( array(
				'id' => 'printing_box_visibility',
				'title' => __('Show Printing Box', 'radykal'),
				'type' => 'checkbox',
				'default' => 'no',
				'description' 	=> __('Show printing box to customers in frontend.', 'radykal'),
			)
		);

		radykal_output_option_item( array(
				'id' => 'use_printing_box_as_bounding_box',
				'title' => __('Printing Box as Bounding Box', 'radykal'),
				'type' => 'checkbox',
				'default' => 'no',
				'description' 	=> __('When an element does not have an individual bounding box, the printing box will be used as bounding box.', 'radykal'),
			)
		);

		radykal_output_option_item( array(
				'type' => 'section-title',
				'title' => 'Canvas Size'
			)
		);

		radykal_output_option_item( array(
				'id' => 'stageWidth',
				'title' => 'Canvas Width',
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => __('Canvas width from UI-Layout/Product Options', 'radykal'),
				'description' => __('For the best performance keep it under 4000px.', 'radykal'),
			)
		);

		radykal_output_option_item( array(
				'id' => 'stageHeight',
				'title' => 'Canvas Height',
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => __('Canvas height from UI-Layout/Product Options', 'radykal'),
				'description' => __('For the best performance keep it under 4000px.', 'radykal'),
			)
		);

		radykal_output_option_item( array(
				'id' => 'auto_calc_canvas_size',
				'type' => 'button',
				'title' => __('Auto-Calc Size By Print Size', 'radykal'),
				'placeholder' => __('Auto-Calc Size', 'radykal'),
				'description' => __('Calculates a proper canvas size by the print size, so the printing box covers the whole canvas in order to export the whole canvas content.', 'radykal'),
				'class' => 'button-secondary'
			)
		);

		radykal_output_option_item( array(
				'type' => 'section-title',
				'title' => 'Miscellanous'
			)
		);

		radykal_output_option_item( array(
				'id' => 'designs_parameter_price',
				'title' => 'Custom Image Price',
				'type' => 'number',
				'placeholder' => fpd_get_option( 'fpd_designs_parameter_price' ),
				'description' => __('This price will be used for custom added images.', 'radykal')
			)
		);

		radykal_output_option_item( array(
				'id' => 'custom_texts_parameter_price',
				'title' => 'Custom Text Price',
				'type' => 'number',
				'placeholder' => fpd_get_option( 'fpd_custom_texts_parameter_price' ),
				'description' => __('This price will be used for custom added texts.', 'radykal')
			)
		);

		radykal_output_option_item( array(
				'id' => 'max_price',
				'title' => 'Maximum View Price',
				'type' => 'number',
				'placeholder' => -1,
				'description' => __('The maximum price that will be charged for the view. -1 will disable this option.', 'radykal')
			)
		);

		radykal_output_option_item( array(
				'id' => 'design_categories',
				'title' => 'Design Categories',
				'type' 		=> 'multiselect',
				'class'		=> 'radykal-select2',
				'placeholder' => __('Choose design categories'),
				'description' => __('You can choose specific design categories for this view.', 'radykal'),
				'css'		=> 'width: 100%',
				'options' => fpd_output_top_level_design_cat_options(false)
			)
		);

		radykal_output_option_item( array(
				'id' => 'disable_image_upload',
				'title' => 'Disable Image Module',
				'type' => 'checkbox',
				'default' => 'no'
			)
		);

		radykal_output_option_item( array(
				'id' => 'disable_custom_text',
				'title' => 'Disable Text Module',
				'type' => 'checkbox',
				'default' => 'no'
			)
		);

		radykal_output_option_item( array(
				'id' => 'disable_designs',
				'title' => 'Disable Designs Module',
				'type' => 'checkbox',
				'default' => 'no'
			)
		);

		radykal_output_option_item( array(
				'id' => 'optional_view',
				'title' => 'Optional View',
				'type' => 'checkbox',
				'default' => 'no',
				'description' => __('The view is optional, the user must unlock the view and the prices for all element will be added to the total product price.', 'radykal')
			)
		);

		?>

	</tbody>
</table>

<?php
	FPD_Admin_Modal::output_footer(
		__('Save', 'radykal')
	);
?>