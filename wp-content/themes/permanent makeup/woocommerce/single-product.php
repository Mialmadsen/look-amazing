<?php

defined('ABSPATH') || exit;

get_header(); // ← your normal header (not get_header('shop'))
?>

<div class="mat"></div>

<main class="container front-page-section">
  <?php
  if ( have_posts() ) :
    while ( have_posts() ) :
      the_post();

      // Notices, scripts, etc. (keep Woo behaviors)
      do_action( 'woocommerce_before_single_product' );

      // The standard Woo layout (gallery + summary, tabs, related…)
      wc_get_template_part( 'content', 'single-product' );

      do_action( 'woocommerce_after_single_product' );
    endwhile;
  endif;
  ?>
</main>

<?php get_footer(); ?>