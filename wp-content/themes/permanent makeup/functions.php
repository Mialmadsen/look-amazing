<?php


// 1. Styles & Fonts

function permanent_makeup_enqueue_styles() {
    // 1) Main stylesheet (cache-busted)
    wp_enqueue_style(
        'permanent-makeup-style',
        get_stylesheet_uri(),
        [],
        filemtime( get_stylesheet_directory() . '/style.css' )
    );

    // 2) Font Awesome (core + subsets)
    wp_enqueue_style(
        'font-awesome-core',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/fontawesome.min.css',
        [],
        '6.5.0'
    );
    wp_enqueue_style(
        'font-awesome-solid',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/solid.min.css',
        ['font-awesome-core'],
        '6.5.0'
    );
    wp_enqueue_style(
        'font-awesome-brands',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/brands.min.css',
        ['font-awesome-core'],
        '6.5.0'
    );

    // 3) Form CSS (only on the survey form template)
    if ( is_page_template( 'template-form.php' ) ) { // adjust to your filename
        $rel  = '/css/form.css';
        $path = get_stylesheet_directory() . $rel;

        wp_enqueue_style(
            'survey-form',
            get_stylesheet_directory_uri() . $rel,
            ['permanent-makeup-style'],
            file_exists($path) ? filemtime($path) : null
        );
    }

    // 4) Testimonial CSS (only on login/register templates)
    if ( is_front_page() || is_home() ) {
    $rel  = '/css/testimonial.css';
    $path = get_stylesheet_directory() . $rel;

    wp_enqueue_style(
        'testimonial-form',
        get_stylesheet_directory_uri() . $rel,
        ['permanent-makeup-style'],
        file_exists($path) ? filemtime($path) : null
    );
}
}


add_action( 'wp_enqueue_scripts', 'permanent_makeup_enqueue_styles' );
// Load custom styles (and optional animations) only on Shop + product categories
add_action('wp_enqueue_scripts', function () {
  // Native Woo contexts
  $is_shopish = is_shop() || is_product_taxonomy();

  // Your English page that acts like the shop
  $is_treatments =
       is_page('treatments')                                   // match by slug
    || is_page_template('page-templates/treatments-shop-en.php') // if you use that template
    || ( function_exists('pll_current_language')               // if PLL maps EN shop page
         && pll_current_language() === 'en'
         && function_exists('pll_get_post')
         && is_page( pll_get_post( wc_get_page_id('shop'), 'en' ) ) );

  if ( $is_shopish || $is_treatments ) {
    wp_enqueue_style(
      'shop-tweaks',
      get_stylesheet_directory_uri() . '/css/shop.css',
      [],
      '1.0'
    );
  }
}, 20);


add_action('wp_enqueue_scripts', function () {
  if ( is_product() ) {
    wp_enqueue_style(
      'single-product-tweaks',
      get_stylesheet_directory_uri() . '/css/single-product.css',
      [],
      '1.0'
    );
  }
});

add_action('template_redirect', function () {
  if ( ! function_exists('pll_current_language') ) return;

  // Only handle English
  if ( pll_current_language() !== 'en' ) return;

  // If someone lands on the shop archive while in EN, send them to the EN page.
  if ( function_exists('is_shop') && is_shop() ) {
    // Try to find the EN translation of the assigned Shop page
    $shop_id = wc_get_page_id('shop');
    $en_shop_id = function_exists('pll_get_post') ? pll_get_post($shop_id, 'en') : 0;

    // Prefer the translated page permalink if it exists; otherwise fall back to /en/treatments/
    $target = $en_shop_id ? get_permalink($en_shop_id) : home_url('/en/treatments/');

    // Avoid loop if we're already on that page
    if ( ! is_page($en_shop_id) ) {
      wp_redirect($target, 301);
      exit;
    }
  }

  // Extra safety: direct path hit to /en/behandlinger-shop/ → /en/treatments/
  $req = trim( parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/' );
  if ( $req === 'en/behandlinger-shop' ) {
    wp_redirect( home_url('/en/treatments/'), 301 );
    exit;
  }
});
// Menu

function permanent_makeup_register_menus() {
    add_theme_support('menus');
    register_nav_menus([
        'primary_en' => __('Primary Menu English', 'permanent-makeup'),
        'primary_da' => __('Primary Menu Dansk',   'permanent-makeup'),
    ]);
}
add_action('after_setup_theme', 'permanent_makeup_register_menus');


// WooCommerce support
add_action('after_setup_theme', function() {
    add_theme_support('woocommerce');
});

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

add_action('wp_enqueue_scripts', function () {
  if ( is_shop() || is_product_taxonomy() ) {
    wp_enqueue_script(
      'products-reveal',
      get_stylesheet_directory_uri() . '/JS/shop.js',
      [], '1.0', true
    );
  }
});

// Header user badge (login link vs. "Hi, Name")
function theme_user_badge() {
    $current_url = home_url( $_SERVER['REQUEST_URI'] ?? '/' );

    if ( is_user_logged_in() ) {
    $u = wp_get_current_user();
    $name = $u->display_name ?: $u->user_login;
    $initial = strtoupper( mb_substr( $name, 0, 1 ) );
    $profile_url = get_edit_user_link( $u->ID );
    $current_url = home_url( $_SERVER['REQUEST_URI'] ?? '/' );
    ?>
    <div class="user-badge">
      <a class="user-badge__link" href="<?php echo esc_url( $profile_url ); ?>">
        <span class="user-badge__avatar" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
        <span class="user-badge__hi"><?php echo esc_html__('Hi, ', 'your-txt') . esc_html( $name ); ?></span>
      </a>

      <!-- CSS-only dropdown -->
      <details class="user-badge__dropdown">
        <summary class="user-badge__toggle" aria-label="Open user menu">
          <i class="fa-solid fa-chevron-down"></i>
        </summary>
        <div class="user-badge__menu">
          <a class="user-badge__menu-item" href="<?php echo esc_url( wp_logout_url( $current_url ) ); ?>">
            <?php esc_html_e('Log out','your-txt'); ?>
          </a>
        </div>
      </details>
    </div>
    <?php

    } else {
        ?>
        <a class="login-link" href="<?php echo esc_url( wp_login_url( $current_url ) ); ?>" aria-label="<?php esc_attr_e('Log in','your-txt'); ?>">
            
            <i class="fa-solid fa-user"></i>
        </a>
        <?php
    }
}

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
        "Læs om FN 17 verdensmål, og bliv klogere på hvordan vi alle kan være med til to gøre en forskel.",
        'Fornavn',
        'Efternavn',
        'Alder',
        'Køn',
        'Vælg',
        'Mand',
        'Kvinde',
        'Andet',
        'By',
        'Tilføj',
        'Send',
        'Spørgeskema',
        'I høj grad',
        'I nogen grad',
        'Neutral',
        'I lav grad',
        'Ved ikke',
        'Accepter terms og conditions ved booking*',
        'Skriv din anmeldelse',
        'Skriv en anmeldelse',
        'Din anmeldelse', 
        'Upload billede', 
        'Send anmeldelse',
        'Luk',
        'Navn',
        'Tak for din anmeldelse!',
        'Din anmeldelse afventer godkendelse. Vi publicerer den, når en administrator har godkendt den.',
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

// Survey form handling
function survey_form_handler() {
  if ( ! isset($_POST['survey_form_nonce']) || ! wp_verify_nonce($_POST['survey_form_nonce'], 'survey_form_nonce') ) {
    wp_die('Security check failed', 400);
  }

//  fetch and sanitize form data
  $firstname = sanitize_text_field( $_POST['firstname'] ?? '' );
  $lastname  = sanitize_text_field( $_POST['lastname']  ?? '' );
  $age       = intval( $_POST['age'] ?? 0 );
  $gender    = sanitize_text_field( $_POST['gender'] ?? '' );
  $city      = sanitize_text_field( $_POST['city'] ?? '' );
  $comment   = sanitize_textarea_field( $_POST['comment'] ?? '' );

  // Likert answers (q1..q6)
  $answers = [];
  for ($i=1; $i<=6; $i++) {
    $answers["q{$i}"] = isset($_POST["q{$i}"]) ? sanitize_text_field($_POST["q{$i}"]) : '';
  }

  // Create a post to store 
  $post_id = wp_insert_post([
    'post_type'   => 'customer-response',   //new custom post type
    'post_status' => 'publish',
    'post_title'  => sprintf('RESPONSE from %s %s', $firstname, $lastname),
  ]);

  if ($post_id && !is_wp_error($post_id)) {
    
    update_field('first_name', $firstname, $post_id);
    update_field('last_name',  $lastname,  $post_id);
    update_field('age',        $age,       $post_id);
    update_field('gender',     $gender,    $post_id);
    update_field('city',       $city,      $post_id);
    update_field('comment',    $comment,   $post_id);

    // individual text fields q1..q6:
    foreach ($answers as $k => $v) {
      update_field($k, $v, $post_id); // fields named q1, q2, ... q6
    }
   
  }

  // Redirect
  $thankyou = get_page_by_path('thank-you');
  $url = ($thankyou instanceof WP_Post) ? get_permalink($thankyou->ID) : home_url('/');
  wp_safe_redirect($url);
  exit;
}
add_action("admin_post_sample_form", "survey_form_handler");
add_action("admin_post_nopriv_sample_form", "survey_form_handler");


// Add aria-label to Home link in menu for accessibility
function my_home_link_aria_label( $atts, $item, $args ) {
    if ( $item->url === home_url( '/' ) ) {
        $atts['aria-label'] = 'Home'; // Change label if needed
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'my_home_link_aria_label', 10, 3 );

/* for the testimonial form*/ 

function pm_get_testimonial_redirect_url() {
    $home = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
    // ensure trailing slash then add anchor to close the :target modal
    $url  = trailingslashit($home) . '#blog-stories';
    // add a success flag so we can show a message
    return add_query_arg('testimonial_submitted', '1', $url);
}
function handle_testimonial_submission() {
    if (
      isset($_POST['submit_testimonial']) &&
      isset($_POST['testimonial_nonce']) &&
      wp_verify_nonce($_POST['testimonial_nonce'], 'submit_testimonial') &&
      is_user_logged_in()
    ) {
      $name = sanitize_text_field($_POST['testimonial_name']);
      $age = isset($_POST['testimonial_age']) ? intval($_POST['testimonial_age']) : '';
      $body = sanitize_textarea_field($_POST['testimonial_body']);
  
      // Create the testimonial post
      $post_id = wp_insert_post(array(
        'post_type'   => 'testimonial',
        'post_status' => 'pending', // or 'publish' if you want no moderation
        'post_title'  => wp_trim_words($body, 8, '...'),
        'post_content'=> $body,
      ));
  
      if (!is_wp_error($post_id)) {
        update_field('testimonial_name', $name, $post_id);
        update_field('testimonial_age', $age, $post_id);
        update_field('testimonial_body_text', $body, $post_id);
  
        // Handle image upload
        if (!empty($_FILES['testimonial_image']['name'])) {
          $file = $_FILES['testimonial_image'];
  
          if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
          }
  
          $upload_overrides = array('test_form' => false);
          $movefile = wp_handle_upload($file, $upload_overrides);
  
          if ($movefile && !isset($movefile['error'])) {
            $filename = $movefile['file'];
            $filetype = wp_check_filetype($filename, null);
            $attachment = array(
              'post_mime_type' => $filetype['type'],
              'post_title'     => sanitize_file_name(basename($filename)),
              'post_content'   => '',
              'post_status'    => 'inherit'
            );
  
            $attach_id = wp_insert_attachment($attachment, $filename, $post_id);
  
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);
  
            update_field('testimonial_image', $attach_id, $post_id);
          }
        }
      }
  
      wp_redirect( pm_get_testimonial_redirect_url() );
      exit;
    }
  }
  add_action('template_redirect', 'handle_testimonial_submission');
