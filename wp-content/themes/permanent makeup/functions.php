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
    // GSAP core + ScrollTrigger
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
        [],
        '3.12.2',
        true
    );

    wp_enqueue_script(
        'gsap-scrolltrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
        ['gsap'],
        '3.12.2',
        true
    );

    // Your custom animations (NOTE: lowercase /js/ folder)
    $file_path = get_template_directory() . '/JS/scroll-animations.js';
    wp_enqueue_script(
        'pm-scroll-animations',
        get_template_directory_uri() . '/JS/scroll-animations.js',
        ['gsap', 'gsap-scrolltrigger'],
        file_exists($file_path) ? filemtime($file_path) : null, // cache-bust on change
        true
    );
}
add_action('wp_enqueue_scripts', 'permanent_makeup_enqueue_scripts');

function pm_register_strings() {
    pll_register_string("PM", "Anmeldelser");
    pll_register_string("PM", "Vores Sustainability Initiatives");
    pll_register_string("PM", "I vores klinik har vi en bevidst tilgang til bæredygtighed, og vi arbejder aktivt på at gøre vores forbrug mere miljøvenligt.");
    pll_register_string("PM", "Læs mere", );
    pll_register_string("PM", "Vil du hjælpe os?");
   pll_register_string("PM", "Vi vil gerne blive klogere på vores kunder, derfor er vi igang med at udføre en spørgeundersøgelse.");
    pll_register_string("PM", "Spørgeskema");
    pll_register_string("PM", "Behandlingen, der matcher dine behov");
    pll_register_string("PM", "Bliv klogere på vores behandlinger");
    pll_register_string("PM", "Galleri");
    pll_register_string("PM", "Book tid");
    pll_register_string("PM", "Åbningstider:");
    pll_register_string("PM", "Adresse:");







}
 

add_action("init", "pm_register_strings");

