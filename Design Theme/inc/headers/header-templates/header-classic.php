<header class="site-header header-classic">
    <div class="container">
        <div class="nav-logo" id ="navLogo">
            <?php if (function_exists('the_custom_logo') && has_custom_logo()) the_custom_logo(); ?>
        </div>

        <div class="nav-menu" id="navMenu">
          <?php wp_nav_menu(array('theme_location' => 'main_menu')); ?>

</div>

        <div class="nav-icons" id="navIcons">
       
    </div>

    <div class="menu-sidebar" id="menuSidebar">

            <div class="nav-logo" id="navLogo">
                 <?php if (function_exists('the_custom_logo') && has_custom_logo()) the_custom_logo(); ?>
            </div>

                  <?php wp_nav_menu(array('theme_location' => 'main_menu')); ?>
            <div class="hamburger-icon" id="hamburgerIcon" >
                <div class="line line-top"></div>
                <div class="line line-middle"></div>
                <div class="line line-bottom"></div>
            </div>

        </div>

       
</div>
</header>

