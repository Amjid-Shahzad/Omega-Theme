<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function theme_global_js_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'design-theme' ) );
    }

    $file = get_template_directory() . '/assets/js/global.js';

    // ✅ Ensure file exists
    if ( ! file_exists( $file ) ) {
        $dir = dirname( $file );
        if ( ! is_dir( $dir ) ) {
            wp_mkdir_p( $dir );
        }
        file_put_contents( $file, "// Global JS\nwindow.Theme = { log: function(msg){ console.log(msg); } };");
    }

    // ✅ Handle Save
    if ( isset( $_POST['save_global_js'] ) ) {
        check_admin_referer( 'global_js_nonce', 'global_js_nonce_field' );
        $code = wp_unslash( $_POST['global_js_code'] );
        if ( file_put_contents( $file, $code ) !== false ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Global JS saved successfully!', 'design-theme' ) . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Could not save. Check file permissions.', 'design-theme' ) . '</p></div>';
        }
    }

    $content = file_get_contents( $file );

    // ✅ Enqueue CodeMirror
    $settings = wp_enqueue_code_editor( array( 'type' => 'text/javascript' ) );
    if ( $settings !== false ) {
        wp_add_inline_script(
            'code-editor',
            sprintf(
                'jQuery(function() {
                    wp.codeEditor.initialize("global-js-editor", %s);
                });',
                wp_json_encode( $settings )
            )
        );
    }

    wp_enqueue_script( 'wp-theme-plugin-editor' );
    wp_enqueue_style( 'wp-codemirror' );

    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Edit Global JS', 'design-theme' ); ?></h1>
        <p><?php esc_html_e( 'Modify your theme’s global JavaScript here. This file is loaded across the entire site.', 'design-theme' ); ?></p>

        <form method="post">
            <?php wp_nonce_field( 'global_js_nonce', 'global_js_nonce_field' ); ?>
            
            <textarea id="global-js-editor" name="global_js_code" style="width:100%;height:500px;"><?php echo esc_textarea( $content ); ?></textarea>
            
            <p style="margin-top:15px;">
                <input type="submit" name="save_global_js" class="button button-primary" value="<?php esc_attr_e( 'Save Global JS File', 'design-theme' ); ?>">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=theme-dashboard' ) ); ?>" class="button"><?php esc_html_e( 'Back to Dashboard', 'design-theme' ); ?></a>
            </p>
        </form>
    </div>
    <?php
}
