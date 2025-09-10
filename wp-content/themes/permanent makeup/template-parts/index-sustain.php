<section class="survey">
    <div class="survey-content">
        
        <div class="survey-cta"> 
            <div class="clip-icon">
            <!-- SVG-ikon for kuvert --><i class="fa-solid fa-sun" style="color: #E1D8C6;"></i>
        </div>
            <div class="text">
                <h2><?php pll_e("Vores Sustainability Initiatives") ?></h2>
                <p> <?php pll_e("I vores klinik har vi en bevidst tilgang til bæredygtighed, og vi arbejder aktivt på at gøre vores forbrug mere miljøvenligt.") ?></p>
            </div>
            
           
        </div>
        
        <a
  href="<?php
    // Try both slugs (EN first, then DA — change to your real slugs)
    $slugs = ['sustainability-initiatives', 'sustainable-initiatives-eng'];

    $base = null;
    foreach ($slugs as $slug) {
      if ($p = get_page_by_path($slug)) { $base = $p; break; }
    }

    // Map to current language (if translation exists)
    $id = $base ? $base->ID : 0;
    if ($id && function_exists('pll_get_post')) {
      $id = pll_get_post($id, pll_current_language()) ?: $id;
    }

    echo esc_url( $id ? get_permalink($id) : home_url('/') );
  ?>"
  class="button-container"
>
  <?php pll_e('Læs mere'); ?><span class="arrow">→</span>
</a>
        
    </div>
</section>