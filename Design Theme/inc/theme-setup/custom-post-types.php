<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register Custom Post Type: Builder Pages
function themebuilder_register_builder_pages() {
    $labels = array(
        'name'               => 'Builder Pages',
        'singular_name'      => 'Builder Page',
        'menu_name'          => 'Builder Pages',
        'name_admin_bar'     => 'Builder Page',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Builder Page',
        'new_item'           => 'New Builder Page',
        'edit_item'          => 'Edit Builder Page',
        'view_item'          => 'View Builder Page',
        'all_items'          => 'All Builder Pages',
        'search_items'       => 'Search Builder Pages',
        'parent_item_colon'  => 'Parent Builder Pages:',
        'not_found'          => 'No Builder Pages found.',
        'not_found_in_trash' => 'No Builder Pages found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'builder-page' ),
        'capability_type'    => 'page',
        'has_archive'        => true,
        'hierarchical'       => true,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-layout', // WordPress icon
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
    );

    register_post_type( 'builder_page', $args );
}
add_action( 'init', 'themebuilder_register_builder_pages' );


// Register a Custom Post Type for Builder Pages
function mini_builder_register_post_type() {
    $labels = array(
        'name' => 'Builder Pages',
        'singular_name' => 'Builder Page',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Builder Page',
        'edit_item' => 'Edit Builder Page',
        'new_item' => 'New Builder Page',
        'view_item' => 'View Builder Page',
        'search_items' => 'Search Builder Pages',
        'not_found' => 'No Builder Pages found',
        'not_found_in_trash' => 'No Builder Pages found in Trash',
    );

    $args = array(
        'label' => 'Builder Pages',
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'supports' => array('title', 'editor'), // Editor = where we will add builder UI later
        'menu_icon' => 'dashicons-layout',
    );

    register_post_type('builder_page', $args);
}
add_action('init', 'mini_builder_register_post_type');

