<?php get_header(); ?>


<!-- Hero image from component -->
<?php
// Get the “Posts page” ID (Settings → Reading → Posts page)
$posts_page_id = (int) get_option('page_for_posts');

// Get ACF fields from that page
$hero_field = get_field('article_hero', $posts_page_id);        // could be URL or array
$heading    = get_field('article_heading', $posts_page_id);

// Normalize the image to a URL
$background_image = is_array($hero_field) ? ($hero_field['url'] ?? '') : (string) $hero_field;

// Render the hero component (WP 5.5+ supports args)
get_template_part('template-parts/components/hero', null, [
  'background_image' => $background_image,
  'heading'          => $heading,
]);
?>

<section class="front-page-section">

<div class="cards_layout_page fade-stagger">
           <?php if(have_posts()): ?>
            <?php
                    while (have_posts()) : the_post();
                        $image      = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                        $heading    = get_the_title();
                        $text       = get_the_excerpt();
                        $link       = get_permalink();
                        $date       = get_the_date();
                        $author     = get_the_author();
                        $categories = get_the_category();
                        $tags       = get_the_tags();

                        get_template_part('template-parts/components/card', null, [
                            'image'      => $image,
                            'heading'    => $heading,
                            'text'       => $text,
                            'link'       => $link,
                            'date'       => $date,
                            'author'     => $author,
                            'categories' => $categories,
                            'tags'       => $tags,
                        ]);
                    endwhile;
                    ?>
        <?php endif; ?>





        
  
</div>



</section>
<?php get_template_part("template-parts/index", "survey") ?>
<div class="space"></div>
<?php get_footer(); ?>

