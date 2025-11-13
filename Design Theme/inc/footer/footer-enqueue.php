<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueue Footer CSS and JS dynamically
 */
function theme_enqueue_footer_assets() {
    if ( is_admin() ) return;

    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();

    $footer_choice = get_theme_mod('footer_template', 'footer-classic');
    $footer_slug   = str_replace(['footer-', 'dynamic-'], '', $footer_choice);

    // Correct paths in unified folder
    $css_file = "{$theme_dir}/inc/footer/footer-templates/footer-{$footer_slug}.css";
    $js_file  = "{$theme_dir}/inc/footer/footer-templates/footer-{$footer_slug}.js";

    $css_url  = "{$theme_uri}/inc/footer/footer-templates/footer-{$footer_slug}.css";
    $js_url   = "{$theme_uri}/inc/footer/footer-templates/footer-{$footer_slug}.js";

    // --- Load CSS if file exists ---
    if ( file_exists( $css_file ) ) {
        wp_enqueue_style(
            "footer-{$footer_slug}-css",
            $css_url,
            [],
            filemtime( $css_file )
        );
    }

    // --- Load JS if file exists ---
    if ( file_exists( $js_file ) ) {
        wp_enqueue_script(
            "footer-{$footer_slug}-js",
            $js_url,
            ['jquery'],
            filemtime( $js_file ),
            true
        );
    }

    // --- Add Customizer CSS/JS inline ---
    $custom_css = get_theme_mod('footer_custom_css', '');
    if ( ! empty( $custom_css ) ) {
        wp_add_inline_style( "footer-{$footer_slug}-css", $custom_css );
    }

    $custom_js = get_theme_mod('footer_custom_js', '');
    if ( ! empty( $custom_js ) ) {
        wp_add_inline_script( "footer-{$footer_slug}-js", $custom_js );
    }
}
add_action('wp_enqueue_scripts', 'theme_enqueue_footer_assets', 25);
