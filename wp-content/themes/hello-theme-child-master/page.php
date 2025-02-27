<?php
/**
 * The template for displaying all pages.
 */

get_header();

// Hole die aktuelle Post-ID
$post_id = get_the_ID();

// Hole die Kategorie aus dem ACF-Feld
$kategorie = get_field('kategorie', $post_id);

// Debug-Ausgabe
if (WP_DEBUG) {
    error_log('Page Template - Post ID: ' . $post_id);
    error_log('Page Template - Kategorie: ' . ($kategorie ? $kategorie : 'keine'));
}

// Hole den Seiteninhalt
$post = get_post($post_id);
$content = $post->post_content;
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
                error_log('Page Template - Generierter Shortcode: ' . $shortcode);
            }

            // Führe den Shortcode aus
            echo do_shortcode($shortcode);
        }
        ?>
    </div>
</main>

<?php
get_footer();
