<?php
/**
 * The template for displaying product content within loops
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<li <?php wc_product_class('', $product); ?>>
    <div class="product-inner">
        <div class="imagewrapper border_item02">
            <div class="woo-entry-image-swap">
                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="woocommerce-LoopProduct-link">
                    <?php
                    // Hauptbild
                    if (has_post_thumbnail($product->get_id())) {
                        echo get_the_post_thumbnail($product->get_id(), 'woocommerce_thumbnail', array(
                            'class' => 'woo-entry-image-main'
                        ));
                    }

                    // Zweites Bild für Hover-Effekt
                    $attachment_ids = $product->get_gallery_image_ids();
                    if (!empty($attachment_ids)) {
                        echo wp_get_attachment_image($attachment_ids[0], 'woocommerce_thumbnail', false, array(
                            'class' => 'woo-entry-image-secondary'
                        ));
                    }
                    ?>
                </a>
            </div>
        </div>

        <h2 class="woocommerce-loop-product__title">
            <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                <?php echo get_the_title(); ?>
            </a>
        </h2>

        <?php if (function_exists('wc_gzd_template_single_tax_info')) : ?>
            <?php wc_gzd_template_single_tax_info(); ?>
        <?php endif; ?>

        <div class="button-wrap">
            <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" 
               class="button product_type_simple">
                <?php echo esc_html__('Produkt gestalten', 'woocommerce'); ?>
            </a>
        </div>
    </div>
</li>