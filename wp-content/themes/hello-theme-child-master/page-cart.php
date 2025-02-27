<?php
/**
 * Template Name: WooCommerce Cart
 * 
 * This template is used to display the WooCommerce cart page.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header(); ?>

<div class="page-content">
    <div class="woocommerce-cart-wrapper">
        <?php 
        while (have_posts()) : 
            the_post();
            
            if (function_exists('WC') && isset(WC()->cart)) {
                if (WC()->cart->is_empty()) {
                    wc_get_template('cart/cart-empty.php');
                } else {
                    wc_get_template('cart/cart.php');
                }
            } else {
                echo '<p>' . esc_html__('WooCommerce ist nicht aktiviert oder nicht korrekt initialisiert.', 'hello-elementor-child') . '</p>';
            }
            
        endwhile;
        ?>
    </div>
</div>

<?php get_footer(); ?>
