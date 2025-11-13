<?php 


/**
 * Properly enable Elementor editor for Footer Sections
 * and redirect editing to a custom preview template.
 */
function theme_footer_section_support_elementor( $supports ) {
    $supports[] = 'footer_section';
    return $supports;
}
add_filter( 'elementor_cpt_support', 'theme_footer_section_support_elementor' );

/**
 * Force Elementor preview to use a custom footer template.
 */
function theme_footer_section_elementor_template( $template ) {
    global $post;

    if ( $post && $post->post_type === 'footer_section' ) {
        $custom_template = get_template_directory() . '/inc/footer/footer-elementor-preview.php';
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }

    return $template;
}
add_filter( 'single_template', 'theme_footer_section_elementor_template' );

/**
 * Add 'Edit with Elementor' link to list table (Admin → Footer Sections)
 */
function theme_footer_section_elementor_link( $actions, $post ) {
    if ( $post->post_type === 'footer_section' && class_exists( '\Elementor\Plugin' ) ) {
        $url = admin_url( 'post.php?post=' . $post->ID . '&action=elementor' );
        $actions['elementor'] = '<a href="' . esc_url( $url ) . '">' . __( 'Edit with Elementor', 'design-theme' ) . '</a>';
    }
    return $actions;
}
add_filter( 'post_row_actions', 'theme_footer_section_elementor_link', 10, 2 );
