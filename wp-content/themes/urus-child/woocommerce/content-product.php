<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
$product_item_class = array('product-item');
$woo_lg_items = Urus_Helper::get_option_c('woo_lg_items',3);
$woo_md_items = Urus_Helper::get_option('woo_md_items',3);
$woo_sm_items = Urus_Helper::get_option('woo_sm_items',4);
$woo_xs_items = Urus_Helper::get_option('woo_xs_items',6);
$woo_ts_items = Urus_Helper::get_option('woo_ts_items',6);
$woo_shop_list_style = Urus_Helper::get_option('woo_shop_list_style','grid');
$woo_product_item_layout= Urus_Helper::get_option('woo_product_item_layout','classic');
$woo_shop_layout= Urus_Helper::get_option('woo_shop_layout','left');

$mobile_product_item_style = Urus_Helper::get_option('mobile_product_item_style', 'default');
$mobile_enable = Urus_Helper::is_mobile_template();
if ($mobile_enable) {
    $woo_shop_list_style = 'grid';
}
if($woo_shop_list_style =='grid'){
    $product_item_class [] = $woo_product_item_layout;
    $product_item_class [] ='col-lg-'.$woo_lg_items;
    $product_item_class [] ='col-md-'.$woo_md_items;
    $product_item_class [] ='col-sm-'.$woo_sm_items;
    $product_item_class [] ='col-xs-'.$woo_xs_items;
    $product_item_class [] ='col-'.$woo_ts_items;
    $product_item_class [] ='rows-space-30';
    $product_item_class [] ='col-gap-default';
    if ($mobile_enable){
        $product_item_class [] ='mobile-template '.$mobile_product_item_style ;
    }

}elseif ($woo_shop_list_style =='masonry'){
    $product_item_class[]='grid-item';
    $product_item_class [] = $woo_product_item_layout;
    $index = $wp_query->current_post +1;
    if( in_array($index,array(1,4,7,10,14,16,20,22))){
        $product_item_class[]='grid-item--width2x';
        $product_item_class[] ='index-'.$index;
    }
}else{
    $product_item_class[] ='list';
    if( $woo_shop_layout == 'full'){
        $product_item_class[] = 'col-lg-6 col-md-12 col-sm-12';
    }else{
        $product_item_class[] ='col-sm-12';
    }
}
$product_item_class = apply_filters('urus_product_item_class',$product_item_class);


?>
<li <?php post_class($product_item_class); ?>>
    <?php if( $woo_shop_list_style == "grid" || $woo_shop_list_style == "masonry"):?>
        <?php if ($mobile_enable) {
            if ($mobile_product_item_style != 'default') {
                wc_get_template_part('product-styles/content-product-mobile', $mobile_product_item_style );    
            }else{
                wc_get_template_part('product-styles/content-product', $woo_product_item_layout );    
            }
        }else{
            wc_get_template_part('product-styles/content-product', $woo_product_item_layout );
        }?>
    <?php else:?>
        <?php wc_get_template_part('content-product', 'list' );?>
    <?php endif;?>

</li>
