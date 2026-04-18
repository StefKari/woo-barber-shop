<?php
if( !class_exists('Urus_Settings_Options_Header')){
    class Urus_Settings_Options_Header{
        public static function get(){
            $section = array(
                array(
                    'title'            => esc_html__( 'Header', 'urus' ),
                    'id'               => 'header',
                    'desc'             => esc_html__( 'Header Setings', 'urus' ),
                    'customizer_width' => '400px',
                    'icon'             => 'el-icon-credit-card',
                    'fields' => array(
                        array(
                            'id'       => 'used_header',
                            'type'     => 'image_select',
                            'title'    => esc_html__('Header Layout', 'urus'),
                            'subtitle' => esc_html__('Select a header layout', 'urus'),
                            'options'  => apply_filters('urus_settings_header_layout',
                                array(
                                    'default' => array(
                                        'title' => esc_html__('Default','urus'),
                                        'img'   => URUS_IMAGES.DS. 'header-default.jpg',
                                    ),
                                    'menu_center' => array(
                                        'title' => esc_html__('Menu Center','urus'),
                                        'img'   => URUS_IMAGES.DS. 'menu-center.jpg',
                                    ),
                                    'logo_on_menu' => array(
                                        'title' => esc_html__('Logo on menu','urus'),
                                        'img'   => URUS_IMAGES.DS. 'logo-on-menu.jpg',
                                    ),
                                    'logo_on_menu_line' => array(
	                                    'title' => esc_html__('Logo on menu with line','urus'),
	                                    'img'   => URUS_IMAGES.DS. 'header-logo-on-menu-line.jpg',
                                    ),
                                    'logo_in_menu' => array(
                                        'title' => esc_html__('Logo in menu','urus'),
                                        'img'   => URUS_IMAGES.DS. 'logo-in-menu.jpg',
                                    ),
                                    'logo_in_menu_line' => array(
	                                    'title' => esc_html__('Logo in menu with line','urus'),
	                                    'img'   => URUS_IMAGES.DS. 'header-logo-in-menu-line.jpg',
                                    ),
                                    'logo_center' => array(
                                        'title' => esc_html__('Logo Center','urus'),
                                        'img'   => URUS_IMAGES.DS. 'logo-center.jpg',
                                    ),
                                    'bar_menu' => array(
                                        'title' => esc_html__('Bar Menu','urus'),
                                        'img'   => URUS_IMAGES.DS. 'bar-menu.jpg',
                                    ),
                                    'full_search' => array(
                                        'title' => esc_html__('Full Search','urus'),
                                        'img'   => URUS_IMAGES.DS. 'header-full-search.jpg',
                                    ),
                                    'menu_dark' => array(
	                                    'title' => esc_html__('Menu dark','urus'),
	                                    'img'   => URUS_IMAGES.DS. 'header-menu-dark.png',
                                    ),
                                    'full_search_with_cat' => array(
	                                    'title' => esc_html__('Search full with categories','urus'),
	                                    'img'   => URUS_IMAGES.DS. 'header-full-search-with-cat.png',
                                    ),
                                    'extend_menu' => array(
	                                    'title' => esc_html__('Header Extend Menu','urus'),
	                                    'img'   => URUS_IMAGES.DS. 'header-menu-extend.png',
                                    ),
                                )
                            ),
                            'default'  => 'default',
                        ),
                        array(
                            'id'      => 'header_dark',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Header Dark', 'urus' ),
                            'default' => '0',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        ),
                        array(
                            'title'    => esc_html__( 'Mini Cart Style', 'urus' ),
                            'id'      => 'mini_cart_style',
                            'type'    => 'select',
                            'default' => 'dropdown',
                            'options'   =>  array(
                                'dropdown' => esc_html__('Dropdown','urus'),
                                'drawer' =>  esc_html__('Drawer','urus'),
                            ),
                        ),
                        array(
                            'id'      => 'enable_sticky_header',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Header Sticky', 'urus' ),
                            'default' => '0',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        ),
	                    array(
		                    'id'      => 'enable_vertical_menu',
		                    'type'    => 'switch',
		                    'title'   => esc_html__( 'Enable vertical menu', 'urus' ),
		                    'default' => '1',
		                    'on'      => esc_html__( 'On', 'urus' ),
		                    'off'     => esc_html__( 'Off', 'urus' ),
		                    'required' => array( 'used_header', '=', "full_search_with_cat" )
	                    ),
	                    array(
		                    'id'      => 'always_open_vertical_menu',
		                    'type'    => 'switch',
		                    'title'   => esc_html__( 'Always open', 'urus' ),
		                    'default' => '0',
		                    'on'      => esc_html__( 'On', 'urus' ),
		                    'off'     => esc_html__( 'Off', 'urus' ),
		                    'required' => array( 'enable_vertical_menu', '=', 1 )
	                    ),
	                    array(
		                    'id'      => 'vertical_opened',
		                    'type'    => 'switch',
		                    'title'   => esc_html__( 'Default open menu', 'urus' ),
		                    'default' => '0',
		                    'on'      => esc_html__( 'On', 'urus' ),
		                    'off'     => esc_html__( 'Off', 'urus' ),
		                    'required' => array( 'always_open_vertical_menu', '=', 0 )
	                    ),
                    ),
                ),
                array(
                    'title'      => esc_html__('Header Extra setting', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'      => 'enable_header_search',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Display Search', 'urus' ),
                            'subtitle' => esc_html__( 'Display search on header', 'urus' ),
                            'default' => '1',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        ),
                        array(
                            'id'      => 'enable_header_account',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Display Account', 'urus' ),
                            'subtitle' => esc_html__( 'Display Account on header', 'urus' ),
                            'default' => '1',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        ),
                        array(
                            'id'      => 'enable_account_icon',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Display Account icon', 'urus' ),
                            'subtitle' => esc_html__( 'Choose icon or text type', 'urus' ),
                            'default' => '0',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                            'required' => array( 'enable_header_account', '=', 1 )
                        ),
                        array(
                            'id'      => 'enable_header_wishlist',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Display Wishlist', 'urus' ),
                            'subtitle' => esc_html__( 'Display Wishlist on header', 'urus' ),
                            'default' => '1',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        ),
                        array(
                            'id'      => 'enable_wishlist_icon',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Display Wishlist icon', 'urus' ),
                            'subtitle' => esc_html__( 'Choose icon or text type', 'urus' ),
                            'default' => '1',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                            'required' => array( 'enable_header_wishlist', '=', 1 )
                        ),
                        array(
                            'id'      => 'enable_bag_icon',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Display Bag icon', 'urus' ),
                            'subtitle' => esc_html__( 'Choose icon or text type', 'urus' ),
                            'default' => '0',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' )
                        ),
                    )
                ),
                array(
                    'title'      => esc_html__('Header Promo', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'      => 'enable_header_promo',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Header Promo', 'urus' ),
                            'default' => '0',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        ),
                        array(
                            'id'               => 'promo_text',
                            'type'             => 'editor',
                            'title'            => esc_html__('Promo text', 'urus'),
                            'default'          => esc_html__('GRAB IT BEFORE IT\'S GONE... SALE\'S NOW UP TO 70% OFF', 'urus'),
                            'args'   => array(
                                'teeny'            => true,
                                'textarea_rows'    => 10
                            ),
                            'required' => array( 'enable_header_promo', '=', 1 )
                        ),

                        array(
                            'id'       => 'header_promo_bg_img',
                            'type'     => 'media',
                            'url'      => true,
                            'title'    => esc_html__( 'Background image', 'urus' ),
                            'compiler' => 'true',
                            'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'urus' ),
                            'subtitle' => esc_html__( 'Upload any media using the WordPress native uploader', 'urus' ),
                            'required' => array( 'enable_header_promo', '=', 1 )
                        ),
                        array(
                            'id'       => 'header_promo_bg_color',
                            'type'     => 'color',
                            'title'    => esc_html__('Background Color', 'urus'),
                            'subtitle' => esc_html__('Select a color for promo background','urus'),
                            'transparent' => false,
                            'default'  => '#1e1e1e',
                            'validate' => 'color',
                            'required' => array( 'enable_header_promo', '=', 1 )
                        ),
                        array(
                            'id'       => 'header_promo_color',
                            'type'     => 'color',
                            'title'    => esc_html__('Text Color', 'urus'),
                            'subtitle' => esc_html__('Select a color for promo text','urus'),
                            'transparent' => false,
                            'default'  => '#ffffff',
                            'validate' => 'color',
                            'required' => array( 'enable_header_promo', '=', 1 )
                        ),
                        array(
		                    'id'       => 'promo_btn_close_color',
		                    'type'     => 'color',
		                    'title'    => esc_html__('Close button color', 'urus'),
		                    'transparent' => false,
		                    'default'  => '#ffffff',
		                    'validate' => 'color',
		                    'required' => array( 'enable_header_promo', '=', 1 )
	                    ),
	                    array(
		                    'id'       => 'promo_btn_close_hover',
		                    'type'     => 'color',
		                    'title'    => esc_html__('Close button color on hover', 'urus'),
		                    'transparent' => false,
		                    'default'  => '',
		                    'validate' => 'color',
		                    'required' => array( 'enable_header_promo', '=', 1 )
	                    ),
	                    array(
		                    'id'            => 'promo_btn_close_size',
		                    'type'          => 'slider',
		                    'title'         => esc_html__( 'Close button size', 'urus' ),
		                    'desc'          => esc_html__( 'Unit (px)', 'urus' ),
		                    'default'       => 18,
		                    'min'           => 10,
		                    'step'          => 1,
		                    'max'           => 30,
		                    'display_value' => 'label',
		                    'required' => array( 'enable_header_promo', '=', 1 )
	                    ),
                        array(
                            'id'            => 'promo_height',
                            'type'          => 'slider',
                            'title'         => esc_html__( 'Promo height', 'urus' ),
                            'subtitle'      => esc_html__( 'Set height for promo area', 'urus' ),
                            'desc'          => esc_html__( 'Unit (px)', 'urus' ),
                            'default'       => 60,
                            'min'           => 40,
                            'step'          => 1,
                            'max'           => 120,
                            'display_value' => 'label',
                            'required' => array( 'enable_header_promo', '=', 1 )
                        ),
                    )
                )
            );
            return apply_filters('urus_settings_section_header',$section);
        }
    }
}
