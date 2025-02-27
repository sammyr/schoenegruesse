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
            cart_id: cart_id
          },
          success: function( response ) {
            $('.cart_totals').unblock();
          }
        }
      )
    });
  });
})(jQuery);