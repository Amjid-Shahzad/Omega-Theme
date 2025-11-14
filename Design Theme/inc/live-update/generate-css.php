<?php

/**
 * Generate main.css and editor.css on Customizer publish
 */

function theme_generate_css_files()
{

    // Get customizer values (fallback to defaults)
    $vars = [
        'website_background'              => get_theme_mod('website_background', '#f7f7f7'),
        'page_background'                 => get_theme_mod('page_background', '#5803c7'),
        'header_background'               => get_theme_mod('header_background', '#ffffff'),
        'footer_background'               => get_theme_mod('footer_background', '#ffffff'),
        'mega_menu_background'            => get_theme_mod('mega_menu_background', '#ffffff'),
        'color_primary'                   => get_theme_mod('color_primary', '#0073e6'),
        'color_on_primary'                => get_theme_mod('color_on_primary', '#005bb5'),
        'color_secondary'                 => get_theme_mod('color_secondary', '#ff6600'),
        'color_on_secondary'              => get_theme_mod('color_on_secondary', '#ff6600'),
        'color_accent'                    => get_theme_mod('color_accent', '#00cc66'),
        'color_on_accent'                 => get_theme_mod('color_on_accent', '#00cc66'),
        'color_success'                   => get_theme_mod('color_success', '#28a745'),
        'color_error'                     => get_theme_mod('color_error', '#dc3545'),
        'text_primary'                    => get_theme_mod('text_primary', '#212121'),
        'text_secondary'                  => get_theme_mod('text_secondary', '#757575'),
        'text_muted'                      => get_theme_mod('text_muted', '#999999'),
        'link'                            => get_theme_mod('link', '#007bff'),
        'border'                          => get_theme_mod('border', '#dadada'),
        'divider'                         => get_theme_mod('divider', '#e0e0e0'),
        'shadow'                          => get_theme_mod('shadow', 'rgba(0,0,0,0.1)'),
        'hover'                           => get_theme_mod('hover', 'rgba(0,0,0,0.05)'),
        'focus'                           => get_theme_mod('focus', 'rgba(0,0,0,0.1)'),

        // Fonts
        'base_font'                       => get_theme_mod('base_font', 'jost'),
        'heading_font'                    => get_theme_mod('heading_font', 'poppins'),
        'subheading_font'                 => get_theme_mod('subheading_font', 'poppins'),
        'button_font'                     => get_theme_mod('button_font', 'poppins'),

        // Buttons
        'button_background'               => get_theme_mod('button_background', '#292fb2'),
        'button_text'                     => get_theme_mod('button_text', '#ffffff'),
        'button_border_color'             => get_theme_mod('button_border_color', '#3038d3'),
        'button_border_width'             => get_theme_mod('button_border_width', 2),
        'button_radius'                   => get_theme_mod('button_radius', 5),
        'button_padding_tb'               => get_theme_mod('button_padding_tb', 10),
        'button_padding_lr'               => get_theme_mod('button_padding_lr', 20),

        // Hover buttons
        'button_background_hover'         => get_theme_mod('button_background_hover', '#ffffff'),
        'button_text_hover'               => get_theme_mod('button_text_hover', '#3038d3'),
        'button_border_color_hover'       => get_theme_mod('button_border_color_hover', '#3038d3'),
        'button_border_width_hover'       => get_theme_mod('button_border_width_hover', 2),
        'button_radius_hover'             => get_theme_mod('button_radius_hover', 5),
        'button_padding_tb_hover'         => get_theme_mod('button_padding_tb_hover', 10),
        'button_padding_lr_hover'         => get_theme_mod('button_padding_lr_hover', 20),
    ];

    // Build :root content
    $root = ":root {\n";
    foreach ($vars as $key => $value) {
        $css_var = "--" . str_replace("_", "-", $key);
        $root .= "  {$css_var}: {$value};\n";
    }
    $root .= "}\n";

    // Final CSS (imports + variables + original static CSS)
    $full_css = <<<CSS
/* ===============================
   Import Google Fonts
   =============================== */
@import url("https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

/* ===============================
   Root Variables (Generated)
   =============================== */
$root

/* ===============================
   Typography Usage
   =============================== */
body {
  background-color: var(--website-background);
  color: var(--text-primary);
  font-family: var(--base-font);
}

h1,
h2,
h3,
h4,
h5,
h6 {
  font-family: var(--heading-font);
}

.subtitle {
  font-family: var(--subheading-font);
}

button,
.wp-block-button__link {
  font-family: var(--button-font);
}

/* ===============================
   Button Styling
   =============================== */
button,
input[type="button"],
input[type="submit"],
.wp-block-button__link {
  background-color: var(--button-background);
  color: var(--button-text);
  border: var(--button-border-width) solid var(--button-border-color);
  border-radius: var(--button-radius);
  padding: var(--button-padding-tb) var(--button-padding-lr);
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  transition: all 0.25s ease;
}

button:hover,
button:focus,
input[type="button"]:hover,
input[type="button"]:focus,
input[type="submit"]:hover,
input[type="submit"]:focus,
.wp-block-button__link:hover,
.wp-block-button__link:focus {
  background-color: var(--button-background-hover);
  color: var(--button-text-hover);
  border-color: var(--button-border-color-hover);
  border-width: var(--button-border-width-hover);
  border-radius: var(--button-radius-hover);
  padding: var(--button-padding-tb-hover) var(--button-padding-lr-hover);
  outline: none;
}

.site-content {
  padding: 0 20px;
}
.site-footer {
  background-color: var(--footer-background);
  text-align: center;
}
.site-header {
  background-color: var(--header-background);
}
.mega-menu {
  background-color: var(--mega-menu-background);
}
CSS;

    // Write frontend
    file_put_contents(get_template_directory() . '/assets/css/main.css', $full_css);

    // Write editor
    file_put_contents(get_template_directory() . '/assets/css/editor.css', $full_css);
}

/** Trigger on publish */
add_action('customize_save_after', 'theme_generate_css_files');
