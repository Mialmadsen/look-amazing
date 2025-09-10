<section class="page-section" id="blog-stories">
  <div class="cards_layout--scaled">
    <a class="section_heading" href="<?php echo esc_url( get_permalink( get_option('page_for_posts') ) ); ?>">
      <h2><?php pll_e("Behandlingen, der matcher dine behov")?></h2>
    </a>
    <div class="cards_layout_page"> <?php
    $q = new WP_Query([
      'post_type'           => 'post',
      'posts_per_page'      => 3,
      'ignore_sticky_posts' => true,
      'suppress_filters'    => false,
    ]);

    if ( $q->have_posts() ) :
      while ( $q->have_posts() ) : $q->the_post();

        $image   = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
        $heading = get_the_title();
        $text    = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 40 );
        $link    = get_permalink();

        get_template_part( 'template-parts/components/card', null, [
          'image'      => $image,
          'heading'    => $heading,
          'text'       => $text,
          'link'       => $link,
          'date'       => get_the_date(),
          'author'     => get_the_author(),
          'categories' => get_the_category(),
          'tags'       => get_the_tags(),
        ] );

      endwhile;
      wp_reset_postdata();
    endif;
    ?>
</div>

   
  </div>
</section>