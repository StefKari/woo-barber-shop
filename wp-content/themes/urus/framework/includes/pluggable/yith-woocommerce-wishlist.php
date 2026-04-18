<?php
if( !class_exists('Urus_Pluggable_Yith_Woocommerce_Wishlist')){
    class Urus_Pluggable_Yith_Woocommerce_Wishlist{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;

        /**
         * Initialize pluggable functions for Visual Composer.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            $wishlist_enable = Urus_Helper::get_option('enable_familab_wishlist', 1);

            
            if (Urus_Helper::is_mobile_template() || $wishlist_enable ) {
                 add_filter('yith_wcwl_positions','__return_null',999,100);
                 return;
            }else{
                add_filter('yith_wcwl_positions',array(__CLASS__,'single_product_wislist_button_positions'),998,1);
            }
            add_action('urus_function_shop_loop_item_wishlist',array(__CLASS__,'add_button'));
            add_action( 'wp_ajax_urus_get_wishlist_count', array(__CLASS__,'urus_get_wishlist_count') );
            add_action( 'wp_ajax_nopriv_urus_get_wishlist_count', array(__CLASS__,'urus_get_wishlist_count') );

            // State that initialization completed.
            self::$initialized = true;
        }

        public static function add_button(){
            global  $product;
            if (is_null($product)){
                return;
            }
            if( get_option('yith_wcwl_enabled') == 'yes' ){
                if ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
                    echo do_shortcode( '[yith_wcwl_add_to_wishlist product_id="' . $product->get_id() . '"]' );
                }
            }
        }

        public static function single_product_wislist_button_positions( $positions ){
            if( isset( $positions['add-to-cart']['hook'] )){
                $positions['add-to-cart']['hook'] = 'woocommerce_single_product_summary';
            }
            if( isset( $positions['add-to-cart']['priority'] )){
                $positions['add-to-cart']['priority'] = 35;
            }
            return $positions;
        }
        public static function urus_get_wishlist_count(){
            echo YITH_WCWL()->count_products( );
            wp_die();
        }
        
    }
}