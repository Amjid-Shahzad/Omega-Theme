<!DOCTYPE html>
<html <?php language_attributes();?> >
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> <?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
// Get selected header template from Customizer
$header_template = get_theme_mod('header_template', 'header-classic');

// Build file path
$template_file = locate_template('inc/headers/header-templates/' . $header_template . '.php');

if ( $template_file ) {
    // Load the selected template
    include $template_file;
} else {
    // Fallback: load classic header if template not found
    include locate_template('inc/headers/header-templates/header-classic.php');
}
?>

<main class="site-content">
