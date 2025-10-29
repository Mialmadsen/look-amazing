<section class="front-page-section" id="blog-stories" role="region" aria-labelledby="section-heading-testimonials">

    <a class="section_heading" href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">
        <h2 id="section-heading-testimonials">
            <?php pll_e("Anmeldelser"); ?>
            <i class="fa-solid fa-thumbs-up" aria-hidden="true"></i>
        </h2>
    </a>

    <div class="testimonial-wrapper fade-stagger">
        <?php
    $args = array(
      'post_type'      => 'testimonial',
      'posts_per_page' => 3,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
      while ($query->have_posts()) :
        $query->the_post();

        $image = get_field('testimonial_image');
        $name  = get_field('testimonial_name');
        $text  = get_field('testimonial_body_text');
        $age   = get_field('testimonial_age');
        $date  = get_the_date();
    ?>
        <article class="testimonial-card card">
            <header class="testimonial-header">
                <?php if (!empty($image)) : ?>
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($name); ?>"
                    class="testimonial-image" />
                <?php endif; ?>

                <?php if (!empty($name)) : ?>
                <h3 class="testimonial-name">
                    <?php echo esc_html($name); ?>
                    <?php if (!empty($age)) : ?>
                    <span class="testimonial-age">, <?php echo esc_html($age); ?></span>
                    <?php endif; ?>
                </h3>
                <?php endif; ?>
            </header>

            <?php if (!empty($text)) : ?>
            <p class="testimonial-text">
                <?php echo wp_trim_words(wp_kses_post($text), 50, '...'); ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($date)) : ?>
            <footer class="testimonial-date">
                <time datetime="<?php echo get_the_date('c'); ?>">
                    <?php echo esc_html($date); ?>
                </time>
            </footer>
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

    <?php if (is_user_logged_in()) : ?>

    <?php if (isset($_GET['testimonial_submitted'])) : ?>
    <div class="testimonial-success-message">
        <p><?php pll_e("Tak! Din anmeldelse er sendt og afventer godkendelse."); ?></p>
    </div>
    <?php endif; ?>

    <div class="testimonial-write-wrapper">
        <a href="#testimonial-form-popup" class="testimonial-write-button">
            <?php echo esc_html( pll__( 'Skriv en anmeldelse' ) ); ?>
        </a>
    </div>

    <div id="testimonial-form-popup" class="testimonial-modal" role="dialog" aria-modal="true"
        aria-labelledby="form-title">
        <div class="testimonial-modal-content">
            <a href="#blog-stories" class="testimonial-close-button"
                aria-label="<?php echo esc_attr( pll__( 'Luk' ) ); ?>">&times;</a>

            <h3 id="form-title"><?php echo esc_html( pll__( 'Skriv din anmeldelse' ) ); ?></h3>

            <form method="post" class="testimonial-form" enctype="multipart/form-data">
                <p>
                    <label for="testimonial_name"><?php echo esc_html( pll__( 'Navn' ) ); ?> *</label>
                    <input type="text" id="testimonial_name" name="testimonial_name" required>
                </p>

                <p>
                    <label for="testimonial_age"><?php echo esc_html( pll__( 'Alder' ) ); ?></label>
                    <input type="number" id="testimonial_age" name="testimonial_age" min="1">
                </p>

                <p>
                    <label for="testimonial_body"><?php echo esc_html( pll__( 'Din anmeldelse' ) ); ?> *</label>
                    <textarea id="testimonial_body" name="testimonial_body" rows="5" required></textarea>
                </p>

                <p>
                    <label for="testimonial_image"><?php echo esc_html( pll__( 'Upload billede' ) ); ?></label>
                    <input type="file" id="testimonial_image" name="testimonial_image" accept="image/*">
                </p>

                <?php wp_nonce_field('submit_testimonial', 'testimonial_nonce'); ?>

                <p>
                    <button type="submit" name="submit_testimonial">
                        <?php echo esc_html( pll__( 'Send anmeldelse' ) ); ?>
                    </button>
                </p>
            </form>
        </div>
    </div>

    <?php endif; ?>

</section>