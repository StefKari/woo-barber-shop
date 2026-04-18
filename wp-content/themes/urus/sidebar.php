<?php
$class = array('sidebar-area');
$blog_used_sidebar = Urus_Helper::get_option( 'blog_used_sidebar', 'widget-area' );
if( is_single()){
    $blog_used_sidebar = Urus_Helper::get_option( 'single_blog_used_sidebar', 'widget-area' );

}
if( is_page()){
    $blog_used_sidebar = Urus_Helper::get_post_meta(get_the_ID(),'page_used_sidebar','widget-area');
    $class[] ='page-sidebar';
}
if( class_exists('WooCommerce')){
    if( is_shop() || is_product_category() || is_tax('product-brand')){
        $blog_used_sidebar = Urus_Helper::get_option('woo_shop_used_sidebar','shop-widget-area');
        $class[] ='shop-sidebar';
    }
    if( is_product()){
        $blog_used_sidebar = Urus_Helper::get_option('woo_single_used_sidebar','shop-widget-area');
        $class[] ='shop-sidebar';
    }
}

$sidebar_layout = Urus_Helper::get_option('sidebar_layout','default');

$class[] = 'sidebar-layout-'.$sidebar_layout;
$class = apply_filters('urus_sidebar_class',$class);
?>
<?php if ( is_active_sidebar( $blog_used_sidebar ) ) : ?>
    <div class="sidebar-head">
        <span class="text"><?php esc_html_e('Sidebar','urus');?></span>
        <a href="#" class="close-block-sidebar"><?php esc_html_e('Close','urus');?></a>
    </div>
    <div id="widget-area" class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">

        <div class="sidebar__inner">
            <?php dynamic_sidebar( $blog_used_sidebar ); ?>
        </div>

    </div><!-- .widget-area -->
<?php endif; ?>
