<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('restore_elementor_connections')) {
    function restore_elementor_connections() {
        global $wpdb;
        
        // 1. Stelle sicher, dass alle Seiten den Elementor-Bearbeitungsmodus haben
        $wpdb->query("
            INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
            SELECT DISTINCT p.ID, '_elementor_edit_mode', 'builder'
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_elementor_edit_mode'
            WHERE p.post_type IN ('page', 'post', 'product')
            AND pm.meta_id IS NULL
            AND p.post_content LIKE '%elementor%'
        ");

        // 2. Setze die Elementor-Version für alle Seiten
        $elementor_version = '3.18.2'; // Aktuelle Version
        $wpdb->query($wpdb->prepare("
            INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
            SELECT DISTINCT p.ID, '_elementor_version', %s
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_elementor_version'
            WHERE p.post_type IN ('page', 'post', 'product')
            AND pm.meta_id IS NULL
            AND p.post_content LIKE '%elementor%'
        ", $elementor_version));

        // 3. Stelle Template-Typen wieder her
        $wpdb->query("
            INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
            SELECT DISTINCT p.ID, '_elementor_template_type', 'wp-page'
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_elementor_template_type'
            WHERE p.post_type IN ('page', 'post', 'product')
            AND pm.meta_id IS NULL
            AND p.post_content LIKE '%elementor%'
        ");

        // 4. Stelle Elementor-Daten aus dem Backup wieder her
        $backup_file = 'backup_2025-01-24-2033_Schne_Gre_856aac4fc6c3-db.sql';
        if (file_exists($backup_file)) {
            $sql_content = file_get_contents($backup_file);
            
            // Extrahiere Elementor-Daten aus dem Backup
            if (preg_match_all("/INSERT INTO `wp_postmeta` .*?VALUES.*?'_elementor_data'.*?'(.*?)'/s", $sql_content, $matches)) {
                foreach ($matches[1] as $elementor_data) {
                    $elementor_data = stripslashes($elementor_data);
                    
                    // Finde die zugehörige Post-ID
                    if (preg_match("/post_id\s*=\s*(\d+)/", $elementor_data, $post_match)) {
                        $post_id = $post_match[1];
                        
                        // Aktualisiere oder füge Elementor-Daten hinzu
                        $wpdb->query($wpdb->prepare("
                            INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
                            VALUES (%d, '_elementor_data', %s)
                            ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)
                        ", $post_id, $elementor_data));
                    }
                }
            }
        }

        // 5. Bereinige den Elementor-Cache
        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
            \Elementor\Plugin::$instance->posts_css_manager->clear_cache();
        }

        // 6. Regeneriere Elementor-Einstellungen
        update_option('elementor_disable_color_schemes', 'yes');
        update_option('elementor_disable_typography_schemes', 'yes');
        update_option('elementor_load_fa4_shim', 'yes');
        update_option('elementor_enable_lightbox', 'yes');

        // 7. Stelle Elementor Pro Einstellungen wieder her
        if (defined('ELEMENTOR_PRO_VERSION')) {
            update_option('elementor_pro_version', ELEMENTOR_PRO_VERSION);
        }

        return true;
    }
}

if (!function_exists('elementor_restore_admin_page')) {
    // Füge Admin-Menüpunkt hinzu
    add_action('admin_menu', function() {
        add_management_page(
            'Elementor Wiederherstellung',
            'Elementor Wiederherstellung',
            'manage_options',
            'elementor-restore',
            'elementor_restore_admin_page'
        );
    });

    function elementor_restore_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_POST['restore_elementor'])) {
            check_admin_referer('restore_elementor_action', 'restore_elementor_nonce');
            
            $result = restore_elementor_connections();
            
            if ($result) {
                echo '<div class="notice notice-success"><p>';
                echo 'Elementor-Verknüpfungen wurden erfolgreich wiederhergestellt.';
                echo '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>';
                echo 'Fehler beim Wiederherstellen der Elementor-Verknüpfungen.';
                echo '</p></div>';
            }
        }
        
        ?>
        <div class="wrap">
            <h1>Elementor Wiederherstellung</h1>
            
            <form method="post">
                <?php wp_nonce_field('restore_elementor_action', 'restore_elementor_nonce'); ?>
                <p>Diese Funktion stellt alle Elementor-Verknüpfungen und -Einstellungen wieder her.</p>
                <p><strong>Wichtig:</strong> Bitte erstellen Sie ein Backup Ihrer Datenbank, bevor Sie fortfahren.</p>
                <input type="submit" name="restore_elementor" class="button button-primary" value="Verknüpfungen wiederherstellen">
            </form>
        </div>
        <?php
    }
}
