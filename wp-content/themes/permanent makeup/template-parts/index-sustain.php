<?php
// build translated permalink like you did
$slugs = ['sustainability-initiatives', 'sustainable-initiatives-eng'];
$base = null;
foreach ($slugs as $slug) {
  if ($p = get_page_by_path($slug)) { $base = $p; break; }
}
$id = $base ? $base->ID : 0;
if ($id && function_exists('pll_get_post')) {
  $id = pll_get_post($id, pll_current_language()) ?: $id;
}
$link = $id ? get_permalink($id) : home_url('/');

get_template_part('template-parts/components/survey', null, [
  'icon'  => 'fa-solid fa-earth-americas',
  'icon_extra_class' => 'animate-earth',
  'title' => pll__('Vores Sustainability Initiatives'),
  'text'  => pll__('I vores klinik har vi en bevidst tilgang til bæredygtighed, og vi arbejder aktivt på at gøre vores forbrug mere miljøvenligt.'),
  'link'  => $link,
  'link_text' => pll__('Læs mere'),
]);