<?php
if (!defined('ABSPATH')) exit;

// Add meta boxes
function add_page_assets_metaboxes() {
    add_meta_box('page_css', __('Custom Page CSS', 'theme'), 'render_page_css_metabox', 'page', 'normal', 'high');
    add_meta_box('page_js', __('Custom Page JS', 'theme'), 'render_page_js_metabox', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_page_assets_metaboxes');

function render_page_css_metabox($post) {
    $slug = $post->post_name;
    $css_file = get_template_directory() . '/assets/css/pages/' . $slug . '.css';

    if (file_exists($css_file)) {
        $value = file_get_contents($css_file);
    } else {
        $value = get_post_meta($post->ID, '_page_css', true);
    }

    echo '<textarea id="page_css" name="page_css" style="width:100%;height:200px;">' 
        . esc_textarea($value) . 
        '</textarea>';
    echo '<p><button type="button" class="button button-secondary reload-css">🔄 Reload CSS</button></p>';
}

function render_page_js_metabox($post) {
    $slug = $post->post_name;
    $js_file = get_template_directory() . '/assets/js/pages/' . $slug . '.js';

    if (file_exists($js_file)) {
        $value = file_get_contents($js_file);
    } else {
        $value = get_post_meta($post->ID, '_page_js', true);
    }

    echo '<textarea id="page_js" name="page_js" style="width:100%;height:200px;">' 
        . esc_textarea($value) . 
        '</textarea>';
    echo '<p><button type="button" class="button button-secondary reload-js">🔄 Reload JS</button></p>';
}

// Save meta values (writes back into the actual files)
function save_page_assets($post_id) {
    if (get_post_type($post_id) !== 'page' || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) return;

    $slug = get_post($post_id)->post_name;
    $js_dir = get_template_directory() . '/assets/js/pages/';
    $css_dir = get_template_directory() . '/assets/css/pages/';

    if (isset($_POST['page_css'])) {
        $code = wp_unslash($_POST['page_css']);
        update_post_meta($post_id, '_page_css', $code);
        file_put_contents($css_dir . $slug . '.css', $code);
    }

    if (isset($_POST['page_js'])) {
        $code = wp_unslash($_POST['page_js']);
        update_post_meta($post_id, '_page_js', $code);
        file_put_contents($js_dir . $slug . '.js', $code);
    }
}
add_action('save_post', 'save_page_assets');

