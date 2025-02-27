<?php
/**
 * Product Loop Start
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<ul class="products columns-<?php echo esc_attr(wc_get_loop_prop('columns')); ?>"><?php