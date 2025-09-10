



<?php 
/**
 * Template Name: Gallery Template
 * Template Post Type: page
 */
get_header(); ?>

<div class='mat'></div>

<?php if(have_posts()): ?>
<?php while(have_posts()): the_post() ?>
<div class="gallery-grid_page fade-stagger">
    
    <?php the_content(); // Gutenberg content (Gallery block) ?>
        
</div>






<?php endwhile ?>
<?php endif ?>


<?php get_footer(); ?>