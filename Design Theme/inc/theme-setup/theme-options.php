<?php


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register theme support
function theme_options() {

    // Add theme menu support
    add_theme_support( 'menus' );

    // Add Theme widget support
    add_theme_support( 'widgets' );

    // Add featured image support
    add_theme_support( 'post-thumbnails' );

    // Add title tag support
    add_theme_support( 'title-tag' );

    // Add HTML5 markup support
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

    // Add custom logo support
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
}
add_action( 'after_setup_theme', 'theme_options' );
