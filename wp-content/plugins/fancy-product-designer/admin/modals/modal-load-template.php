<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-load-template',
	__('Load a template', 'radykal'),
	''
);

?>

<?php

	$templates = FPD_Template::get_templates();

	if(sizeof($templates) == 0)
		echo '<p>'.__('No templates created. You can create a template via the action bar in a product list item.', 'radykal').'</p>';

	echo '<ul class="fpd-modal-list">';
	foreach($templates as $template) {
		// double quotes required
		echo FPD_Admin_Manage_Products::get_template_link_html($template->ID, $template->title);
	}
	echo '</ul>';

?>
</ul>
<?php

	FPD_Admin_Modal::output_footer();

?>
