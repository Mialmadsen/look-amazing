<?php get_header(); ?>
<?php if(have_posts()): ?>
    <?php while(have_posts()): the_post() ?>
        <?php
            $title = get_the_title();
            $date = get_the_date();       // Get the date the post was written
            $author = get_the_author();   // Get the name of the user who wrote the post
            $content = get_the_content(); // Get the full blog content
        ?>

        <!-- HTML to print one blog entry preview -->
        <div class="blog-post">
            <h2><?php echo $title; ?></h2>
            <p><small>Written by <?php echo $author; ?> on <?php echo $date; ?></small></p>
            <div class="post-content">
                <?php echo $content; ?>
            </div>
        </div>

    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>