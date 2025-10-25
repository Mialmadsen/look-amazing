<?php
/**
 * Template Name: Treatments Shop (EN)
 * Description: Outputs page content and (fallback) the Woo products shortcode.
 */
defined('ABSPATH') || exit;

get_header(); ?>

<div class="mat"></div>

<main class="container front-page-section">
  <?php
  while ( have_posts() ) : the_post(); ?>

       
    <div class="page-description" style="margin-bottom: 50px;"><p>Explore our online shop</p></div>

    <div class="page-content cards_layout_page fade-stagger space">
      <?php the_content(); // <-- runs your Shortcode block ?>
     

      
    </div> 
    

    <?php
    // Fallback: if no [products] shortcode is present, render it anyway
    $content = get_post_field('post_content', get_the_ID());
    if ( strpos( $content, '[products' ) === false ) {
      echo '<div class="cards_layout_page fade-stagger space">';
      echo do_shortcode('[products paginate="true" columns="3" orderby="menu_order" order="ASC"]');
      echo '</div>';
    }
    ?>

  <?php endwhile; ?>
</main>

<?php get_footer();