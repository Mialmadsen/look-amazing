<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo ("name")?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <nav class="pm-nav">
  <div class="pm-nav__inner">
    <?php
      // Pick the correct menu location by current language
      $loc = 'primary_en';
      if (function_exists('pll_current_language') && pll_current_language('slug') === 'da') {
        $loc = 'primary_da';
      }

      wp_nav_menu([
        'theme_location' => $loc,
        'menu_class'     => 'pm-nav__list',
        'container'      => false,
      ]);
    ?>

    <div class="lang-switch">
      <?php if (function_exists('pll_the_languages')) {
        pll_the_languages([
          'show_flags' => 0,
          'display_names_as' => 'slug', // shows en, da
          // 'show_names' => 1, // outputs: EN / DA
          'echo'       => 1,
          'hide_if_no_translation' => 0,
          'force_home' => 0,
        ]);
      } ?>
    </div>
  </div>
</nav>
