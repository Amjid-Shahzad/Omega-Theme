<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ---------------------------------------
 *  HELPER: Read color from main.css
 * ---------------------------------------
 */
function theme_get_color_from_css( $variable ) {
    $file = get_stylesheet_directory() . '/assets/css/main.css';
    if ( ! file_exists( $file ) ) return null;

    $content = file_get_contents( $file );
    if ( preg_match( '/--' . preg_quote( $variable, '/' ) . ':\s*([^;]+);/', $content, $m ) ) {
        return trim( $m[1] );
    }
    return null;
}

/**
 * ---------------------------------------
 *  HELPER: Update color inside main.css
 * ---------------------------------------
 */
function theme_update_color_in_css( $variable, $new_color ) {
    $file = get_stylesheet_directory() . '/assets/css/main.css';
    if ( ! file_exists( $file ) ) return false;

    $content = file_get_contents( $file );

    // Replace variable value or add it if missing
    if ( preg_match( '/--' . preg_quote( $variable, '/' ) . ':\s*([^;]+);/', $content ) ) {
        $content = preg_replace(
            '/(--' . preg_quote( $variable, '/' ) . ':\s*)([^;]+)(;)/',
            '${1}' . $new_color . '${3}',
            $content
        );
    } else {
        $content = preg_replace('/(:root\s*\{)/', "$1\n  --{$variable}: {$new_color};", $content);
    }

    return file_put_contents( $file, $content );
}

/**
 * ---------------------------------------
 *  REGISTER COLORS IN CUSTOMIZER
 * ---------------------------------------
 */
add_action( 'customize_register', function( $wp_customize ) {

    $colors = [
        'website-background',
        'page-background',
        'header-background',
        'footer-background',
        'mega-menu-background',
        'color-primary',
        'color-on-primary',
        'color-secondary',
        'color-on-secondary',
        'color-accent',
        'color-on-accent',
        'color-success',
        'color-error',
        'text-primary',
        'text-secondary',
        'text-muted',
        'link',
        'border',
        'divider',
        'shadow',
        'hover',
        'focus'
    ];

    // 🔸 Assign to the main "Global Settings" panel
    $wp_customize->add_section( 'theme_global_colors', [
        'title'       => __( 'Colors', 'your-theme' ),
        'priority'    => 10,
        'panel'       => 'global_settings_panel', // ✅ Inside the Global panel
        'description' => __( 'Manage all theme color variables linked to main.css and theme.json.', 'your-theme' ),
    ]);

    foreach ( $colors as $slug ) {
        $default = theme_get_color_from_css( $slug );

        $wp_customize->add_setting( "theme_color_{$slug}", [
            'default'   => $default ?: '#ffffff',
            'transport' => 'postMessage', // Live preview
        ]);

        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize,
            "theme_color_{$slug}",
            [
                'label'   => ucwords(str_replace('-', ' ', $slug)),
                'section' => 'theme_global_colors',
            ]
        ));
    }
});

/**
 * ---------------------------------------
 *  SAVE main.css + THEME.JSON ON PUBLISH
 * ---------------------------------------
 */
add_action( 'customize_save_after', function() {

    $colors = [
        'website-background',
        'page-background',
        'header-background',
        'footer-background',
        'mega-menu-background',
        'color-primary',
        'color-on-primary',
        'color-secondary',
        'color-on-secondary',
        'color-accent',
        'color-on-accent',
        'color-success',
        'color-error',
        'text-primary',
        'text-secondary',
        'text-muted',
        'link',
        'border',
        'divider',
        'shadow',
        'hover',
        'focus'
    ];

    // 🟢 Update main.css
    foreach ( $colors as $slug ) {
        $value = get_theme_mod( "theme_color_{$slug}" );
        if ( $value ) {
            theme_update_color_in_css( $slug, $value );
        }
    }

    // 🟢 Update theme.json
    $theme_json_path = get_stylesheet_directory() . '/theme.json';
    if ( file_exists( $theme_json_path ) ) {
        $json = json_decode( file_get_contents( $theme_json_path ), true );

        if ( isset( $json['settings']['color']['palette'] ) && is_array( $json['settings']['color']['palette'] ) ) {
            foreach ( $json['settings']['color']['palette'] as &$item ) {
                $slug = $item['slug'];
                $val  = get_theme_mod( "theme_color_{$slug}" );
                if ( $val ) {
                    $item['color'] = $val;
                }
            }

            file_put_contents(
                $theme_json_path,
                json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
            );

            error_log('🎨 theme.json updated to match main.css');
        }
    }

    error_log('✅ main.css updated successfully.');
});

/**
 * ---------------------------------------
 *  FORCE BLOCK EDITOR TO RELOAD NEW COLORS
 * ---------------------------------------
 */
add_filter( 'wp_theme_json_data_theme', function( $theme_json ) {
    $path = get_stylesheet_directory() . '/theme.json';
    if ( file_exists( $path ) ) {
        $data = $theme_json->get_data();
        $data['version'] = 3;
        $theme_json = new WP_Theme_JSON( $data, 'theme' );
    }
    return $theme_json;
});
