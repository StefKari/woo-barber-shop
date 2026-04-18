<?php
if( !class_exists('Urus_Pluggable_Yith_Woocommerce_Social_Login')){
    class Urus_Pluggable_Yith_Woocommerce_Social_Login{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;

        public static $socials_icons = array();

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
            self::$socials_icons = array(
                'facebook' => 'fa fa-facebook',
                'twitter'  => 'fa fa-twitter',
                'google'   => 'fa fa-google',
            );


            remove_action('woocommerce_login_form', array( YITH_WC_Social_Login_Frontend::get_instance(),'social_buttons') );
            remove_action('woocommerce_after_template_part', array( YITH_WC_Social_Login_Frontend::get_instance(),'social_buttons_in_checkout') );
            add_action('woocommerce_login_form_end', array( YITH_WC_Social_Login_Frontend::get_instance(),'social_buttons'),10 );

            add_filter('yith_wc_social_login_icon',array(__CLASS__,'yith_wc_social_login_icon'),10,3);

            // State that initialization completed.
            self::$initialized = true;
        }

        public static function yith_wc_social_login_icon( $social, $key, $args){

            $socials_icons =  self::$socials_icons;

            if( !empty($socials_icons) && isset($socials_icons[$key])){
                $icon ='<span class="icon '.$socials_icons[$key].'"></span>'.$args['value']['label'].'';
                $style ='style="background-color:'.$args['value']['color'].';"';
                $social = sprintf( '<a %s class="%s" href="%s">%s</a>',$style, $args['class'], $args['url'], $icon );
            }
            return $social;
        }
    }
}