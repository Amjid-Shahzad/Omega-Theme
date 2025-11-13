<?php
/**
 * CodeMirror Integration for Header Customizer
 * Adds syntax highlighting to Header CSS & JS textareas.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueue CodeMirror assets for Header Customizer
 */
function theme_header_codemirror_assets() {
    // Load WordPress core CodeMirror assets
    wp_enqueue_script( 'code-editor' );
    wp_enqueue_style( 'code-editor' );

    wp_enqueue_script( 'wp-theme-plugin-editor' );
    wp_enqueue_style( 'wp-codemirror' );
}
add_action( 'customize_controls_enqueue_scripts', 'theme_header_codemirror_assets' );

/**
 * Initialize CodeMirror editors in Header Customizer
 * Adds live change detection so "Publish" activates after edits.
 */
function theme_header_codemirror_init_js() { ?>
    <script>
    jQuery(function($){
        if ( typeof wp === 'undefined' || !wp.hasOwnProperty('codeEditor') ) {
            console.warn('CodeMirror not available in Customizer.');
            return;
        }

        const editors = [
            { id: '#customize-control-header_template_css textarea', mode: 'css', setting: 'header_template_css' },
            { id: '#customize-control-header_template_js textarea',  mode: 'javascript', setting: 'header_template_js' }
        ];

        editors.forEach(({ id, mode, setting }) => {
            const $el = $(id);
            if ( !$el.length ) return;

            // Avoid re-initialization
            if ( $el.data('codemirror-init') ) return;
            $el.data('codemirror-init', true);

            const textarea = $el[0];

            try {
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

                // Sync CodeMirror → textarea + trigger Customizer change
                cm.on('change', function() {
                    const newVal = cm.getValue();
                    textarea.value = newVal;

                    // Update Customizer setting so "Publish" activates
                    if ( wp.customize && wp.customize(setting) ) {
                        wp.customize(setting).set(newVal);
                    }

                    // Trigger input/change events for WP detection
                    $el.trigger('input').trigger('change');
                });

            } catch (e) {
                console.error('Failed to initialize CodeMirror for', id, e);
            }
        });
    });
    </script>
<?php }
add_action( 'customize_controls_print_footer_scripts', 'theme_header_codemirror_init_js' );
