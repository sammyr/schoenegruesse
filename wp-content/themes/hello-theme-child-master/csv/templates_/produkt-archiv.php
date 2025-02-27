 <?php
/**
 * Template Name: Produktübersicht
 *
 * @package OceanWP WordPress theme
 */
get_header(); 
ini_set('display_errors', 0);
$feature_image_url = get_the_post_thumbnail_url(get_the_ID(),'full');


if ($feature_image_url){
?>

<div class="shop_seite_inhalt">
	



<style>
.elementor-587 .elementor-element.elementor-element-8d40875:not(.elementor-motion-effects-element-type-background), .elementor-587 .elementor-element.elementor-element-8d40875 > .elementor-motion-effects-container > .elementor-motion-effects-layer {
    background-image: url("<?php print $feature_image_url; ?>");
}
.elementor-587 .elementor-element.elementor-element-7ac952a .elementor-spacer-inner {
    height: 560px;
}
.elementor-587 .elementor-element.elementor-element-8d40875:not(.elementor-motion-effects-element-type-background), .elementor-587 .elementor-element.elementor-element-8d40875 > .elementor-motion-effects-container > .elementor-motion-effects-layer {
    background-attachment: fixed;
}

</style>

<?php

$oo = '


<div data-elementor-type="post" data-elementor-id="587" class="elementor elementor-587" data-elementor-settings="[]">
			<div class="elementor-inner">
				<div class="elementor-section-wrap">
							<section class="elementor-element elementor-element-8d40875 elementor-section-full_width elementor-section-height-default elementor-section-height-default elementor-section elementor-top-section" data-id="8d40875" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
						<div class="elementor-container elementor-column-gap-no">
				<div class="elementor-row">
				<div class="elementor-element elementor-element-d0adfcd elementor-column elementor-col-100 elementor-top-column" data-id="d0adfcd" data-element_type="column">
			<div class="elementor-column-wrap  elementor-element-populated">
					<div class="elementor-widget-wrap">
				<div class="elementor-element elementor-element-7ac952a elementor-widget elementor-widget-spacer" data-id="7ac952a" data-element_type="widget" data-widget_type="spacer.default">
				<div class="elementor-widget-container">
					<div class="elementor-spacer">
			<div class="elementor-spacer-inner"></div>
		</div>
				</div>
				</div>
						</div>
			</div>
		</div>
						</div>
			</div>
		</section>
						</div>
			</div>
		</div>


';




?>










</div>

<?php
}
?>


	<?php do_action( 'ocean_before_content_wrap' ); ?>

	<div id="content-wrap" class="container clr">

		<?php do_action( 'ocean_before_primary' ); ?>

		<div id="primary" class="content-area clr">

			<?php do_action( 'ocean_before_content' ); ?>

			<div id="content" class="site-content clr">


				<?php do_action( 'ocean_before_content_inner' ); ?>

				<?php
				// Elementor `single` location
				if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
					
					// Start loop
					while ( have_posts() ) : the_post();

							// EDD Page
							if ( is_singular( 'download') ) {
								get_template_part( 'partials/edd/single' );
							}

							// Single Page
							elseif ( is_singular( 'page' ) ) {

								//get_template_part( 'partials/page/layout' );
							?>





<h2 class="shop_seite_titel"><?php print get_the_title(); ?></h2>


<div class="shop_seite_inhalt_text"><?php print the_content(); ?></div>
<?php

function multiexplode ($delimiters,$string) {

    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

$produkt_pod = pods( 'page', get_the_id() );

$produkt_kategorie = $produkt_pod->display('kategorie');

$produkt_kategorien = multiexplode(array(",","and"),$produkt_kategorie);

$produkt_kategorien = array_filter($produkt_kategorien, create_function('$x','return preg_match("#\S#", $x);')); 

$last_key = end($produkt_kategorien);

foreach ($produkt_kategorien as &$kategorie) {

	if ($kategorie == $last_key)
	$kategorie_list .= $kategorie;
		else
	$kategorie_list .= $kategorie.',';
	
}


//print $kategorie_list;

//print '[products columns="3" orderby="popularity" order="DESC" category="'.$kategorie_list.'"]';

//print do_shortcode( '[featured_products prdctfltr="yes" ajax="yes" ]' );


print do_shortcode( '
	[products columns="3" prdctfltr="yes" ajax="no" orderby="menu_order"  order="ASC" category="'.$kategorie_list.'"]
	' );




							}

							// Library post types
		    				elseif ( is_singular( 'oceanwp_library' )
		    						|| is_singular( 'elementor_library' ) ) {

		    					get_template_part( 'partials/library/layout' );

		    				}

							// All other post types.
							else {

		    					get_template_part( 'partials/single/layout', get_post_type() );

		  					}

					endwhile;

				} ?>

				<?php do_action( 'ocean_after_content_inner' ); ?>

			</div><!-- #content -->

			<?php do_action( 'ocean_after_content' ); ?>

		</div><!-- #primary -->

		<?php do_action( 'ocean_after_primary' ); ?>

	</div><!-- #content-wrap -->

	<?php do_action( 'ocean_after_content_wrap' ); ?>

<?php get_footer(); 


?>
