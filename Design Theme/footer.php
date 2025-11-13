</main><!-- #site-content -->

    <?php
    /**
     * Output the selected footer template
     * (Static or Dynamic via /inc/footer/footer-loader.php)
     */
    if ( function_exists( 'theme_output_footer' ) ) {
        theme_output_footer();
    } else {
       
    }
    ?>

    <?php wp_footer(); ?>
</body>
</html>
