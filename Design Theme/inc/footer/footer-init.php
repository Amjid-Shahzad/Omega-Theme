<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Footer Section CPT
 */
function theme_register_footer_section_cpt() {
    $labels = [
        'name'          => __('Footer Sections', 'design-theme'),
        'singular_name' => __('Footer Section', 'design-theme'),
        'add_new_item'  => __('Add New Footer Section', 'design-theme'),
        'edit_item'     => __('Edit Footer Section', 'design-theme'),
        // 'menu_name'     => __('Footer Section', 'design-theme'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'exclude_from_search'=> true,
        'has_archive'        => false,
        'show_ui'            => true,
        'show_in_nav_menus'  => false,
        'show_in_menu'       => true, // under Appearance
        'menu_icon'          => get_stylesheet_directory_uri() . '/assets/icons/footer.png',
        'supports'           => ['title', 'editor', 'revisions', 'elementor'],
        'show_in_rest'       => true, // enables Gutenberg
    ];

    register_post_type('footer_section', $args);
}
add_action('init', 'theme_register_footer_section_cpt');




