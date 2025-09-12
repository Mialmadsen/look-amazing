<?php
$form_link = get_page_by_path('survey'); // slug must be "survey"

$link = $form_link ? get_permalink($form_link->ID) : '#';

get_template_part('template-parts/components/survey', null, [
  'icon'      => 'fa-solid fa-paperclip',
  'title'     => pll__('Vil du hjælpe os?'),
  'text'      => pll__('Vi vil gerne blive klogere på vores kunder, derfor er vi igang med at udføre en spørgeundersøgelse.'),
  'link'      => $link,
  'link_text' => pll__('Spørgeskema'),
]);
?>