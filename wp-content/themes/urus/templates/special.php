<?php
/**
 * Template Name: Special
 *
 */
get_header();
?>
<div class="special-fullwidth-template">
    <div class="container-wapper">
        <?php
        // Start the loop.
        while ( have_posts() ) : the_post();
            ?>
            <?php the_content( );?>
        <?php
            // End the loop.
        endwhile;
        ?>
    </div>
</div>
<?php
get_footer();