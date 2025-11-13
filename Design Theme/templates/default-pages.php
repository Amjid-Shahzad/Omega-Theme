<?php
/**
 * Safely create default pages.
 * Only creates missing ones.
 * Creates WooCommerce pages only if WooCommerce is truly active.
 */
function create_default_pages_safe() {
    // 1️⃣ Base pages
    $default_pages = array(
        'Home'     => 'Welcome to our website! This is the home page.',
        'About'    => 'Learn more about our company and mission.',
        'Services' => 'Explore the services we offer.',
        'Products' => 'Product pages.',
        'Blog'     => 'Read our latest blog posts and updates.',
        'Contact'  => 'Get in touch with us through this page.'
    );

    $page_ids = array();

    // 2️⃣ Create only missing pages
    foreach ( $default_pages as $title => $content ) {
        $existing_page = get_page_by_title( $title );
        if ( $existing_page ) {
            $page_ids[ $title ] = $existing_page->ID;
        } else {
            $page_id = wp_insert_post( array(
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => get_current_user_id(),
                'post_name'    => sanitize_title( $title ),
            ) );
            $page_ids[ $title ] = $page_id;
        }
    }

    // 3️⃣ Respect existing front page settings
    $show_on_front = get_option( 'show_on_front' );
    $front_page_id = get_option( 'page_on_front' );

    if ( empty( $front_page_id ) || $show_on_front !== 'page' ) {
        if ( isset( $page_ids['Home'] ) ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $page_ids['Home'] );
        }
    }

    // 4️⃣ Respect existing blog posts page
    $posts_page_id = get_option( 'page_for_posts' );
    if ( empty( $posts_page_id ) && isset( $page_ids['Blog'] ) ) {
        update_option( 'page_for_posts', $page_ids['Blog'] );
    }

    // 5️⃣ Optional: Log setup status
    error_log( '✅ Default pages check completed. Existing setup preserved.' );
}
add_action( 'after_switch_theme', 'create_default_pages_safe' );


/**
 * Create WooCommerce pages only when WooCommerce is active and initialized.
 */
function create_woocommerce_pages_safe() {

    // Make sure WooCommerce is really active
    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_page_id' ) ) {
        return; // Exit if WooCommerce is not active
    }

    $woocommerce_pages = array(
        'Shop'     => 'Browse our amazing products.',
        'Cart'     => 'View your shopping cart.',
        'Checkout' => 'Complete your purchase.',
        'Account'  => 'Manage your account details and orders.',
    );

    $page_ids = array();

    // Create only missing WooCommerce pages
    foreach ( $woocommerce_pages as $title => $content ) {
        $existing_page = get_page_by_title( $title );
        if ( $existing_page ) {
            $page_ids[ $title ] = $existing_page->ID;
        } else {
            $page_id = wp_insert_post( array(
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => get_current_user_id(),
                'post_name'    => sanitize_title( $title ),
            ) );
            $page_ids[ $title ] = $page_id;
        }
    }

    // Assign WooCommerce pages if not already set
    $wc_options = array(
        'woocommerce_shop_page_id'      => 'Shop',
        'woocommerce_cart_page_id'      => 'Cart',
        'woocommerce_checkout_page_id'  => 'Checkout',
        'woocommerce_myaccount_page_id' => 'Account',
    );

    foreach ( $wc_options as $option => $title ) {
        if ( isset( $page_ids[ $title ] ) && ! get_option( $option ) ) {
            update_option( $option, $page_ids[ $title ] );
        }
    }

    error_log( '🛒 WooCommerce pages created safely.' );
}
add_action( 'woocommerce_init', 'create_woocommerce_pages_safe' );
