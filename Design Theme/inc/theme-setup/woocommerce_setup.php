<?php


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Create/link WooCommerce core pages — run only when WooCommerce plugin is activated.
 *
 * Overview:
 * - When a plugin is activated, WP fires 'activated_plugin'.
 * - We set a transient on that event if the activated plugin is WooCommerce.
 * - On the next 'init' we check the transient and only run the setup if WooCommerce is available.
 *   This avoids race conditions and ensures pages are created only when WooCommerce is actually activated,
 *   not when the theme is installed/activated.
 */

/**
 * Create the required WooCommerce pages (cart, checkout, my-account, shop)
 * Only runs when WooCommerce class is available.
 */


function setup_woocommerce_pages() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return; // Nothing to do if WooCommerce isn't loaded
    }

    $pages = array(
        'cart' => array(
            'title'   => 'Cart',
            'content' => '[woocommerce_cart]',
            'option'  => 'woocommerce_cart_page_id',
        ),
        'checkout' => array(
            'title'   => 'Checkout',
            'content' => '[woocommerce_checkout]',
            'option'  => 'woocommerce_checkout_page_id',
        ),
        'my-account' => array(
            'title'   => 'My Account',
            'content' => '[woocommerce_my_account]',
            'option'  => 'woocommerce_myaccount_page_id',
        ),
        'shop' => array(
            'title'   => 'Shop',
            'content' => '', // Shop uses archive-product.php, no shortcode needed
            'option'  => 'woocommerce_shop_page_id',
        ),
    );

    foreach ( $pages as $slug => $page ) {
        $option = $page['option'];

        // Try the option first (if a page id is already saved)
        $page_id   = get_option( $option );
        $page_post = $page_id ? get_post( $page_id ) : null;

        // If no post found by option, try to find by path/slug
        if ( ! $page_post ) {
            $page_post = get_page_by_path( $slug );
        }

        if ( $page_post ) {
            // Page exists — ensure shortcode/content is present when needed
            if ( ! empty( $page['content'] ) && strpos( $page_post->post_content, $page['content'] ) === false ) {
                // Use wp_update_post with an array (safe)
                wp_update_post( array(
                    'ID'           => $page_post->ID,
                    'post_content' => $page['content'],
                ) );
            }
            update_option( $option, $page_post->ID );
        } else {
            // Page missing — create it
            $new_id = wp_insert_post( array(
                'post_title'   => wp_strip_all_tags( $page['title'] ),
                'post_name'    => $slug,
                'post_content' => $page['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ) );

            if ( $new_id && ! is_wp_error( $new_id ) ) {
                update_option( $option, $new_id );
            }
        }
    }
}

/**
 * When a plugin is activated, schedule a one-time transient if it's WooCommerce.
 * We don't run setup directly here, because WooCommerce may not be fully loaded yet.
 */
function schedule_wc_setup_on_plugin_activation( $plugin, $network_wide ) {
    // plugin path is like "woocommerce/woocommerce.php"
    if ( ! empty( $plugin ) && $plugin === 'woocommerce/woocommerce.php' ) {
        // transient expires quickly — we only need it briefly
        set_transient( 'design_theme_wc_setup_on_activation', 1, MINUTE_IN_SECONDS * 5 );
    }
}
add_action( 'activated_plugin', 'schedule_wc_setup_on_plugin_activation', 10, 2 );

/**
 * On init, if the transient exists and WooCommerce is loaded, run the setup and delete transient.
 */
function design_theme_maybe_run_wc_setup_on_init() {
    if ( ! get_transient( 'design_theme_wc_setup_on_activation' ) ) {
        return;
    }

    if ( class_exists( 'WooCommerce' ) ) {
        // Run the page creation
        setup_woocommerce_pages();

        // Remove transient so it runs only once
        delete_transient( 'design_theme_wc_setup_on_activation' );
    }
}
add_action( 'init', 'design_theme_maybe_run_wc_setup_on_init', 20 );
