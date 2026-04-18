<?php
if( !class_exists('Urus_Pluggable_Lazy')){
    class Urus_Pluggable_Lazy{
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

            if ( !is_admin() ) {

                /* CUSTOM IMAGE ELEMENT */
                add_filter( 'post_thumbnail_html', array( __CLASS__, 'post_thumbnail_html' ), 10, 5 );
                add_filter( 'vc_wpb_getimagesize', array( __CLASS__, 'vc_wpb_getimagesize' ), 10, 3 );
                add_filter( 'wp_kses_allowed_html', array( __CLASS__, 'wp_kses_allowed_html' ), 10, 2 );
                add_filter( 'wp_get_attachment_image_attributes', array( __CLASS__, 'lazy_attachment_image' ), 10, 3 );
            }


            // State that initialization completed.
            self::$initialized = true;
        }

        public static function remove_query_string_version( $src){
            $arr_params = array( 'ver', 'id', 'type', 'version' );
            $src        = urldecode( remove_query_arg( $arr_params, $src ) );

            return $src;
        }
        public static function attr_enqueue_script( $tag, $handle )
        {
            $tag = str_replace( "type='text/javascript' ", '', $tag );
            if ( $handle != 'jquery-core' && $handle != 'utils' )
                $tag = str_replace( ' src', ' defer src', $tag );

            return $tag;
        }
        public static function post_thumbnail_html( $html, $post_ID, $post_thumbnail_id, $size, $attr )
        {
            $enable_lazy = Urus_Helper::get_option('theme_use_lazy_load',0);

            if ( $enable_lazy == 1 ) {
                $html = '<figure>' . $html . '</figure>';
            }

            return $html;
        }
        public static function vc_wpb_getimagesize( $img, $attach_id, $params )
        {
            $enable_lazy = Urus_Helper::get_option('theme_use_lazy_load',0);
            if ( $enable_lazy == 1 ) {
                $img['thumbnail'] = '<figure>' . $img['thumbnail'] . '</figure>';
            }

            return $img;
        }
        public static function wp_kses_allowed_html( $allowedposttags, $context )
        {
            $allowedposttags['img']['data-src']    = true;
            $allowedposttags['img']['data-srcset'] = true;
            $allowedposttags['img']['data-sizes']  = true;

            return $allowedposttags;
        }
        public static function lazy_attachment_image( $attr, $attachment, $size ){
            $enable_lazy = Urus_Helper::get_option('theme_use_lazy_load',0);
            if ( class_exists( 'WooCommerce' ) && $size == 'woocommerce_single' ) {
                if( is_product()){
                    $enable_lazy = 0;
                }
        
            }
            if ( $enable_lazy == 1 ) {
                $data_img         = wp_get_attachment_image_src( $attachment->ID, $size );
                $img_lazy         = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $data_img[1] . "%20" . $data_img[2] . "%27%2F%3E";
                $attr['data-src'] = $attr['src'];
                $attr['src']      = $img_lazy;
                $attr['class']    .= ' lazy';
                if ( isset( $attr['srcset'] ) && $attr['srcset'] != '' ) {
                    $attr['data-srcset'] = $attr['srcset'];
                    $attr['data-sizes']  = $attr['sizes'];
                    unset( $attr['srcset'] );
                    unset( $attr['sizes'] );
                }
            }

            return $attr;
        }
    }

}