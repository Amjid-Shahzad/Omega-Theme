<?php
/**
 * Theme Dashboard for Omega Design
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ------------------------------------------------------------
 * MAIN DASHBOARD MENU
 * ------------------------------------------------------------
 */
function theme_dashboard_menu() {
	add_menu_page(
		__( 'Omega Design', 'design-theme' ),               // Page title
		__( 'Omega Design', 'design-theme' ),               // Menu title
		'manage_options',                                   // Capability
		'theme-dashboard',                                  // Slug
		'theme_dashboard_page',                             // Callback function
		get_stylesheet_directory_uri() . '/assets/icons/theme-logo.png', // Icon
		2                                                   // Position
	);
}
add_action( 'admin_menu', 'theme_dashboard_menu' );

/**
 * ------------------------------------------------------------
 * SUBMENU PAGES (Global JS, Colors, Fonts)
 * ------------------------------------------------------------
 */
function theme_dashboard_submenus() {

add_submenu_page(
    'theme-dashboard',
    __( 'Global JS', 'design-theme' ),
    __( 'Global JS', 'design-theme' ),
    'manage_options',
    'theme-global-js',
    'theme_global_js_page' // ✅ Matches your function name
);

	// ✅ Colors Page
	add_submenu_page(
		'theme-dashboard',
		__( 'Colors', 'design-theme' ),
		__( 'Colors', 'design-theme' ),
		'manage_options',
		'theme-colors',
		'theme_colors_page'
	);

	// ✅ Fonts Page
	add_submenu_page(
		'theme-dashboard',
		__( 'Fonts', 'design-theme' ),
		__( 'Fonts', 'design-theme' ),
		'manage_options',
		'theme-fonts',
		'theme_fonts_page'
	);

	// ✅ Remove Duplicate “Omega Design” submenu entry
	remove_submenu_page( 'theme-dashboard', 'theme-dashboard' );
}
add_action( 'admin_menu', 'theme_dashboard_submenus' );

/**
 * ------------------------------------------------------------
 * DASHBOARD LANDING PAGE
 * ------------------------------------------------------------
 */
function theme_dashboard_page() { ?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Theme Dashboard', 'design-theme' ); ?></h1>
		<p><?php esc_html_e( 'Welcome to your Omega Design Dashboard. Manage Global JS, Colors, Fonts, and more all in one place.', 'design-theme' ); ?></p>

		<hr>

		<h2><?php esc_html_e( 'Quick Links', 'design-theme' ); ?></h2>
		<ul style="line-height:1.8;">
			<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=theme-global-js' ) ); ?>"><?php esc_html_e( 'Edit Global JS', 'design-theme' ); ?></a></li>
			<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=theme-colors' ) ); ?>"><?php esc_html_e( 'Manage Colors', 'design-theme' ); ?></a></li>
			<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=theme-fonts' ) ); ?>"><?php esc_html_e( 'Manage Fonts', 'design-theme' ); ?></a></li>
			<li><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open Customizer', 'design-theme' ); ?></a></li>
		</ul>

		<p style="margin-top:20px;font-style:italic;color:#777;">
			<?php esc_html_e( 'Tip: All changes made in the Customizer are reflected instantly in the preview window.', 'design-theme' ); ?>
		</p>
	</div>
<?php }

/**
 * ------------------------------------------------------------
 * COLORS PAGE
 * ------------------------------------------------------------
 */
function theme_colors_page() { ?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Global Colors', 'design-theme' ); ?></h1>
		<p><?php esc_html_e( 'Manage and preview your global theme colors.', 'design-theme' ); ?></p>
		<p><?php esc_html_e( 'Use the Customizer for instant color preview.', 'design-theme' ); ?></p>
		<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=theme_global_panel' ) ); ?>" class="button button-primary">
			<?php esc_html_e( 'Open Color Customizer', 'design-theme' ); ?>
		</a>
	</div>
<?php }

/**
 * ------------------------------------------------------------
 * FONTS PAGE
 * ------------------------------------------------------------
 */
function theme_fonts_page() { ?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Global Fonts', 'design-theme' ); ?></h1>
		<p><?php esc_html_e( 'Manage typography settings for your theme.', 'design-theme' ); ?></p>
		<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=theme_fonts_section' ) ); ?>" class="button button-primary">
			<?php esc_html_e( 'Open Fonts Customizer', 'design-theme' ); ?>
		</a>
	</div>
<?php 
}
