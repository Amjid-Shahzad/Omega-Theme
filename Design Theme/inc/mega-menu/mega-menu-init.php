<?php
if (!defined('ABSPATH')) exit;

/**
 * Register "Mega Menu" Custom Post Type
 */
function theme_register_mega_menu_cpt() {
    $labels = [
        'name'               => __('Mega Menus', 'design-theme'),
        'singular_name'      => __('Mega Menu', 'design-theme'),
        'add_new'            => __('Add New Mega Menu', 'design-theme'),
        'add_new_item'       => __('Create New Mega Menu', 'design-theme'),
        'edit_item'          => __('Edit Mega Menu', 'design-theme'),
        'menu_name'          => __('Mega Menu', 'design-theme'),
    ];

    $args = [
    'labels'             => $labels,
    'public'             => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'menu_icon'          => get_stylesheet_directory_uri() . '/assets/icons/mega-menu.png',
    'supports'           => ['title', 'editor', 'revisions','elementor'],
    'show_in_rest'       => true,
    'show_in_nav_menus'  => true,
    'rewrite'            => false,
    'publicly_queryable' => true,   // 👈 allows front-end “View” without full public exposure
    ];

    register_post_type('mega_menu', $args);
}
add_action('init', 'theme_register_mega_menu_cpt');
