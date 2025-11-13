<?php
// ========================================================
// PROPERLY ENQUEUE EDITOR STYLES & SCRIPTS IN GUTENBERG
// ========================================================
function theme_block_editor_assets() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    $global_editor_css = $theme_dir . '/assets/css/editor.css';
    $global_editor_js  = $theme_dir . '/assets/js/editor.js';

    // ✅ Add global CSS using add_editor_style (loads inside iframe properly)
    if ( file_exists( $global_editor_css ) ) {
        add_editor_style( 'assets/css/editor.css' );
    }

    // ✅ Enqueue editor JS (safe, stays in parent context)
    if ( file_exists( $global_editor_js ) ) {
        wp_enqueue_script(
            'theme-editor-global',
            $theme_uri . '/assets/js/editor.js',
            array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
            filemtime( $global_editor_js ),
            true
        );
    }
}
add_action( 'enqueue_block_editor_assets', 'theme_block_editor_assets' );


// ========================================================
// PAGE-SPECIFIC STYLES → Inject into iframe correctly
// ========================================================
function theme_enqueue_block_assets() {
    global $post;
    if ( ! $post ) return;

    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    $page_slug = $post->post_name;
    $page_css  = $theme_dir . '/assets/css/pages/' . $page_slug . '.css';

    // ✅ Load page-specific CSS inside editor iframe
    if ( file_exists( $page_css ) ) {
        wp_enqueue_block_style(
            'core/post-content', // attach to a core block so it loads inside iframe
            array(
                'handle' => 'editor-page-' . $page_slug,
                'src'    => $theme_uri . '/assets/css/pages/' . $page_slug . '.css',
                'path'   => $page_css,
                'ver'    => filemtime( $page_css ),
            )
        );
    }
}
add_action( 'enqueue_block_assets', 'theme_enqueue_block_assets' );
