<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Animation Library Functions


// =============================
// Arrow Icon (Customizable)
// =============================
function arrow_icon($atts = []) {
    $atts = shortcode_atts([
        'dir'       => 'right',      // right, left, up, down
        'color'     => '#111',       // arrow color
        'hover'     => '#22c55e',    // hover color
        'size'      => '8px',        // arrow line length
        'thickness' => '2px',        // border thickness
        'animate'   => 'false',      // true or false
    ], $atts);

    $cls = 'arrow-icon';
    if ($atts['animate'] === 'true') $cls .= ' animating';

    $attr = '
        class="'.$cls.'"
        data-dir="'.$atts['dir'].'"
        data-animate="'.esc_attr($atts['animate']).'"
        style="
            --arrow-size:'.$atts['size'].';
            --arrow-thickness:'.$atts['thickness'].';
            --arrow-color:'.$atts['color'].';
            --arrow-hover:'.$atts['hover'].';
        "
    ';

    return '<span '.$attr.'></span>';
}
add_shortcode('arrow_icon', 'arrow_icon');
