<?php
/* Template Name: Blog */ ?>

<?php  get_header();?>
<?php
$page_layout = Urus_Helper::get_option('blog_layout','left');
$page_used_sidebar = Urus_Helper::get_option('blog_used_sidebar','widget-area');
if (is_page()){
	$enable_page_option = Urus_Helper::get_post_meta(get_the_ID(),'enable_page_option',false);
	if ($enable_page_option) {
		$page_layout = Urus_Helper::get_post_meta( get_the_ID(), 'page_layout', 'left' );
		$page_used_sidebar = Urus_Helper::get_post_meta(get_the_ID(),'page_used_sidebar','widget-area');
	}
}

if ( !is_active_sidebar( $page_used_sidebar ) ){
	$page_layout ='full';
}
/*Main container class*/
$main_container_class   = array('main-container');
if ( $page_layout == 'full' ) {
	$main_container_class[] = 'no-sidebar';
} else {
	$main_container_class[] = $page_layout . '-sidebar';
}
$main_content_class   = array('main-content');

if ( $page_layout == 'full' ) {
	$main_content_class[] = 'col-12';
} else {
	$main_content_class[] = 'col-12 col-lg-9';
}
$sidebar_class   = array('sidebar');
if ( $page_layout != 'full' ) {
	$sidebar_class[] = 'col-12 col-lg-3';
}
$main_container_class = apply_filters('urus_page_main_container_class',$main_container_class);
$main_content_class = apply_filters('urus_page_main_container_class',$main_content_class);
$sidebar_class = apply_filters('urus_page_sidebar_class',$sidebar_class);

?>
<?php do_action( 'urus_before_page_content_wrapper' ); ?>
<div class="<?php echo esc_attr( implode( ' ', $main_container_class ) ); ?>">
	<?php
        /*if (function_exists('wcfmmp_is_stores_list_page') && wcfmmp_is_stores_list_page()) {
            Urus_Pluggable_WooCommerce::woocommerce_shop_heading();
        } else {
            get_template_part('template-parts/blog','heading');
        }*/
      //  get_template_part('template-parts/blog','heading');
        ?>
        <div class="container blog_desktop">

        	<div class="row">
        		<div class="<?php echo esc_attr( implode( ' ', $main_content_class ) ); ?>">



        			<div class="article-content">
        				<div class="masonry__grid row auto-clear">



                            <?php
                            $args=array(
                               'post_type' => 'blog',
                               'orderby' => 'date',
                               'order' => 'DESC',
                               'paged' => get_query_var('paged')
                           );
                            
                            
                            $wp_query = new WP_Query($args);
                            if ( have_posts() ) : while ( have_posts() ) : the_post();

					// $date=get_field('news_date',$post->ID);
					// $link=get_permalink($post->ID);

					// $title=__(get_field('news_caption',$post->ID));
					// $content=__(get_field('news_lead_text',$post->ID));

                               ?>


                               <article id="post-10400" class="post-item col-12 post-10400 post type-post status-publish format-standard has-post-thumbnail hentry category-brada">
                                  <div class="post-inner">
                                     <div class="post-thumb">
                                        <a href="<?php echo get_permalink(); ?>">

                                           <figure class="">

                                              <!-- <img width="1000" height="500" src="https://www.barbers.rs/wp-content/uploads/2021/01/xmitovi-o-bradi_1.jpg.pagespeed.ic.atoUz7S4Ne.webp" class="img-responsive wp-post-image attachment-1000x500 size-1000x500" alt="" > -->
                                              <?php
if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
    the_post_thumbnail( 'full' );
}
?>


</figure></a>
</div>
<div class="info">
    <div class="post-item-head">
       <div class="post-categories">
          <!-- <a href="https://www.barbers.rs/category/brada/" rel="category tag">Brada</a> -->                </div>
          <h3 class="post-title"><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h3>
      </div>
      <div class="excerpt">

          <?php the_excerpt(); ?>
      </div>
        								<!-- 	<div class="metas">
        										<span class="author">Od :<span class="name">smartweb</span></span>
        										<span class="date">22 januara, 2021</span>
        										<span class="comment-count">
        										0 Komentara            </span>
        									</div> -->
        									<div class="post-footer">
        										<a class="readmore" href="<?php echo get_permalink(); ?>"><span class="text">Pročitaj više</span><i class="arrow urus-icon-arrow-right"></i></a>

        									</div>
        								</div>
        							</div>
        						</article>



                           <?php endwhile; else: ?>
                           <p>Nema podataka</p> 
                       <?php endif;?>

                   </div>
               </div>



           </div>
           <?php if ( $page_layout != "full" ): ?>
               <div class="<?php echo esc_attr( implode( ' ', $sidebar_class ) ); ?>">
                  <?php get_sidebar(); ?>
              </div>
          <?php endif; ?>
      </div>
  </div>
</div>
<?php do_action( 'urus_after_page_content_wrapper' ); ?>

<style type="text/css">


@media(min-width: 1100px) {

    .blog_desktop{
        margin-top: 70px;

    }
}
</style>
<?php  get_footer();?>