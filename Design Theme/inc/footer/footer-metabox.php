<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Disable Footer Section metabox from Appearance > Menus.
 * Also clean up any legacy hooks from older theme versions.
 */

if ( ! function_exists('theme_remove_footer_section_from_menus_screen') ) {
    function theme_remove_footer_section_from_menus_screen() {
        // Remove the old "Add Footer Section" meta box if it exists
        remove_meta_box( 'add-footer-section', 'nav-menus', 'side' );
    }
    add_action( 'admin_head-nav-menus.php', 'theme_remove_footer_section_from_menus_screen', 99 );
}

if ( ! function_exists('theme_detach_legacy_footer_section_metabox') ) {
    function theme_detach_legacy_footer_section_metabox() {
        // Remove any legacy add_action that registered old footer/metabox logic
        remove_action( 'admin_head-nav-menus.php', 'theme_add_mega_menu_metabox' );
        remove_action( 'admin_head-nav-menus.php', 'theme_add_footer_section_metabox' );
    }
    add_action( 'admin_init', 'theme_detach_legacy_footer_section_metabox', 99 );
}
