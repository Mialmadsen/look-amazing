<?php

defined('ABSPATH') || exit;

get_header('shop'); ?>
<div class='mat'></div>
<main class="container front-page-section">
    

  <?php
  // Optional: show the page title pulled from the assigned Shop page
  if ( apply_filters('woocommerce_show_page_title', true) ) {
    echo '<a class="section_heading" href="' . esc_url( get_permalink( wc_get_page_id('shop') ) ) . '">';
    echo '<h2>' . woocommerce_page_title(false) . '</h2>';
    echo '</a>';
  }

  // Optional: render the Shop page editor content (intro text, blocks)
  do_action('woocommerce_archive_description');

  if ( woocommerce_product_loop() ) :

    // Your grid wrapper with your classes
    echo '<div class="cards_layout_page space fade-stagger">';

      woocommerce_product_loop_start();

      while ( have_posts() ) :
        the_post();
        wc_get_template_part( 'content', 'product' ); // uses plugin default for now
      endwhile;

      woocommerce_product_loop_end();

    echo '</div>'; // .cards_layout_page

    // Native WooCommerce pagination (will go to /behandlinger-shop/page/2/)
    do_action('woocommerce_after_shop_loop');

  else :
    do_action('woocommerce_no_products_found');
  endif;
  ?>
</main>

<?php get_footer('shop'); ?>