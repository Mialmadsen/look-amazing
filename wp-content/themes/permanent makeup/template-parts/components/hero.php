<?php
/**
 * Hero Section Component
 * Expects $args = ['background_image' => (string URL), 'heading' => (string)]
 */
$bg  = $args['background_image'] ?? '';
$h1  = $args['heading'] ?? '';
?>

<?php if ($bg) : ?>
<section class="hero-section" style="background-image: url('<?php echo esc_url($bg); ?>');">
  <div class="hero-content">
    <?php if ($h1) : ?>
      <h1><?php echo esc_html($h1); ?></h1>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>