<?php
/* Template Name: Behandlinger Shop (Auto by slug) */
get_header();

// // Get ACF fields from options page
// $hero_image = get_field('shop_image', 'option');
// $heading = get_field('shop_heading', 'option');

// // Normalize the image to a URL
// $background_image = is_array($hero_image) ? ($hero_image['url'] ?? '') : (string) $hero_image;

// // Render the hero component
// get_template_part('template-parts/components/hero', null, [
//     'background_image' => $background_image,
//     'heading' => $heading
// ]);


if ( have_posts() ) :
  while ( have_posts() ) : the_post(); ?>
  <div class="mat"></div>
  
    <main class="container front-page-section">

      

      <!-- Products grid -->
      <div class="cards_layout_page fade-stagger space">
        <?php
        // Current page for pagination (supports /page/2 on a static page)
        $paged = max( 1, get_query_var('paged') ?: get_query_var('page') );

        $q = new WP_Query( [
          'post_type'      => 'product',
          'posts_per_page' => 9,
          'orderby'        => 'menu_order',
          'order'          => 'ASC',
          'paged'          => $paged,
        ] );

        if ( $q->have_posts() ) :
          while ( $q->have_posts() ) : $q->the_post();

            $_product = wc_get_product( get_the_ID() );

            $img = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
            if ( ! $img && function_exists( 'wc_placeholder_img_src' ) ) {
              $img = wc_placeholder_img_src();
            }

            $excerpt = get_the_excerpt();
            ?>
            <div class="article-card card">
              <a class="card-image-wrapper" href="<?php the_permalink(); ?>">
                <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
              </a>

              <div class="card-text">
                <a href="<?php the_permalink(); ?>" class="no-underline" style="text-decoration:none;color:inherit">
                  <h3><?php the_title(); ?></h3>
                </a>

                <?php
                $cats = get_the_terms( get_the_ID(), 'product_cat' );
                if ( $cats && ! is_wp_error( $cats ) ) {
                  echo '<div class="card-categories">';
                  foreach ( $cats as $c ) {
                    echo '<a class="card-category" href="' . esc_url( get_term_link( $c ) ) . '">' . esc_html( $c->name ) . '</a>';
                  }
                  echo '</div>';
                }
                ?>

                <p><?php echo esc_html( wp_strip_all_tags( wp_trim_words( $excerpt, 40 ) ) ); ?></p>

                <div class="card-meta">
                  <span class="price"><?php echo $_product ? $_product->get_price_html() : ''; ?></span>
                  <span class="cart-cta">
                    <?php
                      // Woo's add-to-cart template expects $product global.
                      global $product;
                      $product = $_product;
                      if ( function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
                        woocommerce_template_loop_add_to_cart();
                      }
                    ?>
                  </span>
                </div>
              </div>
            </div>
          <?php endwhile; ?>

          <div class="space" style="width:100%;">
            <?php
            echo paginate_links( [
              'total'     => $q->max_num_pages,
              'current'   => $paged,
              'mid_size'  => 2,
              'prev_text' => __( '« Previous', 'woocommerce' ),
              'next_text' => __( 'Next »', 'woocommerce' ),
            ] );
            ?>
          </div>

          <?php
          // Clean up
          wp_reset_postdata();
          if ( function_exists( 'wc_setup_product_data' ) ) {
            wc_setup_product_data( null );
          }
          ?>

        <?php else : ?>
          <p><?php echo esc_html__( 'Ingen produkter fundet.', 'woocommerce' ); ?></p>
        <?php endif; // end products loop ?>
      </div>
    </main>
  <?php endwhile;
endif; // end page loop

get_footer();