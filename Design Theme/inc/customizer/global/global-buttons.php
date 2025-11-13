<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ---------------------------------------
 *  HELPER: Read button property from main.css
 * ---------------------------------------
 */
function theme_get_button_value( $variable ) {
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
 *  HELPER: Update button property inside main.css
 * ---------------------------------------
 */
function theme_update_button_value( $variable, $new_value ) {
    $file = get_stylesheet_directory() . '/assets/css/main.css';
    if ( ! file_exists( $file ) ) return false;

    $content = file_get_contents( $file );

    if ( preg_match( '/--' . preg_quote( $variable, '/' ) . ':\s*([^;]+);/', $content ) ) {
        $content = preg_replace(
            '/(--' . preg_quote( $variable, '/' ) . ':\s*)([^;]+)(;)/',
            '${1}' . $new_value . '${3}',
            $content
        );
    } else {
        $content = preg_replace('/(:root\s*\{)/', "$1\n  --{$variable}: {$new_value};", $content);
    }

    return file_put_contents( $file, $content );
}

/**
 * ---------------------------------------
 *  REGISTER BUTTON SETTINGS IN CUSTOMIZER
 * ---------------------------------------
 */
add_action( 'customize_register', function( $wp_customize ) {

    // Define all button variables
    $button_vars = [
        // Base
        'button-background'              => ['type' => 'color'],
        'button-text'                    => ['type' => 'color'],
        'button-border-color'            => ['type' => 'color'],
        'button-border-width'            => ['type' => 'text'],
        'button-radius'                  => ['type' => 'text'],
        'button-padding-top-buttom'      => ['type' => 'text'],
        'button-padding-right-left'      => ['type' => 'text'],
        // Hover
        'button-background-hover'        => ['type' => 'color'],
        'button-text-hover'              => ['type' => 'color'],
        'button-border-color-hover'      => ['type' => 'color'],
        'button-border-width-hover'      => ['type' => 'text'],
        'button-radius-hover'            => ['type' => 'text'],
        'button-padding-top-buttom-hover'=> ['type' => 'text'],
        'button-padding-right-left-hover'=> ['type' => 'text'],
    ];

    $wp_customize->add_section( 'theme_global_buttons', [
        'title'       => __( 'Buttons', 'your-theme' ),
        'priority'    => 20,
        'panel'       => 'global_settings_panel',
        'description' => __( 'Customize button styles synced with main.css and theme.json', 'your-theme' ),
    ]);

    foreach ( $button_vars as $slug => $options ) {
        $default = theme_get_button_value( $slug );

        $wp_customize->add_setting( "theme_button_{$slug}", [
            'default'   => $default ?: '',
            'transport' => 'postMessage',
        ]);

        // Color controls
        if ( $options['type'] === 'color' ) {
            $wp_customize->add_control( new WP_Customize_Color_Control(
                $wp_customize,
                "theme_button_{$slug}",
                [
                    'label'   => ucwords(str_replace('-', ' ', $slug)),
                    'section' => 'theme_global_buttons',
                ]
            ));
        }
        // Text/number controls
        else {
            $wp_customize->add_control( "theme_button_{$slug}", [
                'label'   => ucwords(str_replace('-', ' ', $slug)),
                'section' => 'theme_global_buttons',
                'type'    => 'text',
            ]);
        }
    }
});

/**
 * ---------------------------------------
 *  SAVE BUTTON VARIABLES TO main.css + theme.json
 * ---------------------------------------
 */
add_action( 'customize_save_after', function() {

    $button_vars = [
        'button-background',
        'button-text',
        'button-border-color',
        'button-border-width',
        'button-radius',
        'button-padding-top-buttom',
        'button-padding-right-left',
        'button-background-hover',
        'button-text-hover',
        'button-border-color-hover',
        'button-border-width-hover',
        'button-radius-hover',
        'button-padding-top-buttom-hover',
        'button-padding-right-left-hover',
    ];

    // 🟢 Update main.css
    foreach ( $button_vars as $slug ) {
        $value = get_theme_mod( "theme_button_{$slug}" );
        if ( $value ) {
            theme_update_button_value( $slug, $value );
        }
    }

    // 🟢 Update theme.json button section
    $theme_json_path = get_stylesheet_directory() . '/theme.json';
    if ( file_exists( $theme_json_path ) ) {
        $json = json_decode( file_get_contents( $theme_json_path ), true );

        if ( isset( $json['settings']['custom']['button'] ) ) {
            foreach ( $json['settings']['custom']['button'] as $key => &$val ) {
                $slug = "button-{$key}";
                $mod  = get_theme_mod( "theme_button_{$slug}" );
                if ( $mod ) {
                    $val = $mod;
                }
            }

            file_put_contents(
                $theme_json_path,
                json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
            );

            error_log('🎨 theme.json button section updated successfully.');
        }
    }

    error_log('✅ main.css button variables updated successfully.');
});
