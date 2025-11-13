<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Output Footer (Static or Dynamic)
 */
function theme_output_footer() {

    $footer_choice = get_theme_mod( 'footer_template', 'footer-classic' );

    echo "<!-- DEBUG: Footer choice = {$footer_choice} -->";

    // === Dynamic Footer ===
    if ( strpos( $footer_choice, 'dynamic-' ) === 0 ) {
        $slug = str_replace( 'dynamic-', '', $footer_choice );

        $query = new WP_Query([
            'post_type'      => 'footer_section',
            'name'           => $slug,
            'post_status'    => 'publish',
            'posts_per_page' => 1,
        ]);

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $raw = get_the_content();
                $rendered = '';

                // Gutenberg render
                if ( has_blocks( $raw ) ) {
                    $blocks = parse_blocks( $raw );
                    foreach ( $blocks as $block ) {
                        $rendered .= render_block( $block );
                    }
                } else {
                    $rendered = apply_filters( 'the_content', $raw );
                }

                // Elementor fallback
                if ( empty( trim( $rendered ) ) && class_exists( '\Elementor\Plugin' ) ) {
                    $rendered = \Elementor\Plugin::$instance->frontend->get_builder_content( get_the_ID(), true );
                }

                echo '<footer id="site-footer" class="site-footer dynamic-footer footer-' . esc_attr( $slug ) . '">';
                echo $rendered ?: '<!-- DEBUG: Empty footer_section content for ' . esc_html( $slug ) . ' -->';
                echo '</footer>';
            }
            wp_reset_postdata();
        } else {
            echo "<!-- DEBUG: No footer_section found for slug '{$slug}' -->";
        }

    } else {
        // === Static Footer ===
        $template_slug = str_replace( 'footer-', '', $footer_choice );
        $custom_html   = get_theme_mod( 'footer_html_' . $footer_choice, '' );

        echo '<footer id="site-footer" class="site-footer static-footer footer-' . esc_attr( $template_slug ) . '">';

        if ( ! empty( $custom_html ) ) {
            echo do_shortcode( wp_kses_post( $custom_html ) );
        } else {
            $path = get_template_directory() . '/inc/footer/footer-templates/footer-' . $template_slug . '.php';
            if ( file_exists( $path ) ) {
                include $path;
            } else {
                echo "<!-- DEBUG: Static footer template not found: {$template_slug} -->";
            }
        }

        echo '</footer>';
    }
}
