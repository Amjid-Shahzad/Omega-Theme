<?php
// 1) Register meta keys and expose them to REST so the editor can use wp.data
function mytheme_register_page_meta() {
    register_post_meta( 'page', 'page_custom_css', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
        'auth_callback' => function() { return current_user_can( 'edit_pages' ); },
    ] );

    register_post_meta( 'page', 'page_custom_js', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
        'auth_callback' => function() { return current_user_can( 'edit_pages' ); },
    ] );
}
add_action( 'init', 'mytheme_register_page_meta' );


// 2) Enqueue the editor script (loaded in Gutenberg)
function mytheme_enqueue_editor_live_preview() {
    $asset_path = get_template_directory_uri() . '/assets/js/editor-live-preview.js';
    wp_enqueue_script(
        'mytheme-editor-live-preview',
        $asset_path,
        [ 'wp-data', 'wp-dom-ready', 'wp-edit-post', 'wp-element' ],
        filemtime( get_template_directory() . '/assets/js/editor-live-preview.js' ),
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'mytheme_enqueue_editor_live_preview' );


// 3) On post save, write CSS/JS to uploads folder per page
function mytheme_write_page_assets_on_save( $post_id, $post, $update ) {
    // Only pages, only non-autosave, non-revision
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }
    if ( 'page' !== $post->post_type ) {
        return;
    }

    // Capability check
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Get saved meta
    $css = get_post_meta( $post_id, 'page_custom_css', true );
    $js  = get_post_meta( $post_id, 'page_custom_js', true );

    // Sanitize strings (basic) - allow newlines and common characters
    $css_sanitized = is_string( $css ) ? $css : '';
    $js_sanitized  = is_string( $js ) ? $js : '';

    // Get uploads dir and ensure subfolders exist
    $upload_dir = wp_upload_dir();
    $base_dir   = trailingslashit( $upload_dir['basedir'] ) . 'page-assets';
    $css_dir    = $base_dir . '/css';
    $js_dir     = $base_dir . '/js';

    if ( ! file_exists( $css_dir ) ) {
        wp_mkdir_p( $css_dir );
    }
    if ( ! file_exists( $js_dir ) ) {
        wp_mkdir_p( $js_dir );
    }

    // File paths
    $css_file = $css_dir . '/page-' . intval( $post_id ) . '.css';
    $js_file  = $js_dir  . '/page-' . intval( $post_id ) . '.js';

    // Write the CSS file
    if ( '' !== $css_sanitized ) {
        file_put_contents( $css_file, $css_sanitized );
    } else {
        // remove if empty
        if ( file_exists( $css_file ) ) {
            unlink( $css_file );
        }
    }

    // Write the JS file
    if ( '' !== $js_sanitized ) {
        file_put_contents( $js_file, $js_sanitized );
    } else {
        if ( file_exists( $js_file ) ) {
            unlink( $js_file );
        }
    }
}
add_action( 'save_post', 'mytheme_write_page_assets_on_save', 20, 3 );


// 4) Enqueue dynamic page CSS/JS for frontend when viewing the page
function mytheme_enqueue_dynamic_page_assets() {
    if ( ! is_singular( 'page' ) ) {
        return;
    }

    global $post;
    $post_id = $post->ID;
    $upload_dir = wp_upload_dir();
    $base_url   = trailingslashit( $upload_dir['baseurl'] ) . 'page-assets';
    $css_url    = $base_url . '/css/page-' . intval( $post_id ) . '.css';
    $js_url     = $base_url . '/js/page-' . intval( $post_id ) . '.js';

    // Enqueue CSS if file exists on disk
    $css_path = trailingslashit( $upload_dir['basedir'] ) . 'page-assets/css/page-' . intval( $post_id ) . '.css';
    if ( file_exists( $css_path ) ) {
        wp_enqueue_style( 'mytheme-page-' . $post_id . '-css', $css_url, [], filemtime( $css_path ) );
    }

    // Enqueue JS if file exists on disk
    $js_path = trailingslashit( $upload_dir['basedir'] ) . 'page-assets/js/page-' . intval( $post_id ) . '.js';
    if ( file_exists( $js_path ) ) {
        wp_enqueue_script( 'mytheme-page-' . $post_id . '-js', $js_url, [], filemtime( $js_path ), true );
    }
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_dynamic_page_assets' );
