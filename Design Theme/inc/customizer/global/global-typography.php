<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ------------------------------------------------------------
 *  HELPER: Read font variable from main.css
 * ------------------------------------------------------------
 */
function theme_get_font_from_css( $variable ) {
    $file = get_stylesheet_directory() . '/assets/css/main.css';
    if ( ! file_exists( $file ) ) return null;

    $content = file_get_contents( $file );
    if ( preg_match( '/--' . preg_quote( $variable, '/' ) . ':\s*([^;]+);/', $content, $m ) ) {
        return trim( $m[1] );
    }
    return null;
}

/**
 * ------------------------------------------------------------
 *  HELPER: Update font variable in main.css
 * ------------------------------------------------------------
 */
function theme_update_font_in_css( $variable, $new_font ) {
    $file = get_stylesheet_directory() . '/assets/css/main.css';
    if ( ! file_exists( $file ) ) return false;

    $content = file_get_contents( $file );

    // Replace existing or add if missing
    if ( preg_match( '/--' . preg_quote( $variable, '/' ) . ':\s*([^;]+);/', $content ) ) {
        $content = preg_replace(
            '/(--' . preg_quote( $variable, '/' ) . ':\s*)([^;]+)(;)/',
            '${1}' . $new_font . '${3}',
            $content
        );
    } else {
        $content = preg_replace('/(:root\s*\{)/', "$1\n  --{$variable}: {$new_font};", $content);
    }

    file_put_contents( $file, $content );
    return true;
}

/**
 * ------------------------------------------------------------
 *  REGISTER TYPOGRAPHY OPTIONS IN CUSTOMIZER
 * ------------------------------------------------------------
 */
add_action( 'customize_register', function( $wp_customize ) {

    $fonts = [
        'base-font'       => __( 'Base Font', 'your-theme' ),
        'heading-font'    => __( 'Heading Font', 'your-theme' ),
        'subheading-font' => __( 'Subheading Font', 'your-theme' ),
        'button-font'     => __( 'Button Font', 'your-theme' ),
    ];

    // Add typography section
    $wp_customize->add_section( 'theme_global_fonts', [
        'title'       => __( 'Typography', 'your-theme' ),
        'priority'    => 20,
        'panel'       => 'global_settings_panel',
        'description' => __( 'Manage theme typography and import Google Fonts.', 'your-theme' ),
    ]);

    // Font family controls
    foreach ( $fonts as $slug => $label ) {
        $default = theme_get_font_from_css( $slug ) ?: "'Jost', sans-serif";

        $wp_customize->add_setting( "theme_font_{$slug}", [
            'default'   => $default,
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control( "theme_font_{$slug}", [
            'label'       => $label,
            'section'     => 'theme_global_fonts',
            'type'        => 'text',
            'description' => __( 'Enter font family, e.g. "Poppins, sans-serif".', 'your-theme' ),
        ]);
    }

    // Google Fonts Import URL
    $wp_customize->add_setting( 'theme_google_font_url', [
        'default'   => '',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control( 'theme_google_font_url', [
        'label'       => __( 'Google Font Import URL', 'your-theme' ),
        'section'     => 'theme_global_fonts',
        'type'        => 'text',
        'description' => __( 'Paste a Google Fonts URL (e.g. https://fonts.googleapis.com/css2?family=Roboto&display=swap).', 'your-theme' ),
    ]);
});

/**
 * ------------------------------------------------------------
 *  APPLY SAVED FONTS TO main.css + theme.json
 * ------------------------------------------------------------
 */
add_action( 'customize_save_after', function() {
    $fonts = [ 'base-font', 'heading-font', 'subheading-font', 'button-font' ];

    foreach ( $fonts as $slug ) {
        $value = get_theme_mod( "theme_font_{$slug}" );
        if ( $value ) {
            theme_update_font_in_css( $slug, $value );
        }
    }

    // Update theme.json
    $theme_json_path = get_stylesheet_directory() . '/theme.json';
    if ( file_exists( $theme_json_path ) ) {
        $json = json_decode( file_get_contents( $theme_json_path ), true );

        if ( isset( $json['settings']['typography']['fontFamilies'] ) ) {
            foreach ( $json['settings']['typography']['fontFamilies'] as &$font ) {
                $slug = $font['slug'];
                $val  = get_theme_mod( "theme_font_{$slug}" );
                if ( $val ) {
                    $font['fontFamily'] = $val;
                }
            }
            file_put_contents(
                $theme_json_path,
                json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
            );
        }
    }
});

/**
 * ------------------------------------------------------------
 *  ENQUEUE GOOGLE FONTS EARLY (ABOVE main.css)
 * ------------------------------------------------------------
 */
add_action( 'wp_enqueue_scripts', function() {
    $font_url = get_theme_mod( 'theme_google_font_url' );
    if ( ! empty( $font_url ) ) {
        // Priority 1 ensures it loads before main.css
        wp_enqueue_style( 'theme-custom-google-font', esc_url( $font_url ), [], null );
    }
}, 1);

















/**
 * ---------------------------------------------------------
 *  INJECT FULL :root VARIABLES INTO BLOCK EDITOR + CUSTOMIZER
 * ---------------------------------------------------------
 */
function theme_output_dynamic_root_vars() {

    // COLORS
    $colors = [
        'website-background'      => get_theme_mod('theme_color_website-background', '#c4c4c4'),
        'page-background'         => get_theme_mod('theme_color_page-background', '#5803c7'),
        'header-background'       => get_theme_mod('theme_color_header-background', '#ff0000'),
        'footer-background'       => get_theme_mod('theme_color_footer-background', '#ffffff'),
        'mega-menu-background'    => get_theme_mod('theme_color_mega-menu-background', '#ffffff'),
        'color-primary'           => get_theme_mod('theme_color_primary', '#0073e6'),
        'color-on-primary'        => get_theme_mod('theme_color_on-primary', '#005bb5'),
        'color-secondary'         => get_theme_mod('theme_color_secondary', '#ff6600'),
        'color-on-secondary'      => get_theme_mod('theme_color_on-secondary', '#ff6600'),
        'color-accent'            => get_theme_mod('theme_color_accent', '#00cc66'),
        'color-on-accent'         => get_theme_mod('theme_color_on-accent', '#00cc66'),
        'color-success'           => get_theme_mod('theme_color_success', '#28a745'),
        'color-error'             => get_theme_mod('theme_color_error', '#dc3545'),
        'text-primary'            => get_theme_mod('theme_color_text-primary', '#212121'),
        'text-secondary'          => get_theme_mod('theme_color_text-secondary', '#757575'),
        'text-muted'              => get_theme_mod('theme_color_text-muted', '#999999'),
        'link'                    => get_theme_mod('theme_color_link', '#007bff'),
        'border'                  => get_theme_mod('theme_color_border', '#dadada'),
        'divider'                 => get_theme_mod('theme_color_divider', '#e0e0e0'),
        'shadow'                  => get_theme_mod('theme_color_shadow', 'rgba(0, 0, 0, 0.1)'),
        'hover'                   => get_theme_mod('theme_color_hover', 'rgba(0, 0, 0, 0.05)'),
        'focus'                   => get_theme_mod('theme_color_focus', 'rgba(0, 0, 0, 0.1)')
    ];

    // TYPOGRAPHY
    $fonts = [
        'base-font'       => get_theme_mod('theme_font_base-font', '"Poppins", sans-serif'),
        'heading-font'    => get_theme_mod('theme_font_heading-font', '"Poppins", sans-serif'),
        'subheading-font' => get_theme_mod('theme_font_subheading-font', '"Poppins", sans-serif'),
        'button-font'     => get_theme_mod('theme_font_button-font', '"Poppins", sans-serif')
    ];

    // BUTTONS
    $buttons = [
        'button-background'              => get_theme_mod('theme_button_background', '#dd3333'),
        'button-text'                    => get_theme_mod('theme_button_text', '#ffffff'),
        'button-border-color'            => get_theme_mod('theme_button_border-color', '#dd3333'),
        'button-border-width'            => get_theme_mod('theme_button_border-width', '2px'),
        'button-radius'                  => get_theme_mod('theme_button_radius', '5px'),
        'button-padding-top-buttom'      => get_theme_mod('theme_button_padding-top-buttom', '10px'),
        'button-padding-right-left'      => get_theme_mod('theme_button_padding-right-left', '20px'),
        // Hover
        'button-background-hover'        => get_theme_mod('theme_button_background-hover', '#ffffff'),
        'button-text-hover'              => get_theme_mod('theme_button_text-hover', '#dd3333'),
        'button-border-color-hover'      => get_theme_mod('theme_button_border-color-hover', '#dd3333'),
        'button-border-width-hover'      => get_theme_mod('theme_button_border-width-hover', '2px'),
        'button-radius-hover'            => get_theme_mod('theme_button_radius-hover', '5px'),
        'button-padding-top-buttom-hover'=> get_theme_mod('theme_button_padding-top-buttom-hover', '10px'),
        'button-padding-right-left-hover'=> get_theme_mod('theme_button_padding-right-left-hover', '20px')
    ];

    echo '<style id="theme-editor-root-vars">:root {';
    
    // Output all variables
    foreach ( array_merge($colors, $fonts, $buttons) as $var => $val ) {
        echo "--{$var}: {$val};";
    }

    echo '}</style>';
}

// ✅ Load in Block Editor
add_action('admin_head', function() {
    $screen = get_current_screen();
    if ( $screen && $screen->is_block_editor() ) {
        theme_output_dynamic_root_vars();
    }
});

// ✅ Load in Customizer Preview (live)
add_action('wp_head', 'theme_output_dynamic_root_vars');
