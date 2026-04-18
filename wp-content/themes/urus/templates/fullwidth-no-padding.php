<?php
    /**
     * Template Name: Full Width Page No Padding
     *
     */
    get_header();
?>
    <div class="fullwidth-template-no-padding">
        <div class="container-full">
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