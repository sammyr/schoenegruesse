<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// MENU HOVER FUNKTIONALITÄT
function start_MainMenu_Hover() {
    if (strpos(realpath(''), 'wp-admin') !== false) {
        return;
    }
    
    ?>
    <script>
    console.log('Script-Block erreicht');
    jQuery(document).ready(function($){
        console.log('Menu Hover Script gestartet');
        var image_url = [];
        
        <?php
        // ACF Felder für Menübilder abrufen
        $menu_items = get_posts(array(
            'post_type' => 'menue_bilder',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        echo "console.log('Gefundene Posts:', " . count($menu_items) . ");";
        
        // Hover-Effekte für Menübilder
        foreach($menu_items as $item) {
            $link_id = get_field('link_id', $item->ID);
            $bild_id = get_field('bild_id', $item->ID);
            $bilddatei_id = get_field('bilddatei', $item->ID);
            
            // Hole die vollständige Bild-URL
            $bilddatei = wp_get_attachment_url($bilddatei_id);
            
            echo "console.log('Menübild gefunden:', {";
            echo "  post_id: '" . $item->ID . "',";
            echo "  title: '" . esc_js($item->post_title) . "',";
            echo "  link_id: '" . esc_js($link_id) . "',";
            echo "  bild_id: '" . esc_js($bild_id) . "',";
            echo "  bilddatei_id: '" . esc_js($bilddatei_id) . "',";
            echo "  bilddatei_url: '" . esc_js($bilddatei) . "'";
            echo "});";
            
            if($link_id && $bild_id && $bilddatei) {
                ?>
                console.log('Füge Hover-Event hinzu für:', '<?php echo $link_id; ?>');
                image_url['<?php echo $link_id; ?>'] = $('.<?php echo $bild_id; ?> img').attr('srcset');
                
                $("li.<?php echo $link_id; ?> a").hover(function(){
                    console.log('Hover auf Link:', '<?php echo $link_id; ?>', 'Bild:', '<?php echo $bild_id; ?>');
                    $('.<?php echo $bild_id; ?> img').attr('srcset', '<?php echo $bilddatei; ?>');
                    $('.<?php echo $bild_id; ?> img').attr('style', ' ');
                }, function(){
                    console.log('Mouseleave auf Link:', '<?php echo $link_id; ?>');
                    $('.einladungen-rechts img').attr('style', 'opacity:0; transition: opacity 0.05s; -webkit-transition: opacity 0.05s;');
                });
                <?php
            }
        }
        ?>
        
        console.log('Menu Hover Script beendet');
    });
    </script>
    <?php
}

// Initialisiere die Menüfunktionalität
function init_menu_functionality() {
    add_action('wp_footer', 'start_MainMenu_Hover', 99);
    add_action('wp_enqueue_scripts', function() {
        wp_enqueue_script('jquery');
    });
}
