<?php get_header(); ?>
<div class="container">
    <div class="content">
        <?php if(have_posts()): ?>
            <?php while(have_posts()): the_post() ?>
                <?php
                    $url = get_permalink();
                    $title = get_the_title();
                    $date = get_the_date();       // Get the date the post was written
                    $author = get_the_author();   // Get the name of the user who wrote the post
                    $excerpt = get_the_excerpt(); // Get the first 55 words of the blog entry
                ?>
                
                <!-- HTML to print one blog entry preview -->
                <div class="blog-preview">
                    <h2><a href="<?php echo $url; ?>"><?php echo $title; ?></a></h2>
                    <p><small>Written by <?php echo $author; ?> on <?php echo $date; ?></small></p>
                    <p><?php echo $excerpt; ?></p>
                </div>
                
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>

