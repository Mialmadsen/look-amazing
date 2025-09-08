<?php get_header(); ?>
<!-- Hero image from component -->
<?php
// Get the “Posts page” ID (Settings → Reading → Posts page)
$page_id = (int) get_option('forside');

// Get ACF fields from that page
$hero_field = get_field('frontpage_image', );        // could be URL or array
$heading    = get_field('', $page_id);
$h1    = get_field('frontpage_heading', $page_id);
$h3    = get_field('frontpage_subheading', $page_id);


// Normalize the image to a URL
$background_image = is_array($hero_field) ? ($hero_field['url'] ?? '') : (string) $hero_field;


get_template_part('template-parts/components/hero', null, [
  'background_image' => $background_image,
  'heading'          => $heading,
  'frontpage_heading' => $h1,
  'frontpage_subheading' => $h3,

]);
?>



<?php get_template_part("template-parts/index", "sustain") ?>
<?php get_template_part("template-parts/index", "post") ?>
<?php get_template_part("template-parts/index", "survey") ?>
<?php get_template_part('template-parts/index', 'testimonials'); ?>
<?php get_template_part('template-parts/index', 'galleri-2'); ?>
 







<?php get_footer(); ?>