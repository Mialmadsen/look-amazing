<?php get_header(); ?>

<?php
// Locate the page by slug and map to current language
$base   = get_page_by_path('sustainability-initiatives'); // <- your slug
$page_id = function_exists('pll_get_post') ? pll_get_post($base->ID) : $base->ID;

// Pull ACF fields from that page (normal page, not options)
$hero_field = get_field('sustainable_hero_image', $page_id); // array or url
$h1         = get_field('sustainable_hero_header', $page_id);
$h3         = get_field('sustainable_hero_small_tekst', $page_id);

// Normalize image to URL
$background_image = is_array($hero_field) ? ($hero_field['url'] ?? '') : (string) $hero_field;

// Render your component
get_template_part('template-parts/components/hero', null, [
  'background_image'      => $background_image,
  'frontpage_heading'     => $h1,
  'frontpage_subheading'  => $h3,
]);
?>

<?php get_template_part("template-parts/index", "flyer") ?>

<?php get_footer(); ?>