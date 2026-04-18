<?php
if( !class_exists('Urus_Pluggable')){
    class  Urus_Pluggable{
        public static $theme_layout = array();
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
            self::$theme_layout = Urus_Pluggable::get_theme_layout_support();
            add_action('after_setup_theme',array(__CLASS__,'after_setup_theme'));
            add_action( 'body_class', array( __CLASS__, 'body_class' ) );
            add_action( 'widgets_init', array( __CLASS__, 'register_sidebar_widgets' ) );
            add_filter( 'wp_nav_menu_items',array(__CLASS__,'control_menu_items'), 10, 2 );
            //add_filter( 'wp_nav_menu_items',array(__CLASS__,'control_left_menu_items'), 10, 2 );
	        if( is_admin()){
		        Urus_Plugins::initialize();
			    Urus_Import::initialize();
		        Urus_Dashboard::initialize();
                Urus_Settings::initialize();
                Urus_Settings_Meta_Box::initialize();
	        }
	        Urus_iconset::initialize();
            Urus_Render::initialize();
            Urus_Footer::initialize();
            Urus_Mailchimp::initialize();
            Urus_Pluggable_Lazy::initialize();
            Urus_Popup::initialize();
	        Urus_Sample_Data::initialize();
            // Plug into Visual Composer if available.
            if ( class_exists( 'VC_Manager' ) ) {
                Urus_Pluggable_Visual_Composer::initialize();
                Urus_Pluggable_Visual_Reponsive::initialize();
                if( class_exists('WooCommerce')){
                    new Urus_Shortcodes_Products();
                    new Urus_Shortcodes_Category();
                }
                new Urus_Shortcodes_Title();
                new Urus_Shortcodes_Instagram();
                new Urus_Shortcodes_Slide();
                new Urus_Shortcodes_Custom_Menu();
                new Urus_Shortcodes_Blogs();
                new Urus_Shortcodes_Socials();
                new Urus_Shortcodes_Newsletter();
                new Urus_Shortcodes_Banner();
                new Urus_Shortcodes_Feature_Box();
                new Urus_Shortcodes_Disable_Popup();
                new Urus_Shortcodes_Button();
                new Urus_Shortcodes_Container();
                new Urus_Shortcodes_Tab();
                new Urus_Shortcodes_Special_Banner();
                new Urus_Shortcodes_Countdown();
                new Urus_Shortcodes_Testimonials();
                new Urus_Shortcodes_Full_Search();
                new Urus_Shortcodes_Video();
                new Urus_Shortcodes_Promo_Banner();
            }
            // Plug into WooCommerce if available.
            if ( class_exists( 'WooCommerce' ) ) {
                Urus_Quick_View::initialize();
                Urus_Compare::initialize();
                Urus_Product_360degree::initialize();
                Urus_Pluggable_Search_Module::initialize();
                Urus_Pluggable_Familab_Wishlist::initialize();
                if( class_exists('Familab_Core_Variation_Swatches')){
                    Urus_Pluggable_Woo_Variation_Swatches::initialize();
                }
                if( class_exists('YITH_WCQV')){
                    Urus_Pluggable_Yith_Woocommerce_Quick_View::initialize();
                }
                if( class_exists('YITH_WCWL') ){
                    Urus_Pluggable_Yith_Woocommerce_Wishlist::initialize();
                }
                if( class_exists('YITH_Woocompare')){
                    Urus_Pluggable_Yith_Woocommerce_Compare::initialize();
                }
                if( class_exists('YITH_WCPSC')){
                    Urus_Pluggable_Yith_Product_Size_Charts::initialize();
                }
                Urus_Brand::initialize();
                Urus_Category_Description::initialize();
                Urus_Category_Background::initialize();
                Urus_Pluggable_WooCommerce::initialize();
                Urus_Woo_Variation_Gallery::initialize();
                Urus_Promo_Information::initialize();
            }
            if(class_exists('SitePress')){
                Urus_Pluggable_Wcml::initialize();
            }
            if( class_exists('Familab_Instagram_Shop')){
                Urus_Pluggable_Familab_Instagram_Shop::initialize();
            }

            add_action('urus_before_header',array(__CLASS__,'header_message'),1);
            add_action('urus_header_right_control',array(__CLASS__,'header_control'),1);
            add_action('urus_header_left_control',array(__CLASS__,'header_left_control'),1);
            //HEADER
            add_filter('familab_widgets',array(__CLASS__,'add_widget_list'));
            // State that initialization completed.

            // Elementor
            if( did_action( 'elementor/loaded' )){

                Urus_Pluggable_Elementor::initialize();
            }
            $enable_boxed = Urus_Helper::get_option('enable_boxed',0);
            if( $enable_boxed == 1){
                add_action('urus_before_site_content','Urus_Helper::get_promo_header');
            }else{
                add_action('urus_before_content','Urus_Helper::get_promo_header');
            }

            //add_filter('jpeg_quality',array(__CLASS__,'jpeg_quality'),999);

            add_action( 'wp_ajax_nopriv_urus_ajax_login',  array(__CLASS__,'ajax_mobile_login')  );
            add_action( 'wp_ajax_urus_ajax_login',  array(__CLASS__,'ajax_mobile_login')  );


            self::$initialized = true;
        }

        /**
         * Setup theme.
         *
         * @return  void
         */
        public static function after_setup_theme() {
	        add_theme_support('familab-core');
            // Load language translation.
            load_theme_textdomain( 'urus', URUS_THEME_DIR . '/languages' );

            // Indicate widget sidebars can use selective refresh in the Customizer.
            add_theme_support( 'customize-selective-refresh-widgets' );

            // Add default posts and comments RSS feed links to head.
            add_theme_support( 'automatic-feed-links' );

            // Support WooCommerce plugin.
            add_theme_support( 'woocommerce' );
            add_theme_support( 'wc-product-gallery-lightbox' );
            add_theme_support( 'wc-product-gallery-slider' );
            $woo_single_used_layout = Urus_Helper::get_option('woo_single_used_layout','vertical');
            if (!isset(self::$theme_layout['single_layout'][$woo_single_used_layout]['zoom']) || self::$theme_layout['single_layout'][$woo_single_used_layout]['zoom']){
                add_theme_support( 'wc-product-gallery-zoom' );
            }

            // Enable support for Post Thumbnails on posts and pages.
            add_theme_support( 'post-thumbnails' );

            // Let WordPress manage the document title.
            add_theme_support( 'title-tag' );

            // Add Excerpts to Your Pages
            add_post_type_support( 'page', 'excerpt' );

            // Switch default core markup for search form, comment form, and comments to output valid HTML5.
            add_theme_support(
                'html5',
                array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' )
            );
            // Add supported post format.
            add_theme_support(
                'post-formats',
                array( 'gallery', 'video','audio' )
            );
            // Register nav menu locations.
            register_nav_menus(
                array(
                    'primary'      => esc_html__('Primary Menu', 'urus'),
                    'hamburger_menu' => esc_html__( 'Hamburger Menu (Display on Header use Bar Menu)', 'urus' ),
                    'control_menu' => esc_html__( 'Control Right Menu', 'urus' ),
                    'control_left_menu' => esc_html__( 'Control Left Menu', 'urus' ),
                    'mobile_menu' => esc_html__( 'Mobile Menu', 'urus' ),
                    'extend_primary' => esc_html__( 'Extend Primary Menu', 'urus' ),
                    'vertical_menu' => esc_html__( 'Vertical Menu', 'urus' ),
                    'top_left_menu' => esc_html__( 'Header Top Left Menu', 'urus' ),
                    'top_right_menu' => esc_html__( 'Header Top Right Menu', 'urus' ),
                )
            );
            // Tell TinyMCE editor to use a custom stylesheet.
            add_editor_style();
            // Support the shortcode in the excerpt
            add_filter( 'the_excerpt', 'shortcode_unautop');
            add_filter( 'the_excerpt', 'do_shortcode');
        }

        public static function ajax_mobile_login(){
            // First check the nonce, if it fails the function will break
            check_ajax_referer( 'urus_ajax_frontend', 'security' );
            // Nonce is checked, get the POST data and sign user on
            // Call auth_user_login
            $info = array();
            $info['user_login'] = $_POST['username'];
            $info['user_password'] =  $_POST['password'];
            $info['remember'] = true;
            $user_signon = wp_signon( $info, '' ); // From false to '' since v4.9
            if ( is_wp_error($user_signon) ){
                if (isset($user_signon->errors['incorrect_password'])) {
                    wp_send_json(
                        array(
                            'loggedin'	=>	false,
                            'message'	=>	sprintf('<div class="alert alert-warning">%s</div>',esc_html__('The password you entered is incorrect','urus'))
                        )
                    );
                }else{
                    wp_send_json(
                        array(
                            'loggedin'	=>	false,
                            'message'	=>	sprintf('<div class="alert alert-warning">%s</div>',esc_html__('The username you entered is incorrect','urus'))
                        )
                    );
                }
            } else {
                wp_set_current_user($user_signon->ID);
                $link_items = '<div class="user_links">';
                foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
                    $link_items .= '<li class="'.wc_get_account_menu_item_classes( $endpoint );
                    $link_items .= ($endpoint === 'customer-logout' ? ' ajax-log-out' : '');
                    $link_items .= '"><a href="'.esc_url( wc_get_account_endpoint_url( $endpoint ) ).'">'.esc_html( $label ).'</a></li>';
                endforeach;
                $link_items .= '</div>';
                wp_send_json(
                    array(
                        'loggedin'	=>	true,
                        'message'	=>	$link_items
                    )
                );

            }
            wp_die();
        }

        public static function get_theme_layout_support(){
            return apply_filters('urus_theme_layout_support', array(
                'single_layout' => array(
                    'gallery' => array(
                        'hook' => 'gallery',
                        'zoom' => false,
                    ),
                    'gallery2' => array(
                        'hook' => 'gallery',
                        'zoom' => false,
                    ),
                    'list_gallery' => array(
                        'hook' => 'gallery',
                        'zoom' => false,
                    ),
                    'list' => array(
                        'hook' => 'list',
                        'zoom' => false,
                    ),
                    'large' => array(
                        'hook' => 'large',
                        'zoom' => false,
                    ),
                    'special_gallery' => array(
                        'hook' => 'special_gallery',
                        'zoom' => false,
                    ),
                    'special_slider' => array(
                        'hook' => 'special_slider',
                        'zoom' => false,
                    ),
                    'special_centered_slider' => array(
                        'hook' => 'special_centered_slider',
                        'zoom' => false,
                    )
                )
            ));
        }

        public static  function body_class( $classes ){
	        $classes[] = sanitize_title(URUS_THEME_NAME);
            $classes[] = sanitize_title(URUS_THEME_NAME . "-" . URUS_THEME_VERSION);
            if( is_page()){
                if ($page_extra_class = Urus_Helper::get_post_meta(get_the_ID(),'page_extra_class','') != '') {
                $classes[] = $page_extra_class;
                }
            }
            if( class_exists('WooCommerce')){
                if( is_shop()){
                    $woo_shop_page_used_layout = Urus_Helper::get_option('woo_shop_page_used_layout','vertical');
                    $classes[] = 'shop-page-layout-'.$woo_shop_page_used_layout;
                }
            }
            $enable_variation_loop_product = Urus_Helper::get_option('enable_variation_loop_product',0);
            if($enable_variation_loop_product == 1){
                $classes[] = 'enable-variation-loop-product';
            }
            $boxed_layout = Urus_Helper::get_option('boxed_layout',0);
            if( $boxed_layout == 1){
                $classes[] = 'boxed';
            }
            $woo_shop_filter_display= Urus_Helper::get_option('woo_shop_filter_display','top');
            $classes[] = 'shop-filter-display-'.$woo_shop_filter_display;
            if( Urus_Mobile_Detect::isMobile()){
                $classes[] = 'is-mobile';
            }

            $enable_boxed = Urus_Helper::get_option('enable_boxed','0');
            $blog_heading_style = Urus_Helper::get_option('blog_heading_style','banner');
            if( is_page()){
                $page_heading_style = get_post_meta(get_the_ID(),'page_heading_style',true);
                if ($page_heading_style != '') {
                    $blog_heading_style =  $page_heading_style;
                }
            }
            if( class_exists('WooCommerce')){
                if( !is_shop() && !is_product_category() &&  !is_single() && !is_page_template('templates/fullwidth.php') && !is_page_template('templates/fullwidth-normal.php') && !is_404() && !$enable_boxed){
                    $classes[] = 'blog-heading-'.$blog_heading_style;
                }
            }else{
                $classes[] = 'blog-heading-'.$blog_heading_style;
            }
            $enable_boxed = Urus_Helper::get_option('enable_boxed',0);
            if( $enable_boxed ==1){
                $classes[] ='boxed';
            }

            if( class_exists('YITH_WC_Catalog_Mode')){
                $classes[] ='urus_catalog_mode';
            }


            return $classes;
        }

        public static function register_sidebar_widgets(){
            register_sidebar( array(
                    'name'          => esc_html__( 'Widget Area', 'urus' ),
                    'id'            => 'widget-area',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle"><span class="text">',
                    'after_title'   => '</span></h6>',
                )
            );
            register_sidebar( array(
                    'name'          => esc_html__( 'Shop Widget Area', 'urus' ),
                    'id'            => 'shop-widget-area',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle"><span class="text">',
                    'after_title'   => '</span></h6>',
                )
            );
            register_sidebar( array(
                    'name'          => esc_html__( 'Product Widget Area', 'urus' ),
                    'id'            => 'product-widget-area',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle"><span class="text">',
                    'after_title'   => '</span></h6>',
                )
            );
            register_sidebar( array(
                    'name'          => esc_html__( 'Dropdown Filter', 'urus' ),
                    'id'            => 'dropdown_filter',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widget col-sm-15 %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle"><span class="text">',
                    'after_title'   => '</span></h6>',
                )
            );
            register_sidebar( array(
                    'name'          => esc_html__( 'Step Filter', 'urus' ),
                    'id'            => 'step_filter',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widget col-sm-15 %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle"><span class="text">',
                    'after_title'   => '</span></h6>',
                )
            );
            register_sidebar( array(
                    'name'          => esc_html__( 'Drawer Filter ', 'urus' ),
                    'id'            => 'drawer_filter',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle"><span class="text">',
                    'after_title'   => '</span></h6>',
                )
            );
            register_sidebar( array(
                    'name'          => esc_html__( 'Accordion Filter ', 'urus' ),
                    'id'            => 'accordion_filter',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widget filter-widget-item %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle">',
                    'after_title'   => '</h6>',
                )
            );
            register_sidebar( array(
                    'name'          => esc_html__( 'Accordion Filter All ', 'urus' ),
                    'id'            => 'accordion_filter_all',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widegt filter-widget-item %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle">',
                    'after_title'   => '</h6>',
                )
            );
            register_sidebar( array(
                    'name'          => esc_html__( 'Single Extra Sidebar', 'urus' ),
                    'id'            => 'product-extra-sidebar',
                    'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'urus' ),
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h6 class="widgettitle"><span class="text">',
                    'after_title'   => '</span></h6>',
                )
            );
            register_sidebar( array(
                'id'            => 'filter-mobile',
                'name'          => esc_html__( 'Filter for mobile Shop', 'urus' ),
                'description'   => esc_html__( 'Add widgets here.', 'urus' ),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h3 class="widgettitle">',
                'after_title'   => '</h3>',
            ) );

            $extend_sidebar = Urus_Helper::get_option('extend_sidebar',array());
            if( !empty($extend_sidebar)){
                foreach ($extend_sidebar as $key => $value){
                    if( $value!=''){
                        $key = sanitize_title($value).'_'.$key;
                        register_sidebar( array(
                            'id'            => $key,
                            'name'          => $value,
                            'description'   => esc_html__( 'Add widgets here.', 'urus' ),
                            'before_widget' => '<section id="%1$s" class="widget %2$s">',
                            'after_widget'  => '</section>',
                            'before_title'  => '<h3 class="widgettitle">',
                            'after_title'   => '</h3>',
                        ) );
                    }
                }
            }

        }

        public static function add_widget_list(){
            $widgets = array(
              'no_required' => array('Urus_Widgets_Lastest_Post'),
              'WooCommerce'  => array(
                  'Urus_Widgets_Filter_Attribute',
                  'Urus_Widgets_Filter_Price',
                  'Urus_Widgets_Filter_Orderby',
                  'Urus_Widgets_Filter_Category',
                  'Urus_Widgets_Products',
                  'Urus_Widgets_Newsletter',
                  'Urus_Widgets_Instagram',
                  'Urus_Widgets_Featured_Box',
                  'Urus_Widgets_Product_Brand',
                  'Urus_Widgets_Active_Filters'
              )
            );
            return $widgets;
        }

        public static function header_message(){
            $enable_header_message = Urus_Helper::get_option('enable_header_message',0);
            $header_message_text = Urus_Helper::get_option('header_message_text','');
            if( $enable_header_message ==1 && $header_message_text != ''){
                ?>
                <div class="urus-header-message"><?php echo Urus_Helper::escaped_html($header_message_text);?></div>
                <?php
            }
        }
        public static function header_control(){
            if(has_nav_menu('control_menu')){
                wp_nav_menu( array(
                    'menu'            => 'control_menu',
                    'theme_location'  => 'control_menu',
                    'container'       => '',
                    'container_class' => '',
                    'container_id'    => '',
                    'depth' => 2,
                    'menu_class'      => 'header-control-menu urus-nav cl',

                ));
            }
        }
        public static function header_left_control(){
            if(has_nav_menu('control_left_menu')){
                wp_nav_menu( array(
                    'menu'            => 'control_left_menu',
                    'theme_location'  => 'control_left_menu',
                    'container'       => '',
                    'container_class' => '',
                    'container_id'    => '',
                    'depth' => 2,
                    'menu_class'      => 'header-control-menu left urus-nav cl',

                ));
            }
        }

        public static function control_menu_items($items, $args){
            if ( $args->theme_location == 'control_menu') {
                $used_header = Urus_Helper::get_option('used_header','default');
                switch ($used_header) {
                    case 'logo_in_menu':
                        $items.= Urus_Pluggable_Wcml::wpml_currency_switcher();
                        $items.= Urus_Pluggable_WooCommerce::wishlist_link();
                        $items.= Urus_Pluggable_WooCommerce::header_cart_menu();
                    break;
                    case 'logo_on_menu':
                        $items.= Urus_Pluggable_WooCommerce::search_link(true);
                        $items.= Urus_Pluggable_WooCommerce::userlink();
                        $items.= Urus_Pluggable_WooCommerce::wishlist_link();
                        $items.= Urus_Pluggable_WooCommerce::header_cart_menu();
                        break;
                    case 'logo_center':
                        $items.= Urus_Pluggable_Wcml::wpml_currency_switcher();
                        $items.= Urus_Pluggable_WooCommerce::search_link(true);
                        $items.= Urus_Pluggable_WooCommerce::userlink();
                        $items.= Urus_Pluggable_WooCommerce::wishlist_link();
                        $items.= Urus_Pluggable_WooCommerce::header_cart_menu();
                        break;
                    case 'full_search':
                        $items.= Urus_Pluggable_Wcml::wpml_currency_switcher();
                        $items.= Urus_Pluggable_WooCommerce::userlink();
                        $items.= Urus_Pluggable_WooCommerce::wishlist_link();
                        $items.= Urus_Pluggable_WooCommerce::header_cart_menu();
                        break;
                    default:
                        $items.= Urus_Pluggable_Wcml::wpml_currency_switcher();
                        $items.= Urus_Pluggable_WooCommerce::search_link(true);
                        $items.= Urus_Pluggable_WooCommerce::userlink();
                        $items.= Urus_Pluggable_WooCommerce::wishlist_link();
                        $items.= Urus_Pluggable_WooCommerce::header_cart_menu();
                }

            }elseif ( $args->theme_location == 'control_left_menu') {
                    $used_header = Urus_Helper::get_option('used_header','default');
                    switch ($used_header) {
                        case 'logo_in_menu':
                            $items.= Urus_Pluggable_WooCommerce::search_link(true);
                            $items.= Urus_Pluggable_WooCommerce::userlink();
                            break;
                        default:
                            break;
                    }
            }
            return $items;
        }
        public static function control_left_menu_items($items, $args){
            if ( $args->theme_location == 'control_left_menu') {
                $used_header = Urus_Helper::get_option('used_header','default');
                switch ($used_header) {
                    case 'logo_in_menu':
                        $items.= Urus_Pluggable_WooCommerce::search_link(true);
                        $items.= Urus_Pluggable_WooCommerce::userlink();
                        break;

                    default:
                }
            }
            return $items;
        }

        public static function jpeg_quality($arg){
            return 100;
        }


    }
}
