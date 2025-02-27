<?php

/**
 * Briefumschläge auf der Cartpage
 */

function prefix_cart_envelope() {
	


	$briefumschlaege = pods( 'briefumschlaege', $params );

 	print '

	<style>
	.flex-container {
	  display: flex;
	  flex-wrap: wrap;
	  min-height: 150px;
	}
	.envelope_wrap {
		margin: auto;
		text-align: center;
	}

	.flex-container > div {
	  width: 120px;
	  margin: 10px;
	  text-align: center;
	  line-height: 25px;
	  font-size: 12px;
	}
	.flex-container img {
		border: 1px solid #fff;
	}	
	.flex-container img:hover {
		border: 1px solid #e9e9e9;
	}
	.envelope_title {
		display: block;
		width: 100%;
		font-weight: 600;
	}	
	.cart_totals h2 {
		display:none;
	}
	</style>


<div class="envelope_wrap">
<div class="envelope_title">Briefumschlagauswahl</div>
Bitte auf die Briefumschläge klicken um diese der Bestellung hinzuzufügen.
 	<div class="envelope_line flex-container">
 	';





	$params = array(
	'limit'   => -1  // Return all rows

	);


    $briefumschlaege = pods( 'product', $params );


	if ( 0 < $briefumschlaege->total() ) { 
  		 while ( $briefumschlaege->fetch() ) {
  		 	if ($briefumschlaege->display('product_cat') == "Briefumschlag"){

            $briefumschlag_title = $briefumschlaege->display('post_title');
            $briefumschlag_title = str_replace("Briefumschlag", "", $briefumschlag_title);       
            $briefumschlag_produkt_id = $briefumschlaege->display('ID');
			$briefumschlag_produkt_image =  $briefumschlaege->display('post_thumbnail_url');

  		 	print '<div><a title="Briefumschlag Farbe: '.$briefumschlag_title.'" href="?add-to-cart='.$briefumschlag_produkt_id.'"><img src="'.$briefumschlag_produkt_image.'"></a><br>'.$briefumschlag_title.'</div>';

			}
  		 }

  	}




 print '

<script>
// Remove URL Tag Parameter from Address Bar
if (window.parent.location.href.match(/add-to-cart=/)){
    if (typeof (history.pushState) != "undefined") {
        var obj = { Title: document.title, Url: window.parent.location.pathname };
        history.pushState(obj, obj.Title, obj.Url);
    } else {
        window.parent.location = window.parent.location.pathname;
    }
}
</script>


 </div></div>';



}
add_action( 'woocommerce_after_cart_table', 'prefix_cart_envelope', 1 );






/**
 * Briefumschläge als Shortcode auf der Produktseite
 */




function set_envelop_on_single_product() {
    // Prüfe ob Pods aktiv ist
    if (!function_exists('pods')) {
        return '<p>Diese Funktion ist temporär nicht verfügbar.</p>';
    }

	$params = array(
	'limit'   => -1  // Return all rows
	);


	$briefumschlaege = pods( 'briefumschlaege', $params );
    $produkt_pod = pods( 'product', get_the_id() );
    $product_envelope = $produkt_pod->field('briefumschlaege');


 	print '

	<style>
	.flex-container {
	  display: flex;
	  flex-wrap: wrap;
	  min-height: 150px;
	}
	.envelope_wrap {
		margin: auto;
		text-align: center;
	}

	.flex-container > div {
	  width: 120px;
	  margin: 10px;
	  text-align: center;
	  line-height: 25px;
	  font-size: 12px;
	}
	.flex-container img {
		border: 1px solid #fff;
		width: 150px;
		}	
	.flex-container img:hover {
		border: 1px solid #e9e9e9;
	}
	.envelope_title {
		display: block;
		width: 100%;
		font-weight: 600;
	}	
	.cart_totals h2 {
		display:none;
	}
	</style>
';




print '

<div class="envelope_wrap">
<div class="envelope_title">Briefumschlagauswahl</div>
Bitte auf die Briefumschläge klicken um diese der Bestellung hinzuzufügen.
 	<div class="envelope_line flex-container">
 	';






    if ($product_envelope){

        for ($i=0; $i<count($product_envelope); $i++) {

        	$envelope_pod = pods( 'product', $product_envelope[$i]['ID'] );

            $briefumschlag_title = $envelope_pod->display('post_title');
            $briefumschlag_title = str_replace("Briefumschlag", "", $briefumschlag_title);       
            $briefumschlag_produkt_id = $envelope_pod->display('ID');
			$briefumschlag_produkt_image =  $envelope_pod->display('post_thumbnail_url');

			$product = wc_get_product( $briefumschlag_produkt_id );
			$briefumschlag_produkt_price =  $product->get_price();

  		 	print '<div><a title="Briefumschlag Farbe: '.$briefumschlag_title.'" href="?add-to-cart='.$briefumschlag_produkt_id.'"><img src="'.$briefumschlag_produkt_image.'"></a><br>'.$briefumschlag_title.' </div>';

        }

    }




 print '

<script>
// Remove URL Tag Parameter from Address Bar
if (window.parent.location.href.match(/add-to-cart=/)){
    if (typeof (history.pushState) != "undefined") {
        var obj = { Title: document.title, Url: window.parent.location.pathname };
        history.pushState(obj, obj.Title, obj.Url);
    } else {
        window.parent.location = window.parent.location.pathname;
    }
}
</script>


 </div></div>';








}
add_shortcode('set_envelopes', 'set_envelop_on_single_product');