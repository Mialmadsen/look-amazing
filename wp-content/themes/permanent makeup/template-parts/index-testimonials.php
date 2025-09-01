<section class="testimonial-section">
  <div class="testimonial-wrapper">
    <?php
    $args = array(
      'post_type'      => 'testimonial',
      'posts_per_page' => 3
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) :
      while ($query->have_posts()) : $query->the_post();

        $image = get_field('testimonial_image');
        $name  = get_field('testimonial_name');
        $text  = get_field('testimonial_body_text');
        $age   = get_field('testimonial_age');
        $date  = get_the_date();
        ?>
        
        <article class="testimonial-card">
          <div class="testimonial-header">
            <?php if (!empty($image)) : ?>
              <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($name); ?>" class="testimonial-image" />
            <?php endif; ?>

            <?php if (!empty($name)) : ?>
              <h3 class="testimonial-name"><?php echo esc_html($name); ?></h3>
            <?php endif; ?>

            <?php if (!empty($age)) : ?>
              <span class="testimonial-age">, <?php echo esc_html($age); ?></span>
            <?php endif; ?>
          </div>

          <?php if (!empty($text)) : ?>
            <p class="testimonial-text"><?php echo wp_trim_words(wp_kses_post($text), 50, '...'); ?></p>
          <?php endif; ?>

          <?php if (!empty($date)) : ?>
            <p class="testimonial-date"><?php echo esc_html($date); ?></p>
          <?php endif; ?>
        </article>

        <?php
      endwhile;
      wp_reset_postdata();
    else :
      echo '<p>Ingen testimonials fundet.</p>';
    endif;
    ?>
  </div>
</section>
