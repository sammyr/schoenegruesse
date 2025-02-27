<?php
/**
 * Funktionen für die Produktempfehlungen
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Zeigt Produktempfehlungen basierend auf der Produktkategorie an
 * 
 * @param array $params Shortcode Parameter
 * @return string HTML Output der Produktempfehlungen
 */
function hello_child_product_recommendations($params) {
    // Hole das aktuelle Produkt
    $product_id = get_the_ID();
    if (!$product_id) {
        return '';
    }

    // Hole die Produktkategorien
    $terms = get_the_terms($product_id, 'product_cat');
    if (!$terms || is_wp_error($terms)) {
        return '';
    }

    // Nimm die erste Kategorie
    $category = reset($terms);
    if (!$category) {
        return '';
    }

    // WooCommerce Query für ähnliche Produkte
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 3,
        'post__not_in' => array($product_id),
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category->term_id
            )
        ),
        'orderby' => 'rand'
    );

    $products = new WP_Query($args);

    if (!$products->have_posts()) {
        return '';
    }

    // Erstelle den Output
    ob_start();
    ?>
    <div class="recommendations-wrapper">
        <h3 class="recommendations-title">Weitere Empfehlungen</h3>
        <ul class="products recommendations-list">
            <?php
            while ($products->have_posts()) : $products->the_post();
                global $product;
                wc_get_template('content-product-recommendation-single.php');
            endwhile;
            ?>
        </ul>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('product_recommendations', 'hello_child_product_recommendations');

// Füge CSS für die Produktempfehlungen hinzu
function hello_child_product_recommendations_styles() {
    ?>
    <style>
        .recommendations-wrapper {
            margin: 2em 0;
            overflow-x: hidden;
            padding: 0 1px;
        }
        .recommendations-title {
            text-align: center;
            margin-bottom: 1.5em;
        }
        .recommendations-list {
            display: flex;
            flex-direction: row;
            gap: 1em;
            list-style: none;
            padding: 0;
            margin: 0;
            justify-content: center;
        }
        .single-recommendation {
            text-align: center;
            flex: 0 0 200px;
            max-width: 200px;
            width: 200px;
        }
        .single-recommendation .product-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .single-recommendation .recommendation-image {
            width: 200px;
            height: auto;
            margin-bottom: 0.5em;
            transition: transform 0.3s ease;
        }
        .single-recommendation:hover .recommendation-image {
            transform: scale(1.05);
        }
        .single-recommendation .woocommerce-loop-product__title {
            font-size: 0.85em;
            margin: 0;
            white-space: normal;
            line-height: 1.3;
            padding: 0 5px;
        }
    </style>
    <?php
}
add_action('wp_head', 'hello_child_product_recommendations_styles');
