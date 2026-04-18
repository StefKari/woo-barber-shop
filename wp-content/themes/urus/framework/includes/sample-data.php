<?php

if (!class_exists('Urus_Sample_Data')){
    class Urus_Sample_Data{
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
            
            if( !class_exists('Import_Sample_Data')) return;
            
            // Filter Sample Data Menu
            add_filter('import_sample_data_packages',array(__CLASS__,'import_sample_data_packages'));
            add_filter('import_sample_data_required_plugins',array(__CLASS__,'import_sample_data_required_plugins'));
            add_filter('import_sample_data_demo_site_pattern',array(__CLASS__,'import_sample_data_demo_site_pattern'));
            add_filter('import_sample_data_theme_option_key',array(__CLASS__,'import_sample_data_theme_option_key'));
            add_filter('import_sample_data_reserved_options',array(__CLASS__,'import_sample_data_reserved_options'));
            
            add_action('import_sample_data_after_install_sample_data',array(__CLASS__,'import_sample_data_after_install_sample_data'),10,1);
            
            
            add_action('urus_sample_data_tab','Import_Sample_Data_Dashboard::display_packages');
            
            
            self::$initialized = true;
        }
        
        public static function import_sample_data_demo_site_pattern( $demo_site_pattern ){
            
            $demo_site_pattern = 'https?(%3A|:)[%2F\\\\/]+(rc|demo|urus)\.familab\.net';
            return $demo_site_pattern;
        }
        public static function import_sample_data_theme_option_key( $theme_option_key){
            $theme_option_key = 'urus';
            return $theme_option_key;
        }
        
        public static function import_sample_data_required_plugins( $plugins ){
            $plugins = array();
            if( class_exists('Urus_Plugins')){
                $plugins = Urus_Plugins::$plugins;
            }
            $plugins = array_values($plugins);
            return $plugins;
        }
        
        
        public static function import_sample_data_packages( $packages ){
            return array(
                'fashion' => array(
                    'id'            => 'fashion',
                    'name'          => 'Fashion (10 Home Page)',
                    'thumbail'      => 'https://urus.familab.net/files/fashion.jpg',
                    'demo'          =>'https://urus.familab.net',
                    'download'      => 'https://urus.familab.net/files/fashion.zip',
                    'tags'          => array('all','fashion'),
                    'main'          => true,
                    'sample-page'   => array(
                        array(
                            'name' => 'Modern',
                            'slug' => 'home-modern',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_modern.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_in_menu',
                                'footer_used' => 5318,
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                            )
                        ),
                        array(
                            'name' => 'Elegant',
                            'slug' => 'home-elegant',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_elegant.jpg',
                            'settings' => array(
                                'used_header'  => 'default',
                                'footer_used' => 5411
                            )
                        ),
                        array(
                            'name' => 'Categories',
                            'slug' => 'home-categories',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_categories.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_on_menu',
                                'logo_padding_top' => 25,
                                'logo_padding_bottom' => 25,
                                'footer_used' => 5572,
                                'enable_sticky_header' => 0
                            )
                        ),
                        array(
                            'name' => 'Bestseller',
                            'slug' => 'home-bestseller',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_bestseller.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_center',
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                ),
                                'footer_used' => 6285
                            )
                        ),
                        array(
                            'name' => 'Classic',
                            'slug' => 'home-classic',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_classic.jpg',
                            'settings' => array(
                                'used_header'  => 'bar_menu',
                                'footer_used' => 5849
                            )
                        ),
                        array(
                            'name' => 'Collection',
                            'slug' => 'home-collection',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_collection.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_in_menu',
                                'footer_used' => 6233,
                            )
                        ),
                        array(
                            'name' => 'Stylish',
                            'slug' => 'home-stylish',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_stylish.jpg',
                            'settings' => array(
                                'used_header'  => 'default',
                                'footer_used' => 5838,
                                'header_dark' => 1,
                                'logo' => array(
                                    'url' => 'https://urus.familab.net/wp-content/uploads/2019/02/logo-w.svg'
                                )
                            )
                        ),
                        array(
                            'name' => 'Clean',
                            'slug' => 'home-clean',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_clean.jpg',
                            'settings' => array(
                                'used_header'  => 'menu_center',
                                'footer_used' => 6178,
                            )
                        ),
                        array(
                            'name' => 'Metro',
                            'slug' => 'home-metro',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_metro.jpg',
                            'settings' => array(
                                'used_header'  => 'logo_center',
                                'footer_used' => 6495
                            )
                        ),
                        array(
                            'name' => 'Banner',
                            'slug' => 'home-banner',
                            'thumbail' =>'https://urus.familab.net/wp-content/plugins/urus-demo/assets/images/fashion_banner.jpg',
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
                                'promo_height' => 97
                            )
                        ),
                        
                    )
                ),
                'jewelry' => array(
                    'id'            => 'jewelry',
                    'name'          => 'Jewelry (3 Home Page)',
                    'thumbail'      => 'https://urus.familab.net/files/jewelry.jpg',
                    'demo'          =>'https://urus.familab.net/jewelry',
                    'download'      => 'https://urus.familab.net/files/jewelry.zip',
                    'tags'          => array('all','jewelry'),
                    'main'          => true,
                    'sample-page'   => array(
                        array(
                            'name' => 'Boxed',
                            'slug' => 'home-boxed',
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
                        array(
                            'name' => 'Modern',
                            'slug' => 'home-modern',
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
                        array(
                            'name' => 'Stylish',
                            'slug' => 'home-stylish',
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
                    'id'            => 'furniture',
                    'name'          => 'Furniture (3 Home Page)',
                    'thumbail'      => 'https://urus.familab.net/files/furniture.jpg',
                    'demo'          =>'https://urus.familab.net/furniture',
                    'download'      => 'https://urus.familab.net/files/furniture.zip',
                    'tags'          => array('all','furniture'),
                    'main'          => true,
                    'sample-page'   => array(
                        array(
                            'name' => 'Fullscreen',
                            'slug' => 'home-fullscreen',
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
                        array(
                            'name' => 'Minimal',
                            'slug' => 'home-minimal',
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
                        array(
                            'name' => 'Modern',
                            'slug' => 'home-modern',
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
                'bag' => array(
                    'id'            => 'bag',
                    'name'          => 'Bag (3 Home Page)',
                    'thumbail'      => 'https://urus.familab.net/files/bag.jpg',
                    'demo'          => 'https://urus.familab.net/bag',
                    'download'      => 'https://urus.familab.net/files/bag.zip',
                    'tags'          => array('all','bag'),
                    'main'          => true,
                    'sample-page'   => array(
                        array(
                            'name' => 'Classic',
                            'slug' => 'bag-classic',
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
                        array(
                            'name' => 'Metro',
                            'slug' => 'bag-metro',
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
                        array(
                            'name' => 'Modern',
                            'slug' => 'bag-modern',
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
                'food-drink' => array(
                    'id'            => 'food-drink',
                    'name'          => 'Food & Drink  (3 Home Page)',
                    'thumbail'      => 'https://urus.familab.net/files/food-drink.jpg',
                    'demo'          =>'https://urus.familab.net/food-drink',
                    'download'      => 'https://urus.familab.net/files/food-drink.zip',
                    'tags'          => array('all','furniture'),
                    'main'          => true,
                    'sample-page'   => array(
                        array(
                            'name' => 'Bakery',
                            'slug' => 'home-ballery',
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
                        array(
                            'name' => 'Coffee',
                            'slug' => 'coffee',
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
                        array(
                            'name' => 'Food',
                            'slug' => 'food',
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
                'glasses' => array(
                    'id'            => 'glasses',
                    'name'          => 'Glasses (2 Home Page)',
                    'thumbail'      => 'https://urus.familab.net/files/glasses.jpg',
                    'demo'          => 'https://urus.familab.net/glasses',
                    'download'      => 'https://urus.familab.net/files/glasses.zip',
                    'tags'          => array('all','glasses'),
                    'main'          => true,
                    'sample-page'   => array(
                        array(
                            'name' => 'Minimal',
                            'slug' => 'glasses-minimal',
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
                        array(
                            'name' => 'Modern',
                            'slug' => 'modern',
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
                
                'shoes' => array(
                    'id'            => 'shoes',
                    'name'          => 'Shoes (2 Home Page)',
                    'thumbail'      => 'https://urus.familab.net/files/shoes.jpg',
                    'demo'          => 'https://urus.familab.net/shoes',
                    'download'      => 'https://urus.familab.net/files/shoes.zip',
                    'tags'          => array('all','shoes'),
                    'main'          => true,
                    'sample-page'   => array(
                        array(
                            'name' => 'Modern',
                            'slug' => 'modern',
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
                        array(
                            'name' => 'Vintage',
                            'slug' => 'shoes-vintage',
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
                    'id'            => 'retails',
                    'name'          => 'Retails',
                    'thumbail'      => 'https://urus.familab.net/files/retails.jpg',
                    'demo'          => 'https://urus.familab.net/retails',
                    'download'      => 'https://urus.familab.net/files/retails.zip',
                    'tags'          => array('all','retails'),
                    'main'          => true,
                    'sample-page'   => array(
        
                    )
                ),
                'marketplace' => array(
                    'id'            => 'marketplace',
                    'name'          => 'Marketplace',
                    'thumbail'      => 'https://urus.familab.net/files/marketplace.jpg',
                    'demo'          => 'https://urus.familab.net/marketplace',
                    'download'      => 'https://urus.familab.net/files/marketplace.zip',
                    'tags'          => array('all','marketplace'),
                    'main'          => true,
                    'sample-page'   => array(
        
                    )
                ),
                //and more...
            );
        }
        
        public static function import_sample_data_reserved_options($reserved_options){
            $reserved_options[]='urus_license_key';
            return $reserved_options;
        }
        
        public static function import_sample_data_after_install_sample_data($package){
            
            // Do something here!
            $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
            $home_url = get_home_url('/');
            
            if (!empty($menus)) {
                foreach ($menus as $menu) {
                    $items = wp_get_nav_menu_items($menu->term_id);
                    if (!empty($items)) {
                        foreach ($items as $item) {
                            $_menu_item_type = get_post_meta($item->ID, '_menu_item_type', true);
                            $_menu_item_url = get_post_meta($item->ID, '_menu_item_url', true);
                            
                            if ($_menu_item_type == 'custom') {
                                $_menu_item_url = str_replace('http://urus.familab.net', $home_url, $_menu_item_url);
                                $_menu_item_url = str_replace('https://urus.familab.net', $home_url, $_menu_item_url);
                                update_post_meta($item->ID, '_menu_item_url', $_menu_item_url);
                                
                            }
                        }
                    }
                }
            }
        }
    }
}