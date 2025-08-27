<?php get_header(); ?>

 <?php get_template_part('template-parts/index', 'testimonials'); ?>
 <p>Hello world</p>
<a href="<?php echo get_permalink( get_option('page_for_posts') ); ?>" class="btn">
    View All Posts
</a>









<?php get_footer(); ?>