<?php
if (!defined('ABSPATH')) exit;

function mytheme_colors_page() {
    // Get current color values
    $colors = get_option('theme_colors', [
        'primary' => '#0073e6',
        'secondary' => '#ff6600',
        'accent' => '#00cc66',
        'background' => '#f5f5f5',
    ]);

    if (isset($_POST['save_theme_colors'])) {
        check_admin_referer('theme_colors_nonce', 'theme_colors_nonce_field');

        // Sanitize and save colors
        $colors['primary'] = sanitize_hex_color($_POST['primary'] ?? '#0073e6');
        $colors['secondary'] = sanitize_hex_color($_POST['secondary'] ?? '#ff6600');
        $colors['accent'] = sanitize_hex_color($_POST['accent'] ?? '#00cc66');
        $colors['background'] = sanitize_hex_color($_POST['background'] ?? '#f5f5f5');

        update_option('theme_colors', $colors);

        echo '<div class="notice notice-success is-dismissible"><p>Theme colors saved!</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>Theme Colors</h1>
        <form method="post">
            <?php wp_nonce_field('theme_colors_nonce', 'theme_colors_nonce_field'); ?>

            <p><label>Primary Color: </label> <input type="color" name="primary" value="<?php echo esc_attr($colors['primary']); ?>"></p>
            <p><label>Secondary Color: </label> <input type="color" name="secondary" value="<?php echo esc_attr($colors['secondary']); ?>"></p>
            <p><label>Accent Color: </label> <input type="color" name="accent" value="<?php echo esc_attr($colors['accent']); ?>"></p>
            <p><label>Background Color: </label> <input type="color" name="background" value="<?php echo esc_attr($colors['background']); ?>"></p>

            <p><input type="submit" name="save_theme_colors" class="button button-primary" value="Save Colors"></p>
        </form>
    </div>
    <?php
}
