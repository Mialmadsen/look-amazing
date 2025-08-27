<?php get_header(); ?>
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

        <!-- HTML to print one blog entry preview -->
        <div class="blog-post">
            <h2><?php echo $title; ?></h2>
            <p><small>Written by <?php echo $author; ?> on <?php echo $date; ?></small></p>
            <div class="post-content">
                <?php echo $content; ?>
            </div>
            <?php if($categories): ?>
            <?php foreach($categories as $category): ?>
                <a href="<?php echo get_category_link($category->term_id); ?>">
                    <?php echo $category->name; ?>
                </a>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php if($tags): ?>
            <?php foreach($tags as $tag): ?>
                <a href="<?php echo get_tag_link($tag->term_id); ?>">
                    <?php echo $tag->name; ?>
                </a>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if(comments_open() || get_comments_number()): ?>
            <?php comments_template(); ?>
        <?php endif; ?>

    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>