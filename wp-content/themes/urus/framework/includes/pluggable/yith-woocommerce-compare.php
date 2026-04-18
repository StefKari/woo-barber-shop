<?php
if( !class_exists('Urus_Pluggable_Yith_Woocommerce_Compare')){
    class Urus_Pluggable_Yith_Woocommerce_Compare{
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
            if( get_option('yith_woocompare_compare_button_in_product_page') == 'yes'){
                global $yith_woocompare;
                $is_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );
                if( $yith_woocompare->is_frontend() || $is_ajax ) {
                    if( $is_ajax ){
                        if( !class_exists( 'YITH_Woocompare_Frontend' ) ){
                            $file_name = YITH_WOOCOMPARE_DIR . 'includes/class.yith-woocompare-frontend.php';
                            if( file_exists( $file_name ) ){
                                require_once( $file_name );
                            }
                        }
                        $yith_woocompare->obj = new YITH_Woocompare_Frontend();
                    }
                    remove_action( 'woocommerce_after_shop_loop_item', array( $yith_woocompare->obj, 'add_compare_link' ), 20 );
                    remove_action( 'woocommerce_single_product_summary', array( $yith_woocompare->obj, 'add_compare_link' ), 35 );
                }
                add_action('urus_function_shop_loop_item_compare',array(__CLASS__,'compare_button'));
                add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'compare_button' ), 35 );
            }
            // State that initialization completed.
            self::$initialized = true;
        }
        /**
         * Compare button
         */
        public static function compare_button() {
            ob_start();
            global $product;
            if (is_null($product)){
                return;
            }
            $id = $product->get_id();
            $button_text = get_option( 'yith_woocompare_button_text', esc_html__( 'Compare', 'urus' ) );
            if ( function_exists( 'yith_wpml_register_string' ) && function_exists( 'yit_wpml_string_translate' ) ) {
                yit_wpml_register_string( 'Plugins', 'plugin_yit_compare_button_text', $button_text );
                $button_text = yit_wpml_string_translate( 'Plugins', 'plugin_yit_compare_button_text', $button_text );
            }
            if ( class_exists( 'YITH_Woocompare' )  && ! Urus_Mobile_Detect::isMobile() ) { ?>
                <div class="compare-button hint--bounce hint--top"
                     aria-label="<?php esc_html_e( 'Compare', 'urus' ); ?>">
                    <?php
                    printf( '<a href="%s" class="%s" data-product_id="%d" rel="nofollow">%s</a>',
                        self::get_compare_add_product_url( $id ),
                        'compare button',
                        $id,
                        $button_text );
                    ?>
                </div>
            <?php }
            echo ob_get_clean();
        }
        /**
         * Get compare URL
         */
        private static function get_compare_add_product_url( $product_id ) {
            $action_add = 'yith-woocompare-add-product';
            $url_args = array(
                'action' => $action_add,
                'id'     => $product_id,
            );
            return apply_filters( 'yith_woocompare_add_product_url',
                esc_url_raw( add_query_arg( $url_args ) ),
                $action_add );
        }
    }
}
