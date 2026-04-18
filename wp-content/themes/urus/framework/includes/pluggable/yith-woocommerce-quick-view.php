<?php
if( !class_exists('Urus_Pluggable_Yith_Woocommerce_Quick_View')){
    class Urus_Pluggable_Yith_Woocommerce_Quick_View{
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
            // Class frontend
            $enable             = get_option( 'yith-wcqv-enable' ) == 'yes' ? true : false;
            $enable_on_mobile   = get_option( 'yith-wcqv-enable-mobile' ) ==  'yes' ? true : false;
            // Class frontend
            if( ( ! Urus_Mobile_Detect::isMobile() && $enable ) || ( Urus_Mobile_Detect::isMobile() && $enable_on_mobile && $enable ) ) {
                remove_action( 'woocommerce_after_shop_loop_item', array( YITH_WCQV_Frontend::get_instance(), 'yith_add_quick_view_button' ), 15 );
                add_action( 'urus_function_shop_loop_item_quickview', array( YITH_WCQV_Frontend::get_instance(), 'yith_add_quick_view_button' ), 5 );
            }
            add_filter('yith_add_quick_view_button_html',array(__CLASS__,'yith_add_quick_view_button_html'),10,3);
            
            // State that initialization completed.
            self::$initialized = true;
        }
        public static function yith_add_quick_view_button_html( $button, $label, $product){
            $html ='<div class="hint--top hint--bounce yith-wcqv-button-wapper" aria-label="'.$label.'">';
            $html .= $button;
            $html.='</div>';
            return  $html;
        }

        public static function quick_view_thumb(){
            echo  '<div class="images">';
            global $post, $product;
            $attachment_ids = $product->get_gallery_image_ids();
            if ( $attachment_ids && has_post_thumbnail() ) {
                $html_thumbnail = '';
                foreach ( $attachment_ids as $attachment_id ) {
                    $full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
                    $thumbnail       = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
                    $attributes      = array(
                        'title'                   => get_post_field( 'post_title', $attachment_id ),
                        'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
                        'data-src'                => $full_size_image[0],
                        'data-large_image'        => $full_size_image[0],
                        'data-large_image_width'  => $full_size_image[1],
                        'data-large_image_height' => $full_size_image[2],
                    );
                    $html_thumbnail .='<div>';
                    $html_thumbnail .= wp_get_attachment_image( $attachment_id, 'shop_single', false, $attributes );
                    $html_thumbnail .='</div>';
                }
            }
            $html_main ='<div class="slider-for">';
            $html_main .='<div>';
            $html_main .= get_the_post_thumbnail( $post->ID, 'shop_single' );
            $html_main .='</div>';
            $html_main .=  $html_thumbnail;
            $html_main .='</div>';

            echo ''.$html_main;

            echo  '</div>';
        }
        public static function excerpt(){
            global $product;
            global $post;

            $short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

            if ( ! $short_description ) {
                return;
            }
            if ( $product->get_type() == 'variable' ) return;
            ?>
            <div class="woocommerce-product-details__short-description">
                <?php echo ''.$short_description; // WPCS: XSS ok. ?>
            </div>
            <?php

        }
    }
}