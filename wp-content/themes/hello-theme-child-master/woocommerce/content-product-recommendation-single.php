<?php
/**
 * The template for displaying single product recommendation
 */

defined('ABSPATH') || exit;

global $product;

// Ensure $product is valid and visible
if (!$product || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('single-recommendation', $product); ?>>
    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="product-link">
        <?php
        // Nur das Hauptbild anzeigen
        echo $product->get_image('woocommerce_thumbnail', array(
            'class' => 'recommendation-image',
            'alt' => $product->get_name()
        ));
        ?>
        <h2 class="woocommerce-loop-product__title"><?php echo esc_html($product->get_name()); ?></h2>
    </a>
</li>
