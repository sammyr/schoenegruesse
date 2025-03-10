<?php
require_once(FPD_PLUGIN_ADMIN_DIR.'/views/modal-shortcodes.php');
?>
<br>
<div class="wrap" id="fpd-manage-status">

	<h1><?php esc_html_e( 'Status', 'radykal'); ?></h1>

	<table class="fpd-status-table ui striped table">
		<thead>
			<tr>
				<th colspan="2">
					<?php esc_html_e( 'Server Environment', 'radykal'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="four wide">
					<em><?php esc_html_e('PHP Version', 'radykal'); ?></em>
					<span data-variation="tiny" data-tooltip="<?php esc_attr_e('The PHP version installed on your server.', 'radykal'); ?>">
						<i class="mdi mdi-information-outline icon"></i>
					</span>
				</td>
				<td class="twelve wide">
					<?php echo PHP_VERSION ?>
				</td>
			</tr>
			<tr>
				<td>
					<em><?php esc_html_e('Web Server Info', 'radykal'); ?></em>
					<span data-variation="tiny" data-tooltip="<?php esc_attr_e('The current server software that is used on your web hosting.', 'radykal'); ?>"><i class="mdi mdi-information-outline icon"></i></span>
				</td>
				<td>
					<?php esc_html_e( $_SERVER['SERVER_SOFTWARE'] ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<em><?php esc_html_e('Memory Limit', 'radykal'); ?></em>
					<span data-variation="tiny" data-tooltip="<?php esc_attr_e('The maximum amount of memory in bytes that a script is allowed to allocate.', 'radykal'); ?>"><i class="mdi mdi-information-outline icon"></i></span>
				</td>
				<td>
					<?php esc_html_e( ini_get('memory_limit') ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<em><?php esc_html_e('POST Max. Size', 'radykal'); ?></em>
					<span data-variation="tiny" data-tooltip="<?php esc_attr_e('The largest filesize that can be sent via one POST.', 'radykal'); ?>"><i class="mdi mdi-information-outline icon"></i></span>
				</td>
				<td>
					<?php esc_html_e( ini_get('post_max_size') ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<em><?php esc_html_e('Uploaded Max. Filesize', 'radykal'); ?></em>
					<span data-variation="tiny" data-tooltip="<?php esc_attr_e('The maximum size of an uploaded file.', 'radykal'); ?>"><i class="mdi mdi-information-outline icon"></i></span>
				</td>
				<td>
					<?php esc_html_e( ini_get('upload_max_filesize') ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<em><?php esc_html_e('Max. Execution Time', 'radykal'); ?></em>
					<span data-variation="tiny" data-tooltip="<?php esc_attr_e('The maximum time in seconds a script is allowed to run.', 'radykal'); ?>"><i class="mdi mdi-information-outline icon"></i></span>
				</td>
				<td>
					<?php esc_html_e( ini_get('max_execution_time') ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<em><?php esc_html_e('Max. Input Variables', 'radykal'); ?></em>
					<span data-variation="tiny" data-tooltip="<?php esc_attr_e('How many input variables may be accepted.', 'radykal'); ?>"><i class="mdi mdi-information-outline icon"></i></span>
				</td>
				<td>
					<?php esc_html_e( ini_get('max_input_vars') ); ?>
				</td>
			</tr>
			<?php

				$classes_funcs = array(
					array(
						'type' => 'class',
						'name' => 'ZipArchive',
						'info' => __('Necessary for zipping/unzipping exported or imported products.', 'radykal')
					),
					array(
						'type' => 'function',
						'name' => 'getimagesize',
						'info' => __('Checks if file is an image.', 'radykal')
					),
					array(
						'type' => 'function',
						'name' => 'exif_read_data',
						'info' => __('Gets the orientation of an uploaded image. Required to rotate images uploaded from mobile devices correctly.', 'radykal')
					),
					array(
						'type' => 'function',
						'name' => 'curl_exec',
						'info' => __('Writes files on the server.', 'radykal')
					),
					array(
						'type' => 'function',
						'name' => 'file_put_contents',
						'info' => __(' Writes data to a file.', 'radykal')
					),
					array(
						'type' => 'INI',
						'name' => 'allow_url_fopen',
						'info' => __('Allows to read remote files.', 'radykal')
					),
					array(
						'type' => 'class',
						'name' => 'Imagick',
						'info' => __('Imagick is not enabled on your server.', 'radykal')
					)
				);

				foreach($classes_funcs as $cf) {

					$success_label = __( 'Installed', 'radykal' );
					$error_label = __( 'Not Installed', 'radykal' );

					if( $cf['type'] == 'INI' ) {
						$success_label = __( 'Activated', 'radykal' );
						$error_label = __( 'Disabled', 'radykal' );
					}

					if( $cf['type'] == 'class' && class_exists($cf['name']) )
						$status = '<span class="ui green tiny basic label"><span class="mdi mdi-check icon"></span> '. $success_label .' </span>';
					else if( $cf['type'] == 'function' && function_exists($cf['name']) )
						$status = '<span class="ui green tiny basic label"><span class="mdi mdi-check icon"></span> '. $success_label .' </span>';
					else if( $cf['type'] == 'INI' && (bool) ini_get($cf['name']) )
						$status = '<span class="ui green tiny basic label"><span class="mdi mdi-check icon"></span> '. $success_label .' </span>';
					else
						$status = '<span class="ui red tiny basic label"><span class="mdi mdi-close icon"></span> '. $error_label .' </span>';

					echo '<tr><td><em>'. $cf['type'].'</em>: '.$cf['name'].'<span data-variation="tiny" data-tooltip="'. esc_attr($cf['info']) .'"><i class="mdi mdi-information-outline icon"></i></span></td><td>'. $status .'</td></tr>';

				}

				do_action( 'fpd_status_server_table_end' );
			?>
		</tbody>
	</table>
	<p class="description"><?php _e( 'If any class or function is missing, please install these. Otherwise Fancy Product Designer may not work correctly. If you do not know how to install/activate the PHP classes/functions, please ask your server hoster!', 'radykal' ); ?></p>

</div>