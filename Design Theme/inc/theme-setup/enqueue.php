<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -------------------
// Enqueue styles & scripts
// -------------------
function load_files() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();


    
    
    // -------------------
    // Dynamic Header CSS/JS
    // -------------------
    $header_template = get_theme_mod('header_template', 'header-classic');

    $css_file = $theme_dir . '/inc/headers/header-templates/' . $header_template . '.css'; 
    $js_file = $theme_dir . '/inc/headers/header-templates/' . $header_template . '.js';
   

    $css_file_url = $theme_uri . '/inc/headers/header-templates/' . $header_template . '.css';
    $js_file_url = $theme_uri . '/inc/headers/header-templates/' . $header_template . '.js';

    if ( file_exists($css_file) ) {
        wp_enqueue_style('header-' . $header_template, $css_file_url, [], filemtime($css_file));
    }

    if ( file_exists($js_file) ) {
        wp_enqueue_script('header-' . $header_template, $js_file_url, ['jquery'], filemtime($js_file), true);
    }
    // -------------------
    // Core CSS
    // -------------------
    wp_enqueue_style('main-style', $theme_uri . '/assets/css/main.css', [], filemtime($theme_dir . '/assets/css/main.css'), 'all');
    wp_enqueue_style('site-header-style', $theme_uri . '/assets/css/site-header.css', [], filemtime($theme_dir . '/assets/css/site-header.css'), 'all');
    wp_enqueue_style('site-footer-style', $theme_uri . '/assets/css/site-footer.css', [], filemtime($theme_dir . '/assets/css/site-footer.css'), 'all');
    wp_enqueue_style('animation-library', $theme_uri . '/inc/animation/animation-library.css', [], filemtime($theme_dir . '/inc/animation/animation-library.css'), 'all'); 
    wp_enqueue_style('woocommerce-style', $theme_uri . '/assets/css/woocommerce.css', [], filemtime($theme_dir . '/assets/css/woocommerce.css'), 'all');



    // -------------------
    // Core JS
    // -------------------    
    wp_enqueue_script('main-script', $theme_uri . '/assets/js/main.js', ['jquery'], filemtime($theme_dir . '/assets/js/main.js'), true);

    wp_enqueue_script('header-funs', $theme_uri . '/assets/js/header-funs.js', ['jquery'], filemtime($theme_dir . '/assets/js/header-funs.js'), true);
   
    wp_enqueue_script('theme-mega-menu', $theme_uri . '/assets/js/mega-menu.js', ['jquery'], filemtime($theme_dir . '/assets/js/mega-menu.js'), true);

    wp_enqueue_script('footer-customizer-groups',$theme_uri . '/assets/js/customizer-footer-groups.js',['jquery', 'customize-controls'],false,true);

}
add_action( 'wp_enqueue_scripts', 'load_files', 20 );







// -------------------
// Footer Enqueue
// -------------------
function load_footer_files() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();

    // Get current footer setting
    $footer_template = get_theme_mod('footer_template', 'footer-classic');
    $footer_slug     = str_replace(['footer-', 'dynamic-'], '', $footer_template);

    // Handle STATIC footer templates
    if ( strpos($footer_template, 'footer-') === 0 ) {

        $css_file = $theme_dir . "/inc/footers/footer-templates/{$footer_template}.css";
        $js_file  = $theme_dir . "/inc/footers/footer-templates/{$footer_template}.js";

        $css_file_url = $theme_uri . "/inc/footers/footer-templates/{$footer_template}.css";
        $js_file_url  = $theme_uri . "/inc/footers/footer-templates/{$footer_template}.js";

        if ( file_exists($css_file) ) {
            wp_enqueue_style("footer-$footer_slug", $css_file_url, [], filemtime($css_file));
        }

        if ( file_exists($js_file) ) {
            wp_enqueue_script("footer-$footer_slug", $js_file_url, ['jquery'], filemtime($js_file), true);
        }

        // Customizer inline CSS/JS
        $custom_css = get_theme_mod('footer_custom_css', '');
        if ( ! empty($custom_css) ) {
            wp_add_inline_style("footer-$footer_slug", $custom_css);
        }

        $custom_js = get_theme_mod('footer_custom_js', '');
        if ( ! empty($custom_js) ) {
            wp_add_inline_script("footer-$footer_slug", $custom_js);
        }

    } 
    // Handle DYNAMIC footer sections (from footer_section CPT)
    elseif ( strpos($footer_template, 'dynamic-') === 0 ) {

        $dynamic_slug = str_replace('dynamic-', '', $footer_template);
        $css_file = $theme_dir . "/inc/footer-section/{$dynamic_slug}.css";
        $js_file  = $theme_dir . "/inc/footer-section/{$dynamic_slug}.js";

        $css_file_url = $theme_uri . "/inc/footer-section/{$dynamic_slug}.css";
        $js_file_url  = $theme_uri . "/inc/footer-section/{$dynamic_slug}.js";

        if ( file_exists($css_file) ) {
            wp_enqueue_style("footer-dynamic-$dynamic_slug", $css_file_url, [], filemtime($css_file));
        }

        if ( file_exists($js_file) ) {
            wp_enqueue_script("footer-dynamic-$dynamic_slug", $js_file_url, ['jquery'], filemtime($js_file), true);
        }
    }
}
add_action('wp_enqueue_scripts', 'load_footer_files',999);

