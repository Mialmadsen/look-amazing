<?php
/* Template Name: Login Page */
get_header(); ?>

<main class="pm-container" id="main">
  <div class="pm-content">
    <?php
      
      echo do_shortcode('[theme-my-login]');
    ?>
  </div>
</main>

<?php get_footer();