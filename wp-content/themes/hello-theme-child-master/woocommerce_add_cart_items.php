<?php




/**
 * Wünsche & Bestätigung Felder anlegen
 */
function prefix_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
 foreach( $item as $cart_item_key=>$cart_item ) {

     if( isset( $cart_item['notes'] ) ) {
         $item->add_meta_data( 'notes', $cart_item['notes'], true );
     }
   //  if( isset( $cart_item['ok'] ) ) {
    //     $item->add_meta_data( 'ok', $cart_item['ok'], true );
   //  }
     
 }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'prefix_checkout_create_order_line_item', 10, 4 );






/**
 * Wünsche & Bestätigung in Warenkorb anzeigen
 */

function prefix_after_cart_item_name( $cart_item, $cart_item_key ) {
 $notes = isset( $cart_item['notes'] ) ? $cart_item['notes'] : '';
 $ok = isset( $cart_item['ok'] ) ? $cart_item['ok'] : '';



	if ($cart_item['fpd_data']['fpd_product']){
		 printf(
		 '<br><div><input type="checkbox" class="%s" id="cart_ok_%s" data-cart-id="%s" name="check">ja, ich habe die Inhalte dieser Karte überprüft.</div>',
		 'prefix-cart-ok',
		 $cart_item_key,
		 $cart_item_key,
		 $ok
		 );

		 printf(
		 '<div>Besondere Hinweise für diese Karte?<br><textarea class="%s" id="cart_notes_%s" data-cart-id="%s">%s</textarea></div>',
		 'prefix-cart-notes',
		 $cart_item_key,
		 $cart_item_key,
		 $notes
		 );

	}


}


add_action( 'woocommerce_after_cart_item_name', 'prefix_after_cart_item_name', 10, 2 );






/**
 * Warenkorb AJAX Funktionen laden
 */
function prefix_enqueue_scripts() {
 wp_register_script( 'prefix-script', '/wp-content/themes/oceanwp-child/update-cart-item-ajax.js', array( 'jquery-blockui' ), time(), true );
 wp_localize_script(
 'prefix-script',
 'prefix_vars',
 array(
 'ajaxurl' => admin_url( 'admin-ajax.php' )
 )
 );
 wp_enqueue_script( 'prefix-script' );
}
add_action( 'wp_enqueue_scripts', 'prefix_enqueue_scripts' );



/**
 * Warenkorb Felder Update
 */
function prefix_update_cart_notes() {
 // Do a nonce check
    
 if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'woocommerce-cart' ) ) {
 wp_send_json( array( 'nonce_fail' => 1 ) );
 exit;
 }
 // Save the notes to the cart meta
 $cart = WC()->cart->cart_contents;
 $cart_id = $_POST['cart_id'];
 $notes = $_POST['notes'];
 $ok = $_POST['ok'];

 $cart_item = $cart[$cart_id];
 $cart_item['notes'] = $notes;
 $cart_item['ok'] = $ok;
 WC()->cart->cart_contents[$cart_id] = $cart_item;
 WC()->cart->set_session();
 wp_send_json( array( 'success' => 1 ) );
 exit;
}
add_action( 'wp_ajax_prefix_update_cart_notes', 'prefix_update_cart_notes' );






/**
 * Add OK field to order object
 */
function cfwc_add_custom_data_to_order_ok( $item, $cart_item_key, $values, $order ) {
	//if ($cart_item['fpd_data']['fpd_product']){
        $item->add_meta_data( __( 'Kartendesign bestätigt', 'cfwc1' ), "Ja", true );
   // }

}
add_action( 'woocommerce_checkout_create_order_line_item', 'cfwc_add_custom_data_to_order_ok', 10, 4 );


/**
 *  Woocommerce Bestellungen
 */
function cfwc_add_custom_data_to_order_notes( $item, $cart_item_key, $values, $order ) {
	if ($values['notes']){
		 $notizen = $values['notes'];
         $item->add_meta_data( __( 'Besondere Wünsche für diese Karte', 'cfwc2' ), $notizen, true );
 
     }else{
         $notizen = "keine";
         $item->add_meta_data( __( 'Besondere Wünsche für diese Karte', 'cfwc2' ), $notizen, true );       
     }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'cfwc_add_custom_data_to_order_notes', 10, 4 );









function product_cart_update_script() {
    ?>


  <script type='text/javascript'>
          
           (function($){
 $(document).ready(function(){
 $('.prefix-cart-notes').on('change keyup paste',function(){
 $('.cart_totals').block({
 
 message: null,
 overlayCSS: {
 background: '#fff',
 opacity: 0.6
 
 }
 });
 var cart_id = $(this).data('cart-id');
 $.ajax(
 {
 type: 'POST',
 url: prefix_vars.ajaxurl,
 data: {
 action: 'prefix_update_cart_notes',
 security: $('#woocommerce-cart-nonce').val(),
 notes: $('#cart_notes_' + cart_id).val(),
 ok: $('#cart_ok_' + cart_id).val(),
 cart_id: cart_id
 },
 success: function( response ) {
 $('.cart_totals').unblock();
 }
 }
 )



 });
 });




$(document).on("click", ".checkout-button", function(e){
      var cart_id = $(this).data('cart-id');

      //$checken = jQuery(".prefix-cart-ok").is(":checked");
    var counter = 0;


    $('input:checkbox').each(function(){


        if (!jQuery(this).is(":checked")) {
             counter++;
             $(this).css("border","1px solid #ff1b1b");
             $(this).parent().css("color","#ff1b1b");
             if (counter == 1) cart_item_id = '#'+$(this).attr('id');
            // alert(cart_item_id);

        }

      }) 

        if (counter) {
         // alert(' Bitte bestätigen Sie die Richtigkeit ihrer Karten.');
         $(location).attr('href',cart_item_id);
          e.preventDefault();
        }



});



})(jQuery);



        </script>


    <?php
}
add_action('wp_head', 'product_cart_update_script');





add_action( 'wp_head', 'custom_inline_styles', 900 );
function custom_inline_styles(){
    if ( is_checkout() || is_cart() ){
        ?><style>
        .product-item-thumbnail { float:left; padding-right:10px;}
        .product-item-thumbnail img { margin: 0 !important;}
        </style><?php
    }
}

// Product thumbnail in checkout


// Produktanzeige in CART & CHECKOUT
function isa_woo_cart_attributes($cart_item, $cart_item_key) {
    global $product; 


    if (is_checkout()){ 
        echo "<style>.attachment-woocommerce_thumbnail {display:none;}</style>"; 

        $item_data = $cart_item_key['data']; 

        $post = get_post($item_data->id);
        $product_name = get_the_title($item_data->id);
        $thumb = get_the_post_thumbnail($item_data->id, array( 300, 150)); 
        $link =  get_permalink($item_data->id);
        $notizen = $cart_item_key['notes'];

        if ($notizen){
        $notices = "<br>Besondere Wünsche:<br>".$notizen."<br><br>Anzahl";
    }else{
    	$notices = "Anzahl";
    }

        echo '<div class="checkout_thumbnail" style="float: left; padding-right: 8px"><a class="checkout_title" href="'.$link.'" target="_blank">' . $thumb . '</a></div> 
        <a class="checkout_title" href="'.$link.'" target="_blank">'.$product_name.'</a><div class="addon">'.$notices.'</div>'; 

    } else {
        $item_data = $cart_item_key['data'];     	
		$product_name = get_the_title($item_data->id);
        $link =  get_permalink($item_data->id);

    	echo '<a class="checkout_title" href="'.$link.'" target="_blank">'.$product_name.'</a><div class="addon">'.$notices.'</div>'; 
    }


} 


//if (strpos($_SERVER['REQUEST_URI'], 'kasse') !== false)

//PRODUKTVARIATIONEN WERDEN IM CHECKOUT BEREICH NICHT ANGEZEIGT
//add_filter('woocommerce_cart_item_name', isa_woo_cart_attributes, 10, 2);









?>