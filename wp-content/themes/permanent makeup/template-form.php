<?php
/**
 * Template Name: Form Template
 * Template Post Type: page
 */
get_header();

// Find base page by slug and map to current Polylang language (safe)
$base    = get_page_by_path('survey'); // base slug
$page_id = ($base instanceof WP_Post)
  ? ( function_exists('pll_get_post') ? (int) pll_get_post($base->ID) : (int) $base->ID )
  : 0;

// ACF pulls (guarded)
$hero_field = $page_id ? get_field('form_hero_image',  $page_id) : null;
$h2         = $page_id ? (string) get_field('form_hero_heading', $page_id) : '';
$h1         = $page_id ? (string) get_field('hero_form_title',   $page_id) : '';
$text       = $page_id ? (string) get_field('form_text',         $page_id) : '';

$questions  = array_filter([
  $page_id ? get_field('question_1', $page_id) : '',
  $page_id ? get_field('question_2', $page_id) : '',
  $page_id ? get_field('question_3', $page_id) : '',
  $page_id ? get_field('question_4', $page_id) : '',
  $page_id ? get_field('question_5', $page_id) : '',
  $page_id ? get_field('question_6', $page_id) : '',
]);

$comment_lbl = $page_id ? (string) get_field('comment_option', $page_id) : '';

// Normalize hero bg to URL
$background_image = '';
if (is_array($hero_field)) {
  $background_image = $hero_field['url'] ?? '';
} elseif (is_string($hero_field)) {
  $background_image = $hero_field;
}

// Hero
get_template_part('template-parts/components/hero', null, [
  'background_image'  => $background_image,
  'frontpage_heading' => $h1,
]);
?>

<section class="front-page-section form-section" role="form">
  <div class="form-intro">
    <?php if ($h2): ?>
      <h2 class="section_heading"><?php echo esc_html($h2); ?></h2>
    <?php endif; ?>

    <?php if ($text): ?>
      <div class="form-text">
        <?php echo wpautop( wp_kses_post($text) ); ?>
      </div>
    <?php endif; ?>
  </div>

  <form class="survey-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST" novalidate>
    <?php
      wp_nonce_field('survey_form_nonce', 'survey_form_nonce');
    ?>
    <input type="hidden" name="action" value="sample_form">

    <!-- Top grid -->
    <div class="survey-grid">
      <div class="field">
        <label for="first_name"><?php echo esc_html( pll__('Fornavn') ); ?></label>
        <input type="text" id="first_name" name="firstname"
               placeholder="<?php echo esc_attr( pll__('Tilføj') ); ?>" required>
      </div>

      <div class="field">
        <label for="last_name"><?php echo esc_html( pll__('Efternavn') ); ?></label>
        <input type="text" id="last_name" name="lastname"
               placeholder="<?php echo esc_attr( pll__('Tilføj') ); ?>" required>
      </div>

      <div class="field">
        <label for="age"><?php echo esc_html( pll__('Alder') ); ?></label>
        <input type="number" id="age" name="age"
               placeholder="<?php echo esc_attr( pll__('Tilføj') ); ?>" min="1" max="120" required>
      </div>

      <div class="field">
        <label for="gender"><?php echo esc_html( pll__('Køn') ); ?></label>
        <select id="gender" name="gender" required>
          <option value="" selected disabled><?php echo esc_html( pll__('Vælg') ); ?></option>
          <option value="male"><?php echo esc_html( pll__('Mand') ); ?></option>
          <option value="female"><?php echo esc_html( pll__('Kvinde') ); ?></option>
          <option value="other"><?php echo esc_html( pll__('Andet') ); ?></option>
        </select>
      </div>

      <div class="field">
        <label for="city"><?php echo esc_html( pll__('By') ); ?></label>
        <input type="text" id="city" name="city"
               placeholder="<?php echo esc_attr( pll__('Tilføj') ); ?>" required>
      </div>
    </div>

    <!-- Choice section -->
    <?php
      $scale = [
        pll__('I høj grad'),
        pll__('I nogen grad'),
        pll__('Neutral'),
        pll__('I lav grad'),
        pll__('Ved ikke'),
      ];
    ?>

    <?php if (!empty($questions)) : ?>
      <fieldset class="likert">
        <legend class="sr-only"><?php echo esc_html( pll__('Spørgsmål') ); ?></legend>

        <div class="likert-row likert-head">
          <div class="likert-q"></div>
          <?php foreach ($scale as $label): ?>
            <div class="likert-col"><?php echo esc_html($label); ?></div>
          <?php endforeach; ?>
        </div>

        <?php foreach ($questions as $i => $qtext): $idx = $i + 1; ?>
          <div class="likert-row">
            <label class="likert-q" for="<?php echo esc_attr("q{$idx}_1"); ?>">
              <?php echo esc_html($qtext); ?>
            </label>

            <?php foreach ($scale as $s_i => $label): $s = $s_i + 1; ?>
              <div class="likert-col">
                <input
                  type="radio"
                  id="<?php echo esc_attr("q{$idx}_{$s}"); ?>"
                  name="<?php echo esc_attr("q{$idx}"); ?>"
                  value="<?php echo esc_attr($label); ?>"
                  required
                >
                <span class="square"></span>
                <label for="<?php echo esc_attr("q{$idx}_{$s}"); ?>" class="sr-only">
                  <?php echo esc_html($label); ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </fieldset>
    <?php endif; ?>

    <!-- Comment -->
    <?php if ($comment_lbl): ?>
      <div class="field long">
        <label for="comment"><?php echo esc_html($comment_lbl); ?></label>
        <textarea id="comment" name="comment" rows="4"
                  placeholder="<?php echo esc_attr( pll__('Tilføj') ); ?>"></textarea>
      </div>
    <?php endif; ?>

    <!-- Terms -->
    <div class="field terms">
      <label>
        <input type="checkbox" name="terms" required>
        <span><?php echo esc_html( pll__('Accepter terms og conditions ved booking*') ); ?></span>
      </label>
    </div>

    <button type="submit" id="submit"><?php echo esc_html( pll__('Send') ); ?></button>
  </form>
</section>

<?php get_footer(); ?>