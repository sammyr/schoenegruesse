<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// WooCommerce Setup
function hello_elementor_child_woocommerce_setup() {
    // Aktiviere Standard-WooCommerce-Features
    add_theme_support('woocommerce');
    
    // Aktiviere WooCommerce-Gallery-Features
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'hello_elementor_child_woocommerce_setup');

// Stelle sicher, dass die WooCommerce-Seiten existieren
function ensure_woocommerce_pages() {
    // Array der erforderlichen WooCommerce-Seiten
    $wc_pages = array(
        'shop' => array(
            'title' => 'Shop',
            'shortcode' => '[products]'
        ),
        'cart' => array(
            'title' => 'Warenkorb',
            'shortcode' => '[woocommerce_cart]'
        ),
        'checkout' => array(
            'title' => 'Kasse',
            'shortcode' => '[woocommerce_checkout]'
        ),
        'myaccount' => array(
            'title' => 'Mein Konto',
            'shortcode' => '[woocommerce_my_account]'
        )
    );

    foreach ($wc_pages as $key => $page) {
        $page_id = wc_get_page_id($key);
        
        // Wenn die Seite nicht existiert oder im Papierkorb ist
        if ($page_id <= 0 || get_post_status($page_id) !== 'publish') {
            $page_data = array(
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
                'post_title' => $page['title'],
                'post_content' => $page['shortcode'],
                'comment_status' => 'closed'
            );
            
            $page_id = wp_insert_post($page_data);
            
            if ($page_id && !is_wp_error($page_id)) {
                update_option('woocommerce_' . $key . '_page_id', $page_id);
            }
        }
    }
}
add_action('init', 'ensure_woocommerce_pages');

// Debug WooCommerce Template Loader
function debug_woocommerce_template_loader($template, $template_name, $template_path) {
    if (is_cart()) {
        error_log('=== WooCommerce Template Debug ===');
        error_log('Template Name: ' . $template_name);
        error_log('Template Path: ' . $template_path);
        error_log('Final Template: ' . $template);
        error_log('=== End Template Debug ===');
    }
    return $template;
}
add_filter('woocommerce_locate_template', 'debug_woocommerce_template_loader', 10, 3);

// SHOP ARCHIV HEADER
add_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
add_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// Warenkorb-Funktionen
function hello_elementor_child_cart_fragments($fragments) {
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'hello_elementor_child_cart_fragments');

// Warenkorb-Template-Hooks
add_action('woocommerce_before_cart', 'wc_print_notices', 10);
add_action('woocommerce_before_cart_table', 'woocommerce_output_all_notices', 10);

// Redirect leerer Warenkorb zum Shop
function wc_empty_cart_redirect_url() {
    return get_permalink(wc_get_page_id('shop'));
}
add_filter('woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url');

// Debug-Funktion für Warenkorb-Probleme
function debug_cart_template() {
    if (is_cart()) {
        error_log('=== WooCommerce Cart Template Debug ===');
        error_log('Current template: ' . get_page_template());
        error_log('WC()->cart exists: ' . (isset(WC()->cart) ? 'yes' : 'no'));
        if (isset(WC()->cart)) {
            error_log('Cart is empty: ' . (WC()->cart->is_empty() ? 'yes' : 'no'));
            error_log('Cart contents count: ' . WC()->cart->get_cart_contents_count());
            
            // Überprüfe Template-Hierarchie
            $template_path = get_stylesheet_directory() . '/woocommerce/cart/cart.php';
            $parent_template_path = get_template_directory() . '/woocommerce/cart/cart.php';
            $wc_template_path = WC()->plugin_path() . '/templates/cart/cart.php';
            
            error_log('Child theme template exists: ' . (file_exists($template_path) ? 'yes' : 'no'));
            error_log('Parent theme template exists: ' . (file_exists($parent_template_path) ? 'yes' : 'no'));
            error_log('WooCommerce template exists: ' . (file_exists($wc_template_path) ? 'yes' : 'no'));
            
            // Überprüfe Warenkorb-Seiten-Einstellungen
            error_log('Cart page ID: ' . wc_get_page_id('cart'));
            error_log('Cart page exists: ' . (get_post(wc_get_page_id('cart')) ? 'yes' : 'no'));
            if ($cart_page = get_post(wc_get_page_id('cart'))) {
                error_log('Cart page status: ' . $cart_page->post_status);
                error_log('Cart page content: ' . substr($cart_page->post_content, 0, 100));
            }
        }
        error_log('=== End Cart Template Debug ===');
    }
}
add_action('template_redirect', 'debug_cart_template');

// Archiviere alte WooCommerce-Bestellungen
function archive_old_woocommerce_orders() {
    global $wpdb;
    
    // Bestellungen älter als 6 Monate
    $date = date('Y-m-d', strtotime('-6 months'));
    
    // Erstelle Archiv-Tabelle, wenn sie nicht existiert
    $wpdb->query("
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wc_archived_orders LIKE {$wpdb->prefix}posts;
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wc_archived_order_itemmeta LIKE {$wpdb->prefix}woocommerce_order_itemmeta;
    ");
    
    // Verschiebe alte Bestellungen
    $wpdb->query("
        INSERT INTO {$wpdb->prefix}wc_archived_orders 
        SELECT * FROM {$wpdb->prefix}posts 
        WHERE post_type = 'shop_order' 
        AND post_date < '{$date}'
    ");
    
    // Verschiebe zugehörige Metadaten
    $wpdb->query("
        INSERT INTO {$wpdb->prefix}wc_archived_order_itemmeta 
        SELECT m.* FROM {$wpdb->prefix}woocommerce_order_itemmeta m
        INNER JOIN {$wpdb->prefix}woocommerce_order_items i ON m.order_item_id = i.order_item_id
        INNER JOIN {$wpdb->prefix}wc_archived_orders o ON i.order_id = o.ID
    ");
    
    // Lösche archivierte Daten aus den Original-Tabellen
    $wpdb->query("
        DELETE m FROM {$wpdb->prefix}woocommerce_order_itemmeta m
        INNER JOIN {$wpdb->prefix}woocommerce_order_items i ON m.order_item_id = i.order_item_id
        INNER JOIN {$wpdb->prefix}wc_archived_orders o ON i.order_id = o.ID
    ");
    
    $wpdb->query("
        DELETE FROM {$wpdb->prefix}posts 
        WHERE post_type = 'shop_order' 
        AND post_date < '{$date}'
    ");
}

// Füge einen täglichen Cron-Job hinzu
add_action('init', function() {
    if (!wp_next_scheduled('archive_old_orders_event')) {
        wp_schedule_event(time(), 'daily', 'archive_old_orders_event');
    }
});
add_action('archive_old_orders_event', 'archive_old_woocommerce_orders');

/**
 * Debug: Ausgabe der Quantity-Input-Argumente für Produkte
 */
function debug_quantity_input_args( $args, $product ) {
    error_log( '[DEBUG] woocommerce_quantity_input_args triggered for product ID: ' . $product->get_id() );
    error_log( '[DEBUG] Original args: ' . print_r( $args, true ) );
    return $args;
}
add_filter( 'woocommerce_quantity_input_args', 'debug_quantity_input_args', 10, 2 );

/**
 * Ersetzt das WooCommerce Quantity-Input-Feld durch ein Select-Feld.
 */
function convert_quantity_input_to_select( $html, $args, $product ) {
    // Ermittle Minimum, Maximum, Schrittweite und aktuellen Wert
    $min = isset( $args['min_value'] ) ? $args['min_value'] : 1;
    // Falls kein max_value gesetzt oder -1 (unbegrenzt), setze Standardmaximal 10
    $max = ( isset( $args['max_value'] ) && $args['max_value'] > 0 ) ? $args['max_value'] : 10;
    $step = isset( $args['step'] ) ? $args['step'] : 1;
    $input_value = isset( $args['input_value'] ) ? $args['input_value'] : $min;
    $input_name = isset( $args['input_name'] ) ? $args['input_name'] : 'quantity';

    // Erstelle das Select-Feld
    $select = '<select name="' . esc_attr( $input_name ) . '">';
    for ( $i = $min; $i <= $max; $i += $step ) {
        $selected = ( $i == $input_value ) ? ' selected="selected"' : '';
        $select .= '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
    }
    $select .= '</select>';

    return $select;
}
add_filter( 'woocommerce_quantity_input_html', 'convert_quantity_input_to_select', 10, 3 );
