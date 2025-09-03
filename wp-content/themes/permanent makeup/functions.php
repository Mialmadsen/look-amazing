<?php
function permanent_makeup_enqueue_styles() {
    //Main Stylesheet 
    wp_enqueue_style('permanent-makeup-style', get_stylesheet_uri(), array(), '1.0.0');

    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        array(),
        '6.5.0'
    );
}
add_action('wp_enqueue_scripts', 'permanent_makeup_enqueue_styles');
function permanent_makeup_register_menus() {
    add_theme_support('menus');
    register_nav_menus([
        'primary_en' => __('Primary Menu English', 'permanent-makeup'),
        'primary_da' => __('Primary Menu Dansk',   'permanent-makeup'),
    ]);
}
add_action('after_setup_theme', 'permanent_makeup_register_menus');

// Enqueue GSAP and animation scripts
function permanent_makeup_enqueue_scripts() {
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'gsap-scrolltrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
        ['gsap'],
        null,
        true
    );

   

    wp_enqueue_script(
        'tidslerne-scroll-animations',
        get_template_directory_uri() . '/JS/scroll-animations.js',
        ['gsap', 'gsap-scrolltrigger'],
        null,
        true
    );

   
}
add_action('wp_enqueue_scripts', 'permanent_makeup_enqueue_scripts');

