<section class="front-page-section" id="gallery">
  <?php
  // Assume the page and gallery exist (no extra checks)
  $gallery_page      = get_page_by_path('galleri-2');
  $gallery_page_url  = get_permalink($gallery_page);
  $blocks            = parse_blocks( get_post_field('post_content', $gallery_page->ID) );

  // Find first core/gallery block (recursive, minimal)
  $find_gallery = function($blocks) use (&$find_gallery) {
    foreach ($blocks as $b) {
      if (isset($b['blockName']) && $b['blockName'] === 'core/gallery') return $b;
      if (!empty($b['innerBlocks'])) {
        $g = $find_gallery($b['innerBlocks']);
        if ($g) return $g;
      }
    }
    return null;
  };
  $gallery_block = $find_gallery($blocks);

  // Collect first 3 image IDs from innerBlocks
  $ids = [];
  foreach ($gallery_block['innerBlocks'] as $ib) {
    $ids[] = (int) $ib['attrs']['id'];
  }
  $ids = array_slice(array_filter($ids), 0, 3);
  ?>

  <a class="section_heading" href="<?php echo esc_url($gallery_page_url); ?>">
    <h2>Galleri <i class="fa-solid fa-trophy"></i></h2>
    
  </a>

  <div class="gallery-grid fade-stagger">
    <?php foreach ($ids as $id): ?>
      <div class="gallery-item card">
        <a href="<?php echo esc_url($gallery_page_url); ?>">
          <?php echo wp_get_attachment_image($id, 'large', false, [
            'class'   => 'gallery-preview-img',
            'loading' => 'lazy',
          ]); ?>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>