<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
    <h2 class="comments-title"><?php _e('Kommentarer', 'permanent-makeup'); ?></h2>

    <ul class="commentlist">
        <?php wp_list_comments(); ?>
    </ul>

    <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
  if ( comments_open() ) {
    comment_form([
      'title_reply'       => __('Skriv en kommentar', 'permanent-makeup'),
      'title_reply_to'    => __('Skriv et svar til %s', 'permanent-makeup'),
      'label_submit'      => __('Send kommentar', 'permanent-makeup'),
      'cancel_reply_link' => __('Annuller svar', 'permanent-makeup'),
    ]);
  } else {
    echo '<p class="no-comments">'. __('Kommentarer er lukket.', 'permanent-makeup') .'</p>';
  }
  ?>
</div>