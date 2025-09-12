<?php


// 1. Styles & Fonts

function permanent_makeup_enqueue_styles() {
    // Main Stylesheet
    wp_enqueue_style(
        'permanent-makeup-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css') // cache-bust
    );

    // Load only the Font Awesome subset you need (solid only, lighter than full FA)
    wp_enqueue_style(
        'font-awesome-solid',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/solid.min.css',
        [],
        '6.5.0'
    );
    wp_enqueue_style(
        'font-awesome-core',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/fontawesome.min.css',
        [],
        '6.5.0'
    );
}
add_action('wp_enqueue_scripts', 'permanent_makeup_enqueue_styles');

// Menu

function permanent_makeup_register_menus() {
    add_theme_support('menus');
    register_nav_menus([
        'primary_en' => __('Primary Menu English', 'permanent-makeup'),
        'primary_da' => __('Primary Menu Dansk',   'permanent-makeup'),
    ]);
}
add_action('after_setup_theme', 'permanent_makeup_register_menus');


// 3. Scripts (GSAP + Custom)

function permanent_makeup_enqueue_scripts() {
    // GSAP core + ScrollTrigger (defer for performance)
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

    // Custom animations
    $file_path = get_template_directory() . '/js/scroll-animations.js';
    if (file_exists($file_path)) {
        wp_enqueue_script(
            'pm-scroll-animations',
            get_template_directory_uri() . '/js/scroll-animations.js',
            ['gsap', 'gsap-scrolltrigger'],
            filemtime($file_path), // cache-bust
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'permanent_makeup_enqueue_scripts');



// 4. Polylang strings

function pm_register_strings() {
    $strings = [
        "Anmeldelser",
        "Vores Sustainability Initiatives",
        "I vores klinik har vi en bevidst tilgang til bæredygtighed, og vi arbejder aktivt på at gøre vores forbrug mere miljøvenligt.",
        "Læs mere",
        "Vil du hjælpe os?",
        "Vi vil gerne blive klogere på vores kunder, derfor er vi igang med at udføre en spørgeundersøgelse.",
        "Spørgeskema",
        "Behandlingen, der matcher dine behov",
        "Bliv klogere på vores behandlinger",
        "Galleri",
        "Book tid",
        "Åbningstider:",
        "Adresse:",
        "Har du lyst til at lære mere?",
        "Læs om FN 17 verdensmål, og bliv klogere på hvordan vi alle kan være med til at gøre en forskel.",
    ];

    foreach ($strings as $s) {
        pll_register_string("PM", $s);
    }
}
add_action("init", "pm_register_strings");



// Remove WP emojis
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

// Remove wp-embed
add_action('wp_footer', function () {
    wp_deregister_script('wp-embed');
}, 100);

// Remove dashicons for non-logged-in users
add_action('wp_enqueue_scripts', function () {
    if (!is_user_logged_in()) {
        wp_deregister_style('dashicons');
    }
});

