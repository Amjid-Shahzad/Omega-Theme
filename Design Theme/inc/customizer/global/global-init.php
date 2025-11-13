<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load Global Customizer Modules
 */
function theme_load_global_customizer_modules( $wp_customize ) {

    // Create Main Global Panel
    $wp_customize->add_panel( 'global_settings_panel', array(
        'title'       => __( 'Global Settings', 'your-theme-textdomain' ),
        'priority'    => 5,
        'description' => __( 'Manage global design settings here.', 'your-theme-textdomain' ),
    ) );

    // Load all sub-sections (and assign to the Global panel)
    $path = get_template_directory() . '/inc/customizer/global/';

    require_once $path . 'global-color.php';
    require_once $path . 'global-typography.php';
    require_once $path . 'global-layout.php';
    require_once $path . 'global-buttons.php';
    require_once $path . 'global-themejson-sync.php';

    // Example: Move sections inside the Global panel
    if ( $wp_customize->get_section( 'global_color_section' ) ) {
        $wp_customize->get_section( 'global_color_section' )->panel = 'global_settings_panel';
    }
    if ( $wp_customize->get_section( 'global_typography_section' ) ) {
        $wp_customize->get_section( 'global_typography_section' )->panel = 'global_settings_panel';
    }
    if ( $wp_customize->get_section( 'global_layout_section' ) ) {
        $wp_customize->get_section( 'global_layout_section' )->panel = 'global_settings_panel';
    }
    if ( $wp_customize->get_section( 'global_buttons_section' ) ) {
        $wp_customize->get_section( 'global_buttons_section' )->panel = 'global_settings_panel';
    }
}
add_action( 'customize_register', 'theme_load_global_customizer_modules', 5 );
