<?php get_header(); ?>
<div class="container">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article <?php post_class(); ?>>

            <div class="content"><?php the_content(); ?></div>
        </article>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>

