<?php
if (!defined('ABSPATH')) exit;

/**
 * --------------------------------------------------------
 * Enqueue Global Theme Styles and Scripts
 * --------------------------------------------------------
 */
function theme_enqueue_global_assets() {
    $main_css = get_stylesheet_directory() . '/assets/css/main.css';
    $main_js  = get_stylesheet_directory() . '/assets/js/main.js';

    // Enqueue main stylesheet
    if (file_exists($main_css)) {
        wp_enqueue_style(
            'theme-main-style',
            get_stylesheet_directory_uri() . '/assets/css/main.css',
            [],
            filemtime($main_css)
        );
    }

    // Enqueue main script
    if (file_exists($main_js)) {
        wp_enqueue_script(
            'theme-main-script',
            get_stylesheet_directory_uri() . '/assets/js/main.js',
            ['jquery'],
            filemtime($main_js),
            true
        );
    }

    /**
     * ---------------------------------------
     * Page-Specific CSS & JS (Frontend)
     * ---------------------------------------
     */
    if (is_page()) {
        global $post;
        $page_slug = $post->post_name;
        $page_id   = $post->ID;

        $page_css_path = get_stylesheet_directory() . "/assets/css/pages/{$page_slug}.css";
        $page_js_path  = get_stylesheet_directory() . "/assets/js/pages/{$page_slug}.js";

        // CSS
        if (file_exists($page_css_path)) {
            wp_enqueue_style(
                "page-style-{$page_id}",
                get_stylesheet_directory_uri() . "/assets/css/pages/{$page_slug}.css",
                ['theme-main-style'],
                filemtime($page_css_path)
            );
        }

        // JS
        if (file_exists($page_js_path)) {
            wp_enqueue_script(
                "page-script-{$page_id}",
                get_stylesheet_directory_uri() . "/assets/js/pages/{$page_slug}.js",
                ['theme-main-script', 'jquery'],
                filemtime($page_js_path),
                true
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'theme_enqueue_global_assets');



/**
 * --------------------------------------------------------
 * Load CSS/JS Inside Block Editor (for Live Preview)
 * --------------------------------------------------------
 */
function theme_enqueue_block_editor_assets() {
    global $post;
    if (!$post) return;

    // Editor main style
    $main_css = get_stylesheet_directory() . '/assets/css/editor.css';
    if (file_exists($main_css)) {
        wp_enqueue_style(
            'theme-editor-style',
            get_stylesheet_directory_uri() . '/assets/css/editor.css',
            [],
            filemtime($main_css)
        );
    }

    // Page-specific editor assets
    $page_slug = $post->post_name;
    $page_id   = $post->ID;

    $page_css_path = get_stylesheet_directory() . "/assets/css/pages/{$page_slug}.css";
    $page_js_path  = get_stylesheet_directory() . "/assets/js/pages/{$page_slug}.js";

    // CSS
    if (file_exists($page_css_path)) {
        wp_enqueue_style(
            "editor-page-style-{$page_id}",
            get_stylesheet_directory_uri() . "/assets/css/pages/{$page_slug}.css",
            ['theme-editor-style'],
            filemtime($page_css_path)
        );
    }

    // JS
    if (file_exists($page_js_path)) {
        wp_enqueue_script(
            "editor-page-script-{$page_id}",
            get_stylesheet_directory_uri() . "/assets/js/pages/{$page_slug}.js",
            ['wp-blocks', 'wp-element', 'wp-editor'],
            filemtime($page_js_path),
            true
        );
    }

    // Inline editor variables (safe)
    $primary_color   = esc_html(get_theme_mod('theme_primary_color', '#0073aa'));
    $secondary_color = esc_html(get_theme_mod('theme_secondary_color', '#111'));
    $base_font       = esc_html(get_theme_mod('theme_font_base-font', 'Inter, sans-serif'));
    $heading_font    = esc_html(get_theme_mod('theme_font_heading-font', 'Poppins, sans-serif'));
    $button_font     = esc_html(get_theme_mod('theme_font_button-font', 'Poppins, sans-serif'));

    $custom_css = "
        :root {
            --primary-color: {$primary_color};
            --secondary-color: {$secondary_color};
            --base-font: {$base_font};
            --heading-font: {$heading_font};
            --button-font: {$button_font};
        }
    ";
    wp_add_inline_style('theme-editor-style', $custom_css);
}
add_action('enqueue_block_assets', 'theme_enqueue_block_editor_assets');

