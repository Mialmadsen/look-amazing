<?php get_header(); ?>
<!-- Hero image from component -->
<?php
// Get the “Posts page” ID (Settings → Reading → Posts page)
$posts_page_id = (int) get_option('page_for_posts');

// Get ACF fields from that page
$hero_field = get_field('article_hero', $posts_page_id);        // could be URL or array
$heading    = get_field('', $posts_page_id);

// Normalize the image to a URL
$background_image = is_array($hero_field) ? ($hero_field['url'] ?? '') : (string) $hero_field;

// Render the hero component (WP 5.5+ supports args)
get_template_part('template-parts/components/hero', null, [
  'background_image' => $background_image,
  'heading'          => $heading,
]);
?>

 <?php get_template_part('template-parts/index', 'testimonials'); ?>
 
<a href="<?php echo get_permalink( get_option('page_for_posts') ); ?>" class="btn">
    View All Posts
</a>









<?php get_footer(); ?>