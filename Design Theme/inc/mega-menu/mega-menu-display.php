<?php
if (!defined('ABSPATH')) exit;

/**
 * Inject mega-menu content while WordPress builds each menu item.
 * Works for every theme and walker.
 */
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {

    // Only handle #slug links
    if (empty($item->url) || substr($item->url, 0, 1) !== '#') {
        return $item_output;
    }

    $slug = sanitize_title(substr($item->url, 1));
    if (!$slug) return $item_output;

    // Look up the mega menu CPT
    $mega_menu = get_page_by_path($slug, OBJECT, 'mega_menu');
    if (!$mega_menu) return $item_output;

    // Render and clean content
    $content = apply_filters('the_content', $mega_menu->post_content);
    $content = preg_replace('/<div[^>]*wp-block-post-title[^>]*>.*?<\/div>/is', '', $content);
    $content = preg_replace('/<h[1-6][^>]*>' . preg_quote($mega_menu->post_title, '/') . '<\/h[1-6]>/is', '', $content);
    $content = preg_replace('/<p>(\s|&nbsp;)*<\/p>/', '', $content);
    $content = trim($content);
    if ($content === '') return $item_output;

    // Build dropdown container
    $mega_html = sprintf(
        '<div class="mega-menu-dropdown" id="mega-%s">%s</div>',
        esc_attr($slug),
        $content
    );

    // Append it for top-level and sub-menus alike
    $item_output .= $mega_html;
    return $item_output;

}, 20, 4);


/**
 * Add identifying classes automatically.
 */
add_filter('wp_nav_menu_objects', function ($items) {
    foreach ($items as &$item) {
        if (!empty($item->url) && substr($item->url, 0, 1) === '#') {
            $slug = sanitize_title(substr($item->url, 1));
            if (!empty($item->menu_item_parent)) {
                $item->classes[] = 'sub-mega-menu';
                $item->classes[] = $slug . '-mega-menu';
            } else {
                $item->classes[] = 'has-mega-menu';
                $item->classes[] = $slug . '-mega-menu';
            }
        }
    }
    return $items;
});
