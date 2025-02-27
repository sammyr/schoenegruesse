<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue scripts and styles.
 */
function hello_elementor_child_scripts_styles() {
    wp_enqueue_style(
        'hello-elementor-child',
        get_stylesheet_directory_uri() . '/style.css',
        [
            'hello-elementor'
        ],
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);

// jQuery laden
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('jquery');
});

// Lade Cart CSS auf der Warenkorb- und Kassenseite
function load_cart_styles() {
    if (is_cart() || is_checkout()) {
        wp_enqueue_style('schoengruesse-cart', get_stylesheet_directory_uri() . '/assets/css/cart.css', array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'load_cart_styles');

/**
 * Theme Setup
 */
function hello_elementor_child_theme_setup() {
    // Aktiviere volle Elementor-Unterstützung
    add_theme_support('elementor');
    
    // Aktiviere Standard-Features
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    
    // Aktiviere WooCommerce-Unterstützung
    add_theme_support('woocommerce');
    
    // Aktiviere Elementor Pro Features
    if (defined('ELEMENTOR_PRO_VERSION')) {
        add_theme_support('elementor-pro');
    }
    
    // WooCommerce Support
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'hello_elementor_child_theme_setup');

// Deaktiviere Smilies auf der Warenkorb-Seite
function disable_smilies_on_cart($content) {
    if (is_cart()) {
        remove_filter('the_content', 'convert_smilies', 20);
    }
    return $content;
}
add_filter('the_content', 'disable_smilies_on_cart', 1);

// Debug-Funktion für WooCommerce
function debug_woocommerce_cart() {
    if (is_cart()) {
        error_log('=== WooCommerce Cart Debug ===');
        error_log('WC()->cart exists: ' . (isset(WC()->cart) ? 'yes' : 'no'));
        if (isset(WC()->cart)) {
            error_log('Cart is empty: ' . (WC()->cart->is_empty() ? 'yes' : 'no'));
            error_log('Cart contents count: ' . WC()->cart->get_cart_contents_count());
            error_log('Cart page ID: ' . wc_get_page_id('cart'));
            error_log('Cart page URL: ' . wc_get_cart_url());
            
            // Überprüfe Template-Pfade
            $template_path = get_stylesheet_directory() . '/woocommerce/cart/cart.php';
            error_log('Custom cart template exists: ' . (file_exists($template_path) ? 'yes' : 'no'));
            error_log('Template path: ' . $template_path);
        }
        error_log('=== End WooCommerce Cart Debug ===');
    }
}
add_action('template_redirect', 'debug_woocommerce_cart');

// Debug Funktion für Produktkategorien
function debug_product_category() {
    if (!is_admin()) {
        global $wp_query;
        $object = get_queried_object();
        
        error_log('Current Object Type: ' . get_class($object));
        error_log('Is Product Category: ' . (is_product_category() ? 'yes' : 'no'));
        error_log('Is Shop: ' . (is_shop() ? 'yes' : 'no'));
        error_log('Is Single Product: ' . (is_product() ? 'yes' : 'no'));
        
        if (is_product_category()) {
            error_log('Category Slug: ' . $object->slug);
            error_log('Category ID: ' . $object->term_id);
            
            // Produkte in dieser Kategorie zählen
            $products = wc_get_products(array(
                'status' => 'publish',
                'limit' => -1,
                'category' => array($object->slug)
            ));
            error_log('Products in Category: ' . count($products));
        }
        
        // Template Debug
        error_log('Template Being Used: ' . get_page_template());
    }
}
add_action('template_redirect', 'debug_product_category');

// Debug WooCommerce Kategorien
function debug_wc_categories() {
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));
    
    if (!empty($categories) && !is_wp_error($categories)) {
        error_log('=== WooCommerce Kategorien ===');
        foreach ($categories as $category) {
            error_log(sprintf(
                'Name: %s, Slug: %s, ID: %d, Count: %d',
                $category->name,
                $category->slug,
                $category->term_id,
                $category->count
            ));
        }
    } else {
        error_log('Keine WooCommerce Kategorien gefunden oder Fehler: ' . print_r($categories, true));
    }
}
add_action('init', 'debug_wc_categories');

// WooCommerce Anpassungen
function custom_woocommerce_settings() {
    // Zeige alle Produkte in Kategorien an
    add_filter('loop_shop_per_page', function() {
        return -1; // -1 zeigt alle Produkte an
    }, 20);
    
    // Entferne "Zeige X Ergebnisse" Text
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
}
add_action('init', 'custom_woocommerce_settings');

// WooCommerce Shortcode Anpassungen
function custom_shortcode_products($atts) {
    // Standard-Attribute
    $atts = shortcode_atts(array(
        'columns' => '4',
        'orderby' => 'menu_order',
        'order'   => 'ASC',
        'category'=> '',
        'limit'   => '-1'
    ), $atts, 'products');
    
    // Debug-Ausgabe
    error_log('WooCommerce Shortcode Attribute: ' . print_r($atts, true));
    
    return WC_Shortcodes::products($atts);
}
remove_shortcode('products');
add_shortcode('products', 'custom_shortcode_products');

// WooCommerce Produkt-Shortcodes für Elementor
function sg_product_title_shortcode() {
    ob_start();
    do_action('woocommerce_single_product_summary');
    return ob_get_clean();
}
add_shortcode('sg_product_title', 'sg_product_title_shortcode');

function sg_product_images_shortcode() {
    ob_start();
    do_action('woocommerce_before_single_product_summary');
    return ob_get_clean();
}
add_shortcode('sg_product_images', 'sg_product_images_shortcode');

function sg_product_summary_shortcode() {
    ob_start();
    do_action('woocommerce_single_product_summary');
    return ob_get_clean();
}
add_shortcode('sg_product_summary', 'sg_product_summary_shortcode');

function sg_product_tabs_shortcode() {
    ob_start();
    do_action('woocommerce_after_single_product_summary');
    return ob_get_clean();
}
add_shortcode('sg_product_tabs', 'sg_product_tabs_shortcode');

function sg_full_product_shortcode() {
    ob_start();
    
    do_action('woocommerce_before_single_product');
    
    echo '<div id="product-' . get_the_ID() . '" ' . wc_product_class('', get_the_ID()) . '>';
    
    do_action('woocommerce_before_single_product_summary');
    
    echo '<div class="summary entry-summary">';
    do_action('woocommerce_single_product_summary');
    echo '</div>';
    
    do_action('woocommerce_after_single_product_summary');
    
    echo '</div>';
    
    do_action('woocommerce_after_single_product');
    
    return ob_get_clean();
}
add_shortcode('sg_full_product', 'sg_full_product_shortcode');

// JavaScript, um das Quantity-Feld durch ein Select-Feld zu ersetzen
add_action( 'wp_footer', function() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var selectOptions = [
                10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100,
                105, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 225, 250, 275, 300,
                325, 350, 375, 400, 425, 450, 475, 500
            ];

            var select = '<select name="quantity" title="Qty" class="qty">';
            for (var i = 0; i < selectOptions.length; i++) {
                select += '<option value="' + selectOptions[i] + '">' + selectOptions[i] + '</option>';
            }
            select += '</select>';

            $('input[name="quantity"]').replaceWith(select);
        });
    </script>
    <?php
});

// Laden der erweiterten Funktionen für den Fancy Product Designer
function load_fpd_extensions() {
    $plugin_path = WP_PLUGIN_DIR . '/fancy-product-designer';
    $extension_file = $plugin_path . '/woo/product-images-replacement.php';
    
    if (file_exists($extension_file)) {
        require_once($extension_file);
    }
}
add_action('init', 'load_fpd_extensions', 5);

// Lade die verschiedenen Funktionsdateien
$include_files = array(
    'elementor-functions.php',
    'elementor-restore.php',
    'woocommerce-functions.php',
    'gallery-functions.php',
    'product-details-functions.php',
    'product-series-functions.php',
    'envelope-functions.php',
    'product-recommendations-functions.php'
);

foreach ($include_files as $file) {
    $file_path = get_stylesheet_directory() . '/includes/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

// Menüfunktionen einbinden
require_once get_stylesheet_directory() . '/includes/menu-functions.php';
init_menu_functionality();

// Stelle sicher, dass WooCommerce initialisiert ist
function check_woocommerce_dependencies() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>' . __('WooCommerce muss installiert und aktiviert sein.', 'hello-elementor-child') . '</p></div>';
        });
        return;
    }
}
add_action('admin_init', 'check_woocommerce_dependencies');

// Fancy Product Designer Debug-Funktionen einbinden
require_once get_stylesheet_directory() . '/includes/fpd-debug.php';

// FPD Galerie-Override einbinden
require_once get_stylesheet_directory() . '/includes/fpd-gallery-override.php';

// FPD Galerie-Integration einbinden
function hello_child_enqueue_fpd_gallery_integration() {
    // Nur auf Produktseiten laden
    if (is_product()) {
        wp_enqueue_script(
            'fpd-gallery-integration',
            get_stylesheet_directory_uri() . '/includes/fpd-gallery-integration.js',
            array('jquery'),
            '1.8.6' . '?' . time(),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'hello_child_enqueue_fpd_gallery_integration', 99);

// Lade die verschiedenen Funktionsdateien
$include_files = array(
    'elementor-functions.php',
    'elementor-restore.php',
    'woocommerce-functions.php',
    'gallery-functions.php',
    'product-details-functions.php',
    'product-series-functions.php',
    'envelope-functions.php',
    'product-recommendations-functions.php'
);

foreach ($include_files as $file) {
    $file_path = get_stylesheet_directory() . '/includes/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}
