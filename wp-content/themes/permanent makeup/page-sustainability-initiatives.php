<?php get_header(); ?>

<?php
// Locate the page by slug and map to current language
$base   = get_page_by_path('sustainability-initiatives'); // <- your slug
$page_id = function_exists('pll_get_post') ? pll_get_post($base->ID) : $base->ID;

// Pull ACF fields from that page (normal page, not options)
$hero_field = get_field('sustainable_hero_image', $page_id); // array or url
$h1         = get_field('sustainable_hero_header', $page_id);
$h3         = get_field('sustainable_hero_small_tekst', $page_id);
$title_1= get_field('world_goals_header', $page_id);
$text_1= get_field('world_goals_text', $page_id);
$title_2= get_field('our_initiatives_header', $page_id);
$text_2= get_field('our_initiatives_text', $page_id);
$video= get_field('video_link', $page_id);


// Normalize image to URL
$background_image = is_array($hero_field) ? ($hero_field['url'] ?? '') : (string) $hero_field;

// Render your component
get_template_part('template-parts/components/hero', null, [
  'background_image'      => $background_image,
  'frontpage_heading'     => $h1,
  'frontpage_subheading'  => $h3,

]);
?>

<section class="front-page-section sustainability">
  <div class="sust-container">
    <h2><?php echo esc_html($title_1); ?></h2>
    <div class="prose">
      <?php echo wpautop( wp_kses_post($text_1) ); ?>
    </div>

    

    
</section>
<?php get_template_part('template-parts/index', 'flyer'); ?>

<section class="front-page-section sustainability">
  
    
<h2><?php echo esc_html($title_2); ?></h2>
    <div class="prose">
      <?php echo wpautop( wp_kses_post($text_2) ); ?>
    </div>
  </div>
</section>

<section class="front-page-section">
  <?php get_template_part('template-parts/blog-story', null, [
  'query_args' => [
    'p' => 223,   // your post ID
    'post_type' => 'post',
    'suppress_filters' => false,
  ]
]); ?>
</section>

<div class="video-container">
  <iframe width="560" height="315" src="<?php echo esc_url($video) ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</div>



<?php get_footer(); ?>