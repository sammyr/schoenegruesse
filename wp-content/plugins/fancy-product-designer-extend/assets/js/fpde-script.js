var $ = jQuery.noConflict();

$(document).ready(function(){
	
	$('form.cart').on('submit', function(event){
		//event.preventDefault();
	});

	$('.fpde-confirmation-form-check input').on('change', function(){
		if($(this).is(':checked'))
			$(this).next('span').hide();
	});

	$('#fpde-confirm').on('click', function(){
		var errors = [];
		$('.fpde-confirmation-form-check input').each(function(i,e){
			if(!$(e).is(':checked')){
				errors.push('error');
				$(e).next('span').show();
			}
			else
				$(e).next('span').hide();
		});

		if($('#fpde-confirm-note').val().toString().length > 1000){
			errors.push('error');
			$('#fpde-confirm-note').next('.error').show();
		}
		else
			$('#fpde-confirm-note').next('.error').hide();

		if(!errors.length){
			$('#add-to-cart').trigger('click');
			$.magnificPopup.close();
		}
	});

	$(document).on('click', '#add-to-cart-extended', function(){
		if(!$(this).hasClass('disabled') && !$(this).hasClass('wc-variation-selection-needed')){
			$.magnificPopup.open({
				closeBtnInside:true,
			  	items: {
			    	src: '#fdpe-popup',
			    	type: 'inline'
			  	},
			  	callbacks: {
			  		open: function(){
			  			$('.woocommerce-product-gallery__wrapper a').each(function(i,e){
			  				var img_class = (i == 0) ? 'active' : '';
			  				var src = $(e).attr('href');
			  				$('.fpde-confirmation-gallery').append('<img class="fpde-gallery-img fpde-gallery-img-'+i+' '+img_class+'" src="'+src+'">');
			  				$('.fpde-confirmation-gallery-nav ul').append('<li class="fpde-gallery-nav '+img_class+'"></li>');
			  			});

			  			$(document).on('click', '.fpde-gallery-nav', function(){
			  				$('.fpde-gallery-nav').removeClass('active');
							$(this).addClass('active');
							var ind = $(this).index();
							$('.fpde-gallery-img').removeClass('active');
							$('.fpde-gallery-img-'+ind).addClass('active');
						});

						$('#fpde-confirm-note').val($('#fpde-notes').val());
			  		},
			  		close: function(){
			  			$('.fpde-confirmation-gallery').empty();
			  			$('.fpde-confirmation-gallery-nav ul').empty();
			  		}
			  	}
			});
		}else{
			$('#add-to-cart').click();
		}
	});

	
	$(document).on('click', '.fpde-cart-nav', function(){
		$(this).closest('ul').find('.fpde-cart-nav').removeClass('active');
		$(this).addClass('active');
		var ind = $(this).index();
		var gallery_ind = $(this).data('gallery');
		$('.fpde-cart-gallery[data-gallery="'+gallery_ind+'"] .fpde-cart-img').removeClass('active');
		$('.fpde-cart-gallery[data-gallery="'+gallery_ind+'"] .fpde-cart-img-'+ind).addClass('active');
	});


	$('.fpde-cart-gallery').each(function(i,e) {
	    $(e).magnificPopup({
	        delegate: 'img',
	        type: 'image',
	        gallery: {
	          enabled:true
	        }
	    });
	});


	$('#fpde-confirm-note').on('change paste', function(){
		$('input[name="notes"]').val($(this).val());
	});


	

	if($('.woo-variation-raw-select').length > 0 && !$('.woo-variation-raw-select').val())
		$('#add-to-cart-extended').addClass('disabled wc-variation-selection-needed');

	$('.woo-variation-raw-select').on('change', function(){
		if($(this).val().length)
			$('#add-to-cart-extended').removeClass('disabled wc-variation-selection-needed');
	});

	$(document).on('mouseup', '.fpd-modal-close', function(){
		$('.fpd-done').trigger('click');
	});

	$('.fpd-done').on('click', function(){

		for(var i = 1; i < fancyProductDesigner.viewInstances.length; i++){
			fancyProductDesigner.viewInstances[i].toDataURL(function(dataURL) {
				
				var input = '<input type="hidden" name="fpde_product_thumbnails[]" value="'+dataURL+'">';
				$('#fpde-notes').before(input);

				$('.woocommerce-product-gallery__wrapper a:eq('+i+')').attr('href', dataURL);
				$('.woocommerce-product-gallery__image').eq(i).attr('data-thumb', dataURL);
				$('.woocommerce-product-gallery__wrapper a:eq('+i+') img').attr('src', dataURL);
				$('.woocommerce-product-gallery__wrapper a:eq('+i+') img').attr('data-src', dataURL);
				$('.woocommerce-product-gallery__wrapper a:eq('+i+') img').attr('data-large_image', dataURL);
				$('.woocommerce-product-gallery__wrapper a:eq('+i+') img').attr('srcset', dataURL); 
				$('.flex-control-thumbs li:eq('+i+') img').attr('src', dataURL);
			}, 'transparent', {format: 'png'});
		}

	});

	if($('.fpde-product-thumbnails').length > 0)
		$('.fpde-product-thumbnails').each(function(i,e){
			var ind = i+1*1;
			var thumb = $(e).val();

			$('.woocommerce-product-gallery__wrapper a:eq('+ind+')').attr('href', thumb);
			$('.woocommerce-product-gallery__image').eq(ind).attr('data-thumb', thumb);
			$('.woocommerce-product-gallery__wrapper a:eq('+ind+') img').attr('src', thumb);
			$('.woocommerce-product-gallery__wrapper a:eq('+ind+') img').attr('data-src', thumb);
			$('.woocommerce-product-gallery__wrapper a:eq('+ind+') img').attr('data-large_image', thumb);
			$('.woocommerce-product-gallery__wrapper a:eq('+ind+') img').attr('srcset', thumb);
			$('.flex-control-thumbs li:eq('+ind+') img').attr('src', thumb);
			
		});

});


$( document.body ).on('wc_fragments_refreshed', function(event){
	if($('.fpde-cart-gallery').length > 0){
		$('.fpde-cart-gallery').each(function(i,e) {
		    $(e).magnificPopup({
		        delegate: 'img',
		        type: 'image',
		        gallery: {
		          enabled:true
		        }
		    });
		});
	}
		
});