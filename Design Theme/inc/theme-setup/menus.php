<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register header and footer menus
function register_menus() {
    register_nav_menus( array(
        'header_menu' => __( 'Header Menu', 'primary-menu' ),
        'main_menu'   => __( 'Main Menu', 'primary-menu' ),
        'footer_menu' => __( 'Footer Menu', 'footer-menu' ),
        'top_menu'    => __( 'Top Menu', 'top-menu' ),
    ));
}
add_action( 'after_setup_theme', 'register_menus' );


// Create default menus only if no menus exist
function maybe_create_default_menus() {

    // Get all registered menus
    $existing_menus = wp_get_nav_menus();

    // If there are already menus, do nothing
    if ( ! empty( $existing_menus ) ) {
        return;
    }

    // Otherwise, create default menus
    $header_menu_id = wp_create_nav_menu( 'Header Menu' );
    $main_menu_id   = wp_create_nav_menu( 'Main Menu' );
    $footer_menu_id = wp_create_nav_menu( 'Footer Menu' );
    $top_menu_id    = wp_create_nav_menu( 'Top Menu' );
    

    // Example: Add sample menu items
    wp_update_nav_menu_item($main_menu_id, 0, array(
        'menu-item-title' => __('Home'),
        'menu-item-url' => home_url( '/' ),
        'menu-item-status' => 'publish'
    ));

    // Assign them to theme locations
    $locations = get_theme_mod( 'nav_menu_locations' );
    $locations['header_menu'] = $header_menu_id;
    $locations['main_menu']   = $main_menu_id;
    $locations['footer_menu'] = $footer_menu_id;
    $locations['top_menu']    = $top_menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );
}
add_action( 'after_switch_theme', 'maybe_create_default_menus' );
