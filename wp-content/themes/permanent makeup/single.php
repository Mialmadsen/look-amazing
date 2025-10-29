<?php get_header(); ?>
<?php // Render the hero component 
$image = get_the_post_thumbnail_url(); // Get the featured image of the post
get_template_part('template-parts/components/hero', null, [
  'background_image' => $image,
  
]);
?>
<div class="blog-wrapper">
    <?php if(have_posts()): ?>
    <?php while(have_posts()): the_post() ?>
    <?php
                
                $title = get_the_title();
                $date = get_the_date();       // Get the date the post was written
                $author = get_the_author();   // Get the name of the user who wrote the post
                $content = get_the_content(); // Get the full blog content
                $categories = get_the_category();
                
                $tags = get_the_tags();       // Get the tags associated with the post
                
?>


    <!-- Blog Post -->


    <div class="blog-post">
        <div class="post-header">
            <h2 class="post-title"><?php echo $title; ?></h2>
            <p class="post-meta">
                <small>Written by <span class="post-author"><?php echo $author; ?></span>
                    on <span class="post-date"><?php echo $date; ?></span></small>
            </p>
        </div>
        <div class="post-content">

            <?php echo $content; ?>
        </div>



        <div class="post-taxonomies">
            <?php if($categories): ?>
            <div class="post-categories">
                <strong>Categories:</strong>
                <?php foreach($categories as $category): ?>
                <a class="category-link" href="<?php echo get_category_link($category->term_id); ?>">
                    <?php echo $category->name; ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if($tags): ?>
            <div class="post-tags">
                <strong>Tags:</strong>
                <?php foreach($tags as $tag): ?>
                <a class="tag-link" href="<?php echo get_tag_link($tag->term_id); ?>">
                    <?php echo $tag->name; ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="post-comments">
            <?php if(comments_open() || get_comments_number()): ?>
            <?php comments_template(); ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- End Blog Post -->

    <?php endwhile; ?>
    <?php endif; ?>
</div>
<?php get_template_part('template-parts/components/read-more'); ?>
<?php get_footer(); ?>