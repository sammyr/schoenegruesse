<?php
/*
* Plugin Name: Fancy Product Designer Extend
* Description: Extend functionality for creating and sell customizable products.
* Version: 1.0
* Author: silver-solutions
* Author URI: https://silver-solutions.net
*/

if (!defined('FPDE_THEME_DIR'))
    define('FPDE_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());
 
if (!defined('FPDE_PLUGIN_NAME'))
    define('FPDE_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
 
if (!defined('FPDE_PLUGIN_DIR'))
    define('FPDE_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . FPDE_PLUGIN_NAME);
 
if (!defined('FPDE_PLUGIN_URL'))
    define('FPDE_PLUGIN_URL', WP_PLUGIN_URL . '/' . FPDE_PLUGIN_NAME);



function fpde_load_styles_scripts() {
  	
  	wp_enqueue_style('fpde-style', FPDE_PLUGIN_URL.'/assets/css/fpde-style.css', false, uniqid());
  	wp_enqueue_style('magnific-popup-style', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css');


  	if(!wp_script_is('jquery','enqueued'))
    	wp_enqueue_script('jquery');

  	wp_enqueue_script('magnific-popup-script', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js', array('jquery'), '', true);
  	wp_enqueue_script('fpde-script', FPDE_PLUGIN_URL.'/assets/js/fpde-script.js', array('jquery'), uniqid(), true);

    wp_dequeue_script('oceanwp-woo-ajax-addtocart');
    wp_deregister_script('oceanwp-woo-ajax-addtocart');
    wp_enqueue_script('fdpe-oceanwp-woo-ajax-addtocart', FPDE_PLUGIN_URL.'/assets/js/fpde-woo-ajax-add-to-cart.js', array( 'jquery' ), uniqid(), true );
}
add_action('wp_head', 'fpde_load_styles_scripts', 9999999);



function fdpe_override_woocommerce_template( $template, $template_name, $template_path ) {
	//print_r($template);
    preg_match('/woocommerce\/(templates\/)?(.*)/m', $template, $template_matches);

 	if( isset($template_matches[2]) && $template_matches[2] == 'single-product/add-to-cart/simple.php' )
 		$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocomerce/templates/single-product/add-to-cart/simple.php';

    if( isset($template_matches[2]) && $template_matches[2] == 'single-product/add-to-cart/variation-add-to-cart-button.php' )
        $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocomerce/templates/single-product/add-to-cart/variation-add-to-cart-button.php';

    if( isset($template_matches[2]) && $template_matches[2] == 'cart/cart.php' )
        $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocomerce/templates/cart/cart.php';
 	
 	return $template;
}
add_filter( 'woocommerce_locate_template', 'fdpe_override_woocommerce_template', 10, 3 );



function fpde_add_cart_single_product_ajax(){
    
    $product_id     = sanitize_text_field( $_POST['product_id'] );
    $variation_id   = sanitize_text_field( $_POST['variation_id'] );
    $variation      = $_POST['variation'];
    $quantity       = sanitize_text_field( $_POST['quantity'] );


    if ( $variation_id )
        $last_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation);
    else
        $last_item_key = WC()->cart->add_to_cart( $product_id, $quantity);

    wp_send_json( array( 'success' => $last_item_key ) );
}
add_action( 'wp_ajax_fpde_add_cart_single_product', 'fpde_add_cart_single_product_ajax');
add_action( 'wp_ajax_nopriv_fpde_add_cart_single_product', 'fpde_add_cart_single_product_ajax');



function fpde_add_cart_item_data( $cart_item_meta, $product_id ) {
    $cart_item_meta['notes'] = isset( $_POST ['notes'] ) ? sanitize_text_field ( $_POST ['notes'] ) : '';
    $cart_item_meta['fpde_product_thumbnails'] = isset( $_POST ['fpde_product_thumbnails'] ) ? $_POST ['fpde_product_thumbnails'] : '';
    return $cart_item_meta;
}
add_filter( 'woocommerce_add_cart_item_data', 'fpde_add_cart_item_data', 10, 2 );




function fpde_get_item_data ( $other_data, $cart_item ) {

    if ( isset( $cart_item [ 'notes' ] ) )
        $other_data[] = array('name' => 'Notes', 'display' => $cart_item ['notes']);
    
    return $other_data;
}
add_filter( 'woocommerce_get_item_data', 'fpde_get_item_data' , 10, 2 );


add_action( 'wp_loaded', function(){
    remove_action( 'woocommerce_after_cart_item_name', 'prefix_after_cart_item_name' );
} );

function prefix_after_cart_item_name1( $cart_item, $cart_item_key ) {

    echo '<br><div><input type="checkbox" class="prefix-cart-ok" id="cart_ok_'.$cart_item_key.'" data-cart-id="'.$cart_item_key.'" name="check" checked="checked">ja, ich habe die Inhalte dieser Karte überprüft.</div><div>Besondere Hinweise für diese Karte?<br><textarea class="prefix-cart-notes" id="cart_notes_'.$cart_item_key.'" data-cart-id="'.$cart_item_key.'">'.$cart_item['notes'].'</textarea></div>';   
}
add_action( 'woocommerce_after_cart_item_name', 'prefix_after_cart_item_name1', 10, 2 );



function fpde_add_order_item_meta ( $item_id, $values ) {
    if (isset($values [ 'notes' ]))
        wc_add_order_item_meta( $item_id, 'Notes', $values['notes'] );
}
add_action( 'woocommerce_add_order_item_meta', 'fpde_add_order_item_meta' , 10, 2);

?>