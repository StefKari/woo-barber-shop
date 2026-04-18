<?php
if( !class_exists('Urus_Pluggable_Wcml')){
    class Urus_Pluggable_Wcml{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;
        protected static $mobile_template = false;
    
        /**
         * Initialize pluggable functions for Visual Composer.
         * Initialize pluggable functions for Visual Composer.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            
            add_filter('wcml_client_currency',array(__CLASS__,'wcml_client_currency'));
            
            // State that initialization completed.
            self::$initialized = true;
        }
    
        public static function wpml_currency_switcher(){
            ob_start();
            $format ='%code%';
            $format = apply_filters('urus_wcml_currency_switcher_format',$format);
            $args = array(
              'switcher_style'=>'urus-menu-list',
              'format' => $format,
            );
            
            
            do_action('wcml_currency_switcher', $args);
            $html = ob_get_clean();
            return $html;
        }
    
        public static function wcml_client_currency($current_currency){
            $currency = isset( $_GET['currency'] ) ? esc_attr( $_GET['currency'] ) : $current_currency;
        
            return strtoupper( $currency );
        
        }
    
        function myplugin_wcml_cs_dirs_to_scan( $dirs ) {
            $folder_name = basename( dirname( __FILE__ ) );
            $dirs[]     = trailingslashit( WP_PLUGIN_DIR ) . $folder_name . '/templates/';
            return $dirs;
        }
    }
}