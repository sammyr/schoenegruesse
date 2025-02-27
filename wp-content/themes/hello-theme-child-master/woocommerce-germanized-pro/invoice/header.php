<?php
/**
 * Invoice Header
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var \Vendidero\StoreaBill\Document\Document $document
 */
global $document;
?>
<header id="header">
	<?php echo sab_render_blocks( $document->get_template()->get_header_blocks() ); ?>
</header>
