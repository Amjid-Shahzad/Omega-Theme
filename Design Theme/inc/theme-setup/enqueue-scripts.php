<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function theme_enqueue_assets() {
    // Main theme CSS
    wp_enqueue_style('theme-main-style', get_template_directory_uri() . '/assets/css/main.css', array(), filemtime(get_template_directory() . '/assets/css/main.css'));

    // Main JS (optional)
    wp_enqueue_script('theme-main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), filemtime(get_template_directory() . '/assets/js/main.js'), true);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_assets');

/**
 * Load the same CSS/JS into the block editor
 */
function theme_block_editor_assets() {
    wp_enqueue_style('theme-editor-style', get_template_directory_uri() . '/assets/css/main.css', array(), filemtime(get_template_directory() . '/assets/css/main.css'));
    wp_enqueue_script('theme-editor-js', get_template_directory_uri() . '/assets/js/main.js', array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'), filemtime(get_template_directory() . '/assets/js/main.js'), true);
}
add_action('enqueue_block_editor_assets', 'theme_block_editor_assets');

/**
 * Allow custom colors, gradients, and fonts inside the editor
 */
add_filter('block_editor_settings_all', function($settings) {
    $settings['disableCustomColors'] = false;
    $settings['disableCustomGradients'] = false;
    $settings['disableCustomFontSizes'] = false;
    return $settings;
});
