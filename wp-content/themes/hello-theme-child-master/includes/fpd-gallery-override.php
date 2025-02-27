<?php
/**
 * FPD Galerie-Override
 * Verhindert, dass der Fancy Product Designer die WooCommerce-Produktgalerie entfernt
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Diese Funktion wird ausgeführt, bevor der FPD die Produktbilder entfernt
function fpd_prevent_gallery_removal() {
    // Entferne den Standard-Hook, der die Galerie entfernt
    global $product_settings;
    
    // Entferne die Aktion mit niedriger Priorität, damit sie nach der FPD-Initialisierung ausgeführt wird
    add_action('woocommerce_before_single_product', 'fpd_restore_product_gallery', 5);
}
add_action('init', 'fpd_prevent_gallery_removal');

// Diese Funktion stellt sicher, dass die Produktgalerie angezeigt wird
function fpd_restore_product_gallery() {
    // Nur auf Produktseiten ausführen
    if (!is_product()) return;
    
    // Entferne den Hook, der die Galerie entfernt
    remove_action('woocommerce_before_single_product', 'remove_action', 5);
    
    // Stelle sicher, dass die Galerie angezeigt wird
    if (!has_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images')) {
        add_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    }
    
    // Füge JavaScript hinzu, um zu verhindern, dass der FPD die Galerie versteckt
    add_action('wp_footer', 'fpd_gallery_override_script');
}

// JavaScript, um zu verhindern, dass der FPD die Galerie versteckt
function fpd_gallery_override_script() {
    if (!is_product()) return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Verhindere, dass die Galerie ausgeblendet wird
        var originalHide = $.fn.hide;
        $.fn.hide = function() {
            // Wenn es sich um die Produktgalerie handelt, nicht ausblenden
            if ($(this).hasClass('woocommerce-product-gallery') || 
                $(this).hasClass('woocommerce-product-gallery__wrapper')) {
                console.log('FPD: Verhindere Ausblenden der Produktgalerie');
                return this;
            }
            // Sonst normal ausblenden
            return originalHide.apply(this, arguments);
        };
        
        // Stelle sicher, dass die Galerie sichtbar bleibt
        setInterval(function() {
            $('.woocommerce-product-gallery, .woocommerce-product-gallery__wrapper').css('display', 'block');
        }, 500);
    });
    </script>
    <?php
}
