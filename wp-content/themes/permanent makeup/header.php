<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description"
        content="Professionel permanent makeup og skønhedsbehandlinger i Esbjerg. Få smukke bryn, vipper og læber med naturligt resultat. Book din tid i dag!">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <nav class="pm-nav login-nav">
        <div class="pm-nav__inner">


            <!-- Hamburger -->
            <button class="pm-nav__toggle" aria-expanded="false" aria-controls="pm-menu">
                <i class="fa-solid fa-bars icon-bars"></i>
                <i class="fa-solid fa-xmark icon-close"></i>

            </button>


            <?php
    // Pick the correct menu location by current language
    $loc = 'primary_en';
    if ( function_exists('pll_current_language') && pll_current_language('slug') === 'da' ) {
      $loc = 'primary_da';
    }

    // Output UL with our id & class
    wp_nav_menu([
      'theme_location' => $loc,
      'container'      => false,
      'menu_id'        => 'pm-menu',          // <ul id="pm-menu">
      'menu_class'     => 'pm-nav__list',     //   class="pm-nav__list"
    ]);
    ?>

            <div class="lang-switch">
                <?php if ( function_exists('pll_the_languages') ) {
        pll_the_languages([
          'show_flags' => 0,
          'display_names_as' => 'slug', // EN / DA
          'echo'       => 1,
          'hide_if_no_translation' => 0,
          'force_home' => 0,
        ]);
      } ?>
            </div>

            <?php if ( function_exists('theme_user_badge') ) theme_user_badge(); ?>

        </div>
    </nav>