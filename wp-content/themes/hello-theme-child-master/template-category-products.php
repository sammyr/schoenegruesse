<?php
/**
 * Template Name: Produkte nach Kategorie
 * 
 * Template für Seiten, die Produkte einer bestimmten Kategorie anzeigen sollen
 */

get_header();

// Hole die aktuelle Post-ID
$post_id = get_the_ID();

// Hole die Kategorie aus dem ACF-Feld
$kategorie = get_field('kategorie', $post_id);

// Hole den Seiteninhalt
$post = get_post($post_id);
$content = $post->post_content;

// Hole den Seitentitel für die CSS-Klasse
$slug = $post->post_name;
?>

<main id="content" class="site-main page-<?php echo esc_attr($slug); ?>">
    <div class="page-content">
        <?php
        // Zeige den Seiteninhalt an
        if (!empty($content)) {
            echo apply_filters('the_content', $content);
        }

        // Wenn eine Kategorie ausgewählt wurde
        if (!empty($kategorie)) {
            // Erstelle die Shortcode-Attribute
            $shortcode_atts = array(
                'category' => $kategorie,
                'columns' => 3,
                'orderby' => 'menu_order title',
                'order' => 'ASC',
                'paginate' => false,
                'limit' => -1 // Alle Produkte anzeigen
            );

            // Erstelle den Shortcode
            $shortcode = '[products';
            foreach ($shortcode_atts as $key => $value) {
                $shortcode .= ' ' . $key . '="' . esc_attr($value) . '"';
            }
            $shortcode .= ']';

            // Debug-Ausgabe
            if (WP_DEBUG) {
                error_log('Template Category Products - Seite: ' . $post->post_title);
                error_log('Template Category Products - Verwendete Kategorie: ' . $kategorie);
                error_log('Template Category Products - Generierter Shortcode: ' . $shortcode);
            }

            // Führe den Shortcode aus
            echo do_shortcode($shortcode);
        } else {
            if (WP_DEBUG) {
                error_log('Template Category Products - Keine Kategorie im ACF-Feld gefunden für Seite: ' . $post->post_title);
            }
        }
        ?>
    </div>
</main>

<?php
get_footer();
