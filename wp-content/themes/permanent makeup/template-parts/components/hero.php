<?php
/**
 * Hero Section Component
 * Expects $args = ['background_image' => (string URL), 'heading' => (string)]
 */
$bg  = $args['background_image'] ?? '';
$h2  = $args['heading'] ?? '';
$h1  = $args['frontpage_heading'] ?? '';
$h3  = $args['frontpage_subheading'] ?? '';
?>

<?php if ($bg) : ?>
<div class="hero-section" style="background-image: url('<?php echo esc_url($bg); ?>');">
  <div class="hero-text">
    <?php if ($h1) : ?>
          <h1 class='hero-title'><?php echo esc_html($h1); ?></h1>
    <?php endif; ?>
    <?php if ($h3) : ?>
          <h3 class="hero-subtitle"><?php echo esc_html($h3); ?></h3>
    <?php endif; ?>

    <a class="hero-cta" href="">
      <button class="hero-btn"><?php pll_e("Book tid") ?><span class="arrow">→</span></button>
    </a>
  </div>
    

</div>  
<div class="hero-content">
    <?php if ($h2) : ?>
      <h2><?php echo esc_html($h2); ?></h2>
    <?php endif; ?>
</div>
<?php endif; ?>