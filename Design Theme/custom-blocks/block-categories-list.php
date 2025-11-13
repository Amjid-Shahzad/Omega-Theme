<?php
/**
 * Register custom block categories for the block editor.
 *
 * @package AmjadTheme
 */

if ( ! function_exists( 'amjad_register_custom_block_categories' ) ) {
    /**
     * Adds custom block categories at the top (before core categories).
     *
     * @param array   $categories Default categories.
     * @param WP_Post $post       Current post object.
     *
     * @return array Modified categories.
     */
    function amjad_register_custom_block_categories( $categories, $post ) {

        $custom_categories = array(
            array(
                'slug'  => 'mega-menu',
                'title' => __( 'Mega Menu', 'amjad-theme' ),
                'icon'  => 'menu',
            ),
            array(
                'slug'  => 'footer-blocks',
                'title' => __( 'Footer Blocks', 'amjad-theme' ),
                'icon'  => 'admin-appearance',
            ),
            array(
                'slug'  => 'main-blocks',
                'title' => __( 'Main Blocks', 'amjad-theme' ),
                'icon'  => 'layout',
            ),
            array(
                'slug'  => 'theme-blocks',
                'title' => __( 'Theme Blocks', 'amjad-theme' ),
                'icon'  => 'art',
            ),
        );

        // Put custom categories before default WordPress ones
        return array_merge( $custom_categories, $categories );
    }

    add_filter( 'block_categories_all', 'amjad_register_custom_block_categories', 10, 2 );
}
