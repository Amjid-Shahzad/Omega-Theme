<?php
if (!defined('ABSPATH')) exit;

function mytheme_admin_assets($hook) {
    $allowed_pages = [
        'toplevel_page_mytheme-dashboard',
        'theme-options_page_mytheme-global-js',
        'theme-options_page_mytheme-colors',
        'theme-options_page_mytheme-fonts',
    ];
    if (!in_array($hook, $allowed_pages)) return;

    // Admin CSS
    wp_enqueue_style('mytheme-admin-css', get_template_directory_uri() . '/inc/admin/css/admin-style.css', [], '1.0.0');

    // Admin JS
    wp_enqueue_script('mytheme-admin-js', get_template_directory_uri() . '/inc/admin/js/admin-script.js', ['jquery'], '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'mytheme_admin_assets');
