<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Footer Customizer (Full Version)
 * - Each static footer has its own HTML, CSS, and JS.
 * - HTML stored in DB, CSS/JS read from and saved to files.
 * - Dynamic footers hide all inputs.
 */
function theme_customize_footer( $wp_customize ) {

    // ---- SECTION ----
    $wp_customize->add_section('footer_options', [
        'title'       => __('Footer Options', 'design-theme'),
        'priority'    => 40,
        'description' => __('Select footer template and customize HTML, CSS, and JS per template.', 'design-theme'),
    ]);

    // ---- TEMPLATE SELECT ----
    $wp_customize->add_setting('footer_template', [
        'default'           => 'footer-classic',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    // Static footer templates
    $choices = [
        'footer-classic'  => 'Classic',
        'footer-cta'      => 'CTA',
        'footer-minimal'  => 'Minimal',
        'footer-centered' => 'Centered',
    ];

    // Add dynamic Gutenberg footers
    $footer_sections = get_posts([
        'post_type'      => 'footer_section',
        'post_status'    => 'publish',
        'numberposts'    => -1,
    ]);

    if ( ! empty( $footer_sections ) ) {
        foreach ( $footer_sections as $section ) {
            $choices[ 'dynamic-' . $section->post_name ] = 'Dynamic: ' . $section->post_title;
        }
    }

    $wp_customize->add_control('footer_template', [
        'label'   => __('Select Footer Template', 'design-theme'),
        'section' => 'footer_options',
        'type'    => 'select',
        'choices' => $choices,
    ]);

    // Current active template
    $active_template = get_theme_mod('footer_template', 'footer-classic');
    $template_slug = str_replace('footer-', '', $active_template);

    // ---- HTML (DB stored per template) ----
    $current_html = get_theme_mod( 'footer_html_' . $active_template, '' );

    $wp_customize->add_setting('footer_custom_html', [
        'default'           => $current_html,
        'sanitize_callback' => 'wp_kses_post',
    ]);

    $wp_customize->add_control('footer_custom_html', [
        'label'           => __('Footer HTML (per template)', 'design-theme'),
        'section'         => 'footer_options',
        'type'            => 'textarea',
        'description'     => __('HTML saved separately for each footer template.', 'design-theme'),
        'active_callback' => 'theme_footer_show_fields',
    ]);

    // ---- CSS (file-based) ----
    $css_file = get_template_directory() . "/assets/css/footer-templates/footer-{$template_slug}.css";
    $current_css = file_exists( $css_file ) ? file_get_contents( $css_file ) : '';

    $wp_customize->add_setting('footer_custom_css', [
        'default'           => $current_css,
        'sanitize_callback' => 'wp_strip_all_tags',
    ]);

    $wp_customize->add_control('footer_custom_css', [
        'label'           => __('Footer CSS (per template)', 'design-theme'),
        'section'         => 'footer_options',
        'type'            => 'textarea',
        'description'     => __('CSS loaded from and saved to matching file.', 'design-theme'),
        'active_callback' => 'theme_footer_show_fields',
    ]);

    // ---- JS (file-based) ----
    $js_file = get_template_directory() . "/assets/js/footer-templates/footer-{$template_slug}.js";
    $current_js = file_exists( $js_file ) ? file_get_contents( $js_file ) : '';

    $wp_customize->add_setting('footer_custom_js', [
        'default'           => $current_js,
        'sanitize_callback' => 'wp_strip_all_tags',
    ]);

    $wp_customize->add_control('footer_custom_js', [
        'label'           => __('Footer JS (per template)', 'design-theme'),
        'section'         => 'footer_options',
        'type'            => 'textarea',
        'description'     => __('JS loaded from and saved to matching file.', 'design-theme'),
        'active_callback' => 'theme_footer_show_fields',
    ]);
}
add_action('customize_register', 'theme_customize_footer');

/**
 * Hide inputs when Dynamic Footer is selected
 */
function theme_footer_show_fields() {
    $selected = get_theme_mod('footer_template', 'footer-classic');
    return ( strpos( $selected, 'dynamic-' ) !== 0 );
}

/**
 * AJAX: Load data (HTML/CSS/JS) for selected template dynamically
 */
add_action('wp_ajax_theme_get_footer_fields', function() {
    $template = sanitize_text_field( $_POST['template'] ?? 'footer-classic' );
    $template_slug = str_replace('footer-', '', $template);

    $html = get_theme_mod( 'footer_html_' . $template, '' );

    $css_file = get_template_directory() . "/assets/css/footer-templates/footer-{$template_slug}.css";
    $js_file  = get_template_directory() . "/assets/js/footer-templates/footer-{$template_slug}.js";

    $css = file_exists( $css_file ) ? file_get_contents( $css_file ) : '';
    $js  = file_exists( $js_file )  ? file_get_contents( $js_file )  : '';

    wp_send_json_success([
        'html' => $html,
        'css'  => $css,
        'js'   => $js,
    ]);
});

/**
 * Save HTML to DB and CSS/JS to file
 */
function theme_save_footer_fields_to_sources() {
    if ( isset( $_POST['customized'] ) ) {
        $customized = json_decode( stripslashes( $_POST['customized'] ), true );
        $template = get_theme_mod( 'footer_template', 'footer-classic' );
        $slug = str_replace( 'footer-', '', $template );

        // HTML → DB
        if ( isset( $customized['footer_custom_html'] ) ) {
            $html_key = 'footer_html_' . $template;
            set_theme_mod( $html_key, wp_kses_post( wp_unslash( $customized['footer_custom_html'] ) ) );
        }

        // CSS → File
        $css_dir = get_template_directory() . '/assets/css/footer-templates/';
        wp_mkdir_p( $css_dir );
        if ( isset( $customized['footer_custom_css'] ) ) {
            $css_content = wp_unslash( $customized['footer_custom_css'] );
            file_put_contents( "{$css_dir}footer-{$slug}.css", $css_content );
        }

        // JS → File
        $js_dir = get_template_directory() . '/assets/js/footer-templates/';
        wp_mkdir_p( $js_dir );
        if ( isset( $customized['footer_custom_js'] ) ) {
            $js_content = wp_unslash( $customized['footer_custom_js'] );
            file_put_contents( "{$js_dir}footer-{$slug}.js", $js_content );
        }
    }
}
add_action( 'customize_save_after', 'theme_save_footer_fields_to_sources' );

/**
 * Live update fields when switching footer templates
 */
function theme_footer_customizer_live_switch_js() {
    ?>
    <script>
    (function($){
        wp.customize('footer_template', function(value){
            value.bind(function(to){
                const controls = [
                    '#customize-control-footer_custom_html',
                    '#customize-control-footer_custom_css',
                    '#customize-control-footer_custom_js'
                ];

                if(to.startsWith('dynamic-')){
                    controls.forEach(sel => $(sel).hide());
                    return;
                } else {
                    controls.forEach(sel => $(sel).show());
                }

                // Fetch new template data
                wp.ajax.post('theme_get_footer_fields', { template: to }).done(function(res){
                    wp.customize('footer_custom_html').set(res.html);
                    wp.customize('footer_custom_css').set(res.css);
                    wp.customize('footer_custom_js').set(res.js);
                });
            });
        });
    })(jQuery);
    </script>
    <?php
}
add_action('customize_controls_print_footer_scripts', 'theme_footer_customizer_live_switch_js');







/**
 * --------------------------------------------------------------
 * Enable CodeMirror for Footer Customizer Editors
 * --------------------------------------------------------------
 */
function theme_footer_customizer_codemirror_assets() {
    // Enqueue WordPress core CodeMirror assets
    $settings = wp_enqueue_code_editor( [ 'type' => 'text/html' ] );
    if ( false === $settings ) {
        return; // CodeMirror unavailable (older WP or missing assets)
    }

    wp_enqueue_script( 'wp-theme-plugin-editor' );
    wp_enqueue_style( 'wp-codemirror' );

    ?>
    <script>
    (function($){
        $(function(){
            if ( typeof wp === 'undefined' || !wp.codeEditor ) {
                console.warn('❌ CodeMirror not available for Footer Customizer.');
                return;
            }

            const editors = [
                { id: '#customize-control-footer_custom_html textarea', mode: 'htmlmixed', setting: 'footer_custom_html' },
                { id: '#customize-control-footer_custom_css textarea',  mode: 'css',        setting: 'footer_custom_css'  },
                { id: '#customize-control-footer_custom_js textarea',   mode: 'javascript', setting: 'footer_custom_js'   }
            ];

            editors.forEach(({ id, mode, setting }) => {
                const $el = $(id);
                if ( !$el.length ) return;

                // Avoid duplicate initialization
                if ( $el.data('codemirror-init') ) return;
                $el.data('codemirror-init', true);

                const textarea = $el[0];

                try {
                    // Initialize CodeMirror via WP API
                    const cm = wp.codeEditor.initialize(textarea, {
                        codemirror: {
                            mode: mode,
                            lineNumbers: true,
                            styleActiveLine: true,
                            matchBrackets: true,
                            autoCloseBrackets: true,
                            indentUnit: 2,
                            tabSize: 2,
                            lineWrapping: true,
                            theme: 'default'
                        }
                    }).codemirror;

                    // Sync CodeMirror → textarea + Customizer live setting
                    cm.on('change', function(){
                        const newVal = cm.getValue();
                        textarea.value = newVal;

                        // Update Customizer setting so "Publish" button activates
                        if ( wp.customize && wp.customize(setting) ) {
                            wp.customize(setting).set(newVal);
                        }

                        // Fire native input/change events for WP tracking
                        $el.trigger('input').trigger('change');
                    });

                    // Keep instance for debugging
                    $el.data('cm-instance', cm);

                } catch (e) {
                    console.error('⚠️ Failed to initialize CodeMirror for', id, e);
                }
            });

            console.log('%c✅ Footer CodeMirror initialized successfully', 'color:#3c763d');
        });
    })(jQuery);
    </script>
    <?php
}
add_action( 'customize_controls_print_footer_scripts', 'theme_footer_customizer_codemirror_assets', 20 );
