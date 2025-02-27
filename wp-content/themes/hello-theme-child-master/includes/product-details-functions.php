<?php
/**
 * Funktionen für die Produktdetails-Anzeige
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Zeigt die Produktdetails in einem strukturierten Format an
 */
function hello_child_product_details($params) {
    $product_id = get_the_ID();
    if (!$product_id) return '';

    // Debug-Ausgaben
    error_log('=== Product Details Debug ===');
    error_log('Product ID: ' . $product_id);

    // Hole die Produkt-Kategorien für das Format
    $format_terms = get_the_terms($product_id, 'product_cat');
    $leistungen = get_field('leistungen', $product_id);

    error_log('Format Terms: ' . print_r($format_terms, true));
    error_log('Leistungen: ' . print_r($leistungen, true));
    
    $output = '<div class="product-details-wrapper">';

    // Designer Block
    $output .= '<div class="details-block">';
    $output .= '<h3>DESIGNER</h3>';
    $output .= '<p>WP DESIGNAGENTUR</p>';
    $output .= '</div>';

    // Format Block
    if ($format_terms && !is_wp_error($format_terms)) {
        foreach ($format_terms as $term) {
            // Prüfe ob die Kategorie ein Format ist
            if (in_array($term->slug, ['a6-148-x-105-mm-%c2%b7-2-seiten', 'a6-148-x-105-mm-%c2%b7-1-seite', 'a6-148-x-105-mm-%c2%b7-klappkarte-4-seiten'])) {
                $output .= '<div class="details-block">';
                $output .= '<h3>FORMAT</h3>';
                $output .= '<div class="format-line">';
                $output .= '<svg class="format-icon" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#999" d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"/>
                </svg>';
                $output .= '<span class="format-text">' . esc_html($term->name) . '</span>';
                $output .= '</div>';
                $output .= '</div>';
                break;
            }
        }
    }

    // Retusche-Service Block
    $output .= '<div class="details-block">';
    $output .= '<div class="service-header">';
    $output .= '<h3>RETUSCHE-SERVICE INKLUSIVE</h3>';
    $output .= '<svg class="checkmark-large" width="20" height="20" viewBox="0 0 24 24">
        <path fill="#22c55e" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
    </svg>';
    $output .= '</div>';
    $output .= '</div>';

    // Leistungen Liste
    if ($leistungen && is_array($leistungen)) {
        $has_valid_leistungen = false;
        $leistungen_output = '';
        
        foreach ($leistungen as $leistung) {
            if (is_object($leistung) && isset($leistung->post_title)) {
                $leistung_title = $leistung->post_title;
            } else if (is_numeric($leistung)) {
                $leistung_title = get_the_title($leistung);
            }
            
            if (!empty($leistung_title)) {
                $has_valid_leistungen = true;
                $leistungen_output .= '<div class="leistung-line">';
                $leistungen_output .= '<svg class="checkmark-icon" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#22c55e" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>';
                $leistungen_output .= '<span class="leistung-text">' . esc_html($leistung_title) . '</span>';
                $leistungen_output .= '</div>';
            }
        }
        
        // Nur ausgeben wenn gültige Leistungen vorhanden sind
        if ($has_valid_leistungen) {
            $output .= '<div class="leistungen-list">';
            $output .= $leistungen_output;
            $output .= '</div>';
        }
    }
    
    // Fly Cream Block
    $output .= '<div class="details-block">';
    $output .= '<div class="service-header">';
    $output .= '<h3>FLY CREAM (NEUTRALE) UMSCHLÄGE SIND INKLUSIVE</h3>';
    $output .= '<svg class="checkmark-large" width="20" height="20" viewBox="0 0 24 24">
        <path fill="#22c55e" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
    </svg>';
    $output .= '</div>';
    $output .= '</div>';

    $output .= '</div>';
    
    return $output;
}
add_shortcode('product_details', 'hello_child_product_details');

function hello_child_product_details_styles() {
    ?>
    <style>
        .product-details-wrapper {
            margin: 2em 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            max-width: 600px;
        }
        
        .details-block {
            margin-bottom: 0em;
            padding: 1.0em 0;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        
        .details-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .details-block h3 {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin: 0 0 0.75em 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .details-block p {
            font-size: 15px;
            color: #333;
            margin: 0;
            line-height: 1.4;
            font-weight: 400;
        }
        
        .format-line,
        .leistung-line {
            display: flex;
            align-items: center;
            gap: 0.75em;
        }
        
        .format-icon,
        .checkmark-icon {
            flex-shrink: 0;
            opacity: 0.6;
        }
        
        .format-text,
        .leistung-text {
            font-size: 15px;
            color: #333 !important;
            line-height: 1.4;
            font-weight: 400;
        }
        
        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .service-header h3 {
            margin: 0;
        }
        
        .checkmark-large {
            flex-shrink: 0;
        }
        
        .leistungen-list {
            margin: 1em 0;
        }
        
        @media (max-width: 768px) {
            .details-block {
                padding: 1.25em 0;
            }
            
            .details-block h3 {
                font-size: 13px;
            }
            
            .format-text,
            .leistung-text {
                font-size: 14px;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'hello_child_product_details_styles');
