<?php
if( !class_exists('Urus_Settings_Options_Woocommerce')){
    class Urus_Settings_Options_Woocommerce{
        public static function get(  $section = array()){
            $section = array(
                // woo setting
                array(
                    'title'  => esc_html__( 'WooCommerce', 'urus' ),
                    'desc'   => esc_html__( 'WooCommerce Settings', 'urus' ),
                    'icon'   => 'el-icon-shopping-cart',
                    'fields' => array(
                        array(
                            'title'    => esc_html__('Number of days newness','urus'),
                            'id'       => 'woo_newness',
                            'type'     => 'text',
                            'default'  => '7',
                        ),
                        array(
                            'title'    => esc_html__( 'Products perpage', 'urus' ),
                            'id'      => 'woo_products_perpage',
                            'type'    => 'text',
                            'default' => '12',
                            'validate' => 'numeric',
                            'subtitle'    => esc_html__( 'Number of products on shop page', 'urus' ),
                        ),
                        array(
                            'id'       => 'enable_custom_placeholder',
                            'type'     => 'switch',
                            'title'    => esc_html__('Use Custom Placeholder Image','urus'),
                            'subtitle'     => esc_html__( 'Products which has no image will display image as your uploaded placeholder image', 'urus' ),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'custom_placeholder_url',
                            'type'     => 'media',
                            'url'      => true,
                            'title'    => esc_html__( 'Custom Placeholder Image', 'urus' ),
                            'compiler' => 'true',
                            'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'urus' ),
                            'subtitle' => esc_html__( 'Upload any media using the WordPress native uploader', 'urus' ),
                            'default'  => array(
                                'url'       => URUS_IMAGES . '/product-placeholder.jpg',
                                'id'        => '',
                                'width'     => 680,
                                'height'    => 833,
                                'thumbnail' => URUS_IMAGES . '/product-placeholder.jpg',
                                'title'     => get_bloginfo('name')
                            ),
                            'required' => array( 'enable_custom_placeholder', '=', true )
                        )
                    )
                ),
                // shop page
                array(
                    'title'      => esc_html__('Shop Page', 'urus'),
                    'desc'       => esc_html__('Product List Settings', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'       => 'woo_shop_layout',
                            'type'     => 'image_select',
                            'compiler' => true,
                            'title'    => esc_html__( 'Sidebar Position', 'urus' ),
                            'subtitle' => esc_html__( 'Select sidebar position on shop, product archive page.', 'urus' ),
                            'options'  => array(
                                'left'  => array('alt' => '1 Column Left', 'img' => URUS_IMAGES.'/2cl.png'),
                                'right' => array('alt' => '2 Column Right', 'img' => URUS_IMAGES.'/2cr.png'),
                                'full'  => array('alt' => 'Full Width', 'img' => URUS_IMAGES.'/1column.png' ),
                            ),
                            'default'  => 'left',
                        ),
                        array(
                            'id'      => 'woo_shop_used_sidebar',
                            'type'    => 'select',
                            'multi'   => false,
                            'title'   => esc_html__( 'Sidebar', 'urus' ),
                            'options' => Urus_Settings_Options::get_sidebars(),
                            'default' => 'shop-widget-area',
                            'required' => array('woo_shop_layout','=',array('left','right'))
                        ),
                        array(
                            'id'      => 'shop_heading_style',
                            'type'    => 'image_select',
                            'multi'   => false,
                            'title'   => esc_html__( 'Heading Style', 'urus' ),
                            'options' => array(
                                'simple' => array(
                                    'title' => esc_html__( 'Simple', 'urus' ),
                                    'alt' => esc_html__( 'Simple', 'urus' ),
                                    'img' => URUS_IMAGES.'/heading-simple.jpg' ),
                                'banner' => array(
                                    'title' => esc_html__( 'Banner', 'urus' ),
                                    'alt' => esc_html__( 'Banner', 'urus' ),
                                    'img' => URUS_IMAGES.'/heading-banner.jpg' ),
                            ),
                            'default' => 'simple',
                        ),

                        array(
                            'id'       => 'shop_heading_background',
                            'type'     => 'background',
                            'title'    => esc_html__('Shop Heading Background', 'urus'),
                            'subtitle' => esc_html__('Heading background with image, color, etc.', 'urus'),
                            'required' => array('shop_heading_style','=',array('banner'))
                        ),
                        array(
                            'id'       => 'display_categories',
                            'type'     => 'switch',
                            'title'    => esc_html__( 'Show  Categories', 'urus' ),
                            'default'  => false,
                            'required' => array('shop_heading_style','=',array('banner'))
                        ),
                        array(
                            'title'    => esc_html__( 'Categories Display', 'urus' ),
                            'id'      => 'display_categories_style',
                            'type'    => 'select',
                            'default' => 'simple',
                            'options'   =>  array(
                                'simple'    => esc_html__('Simple','urus'),
                                'mini'    => esc_html__('Mini','urus'),
                            ),
                            'required' => array('display_categories','=',array(true))
                        ),
                        array(
                            'title'    => esc_html__( 'Categories Display Query', 'urus' ),
                            'id'      => 'display_categories_query',
                            'type'    => 'select',
                            'default' => 'only_main_cat',
                            'options'   =>  array(
                                'only_main_cat'    => esc_html__('Only Main Category' ,'urus'),
                                'show_sub_cat'    => esc_html__('Only show children of the current category','urus'),
                            ),
                            'required' => array('display_categories','=',array(true))

                        ),

                        array(
                            'id'       => 'shop_heading_dark',
                            'type'     => 'switch',
                            'title'    => esc_html__( 'Shop Heading Dark', 'urus' ),
                            'default'  => false,
                            'required' => array('shop_heading_style','=',array('banner'))
                        ),
                        array(
                            'id'       => 'shop_heading_overlay',
                            'type'     => 'switch',
                            'title'    => esc_html__( 'Shop Heading Overlay', 'urus' ),
                            'default'  => false,
                            'required' => array('shop_heading_style','=',array('banner'))
                        ),
                        array(
                            'id'       => 'shop_heading_overlay_color',
                            'type'     => 'color_rgba',
                            'title'    => esc_html__('Overlay Color', 'urus'),
                            'required' => array('shop_heading_overlay','=',true),
                        ),
                        array(
                            'title'    => esc_html__( 'Shop Layout', 'urus' ),
                            'id'      => 'shop_layout',
                            'type'    => 'select',
                            'default' => 'simple',
                            'options'   =>  array(
                                'simple'    => esc_html__('Simple','urus'),
                                'modern'    => esc_html__('Modern','urus'),
                            ),
                            'required' => array('shop_heading_style','=',array('banner'))
                        ),
                        array(
                            'id'       => 'shop_background',
                            'type'     => 'background',
                            'title'    => esc_html__('Shop Background', 'urus'),
                            'subtitle' => esc_html__('Heading with image, color, etc.', 'urus'),
                            'required' => array('shop_layout','=',array('modern'))
                        ),
                        array(
                            'id'       => 'woo_shop_list_style',
                            'type'     => 'image_select',
                            'compiler' => true,
                            'title'    => esc_html__( 'Shop List Layout', 'urus' ),
                            'subtitle' => esc_html__( 'Select default layout for shop, product category archive.', 'urus' ),
                            'options'  => array(
                                'grid' => array( 'alt' => 'Layout Grid', 'img' => URUS_IMAGES.'/grid-display.png' ),
                                'list' => array( 'alt' => 'Layout List', 'img' => URUS_IMAGES.'/list-display.png' ),
                                'masonry' => array( 'alt' => 'Layout Masonry', 'img' => URUS_IMAGES.'/masonry-display.png' ),
                            ),
                            'default'  => 'grid',
                        ),

                        array(
                            'title'    => esc_html__( 'Items per row on Desktop( For grid mode )', 'urus' ),
                            'subtitle'=> esc_html__( '(Screen resolution of device >= 1200px )', 'urus' ),
                            'id'      => 'woo_lg_items',
                            'type'    => 'select',
                            'default' => '3',
                            'options'   =>  array(
                                '12'    =>  '1 item',
                                '6'     =>  '2 items',
                                '4'     =>  '3 items',
                                '3'     =>  '4 items',
                                '15'    =>  '5 items',
                                '2'     =>  '6 items',
                            ),
                        ),
                        array(
                            'title'    => esc_html__( 'Items per row on landscape tablet( For grid mode )', 'urus' ),
                            'subtitle'=>esc_html__('(Screen resolution of device >=992px and < 1200px )','urus'),
                            'id'      => 'woo_md_items',
                            'type'    => 'select',
                            'default' => '3',
                            'options'   =>  array(
                                '12'    =>  '1 item',
                                '6'     =>  '2 items',
                                '4'     =>  '3 items',
                                '3'     =>  '4 items',
                                '15'    =>  '5 items',
                                '2'     =>  '6 items',
                            ),

                        ),
                        array(
                            'title'    => esc_html__( 'Items per row on portrait tablet( For grid mode )', 'urus' ),
                            'subtitle'=>esc_html__('(Screen resolution of device >=768px and < 992px )','urus'),
                            'id'      => 'woo_sm_items',
                            'type'    => 'select',
                            'default' => '4',
                            'options' => array(
                                '12'    =>  '1 item',
                                '6'     =>  '2 items',
                                '4'     =>  '3 items',
                                '3'     =>  '4 items',
                                '15'    =>  '5 items',
                                '2'     =>  '6 items',
                            ),

                        ),
                        array(
                            'title'    => esc_html__( 'Items per row on Mobile( For grid mode )', 'urus' ),
                            'subtitle'=>esc_html__('(Screen resolution of device >=480  add < 768px)','urus'),
                            'id'      => 'woo_xs_items',
                            'type'    => 'select',
                            'default' => '6',
                            'options' => array(
                                '12'    =>  '1 item',
                                '6'     =>  '2 items',
                                '4'     =>  '3 items',
                                '3'     =>  '4 items',
                                '15'    =>  '5 items',
                                '2'     =>  '6 items',
                            ),

                        ),
                        array(
                            'title'    => esc_html__( 'Items per row on Mobile( For grid mode )', 'urus' ),
                            'subtitle'=>esc_html__('(Screen resolution of device < 480px)','urus'),
                            'id'      => 'woo_ts_items',
                            'type'    => 'select',
                            'default' => '6',
                            'options' => array(
                                '12'    =>  '1 item',
                                '6'     =>  '2 items',
                                '4'     =>  '3 items',
                                '3'     =>  '4 items',
                                '15'    =>  '5 items',
                                '2'     =>  '6 items',
                            ),

                        ),
                        array(
                            'id'       => 'woo_shop_infinite_load',
                            'type'     => 'select',
                            'title'    => esc_html__('Infinite Load', 'urus'),
                            'subtitle' => esc_html__('	Configure "infinite" product loading.', 'urus'),
                            'options'  =>  array(
                                'default' => esc_html__('Disable','urus'),
                                'button' => esc_html__('Button','urus'),
                                'scroll' => esc_html__('Scroll','urus')
                            ),
                            'default'  => 'default',
                        ),

                    )
                ),

            );

            $section[] =  array(
                'title'        => esc_html__( 'Shop Filters', 'urus' ),
                'desc'         => esc_html__( 'Shop Filter Settings', 'urus' ),
                'subsection'   => true,
                'fields'       => array(
                    array(
                        'id'      => 'enable_ajax_filter',
                        'type'    => 'switch',
                        'title'   => esc_html__( 'Enable ajax filter', 'urus' ),
                        'default' => '0',
                        'on'      => esc_html__( 'On', 'urus' ),
                        'off'     => esc_html__( 'Off', 'urus' ),
                    ),
                    array(
                        'id'       => 'shop_filter_style',
                        'type'     => 'select',
                        'title'    => esc_html__('Filter Layout','urus'),
                        'options' => array(
                            'dropdown'      =>  'Dropdown',
                            'canvas'        =>  'Off Canvas Sidebar',
                            'drawer'        =>  'Drawer Sidebar',
                            'accordion'     =>  'Accordion',
                            'step_filter'          =>  'Step Filter',
                        ),
                        'required' => array(
                            array( 'woo_shop_layout', '=', array( 'full' ) ),
                        ),
                        'default'  => 'dropdown'
                    ),
                    array(
                        'id'      => 'enable_instant_filter',
                        'type'    => 'switch',
                        'title'   => esc_html__( 'Instant Filter', 'urus' ),
                        'default' => '0',
                        'on'      => esc_html__( 'On', 'urus' ),
                        'off'     => esc_html__( 'Off', 'urus' ),
                    ),

                )
            );
            //product item
            $section[] = array(
                'title'        => esc_html__( 'Product Item', 'urus' ),
                'desc'         => esc_html__( 'Settings for Product item on Shop page', 'urus' ),
                'subsection'   => true,
                'fields'       => apply_filters('urus_settings_section_field_product_items',array(
                    array(
                        'id'       => 'woo_product_item_layout',
                        'type'     => 'image_select',
                        'compiler' => true,
                        'title'    => esc_html__( 'Layout', 'urus' ),
                        'subtitle' => esc_html__( 'Select layout for product item.', 'urus' ),
                        'options'  => apply_filters('urus_settings_woo_product_item_layout',
                            array(
                                'default' => array(
                                    'alt' => esc_html__('Default','urus'),
                                    'img' => URUS_IMAGES.'/classic.jpg'
                                ),
                                'classic' => array(
                                    'alt' => esc_html__('Classic','urus'),
                                    'img' => URUS_IMAGES.'/classic.jpg'
                                ),
                                'cart_and_icon' => array(
                                    'alt' => esc_html__('Cart and icon','urus'),
                                    'img' => URUS_IMAGES.'/cart_and_icon.jpg'
                                ),
                                'full' => array(
                                    'alt' => esc_html__('Full info','urus'),
                                    'img' => URUS_IMAGES.'/full_info.jpg'
                                ),
                                'vertical_icon' => array(
                                    'alt' => esc_html__('Vertical Icon','urus'),
                                    'img' => URUS_IMAGES.'/vertical_icon.jpg'
                                ),
                                'info_on_img' => array(
                                    'alt' => esc_html__('Info on image','urus'),
                                    'img' => URUS_IMAGES.'/info_on_img.jpg'
                                ),
                                'overlay_info' => array(
                                    'alt' => esc_html__('Overlay Info','urus'),
                                    'img' => URUS_IMAGES.'/overlay_info.jpg'
                                ),
                                'overlay_center' => array(
                                    'alt' => esc_html__('Overlay Center','urus'),
                                    'img' => URUS_IMAGES.'/overlay_center.jpg'
                                )
                            )
                        ),
                        'default'  => 'classic',
                    ),
                    array(
                        'id'       => 'woo_product_item_image',
                        'type'     => 'image_select',
                        'compiler' => true,
                        'title'    => esc_html__( 'Image style', 'urus' ),
                        'subtitle' => esc_html__( 'Select Image style for product item.', 'urus' ),
                        'options'  =>
                            array(
                                'classic' => array(
                                    'alt' => esc_html__('Classic','urus'),
                                    'img' => URUS_IMAGES.'/item_classic.jpg'
                                ),
                                'gallery' => array(
                                    'alt' => esc_html__('Gallery images','urus'),
                                    'img' => URUS_IMAGES.'/item_gallery.jpg'
                                ),
                                'slider' => array(
                                    'alt' => esc_html__('Slider image','urus'),
                                    'img' => URUS_IMAGES.'/item_slider.jpg'
                                ),
                                'zoom' => array(
                                    'alt' => esc_html__('Zoom image','urus'),
                                    'img' => URUS_IMAGES.'/item_zoom.jpg'
                                ),
                                'secondary_image' => array(
                                    'alt' => esc_html__('Secondary image on hover','urus'),
                                    'img' => URUS_IMAGES.'/item_classic.jpg'
                                )
                            ),
                        'default'  => 'classic',
                        'required' => array( 'woo_product_item_layout', '=', array( 'default','classic', 'cart_and_icon','full','vertical_icon' ) )
                    ),
                    array(
                        'id'       => 'woo_product_item_background_btn',
                        'type'     => 'image_select',
                        'compiler' => true,
                        'title'    => esc_html__( 'Button layout', 'urus' ),
                        'subtitle' => esc_html__( 'Select Button layout for product item.', 'urus' ),
                        'options'  =>
                            array(
                                'light' => array(
                                    'alt' => esc_html__('Light','urus'),
                                    'img' => URUS_IMAGES.'/light_btn.jpg'
                                ),
                                'dark' => array(
                                    'alt' => esc_html__('Dark','urus'),
                                    'img' => URUS_IMAGES.'/dark_btn.jpg'
                                )
                            ),
                        'default'  => 'light',
                        'required' => array( 'woo_product_item_layout', '=', array( 'classic','default') )
                    ),
                    array(
                        'id'       => 'woo_product_rating_in_loop',
                        'type'     => 'switch',
                        'title'    => esc_html__('Rating On Loop','urus'),
                        'default'  => false
                    ),
                    array(
                        'id'       => 'price_color',
                        'type'     => 'color',
                        'title'    => esc_html__('Price Color', 'urus'),
                        'subtitle' => esc_html__('Select a color for price','urus'),
                        'default'  => '#232529',
                        'transparent' => false,
                        'validate' => 'color',
                    ),
                    array(
                        'id'       => 'price_sale_color',
                        'type'     => 'color',
                        'title'    => esc_html__('Price Sale Color', 'urus'),
                        'subtitle' => esc_html__('Select a color for price','urus'),
                        'default'  => '#fc1111',
                        'transparent' => false,
                        'validate' => 'color',
                    ),
                    array(
                        'id'       => 'disable_new_label',
                        'type'     => 'switch',
                        'title'    => esc_html__('Disable new label','urus'),
                        'subtitle'     => esc_html__( '', 'urus' ),
                        'default'  => false,
                    ),
                    array(
                        'id'       => 'disable_sale_label',
                        'type'     => 'switch',
                        'title'    => esc_html__('Disable sale label','urus'),
                        'subtitle'     => esc_html__( '', 'urus' ),
                        'default'  => false,
                    ),
                    array(
                        'id'       => 'sale_content',
                        'type'     => 'select',
                        'title'    => esc_html__('sale content','urus'),
                        'subtitle'     => esc_html__( '', 'urus' ),
                        'options'  => array(
                            'default'      => esc_html__('Default','urus'),
                            'percent'     => esc_html__('Percent Off','urus'),
                        ),
                        'default'  => 'default',
                        'required' => array( 'disable_sale_label', '=', false )
                    ),
                    array(
                        'id'       => 'display_out_of_stock',
                        'type'     => 'switch',
                        'title'    => esc_html__('Display Out of stock label','urus'),
                        'subtitle'     => esc_html__( '', 'urus' ),
                        'default'  => false,
                    ),
                ))
            );
            //single product
            $section[] =array(
                    'title'        => esc_html__( 'Single Product', 'urus' ),
                    'desc'         => esc_html__( 'Single Product settings', 'urus' ),
                    'subsection'   => true,
                    'fields'       => array(
                        array(
                            'id' => 'single-product-section-start',
                            'type' => 'section',
                            'title' => esc_html__('Layout Options', 'urus'),
                            'subtitle' => esc_html__('Setting layouts for single product.', 'urus'),
                            'indent' => true
                        ),
                        //sidebar layout
                        array(
                            'id'       => 'woo_single_layout',
                            'type'     => 'image_select',
                            'title'    => esc_html__( 'Single Product Sidebar Position', 'urus' ),
                            'subtitle' => esc_html__( 'Select sidebar position on single product page.', 'urus' ),
                            'options'  => array(
                                'left'      => array( 'alt' => '1 Column Left', 'img' => URUS_IMAGES.'/2cl.png' ),
                                'right'     => array( 'alt' => '2 Column Right', 'img' => URUS_IMAGES.'/2cr.png' ),
                                'full' => array( 'alt' => 'Full Width', 'img' => URUS_IMAGES.'/1column.png' ),
                            ),
                            'default'  => 'left',
                        ),
                        array(
                            'id'      => 'woo_single_used_sidebar',
                            'type'    => 'select',
                            'multi'   => false,
                            'title'   => esc_html__( 'Single Product Sidebar', 'urus' ),
                            'options' => Urus_Settings_Options::get_sidebars(),
                            'default' => 'widget-area',
                            'required' => array('woo_single_layout','=',array('left','right')),
                            'subtitle' => esc_html__( 'Select sidebar use for product page.', 'urus' ),
                        ),
                        array(
                            'id'      => 'woo_single_used_layout',
                            'type'    => 'image_select',
                            'multi'   => false,
                            'title'   => esc_html__( 'Single Product Layout', 'urus' ),
                            'options' => array(
                                'vertical' => array(
                                    'title' => esc_html__( 'Vertical thumbnail', 'urus' ),
                                    'alt' => esc_html__( 'Vertical thumbnail', 'urus' ),
                                    'img' => URUS_IMAGES.'/default.jpg' ),
                                'horizontal' => array(
                                    'title' => esc_html__( 'Horizontal thumbnail', 'urus' ),
                                    'alt' => esc_html__( 'Horizontal thumbnail', 'urus' ),
                                    'img' => URUS_IMAGES.'/horizontal.jpg' ),
                                'list' => array(
                                    'title' => esc_html__( 'Sticky Detail with accordions', 'urus' ),
                                    'alt' => esc_html__( 'Sticky Detail with accordions', 'urus' ),
                                    'img' => URUS_IMAGES.'/sticky-accordion.jpg' ),
                                'list_gallery' => array(
                                    'title' => esc_html__( 'Sticky Detail with tabs', 'urus' ),
                                    'alt' => esc_html__( 'Sticky Detail with tabs', 'urus' ),
                                    'img' => URUS_IMAGES.'/sticky.jpg' ),
                                'special_gallery' => array(
                                    'title' => esc_html__( 'Sticky Center', 'urus' ),
                                    'alt' => esc_html__( 'Sticky Center', 'urus' ),
                                    'img' => URUS_IMAGES.'/special_gallery.jpg' ),
                                'background' => array(
                                    'title' => esc_html__( 'With Background', 'urus' ),
                                    'alt' => esc_html__( 'With Background', 'urus' ),
                                    'img' => URUS_IMAGES.'/background.jpg' ),
                                'gallery' => array(
                                    'title' => esc_html__( 'Gallery Basic', 'urus' ),
                                    'alt' => esc_html__( 'Gallery Basic', 'urus' ),
                                    'img' => URUS_IMAGES.'/gallery.jpg' ),
                                'gallery2' => array(
                                    'title' => esc_html__( 'Gallery Modern', 'urus' ),
                                    'alt' => esc_html__( 'Gallery Modern', 'urus' ),
                                    'img' => URUS_IMAGES.'/gallery2.jpg' ),
                                'large' => array(
                                    'title' => esc_html__( 'Slider Large', 'urus' ),
                                    'alt' => esc_html__( 'Slider Large', 'urus' ),
                                    'img' => URUS_IMAGES.'/large.jpg' ),
                                'special_slider' => array(
                                    'title' => esc_html__( 'Slider Center', 'urus' ),
                                    'alt' => esc_html__( 'Slider Center', 'urus' ),
                                    'img' => URUS_IMAGES.'/special_slide.jpg' ),
                                'special_centered_slider' => array(
                                    'title' => esc_html__( 'Slider Gallery', 'urus' ),
                                    'alt' => esc_html__( 'Slider Gallery', 'urus' ),
                                    'img' => URUS_IMAGES.'/special_centered_slider.jpg' ),
                                'extra-sidebar' => array(
                                    'title' => esc_html__( 'Extra Sidebar', 'urus' ),
                                    'alt' => esc_html__( 'Extra Sidebar', 'urus' ),
                                    'img' => URUS_IMAGES.'/extra-sidebar.jpg' ),
                            ),
                            'default' => 'vertical',
                        ),
                        array(
                            'id'       => 'single_product_background_type',
                            'type'     => 'background',
                            'title'    => esc_html__('Background', 'urus'),
                            'subtitle' => esc_html__('Single background with image, color, etc.', 'urus'),
                            'required' => array('woo_single_used_layout','=',array('background'))
                        ),

                        array(
                            'id'     => 'single-product-section-end',
                            'type'   => 'section',
                            'indent' => false,
                        ),
                        array(
                            'id' => 'single-product-section-start2',
                            'type' => 'section',
                            'title' => esc_html__('Info Options', 'urus'),
                            'subtitle' => esc_html__('Setting info for single product.', 'urus'),
                            'indent' => true
                        ),
                        array(
                            'id'     => 'single-product-section-end2',
                            'type'   => 'section',
                            'indent' => false,
                        ),
                        array(
                            'id'       => 'single_product_share',
                            'type'     => 'switch',
                            'title'    => esc_html__('Product Share','urus'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'single_product_navigation',
                            'type'     => 'switch',
                            'title'    => esc_html__('Product Navigation','urus'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'single_product_show_label',
                            'type'     => 'switch',
                            'title'    => esc_html__('Show attribute label on option selection','urus'),
                            'default'  => false,
                        )
                    )

                );
            $section = apply_filters('urus_settings_section_woocommerce',$section);
            return $section;
        }
    }
}
