<?php
/**
 * Template Name: Full Width Page
 *
 */
get_header();
?>
    <div class="fullwidth-template">
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