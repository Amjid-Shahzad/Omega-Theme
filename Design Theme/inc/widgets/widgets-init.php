<?php
/**
 * Register All Widget Areas and Add CSS Editor Support
 */
function register_theme_widget_areas() {

    // 🔹 General Sidebars
    for ($i = 1; $i <= 5; $i++) {
        register_sidebar(array(
            'name'          => 'Sidebar ' . $i,
            'id'            => 'sidebar-' . $i,
            'before_widget' => '<div class="sidebar-widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="sidebar-widget-title">',
            'after_title'   => '</h4>',
        ));
    }

    // 🔹 Blog Sidebars
    for ($i = 1; $i <= 5; $i++) {
        register_sidebar(array(
            'name'          => 'Blog Sidebar ' . $i,
            'id'            => 'blog-sidebar-' . $i,
            'before_widget' => '<div class="blog-widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="blog-widget-title">',
            'after_title'   => '</h4>',
        ));
    }

    // 🔹 Extra Widget Areas (you can extend here)
    register_sidebar(array(
        'name'          => 'Footer Widgets',
        'id'            => 'footer-widgets',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'register_theme_widget_areas');


/**
 * Save CSS for Mega Menu via AJAX
 */
add_action('wp_ajax_save_mega_menu_css', function(){
    check_ajax_referer('save-mega-menu-css');

    $sidebar_id = sanitize_text_field($_POST['sidebar_id']);
    $css = wp_kses_post($_POST['css']);

    if($sidebar_id && $css !== null){
        update_option('mega_menu_css_' . $sidebar_id, $css);
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
});


/**
 * Add custom CSS field to every widget
 */
function widget_custom_css_form($widget, $return, $instance) {
    $css = isset($instance['custom_css']) ? $instance['custom_css'] : '';
    ?>
    <p>
        <label for="<?php echo $widget->get_field_id('custom_css'); ?>">
            <?php _e('Custom CSS:'); ?>
        </label>
        <textarea class="widefat" rows="6" 
            id="<?php echo $widget->get_field_id('custom_css'); ?>"
            name="<?php echo $widget->get_field_name('custom_css'); ?>"><?php echo esc_textarea($css); ?></textarea>
    </p>
    <?php
}
add_action('in_widget_form', 'widget_custom_css_form', 10, 3);

/**
 * Save custom CSS field
 */
function widget_custom_css_update($instance, $new_instance, $old_instance) {
    if (isset($new_instance['custom_css'])) {
        $instance['custom_css'] = $new_instance['custom_css'];
    }
    return $instance;
}
add_filter('widget_update_callback', 'widget_custom_css_update', 10, 3);

/**
 * Print widget custom CSS on frontend
 */
function widget_custom_css_frontend($params) {
    global $wp_registered_widgets;

    $widget_id = $params[0]['widget_id'];
    $widget_obj = $wp_registered_widgets[$widget_id];

    if (!empty($widget_obj['callback']) && is_array($widget_obj['callback'])) {
        $widget_instance = get_option($widget_obj['callback'][0]->option_name);
        $number = $widget_obj['params'][0]['number'];

        if (isset($widget_instance[$number]['custom_css']) && $widget_instance[$number]['custom_css']) {
            echo "<style id='widget-css-{$widget_id}'>" . $widget_instance[$number]['custom_css'] . "</style>";
        }
    }

    return $params;
}
add_filter('dynamic_sidebar_params', 'widget_custom_css_frontend');






