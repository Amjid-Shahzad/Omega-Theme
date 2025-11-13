<?php
// Automatically add has-mega-widget if any child menu item contains a mega-submenu-widget
add_filter('wp_nav_menu_objects', function($items, $args) {

    // Build a map of parent ID => children
    $children_map = [];
    foreach ($items as $item) {
        if ($item->menu_item_parent) {
            $children_map[$item->menu_item_parent][] = $item;
        }
    }

    foreach ($items as &$item) {
        if (isset($children_map[$item->ID])) {
            foreach ($children_map[$item->ID] as $child) {
                // Check if the menu item's content contains 'mega-submenu-widget'
                // Many themes put HTML in title or description
                if (
                    strpos($child->title, 'mega-submenu-widget') !== false ||
                    (isset($child->description) && strpos($child->description, 'mega-submenu-widget') !== false)
                ) {
                    $item->classes[] = 'has-mega-widget';
                    break;
                }
            }
        }
    }

    return $items;

}, 10, 2);


