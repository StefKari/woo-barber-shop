<?php
/**
 * Created by Familab.
 * User: Familab
 * Date: 07/03/2019
 * Time: 9:54 AM
 */

add_action('woocommerce_before_single_product_summary',array('Urus_Pluggable_WooCommerce','urus_show_product_video'),10);
add_action('woocommerce_before_single_product_summary',array('Urus_Pluggable_WooCommerce','urus_show_product_360deg'),10);

add_filter('woocommerce_product_additional_information_heading',array('Urus_Pluggable_WooCommerce','hiden_tab_content_title'));
add_filter('woocommerce_product_description_heading',array('Urus_Pluggable_WooCommerce','hiden_tab_content_title'));
remove_action('woocommerce_before_single_product_summary','woocommerce_show_product_sale_flash',10);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 4 );

add_action('urus_single_top_bar',array('Urus_Pluggable_WooCommerce','woocommerce_breadcrumb'),1);
add_action('urus_single_top_bar',array('Urus_Pluggable_WooCommerce','single_nav'),10);

add_action('woocommerce_share',array('Urus_Pluggable_WooCommerce','woocommerce_share'),10);

remove_action('woocommerce_before_single_product_summary','woocommerce_show_product_images',20);

add_action('woocommerce_before_single_product_summary',array('Urus_Pluggable_WooCommerce','woocommerce_show_product_centered_slider_thumbnails'),20);

add_filter('woocommerce_output_related_products_args',array('Urus_Pluggable_WooCommerce','woocommerce_output_related_products_args'),10,1);
if( Urus_Mobile_Detect::isMobile()){
    remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs',10);
    add_action('woocommerce_after_single_product_summary',array('Urus_Pluggable_WooCommerce','single_mobile_tabs'),10);
}

add_action('woocommerce_single_product_summary',array('Urus_Pluggable_WooCommerce','urus_show_product_promo'),12);
add_action('woocommerce_single_product_summary',array('Urus_Pluggable_WooCommerce','single_product_coundown'),13);
add_action('woocommerce_single_product_summary',array('Urus_Brand','display_product_brand_list'),14);


add_action( 'wp_ajax_urus_add_cart_single_ajax', array('Urus_Pluggable_WooCommerce','add_cart_single_ajax') );
add_action( 'wp_ajax_nopriv_urus_add_cart_single_ajax', array('Urus_Pluggable_WooCommerce','add_cart_single_ajax') );


