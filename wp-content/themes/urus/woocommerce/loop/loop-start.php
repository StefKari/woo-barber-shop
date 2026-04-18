<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$woo_lg_items = Urus_Helper::get_option_c('woo_lg_items',3);
$class = array('products');
if($woo_lg_items == 15 || $woo_lg_items =='2'){
    $class[] ='col-gap-20';
}
$data_masonry='';
$woo_shop_list_style = Urus_Helper::get_option('woo_shop_list_style','grid');
if( $woo_shop_list_style =='masonry'){
    $class[] ='grid urus-masonry';
    $data_masonry ="data-settings='[{ \"itemSelector\": \".grid-item\", \"columnWidth\": \".grid-sizer\" }]'";
}else{
    $class[] ='row';
    
}
if( $woo_shop_list_style =='grid'){
    $class[] ='grid';
    $data_masonry ="";
}
?>
<div data-nb_cols="<?php echo esc_attr($woo_lg_items);?>" class="product-list-wapper" id="product-list-wapper">
    <?php do_action('urus_before_loop_product');?>
<ul <?php echo e_data($data_masonry);?> class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
    <?php
        if( $woo_shop_list_style =='masonry'){
            echo '<li class="grid-sizer"></li>';
        }
    ?>
