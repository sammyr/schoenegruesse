<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Elementor Theme Support
 */
function hello_elementor_child_register_elementor_locations($elementor_theme_manager) {
    $elementor_theme_manager->register_all_core_location();
}
add_action('elementor/theme/register_locations', 'hello_elementor_child_register_elementor_locations');

/**
 * Elementor-spezifische Funktionen
 */
function init_elementor_support() {
    // Aktiviere Elementor-Support für Custom Post Types
    add_post_type_support('product', 'elementor');
    add_post_type_support('page', 'elementor');
    add_post_type_support('post', 'elementor');
}
add_action('init', 'init_elementor_support');

/**
 * Debug-Funktionen für Elementor
 */
function debug_elementor_template() {
    if (is_singular()) {
        global $post;
        
        // Überprüfe Elementor-Status
        $is_elementor = get_post_meta($post->ID, '_elementor_edit_mode', true);
        $template_type = get_post_meta($post->ID, '_elementor_template_type', true);
        
        error_log(sprintf(
            'Elementor Debug - Post ID: %d, Is Elementor: %s, Template Type: %s',
            $post->ID,
            $is_elementor ? 'yes' : 'no',
            $template_type ? $template_type : 'none'
        ));
    }
}
add_action('template_redirect', 'debug_elementor_template');
