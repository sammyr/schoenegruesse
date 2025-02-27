<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function product_gallery_thumbnail_description() {
    if (!is_admin()) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.woocommerce-product-gallery__image a').each(function() {
                var $link = $(this);
                var $img = $link.find('img');
                var alt = $img.attr('alt');
                if (alt) {
                    $link.after('<div class="image-description">' + alt + '</div>');
                }
            });
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'product_gallery_thumbnail_description');

function product_archive_gallery_hover() {
    if (!is_admin()) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(".woo-entry-image-main").hover(
                function() {
                    $(this).parent("a").parent("div").parent(".image-wrap").find(".item_target").css({
                        "opacity": "1",
                        "visibility": "visible"
                    });
                },
                function() {
                    $(this).parent("a").parent("div").parent(".image-wrap").find(".item_target").css({
                        "opacity": "0",
                        "visibility": "hidden"
                    });
                }
            );
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'product_archive_gallery_hover');
