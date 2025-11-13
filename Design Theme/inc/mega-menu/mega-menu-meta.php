<?php
if (!defined('ABSPATH')) {
    exit;
}

//====================================================
//Mega Menu CSS & JS Meta Boxes
//Adds code editors to each Mega Menu edit screen
//====================================================
function mega_menu_asset_boxes() {
    add_meta_box(
        'mega_menu_css_box',
        __('Mega Menu Custom CSS', 'design-theme'),
        'theme_render_mega_menu_css_box',
        'mega_menu', // custom post type slug
        'normal',
        'default'
    );

    add_meta_box(
        'mega_menu_js_box',
        __('Mega Menu Custom JS', 'design-theme'),
        'theme_render_mega_menu_js_box',
        'mega_menu',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mega_menu_asset_boxes');

//=======================================
//Render CSS Field
//=======================================
function theme_render_mega_menu_css_box($post) {
    $slug = $post->post_name;
    $css_file = get_template_directory() . "/assets/css/mega-menus/{$slug}.css";

    // ------------Ensure file exists----------------
    if (!file_exists($css_file)) {
        wp_mkdir_p(dirname($css_file));
        file_put_contents($css_file, "/* CSS for Mega Menu: {$slug} */\n");
    }

    $value = file_exists($css_file) ? file_get_contents($css_file) : '';
    echo '<textarea name="mega_menu_css" id="mega_menu_css" style="width:100%;height:220px;font-family:monospace;">' .
        esc_textarea($value) .
        '</textarea>';
}

//=================================
//Render JS Field
//=================================
function theme_render_mega_menu_js_box($post) {
    $slug = $post->post_name;
    $js_file = get_template_directory() . "/assets/js/mega-menus/{$slug}.js";

    // Ensure file exists
    if (!file_exists($js_file)) {
        wp_mkdir_p(dirname($js_file));
        file_put_contents($js_file, "// JS for Mega Menu: {$slug}\n");
    }

    $value = file_exists($js_file) ? file_get_contents($js_file) : '';
    echo '<textarea name="mega_menu_js" id="mega_menu_js" style="width:100%;height:220px;font-family:monospace;">' .
        esc_textarea($value) .
        '</textarea>';
}

/**
 * Save CSS + JS contents back to files
 */
function theme_save_mega_menu_assets($post_id) {
    // Security check
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (get_post_type($post_id) !== 'mega_menu') {
        return;
    }

    $post = get_post($post_id);
    $slug = $post->post_name;

    $css_dir = get_template_directory() . '/assets/css/mega-menus/';
    $js_dir  = get_template_directory() . '/assets/js/mega-menus/';
    wp_mkdir_p($css_dir);
    wp_mkdir_p($js_dir);

    if (isset($_POST['mega_menu_css'])) {
        $css = wp_unslash($_POST['mega_menu_css']);
        file_put_contents("{$css_dir}{$slug}.css", $css);
        update_post_meta($post_id, '_mega_menu_css', $css);
    }

    if (isset($_POST['mega_menu_js'])) {
        $js = wp_unslash($_POST['mega_menu_js']);
        file_put_contents("{$js_dir}{$slug}.js", $js);
        update_post_meta($post_id, '_mega_menu_js', $js);
    }
}
add_action('save_post', 'theme_save_mega_menu_assets');
