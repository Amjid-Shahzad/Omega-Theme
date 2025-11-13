<?php
if (!defined('ABSPATH')) exit;

// Create assets for individual pages
function init_page_assets($post_id, $post, $update) {
    if ($post->post_type !== 'page' || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $slug = $post->post_name;
    if (empty($slug)) return;

    $js_dir  = get_template_directory() . '/assets/js/pages/';
    $css_dir = get_template_directory() . '/assets/css/pages/';
    if (!file_exists($js_dir)) mkdir($js_dir, 0755, true);
    if (!file_exists($css_dir)) mkdir($css_dir, 0755, true);

    $js_file  = $js_dir . $slug . '.js';
    $css_file = $css_dir . $slug . '.css';
    

    if (!file_exists($js_file)) file_put_contents($js_file, "// JS for page: {$slug}\nconsole.log('Page {$slug} JS loaded');\n");
    if (!file_exists($css_file)) file_put_contents($css_file, "/* CSS for page: {$slug} */\nbody.page-{$slug} {\n    /* custom styles */\n}\n");

    $global_js = get_template_directory() . '/assets/js/global.js';
    if (!file_exists($global_js)) file_put_contents($global_js, "// Global JS\nwindow.Theme = { log: function(msg){ console.log(msg); } };");
}
add_action('wp_insert_post', 'init_page_assets', 20, 3);

// On theme activation: run for all existing pages
function create_assets_for_existing_pages() {
    $pages = get_posts(['post_type' => 'page', 'numberposts' => -1, 'post_status' => 'publish']);
    foreach ($pages as $page) {
        init_page_assets($page->ID, $page, false);
    }
}
add_action('after_switch_theme', 'create_assets_for_existing_pages');
