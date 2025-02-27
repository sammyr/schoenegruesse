<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin_Manage_Products') ) {

	class FPD_Admin_Manage_Products {

		public function output() {

			global $wpdb;
			$page_links = false;

			//update V3.9.7
			if( isset($_POST['fpd_update_products_to_user']) ) {

				$update_products = FPD_Product::get_products( array(
					'where' 	=> 'user_id IS NULL'
				) );

				foreach( $update_products as $update_product ) {

					$fpd_update_product = new FPD_Product( $update_product->ID );
					$fpd_update_product->update( null, null, null, $_POST['fpd_update_products_to_user']);

				}

				update_option( 'fpd_updater_dokan_hidden', 'yes' );

			}

			if( isset($_POST['fpd_filter_by']) )
				update_option('fpd_admin_filter_by', $_POST['fpd_filter_by']);

			if( isset($_POST['fpd_order_by']) )
				update_option('fpd_admin_order_by', $_POST['fpd_order_by']);

			$filter_by = get_option('fpd_admin_filter_by', 'title');
			$order_by = get_option('fpd_admin_order_by', 'ASC');

			$where = '';
			if( isset($_POST['fpd_search_products_string']) && !empty($_POST['fpd_search_products_string']) )
				$where = "title LIKE '%{$_POST['fpd_search_products_string']}%'";

			$categories = FPD_Category::get_categories( array(
				'order_by' => 'title ASC'
			) );

			require_once( FPD_PLUGIN_ADMIN_DIR.'/class-admin-import.php' );
			?>

			<!-- wrap beginn -->
			<div class="wrap" id="fpd-manage-products">
				<h2>
					<?php _e('Manage Products', 'radykal'); ?>
					<?php fpd_admin_display_version_info(true); ?>
				</h2>

			<?php

			$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
			$limit = 20;
			$offset = ( $pagenum - 1 ) * $limit;
			$total = sizeof( FPD_Product::get_products() );
			$num_of_pages = ceil( $total / $limit );

			$page_links = paginate_links( array(
			    'base' 		=> add_query_arg( 'paged', '%#%' ),
			    'format' 	=> '',
			    'prev_text' => '&laquo;',
			    'next_text' => '&raquo;',
			    'total' 	=> $num_of_pages,
			    'current' 	=> $pagenum
			) );

			$products = FPD_Product::get_products( array(
				'where' 	=> $where,
				'order_by' 	=> $filter_by . ' '. $order_by,
				'limit' 	=> $limit,
				'offset' 	=> $offset
			) );

			//select by category
			if( isset($_GET['category_id']) ) {

				$page_links = false;
				$products = FPD_Product::get_products( array(
					'where' 	=> "ID IN (SELECT product_id FROM ".FPD_CATEGORY_PRODUCTS_REL_TABLE." WHERE category_id={$_GET['category_id']})",
				) );

			}

			if ( isset($_GET['info']) ) {
		        require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-updated-installed-info.php');
		    }

			$total_product_templates = 0;
			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-load-demo.php');
			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-load-template.php');
			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-edit-product-options.php');
			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-shortcodes.php');
			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-templates-library.php');

			if( get_option('fpd_updater_dokan_hidden', 'no') !== 'yes' ): ?>
				<div class="fpd-panel">
					<h3>Update V3.9.7</h3>
					<p>Your action is required to update to the latest version. Please select an user who will be assigned as creator for your current FPD products.</p>
					<form method="POST">
						<select name="fpd_update_products_to_user">
							<?php

							$users = get_users( array('fields' => array('ID', 'user_nicename')) );

							foreach( $users as $user ) {
								echo '<option value="'.$user->ID.'">'.$user->user_nicename.'</option>';
							}

							?>
						</select>
						<p>
						<input type="submit" name="run_updater" value="Run Updater" class="button-primary" />
						</p>
					</form>
				</div>
			<?php endif; ?>
				<div class="fpd-clearfix">

					<form action="<?php echo admin_url('admin.php?page=fancy_product_designer'); ?>" method="POST"enctype="multipart/form-data" name="fpd_import" class="fpd-hidden">
						<input type="checkbox" name="fpd_import_to_library" />
						<input name="fpd_import_file" type="file" />
					</form>

					<div id="fpd-products" class="fpd-panel">

						<h3>
							<p class="description"><?php _e('Create Product From:', 'radykal'); ?></p>
							<?php

							$create_product_buttons = array(
								'fpd-open-templates-library' => array(
									'title' => __( 'Create a product from pre-made templates', 'radykal' ),
									'text' 	=> __( 'Templates Library', 'radykal' ),
									'attrs' => 'data-total='.$total_product_templates.''
								),
								'fpd-add-product' => array(
									'title' => __( 'Create a product from scratch', 'radykal' ),
									'text' 	=> __( 'New', 'radykal' )
								),
								'fpd-load-template' => array(
									'title' => __( 'Create a product from your saved templates', 'radykal' ),
									'text' 	=> __( 'My Templates', 'radykal' )
								),
								'fpd-load-demo' => array(
									'title' => __( 'Download a demo to get started', 'radykal' ),
									'text' 	=> __( 'Demo', 'radykal' )
								),
								'fpd-import-product' => array(
									'title' => __( 'Import a product from an exported product', 'radykal' ),
									'text' 	=> __( 'Import', 'radykal' )
								),
							);

							$create_product_buttons = apply_filters( 'fpd_admin_manage_products_create_buttons', $create_product_buttons );

							foreach( $create_product_buttons as $key => $btn )
								echo '<button class="add-new-h2 fpd-admin-tooltip" id="'.esc_attr( $key ).'" title="'.esc_attr( $btn['title'] ).'" '.esc_attr( isset( $btn['attrs'] ) ? $btn['attrs'] : '' ).'> '.esc_html( $btn['text'] ).'</button>';

							?>
						</h3>

						<div id="fpd-products-nav" class="fpd-clearfix">

							<form method="POST">
								<span class="description"><?php _e('Filter:', 'radykal') ?></span>
								<select name="fpd_filter_by" class="radykal-input">
									<option value="ID" <?php selected($filter_by, 'ID'); ?> ><?php _e('ID', 'radykal') ?></option>
									<option value="title" <?php selected($filter_by, 'title'); ?> ><?php _e('Title', 'radykal') ?></option>
								</select>
								<select name="fpd_order_by" class="radykal-input">
									<option value="ASC" <?php selected($order_by, 'ASC'); ?> ><?php _e('Ascending', 'radykal') ?></option>
									<option value="DESC" <?php selected($order_by, 'DESC'); ?>><?php _e('Descending', 'radykal') ?></option>
								</select>
							</form>
							<form method="POST" name="fpd_search_products">
								<input type="text" name="fpd_search_products_string" placeholder="<?php _e('Search Products...', 'radykal') ?>" class="radykal-input" />
								<input type="submit" class="button button-secondary" value="<?php _e('Search', 'radykal') ?>" />
							</form>

							<?php do_action( 'fpd_admin_manage_products_filter_nav' ); ?>

						</div>

						<?php if( empty($products) ): ?>
						<p class="fpd-error-message"><strong><?php _e('No Products found!', 'radykal') ?></strong></p>
						<?php endif; ?>

						<ul id="fpd-products-list">
							<?php

							foreach($products as $product) {
								$fancy_product = new FPD_Product($product->ID);
								$category_ids = $fancy_product->get_category_ids();

								echo self::get_product_item_html(
									$product->ID,
									$product->title,
									implode(',', $category_ids),
									isset($product->thumbnail) ? stripslashes($product->thumbnail) : '',
									$product->user_id
								);

								echo '<ul class="fpd-views-list">';
								$product_views = $fancy_product->get_views();
								if( !empty($product_views) ) {

									foreach($product_views as $view) {

										echo self::get_view_item_html(
											$view->ID,
											$view->thumbnail,
											$view->title,
											$product->user_id
										);

									}

								}
								echo '</ul>';

							}

							?>
						</ul>
						<?php
						if ( $page_links ) {
						    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 0;">' . $page_links . '</div></div>';
						}
						?>
						<div class="fpd-ui-blocker"></div>

					</div>
					<div id="fpd-categories" class="fpd-panel">
						<h3>
							<?php _e('Categories', 'radykal'); ?>
							<a href="#" id="fpd-add-category" class="add-new-h2"><?php _e('Add New', 'radykal'); ?></a>
						</h3>
						<ul id="fpd-categories-list">
							<?php

							foreach($categories as $category) {
								echo self::get_category_item_html($category->ID, $category->title);
							}

							?>
						</ul>
						<div class="fpd-ui-blocker"></div>

					</div>

				</div>

			</div>
			<?php

		}

		public static function get_product_item_html( $id, $title, $category_ids='', $thumbnail='', $user_id='' ) {

			if( !empty($thumbnail) ) {
				$thumbnail = '<img src="'.$thumbnail.'" />';
			}

			$actions = array(
				'fpd-add-view' => array(
					'title' => __('Add View', 'radykal'),
					'icon'  => 'fpd-admin-icon-add-box'
				),
				'fpd-edit-product-title' => array(
					'title' => __('Edit Title', 'radykal'),
					'icon'  => 'fpd-admin-icon-mode-edit'
				),
				'fpd-edit-product-options' => array(
					'title' => __('Edit Options', 'radykal'),
					'icon'  => 'fpd-admin-icon-settings'
				),
				'fpd-export-product' => array(
					'title' => __('Export', 'radykal'),
					'icon'  => 'fpd-admin-icon-cloud-download'
				),
				'fpd-save-as-template' => array(
					'title' => __('Save as template', 'radykal'),
					'icon'  => 'fpd-admin-icon-template'
				),
				'fpd-duplicate-product' => array(
					'title' => __('Duplicate', 'radykal'),
					'icon'  => 'fpd-admin-icon-content-copy'
				),
				'fpd-remove-product' => array(
					'title' => __('Delete', 'radykal'),
					'icon'  => 'fpd-admin-icon-bin'
				),
			);

			$actions = apply_filters( 'fpd_admin_manage_products_product_actions', $actions, $id, $user_id );

			$user_info = get_userdata( intval($user_id) );
			$username = $user_info ?  __(' | ', 'radykal') . $user_info->user_nicename : '';

			ob_start();
			?>
			<li id="<?php echo $id; ?>" data-categories="<?php echo $category_ids; ?>" class="fpd-product-item fpd-clearfix">
				<span class="fpd-clearfix">
					<span class="fpd-single-image-upload fpd-admin-tooltip" title="<?php _e('Product Thumbnail', 'radykal'); ?>">
						<span class="fpd-remove">
							<span class="dashicons dashicons-minus"></span>
						</span>
						<?php echo $thumbnail; ?>
					</span>
					<span class="fpd-product-meta">
						<span class="fpd-item-id"># <?php echo $id . $username; ?></span>
						<span class="fpd-product-title"><?php echo $title; ?></span>
					</span>
				</span>
				<span>
					<?php

						foreach( $actions as $key => $action )
							echo '<a href="#" class="'.$key.' fpd-admin-tooltip" title="'.$action['title'].'"><i class="'.$action['icon'].'"></i></a>';

					?>
				</span>
			</li>
			<?php

			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		public static function get_view_item_html( $id, $image, $title, $user_id='' ) {

			$product_builder_url = admin_url().'admin.php?page=fpd_product_builder&view_id='.$id;

			$actions = array(
				'fpd-edit-view-layers' => array(
					'title' => __('Edit view in product builder', 'radykal'),
					'icon'  => 'fpd-admin-icon-layers',
					'href' 	=> esc_attr( $product_builder_url )
				),
				'fpd-edit-view-title' => array(
					'title' => __('Edit Title', 'radykal'),
					'icon'  => 'fpd-admin-icon-mode-edit'
				),
				'fpd-duplicate-view' => array(
					'title' => __('Duplicate', 'radykal'),
					'icon'  => 'fpd-admin-icon-content-copy'
				),
				'fpd-remove-view' => array(
					'title' => __('Delete', 'radykal'),
					'icon'  => 'fpd-admin-icon-bin'
				),
			);

			$actions = apply_filters( 'fpd_admin_manage_products_view_actions', $actions, $id, $user_id );

			ob_start();
			?>
			<li id="<?php esc_attr_e( $id ); ?>" class="fpd-view-item fpd-clearfix">
				<span>
					<img src="<?php esc_attr_e( $image ); ?>" class="fpd-admin-tooltip" title="<?php _e( 'View Thumbnail', 'radykal' ); ?>" />
					<label><?php esc_html_e( $title ); ?></label>
				</span>
				<span>
					<?php

						foreach( $actions as $key => $action ) {

							$href = isset( $action['href'] ) ? $action['href'] : '#';

							echo '<a href="'. $href .'" class="'. $key .' fpd-admin-tooltip" title="'. $action['title'] .'" target="_self"><i class="'. $action['icon'] .'"></i></a>';

						}


					?>
				</span>
			</li>
			<?php

			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		public static function get_category_item_html( $id, $title ) {

			$active_filter = '';
			$url_params = '?page=fancy_product_designer&category_id='.$id;
			if( isset($_GET['category_id']) &&  $_GET['category_id'] === $id ) {
				$active_filter = 'fpd-active';
				$url_params = '?page=fancy_product_designer';
			}


			return '<li id="'.$id.'" class="fpd-category-item fpd-clearfix"><span><div class="fpd-ad-checkbox"><input type="checkbox" id="fpd_category_'.$id.'" /><label for="fpd_category_'.$id.'">'.$title.'</label></div></span><span><a href="'.$url_params.'" class="fpd-filter-category fpd-admin-tooltip '.$active_filter.'" title="'.__('Show only products of this category', 'radykal').'"><i class="fpd-admin-icon-remove-red-eye"></i></a><a href="#" class="fpd-edit-category-title fpd-admin-tooltip" title="'.__('Edit Title', 'radykal').'"><i class="fpd-admin-icon-mode-edit"></i></a><a href="#" class="fpd-remove-category fpd-admin-tooltip" title="'.__('Delete', 'radykal').'"><i class="fpd-admin-icon-bin"></i></a></span></li>';

		}

		public static function get_template_link_html( $template_id, $title) {

			return "<li><a href='#' id='".esc_attr( $template_id )."'>".$title."</a><a href='#' class='fpd-remove-template fpd-right'><i class='fpd-admin-icon-close'></i></a></li>";

		}
	}
}

new FPD_Admin_Manage_Products();

?>