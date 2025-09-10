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
        
        <a href="<?php 
  $page = get_page_by_path('sustainability-initiatives'); 
  if ($page) {
    echo esc_url( get_permalink( function_exists('pll_get_post') ? pll_get_post($page->ID) : $page->ID ) ); 
  } 
?>" class="button-container">
  <?php pll_e('Læs mere'); ?><span class="arrow">→</span>
</a>
        
    </div>
</section>