<?php
/**
 * Enqueue per-widget CSS/JS files for active widget areas only.
 */
if (!defined('ABSPATH')) exit;

function theme_enqueue_widget_area_assets(): void {
	global $wp_registered_sidebars;
	if (!is_iterable($wp_registered_sidebars) || empty($wp_registered_sidebars)) return;

	$theme_dir = get_template_directory();
	$theme_uri = get_template_directory_uri();

	foreach ($wp_registered_sidebars as $sidebar) {
		$sidebar_id = $sidebar['id'] ?? '';
		if (!$sidebar_id) continue;

		if (!is_active_sidebar($sidebar_id)) continue; // only when used on the page

		$id       = sanitize_title($sidebar_id);
		$css_path = "{$theme_dir}/assets/css/widgets/{$id}.css";
		$js_path  = "{$theme_dir}/assets/js/widgets/{$id}.js";

		if (file_exists($css_path)) {
			wp_enqueue_style(
				"widget-area-{$id}-css",
				"{$theme_uri}/assets/css/widgets/{$id}.css",
				[],
				@filemtime($css_path) ?: null
			);
		}
		if (file_exists($js_path)) {
			wp_enqueue_script(
				"widget-area-{$id}-js",
				"{$theme_uri}/assets/js/widgets/{$id}.js",
				[], // add 'jquery' here only if your scripts need it
				@filemtime($js_path) ?: null,
				true
			);
		}
	}
}
add_action('wp_enqueue_scripts', 'theme_enqueue_widget_area_assets', 25);
