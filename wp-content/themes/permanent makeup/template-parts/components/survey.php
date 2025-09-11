<?php
/**
 * Survey Section Component
 *
 * Expects $args = [
 *   'icon'  => 'fa-solid fa-earth-americas',  // FontAwesome icon class
 *   'icon_extra_class' => '',                 // optional animation class
 *   'title' => 'Your title',
 *   'text'  => 'Your text',
 *   'link'  => '#',                           // URL (can be external or WP)
 *   'link_text' => 'Læs mere',                // Button label
 * ]
 */
$icon  = $args['icon'] ?? 'fa-solid fa-circle';
$icon_extra = $args['icon_extra_class'] ?? '';
$title = $args['title'] ?? '';
$text  = $args['text'] ?? '';
$link  = $args['link'] ?? '#';
$link_text = $args['link_text'] ?? '';
?>

<section class="survey">
  <div class="survey-content">
    <div class="survey-cta">
      <div class="clip-icon">
        <i class="<?php echo esc_attr($icon . ' ' . $icon_extra); ?>" style="color: #E1D8C6;"></i>
      </div>
      <div class="text">
        <?php if ($title): ?><h2><?php echo esc_html($title); ?></h2><?php endif; ?>
        <?php if ($text): ?><p><?php echo esc_html($text); ?></p><?php endif; ?>
      </div>
    </div>

    <?php if ($link && $link_text): ?>
      <a href="<?php echo esc_url($link); ?>" class="button-container">
        <?php echo esc_html($link_text); ?> <span class="arrow">→</span>
      </a>
    <?php endif; ?>
  </div>
</section>