<?php
    /**
     * Template Name: Full Width Normal
     *
     */
    get_header();
?>
    <div class="fullwidth-normal-template">
        <div class="container">
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