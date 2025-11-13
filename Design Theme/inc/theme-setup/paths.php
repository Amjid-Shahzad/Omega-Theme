<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// all files paths defined here

define('Theme_uri',get_template_directory_uri());
define('Theme_dir',get_template_directory());

// CSS files paths
$main_css = Theme_uri . '/assets/css/main.css';
$site_header_css = Theme_uri . '/assets/css/site-header.css';
$site_footer_css = Theme_uri . '/assets/css/site-footer.css';
$theme_color_css = Theme_uri . '/assets/css/colors.css';
$woocommerce_css = Theme_uri . '/assets/css/woocommerce.css';


// JS files paths
$main_js = Theme_uri . '/assets/js/main.js';
