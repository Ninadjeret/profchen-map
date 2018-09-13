<?php
/*
 * Template Name: Content
 */
get_header();
?>
<div class="page-content container mt-60 mb-60">
    <?php while( have_posts() ) { the_post(); ?>
        <?php the_content(); ?>
    <?php } ?>
</div>
<?php
get_footer();
?>