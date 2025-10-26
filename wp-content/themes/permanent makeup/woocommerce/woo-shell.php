<?php
/**
 * Template Name: Woo Shell 
 * Description: Use for Cart / Checkout / My Account pages to match site shell.
 */
defined('ABSPATH') || exit;

get_header(); ?>

<div class="mat"></div>

<main class="container front-page-section">
  <?php while ( have_posts() ) : the_post(); ?>
    <a class="section_heading" href="<?php echo esc_url( get_permalink() ); ?>">
      <h2><?php the_title(); ?></h2>
    </a>

    <div class="page-content">
      <?php the_content(); ?>  <!-- This renders [woocommerce_cart] on the Cart page -->
    </div>
  <?php endwhile; ?>
</main>

<?php get_footer();