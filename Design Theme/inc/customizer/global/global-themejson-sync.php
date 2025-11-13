<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Upsert helper: update or add a palette entry by slug
 */
function theme_upsert_palette_entry( array &$palette, string $slug, $color, string $name ) {
    $found = false;
    foreach ( $palette as &$entry ) {
        if ( isset($entry['slug']) && $entry['slug'] === $slug ) {
            $entry['color'] = $color;
            $entry['name']  = $name;
            $found = true;
            break;
        }
    }
    if ( ! $found ) {
        $palette[] = array(
            'slug'  => $slug,
            'color' => $color,
            'name'  => $name,
        );
    }
}

/**
 * AJAX: write Customizer colors directly into theme.json
 */
function theme_ajax_update_theme_json() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_send_json_error( array( 'message' => 'insufficient_permissions' ), 403 );
    }
    check_ajax_referer( 'theme_json_sync' );

    $settings = isset($_POST['settings']) && is_array($_POST['settings']) ? $_POST['settings'] : array();

    // Map: customizer setting id -> slug (strip "theme_color_")
    $pairs = array();
    foreach ( $settings as $key => $val ) {
        if ( strpos( $key, 'theme_color_' ) === 0 ) {
            $slug = substr( $key, strlen( 'theme_color_' ) );
            $pairs[$slug] = sanitize_text_field( $val ); // color or '' (empty means revert to default)
        }
    }

    $theme_json_path = get_stylesheet_directory() . '/theme.json';
    if ( ! file_exists( $theme_json_path ) ) {
        wp_send_json_error( array( 'message' => 'theme_json_missing' ), 400 );
    }
    if ( ! is_writable( $theme_json_path ) ) {
        wp_send_json_error( array( 'message' => 'theme_json_not_writable' ), 400 );
    }

    $json = json_decode( file_get_contents( $theme_json_path ), true );
    if ( ! is_array( $json ) ) $json = array();

    if ( ! isset( $json['settings'] ) || ! is_array( $json['settings'] ) ) {
        $json['settings'] = array();
    }
    if ( ! isset( $json['settings']['color'] ) || ! is_array( $json['settings']['color'] ) ) {
        $json['settings']['color'] = array();
    }
    if ( ! isset( $json['settings']['color']['palette'] ) || ! is_array( $json['settings']['color']['palette'] ) ) {
        $json['settings']['color']['palette'] = array();
    }

    // Build an updatable palette
    $palette = $json['settings']['color']['palette'];

    // Human-friendly names
    $label = function( $slug ) {
        return ucwords( str_replace( array('-', '  '), array(' ', ' '), $slug ) );
    };

    foreach ( $pairs as $slug => $color ) {
        // If user cleared the color (''), we set it to null so WP can fall back to base theme defaults
        $normalized = ( $color === '' ) ? null : $color;
        theme_upsert_palette_entry( $palette, $slug, $normalized, $label( $slug ) );
    }

    // Save back
    $json['settings']['color']['palette'] = $palette;
    // Keep "custom": true (as you had)
    $json['settings']['color']['custom']  = true;

    $encoded = json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
    file_put_contents( $theme_json_path, $encoded );

    wp_send_json_success( array( 'message' => 'theme_json_updated' ) );
}
add_action( 'wp_ajax_theme_update_theme_json', 'theme_ajax_update_theme_json' );
