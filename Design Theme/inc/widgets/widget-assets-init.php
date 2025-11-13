<?php
/**
 * Ensure per-widget asset files exist.
 * Creates:
 *   /assets/css/widgets/{id}.css
 *   /assets/js/widgets/{id}.js
 */
if (!defined('ABSPATH')) exit;

function theme_widget_assets_ensure_files(): void {
	global $wp_registered_sidebars;
	if (!is_iterable($wp_registered_sidebars) || empty($wp_registered_sidebars)) return;

	$css_dir = get_template_directory() . '/assets/css/widgets/';
	$js_dir  = get_template_directory() . '/assets/js/widgets/';

	if (!file_exists($css_dir)) wp_mkdir_p($css_dir);
	if (!file_exists($js_dir))  wp_mkdir_p($js_dir);

	foreach ($wp_registered_sidebars as $sidebar) {
		if (empty($sidebar['id'])) continue;
		$id = sanitize_title($sidebar['id']);

		$css_file = $css_dir . $id . '.css';
		$js_file  = $js_dir  . $id . '.js';

		if (!file_exists($css_file)) {
			@file_put_contents(
				$css_file,
				"/* CSS for widget area: {$id} */\n.widget-area-{$id} {\n\t/* custom styles */\n}\n",
				LOCK_EX
			);
		}
		if (!file_exists($js_file)) {
			@file_put_contents(
				$js_file,
				"// JS for widget area: {$id}\n",
				LOCK_EX
			);
		}
	}
}

/**
 * Run after sidebars are registered.
 * widgets_init ensures $wp_registered_sidebars is populated.
 */
add_action('widgets_init', 'theme_widget_assets_ensure_files', 20);
