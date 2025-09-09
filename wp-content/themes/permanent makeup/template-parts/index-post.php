<section class="front-page-section" id="blog-stories">
  <a class="section_heading" href="<?php echo esc_url( get_permalink( get_option('page_for_posts') ) ); ?>">
    <h2><?php pll_e("Bliv klogere på vores behandlinger") ?></h2>
  </a>

  <?php
  // Latest normal blog post
  $q = new WP_Query([
    'post_type'           => 'post',
    'posts_per_page'      => 1,
    'ignore_sticky_posts' => true,
    'suppress_filters'    => false, // lets Polylang filter language
  ]);

  if ( $q->have_posts() ) :
    while ( $q->have_posts() ) : $q->the_post();

      // Image: featured or first <img> from content
      $image = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
      $heading = get_the_title();
      $text    = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 40 );
      $link    = get_permalink();
      // NEW: ACF overlay image
$overlay = get_field('overlay_image');
  ?>
<div class="story-card-grid fade-stagger">
  <?php if ( !empty($image) ) : ?>
  <div class="story-card-img card">
    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($heading); ?>">

    <?php if ($overlay) :
      echo wp_get_attachment_image(
        $overlay['ID'],
        'medium_large',
        false,
        [
          'class'    => 'story-card-overlay',
          'alt'      => esc_attr($overlay['alt'] ?: $heading),
          'loading'  => 'lazy',
          'decoding' => 'async',
        ]
      );
    endif; ?>
  </div>
  <?php endif; ?>
  <div class="story-card-card card">
          <div class="story-card-heading">
            <h3><?php echo esc_html($heading); ?></h3>
          </div>
          <div class="story-card-text">
            <p><?php echo esc_html($text); ?></p>
          </div>
          <a href="<?php echo esc_url($link); ?>" class="btn">
            <?php pll_e("Læs mere") ?> <span class="arrow">→</span>
          </a>
        </div>
  </div>
</div>
    
      
      
  <?php
    endwhile; wp_reset_postdata();
  endif;
  ?>
</section>