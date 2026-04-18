<?php
if( !class_exists('Urus_Pluggable_Familab_Wishlist')){
    class Urus_Pluggable_Familab_Wishlist{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;
        /**
         * Initialize pluggable functions.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            add_filter('urus_settings_section_woocommerce',array(__CLASS__,'settings'),101,1);
            $wishlist_enable = Urus_Helper::get_option('enable_familab_wishlist', 1 );
            if( $wishlist_enable == 0 ){
                return;
            }
            add_filter('yith_wcwl_positions','__return_null',999,100);
            add_action( 'urus_function_shop_loop_item_wishlist', array( __CLASS__, 'wishlist_button' ), 20 );
            add_action( 'urus_after_mobile_menu', array(__CLASS__, 'show_wishlist_html' ));
            $urus_single_wishlist_hook = apply_filters( 'urus_single_wishlist_hook', 'woocommerce_single_product_summary' );
            $urus_single_wishlist_position = apply_filters( 'urus_single_wishlist_position', 34 );
            add_action( $urus_single_wishlist_hook, array( __CLASS__, 'wishlist_button' ), $urus_single_wishlist_position );
            self::$initialized = true;
        }

        public static function show_wishlist_html(){
            echo Urus_Helper::urus_wishlist_html();
        }

        public static function settings($settings){
            $settings[] = array(
                'title'        => esc_html__( 'Wishlist', 'urus' ),
                'subsection'   => true,
                'fields'       => array(
                    array(
                        'id'       => 'enable_familab_wishlist',
                        'subtitle' => esc_html__('Users can use Wishlist even when account is not logged in', 'urus'),
                        'type'     => 'switch',
                        'title'    => esc_html__('Enable Familab Wishlist module','urus'),
                        'default'  => true
                    ),
                    array(
                        'id'       => 'open_wishlist_on_add',
                        'subtitle' => esc_html__('Wishlist drawer will open automatically when an item has been added to wishlist', 'urus'),
                        'type'     => 'switch',
                        'title'    => esc_html__('Automatic open Wishlist','urus'),
                        'default'  => true
                    ),
                )
            );
            return $settings;
        }

        public static function wishlist_button(){
            global $product;
            $product_id = $product->get_id();
            $product_info = [];
            $product_info['id'] = $product_id;
            $product_info['price'] = $product->get_price_html();
            $product_info['title'] = $product->get_title();
            $product_info['url'] = get_permalink( $product_id );
            $attachment_id = get_post_thumbnail_id($product_id);
            $product_img = self::get_product_image($attachment_id);
            $product_info['thumb'] = $product_img;
            $product_info['type'] = $product->get_type();
            $auto_open_wishlist = Urus_Helper::get_option('open_wishlist_on_add', 1 );

            $hint_class = Urus_Pluggable_WooCommerce::get_product_loop_hint_class('wishlist');
            ?>
            <div class="urus-add-to-wishlist-btn <?php echo esc_attr($hint_class);?>" aria-label="<?php esc_attr_e('Add to wishlist', 'urus') ?>"
                data-product-id="<?php echo esc_attr($product_id); ?>"
                data-product-info="<?php echo esc_attr( wp_json_encode($product_info) ); ?>"
                data-auto-open="<?php echo esc_attr( $auto_open_wishlist ); ?>">
                <a href="javascript:void(0);" aria-label="<?php esc_html_e('Add to wishlist', 'urus'); ?>" rel="nofollow" >
                    <?php esc_html_e('Add to wishlist', 'urus') ?>
                </a>
            </div>
            <?php
        }
        public static function get_product_image($attachment_id, $size = 'thumbnail', $icon = false ){
            $thumb = wp_get_attachment_image_src(  $attachment_id ,$size, $icon );
            if ($thumb == false) {
                $width = 150;
                $height = 150;
                $placeholder_url="";
                $use_custom_placeholder = Urus_Helper::get_option('enable_custom_placeholder',false);
                if ($use_custom_placeholder ) {
                    $placeholder_url = Urus_Pluggable_WooCommerce::urus_custom_woocommerce_placeholder( "" , true);
                }else{
                    $placeholder_url = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $width . "%20" . $height . "%27%2F%3E";
                }
                return $placeholder_url;
            }else{
                return $thumb[0];
            }
        }
    }
}
