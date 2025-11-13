<header class="site-header header-minimal">
    <div class="container text-center">
        <div class="nav-logo">
            <?php if (function_exists('the_custom_logo') && has_custom_logo()) the_custom_logo(); ?>
        </div>

        <button class="menu-toggle" aria-label="Toggle Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="nav-menu hidden">
                       <?php wp_nav_menu(array('theme_location' => 'main_menu')); ?>


        </nav>
    </div>
</header>
