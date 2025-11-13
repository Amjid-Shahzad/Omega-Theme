<?php
/**
 * Auto-import all theme images and icons into the WordPress Media Library
 * when the theme is activated.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function theme_import_default_media_on_activation() {

    // Directories containing bundled media assets
    $directories = [
        get_template_directory() . '/assets/images/',
        get_template_directory() . '/assets/icons/',
    ];

    foreach ( $directories as $dir_path ) {

        if ( ! is_dir( $dir_path ) ) continue;

        // Supported file formats
        $media_files = glob( $dir_path . '*.{jpg,jpeg,png,gif,svg,webp}', GLOB_BRACE );

        if ( empty( $media_files ) ) continue;

        foreach ( $media_files as $file_path ) {

            $filename = basename( $file_path );

            // Skip if already imported
            $existing = get_page_by_title( $filename, OBJECT, 'attachment' );
            if ( $existing ) continue;

            // Copy file into WP uploads folder
            $upload = wp_upload_bits( $filename, null, file_get_contents( $file_path ) );
            if ( $upload['error'] ) continue;

            $filetype = wp_check_filetype( $filename, null );

            $attachment = [
                'guid'           => $upload['url'],
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ];

            // Insert attachment
            $attach_id = wp_insert_attachment( $attachment, $upload['file'] );

            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );
        }
    }

    error_log( '✅ Default theme images & icons imported successfully.' );
}
add_action( 'after_switch_theme', 'theme_import_default_media_on_activation' );
