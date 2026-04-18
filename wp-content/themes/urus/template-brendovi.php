<?php /*

 * Template Name: Brendovi

 */

get_header(); ?>

<h1>TEST</h1>




<div class="container">


<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>






<?php the_content(); ?>



<?php endwhile; else : ?>
    <p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>

</div>


 <?php get_footer(); ?>