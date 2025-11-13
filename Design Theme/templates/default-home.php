<?php
// default-home.php

function default_home_page_content() {
    global $wpdb;

    // Try to get "Home" page ID directly (exact title match)
    $home_page_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts}
         WHERE post_title = %s
           AND post_type = 'page'
           AND post_status IN ('publish','private','draft','pending')
         LIMIT 1",
        'Home'
    ) );

    if ( ! $home_page_id ) {
        // Home page doesn't exist, create it
        $home_page_id = wp_insert_post( array(
            'post_title'    => 'Home',
            'post_content'  => '<h1>Welcome to Our Website</h1><p>This is the default content for your homepage. You can edit this content anytime.</p>',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
        ) );

        if ( $home_page_id ) {
            // Set as front page
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $home_page_id );
        }
    } else {
        // Get the full page object
        $home_page = get_post( $home_page_id );

        // If content is empty, add default content
        if ( empty( trim( $home_page->post_content ) ) ) {
            wp_update_post( array(
                'ID'           => $home_page_id,
                'post_content' => '<h1>Welcome to Our Website</h1><p>This is the default content for your homepage. You can edit this content anytime.</p>',
            ) );
        }

        // Ensure it's set as front page
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home_page_id );
    }
}

// Hook the function
add_action( 'after_setup_theme', 'default_home_page_content' );
