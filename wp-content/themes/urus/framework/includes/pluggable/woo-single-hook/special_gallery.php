<?php
/**
 * Created by Familab.
 * User: Familab
 * Date: 07/03/2019
 * Time: 9:54 AM
 */

add_action('urus_single_top_bar',array('Urus_Pluggable_WooCommerce','woocommerce_breadcrumb'),1);
add_action('urus_single_top_bar',array('Urus_Pluggable_WooCommerce','single_nav'),10);

add_action('urus_single_product_gallery',array('Urus_Pluggable_WooCommerce','woocommerce_show_product_thumbnails'),20);

add_action( 'urus_left_sticky_single_product_summary', 'woocommerce_template_single_rating', 5 );
add_action( 'urus_left_sticky_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'urus_left_sticky_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'urus_left_sticky_single_product_summary',array('Urus_Brand','display_product_brand_list'),10);
add_action( 'urus_left_sticky_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'urus_left_sticky_single_product_summary', array( 'Urus_Pluggable_Familab_Wishlist', 'wishlist_button' ));
add_action( 'urus_left_sticky_single_product_summary', 'woocommerce_template_single_meta', 61 );
add_action( 'urus_left_sticky_single_product_summary', 'woocommerce_template_single_sharing', 50 );
add_action( 'urus_left_sticky_single_product_summary', array('Urus_Pluggable_WooCommerce','single_canvas_tabs_title'), 60 );

add_action( 'urus_right_sticky_single_product_summary',array('Urus_Pluggable_WooCommerce','urus_show_product_promo'),12);
add_action( 'urus_right_sticky_single_product_summary',array('Urus_Pluggable_WooCommerce','single_product_coundown'),13);
add_action('urus_right_sticky_single_product_summary',array('Urus_Pluggable_Yith_Product_Size_Charts','size_chart_button'),30);
add_action( 'urus_right_sticky_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action('urus_right_sticky_single_product_summary',array('Urus_Compare','button'),31);

add_action( 'urus_sticky_single_product_tab', array('Urus_Pluggable_WooCommerce','single_canvas_tabs'), 10 );

