<header class="site-header header-vertical">
    <div class="vertical-container">
        <div class="nav-logo">
            <?php if (function_exists('the_custom_logo') && has_custom_logo()) the_custom_logo(); ?>
        </div>

        <nav class="nav-menu-vertical">
                      <?php wp_nav_menu(array('theme_location' => 'main_menu')); ?>


        </nav>

        <div class="nav-icons-vertical">
            <a href="<?php echo esc_url(wp_login_url()); ?>" class="icon-login"><i class="fa fa-user"></i></a>
            <a href="<?php echo esc_url(home_url('/cart')); ?>" class="icon-cart"><i class="fa fa-shopping-cart"></i></a>
            <a href="#" class="icon-search"><i class="fa fa-search"></i></a>
        </div>

        <button class="menu-toggle-vertical" aria-label="Toggle Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>
