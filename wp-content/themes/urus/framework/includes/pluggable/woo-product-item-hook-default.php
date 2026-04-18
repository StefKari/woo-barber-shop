<?php
remove_action('woocommerce_before_shop_loop_item','woocommerce_template_loop_product_link_open',10);

remove_action('woocommerce_before_shop_loop_item_title','woocommerce_show_product_loop_sale_flash',10);
remove_action('woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_thumbnail',10);
add_action('woocommerce_before_shop_loop_item_title',array('Urus_Pluggable_WooCommerce','product_loop_sale_flash'),10);
add_action('woocommerce_before_shop_loop_item_title',array('Urus_Pluggable_WooCommerce','template_loop_product_thumbnail'),15);

remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10);
add_action('woocommerce_shop_loop_item_title',array('Urus_Pluggable_WooCommerce','template_loop_product_title'),10);


//remove product price and add to another position
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 5 );

//remove product rating and add to another position
$woo_product_rating_in_loop = Urus_Helper::get_option('woo_product_rating_in_loop',0);
if($woo_product_rating_in_loop  == 0){
    remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating',5);
}

remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_product_link_close',5);

add_action('urus_product_loop_group_flash_content','woocommerce_show_product_loop_sale_flash',10);
add_action('urus_product_loop_group_flash_content',array('Urus_Pluggable_WooCommerce','woocommerce_show_product_loop_new_flash'),15);

add_action('urus_function_loop_item_countdown',array('Urus_Pluggable_WooCommerce','template_loop_count_down'),13);


