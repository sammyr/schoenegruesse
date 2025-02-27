<?php
/**
 * Funktionen für die Produktserien
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Zeigt verwandte Produkte aus der gleichen Serie an
 */
function hello_child_product_series($params) {
    global $post;
    $output = '';
    
    $serienauswahl = get_field('serienauswahl', $post->ID);
    if ($serienauswahl && is_array($serienauswahl)) {
        // Sammle die IDs aus den Post-Objekten
        $product_ids = array();
        foreach ($serienauswahl as $product) {
            if (is_object($product)) {
                $product_ids[] = $product->ID;
            } elseif (is_array($product)) {
                $product_ids[] = $product['ID'];
            } else {
                $product_ids[] = $product;
            }
        }
        
        // Entferne das aktuelle Produkt aus der Liste
        $product_ids = array_diff($product_ids, array($post->ID));
        
        if (!empty($product_ids)) {
            $output .= '<div class="series-section">';
            $output .= '<div class="serien_header">WEITERE ARTIKEL AUF DIESER SERIE:</div>';
            $output .= '<div class="series-products">';
            
            // Hole die Produkte manuell statt WooCommerce Shortcode
            foreach ($product_ids as $pid) {
                $product = wc_get_product($pid);
                if ($product) {
                    $image_id = $product->get_image_id();
                    $image_url = wp_get_attachment_image_url($image_id, 'full');
                    $product_url = get_permalink($pid);
                    $product_title = $product->get_title();
                    
                    $output .= '<div class="series-product">';
                    $output .= '<a href="' . esc_url($product_url) . '">';
                    $output .= '<div class="product-image-wrapper">';
                    if ($image_url) {
                        $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product_title) . '">';
                    }
                    $output .= '</div>';
                    $output .= '<h3>' . esc_html($product_title) . '</h3>';
                    $output .= '</a>';
                    $output .= '</div>';
                }
            }
            
            $output .= '</div>';
            $output .= '</div>';
        }
    }
    
    return $output;
}
add_shortcode('product_series', 'hello_child_product_series');

// Füge CSS für die Produktserien hinzu
function hello_child_product_series_styles() {
    ?>
    <style>
        .series-section {
            margin: 4em 0;
            padding: 0;
        }
        
        .series-title {
            font-size: 16px;
            font-weight: 400;
            color: #666;
            margin: 0 0 2em 0;
            text-transform: uppercase;
        }
        
        .series-products {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2em;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .series-product {
            text-align: center;
           
            padding: 1em;
        }
        
        .series-product a {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .product-image-wrapper {
            position: relative;
            margin-bottom: 1em;
            border: 1px solid #ddd;
            background: #fff;
            padding: 1em;
        }
        
        .product-image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .series-product h3 {
            font-size: 14px;
            margin: 0;
            padding: 0;
            color: #333;
            font-weight: 400;
        }
        
        @media (max-width: 768px) {
            .series-products {
                grid-template-columns: repeat(2, 1fr);
                gap: 1em;
            }
            
            .series-title {
                font-size: 14px;
                margin-bottom: 1.5em;
            }
            
            .series-product h3 {
                font-size: 13px;
            }
        }
        
        @media (max-width: 480px) {
            .series-products {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'hello_child_product_series_styles');
