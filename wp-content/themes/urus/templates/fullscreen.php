<?php
    /**
     * Template Name: FullScreen Page
     *
     */
    get_header();
?>
    <div class="fullscreen-template" id="fullscreen-template">
        <?php
            // Start the loop.
            while ( have_posts() ) : the_post();
                ?>
                <?php the_content( );?>
            <?php
                // End the loop.
            endwhile;
        ?>
        <?php
        if( did_action( 'elementor/loaded' )){
            //do nothing
        }else{
            ?>
            <div class="section fp-auto-height" >
                <?php do_action('urus_fullscreen_footer');?>
            </div>
        <?php
        }
        ?>

    </div>
<?php
get_footer();
