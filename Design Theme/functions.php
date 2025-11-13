<?php



// ====================================================
// EXIT IF ACCESSED DIRECTLY
// ====================================================
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

require_once get_template_directory() . '/early-output-check.php';

// ====================================================
// MAIN PATH FILE
// ====================================================
require_once get_template_directory() . '/inc/theme-setup/paths.php';




// ====================================================
// CUSTOM BLOCKS CATEGORIES
// ====================================================
require Theme_dir . '/custom-blocks/block-categories-list.php';



// ====================================================
// INCLUDE THEME DASHBOARD
// ====================================================
require Theme_dir . '/inc/admin/admin.php';


// ====================================================
// ANIMATION LIBRARY
// ====================================================
require Theme_dir . '/inc/animation/animation-library.php';


// ====================================================
// BLOCK EDITOR
// ====================================================
add_action('enqueue_block_assets', function() {
    wp_enqueue_style(
        'editor-global-css',
        get_template_directory_uri() . '/assets/css/editor.css', // or your file path
        [],
        wp_get_theme()->get('Version')
    );
});



// ====================================================
// FOOTER SYSTEM
// ====================================================
require_once Theme_dir . '/inc/footer/footer-init.php';
require_once Theme_dir . '/inc/footer/footer-meta.php';
require_once Theme_dir . '/inc/footer/footer-metabox.php';
require_once Theme_dir . '/inc/footer/footer-enqueue.php';
require_once Theme_dir . '/inc/footer/dynamic-footer-customizer.php';
require_once Theme_dir . '/inc/footer/footer-loader.php';



// ====================================================
// CUSTOMIZER SETTINGS
// ====================================================
require_once Theme_dir . '/inc/customizer/global/global-init.php';







/**
 * Customizer preview assets + data for AJAX sync
 */
function customizer_preview_assets() {
    wp_enqueue_script(
        'theme-customizer-live',
        get_template_directory_uri() . '/assets/js/customizer-live.js',
        array( 'customize-preview', 'jquery' ),
        wp_get_theme()->get( 'Version' ),
        true
    );

    wp_localize_script( 'theme-customizer-live', 'ThemeJSONSync', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'theme_json_sync' ),
    ) );
}
add_action( 'customize_preview_init', 'customizer_preview_assets' );











// Customizer modules
require_once get_template_directory() . '/inc/customizer/global/global-color.php';
require_once get_template_directory() . '/inc/customizer/global/global-themejson-sync.php';
// ====================================================
require_once Theme_dir . '/inc/headers/dynamic-header-customizer.php';




// ====================================================
// MEGA MENU SYSTEM
// ====================================================
require Theme_dir . '/inc/mega-menu/mega-menu-init.php';
require Theme_dir . '/inc/mega-menu/mega-menu-meta.php';
require Theme_dir . '/inc/mega-menu/mega-menu-metabox.php';
require Theme_dir . '/inc/mega-menu/mega-menu-display.php';
require Theme_dir . '/inc/mega-menu/mega-menu-enqueue.php';


// ====================================================
// PAGE ASSETS SYSTEM
// ====================================================
require Theme_dir . '/inc/page-assets/page-assets-init.php';
require Theme_dir . '/inc/page-assets/page-assets-enqueue.php';
require Theme_dir . '/inc/page-assets/page-assets-meta.php';
require Theme_dir . '/inc/page-assets/header-codemirror.php';


// ====================================================
// THEME SETUP
// ====================================================
// include enqueue file
require Theme_dir . '/inc/theme-setup/enqueue.php';

// include menus file
require Theme_dir . '/inc/theme-setup/menus.php';
// include paths file

// include patterns file
require Theme_dir . '/inc/theme-setup/patterns.php';

// include media import file
require Theme_dir . '/inc/theme-setup/theme-media-import.php';
// theme options
require Theme_dir . '/inc/theme-setup/theme-options.php';
// include woocommerce setup file
require Theme_dir . '/inc/theme-setup/woocommerce_setup.php';
// include editor live preview file
require Theme_dir . '/inc/page-assets/editor-live-preview.php';


// ====================================================
// WIDGETS SYSTEM
// ====================================================
require Theme_dir . '/inc/widgets/widgets-init.php';
require Theme_dir . '/inc/widgets/widget-assets-init.php';
require Theme_dir . '/inc/widgets/widget-assets-meta.php';
require Theme_dir . '/inc/widgets/widget-assets-enqueue.php';



// page assets page title toggle
require Theme_dir . '/inc/page-assets/page-title-toggle.php';




// include Default Pages Creation
require Theme_dir . '/templates/default-pages.php';
// include default home page
require Theme_dir . '/templates/default-home.php';






add_action('init', function() {
    if (isset($_GET['mega_menu']) && $_GET['mega_menu'] !== '') {
        $slug = sanitize_title($_GET['mega_menu']);
        $mega = get_page_by_path($slug, OBJECT, 'mega_menu');

        if ($mega) {
            // Set correct headers for HTML output
            header('Content-Type: text/html; charset=utf-8');

            // Output only the content (no header/footer)
            echo apply_filters('the_content', $mega->post_content);
        } else {
            // Graceful empty response if slug invalid
            status_header(404);
            echo '<!-- Mega Menu not found -->';
        }

        // Stop WP from loading full theme
        exit;
    }
});



/**
 * Clean Mega Menu endpoint: returns only the menu content (no header, no title)
 */
add_action('init', function () {
    if (isset($_GET['mega_menu']) && $_GET['mega_menu'] !== '') {
        $slug = sanitize_title($_GET['mega_menu']);
        $mega = get_page_by_path($slug, OBJECT, 'mega_menu');

        if ($mega instanceof WP_Post) {
            // Stop WP from rendering templates
            header('Content-Type: text/html; charset=utf-8');

            // Extract and filter post content only
            $content = apply_filters('the_content', $mega->post_content);

            // Remove any post titles accidentally embedded in content
            $content = preg_replace('/<h[1-6][^>]*class="[^"]*entry-title[^"]*"[^>]*>.*?<\/h[1-6]>/', '', $content);
            $content = preg_replace('/<h[1-6][^>]*>.*?' . preg_quote($mega->post_title, '/') . '.*?<\/h[1-6]>/', '', $content);

            echo trim($content);
        } else {
            status_header(404);
            echo '<!-- Mega menu not found -->';
        }

        exit; // 🔥 critical — prevent theme header/footer loading
    }
});

// Automatically append arrow icons to menu items with submenus
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {
    if (in_array('menu-item-has-children', $item->classes)) {
        // Inject arrow icon shortcode after the link text
        $arrow = do_shortcode('[arrow_icon dir="down" color="#111" active="#22c55e" size="3px" thickness="2px" animate="false"]');
        $item_output = str_replace('</a>', ' ' . $arrow . '</a>', $item_output);
    }
    return $item_output;
}, 10, 4);


// Ensure pages fully support Elementor
add_action( 'init', function() {
    add_post_type_support( 'page', [ 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ] );
});
// Ensure mega_menu CPT supports Elementor
add_action( 'init', function() {    
    add_post_type_support( 'mega_menu', [ 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ] );
}); 
// Ensure footer_section CPT supports Elementor
add_action( 'init', function() {    
    add_post_type_support( 'footer_section', [ 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ] );
}); 














add_filter( 'wp_theme_json_data_theme', function( $theme_json ) {
    $path = get_stylesheet_directory() . '/theme.json';
    if ( file_exists( $path ) ) {
        $mtime = filemtime( $path );
        $data = $theme_json->get_data();
        $data['version'] = 3; // ensure still valid
        $theme_json = new WP_Theme_JSON( $data, 'theme' );
    }
    return $theme_json;
});


add_action('after_setup_theme', function() {
    // Enable editor styles support
    add_theme_support('editor-styles');

    // Load the main stylesheet inside the block editor
    add_editor_style('assets/css/main.css');
});






function register_blocks() {
    $blocks_dir = get_template_directory() . '/custom-blocks/blocks';
    $build_dir  = get_template_directory() . '/custom-blocks/build';

    // Ensure directory exists
    if ( ! is_dir( $blocks_dir ) ) {
        return;
    }

    // Loop through each subfolder in /blocks
    $block_folders = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

    foreach ( $block_folders as $block_folder ) {
        // Path to block.json
        $block_json = $block_folder . '/block.json';

        if ( file_exists( $block_json ) ) {
            // Load block.json data
            $block_name = basename( $block_folder );

            // Try to locate build files for this block
            $asset_file = $build_dir . '/' . $block_name . '/index.asset.php';
            $script_file = $build_dir . '/' . $block_name . '/index.js';
            $style_file  = $build_dir . '/' . $block_name . '/style-index.css';

            if ( file_exists( $asset_file ) && file_exists( $script_file ) ) {
                $assets = include( $asset_file );

                // Register compiled block assets
                wp_register_script(
                    'custom-block-' . $block_name,
                    get_template_directory_uri() . '/custom-blocks/build/' . $block_name . '/index.js',
                    $assets['dependencies'],
                    $assets['version'],
                    true
                );

                if ( file_exists( $style_file ) ) {
                    wp_register_style(
                        'custom-block-' . $block_name,
                        get_template_directory_uri() . '/custom-blocks/build/' . $block_name . '/style-index.css',
                        array(),
                        $assets['version']
                    );
                }

                register_block_type(
                    $block_folder,
                    array(
                        'editor_script' => 'custom-block-' . $block_name,
                        'style'         => 'custom-block-' . $block_name,
                    )
                );
            } else {
                // If no build files, register using raw source
                register_block_type( $block_folder );
            }
        }
    }
}
add_action( 'init', 'register_blocks' );







