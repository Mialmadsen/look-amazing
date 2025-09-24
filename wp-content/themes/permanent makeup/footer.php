<footer class="site-footer">

  <div class="footer-container">

   <!-- Venstre kolonne lokation img -->
<div class="footer-col footer-map">
  <?php 
  $location_img = get_field("location_img", "option");
  if ( !empty($location_img) ) : ?>
    <img 
      src="<?php echo esc_url($location_img['url']); ?>" 
      alt="<?php echo esc_attr($location_img['alt']); ?>" 
      width="100%" 
      height="200" 
      style="object-fit: cover;">
  <?php endif; ?>
</div>

    <!-- Midterste kolonne (navn, adresse, sociale medier) -->
    <div class="footer-col footer-info">
      <?php 
      $footer_name = get_field("footer_name", "option");
      $adress = get_field("adress", "option");
      ?>
      <?php if ( $footer_name ) : ?>
        <h3><?php echo esc_html($footer_name); ?></h3>
      <?php endif; ?>

      <?php if ( $adress ) : ?>
        <p><strong><?php pll_e("Adresse:") ?></strong><br>
          <?php echo nl2br( esc_html($adress) ); ?>
        </p>
      <?php endif; ?>


    <!--   <div class="footer-socials">
        <a href="<?php echo esc_url('https://instagram.com/DININSTAGRAM'); ?>" target="_blank" rel="noopener">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="<?php echo esc_url('https://facebook.com/DINFACEBOOK'); ?>" target="_blank" rel="noopener">
          <i class="fab fa-facebook-f"></i>
        </a>
      </div> -->
    </div>

    <!-- åbningstider -->
    <div class="footer-col footer-hours">
      <?php 
      $opening_hours = get_field("opening_hours", "option");
      ?>
      <?php if ( $opening_hours ) : ?>
        <p><strong><?php pll_e("Åbningstider:") ?></strong><br>
          <?php echo nl2br( esc_html($opening_hours) ); ?>
        </p>
      <?php endif; ?>
    </div>

  </div>

    <!-- Sociale Medier Ikoner -->
  <div class="footer-socials">
    <a href="<?php the_field('facebook_url', "option"); ?>" target="_blank" aria-label="Facebook">
        <i class="fab fa-facebook-f"></i>
    </a>

    <a href="<?php the_field('instagram_url', "option"); ?>" target="_blank" aria-label="Instagram">
        <i class="fab fa-instagram"></i>
    </a>

  </div>


  <!-- Copyrights -->
  <div class="footer-bottom">
    <?php 
    $copyrights = get_field("copyrights", "option");
    if ( $copyrights ) : ?>
      <p><?php echo esc_html($copyrights); ?></p>
    <?php endif; ?>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>