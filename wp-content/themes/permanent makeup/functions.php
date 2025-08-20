<?php
function permanent_makeup_enqueue_styles() {
    //Main Stylesheet 
    wp_enqueue_style('permanent-makeup-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'permanent_makeup_enqueue_styles');