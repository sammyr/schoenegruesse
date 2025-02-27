<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Optimiere Bildgrößen für bessere Performance
function optimize_image_sizes() {
    // Entferne Standard-Bildgrößen
    remove_image_size('1536x1536');
    remove_image_size('2048x2048');
    
    // Setze maximale Bildgröße für Uploads
    add_filter('big_image_size_threshold', function($threshold) {
        return 2560; // Maximum Breite/Höhe in Pixeln
    });
    
    // Optimiere JPEG-Qualität
    add_filter('jpeg_quality', function($quality) {
        return 80; // Qualität zwischen 0-100
    });
}
add_action('init', 'optimize_image_sizes');

// Optimiere Plugin-Ladereihenfolge
function optimize_plugin_load_order($plugins) {
    if (!is_array($plugins)) {
        return $plugins;
    }
    return $plugins;
}
add_filter('option_active_plugins', 'optimize_plugin_load_order');
