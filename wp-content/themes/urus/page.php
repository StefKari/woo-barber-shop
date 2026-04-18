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
        get_template_part('template-parts/blog','heading');
        ?>
        <div class="container">
            
            <div class="row">
                <div class="<?php echo esc_attr( implode( ' ', $main_content_class ) ); ?>">
                    <?php
                    if ( have_posts() ) {
                        while ( have_posts() ) {
                            the_post();
                            ?>
                            <div class="page-main-content">
                                <?php
                                the_content();
                                wp_link_pages( array(
                                        'before'      => '<div class="page-links"><span class="screen-reader-text page-links-title">' . esc_html__( 'Pages:', 'urus' ) . '</span>',
                                        'after'       => '</div>',
                                        'link_before' => '<span>',
                                        'link_after'  => '</span>',
                                        'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'urus' ) . ' </span>%',
                                        'separator'   => '<span class="screen-reader-text">, </span>',
                                    )
                                );
                                ?>
                            </div>
                            <?php
                            // If comments are open or we have at least one comment, load up the comment template.
                            if ( comments_open() || get_comments_number() ) :
                                comments_template();
                            endif;
                            ?>
                            <?php
                        }
                    }
                    ?>
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
<?php  get_footer();?>