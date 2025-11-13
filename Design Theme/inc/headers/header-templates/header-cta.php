<header class="site-header header-cta">
    <div class="container">
        <div class="nav-logo">
            <?php if (function_exists('the_custom_logo') && has_custom_logo()) the_custom_logo(); ?>
        </div>

        <nav class="nav-menu">
                       <?php wp_nav_menu(array('theme_location' => 'main_menu')); ?>


        </nav>

        <div class="nav-icons">
            <a href="#" class="nav-cta-btn">Get Started</a>
            <a href="<?php echo esc_url(home_url('/cart')); ?>" class="icon-cart"><i class="fa fa-shopping-cart"></i></a>
            <a href="#" class="icon-search"><i class="fa fa-search"></i></a>
        </div>

        <div id="hamburger-icon" class="hamburger">
            <div class="line line-top"></div>
            <div class="line line-middle"></div>
            <div class="line line-bottom"></div>
        </div>
    </div>
</header>
