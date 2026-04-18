<?php
/**
 * Created by Familab.
 * User: Familab
 * Date: 07/03/2019
 * Time: 9:54 AM
 */

add_action('woocommerce_before_single_product_summary',array('Urus_Pluggable_WooCommerce','urus_show_product_video'),10);
add_action('woocommerce_before_single_product_summary',array('Urus_Pluggable_WooCommerce','urus_show_product_360deg'),10);
//Add class to main container
add_filter('urus_shop_main_container_class', array('Urus_Pluggable_WooCommerce','product_mobile_main_container_class'), 10);
//Change product thumbnails option
remove_action( 'woocommerce_shop_heding', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs',10);
add_action('woocommerce_after_single_product_summary',array('Urus_Pluggable_WooCommerce','single_mobile_tabs'),10);
add_action('woocommerce_single_product_summary',array('Urus_Pluggable_WooCommerce','urus_show_product_promo'),12);
add_action('woocommerce_single_product_summary',array('Urus_Pluggable_WooCommerce','single_product_coundown'),13);
add_action('woocommerce_single_product_summary',array('Urus_Brand','display_product_brand_list'),14);
add_action( 'wp_ajax_urus_add_cart_single_ajax', array('Urus_Pluggable_WooCommerce','add_cart_single_ajax') );
add_action( 'wp_ajax_nopriv_urus_add_cart_single_ajax', array('Urus_Pluggable_WooCommerce','add_cart_single_ajax') );
$mobile_single_show_expert = (bool) Urus_Helper::get_option('mobile_single_show_expert',0);
if ($mobile_single_show_expert){
    add_action( 'woocommerce_single_product_summary','woocommerce_template_single_excerpt', 20 );
}
add_action('woocommerce_share',array('Urus_Pluggable_WooCommerce','woocommerce_share'),10);
