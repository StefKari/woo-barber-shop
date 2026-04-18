<?php get_header();?>
<?php
if (Urus_Helper::is_mobile_template() && (is_shop() || is_product_category())){
    wc_get_template_part('shop','mobile');
    return false;
}

$woo_shop_layout = apply_filters('woo_sidebar_option_layout','left');
$woo_shop_width = Urus_Helper::get_option('woo_shop_width','full');

$main_container_class = array('main-container shop-page');
$main_content_class   = array('main-content');
$sidebar_class   = array('sidebar urus-block-filter-wapper');
$main_container_inner_class = array('container__inner');
if( $woo_shop_layout == 'full'){
    $main_container_class[] = 'no-sidebar';
    $sidebar_class[] = '';
}else{
    $main_container_class[] = $woo_shop_layout.'-sidebar';
}
if( is_product()){
    $main_container_inner_class[] ='container-wapper';
}else{
    $main_container_inner_class[] ='container';
}

if ( $woo_shop_layout == 'full' ) {
    $main_content_class[] = 'col-lg-12 col-md-12 col-sm-12';
} else {
    $main_content_class[] = 'col-lg-9 col-md-12 col-sm-12';
}
if ( $woo_shop_layout != 'full' ) {
    $sidebar_class[] = 'col-lg-3 col-md-12 col-sm-12';
}
$main_container_class = apply_filters('urus_shop_main_container_class',$main_container_class);
$main_content_class = apply_filters('urus_shop_main_content_class',$main_content_class);
$sidebar_class = apply_filters('urus_shop_sidebar_class',$sidebar_class);
if (is_product()){
    $single_layout = Urus_Helper::get_option('woo_single_used_layout','default');
    $main_container_class[] = 'woo_single_layout_'.$single_layout;
}
$main_container_class[] ='urus-shop-page-content';

$shop_layout = Urus_Helper::get_option('shop_layout','simple');
$main_container_class[] = $shop_layout;
$shop_heading_style = Urus_Helper::get_option('shop_heading_style','banner');
if( $shop_layout =='modern' && $shop_heading_style == 'banner' && !is_product() ){
    $main_container_class[] ='shop-layout-background-modern';
}
?>
<div id="shop-page-wapper" class="<?php echo esc_attr( implode( ' ', $main_container_class ) ); ?>">
    <?php
    /**
     * Hook: woocommerce_before_main_content.
     *
     * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
     * @hooked Urus_Pluggable_WooCommerce::woocommerce_breadcrumb - 20
     * @hooked WC_Structured_Data::generate_website_data() - 30
     */
    do_action( 'woocommerce_before_main_content' );
    ?>
    <div class="<?php echo esc_attr( implode( ' ', $main_container_inner_class ) ); ?>">
        <?php do_action('urus_woocommerce_before_main_content');?>
        <?php if( is_product()):?>
        <div class="urus_single_top_bar">
            <?php do_action( 'urus_single_top_bar' );?>
        </div>
        <?php endif;?>
        <div class="urus-content-inner row">
            <div class="<?php echo esc_attr( implode( ' ', $main_content_class ) ); ?>">
                <?php  do_action( 'urus_before_shop_main_content' );?>
                <?php woocommerce_content(); ?>
                <?php  do_action( 'urus_after_shop_main_content' );?>
            </div>
            <?php if ( $woo_shop_layout != "full" ): ?>
                <div class="<?php echo esc_attr( implode( ' ', $sidebar_class ) ); ?> ">
                    <?php get_sidebar(); ?>
                </div>
                <a href="#." class="mobile-sidebar-overlay"></a>
            <?php endif; ?>
        </div>
        <?php do_action('urus_woocommerce_after_main_content');?>
    </div>
    <?php
    /**
     * Hook: woocommerce_after_main_content.
     *
     * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
     */
    do_action( 'woocommerce_after_main_content' );
    ?>
</div>

<?php get_footer();?>
