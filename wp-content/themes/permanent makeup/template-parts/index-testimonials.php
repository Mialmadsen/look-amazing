<section class="">
    <div class="testimonial-box">
        <div class="">
            <?php
            $args = array(
                'post_type' => 'testimonial',
                'posts_per_page' => 3
            );
            $query = new WP_Query($args);
            echo '<pre>';
// print_r($query->posts);
// echo '</pre>';

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $image = get_field('testimonial_image'); // image URL
                    $heading = get_field('testimonial_name');
                    $age = get_field('testimonial_age');
                    $text = get_field("testimonial_body_text");
                    $date = get_the_date();     
            ?>
                    <div class="article-card card">
                        <div class="card-image-wrapper">
                            <img src="<?php echo esc_url($image["url"]); ?>" alt="<?php echo esc_attr($heading); ?>">
                        </div>
                        <div class="card-text">
                            <?php if (!empty($heading)) : ?>
                                <h3><?php echo esc_html($heading); ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($text)) : ?>
                                <p><?php echo wp_trim_words(wp_kses_post($text), 50, '...'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else:
                echo '<p>No testimonials found. Check that the custom post type "testimonials" exists and has published posts.</p>';
            endif;
            ?>
        </div>
    </div>
</section>