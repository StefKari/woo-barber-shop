<?php
$show_sidebar_icon = false;
$layout = Urus_Helper::get_option( 'blog_layout', 'left' );
$blog_used_sidebar = Urus_Helper::get_option( 'blog_used_sidebar', 'widget-area' );
if( is_single()){
    $blog_used_sidebar = Urus_Helper::get_option( 'single_blog_used_sidebar', 'widget-area' );
    $layout = Urus_Helper::get_option( 'single_blog_layout', 'left' );
}
if( is_page()){
    $enable_page_option = Urus_Helper::get_post_meta(get_the_ID(),'enable_page_option',false);
    if ($enable_page_option){
    $blog_used_sidebar = Urus_Helper::get_post_meta(get_the_ID(),'page_used_sidebar','widget-area');
    $layout = Urus_Helper::get_post_meta( get_the_ID(), 'page_layout', 'left' );
    $page_template = Urus_Helper::get_post_meta(get_the_ID(),'page_used_sidebar','default');
    }
}
if( class_exists('WooCommerce')){
    if( is_shop() || is_product_category()) {
        $blog_used_sidebar = Urus_Helper::get_option('woo_used_sidebar', 'shop-widget-area');
        $layout = Urus_Helper::get_option('woo_shop_layout', 'left');
    }
    if( is_product()){
        $blog_used_sidebar = Urus_Helper::get_option('woo_single_used_sidebar','shop-widget-area');
        $layout = Urus_Helper::get_option('woo_single_layout','left');
    }
}

if( is_active_sidebar($blog_used_sidebar) && $layout!='full'){
    $show_sidebar_icon = true;
}
if( is_page() && $page_template !='default'){
        $show_sidebar_icon = false;
}


?>

<div class="mobile-nav">
    <div class="wapper">
        <div class="item item-home">
            <a href="<?php echo esc_url( get_home_url('/') )?>">
                <span class="icon urus-icon urus-icon-home"></span>
                <?php esc_html_e('Home','urus');?>
            </a>
        </div>
        <?php if( $show_sidebar_icon ):?>
        <div class="item item-sidebar">
            <a class="sidebar-toggle" href="#">
                <span class="icon urus-icon urus-icon-bar2"></span>
                <?php esc_html_e('Sidebar','urus');?>
            </a>
        </div>
        <?php endif;?>
        <?php if( class_exists('YITH_WCWL')):?>
            <?php $wishlist_link = get_permalink( get_option('yith_wcwl_wishlist_page_id') );?>
            <div class="item item-wishlist">
                <a class="" href="<?php echo esc_url($wishlist_link);?>">
                    <span class="icon urus-icon urus-icon-heart"></span>
                    <?php esc_html_e('Wishlist','urus');?>
                </a>
            </div>
        <?php endif;?>
        <?php if(class_exists('WooCommerce')):?>
            <?php $myaccount_link = get_permalink( get_option('woocommerce_myaccount_page_id') );?>
            <div class="item account">
                <a class="" href="<?php echo esc_url($myaccount_link);?>">
                    <span class="icon urus-icon urus-icon-user"></span>
                    <?php esc_html_e('Account','urus');?>
                </a>
            </div>
        <?php endif;?>
    </div>
</div>