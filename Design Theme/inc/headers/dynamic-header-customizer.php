<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 1) Register Customizer controls for Header templates
 */
function theme_customize_header($wp_customize) {

    // Section
    $wp_customize->add_section('header_options', array(
        'title'       => __('Header Options', 'your-theme'),
        'priority'    => 30,
        'description' => __('Select the header template and edit CSS/JS dynamically', 'your-theme'),
    ));

    // Template selector
    $wp_customize->add_setting('header_template', array(
        'default'           => 'header-classic',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('header_template', array(
        'label'   => __('Header Template', 'your-theme'),
        'section' => 'header_options',
        'type'    => 'select',
        'choices' => array(
            'header-classic'      => 'Classic',
            'header-logo-right'   => 'Logo Right',
            'header-centered'     => 'Centered',
            'header-cta'          => 'With CTA',
            'header-transparent'  => 'Transparent',
            'header-vertical'     => 'Vertical',
            'header-search'       => 'With Search',
            'header-mega-menu'    => 'Mega Menu',
            'header-sticky'       => 'Sticky',
            'header-minimal'      => 'Minimal',
        ),
    ));

    // Prefill initial CSS/JS from current template files
    $header_template_name = get_theme_mod('header_template', 'header-classic');
    $base_dir = get_template_directory() . '/inc/headers/header-templates/';
    $css_file = $base_dir . $header_template_name . '.css';
    $js_file  = $base_dir . $header_template_name . '.js';

    $initial_css = (file_exists($css_file) && is_readable($css_file)) ? file_get_contents($css_file) : '';
    $initial_js  = (file_exists($js_file)  && is_readable($js_file))  ? file_get_contents($js_file) : '';

    // CSS editor
    $wp_customize->add_setting('header_template_css', array(
        'default'           => $initial_css,
       'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('header_template_css', array(
        'label'       => __('Header Template CSS', 'your-theme'),
        'section'     => 'header_options',
        'type'        => 'textarea',
        'description' => __('Edit CSS for the active header template. Saved into a .css file.', 'your-theme'),
    ));

    // JS editor
    $wp_customize->add_setting('header_template_js', array(
        'default'           => $initial_js,
        'sanitize_callback' => 'wp_kses_data',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('header_template_js', array(
        'label'       => __('Header Template JS', 'your-theme'),
        'section'     => 'header_options',
        'type'        => 'textarea',
        'description' => __('Edit JS for the active header template. Saved into a .js file.', 'your-theme'),
    ));

    // Manual reload button (in controls frame)
    if ( class_exists('WP_Customize_Control') ) {
        class Header_Reload_Button_Control extends WP_Customize_Control {
            public $type = 'button';
            public function render_content() { ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <button type="button" id="header-reload-button" class="button"><?php echo esc_html( $this->description ); ?></button>
                </label>
            <?php }
        }

        $wp_customize->add_setting('header_reload_button', array(
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(new Header_Reload_Button_Control(
            $wp_customize,
            'header_reload_button',
            array(
                'label'       => __('Click Here For CSS & JS Live Edit', 'your-theme'),
                'section'     => 'header_options',
                'description' => __('Reload Now', 'your-theme'),
            )
        ));
    }
}
add_action('customize_register', 'theme_customize_header');

/**
 * 2) Save CSS/JS into files when Customizer is saved
 */
function save_dynamic_header_files() {
    $header_template = get_theme_mod('header_template', 'header-classic');
    $base_dir = get_template_directory() . '/inc/headers/header-templates/';
    $css_file = $base_dir . $header_template . '.css';
    $js_file  = $base_dir . $header_template . '.js';

    if ( ! file_exists( $base_dir ) ) {
        wp_mkdir_p( $base_dir );
    }

    $header_css = get_theme_mod('header_template_css', '');
    @file_put_contents($css_file, $header_css);

    $header_js = html_entity_decode( get_theme_mod('header_template_js', ''), ENT_QUOTES, 'UTF-8' );
@file_put_contents($js_file, $header_js);

}
add_action('customize_save_after', 'save_dynamic_header_files');

/**
 * 3) AJAX: Fetch latest file contents from disk
 */
function fetch_header_template_files() {
    if ( isset($_POST['template']) && ! empty($_POST['template']) ) {
        $template = sanitize_file_name($_POST['template']);
        $base_dir = get_template_directory() . '/inc/headers/header-templates/';
        $css_file = $base_dir . $template . '.css';
        $js_file  = $base_dir . $template . '.js';

        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($css_file, true);
            @opcache_invalidate($js_file, true);
        }

        $css_content = (file_exists($css_file) && is_readable($css_file)) ? file_get_contents($css_file) : '';
$js_content  = (file_exists($js_file)  && is_readable($js_file))  ? file_get_contents($js_file)  : '';

// decode before sending
$css_content = html_entity_decode($css_content, ENT_QUOTES, 'UTF-8');
$js_content  = html_entity_decode($js_content, ENT_QUOTES, 'UTF-8');

wp_send_json_success(array(
    'css' => $css_content,
    'js'  => $js_content,
));

    } else {
        wp_send_json_error('Template name missing or invalid');
    }
    wp_die();
}
add_action('wp_ajax_fetch_header_template_files', 'fetch_header_template_files');

/**
 * 4A) CONTROLS FRAME JS (Reload button + input fields + notice)
 */
function header_dynamic_customizer_controls_js() { ?>
    <script>
        (function( api, $ ){

            function setSettingAndTextarea(settingId, newVal){
                if (api(settingId)) {
                    api(settingId).set(newVal);
                }
                var control = api.control(settingId);
                if (control) {
                    control.container.find('textarea').val(newVal).trigger('change');
                }
            }

            function fetchFilesIntoControls(template){
                $.ajax({
                    url: '<?php echo esc_url( admin_url("admin-ajax.php") ); ?>',
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    data: {
                        action: 'fetch_header_template_files',
                        template: template,
                        t: Date.now()
                    },
                    success: function(response){
                        if (response && response.success) {
                            var data = response.data || {};

                            if (typeof data.css !== 'undefined') {
                                setSettingAndTextarea('header_template_css', data.css);
                                console.log("✅ CSS reloaded:", data.css.substring(0,100));
                            }
                            if (typeof data.js !== 'undefined') {
                                setSettingAndTextarea('header_template_js', data.js);
                                console.log("✅ JS reloaded:", data.js.substring(0,100));
                            }

                            // Show notice in controls
                            var $notice = $('#header-reload-notice');
                            if (!$notice.length) {
                                $notice = $('<div id="header-reload-notice" style="margin-top:10px;padding:8px;background:#d1f7d6;border:1px solid #2c7a2c;color:#2c7a2c;font-size:13px;border-radius:4px;"></div>');
                                $('#header-reload-button').after($notice);
                            }
                            var now = new Date().toLocaleTimeString();
                            $notice.text("CSS & JS reloaded from file at " + now);

                        } else {
                            console.error('❌ Fetch error:', response ? response.data : 'No response');
                        }
                    },
                    error: function(err){
                        console.error('AJAX request failed', err);
                    }
                });
            }

            api.bind('ready', function(){
                var currentTemplate = api('header_template')();
                fetchFilesIntoControls(currentTemplate);
            });

            api('header_template', function(setting){
                setting.bind(function(newval){
                    fetchFilesIntoControls(newval);
                });
            });

            $(document).on('click', '#header-reload-button', function(e){
                e.preventDefault();
                var currentTemplate = api('header_template')();
                fetchFilesIntoControls(currentTemplate);
            });

        })( wp.customize, jQuery );
    </script>
<?php }
add_action('customize_controls_print_footer_scripts', 'header_dynamic_customizer_controls_js');

/**
 * 4B) PREVIEW FRAME JS (inject CSS/JS into live preview)
 */
function header_dynamic_customizer_preview_js() {
    wp_enqueue_script(
        'theme-header-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        array('customize-preview', 'jquery'), // ✅ ensures wp.customize is available
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('customize_preview_init', 'header_dynamic_customizer_preview_js');




