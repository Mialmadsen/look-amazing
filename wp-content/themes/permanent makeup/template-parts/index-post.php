<section class="front-page-section" id="blog-stories" role="banner">
    <a class="section_heading" href="<?php echo esc_url( get_permalink( get_option('page_for_posts') ) ); ?>">
        <h2><?php pll_e("Bliv klogere på vores behandlinger") ?>
            <i class="fa-solid fa-wand-magic-sparkles"></i>
        </h2>
    </a>

    <?php 
  // Call reusable template part for latest post
  get_template_part('template-parts/blog-story', null, [
    'query_args' => [
      'post_type'           => 'post',
      'posts_per_page'      => 1,   // latest post
      'ignore_sticky_posts' => true,
      'suppress_filters'    => false, // Polylang
    ]
  ]); 
  ?>
</section>