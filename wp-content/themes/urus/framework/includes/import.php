<?php
if( !class_exists('familabblank_Import')){
    class  Urus_Import{
        protected static $initialized = false;
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            add_filter( 'familab_core_import_demos', array( __CLASS__, 'import_demos' ),999 );
            add_filter( 'familab_core_import_maps_required', array( __CLASS__, 'import_maps_required' ),999 );
            add_action('urus_sample_data_tab',array(__CLASS__,'display_import'));
            self::$initialized = true;
        }
        public static function import_demos(){
            $theme = wp_get_theme(URUS_THEME_SLUG);
            return array(
                'fashion'  => array(
                    'screenshot'  => 'https://urus.familab.net/files/fashion.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Fashion Demo', 'urus' ),
                    'description' => esc_html__( 'This is a fashion import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'fashion',
                    'sliders' => array('home_modern','home_categories','home_elegant','home_classic','home_stylish','home_bestseller','home_metro','home_banner'),
                    'homes'   => array(
                        'fashion-modern' => array(
                            'name' => 'Home - Modern',
                            'slug' => 'home-modern',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_modern.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_in_menu',
                                'footer_used' => 5318,
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                                'enable_sticky_header' => 1,
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-elegant' => array(
                            'name' => 'Home - Elegant',
                            'slug' => 'home-elegant',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_elegant.jpg',
                            'settings' => array(
                                'used_header'  => 'default',
                                'footer_used' => 5411,
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                                'logo_padding_top' => 22,
                                'logo_padding_bottom' => 22,
                                'enable_sticky_header' => 1,
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-categories' => array(
                            'name' => 'Home - Categories',
                            'slug' => 'home-categories',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_categories.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_on_menu',
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'footer_used' => 5572,
                                'enable_sticky_header' => 0,
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-bestseller' => array(
                            'name' => 'Home - Bestseller',
                            'slug' => 'home-bestseller',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_bestseller.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_center',
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                                'footer_used' => 6285,
                                'logo_padding_top' => 22,
                                'logo_padding_bottom' => 22,
                                'enable_sticky_header' => 1,
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-classic' => array(
                            'name' => 'Home - Classic',
                            'slug' => 'home-classic',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_classic.jpg',
                            'settings' => array(
                                'used_header'  => 'bar_menu',
                                'footer_used' => 5849,
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                                'logo_padding_top' => 22,
                                'logo_padding_bottom' => 22,
                                'enable_sticky_header' => 1,
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-collection' => array(
                            'name' => 'Home - Collection',
                            'slug' => 'home-collection',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_collection.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_in_menu',
                                'footer_used' => 6233,
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                                'logo_padding_top' => 22,
                                'logo_padding_bottom' => 22,
                                'enable_sticky_header' => 1,
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-stylish' => array(
                            'name' => 'Home - Stylish',
                            'slug' => 'home-stylish',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_stylish.jpg',
                            'settings' => array(
                                'used_header'  => 'default',
                                'footer_used' => 5838,
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                                'logo_padding_top' => 22,
                                'logo_padding_bottom' => 22,
                                'enable_sticky_header' => 1,
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-clean' => array(
                            'name' => 'Home - Clean',
                            'slug' => 'home-clean',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_clean.jpg',
                            'settings' => array(
                                'used_header'  => 'menu_center',
                                'footer_used' => 6178,
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                                'logo_padding_top' => 22,
                                'logo_padding_bottom' => 22,
                                'enable_sticky_header' => 1,
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-metro' => array(
                            'name' => 'Home - Metro',
                            'slug' => 'home-metro',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_metro.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_center',
                                'footer_used' => 6495,
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                                'logo_padding_top' => 22,
                                'logo_padding_bottom' => 22,
                                'enable_sticky_header' => 1,
                                'enable_header_promo' =>0,
                            )
                        ),
                        'fashion-banner' => array(
                            'name' => 'Home - Banner',
                            'slug' => 'home-banner',
                            'package_key' => 'fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_banner.jpg',
                            'settings' => array(
                                'used_header'  => 'default',
                                'footer_used' => 6450,
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                                'enable_header_promo' =>1,
                                'promo_text' => 'FREE UK DELIVERY* FIND OUT MORE',
                                'header_promo_bg_color' => '#1e56c6',
                                'promo_height' => 97,
                                'logo_padding_top' => 22,
                                'logo_padding_bottom' => 22,
                                'enable_sticky_header' => 1,
                            )
                        ),
                    )
                ),
                'jewelry' => array(
                    'screenshot'  => 'https://urus.familab.net/files/jewelry.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Jewelry Demo', 'urus' ),
                    'description' => esc_html__( 'This is a jewelry import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'jewelry',
                    'sliders' => array('jewelry_boxed','jewelry_stylish','jewelry_modern'),
                    'homes'   => array(
                        'jewelry-boxed' => array(
                            'name' => 'Home - Boxed',
                            'slug' => 'home-boxed',
                            'package_key' => 'jewelry',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/jewelry_boxed.jpg',
                            'settings' => array(
                                'enable_boxed' =>1,
                                'enable_header_promo' =>1,
                                'promo_text' => '<div class="text-u" style="font-size: 10px;color: #000;">FREE BOLD EAR CUFF WITH EVERY ORDER OVER $300 THIS WEEKEND ONLY</div>',
                                'header_promo_bg_color' => '#f8dbd3',
                                'promo_height' => 44,
                                'used_header'  => 'menu_dark',
                                'header_dark' => 0,
                                'logo_padding_top' => 18,
                                'logo_padding_bottom' => 18,
                                'main_color' => "#b97563",
                                'body_background' => array(
                                    'background-color' =>'#efefef',
                                    'background-repeat' => 'no-repeat',
                                    'background-size' => 'cover',
                                    'background-position' =>'top left',
                                    'background-image' => 'https://urus.familab.net/jewelry/wp-content/uploads/sites/2/2019/04/Jewelry-Boxed-background.jpg',
                                    'media' => array(
                                        'id'        =>'',
                                        'height'    =>'',
                                        'width'     =>'',
                                        'thumbnail' =>''
                                    )
                                )
                            )
                        ),
                        'jewelry-modern' => array(
                            'name' => 'Home - Modern',
                            'slug' => 'home-modern',
                            'package_key' => 'jewelry',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/jewelry_modern.jpg',
                            'settings' => array(
                                'enable_boxed' =>0,
                                'used_header'  => 'logo_on_menu',
                                'footer_used' => 169,
                                'logo_padding_top' => 30,
                                'logo_padding_bottom' => 30,
                                'enable_header_promo' =>1,
                                'promo_text' => '<div class="text-u" style="font-size: 11px;color: #fff;">Enjoy sale on selected items </div>',
                                'header_promo_bg_color' => '#d8c0b4',
                                'promo_height' => 50,
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                                'main_color' => '#d9b198'
                            )
                        ),
                        'jewelry-stylish' => array(
                            'name' => 'Home - Stylish',
                            'slug' => 'home-stylish',
                            'package_key' => 'jewelry',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/jewelry_stylish.jpg',
                            'settings' => array(
                                'enable_boxed' =>0,
                                'footer_used' => 228,
                                'used_header'  => 'default',
                                'main_color' =>'#dfb7ab'
                            )
                        ),
                    )
                ),
                'furniture' => array(
                    'screenshot'  => 'https://urus.familab.net/files/furniture.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Furniture Demo', 'urus' ),
                    'description' => esc_html__( 'This is a furniture import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'furniture',
                    'sliders' => array('furniture_fullscreen','furniture_modern','furniture_minimal'),
                    'homes'   => array(
                        'furniture-fullscreen' => array(
                            'name' => 'Home - Fullscreen',
                            'slug' => 'home-fullscreen',
                            'package_key' => 'furniture',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/furniture_fullscreen.jpg',
                            'settings' => array(
                                'used_header'  => 'menu_center',
                                'footer_used' => 18,
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                            )
                        ),
                        'furniture-minimal' => array(
                            'name' => 'Home - Minimal',
                            'slug' => 'home-minimal',
                            'package_key' => 'furniture',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/funiture_minimal.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_in_menu',
                                'footer_used' => 138,
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'main_color' => '#fc1111',
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg',
                                ),
                            )
                        ),
                        'furniture-modern' => array(
                            'name' => 'Home - Modern',
                            'slug' => 'home-modern',
                            'package_key' => 'furniture',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/funiture_modern.jpg',
                            'settings' => array(
                                'used_header' => 'full_search',
                                'footer_used' => 206,
                                'enable_header_promo' =>1,
                                'promo_text' => '<div style="color: #000;font-size: 20px;letter-spacing: 0;font-weight: 500;">Let’s make home your happy place <span class="urus-icon-next-5" style="font-size: 16px; vertical-align: middle; margin-left: 6px;"></span></div>',
                                'promo_height' => 86,
                                'header_dark' => 0,
                                'header_promo_bg_img' => array(
                                    'url' =>'https://urus.familab.net/funiture/wp-content/uploads/sites/3/2019/03/promo-banner.jpg'
                                ),
                                'logo_padding_top' => 30,
                                'logo_padding_bottom' => 30,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo.svg'
                                ),
                            )
                        ),
                    )
                ),
                'food'=>  array(
                    'screenshot'  => 'https://urus.familab.net/files/food-drink.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Food & Drink Demo', 'urus' ),
                    'description' => esc_html__( 'This is a food & drink import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'food',
                    'sliders' => array('food_home3','food_coffe'),
                    'homes'   => array(
                        'food-ballery' => array(
                            'name' => 'Bakery',
                            'slug' => 'home-ballery',
                            'package_key' => 'food',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/ballery.jpg',
                            'settings' => array(
                                'used_header' => 'logo_center',
                                'footer_used' => 140,
                                'header_promo_bg_color' => '',
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                                'main_color' => '#976135'
                            )
                        ),
                        'food-coffee' => array(
                            'name' => 'Coffee',
                            'slug' => 'coffee',
                            'package_key' => 'food',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/coffee.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_in_menu',
                                'footer_used' => 311,
                                'header_promo_bg_color' => '',
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'main_color' => '#9c775c'
                            )
                        ),
                        'food-food' => array(
                            'name' => 'Food',
                            'slug' => 'food',
                            'package_key' => 'food',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/food_style2.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_on_menu',
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'enable_header_promo' =>1,
                                'promo_text' => '<div style="color: #fff;font-size: 11px; letter-spacing: 2px;font-weight: 700; text-transform: uppercase;">DELIVERY 3-7 WORKING DAYS  FREE SHIPPING OVER £29</div>',
                                'header_promo_bg_color' => '#1e1e1e',
                                'main_color' => '#c24511',
                                'promo_height' => 47,
                                'header_dark' => 0,
                                'footer_used' => 405,
                            )
                        ),
                    )
                ),
                'glasses'=>  array(
                    'screenshot'  => 'https://urus.familab.net/files/glasses.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Glasses Demo', 'urus' ),
                    'description' => esc_html__( 'This is a glasses import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'glasses',
                    'sliders' => array('glasses_minimal','glasses_modern'),
                    'homes'   => array(
                        'glasses-minimal' => array(
                            'name' => 'Glasses Minimal',
                            'slug' => 'glasses-minimal',
                            'package_key' => 'glasses',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/glasses_minimal.jpg',
                            'settings' => array(
                                'used_header' => 'logo_center',
                                'footer_used' => 177,
                                'enable_header_promo' =>1,
                                'promo_text' => '<div style="color: #fff;font-size: 16px; letter-spacing: 0;font-weight: 400;">New Spring Looks - BOGO from $19</div>',
                                'header_promo_bg_color' => '#131313',
                                'promo_height' => 48,
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 30,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus//assets/images/logo-text.svg'
                                ),
                                'main_color' => '#44b2c2',
                            )
                        ),
                        'glasses-modern' => array(
                            'name' => 'Modern',
                            'slug' => 'modern',
                            'package_key' => 'glasses',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/glasses_modern.jpg',
                            'settings' => array(
                                'used_header' => 'logo_center',
                                'footer_used' => 247,
                                'logo_padding_top' => 30,
                                'logo_padding_bottom' => 35,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus//assets/images/logo-text.svg'
                                ),
                            )
                        ),
                    )
                ),
                'bag'=> array(
                    'screenshot'  => 'https://urus.familab.net/files/bag.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Bag & Drink Demo', 'urus' ),
                    'description' => esc_html__( 'This is a bag import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'bag',
                    'sliders' => array('bag_classic','bag_metro','bag_modern'),
                    'homes'   => array(
                        'bag-classic' => array(
                            'name' => 'Bag Classic',
                            'slug' => 'bag-classic',
                            'package_key' => 'bag',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/bag_classic.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_on_menu_line',
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 17,
                                'main_color' => '#fc1111',
                                'enable_header_promo' =>1,
                                'promo_text' => '<div style="color: #fff;font-size: 14px; font-weight: 400; ">Free international shipping starts on orders of €100+</div>',
                                'header_promo_bg_color' => '#171717',
                                'promo_height' => 49,
                                'header_dark' => 0,
                                'footer_used' => 82,
                                'enable_sticky_header' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus//assets/images/logo.svg'
                                ),
                            )
                        ),
                        'bag-metro' => array(
                            'name' => 'Bag Metro',
                            'slug' => 'bag-metro',
                            'package_key' => 'bag',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/bag_metro.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_in_menu_line',
                                'footer_used' => 311,
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 20,
                                'main_color' => '#fc1111',
                                'enable_header_promo' =>1,
                                'promo_text' => '<div style="color: #fff;font-size: 14px; font-weight: 400; ">Free international shipping starts on orders of €100+</div>',
                                'header_promo_bg_color' => '#232529'
                            )
                        ),
                        'bag-modern' => array(
                            'name' => 'Bag Modern',
                            'slug' => 'bag-modern',
                            'package_key' => 'bag',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/bag_modern.jpg',
                            'settings' => array(
                                'used_header' => 'logo_center',
                                'footer_used' => 139,
                                'enable_header_promo' =>1,
                                'main_color' => '#fc1111',
                                'promo_text' => '<div style="color: #fff;font-size: 14px; font-weight: 400; ">Free international shipping starts on orders of €100+</div>',
                                'header_promo_bg_color' => '#171717',
                                'promo_height' => 49,
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus//assets/images/logo.svg'
                                ),
                            )
                        ),
                    )
                ),
                'shoes' => array(
                    'screenshot'  => 'https://urus.familab.net/files/shoes.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Shoes Demo', 'urus' ),
                    'description' => esc_html__( 'This is a shoes import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'shoes',
                    'sliders' => array('shoes_modern','shoes_vintage'),
                    'homes'   => array(
                        'shoes-modern' => array(
                            'name' => 'Home - modern',
                            'slug' => 'home-modern',
                            'package_key' => 'shoes',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/shoes_modern.jpg',
                            'settings' => array(
                                'enable_header_promo' => 1,
                                'promo_text' => '<div class="text-u" style="font-size: 14px;color: #ffffff;">Free international shipping starts on orders of €100+</div>',
                                'header_promo_bg_color' => '#171717',
                                'promo_height' => 48,
                                'used_header'  => 'menu_center',
                                'header_dark' => 0,
                                'logo_padding_top' => 20,
                                'logo_padding_bottom' => 20,
                                'main_color' => "#ff2929",
                                'footer_used' => 160,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/shoes/wp-content/uploads/sites/8/2019/04/logo-shoes.svg'
                                ),
                            )
                        ),
                        'shoes-vintage' => array(
                            'name' => 'Shoes - vintage',
                            'slug' => 'shoes-vintage',
                            'package_key' => 'shoes',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/shose_vintage.jpg',
                            'settings' => array(
                                'enable_header_promo' => 1,
                                'promo_text' => '<div class="text-u" style="font-size: 14px;color: #ffffff;">Free international shipping starts on orders of €100+</div>',
                                'header_promo_bg_color' => '#171717',
                                'promo_height' => 48,
                                'used_header'  => 'logo_center',
                                'header_dark' => 0,
                                'logo_padding_top' => 30,
                                'logo_padding_bottom' => 30,
                                'footer_used' => 217,
                                'main_color' => "#7a6b64",
                                'price_sale_color' => "#232529",
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/shoes/wp-content/uploads/sites/8/2019/04/logo-vintage.svg'
                                ),
                                'body_background' => array(
                                    'background-color' =>'',
                                    'background-repeat' => 'no-repeat',
                                    'background-size' => 'cover',
                                    'background-position' =>'bottom',
                                    'background-image' =>'https://urus.familab.net/shoes/wp-content/uploads/sites/8/2019/04/Shose-Vintage.jpg',
                                    'media' => array(
                                        'id'        =>'',
                                        'height'    =>'',
                                        'width'     =>'',
                                        'thumbnail' =>''
                                    )
                                ),
                                'opt_typography_body_font' => array(
                                    'font-family' =>'Crimson Text',
                                    'font-options' =>'',
                                    'google' => 1,
                                    'font-weight' => '400',
                                    'font-style' => 'latin',
                                    'text-align' =>'',
                                    'font-size' => '',
                                    'line-height' =>'',
                                    'color' =>''
                                )
                            )
                        ),
                    )
                ),
                'retails' => array(
                    'screenshot'  => 'https://urus.familab.net/files/retails.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Retails Demo', 'urus' ),
                    'description' => esc_html__( 'This is a retails import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'retails',
                    'sliders' => array('retails_slider'),
                ),
                'marketplace'=>array(
                    'screenshot'  => 'https://urus.familab.net/files/marketplace.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Marketplace Demo', 'urus' ),
                    'description' => esc_html__( 'This is a marketplace import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'marketplace',
                ),
                'e_furniture' => array(
                    'screenshot'  => 'https://urus.familab.net/files/furniture.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Elementor Furniture Demo', 'urus' ),
                    'description' => esc_html__( 'This is a furniture import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'e_furniture',
                    'sliders' => array('furniture_fullscreen','furniture_modern','furniture_minimal'),
                    'builder' => 'elementor',
                    'homes'   => array(
                        'e_fur_fullscreen' => array(
                            'name' => 'Furniture – Full Screen',
                            'slug' => 'furniture-full-screen',
                            'package_key' => 'e_furniture',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/furniture_fullscreen.jpg',
                            'settings' => array(
                                'enable_header_promo' => 0,
                                'used_header'  => 'menu_center',
                                'footer_used' => 18,
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                            )
                        ),
                        'e_fur_minimal' => array(
                            'name' => 'Furniture – Minimal',
                            'slug' => 'furniture-minimal',
                            'package_key' => 'e_furniture',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/funiture_minimal.jpg',
                            'settings' => array(
                                'enable_header_promo' => 0,
                                'used_header'  => 'logo_in_menu',
                                'footer_used' => 138,
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'main_color' => '#fc1111',
                                'header_dark' => 0,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg',
                                ),
                            )
                        ),
                        'e_fur_modern' =>array(
                            'name' => 'Furniture – Modern',
                            'slug' => 'furniture-modern',
                            'package_key' => 'e_furniture',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/funiture_modern.jpg',
                            'settings' => array(
                                'used_header' => 'full_search',
                                'footer_used' => 206,
                                'enable_header_promo' =>1,
                                'promo_text' => '<div style="color: #000;font-size: 20px;letter-spacing: 0;font-weight: 500;">Let’s make home your happy place <span class="urus-icon-next-5" style="font-size: 16px; vertical-align: middle; margin-left: 6px;"></span></div>',
                                'promo_height' => 86,
                                'header_dark' => 0,
                                'header_promo_bg_img' => array(
                                    'url' =>'https://urus.familab.net/funiture/wp-content/uploads/sites/3/2019/03/promo-banner.jpg'
                                ),
                                'logo_padding_top' => 30,
                                'logo_padding_bottom' => 30,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo.svg'
                                ),
                            )
                        ),
                    )
                ),
                'e_fashion' => array(
                    'screenshot'  => 'https://urus.familab.net/files/fashion.jpg',
                    'featch_attachment' => true,
                    'name'        => $theme->name . esc_html__( ' - Elementor Fashion Demo', 'urus' ),
                    'description' => esc_html__( 'This is a fashion import package. Once installed, everything looks like our demo. However, it is large and takes a lot of time.',
                        'urus' ),
                    'package_file'         => 'e_fashion',
                    'sliders' => array('home_elegant','home_modern','home_categories','home_banner','home_bestseller','home_classic','home_metro','home_stylish'),
                    'builder' => 'elementor',
                    'homes'   => array(
                        'e_fashion_modern' => array(
                            'name' => 'Fashion - Modern',
                            'slug' => 'fashion-modern',
                            'package_key' => 'e_fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_modern.jpg',
                            'settings' => array(
                                'theme_use_footer_builder' => 1,
                                'used_header'              => 'logo_in_menu',
                                'footer_used'              => 471,
                                'logo_padding_top'         => 25,
                                'logo_padding_bottom'      => 25,
                                'header_dark' => 0,
                                'logo'        => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),

                            ),
                        ),
                        'e_fashion_elegant' => array(
                            'name' => 'Fashion - Elegant',
                            'slug' => 'fashion-elegant',
                            'package_key' => 'e_fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_elegant.jpg',
                            'settings' => array(
                                'used_header' => 'default',
                                'footer_used' => 513,
                                'header_dark' => 0,
                                'logo'        => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                            )
                        ),
                        'e_fashion_categories' => array(
                            'name' => 'Fashion - Categories',
                            'slug' => 'fashion-categories',
                            'package_key' => 'e_fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_categories.jpg',
                            'settings' => array(
                                'used_header'         => 'logo_on_menu',
                                'logo_padding_top'    => 25,
                                'logo_padding_bottom' => 25,
                                'footer_used'         => 544,
                                'header_dark' => 0,
                                'logo'        => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                            )
                        ),
                        'e_fashion_bestseller' => array(
                            'name' => 'Fashion - Bestseller',
                            'slug' => 'fashion-bestseller',
                            'package_key' => 'e_fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_bestseller.jpg',
                            'settings' => array(
                                'used_header' => 'logo_center',
                                'header_dark' => 1,
                                'logo'        => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                                'footer_used' => 584,
                            )
                        ),
                        'e_fashion_classic' => array(
                            'name' => 'Fashion -  Classic',
                            'slug' => 'fashion-classic',
                            'package_key' => 'e_fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_classic.jpg',
                            'settings' => array(
                                'theme_use_footer_builder' => 1,
                                'used_header'              => 'bar_menu',
                                'footer_used'              => 623,
                                'header_dark' => 0,
                                'logo'        => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                            )
                        ),
                        'e_fashion_stylish' => array(
                            'name' => 'Fashion - Stylish',
                            'slug' => 'fashion-stylish',
                            'package_key' => 'e_fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_stylish.jpg',
                            'settings' => array(
                                'used_header'  => 'default',
                                'footer_used' => 783,
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                )
                            )
                        ),
                        'e_fashion_clean' => array(
                            'name' => 'Fashion -  Clean',
                            'slug' => 'fashion-clean',
                            'package_key' => 'e_fashion',
                            'thumbnail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_clean.jpg',
                            'settings' => array(
                                'used_header'  => 'menu_center',
                                'footer_used' => 874,
                                'header_dark' => 0,
                                'logo'        => array(
                                    'url' => 'https://urus.familab.net/wp-content/themes/urus/assets/images/logo.svg'
                                ),
                            )
                        )
                    )
                ),
            );
        }

        public static function import_maps_required(){
            return array(
                'media' => array('logo','header_promo_bg_img','logo_mobile','custom_placeholder_url'),
                'background' => array('body_background','page_heading_background','blog_heading_background','shop_heading_background','shop_background','single_product_background_type'),
                'post' => array('footer_used','popup_used','popup_pages_display'),
            );
        }

        public static function display_import(){
            if (class_exists('Familab_Core_Import')){
                $demo = self::import_demos();
                $importer = new Familab_Core_Import();
                $importer->settup_import_data($demo);
                echo e_data($importer->display_intro_step(false));
            }
        }
    }
}
