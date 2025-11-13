<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add "Mega Menus" metabox to Appearance > Menus
 */
function mega_menu_metabox() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    add_meta_box(
        'add-mega-menu',
        __( 'Mega Menus', 'design-theme' ),
        'theme_render_mega_menu_metabox',
        'nav-menus',
        'side',
        'default'
    );
}
add_action( 'admin_head-nav-menus.php', 'mega_menu_metabox' );

/**
 * Render metabox with all published Mega Menus
 */
function theme_render_mega_menu_metabox() {
    $mega_menus = get_posts( array(
        'post_type'      => 'mega_menu',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    ) );

    if ( empty( $mega_menus ) ) {
        echo '<p>' . esc_html__( 'No Mega Menus found.', 'design-theme' ) . '</p>';
        return;
    }

    ?>
    <div id="posttype-mega-menu" class="posttypediv">
        <div id="tabs-panel-mega-menu-all" class="tabs-panel tabs-panel-active">
            <ul id="mega-menu-checklist" class="categorychecklist form-no-clear">
                <?php foreach ( $mega_menus as $menu ) : 
                    $id   = esc_attr( $menu->ID );
                    $slug = esc_attr( $menu->post_name );
                    $name = esc_html( $menu->post_title );
                ?>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="<?php echo $id; ?>"> 
                            <?php echo $name; ?>
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
                        <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php echo esc_attr( $name ); ?>">
                        <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="#<?php echo esc_attr( $slug ); ?>">
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <p class="button-controls">
            <span class="add-to-menu">
                <button type="submit" class="button-secondary submit-add-to-menu right" 
                    value="<?php esc_attr_e( 'Add to Menu', 'design-theme' ); ?>" 
                    name="add-post-type-menu-item" 
                    id="submit-posttype-mega-menu">
                    <?php esc_html_e( 'Add to Menu', 'design-theme' ); ?>
                </button>
                <span class="spinner"></span>
            </span>
        </p>
    </div>
    <?php
}
