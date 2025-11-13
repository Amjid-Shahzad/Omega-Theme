<?php
/**
 * Widgets as Menu Items with Mega Menu Dropdown + CSS Editor
 */

// 1️⃣ Register Widgets Meta Box in Menus
function add_widgets_meta_box_to_menu() {
    add_meta_box(
        'widgets-menu-meta-box',
        __( 'Menu Widgets' ),
        'render_widgets_meta_box',
        'nav-menus',
        'side',
        'default'
    );
}
add_action( 'admin_head-nav-menus.php', 'add_widgets_meta_box_to_menu' );

// 2️⃣ Render Widgets Meta Box
function render_widgets_meta_box() {
    global $wp_registered_sidebars;

    if ( empty( $wp_registered_sidebars ) ) {
        echo '<p>No widget areas registered.</p>';
        return;
    }

    echo '<ul id="widgets-checklist" class="categorychecklist form-no-clear">';
    foreach ( $wp_registered_sidebars as $id => $sidebar ) {
        echo '<li>';
        echo '<label>';
        echo '<input type="checkbox" class="widgets-menu-item" value="' . esc_attr( $id ) . '">';
        echo esc_html( $sidebar['name'] );
        echo '</label>';
        echo '</li>';
    }
    echo '</ul>';
    echo '<p class="button-controls">';
    echo '<span class="add-to-menu">';
    echo '<button type="submit" class="button-secondary submit-add-to-menu" id="add-widgets-to-menu">' . __('Add to Menu') . '</button>';
    echo '<span class="spinner"></span>';
    echo '</span>';
    echo '</p>';

    ?>
    <script>
    jQuery(document).ready(function($){
        $('#add-widgets-to-menu').on('click', function(e){
            e.preventDefault();
            var checked = $('#widgets-checklist input:checked');
            var menuID = $('#menu').val();

            checked.each(function(){
                var sidebarID = $(this).val();
                var sidebarName = $(this).parent().text().trim();

                $.post(ajaxurl, {
                    action: 'add_widget_to_menu',
                    menu: menuID,
                    sidebar_id: sidebarID,
                    sidebar_name: sidebarName,
                    _wpnonce: '<?php echo wp_create_nonce('add-widget-menu'); ?>'
                }, function(response){
                    if(response.success){
                        location.reload();
                    }
                });
            });
        });
    });
    </script>
    <?php
}

// 3️⃣ AJAX handler to add menu items in database
function ajax_add_widget_to_menu() {
    check_ajax_referer('add-widget-menu');

    $menu_id = intval($_POST['menu']);
    $sidebar_id = sanitize_text_field($_POST['sidebar_id']);
    $sidebar_name = sanitize_text_field($_POST['sidebar_name']);

    if(!$menu_id || !$sidebar_id || !$sidebar_name){
        wp_send_json_error('Invalid data');
    }

    $menu_item_data = array(
        'menu-item-title' => $sidebar_name,
        'menu-item-url' => '#' . $sidebar_id,
        'menu-item-type' => 'custom',
        'menu-item-status' => 'publish',
        'menu-item-object' => 'widget-area',
    );

    wp_update_nav_menu_item($menu_id, 0, $menu_item_data);
    wp_send_json_success('Widget added');
}
add_action('wp_ajax_add_widget_to_menu', 'ajax_add_widget_to_menu');

// 4️⃣ Add CSS Editor Field per Menu Item
add_action('wp_nav_menu_item_custom_fields', function($item_id, $item, $depth, $args, $id){
    ?>
    <p class="field-mega-menu-css description description-wide">
        <label for="edit-menu-item-mega-css-<?php echo $item_id; ?>">
            Mega Menu Custom CSS<br>
            <textarea style="width:100%" 
                      id="edit-menu-item-mega-css-<?php echo $item_id; ?>" 
                      class="widefat code edit-menu-item-mega-css"
                      name="menu-item-mega-css[<?php echo $item_id; ?>]"><?php
                        echo esc_textarea(get_post_meta($item_id, '_mega_menu_css', true));
                      ?></textarea>
        </label>
    </p>
    <?php
}, 10, 5);

// 5️⃣ Save CSS Field
add_action('wp_update_nav_menu_item', function($menu_id, $menu_item_db_id){
    if(isset($_POST['menu-item-mega-css'][$menu_item_db_id])){
        update_post_meta($menu_item_db_id, '_mega_menu_css', wp_kses_post($_POST['menu-item-mega-css'][$menu_item_db_id]));
    }
}, 10, 2);

// 6️⃣ Frontend Render Mega Menu with Custom CSS
function render_widget_menu_item( $item_output, $item, $depth, $args ) {
    if( isset($item->url) && strpos($item->url, '#') === 0 ){
        $sidebar_id = substr($item->url, 1);
        if(is_active_sidebar($sidebar_id)){
            ob_start();
            dynamic_sidebar($sidebar_id);
            $widgets_output = ob_get_clean();

            $custom_css = get_post_meta($item->ID,'_mega_menu_css',true);
            $style = $custom_css ? ' style="'.esc_attr($custom_css).'"' : '';

            if($depth == 0){
                $item_output .= '<div class="mega-menu-dropdown"'.$style.'>' . $widgets_output . '</div>';
            } else {
                $item_output = '<div class="mega-submenu-widget"'.$style.'>' . $widgets_output . '</div>';
            }
        }
    }
    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'render_widget_menu_item', 10, 4);
