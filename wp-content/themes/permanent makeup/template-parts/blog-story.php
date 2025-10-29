<?php
/**
 * Template part: Blog Story Card
 * Expects $args = array( 'query_args' => [] )
 */

$q = new WP_Query( $args['query_args'] ?? [] );

if ( $q->have_posts() ) :
  while ( $q->have_posts() ) : $q->the_post();

    $image   = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
    $heading = get_the_title();
    $text    = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 40);
    $link    = get_permalink();
    $overlay = get_field('overlay_image');
    ?>
<div class="story-card-grid fade-stagger">
    <?php if ($image): ?>
    <div class="story-card-img card">
        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($heading); ?>">
        <?php if ($overlay) echo wp_get_attachment_image($overlay['ID'], 'medium_large', false, [
            'class'=>'story-card-overlay',
            'alt'=>esc_attr($overlay['alt'] ?: $heading),
            'loading'=>'lazy',
            'decoding'=>'async'
          ]); ?>
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
            <?php pll_e("Læs mere"); ?> <span class="arrow">→</span>
        </a>
    </div>
</div>
<?php
  endwhile;
  wp_reset_postdata();
endif;