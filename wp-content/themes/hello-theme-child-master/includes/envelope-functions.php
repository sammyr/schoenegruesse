<?php
/**
 * Funktionen für die Briefumschlag-Anzeige
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Zeigt Briefumschläge auf der Produktseite an
 * 
 * @param array $params Shortcode Parameter
 * @return string HTML Output der Briefumschläge
 */
function hello_child_product_envelopes($params) {
    // Hole das aktuelle Produkt
    $product_id = get_the_ID();
    if (!$product_id) {
        return '';
    }

    // Hole die Briefumschläge aus den ACF-Feldern
    $envelopes = get_field('briefumschlaege', $product_id);
    if (empty($envelopes)) {
        return '';
    }

    // Erstelle den Output
    $output = '<div class="envelope-wrapper">';
    $output .= '<div class="envelope-title">Briefumschlagauswahl</div>';
    $output .= '<p>Bitte auf die Briefumschläge klicken um diese der Bestellung hinzuzufügen.</p>';
    $output .= '<div class="envelope-grid">';

    // Füge jeden Briefumschlag hinzu
    foreach ($envelopes as $envelope) {
        if (is_array($envelope)) {
            $envelope_id = $envelope['ID'];
        } elseif (is_object($envelope)) {
            $envelope_id = $envelope->ID;
        } else {
            $envelope_id = $envelope;
        }

        $product = wc_get_product($envelope_id);
        if (!$product) continue;

        $title = str_replace("Briefumschlag", "", $product->get_title());
        $image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
        
        $output .= sprintf(
            '<div class="envelope-item">
                <a href="?add-to-cart=%d" title="Briefumschlag Farbe: %s">
                    <img src="%s" alt="%s">
                </a>
                <span class="envelope-name">%s</span>
            </div>',
            $envelope_id,
            esc_attr($title),
            esc_url($image),
            esc_attr($title),
            esc_html($title)
        );
    }

    $output .= '</div>';
    $output .= '<script>
        // Remove URL Tag Parameter from Address Bar
        if (window.parent.location.href.match(/add-to-cart=/)){
            if (typeof (history.pushState) != "undefined") {
                var obj = { Title: document.title, Url: window.parent.location.pathname };
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                window.parent.location = window.parent.location.pathname;
            }
        }
    </script>';
    $output .= '</div>';

    return $output;
}
add_shortcode('set_envelopes', 'hello_child_product_envelopes');

// Füge CSS für die Briefumschläge hinzu
function hello_child_product_envelopes_styles() {
    ?>
    <style>
        .envelope-wrapper {
            margin: 2em 0;
            padding: 1em;
            border-radius: 4px;
            text-align: center;
        }
        
        .envelope-title {
            margin-bottom: 1em;
            font-size: 1.2em;
            color: #333;
            font-weight: 600;
            width: 100%;
        }
        
        .envelope-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1em;
            padding: 1em 0;
        }
        
        .envelope-item {
            margin: 0 !important;
            padding: 0.5em !important;
            text-align: center;
        }
        
        .envelope-item a {
            text-decoration: none !important;
            border: none !important;
            display: block;
        }
        
        .envelope-item img {
            width: 150px;
            max-width: 100%;
            height: auto;
            margin: 0 auto 0.5em !important;
            border: 1px solid transparent;
            transition: border-color 0.2s;
        }
        
        .envelope-name {
            display: block;
            font-size: 12px;
            line-height: 1.4;
        }
        
        @media (max-width: 768px) {
            .envelope-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'hello_child_product_envelopes_styles');
