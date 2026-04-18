<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */
defined( 'ABSPATH' ) || exit;
/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );
if ( post_password_required() ) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
if (Urus_Helper::is_mobile_template()) {
    wc_get_template_part( 'content', 'single-product-mobile' );
    return;
}
$class = array('product');
global $product;
$attachment_ids = $product->get_gallery_image_ids();
if( empty($attachment_ids)){
    $class[] ='no-gallery-image';
}
$woo_single_used_layout = Urus_Helper::get_option('woo_single_used_layout','vertical');
$class[] = 'single-product-layout-'.$woo_single_used_layout;

$date = Urus_Pluggable_WooCommerce::get_max_date_sale($product);
if( $date > 0){
    $class[] = 'single-has-date-sale';
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class($class); ?>>
    <?php wc_get_template_part('single-layouts/'.$woo_single_used_layout );?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
