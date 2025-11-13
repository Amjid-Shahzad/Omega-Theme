<?php
if (!defined('ABSPATH')) exit;

/**
 * Enqueue Mega Menu CSS/JS files automatically
 */
function theme_enqueue_mega_menu_assets() {
    $mega_menus = get_posts([
        'post_type'      => 'mega_menu',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    ]);

    if (empty($mega_menus)) return;

    $theme_dir = get_stylesheet_directory();
    $theme_uri = get_stylesheet_directory_uri();

    $css_base = $theme_dir . '/assets/css/mega-menus/';
    $js_base  = $theme_dir . '/assets/js/mega-menus/';

    foreach ($mega_menus as $menu) {
        $slug = sanitize_title($menu->post_name);
        $css_file = "{$css_base}{$slug}.css";
        $js_file  = "{$js_base}{$slug}.js";

        if (file_exists($css_file)) {
            wp_enqueue_style(
                "mega-menu-{$slug}-css",
                "{$theme_uri}/assets/css/mega-menus/{$slug}.css",
                [],
                filemtime($css_file)
            );
        }

        if (file_exists($js_file)) {
            wp_enqueue_script(
                "mega-menu-{$slug}-js",
                "{$theme_uri}/assets/js/mega-menus/{$slug}.js",
                ['jquery'],
                filemtime($js_file),
                true
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'theme_enqueue_mega_menu_assets', 999);
add_action('enqueue_block_assets', 'theme_enqueue_mega_menu_assets');
add_action('enqueue_block_editor_assets', 'theme_enqueue_mega_menu_assets');
