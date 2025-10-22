<?php
/* Template Name: Register Page */
get_header(); ?>
<section class="front-page-section" >
  <div class="pm-content">
    <?php echo do_shortcode('[theme-my-login action="register"]'); ?>
  </div>
</section>
<?php get_footer();