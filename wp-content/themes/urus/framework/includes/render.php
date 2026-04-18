<?php
if( !class_exists('Urus_Render')){
    class Urus_Render{
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

            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ));
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ));
            add_action( 'wp_enqueue_scripts', array(__CLASS__,'inline_css'),99);

            // State that initialization completed.
            self::$initialized = true;
        }

        public static function admin_scripts(){
            global $wp_scripts;
            wp_enqueue_style( 'magnific-popup', URUS_THEME_URI. 'assets/3rd-party/magnific-popup/magnific-popup.css', array(), false );
            wp_enqueue_style( 'urus-icon', URUS_THEME_URI. 'assets/css/urus-icon.css', array(), false );
            wp_enqueue_script( 'magnific-popup', get_theme_file_uri( '/assets/3rd-party/magnific-popup/jquery.magnific-popup.min.js' ), array( 'jquery' ), false, true );
            add_thickbox();
            wp_enqueue_script( 'jquery-ui-datepicker' );
            $jquery_version = isset( $wp_scripts->registered[ 'jquery-ui-core' ]->ver ) ? $wp_scripts->registered[ 'jquery-ui-core' ]->ver : '1.9.2';
            wp_register_style( 'jquery-ui-css', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
            wp_enqueue_style( 'jquery-ui-css' );
            wp_enqueue_script( 'urus-admin', get_theme_file_uri( '/assets/js/admin/familab_admin.js' ), array( 'jquery' ), '1.0.0', true );
            wp_localize_script( 'urus-admin', 'urus_ajax_admin', array(
                    'ajaxurl'  => admin_url( 'admin-ajax.php' ),
                    'security' => wp_create_nonce( 'urus_ajax_admin' ),
                    'install_popup_title' => esc_html__('Install Sample Data','urus'),
                    'uninstall_popup_title' => esc_html__('UnInstall Sample Data','urus')
                )
            );
        }

        /*Load Google fonts*/

        public static function google_fonts_url(){
            $font_families = array();
            $font_families[] = 'Roboto:300,400,500,600,700';
            $font_families = apply_filters('urus_default_font',$font_families);
            $font_subsets  = array( 'latin', 'latin-ext' );

            $settings = array(
                'opt_typography_body_font',
                'opt_typography_h1_font',
                'opt_typography_h2_font',
                'opt_typography_h3_font',
                'opt_typography_h4_font',
                'opt_typography_h5_font',
                'opt_typography_h6_font',
            );

            foreach ( $settings as $setting ) {
                $typography = Urus_Helper::get_option( $setting );

                if (
                    isset( $typography['font-family'] )
                    && ! empty( $typography['font-family'] )
                    && ( 'Sofia Pro' !== $typography['font-family'] )
                    && ! array_key_exists( $typography['font-family'], $font_families )
                ) {
                    $family = trim( $typography['font-family'] );
                    $family = trim( $family, ',' );

                    $font_families[ $family ] = $family;

                    if ( isset( $typography['subsets'] ) ) {
                        if ( is_array( $typography['subsets'] ) ) {
                            $font_subsets = array_merge( $font_subsets, $typography['subsets'] );
                        } else {
                            $font_subsets[] = $typography['subsets'];
                        }
                    }
                }
            }

            if ( ! empty( $font_families ) ) {
                $font_subsets = array_unique( $font_subsets );
                $query_args   = array(
                    'family' => urlencode( implode( '|', $font_families ) ),
                    'subset' => urlencode( implode( ',', $font_subsets ) ),
                );

                $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
            }
            return urldecode( $fonts_url );
        }

        // Enqueue Frontend
        public static function scripts(){
            $current_theme = wp_get_theme('fami');
            if ( ! empty( $current_theme['Template'] ) ) {
                $current_theme = wp_get_theme( $current_theme['Template'] );
            }
            $version = $current_theme->get('Version');
            wp_enqueue_style( 'urus-googlefonts', Urus_Render::google_fonts_url(), array(), null );

            wp_enqueue_style( 'font-awesome', URUS_THEME_URI. 'assets/3rd-party/font-awesome/css/font-awesome.min.css', array(), '2.4' );
            wp_enqueue_style( 'bootstrap', URUS_THEME_URI. 'assets/3rd-party/bootstrap/bootstrap.min.css', array(), '4.1.3' );
            wp_enqueue_style( 'chosen', URUS_THEME_URI. 'assets/3rd-party/chosen/chosen.min.css', array(), false );
            wp_enqueue_style( 'jquery-scrollbar', URUS_THEME_URI. 'assets/3rd-party/jquery.scrollbar/jquery.mCustomScrollbar.min.css', array(), false );
            wp_enqueue_style( 'magnific-popup', URUS_THEME_URI. 'assets/3rd-party/magnific-popup/magnific-popup.css', array(), false );
            wp_enqueue_style( 'animate', URUS_THEME_URI. 'assets/3rd-party/anime/animate.min.css', array(), false );
            wp_enqueue_style( 'swiper', URUS_THEME_URI. 'assets/3rd-party/swiper/swiper.min.css', array(), '4.4.5' );
            wp_enqueue_style( 'igrowl', URUS_THEME_URI. 'assets/3rd-party/iGrowl/css/igrowl.min.css', array(), false );


            wp_enqueue_style( 'urus-icon',  get_theme_file_uri( '/assets/css/urus-icon.css' ), array(), '1.0.0' );
            wp_enqueue_style( 'hint', URUS_THEME_URI. 'assets/3rd-party/hint/hint.css', array(), '2.4' );

            wp_enqueue_style( 'urus-global',  get_theme_file_uri( '/assets/css/global.css' ), array(), '1.0.0' );
            wp_enqueue_style( 'urus-blog',  get_theme_file_uri( '/assets/css/blog.css' ), array(), '1.0.0' );
            wp_enqueue_style( 'urus-woocommerce',  get_theme_file_uri( '/assets/css/woocommerce.css' ), array(), '1.0.0' );
            wp_enqueue_style( 'urus-theme',  get_theme_file_uri( '/assets/css/theme.css' ), array(), '1.0.0' );
            wp_enqueue_style( 'urus-elements',  get_theme_file_uri( '/assets/css/elements.css' ), array(), '1.0.0' );
            wp_enqueue_style( 'urus-mobile', get_theme_file_uri( '/assets/css/urus_mobile.css' ), array(), '1.0.0' );


            wp_enqueue_style( 'urus', get_stylesheet_uri() );

            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
            }
            $enable_lazy = Urus_Helper::get_option( 'theme_use_lazy_load', 0 );
            if ($enable_lazy){
                wp_enqueue_script( 'lazyload', URUS_THEME_URI.'assets/3rd-party/lazyload/jquery.lazy.js', array( 'jquery' ), '1.0', true );
            }
            wp_enqueue_script( 'bootstrap', URUS_THEME_URI. 'assets/3rd-party/bootstrap/bootstrap.min.js', array( 'jquery' ), '4.1.3', true );
            wp_enqueue_script( 'igrowl', URUS_THEME_URI. 'assets/3rd-party/iGrowl/js/igrowl.min.js', array( 'jquery' ), '3.0.1', true );
            wp_enqueue_script( 'anime', URUS_THEME_URI. 'assets/3rd-party/anime/anime.min.js', array( 'jquery' ), '3.7.0', true );
            wp_enqueue_script( 'chosen', URUS_THEME_URI. 'assets/3rd-party/chosen/chosen.jquery.min.js', array( 'jquery' ), '1.8.2', true );
            wp_enqueue_script( 'pjax', URUS_THEME_URI. 'assets/3rd-party/pjax/jquery.pjax.js' , array( 'jquery' ), '1.8.0', true );
            wp_enqueue_script( 'fitvids', URUS_THEME_URI. 'assets/3rd-party/fitvids/fitvids.min.js', array( 'jquery' ), '1.1', true );
            wp_enqueue_script( 'jquery-scrollbar', URUS_THEME_URI. 'assets/3rd-party/jquery.scrollbar/jquery.mCustomScrollbar.min.js' , array( 'jquery' ), false, true );
            wp_enqueue_script( 'sticky-sidebar', URUS_THEME_URI. 'assets/3rd-party/sticky-sidebar/jquery.sticky-sidebar.min.js' , array( 'jquery' ), '3.3.1', true );
            wp_enqueue_script( 'magnific-popup', URUS_THEME_URI. 'assets/3rd-party/magnific-popup/jquery.magnific-popup.min.js' , array( 'jquery' ), '1.1.0', true );
            wp_enqueue_script( 'countdown', URUS_THEME_URI. 'assets/3rd-party/countdown/countdown.min.js' , array( 'jquery' ), false, true );
            wp_enqueue_script( 'masonry',URUS_THEME_URI. 'assets/3rd-party/masonry/masonry.pkgd.min.js' , array( 'jquery' ), '4.2.2', true );
            wp_enqueue_script( 'isotope',URUS_THEME_URI. 'assets/3rd-party/isotope/isotope.pkgd.min.js' , array( 'jquery' ), '3.0.6', true );
            wp_enqueue_script( 'imagesloaded',URUS_THEME_URI. 'assets/3rd-party/imagesloaded/imagesloaded.pkgd.min.js' , array( 'jquery' ), '4.1.4', true );
            wp_enqueue_script( 'jquery-sticky', URUS_THEME_URI. 'assets/3rd-party/jquery.sticky.js' , array( 'jquery' ), '1.0.4', true );
            wp_dequeue_script( 'swiper' );
            wp_deregister_script( 'swiper' );
            wp_enqueue_script( 'swiper', URUS_THEME_URI. 'assets/3rd-party/swiper/swiper.min.js' , array( 'jquery' ), '5.3.1', true );
            wp_enqueue_script( 'threesixty', URUS_THEME_URI. 'assets/3rd-party/threesixty.min.js' , array( 'jquery' ), '2.0.5', true );
            wp_enqueue_script( 'jquery-easings', URUS_THEME_URI. 'assets/3rd-party/multiscroll/jquery.easings.min.js' , array( 'jquery' ), '1.9.2', true );

            wp_enqueue_script( 'zoom', URUS_THEME_URI. 'assets/3rd-party/zoom/jquery.zoom.min.js' , array( 'jquery' ), '1.7.21', true );
            wp_enqueue_script( 'urus-mobile-template', get_theme_file_uri( '/assets/js/mobile-template.js' ),array( 'jquery'), $version, true );
            wp_enqueue_script( 'cookie', get_theme_file_uri( '/assets/js/js.cookie.js' ), array( 'jquery' ), '2.2.0', true );

            if (is_page_template('templates/fullscreen.php')) {
                wp_enqueue_style('fullPage', URUS_THEME_URI . 'assets/3rd-party/fullPage/fullpage.css', array(), '3.0.3');
                wp_enqueue_script( 'fullpage', URUS_THEME_URI. 'assets/3rd-party/fullPage/fullpage.js' , array( 'jquery' ), '3.0.3', true );
            }

            if( !Urus_Mobile_Detect::isMobile()){
                wp_enqueue_script( 'urus-menu', get_theme_file_uri( '/assets/js/mega-menu.js' ), array( 'jquery' ), $version, true );
            }

            if( class_exists('WooCommerce') && is_product()){
                wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'jquery-ui-accordion' );
            }

            if( did_action( 'elementor/loaded' )){
                wp_enqueue_style( 'slick', URUS_THEME_URI. 'assets/3rd-party/slick/slick.min.css', array(), false );
            }

            wp_enqueue_script( 'urus', get_theme_file_uri( '/assets/js/functions.js' ), array( 'jquery', 'wp-util' ), $version, true );

            $script = array();
            if( class_exists('WooCommerce')){
                $suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                $script[]  = WC()->plugin_url() . '/assets/js/frontend/price-slider' . $suffix . '.js';
            }
            // Product Thumb
            $woo_single_used_layout = Urus_Helper::get_option('woo_single_used_layout','vertical');
            $mobile_single_layout_style = Urus_Helper::get_option('mobile_single_layout_style','style1');
            $v_layout = array('vertical','extra-sidebar','background');
            if (in_array($woo_single_used_layout,$v_layout)){
                $owl_vertical = true;
            }else{
                $owl_vertical = false;
            }
            if( $mobile_single_layout_style =='style1' && Urus_Helper::is_mobile_template()){
                $owl_vertical = false;
            }
            $atts = array(
                'owl_loop' => false,
                'owl_slide_margin' => 12,
                'owl_focus_select' => true,
                'owl_ts_items' => 4,
                'owl_xs_items' => 4,
                'owl_sm_items' => 4,
                'owl_md_items' => 4,
                'owl_lg_items' => 4,
                'owl_ls_items' => 4,
                'owl_vertical' => $owl_vertical,
                'owl_responsive_vertical' => 1366
            );
            if( $mobile_single_layout_style =='style1' && Urus_Helper::is_mobile_template()){
                $atts['owl_slide_margin'] = 10;
            }

            $product_thumb_slide = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
            $owl_settings        = explode( ' ', $product_thumb_slide );
            $is_shop = 0;
            if( function_exists('is_shop')){
                if( is_shop() || is_product_category()){
                    $is_shop =1;
                }
            }
            $instant_filter = Urus_Helper::get_option('enable_instant_filter',0);
            $shop_filter_style = Urus_Helper::get_option('shop_filter_style','dropdown');
            if( $shop_filter_style =='accordion' || $shop_filter_style =='accordion'){
                $instant_filter = 1;
            }

            $data_responsive = 3;
            if( did_action( 'elementor/loaded' )){
                $data_responsive = 9;
            }

            $security_nonce = wp_create_nonce( 'urus_ajax_frontend' );
            wp_localize_script( 'urus', 'urus_ajax_frontend', array(
                    'ajaxurl'  => admin_url( 'admin-ajax.php' ),
                    'security' => $security_nonce,
                    'site_id' => get_current_blog_id(),
                    'added_to_cart_notification_text' => apply_filters('urus_added_to_cart_notification_text', esc_html__('has been added to cart!', 'urus')),
                    'view_cart_notification_text'     => apply_filters('urus_view_cart_notification_text', esc_html__('View Cart', 'urus')),
                    'added_to_cart_text'              => apply_filters('urus_adding_to_cart_text', esc_html__('Product has been added to cart!', 'urus')),
                    'wc_cart_url'                     => (function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : ''),
                    'added_to_wishlist_text'          => get_option('yith_wcwl_product_added_text', esc_html__('Product has been added to wishlist!', 'urus')),
                    'wishlist_url'                    => (function_exists('YITH_WCWL') ? esc_url(YITH_WCWL()->get_wishlist_url()) : ''),
                    'browse_wishlist_text'            => get_option('yith_wcwl_browse_wishlist_text', esc_html__('Browse Wishlist', 'urus')),
                    'growl_notice_text'               => esc_html__('Notice!', 'urus'),
                    'removed_cart_text'               => esc_html__('Product Removed', 'urus'),
                    'enable_lazy'                     => $enable_lazy,
                    'woo_single_layout'               => Urus_Helper::get_option('woo_single_layout', 'left'),
                    'woo_single_used_layout'          => Urus_Helper::get_option('woo_single_used_layout', 'default'),
                    'growl_success_text'              => esc_html__('Successful!', 'urus'),
                    'enable_ajax_filter'              => Urus_Helper::get_option('enable_ajax_filter', 0),
                    'enable_sidebar_sticky'           => Urus_Helper::get_option('enable_sidebar_sticky', 0),
                    'enable_variation_loop_product'   => Urus_Helper::get_option('enable_variation_loop_product',1),
                    'enable_quick_add_loop_product'   => Urus_Helper::get_option('enable_quick_add_loop_product',0),
                    'response_script'                 => !empty( $script ) ? array_values( $script ) : array(),
                    'product_thumb_data_slick'        => urldecode( $owl_settings[3] ),
                    'product_thumb_data_responsive'   => urldecode( $owl_settings[$data_responsive] ),
                    'mini_cart_style' => Urus_Helper::get_option('mini_cart_style','dropdown'),
                    'secs_text' => esc_html__('Secs','urus'),
                    'mins_text' => esc_html__('Mins','urus'),
                    'hrs_text' => esc_html__('Hours','urus'),
                    'days_text' => esc_html__('Days','urus'),
                    'enable_sticky_header' => Urus_Helper::get_option('enable_sticky_header',0),
                    'is_shop' => $is_shop,
                    'prevArrow' => $atts['owl_vertical']? familab_icons('arrow-top'):familab_icons('arrow-left'),
                    'nextArrow' => $atts['owl_vertical']? familab_icons('arrow-bottom'):familab_icons('arrow-right'),
                    'icon_close'=> familab_icons('close'),
                    'icon_cart'=> familab_icons('cart'),
                    'icon_search'=> familab_icons('search'),
                    'icon_arrow_left'=> familab_icons('arrow-left'),
                    'icon_arrow_right'=> familab_icons('arrow-right'),
                    'add_to_cart' => esc_html__('Add to cart', 'urus'),
                    'unavailable' => esc_html__('Unavailable', 'urus'),
                    'select_options' => esc_html__('Select options', 'urus'),
                    'ajax_login_redirecturl' =>  Urus_Helper::get_current_page_url(),
                    'urus_is_mobile' => Urus_Mobile_Detect::isMobile(),
                    'enable_instant_filter' => Urus_Helper::get_option('enable_instant_filter',0),
                    'instant_filter' => $instant_filter,
                    'add_to_wishlist' => esc_html__('Add to Wishlist', 'urus'),
                    'urus_added_to_wishlist_text'  => esc_html__('Product has been added to wishlist!', 'urus'),
                    'view_wishlist' => esc_html__('View wishlist', 'urus'),
                    'empty_wishlist' => esc_html__('Your wishlist is currently empty', 'urus'),
                    'remove_from_wishlist' => esc_html__('Remove this product', 'urus'),
                    'view_product' => esc_html__('View product', 'urus'),
                    'enable_sticky_sidebar' => Urus_Helper::get_option('enable_sticky_sidebar',0),
                    'remove_btn' => esc_html__('Remove','urus'),
                    'is_admin' => is_admin(),
                )
            );

            wp_localize_script( 'urus', 'familab_ajax', array(
                    'ajaxurl'             => admin_url( 'admin-ajax.php' ),
                    'security'            => $security_nonce,
                    'search_empty'        => esc_html__('No Item matched your keyword','urus'),
                    'add_to_cart'  => esc_html__('Add to cart','urus'),
                    'item_added_to_cart'  => esc_html__('Item added to cart','urus'),
                    '_urus_live_search_products' =>get_option('_urus_live_search_products',array()),
                    '_urus_current_lang' => apply_filters( 'wpml_current_language', NULL ),
                )
            );
        }
        public static function getMinStylesheet(){
            $stylesheet_dir_uri = get_stylesheet_directory_uri();
            $stylesheet_uri = $stylesheet_dir_uri . '/style.min.css';
            /**
             * Filters the URI of the current theme stylesheet.
             *
             * @since 1.5.0
             *
             * @param string $stylesheet_uri     Stylesheet URI for the current theme/child theme.
             * @param string $stylesheet_dir_uri Stylesheet directory URI for the current theme/child theme.
             */
            return apply_filters( 'stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
        }
        public static function inline_css(){
            $custom_css = Urus_Helper::get_option('custom_css','');
            $css ='';
            // Get settings css
            $css .= self::settings_css();
            $css .= self::megamenu_css();
            $css .= self::popup_css();
            $css .= self::vc_singular_css();
            $css .= Urus_Pluggable_Visual_Reponsive::get_shortcodes_custom_css();
            $css .= $custom_css;
            $css .= self::option_css();
            $css = preg_replace( '/\s+/', ' ', $css );
            $css = apply_filters('urus_add_css_inline',$css);
            wp_add_inline_style( 'urus', $css );
        }
        public static function settings_css(){
            $css ='';
            $body_width = Urus_Helper::get_option('body_width',1920);
            $content_width = Urus_Helper::get_option('content_width',1170);
            $gutter_width = Urus_Helper::get_option('gutter_width',30);
            $boxed_layout = Urus_Helper::get_option('boxed_layout',0);
            $main_color = Urus_Helper::get_option('main_color','#83b735');
            $enable_boxed= Urus_Helper::get_option('enable_boxed',0);
            $preloader_background_color = Urus_Helper::get_option('preloader_background_color',array('color' =>'#48c8fd','alpha'=>1,'rgba'=>'rgba(33,88,97,1)'));
            $thumb_height = get_option( 'thumbnail_size_h' );
            $opt_typography_body_font = Urus_Helper::get_option('opt_typography_body_font');
            $opt_typography_h1_font = Urus_Helper::get_option('opt_typography_h1_font');
            $opt_typography_h2_font = Urus_Helper::get_option('opt_typography_h2_font');
            $opt_typography_h3_font = Urus_Helper::get_option('opt_typography_h3_font');
            $opt_typography_h4_font = Urus_Helper::get_option('opt_typography_h4_font');
            $opt_typography_h5_font = Urus_Helper::get_option('opt_typography_h5_font');
            $opt_typography_h6_font = Urus_Helper::get_option('opt_typography_h6_font');

            $price_color = Urus_Helper::get_option('price_color','#232529');
            $price_sale_color = Urus_Helper::get_option('price_sale_color','#fc1111');
            $body_background = Urus_Helper::get_option('body_background');
            if( $enable_boxed  == 1){
                $css .='
               .site-content{
                    margin: 0 auto;
                    background-color:#fff;
                    overflow: hidden;
                    max-width:100%;
                    width:1400px;
                    margin-top:50px;
                    margin-bottom:85px;
               }
               @media (max-width: 1440px){
                    .site-content{
                        width:1200px;
                    }
                }

               body{
                background-color:#fff;
               }
            ';
            }
            if( !empty($body_background)){
                if( isset($body_background['background-color']) && $body_background['background-color']!=''){
                    $css.='body {background-color:'.$body_background['background-color'].';}';
                }
                if( isset($body_background['background-repeat']) && $body_background['background-repeat']!=''){
                    $css.='body {background-repeat:'.$body_background['background-repeat'].';}';
                }
                if( isset($body_background['background-size']) && $body_background['background-size']!=''){
                    $css.='body {background-size:'.$body_background['background-size'].';}';
                }
                if( isset($body_background['background-attachment']) && $body_background['background-attachment']!=''){
                    $css.='body {background-attachment:'.$body_background['background-attachment'].';}';
                }
                if( isset($body_background['background-position']) && $body_background['background-position']!=''){
                    $css.='body {background-position:'.$body_background['background-position'].';}';
                }
                if( isset($body_background['background-image']) && !empty($body_background['background-image'])) {
                    $css .= 'body {background-image: url("' . $body_background['background-image'] . '");}';
                }
            }
            if( !empty($opt_typography_body_font)){
                $css .= self::typography_css($opt_typography_body_font,'body');
            }
            if( !empty($opt_typography_h1_font)){
                $css .= self::typography_css($opt_typography_h1_font,'h1,.h1');
            }
            if( !empty($opt_typography_h2_font)){
                $css .= self::typography_css($opt_typography_h2_font,'h2,.h2');
            }
            if( !empty($opt_typography_h3_font)){
                $css .= self::typography_css($opt_typography_h3_font,'h3,.h3');
            }
            if( !empty($opt_typography_h4_font)){
                $css .= self::typography_css($opt_typography_h4_font,'h4,.h4');
            }
            if( !empty($opt_typography_h5_font)){
                $css .= self::typography_css($opt_typography_h5_font,'h5,.h5');
            }
            if( !empty($opt_typography_h6_font)){
                $css .= self::typography_css($opt_typography_h6_font,'h6,.h6');
            }
            // Price Color
            $css .='
            .product-item .price{
                color:'.$price_color.';
            }
            .product-item .price ins{
                color:'.$price_sale_color.';
            }
        ';

            $css  .='
            @media (min-width:1200px){
                .container{max-width:'.($content_width+30).'px;}
                
            }
            @media (min-width:1500px){
                .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12,
                .col, .col-1, .col-10, .col-11, .col-12, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-auto, .col-lg, .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-auto, .col-md, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-auto, .col-sm, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-auto, .col-xl, .col-xl-1, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-auto,
                .col-lg-15, .col-md-15, .col-sm-15, .col-xs-15{
                    padding-left:'.($gutter_width/2).'px;
                    padding-right:'.($gutter_width/2).'px;
                }
                .row,
                .col2-set{
                    margin-left: -'.($gutter_width/2).'px;
                    margin-right: -'.($gutter_width/2).'px;
                }
            }
            .tis-image-items.grid-style .single-image{
                margin-bottom:'.$gutter_width.'px;
            }
            a:hover,
            a:focus,
            a:active,
            .main-menu>li>a:hover,
            .main-menu>li.current-menu-item>a,
            .breadcrumbs a:hover,
            .widget_product_categories .product-categories li>a:active,
            .widget_product_categories .product-categories li>a:focus,
            .widget_product_categories .product-categories li>a:hover,
            .widget_product_categories .product-categories li.current-cat>a,
            .woocommerce-widget-layered-nav-list:not(.layered-nav-swatches) li.chosen>a,
            .woocommerce-widget-layered-nav-list:not(.layered-nav-swatches) li>a:hover,
            .yith-wcwl-add-button:hover .add_to_wishlist,
            .error-404 .page-content>.sub-link a,
            .compare-button:hover .compare,
            .urus_widget_orderby_filter li.selected>a,
            .summary .product-item-share a:hover,
            .woocommerce-mini-cart__total .woocommerce-Price-amount,
            .urus-category  .info .category-name:hover a,
            .urus-custom-menu ul li>a:hover,
            .urus-socials .socials a:hover,
            .header-sidebar-fixed.display-menu .menu li>a:hover,
            .urus_special_banner.layout2 .subtitle,
            .urus_special_banner .title a:hover,
            .link__hover span::before,
            .post-item  .post-title:hover a,
            .post-item .readmore:hover,
            .nav-links .page-numbers.current,
            .widget_recent_comments ul li:hover > a,
            .widget_recent_entries ul li:hover > a,
            .widget_categories ul li>a:hover,
            .widget_meta ul li>a:hover,
            .widget_pages ul li>a:hover,
            .widget_nav_menu ul li>a:hover,
            .widget_archive ul li>a:hover,
            .widget_categories ul li.current-cat>a,
            .main-menu .sub-menu>li>a:hover,
            .famisp-sales-popup-wrap .famisp-product-name:hover,
            .woocommerce-MyAccount-navigation-link.is-active,
            .woocommerce-MyAccount-navigation-link.is-active a,
            .blog-heading.banner .breadcrumbs .trail-item:not(.trail-end):hover span,
            .blog-heading.banner .post-categories li a,
            .page-links > span ,
            .urus-blog-share a:hover,
            li.woocommerce-MyAccount-navigation-link:hover,
            .post-categories a,
            .widget_rss .rss-date,
            .switch-mod.selected,
            .urus-newsletter.default .newsletter-form-button:hover,
            .urus-nav.main-menu > .menu-item:hover>a,
            .urus-nav.main-menu > .menu-item.current-menu-item>a,
            .product-info .product-name:hover a,
            .urus_special_banner.layout1:hover .label_text,
            .footer-post .post-navigation .nav-links .post-title:hover,
            .urus_widget_lastest_post .post-title:hover a,
            .post-item .info-bottom a:hover,
            .comment-navigation .nav-links a:hover,
            .hamburger-menu li:hover>a,
            .product-360-button,
            .product-video-button,
            .yith-wfbt-submit-block .price_text .total_price,
            .woocommerce-product-gallery__trigger:hover::before,
            .urus_special_banner.default .title>a:hover,
            .familab-header-mobile .cart-link .icon-count,
            .urus-custom-menu.color-light .menu-item a:hover,
            .urus-custom-menu.inline ul > li > :hover,
            .urus-custom-menu.vertical .menu-item a:hover,
            .footer .urus-custom-menu.inline.inline-special-1 ul > li > a:hover,
            .single .summary .price,
            .product-subcategory-wapper .sub-cat-item:hover .name a,
            .product-subcategory-wapper .sub-cat-item.current-cat .name a,
            .shop-heading.light .product-subcategory-wapper .sub-cat-item:hover .name a,
            .urus_widget_brand .current-brand>a,
            .urus_widget_brand ul li:hover> a,
            .urus_special_banner.banner-collection .banner-button:hover,
            .link:hover,
            .urus-newsletter.layout6 .newsletter-form-button:hover,
             .urus-category.layout2 .button-link:hover,
             .urus_special_banner.style1 .banner-button:hover,
            .urus_special_banner.style2 .banner-button:hover,
            .urus_special_banner.style8 .banner-button:hover,
            .urus_special_banner.style11 .title:hover,
            .urus-category.layout5:hover  .category-name .link__hover,
            .urus-feature-box.layout2 .icon,
            .full.product-item .buttons > div a:hover::before,
            .urus-feature-box.layout3 .icon,
            .product-item.cart_and_icon .buttons > div a:hover:before,
            .product-item.cart_and_icon .product-info .add-to-cart-wapper a,
            .product-item.info_on_img .buttons > div a:hover::before,
            .product-item.info_on_img .product-info .add-to-cart-wapper a,
            .product-item.info_on_img .wishlist-added.urus-add-to-wishlist-btn a::before,
            .product-item.overlay_info .buttons > div a:hover::before,
            .product-item.overlay_info .buttons > div a:hover::before,
            .product-item.overlay_info .product-info .add-to-cart-wapper a,
            .product-item.overlay_info .wishlist-added.urus-add-to-wishlist-btn a::before,
            .product-item.overlay_center .buttons .add-to-cart-wapper a:hover,
            .product-item.cart_and_icon .buttons .urus-add-to-wishlist-btn.wishlist-added a,
            .product-item.cart_and_icon .buttons .compare-button .urus-compare.compare-added,
            .product-item.full .buttons .compare-button .urus-compare.compare-added,
            .product-item.cart_and_icon .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a,
            .product-item.cart_and_icon .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
            .is-mobile .full.product-item:hover .buttons .add-to-cart-wapper a::before,
            .quantity .group-buttons a:hover::before,
            .urus-category.layout1 .button-link:hover,
            .urus-products .loadmore-wapper.style1 a:hover,
            .urus-feature-box.icon-main-color .icon,
            .urus-products .loadmore-wapper.style1 a:hover::before,
            .urus_special_banner.style11 .banner-button:hover,
            .urus_special_banner.style14 .banner-button:hover,
            .urus-feature-box.main-color-icon .icon,
            .urus-button.text-line1:hover,
            .urus-categories .product-category:hover .cat-name,
            .urus-category.layout11 .list-cat-child .inner-list li a:hover,
            .overlay_info.product-item .buttons .compare-button .urus-compare.compare-added,
            .info_on_img.product-item .buttons .compare-button .urus-compare.compare-added,
            .product-item.info_on_img .buttons .add_to_cart_button.added,
            .product-item.info_on_img .buttons .add_to_cart_button.recent-added,
            .urus-category.layout9 .button-link:hover,
            .header.search-has-categories .extend-primary-menu > li >a,
            .urus-single-product-mobile .summary .urus-add-to-wishlist-btn.wishlist-added a,
            .urus-single-product-mobile .summary .urus-add-to-wishlist-btn a:hover,
            .compare-panel-btn:hover::before,
            .footer .urus-custom-menu.vertical .icon.icon-font,
            .page-template-special .header.logo_menu_center .urus-nav.main-menu > .menu-item>a:hover,
			.page-template-fullscreen .header.logo_menu_center .urus-nav.main-menu > .menu-item.current-menu-ancestor>a,
			.theme-urus #wcfmmp-stores-wrap a.wcfmmp-visit-store,
            .theme-urus #wcfmmp-store .tab_area .tab_links li:hover a, 
            .theme-urus #wcfmmp-store .tab_area .tab_links li.active a{
                color:'.$main_color.';
            }
            .text-underline:before,
            .urus_special_banner.layout2 .image .button,
            .urus-newsletter.layout1.primary .urus-newsletter-form .form-field,
            .product-item.list .group-control-bottom .button,
            .wc-proceed-to-checkout .button,
            .cart-dropdown .mini-cart-bottom .buttons .button:hover,
            .button.alt,
            #Familab_MobileMenu .slinky-menu .header .title,
            #fp-nav ul li a.active span, .fp-slidesNav ul li a.active span, #fp-nav ul li:hover a.active span, .fp-slidesNav ul li:hover a.active span, 
            .post-item .readmore::before,
            .urus-section-title:after,
            .remove_compare:hover,
            .product-video-button:hover,
            .mobile_shop_filter_content .close-mobile-filter,
            .mobile_shop_filter_content .widget-title:after,
            #Familab_MobileMenu.style1 .close-menu,
            .urus-single-product-mobile .mobile-open-cart,
            .wc-tab-canvas .tab-head,
            .single-product .wc-tab .tab-head,
            .urus-category .category-name a:after,
            .post-item .readmore:hover .text::after,
            .blog-style-standard .post-item .post-item-head .date,
            .tagcloud a:hover,
            .cart-dropdown .mini-cart-bottom .buttons .button.checkout:hover,
            .urus_special_banner.layout1 .banner-button:hover::after,
            .button.primary,
            #cookie-notice .cn-button:hover,
            .summary .cart .button:hover,
            .single_add_to_cart_button:hover,
            #familab-search-mobile .drawer_back > .js-drawer-close,
            .urus_filter_content .prdctfltr_collector_border > span,
            .urus-socials.default .social::before,
            .urus-socials.layout1 a.social::before,
            .urus-socials.layout3 a.social::before,
            .urus_special_banner.style11 .banner-button:hover::before,
            .urus_special_banner.layout1 .label_text::before,
            .urus-newsletter.layout10 .newsletter-form-button:hover,
            .mini-cart-head .mini-cart-undo,
            .cart-drawer .mini-cart-head .mini-cart-undo,
            .mini-cart-head .mini-cart-undo,
            .post__related__wapper .urus-section-title .title + .subtitle:after,
            .button:hover, button:hover, input[type=submit]:hover,
            .urus-products .loadmore-wapper.default a:hover,
            .prdctfltr-pagination-load-more .button:hover,
            .urus-nav.main-menu > .menu-item>a:after,
            .urus-category.default .product-category-item:hover .info,
            .title-dash-style1 .title:after,
            .filter-loadding-wapper .filter-loadding,
            .wc-tabs li.active a::before,
            .urus-button.default:hover::after,
            .urus-newsletter.layout2 .newsletter-form-button::after,
            .shop-control .shop-action .show-filter-btn:hover,
            .urus-filter-accordion>li.widget-toggle.active > a,
            .urus-filter-accordion>li.widget-toggle > a:hover,
            .urus-blogs.default .post-title:after,
            .product-subcategory-wapper .sub-cat-item:after,
            .urus-tab.default .tab-link > li.active > a:after,
            .urus-tab.default .tab-link > li:hover > a:after,
            .urus_special_banner.banner-collection .banner-button:hover::after,
            .link:hover::after,
            .urus-category.layout2 .button-link:hover::after,
            .urus-socials.layout2 .social:hover,
            .urus-blogs.default .read-more:hover::after,
            .summary .urus-add-to-wishlist-btn.wishlist-added a,
            .summary .urus-add-to-wishlist-btn:hover a,
            .header.full-search .main-menu-wapper,
            .product-item .buttons .add-to-cart-wapper a:hover,
            .product-item .buttons .yith-wcwl-add-to-wishlist a:hover,
            .product-item .buttons .compare-button a:hover,
            .product-item .buttons .yith-wcqv-button-wapper a:hover,
            .product-item .buttons .quick-view-btn a:hover,
            .product-item .urus-add-to-wishlist-btn a:hover,
            .product-item .urus-swiper ~ .slick-arrow:hover,
            .product-item .buttons .add_to_cart_button.added,
            .product-item .buttons .add_to_cart_button.recent-added,
            .product-item.full .buttons .add-to-cart-wapper a,
            .product-item .buttons .urus-add-to-wishlist-btn a:hover,
            .product-item:not(.cart_and_icon) .buttons .compare-button .urus-compare.compare-added,
            .product-item:not(.cart_and_icon) .buttons .urus-add-to-wishlist-btn.wishlist-added a,
            .product-item:not(.cart_and_icon) .buttons .urus-add-to-wishlist-btn.added a,
            .product-item:not(.cart_and_icon) .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a,
            .product-item:not(.cart_and_icon) .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
            .product-item.full .urus-add-to-wishlist-btn.wishlist-added a,
            .product-item.full .urus-add-to-wishlist-btn.added a,
            .urus-category.layout7 .info,
            .urus-category.layout11 .info,
            .urus-section-title.default:before,
            .product-item .variations_form .change-value.text:hover,
            .product-item .variations_form .change-value.text.active,
            .product-item.overlay_center .buttons .add-to-cart-wapper a,
            .product-item .urus_swatch_attribute.text:hover,
            .product-item .urus_swatch_attribute.text.active,
            .footer.dark .urus-custom-menu.vertical .title::after,
            .urus_special_banner.style3 .banner-button:hover,
            .urus_special_banner.style13 .banner-button:hover,
            .urus_special_banner.style14 .banner-button:hover::before,
            .dark .urus-newsletter.layout11 .newsletter-form-button,
            .urus-socials.layout4 .social:hover,
            .header.extend_menu .extend-menu-wapper,
            .urus_full_search .button-link,
            .urus-newsletter.layout12 .newsletter-form-button,
            .urus_special_banner.style15 .banner-button,
            .urus_special_banner.style16 .banner-button,
            .theme-urus a.wcfm_catalog_enquiry:hover, span.add_enquiry:hover, 
            .theme-urus a.wcfm_follow_me:hover, a.wcfm_chat_now_button:hover,
            .theme-urus #wcfmmp-stores-lists .wcfm_catalog_enquiry:hover,
            .theme-urus #wcfmmp-store .tab_area .tab_links li a:before,
            .urus-box-products .box-head .title::after,
            #Familab_WishlistDrawer .single-wishlist-item .product-add-to-cart a {
                background-color:'.$main_color.';
            }
            .slick-dots button:hover,
            .slick-dots .slick-active button,
            .urus-newsletter.layout1.primary .urus-newsletter-form .form-field ,
             blockquote p::before,
            .remove_compare:hover,
            .tagcloud a:hover,
            .urus-socials.layout1 a.social:hover,
            .urus-socials.layout3 a.social:hover,
            .urus_special_banner.style11 .banner-button:hover::before,
            .urus-newsletter.layout10 .newsletter-form-button:hover,
            .backtotop.default:hover,
            .urus-products .loadmore-wapper.default a:hover,
            .prdctfltr-pagination-load-more .button:hover,
            .urus-nav.main-menu .menu-item > a::after,
            .shop-control .shop-action .show-filter-btn:hover,
            .urus-filter-accordion>li.widget-toggle.active > a,
            .urus-filter-accordion>li.widget-toggle > a:hover,
            .urus_special_banner.style5 .banner-button:hover,
            .urus-newsletter.layout6 .newsletter-form-button:hover,
            .urus-category .button-link:hover,
            .urus_special_banner.style1 .banner-button:hover,
            .urus_special_banner.style2 .banner-button:hover,
            .product-item .buttons > div a:hover,
            .product-item .urus_swatch_attribute.text:hover,
            .product-item .urus_swatch_attribute.text.active,
            .product-item:not(.cart_and_icon) .buttons .add-to-cart-wapper a:hover,
            .product-item:not(.cart_and_icon) .buttons .yith-wcwl-add-to-wishlist a:hover,
            .product-item:not(.cart_and_icon) .buttons .compare-button a:hover,
            .product-item:not(.cart_and_icon) .buttons .compare-button .urus-compare.compare-added,
            .product-item .buttons .yith-wcqv-button-wapper a:hover,
            .product-item .buttons .quick-view-btn a:hover,
            .product-item .buttons .urus-add-to-wishlist-btn a:hover,
            .product-item .urus_swatch_attribute.text:hover,
            .product-item .urus_swatch_attribute.text.active,
            .product-item .variations_form .change-value.text.active,
            .product-item .familab_swatch_attribute.text.active,
            .product-item .variations_form .change-value:hover,
            .product-item .familab_swatch_attribute:hover,
            .product-item .familab_swatch_attribute.photo-extend.active,
            .urus-single-product-top .flex-control-nav li img.flex-active,
            .urus-products .loadmore-wapper.style1 a:hover,
            .product-item:not(.cart_and_icon) .buttons .add_to_cart_button.added,
            .product-item:not(.cart_and_icon) .buttons .add_to_cart_button.recent-added,
            .product-item:not(.cart_and_icon) .buttons .urus-add-to-wishlist-btn.wishlist-added a,
            .product-item:not(.cart_and_icon) .buttons .urus-add-to-wishlist-btn.added a,
            .product-item:not(.cart_and_icon) .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a,
            .product-item:not(.cart_and_icon) .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
            .compare-panel-btn:hover::before,
            .theme-urus #wcfmmp-stores-wrap ul.wcfmmp-store-wrap li .store-content,
            .theme-urus #wcfmmp-stores-wrap a.wcfmmp-visit-store{
                border-color:'.$main_color.';
            }
            .model_menu_wrapper .scene path {
                fill: '.$main_color.';
            }
        ';

            $css.='
            .preloader:after{
                background-color:'.$preloader_background_color['rgba'].';
            }
        ';
            $css.='
        .backtotop  .backtotop-round{
            stroke: '.$main_color.';
        }
        ';

            $shop_background = Urus_Helper::get_option('shop_background',array());
            if( !empty($shop_background)){
                if( isset($shop_background['background-color']) && $shop_background['background-color']!=''){
                    $css.='.shop-layout-background-modern {background-color:'.$shop_background['background-color'].';}';
                }
                if( isset($shop_background['background-repeat']) && $shop_background['background-repeat']!=''){
                    $css.='.shop-layout-background-modern {background-repeat:'.$shop_background['background-repeat'].';}';
                }
                if( isset($shop_background['background-size']) && $shop_background['background-size']!=''){
                    $css.='.shop-layout-background-modern {background-size:'.$shop_background['background-size'].';}';
                }
                if( isset($shop_background['background-attachment']) && $shop_background['background-attachment']!=''){
                    $css.='.shop-layout-background-modern {background-attachment:'.$shop_background['background-attachment'].';}';
                }
                if( isset($shop_background['background-position']) && $shop_background['background-position']!=''){
                    $css.='.shop-layout-background-modern {background-position:'.$shop_background['background-position'].';}';
                }
                if( isset($shop_background['background-image']) && $shop_background['background-image']!='') {
                    $css .= '.shop-layout-background-modern {background-image: url("' . $shop_background['background-image'] . '");}';
                }
            }
            $shop_heading_overlay_color = Urus_Helper::get_option('shop_heading_overlay_color',array());
            if( !empty($shop_heading_overlay_color) && isset($shop_heading_overlay_color['rgba']) && $shop_heading_overlay_color['rgba']!=''){
                $css .='.shop-heading.overlay::before {background-color: '.$shop_heading_overlay_color['rgba'].';}';
            }
            // Plug into WooCommerce if available.
            if ( class_exists( 'WooCommerce' ) ) {
                if( is_shop()){
                    $shop_page_id = get_option( 'woocommerce_shop_page_id' );
                    $vc_css = get_post_meta($shop_page_id,'_wpb_shortcodes_custom_css',true);
                    $css.= $vc_css;
                }
            }

            $single_product_background_type = Urus_Helper::get_option('single_product_background_type',array());


            if( !empty($single_product_background_type)){
                if( isset($single_product_background_type['background-color']) && $single_product_background_type['background-color']!=''){
                    $css.='.background-single-product-top .background-content {background-color:'.$single_product_background_type['background-color'].';}';
                }
                if( isset($single_product_background_type['background-repeat']) && $single_product_background_type['background-repeat']!=''){
                    $css.='.background-single-product-top .background-content {background-repeat:'.$single_product_background_type['background-repeat'].';}';
                }
                if( isset($single_product_background_type['background-size']) && $single_product_background_type['background-size']!=''){
                    $css.='.background-single-product-top .background-content {background-size:'.$shop_background['background-size'].';}';
                }
                if( isset($single_product_background_type['background-attachment']) && $single_product_background_type['background-attachment']!=''){
                    $css.='.background-single-product-top .background-content {background-attachment:'.$single_product_background_type['background-attachment'].';}';
                }
                if( isset($single_product_background_type['background-position']) && $single_product_background_type['background-position']!=''){
                    $css.='.background-single-product-top .background-content {background-position:'.$single_product_background_type['background-position'].';}';
                }
                if( isset($single_product_background_type['background-image']) && $single_product_background_type['background-image']!='') {
                    $css .= '.background-single-product-top .background-content{background-image: url("' . $single_product_background_type['background-image'] . '");}';
                }
            }

            return $css;
        }
        public static function popup_css(){
            $popup_css ='';
            $args = array(
                'post_type' => 'urus-popup',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $posts = new WP_Query( $args );
            if ( $posts -> have_posts() ) {
                while ( $posts -> have_posts() ) {
                    $posts->the_post();
                    $popup_css .= get_post_meta(get_the_ID(),'_wpb_shortcodes_custom_css',true);
                    $popup_css .= get_post_meta(get_the_ID(),'_urus_vc_shortcode_custom_css',true);
                    $popup_css .= get_post_meta(get_the_ID(),'_urus_shortcode_custom_css',true);
                }
            }
            wp_reset_postdata();
            return $popup_css;
        }
        public static function option_css(){
            //logo style setting
            $css = '';
            $logo_width = Urus_Helper::get_option('logo_width',97);
            $logo_inner_style = Urus_Helper::get_logo_inner_style();
            $css .= '.header .main-header .logo a,.urus-login-form-popup .form-head a{
            width: '.apply_filters('urus_set_logo_width',$logo_width).'px;
            '.apply_filters('urus_set_logo_style',$logo_inner_style).'
        }
        ';
            $css .= '.header.logo_center .main-header{
            display: grid;
            grid-template-columns: 1fr '.apply_filters('urus_set_logo_width',$logo_width).'px 1fr;
        }
        ';
            if (Urus_Helper::get_option('enable_header_promo',0)){
                $promo_height = Urus_Helper::get_option('promo_height',60).'px';
                $promo_color = Urus_Helper::get_option('header_promo_color','#ffffff');
                $promo_bg_img = Urus_Helper::get_option('header_promo_bg_img');
                $promo_btn_close_color = Urus_Helper::get_option('promo_btn_close_color', "#fff");
                $promo_btn_close_hover = Urus_Helper::get_option('promo_btn_close_hover', "#000");
                $promo_btn_close_size = Urus_Helper::get_option('promo_btn_close_size', 18);
                $promo_bg = '';
                if (isset($promo_bg_img['url']) && $promo_bg_img['url'] != ''){
                    $promo_bg = 'background-image: url("'.$promo_bg_img['url'].'");';
                    $promo_bg .='background-repeat: no-repeat;';
                    $promo_bg .='background-position: left top;';
                    $promo_bg .='background-size: cover;';
                }
                $promo_bg .= 'background-color: '.Urus_Helper::get_option('header_promo_bg_color','#ffffff').';';
                $css .= '.header-promo{
                height: '.$promo_height.';
                max-height:  '.$promo_height.';'
                    .$promo_bg.'
	            }
	            .header-promo .header-promo-text{
	                color: '.$promo_color.'
	            }
	            body{
	                padding-top: '.$promo_height.';
	            }
	            .header-promo .header-promo-control{
	                font-size: '.$promo_btn_close_size.'px;
	                color: '.$promo_btn_close_color.';
	            }';
                if (!empty($promo_btn_close_hover)){
                	$css .= '.header-promo .header-promo-control:hover{
		                color: '.$promo_btn_close_hover.';
		            }';
                }

            }
            //

            if (Urus_Helper::get_option('enable_mobile_template',1)){
                $mobile_logo_width = Urus_Helper::get_option('mobile_logo_width','120').'px';
                $mobile_inner_style = Urus_Helper::get_logo_inner_style('mobile');
                $css .= '
                .mobile-header-logo a{
                    max-width: '.apply_filters('urus_set_mobile_logo_width',$mobile_logo_width).';
                    width: 100%;
                }
                @media(min-width: 560px){
                    .mobile-header-logo a{
	                    width: '.apply_filters('urus_set_mobile_logo_width',$mobile_logo_width).';
	                    '.apply_filters('urus_set_mobile_logo_style',$mobile_inner_style).'
	                }
                }';
            }
            return $css;
        }
        public static function megamenu_css(){
            $vc_css ='';
            $args = array(
                'post_type' => 'familab_menu',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $posts = new WP_Query( $args );
            if ( $posts -> have_posts() ) {
                while ( $posts -> have_posts() ) {
                    $posts->the_post();
                    $vc_css .= get_post_meta(get_the_ID(),'_wpb_shortcodes_custom_css',true);
                    $vc_css .= get_post_meta(get_the_ID(),'_urus_vc_shortcode_custom_css',true);
                    $vc_css .= get_post_meta(get_the_ID(),'_urus_shortcode_custom_css',true);
                }
            }
            wp_reset_postdata();
            return $vc_css;
        }

        public static function vc_singular_css(){
            if( is_singular()){
                return get_post_meta( get_the_ID(), '_urus_shortcode_custom_css', true );
            }
            return '';
        }
        public static function typography_css($var = null,$selector = ''){
            if (is_null($var) || $selector == ''){
                return '';
            }
            $attrs = ['font-family','font-style','font-weight','font-size','line-height','word-spacing','letter-spacing','color string','text-align','text-transform'];
            $css = '';
            $style      = '';
            if ($var && !empty($var)){
                foreach ($attrs as $attr){
                    if (isset($var[$attr]) && !empty($var[$attr])){
                        $style .= $attr . ': ' . $var[$attr] . ';';
                    }
                }
            }
            if ( ! empty( $style ) ) {
                $css .= $selector . '{' . $style . '}';
            }
            return $css;
        }
    }
}
