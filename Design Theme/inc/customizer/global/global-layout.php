<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function theme_global_layout_section( $wp_customize ) {
    $wp_customize->add_section( 'theme_global_layout', array(
        'title'    => __( 'Layout', 'design-theme' ),
        'panel'    => 'theme_global_panel',
        'priority' => 30,
    ));

    $wp_customize->add_setting( 'theme_container_width', array(
        'default'   => 1200,
        'transport' => 'postMessage',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control( 'theme_container_width', array(
        'label'   => __( 'Container Width (px)', 'design-theme' ),
        'section' => 'theme_global_layout',
        'type'    => 'number',
    ));
}
add_action( 'customize_register', 'theme_global_layout_section', 10 );
