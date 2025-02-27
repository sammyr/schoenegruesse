<div id="fpd-element-props-sidebar">

	<div id="fpd-sidebar-actions">
		<div id="fpd-element-props-sidebar-close">
			<i class="fpd-admin-icon-close fpd-admin-tooltip"  title="<?php _e('Close Sidebar', 'radykal'); ?>" style="font-size: 20px;"></i>
		</div>
		<div id="fpd-element-props-sidebar-switcher">
			<i class="fpd-admin-icon-dock-left fpd-admin-tooltip"  title="<?php _e('Dock to left', 'radykal'); ?>"></i>
			<i class="fpd-admin-icon-dock-right fpd-admin-tooltip"  title="<?php _e('Dock to right', 'radykal'); ?>"></i>
		</div>
	</div>

	<form class="fpd-accordion radykal-settings-form">

		<div class="fpd-accordion-tab">

			<input id="fpd-tab-general" class="fpd-ignore" type="radio" name="accordion_sidebar" checked="checked" />
			<label for="fpd-tab-general"><?php _e( 'General', 'radykal' ); ?></label>
			<div class="fpd-content">

				<table>
					<tbody>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Left', 'radykal' ); ?></label>
								</div>
								<input type="number" step="1" name="left" placeholder="0" value="" class="fpd-only-numbers">
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Top', 'radykal' ); ?></label>
								</div>
								<input type="number" step="1" name="top" placeholder="0" value="" class="fpd-only-numbers">
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Width', 'radykal' ); ?></label>
								</div>
								<input type="number" step="1" name="width" placeholder="0" value="" class="fpd-scale-to fpd-text-hidden fpd-ignore">
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Height', 'radykal' ); ?></label>
								</div>
								<input type="number" step="1" name="height" placeholder="0" value="" class="fpd-scale-to fpd-text-hidden fpd-ignore">
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'X-Origin', 'radykal' ); ?></label>
								</div>
								<div class="fpd-button-group fpd-originX">
									<input type="hidden" name="originX" value="center" class="fpd-radio-buttons" />
									<span data-value="left">
										<i class="fpd-admin-icon-originX-left"></i>
									</span>
									<span data-value="center">
										<i class="fpd-admin-icon-originX-center"></i>
									</span>
									<span data-value="right">
										<i class="fpd-admin-icon-originX-right"></i>
									</span>
								</div>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Y-Origin', 'radykal' ); ?></label>
								</div>
								<div class="fpd-button-group fpd-originY">
									<input type="hidden" name="originY" value="center" class="fpd-radio-buttons" />
									<span data-value="top">
										<i class="fpd-admin-icon-originY-top"></i>
									</span>
									<span data-value="center">
										<i class="fpd-admin-icon-originY-center"></i>
									</span>
									<span data-value="bottom">
										<i class="fpd-admin-icon-originY-bottom"></i>
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Price', 'radykal' ); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Always use a dot as the decimal separator!', 'radykal'); ?>"></i>
								</div>
								<input type="number" step="0.01" min="0" name="price" placeholder="0" value="" class="fpd-prevent-whitespace fpd-only-numbers fpd-allow-dots">
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Replace', 'radykal' ); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Elements with the same replace value are replaced by each other.', 'radykal'); ?>"></i>
								</div>
								<input type="text" name="replace" value="" class="fpd-upload-zone-hidden large-text">
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Stay On Top', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="topped" value="1">
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Auto-Select', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="autoSelect" value="1">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Locked', 'radykal'); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('The user needs to unlock the element in Layers module to edit it.', 'radykal'); ?>"></i>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="locked" value="1">
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Exclude In Export', 'radykal'); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Excludes the layer from export.', 'radykal'); ?>"></i>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="excludeFromExport" value="1">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<?php do_action( 'fpd_product_builder_general_options_end', $request_view_id); ?>
					</tbody>
				</table>

			</div><!-- Tab Content -->

		</div><!-- General Tab -->

		<div class="fpd-accordion-tab">

			<input id="fpd-tab-colors" class="fpd-ignore" type="radio" name="accordion_sidebar" />
			<label for="fpd-tab-colors"><?php _e( 'Colors', 'radykal' ); ?></label>
			<div class="fpd-content">

				<table>
					<tbody>

						<tr>
							<td class="fpd-color-options">
								<div class="fpd-prop-title">
									<label><?php _e('Available Colors', 'radykal'); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('One color value: Colorpicker, Multiple color values: Fixed color palette', 'radykal'); ?>"></i>
								</div>
								<input type="text" name="_color" class="fpd-ignore" value="" placeholder="<?php _e('e.g. #000000', 'radykal') ; ?>" size="20" />
								<button class="button button-secondary" id="fpd-add-color"><?php _e('Add', 'radykal') ; ?></button>
								<button class="button button-secondary" id="fpd-empty-colors"><?php _e('Empty', 'radykal') ; ?></button>
								<div id="fpd-color-tags"></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Color Picker per path', 'radykal'); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Every path in the SVG gets an own color picker.', 'radykal'); ?>"></i>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="colors" value="1" class="fpd-svg-options" data-checkedsel="" data-uncheckedsel=".fpd-color-options:first">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<tr>
							<td class="fpd-color-options">
								<div class="fpd-prop-title">
									<?php _e('Color Link Group', 'radykal'); ?>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('You can set color links between elements.', 'radykal'); ?>"></i>
								</div>
								<input type="text" name="colorLinkGroup" value="" class="widefat fpd-upload-zone-hidden" />
							</td>
						</tr>

						<?php do_action( 'fpd_product_builder_color_options_end', $request_view_id); ?>
					</tbody>
				</table>

			</div><!-- Tab Content -->

		</div><!-- Colors Tab -->

		<div class="fpd-accordion-tab">

			<input id="fpd-tab-custom-props" class="fpd-ignore" type="radio" name="accordion_sidebar" />
			<label for="fpd-tab-custom-props"><?php _e( 'Customizable Properties', 'radykal' ); ?></label>
			<div class="fpd-content">

				<table>
					<tbody>
						<tr>
							<td class="description" colspan="2">
								<span class="fpd-upload-zone-show"><?php _e( 'These properties will be applied to the element, that is added into the upload zone.', 'radykal' ); ?></span>
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Movable', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="draggable" value="1">
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Scalable', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="resizable" value="1">
									<div><span></span></div>
								</label>
							</td>


						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Rotatable', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="rotatable" value="1">
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Removable', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="removable" value="1" class="fpd-upload-zone-hidden">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Layer Depth Changeable', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="zChangeable" value="1">
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Allow Unproportional Scaling', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="uniScalingUnlockable" value="1">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Advanced Editing', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="advancedEditing" value="1">
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Curvable', 'radykal'); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Allow customer to switch between curvable and normal text.', 'radykal'); ?>"></i>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="curvable" value="1" class="fpd-text-box-hidden">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<?php do_action('fpd_product_builder_custom_props_options_end'); ?>
					</tbody>
				</table>

			</div><!-- Tab Content -->

		</div><!-- Custom Props Tab -->

		<div class="fpd-accordion-tab">

			<input id="fpd-tab-text" class="fpd-ignore" type="radio" name="accordion_sidebar" />
			<label for="fpd-tab-text"><?php _e( 'Text', 'radykal' ); ?></label>
			<div class="fpd-content">

				<table>
					<tbody>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Min. Font Size', 'radykal' ); ?></label>
								</div>
								<input type="number" name="minFontSize" min="1" step="1" placeholder="1" value="" class="fpd-only-numbers">
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Max. Font Size', 'radykal' ); ?></label>
								</div>
								<input type="number" name="maxFontSize" min="1" step="1" placeholder="1000" value="" class="fpd-only-numbers">
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Font Size To Width', 'radykal' ); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('The font size will be automatically adjusted, so the text fits into the defined width.', 'radykal'); ?>"></i>
								</div>
								<input type="number" name="widthFontSize" min="0" step="1" placeholder="0" value="" class="widefat fpd-only-numbers">
							</td>
							<td>
								<div class="fpd-prop-title">
									<?php _e('Text Link Group', 'radykal'); ?>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Link the text content between elements.', 'radykal'); ?>"></i>
								</div>
								<input type="text" name="textLinkGroup" value="" class="widefat" />
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Max. Characters', 'radykal' ); ?></label>
								</div>
								<input type="number" name="maxLength" min="1" step="1" placeholder="0" value="" class="fpd-only-numbers">
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Max. Lines', 'radykal' ); ?></label>
								</div>
								<input type="number" name="maxLines" min="1" step="1" placeholder="0" value="" class="fpd-only-numbers">
							</td>
						</tr>
						<tr>

						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Editable', 'radykal'); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="editable" value="1">
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Charge After Editing', 'radykal' ); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('If the text has price, it will be charged first after the text has been edited.', 'radykal'); ?>"></i>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="chargeAfterEditing" value="1">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="fpd-prop-title">
									<label><?php _e( 'Text Box Width', 'radykal' ); ?></label>
								</div>
								<input type="number" name="width" min="20" step="1" class="widefat fpd-only-numbers" value="">																		</td>
						</tr>
						<?php do_action('fpd_product_builder_text_options_end'); ?>
					</tbody>
				</table>

			</div><!-- Tab Content -->

		</div> <!-- Text Options -->

		<div class="fpd-accordion-tab">

			<input id="fpd-tab-bounding-box" class="fpd-ignore" type="radio" name="accordion_sidebar" />
			<label for="fpd-tab-bounding-box"><?php _e( 'Bounding Box', 'radykal' ); ?></label>
			<div class="fpd-content">

				<table>
					<tbody>
						<tr>
							<td colspan="2">
								<div class="fpd-prop-title">
									<label><?php _e( 'Use another element as bounding box', 'radykal' ); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="bounding_box_control" value="1" class="fpd-ignore"  data-checkedsel="#bounding-box-element" data-uncheckedsel=".boundig-box-custom">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="fpd-prop-title">
									<label><?php _e('Define Bounding Box', 'radykal'); ?></label>
								</div>
								<input type="text" id="bounding-box-element" name="bounding_box_by_other" class="widefat" placeholder="<?php _e('Title of an image element in the same view.', 'radykal'); ?>" style="display: none;" value="" />
							</td>
						</tr>
						<tr class="boundig-box-custom">
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Left', 'radykal' ); ?></label>
								</div>
								<input type="number" name="bounding_box_x" size="3" placeholder="0" value="">
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Top', 'radykal' ); ?></label>
								</div>
								<input type="number" name="bounding_box_y" size="3" placeholder="0" value="">
							</td>
						</tr>
						<tr class="boundig-box-custom">
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Width', 'radykal' ); ?></label>
								</div>
								<input type="number" name="bounding_box_width" size="3" placeholder="0" value="">
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Height', 'radykal' ); ?></label>
								</div>
								<input type="number" name="bounding_box_height" size="3" placeholder="0" value="">
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="fpd-prop-title">
									<label><?php _e( 'Mode', 'radykal' ); ?></label>
								</div>
								<select name="boundingBoxMode" class="widefat">
									<option value="inside" selected=""><?php _e('Inside', 'radykal'); ?></option>
									<option value="clipping"><?php _e('Clipping', 'radykal'); ?></option>
									<option value="limitModify"><?php _e('Limit Modification', 'radykal'); ?></option>
									<option value="none"><?php _e('None', 'radykal'); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>

			</div><!-- Tab Content -->

		</div> <!-- Bounding Box Tab -->

		<div class="fpd-accordion-tab">

			<input id="fpd-tab-upload-zone" class="fpd-ignore" type="radio" name="accordion_sidebar" />
			<label for="fpd-tab-upload-zone"><?php _e( 'Upload Zone', 'radykal' ); ?></label>
			<div class="fpd-content">

				<table>
					<tbody>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Image Uploads', 'radykal' ); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="adds_uploads" value="1" checked="checked">
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Custom Texts', 'radykal' ); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="adds_texts" value="1" checked="checked">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<tr>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e( 'Designs', 'radykal' ); ?></label>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="adds_designs" value="1" checked="checked" data-checkedsel='#fpd-uz-design-cats'>
									<div><span></span></div>
								</label>
							</td>
							<td>
								<div class="fpd-prop-title">
									<label><?php _e('Movable', 'radykal'); ?></label>
									<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('The user can move the upload zone.', 'radykal'); ?>"></i>
								</div>
								<label class="fpd-ad-switch">
									<input type="checkbox" name="uploadZoneMovable" value="1">
									<div><span></span></div>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div class="fpd-prop-title">
									<label><?php _e( 'Scale Mode', 'radykal' ); ?></label>
								</div>
								<select name="scaleMode" class="widefat">
									<option value="fit" selected=""><?php _e('Fit', 'radykal'); ?></option>
									<option value="cover"><?php _e('Cover', 'radykal'); ?></option>
								</select>
							</td>
						</tr>
						<tr id="fpd-uz-design-cats">
							<td colspan="2">
								<div class="fpd-prop-title">
									<label><?php _e( 'Design Categories', 'radykal' ); ?></label>
								</div>
								<select multiple="multiple" name="designCategories[]" class="radykal-select2" style="width: 100%;" data-placeholder="<?php _e('Choose categories.', 'radykal'); ?>">
									<?php fpd_output_top_level_design_cat_options(); ?>
								</select>
							</td>
						</tr>
						<?php do_action('fpd_product_builder_uz_options_end'); ?>
					</tbody>
				</table>

			</div><!-- Tab Content -->

		</div> <!-- Upload Zone Tab -->

	</form><!-- Accordion -->

</div><!-- Sidebar -->
