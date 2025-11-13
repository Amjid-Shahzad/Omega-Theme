<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Add CSS & JS Meta Boxes to Footer Sections
 */
function theme_add_footer_section_asset_boxes() {
    add_meta_box(
        'footer_section_css_box',
        __('Footer Section Custom CSS', 'design-theme'),
        'theme_render_footer_section_css_box',
        'footer_section',
        'normal',
        'default'
    );

    add_meta_box(
        'footer_section_js_box',
        __('Footer Section Custom JS', 'design-theme'),
        'theme_render_footer_section_js_box',
        'footer_section',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'theme_add_footer_section_asset_boxes');

/**
 * Render CSS Meta Box
 */
function theme_render_footer_section_css_box( $post ) {
    $slug = $post->post_name;
    $file = get_template_directory() . "/inc/footer/footer-templates/footer-{$slug}.css";

    if ( ! file_exists( $file ) ) {
        wp_mkdir_p( dirname( $file ) );
        file_put_contents( $file, "/* CSS for Footer Section: {$slug} */\n" );
    }

    $content = file_exists( $file ) ? file_get_contents( $file ) : '';
    echo '<textarea name="footer_section_css" style="width:100%;height:220px;font-family:monospace;">' .
        esc_textarea( $content ) . '</textarea>';
}

/**
 * Render JS Meta Box
 */
function theme_render_footer_section_js_box( $post ) {
    $slug = $post->post_name;
    $file = get_template_directory() . "/inc/footer/footer-templates/footer-{$slug}.js";

    if ( ! file_exists( $file ) ) {
        wp_mkdir_p( dirname( $file ) );
        file_put_contents( $file, "// JS for Footer Section: {$slug}\n" );
    }

    $content = file_exists( $file ) ? file_get_contents( $file ) : '';
    echo '<textarea name="footer_section_js" style="width:100%;height:220px;font-family:monospace;">' .
        esc_textarea( $content ) . '</textarea>';
}

/**
 * Save CSS & JS Meta Box Data
 */
function theme_save_footer_section_assets( $post_id ) {
    // Security check
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( get_post_type( $post_id ) !== 'footer_section' ) return;

    $slug = get_post_field( 'post_name', $post_id );

    $css_dir = get_template_directory() . '/inc/footer/footer-templates/';
    $js_dir  = get_template_directory() . '/inc/footer/footer-templates/';

    wp_mkdir_p( $css_dir );
    wp_mkdir_p( $js_dir );

    // Save CSS
    if ( isset( $_POST['footer_section_css'] ) ) {
        $css_content = wp_unslash( $_POST['footer_section_css'] );
        file_put_contents( "{$css_dir}footer-{$slug}.css", $css_content );
        update_post_meta( $post_id, '_footer_section_css', $css_content );
    }

    // Save JS
    if ( isset( $_POST['footer_section_js'] ) ) {
        $js_content = wp_unslash( $_POST['footer_section_js'] );
        file_put_contents( "{$js_dir}footer-{$slug}.js", $js_content );
        update_post_meta( $post_id, '_footer_section_js', $js_content );
    }
}
add_action('save_post', 'theme_save_footer_section_assets');
