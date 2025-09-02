<?php

/**
 * Article Card Component
 * Expected $args from get_template_part:
 *  image, heading, text, link, date, author, categories, tags
 */
$image      = $args['image']      ?? '';
$heading    = $args['heading']    ?? '';
$text       = $args['text']       ?? '';
$link       = $args['link']       ?? '';
$date       = $args['date']       ?? '';
$author     = $args['author']     ?? '';
$categories = $args['categories'] ?? [];
$tags       = $args['tags']       ?? [];
?>

<?php if (!empty($link)) : ?>
<a href="<?php echo esc_url($link); ?>" class="article-card card">
    
    <?php if (!empty($image)) : ?>
    <div class="card-image-wrapper">
        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($heading); ?>">
    </div>
    <?php endif; ?>

    <div class="card-text">
        <?php if (!empty($heading)) : ?>
        <h3 class="card-title"><?php echo esc_html($heading); ?></h3>
        <?php endif; ?>

        <div class="card-meta">
            <?php if (!empty($author)) : ?>
                <span class="card-author"><?php echo esc_html($author); ?></span>
            <?php endif; ?>
            <?php if (!empty($date)) : ?>
                <span class="card-date"><?php echo esc_html($date); ?></span>
            <?php endif; ?>
        </div>

        <?php if (!empty($categories)) : ?>
        <div class="card-categories">
            <?php foreach ($categories as $category) : ?>
                <span class="card-category"><?php echo esc_html($category->name); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($tags)) : ?>
        <div class="card-tags">
            <?php foreach ($tags as $tag) : ?>
                <span class="card-tag">#<?php echo esc_html($tag->name); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($text)) : ?>
        <p class="card-excerpt"><?php echo wp_trim_words(wp_kses_post($text), 10, '...'); ?></p>
        <?php endif; ?>
    </div>
</a>
<?php endif; ?>