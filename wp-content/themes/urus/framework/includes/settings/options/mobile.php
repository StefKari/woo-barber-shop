<?php
if(!class_exists('Urus_Settings_Options_Mobile')) {
	class Urus_Settings_Options_Mobile{
		public static function get(){
			$menus = wp_get_nav_menus();
			$menu_items = [];
			foreach ($menus as $item ) {
				$menu_items[$item->slug] = $item->name;
			}
			$section = array(
				array(
					'title'  => esc_html__( 'Mobile Templates', 'urus' ),
					'desc'   => esc_html__( 'Template settings on mobile devices', 'urus' ),
					'icon'   => 'el el-photo',
					'fields' => array(
						array(
							'id'      => 'enable_mobile_template',
							'type'    => 'switch',
							'title'   => esc_html__( 'Mobile Template', 'urus' ),
							'default' => '1',
							'on'      => esc_html__( 'On', 'urus' ),
							'off'     => esc_html__( 'Off', 'urus' ),
						),
                        array(
                            'id'      => 'enable_mobile_page',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Mobile Page', 'urus' ),
                            'subtitle'   => esc_html__( 'Turn On if you want to use own Homepage for Mobile devices. By default, it turns off.', 'urus' ),
                            'default' => false,
                        ),

					)
				),
				array(
					'title'      => esc_html__('Header', 'urus'),
					'desc'       => esc_html__('Settings for header on mobile', 'urus'),
					'subsection' => true,
					'fields'     => array(
						array(
							'id'       => 'mobile_header',
							'type'     => 'image_select',
							'compiler' => true,
							'title'    => esc_html__( 'Header Layout', 'urus' ),
							'subtitle' => esc_html__( 'Select a layout.', 'urus' ),
							'options'  => array(
								'style1' => array( 'alt' => 'Header Layout 1', 'img' => URUS_IMAGES.'/mobile_headder1.jpg' ),
								'style2' => array( 'alt' => 'Header Layout 2', 'img' => URUS_IMAGES.'/mobile_headder2.jpg' ),
							),
							'default'  => 'style1',
						),
					),
				),
				array(
					'title'      => esc_html__('Mobile Logo', 'urus'),
					'desc'       => esc_html__('Mobile Logo Settings', 'urus'),
					'subsection' => true,
					'fields'     => array(
						array(
							'id'       => 'logo_mobile',
							'type'     => 'media',
							'url'      => true,
							'title'    => esc_html__( 'Logo Mobile', 'urus' ),
							'compiler' => 'true',
							//'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
							'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'urus' ),
							'subtitle' => esc_html__( 'Upload any media using the WordPress native uploader', 'urus' ),
							'default'  => array(
                                'url'       => URUS_IMAGES . '/logo.svg',
                                'id'        => '',
                                'width'     => '80',
                                'height'    => '29',
                                'thumbnail' => '',
                                'title'     => get_bloginfo('name')
                            ),
						),
                        array(
                            'id'            => 'mobile_logo_width',
                            'type'          => 'slider',
                            'title'         => esc_html__( 'Logo width', 'urus' ),
                            'subtitle'      => esc_html__( 'Set width for logo area in the header', 'urus' ),
                            'desc'          => esc_html__( 'Unit (px)', 'urus' ),
                            'default'       => 120,
                            'min'           => 30,
                            'step'          => 1,
                            'max'           => 300,
                            'display_value' => 'label'
                        ),

						array(
							'id'            => 'mobile_logo_padding_left',
							'type'          => 'slider',
							'title'         => esc_html__( 'Padding Left', 'urus' ),
							'desc'          => esc_html__( 'Unit (px)', 'urus' ),
							'default'       => 0,
							'min'           => 0,
							'step'          => 1,
							'max'           => 150,
							'display_value' => 'label'
						),
						array(
							'id'            => 'mobile_logo_padding_right',
							'type'          => 'slider',
							'title'         => esc_html__( 'Padding Right', 'urus' ),
							'desc'          => esc_html__( 'Unit (px)', 'urus' ),
							'default'       => 0,
							'min'           => 0,
							'step'          => 1,
							'max'           => 150,
							'display_value' => 'label'
						),
						array(
							'id'            => 'mobile_logo_padding_top',
							'type'          => 'slider',
							'title'         => esc_html__( 'Padding Top', 'urus' ),
							'desc'          => esc_html__( 'Unit (px)', 'urus' ),
							'default'       => 0,
							'min'           => 0,
							'step'          => 1,
							'max'           => 50,
							'display_value' => 'label'
						),
						array(
							'id'            => 'mobile_logo_padding_bottom',
							'type'          => 'slider',
							'title'         => esc_html__( 'Padding Bottom', 'urus' ),
							'desc'          => esc_html__( 'Unit (px)', 'urus' ),
							'default'       => 0,
							'min'           => 0,
							'step'          => 1,
							'max'           => 50,
							'display_value' => 'label'
						),
					),
				),
				array(
					'title'      => esc_html__('Shop Page', 'urus'),
					'desc'       => esc_html__('Settings for single product page on mobile', 'urus'),
					'subsection' => true,
					'fields'     => array(
						array(
							'id'       => 'mobile_shop',
							'type'     => 'image_select',
							'compiler' => true,
							'title'    => esc_html__( 'Shop Layout', 'urus' ),
							'subtitle' => esc_html__( 'Select a layout.', 'urus' ),
							'options'  => array(
								'style1' => array( 'alt' => 'Shop Layout 1', 'img' => URUS_IMAGES.DS.'mobile_shop1.jpg' ),
							),
							'default'  => 'style1',
						),
					),
				),
				array(
					'title'      => esc_html__('Single Product Page', 'urus'),
					'desc'       => esc_html__('Settings for single product page on mobile', 'urus'),
					'subsection' => true,
					'fields'     => array(
						array(
							'id'      => 'mobile_single_sticky_btn',
							'type'    => 'switch',
							'title'   => esc_html__( 'Sticky "Add To Cart" button', 'urus' ),
							'subtitle' => esc_html__( 'This will show fixed position Add to Cart button', 'urus' ),
							'default' => '1',
							'on'      => esc_html__( 'On', 'urus' ),
							'off'     => esc_html__( 'Off', 'urus' ),
						),

						array(
							'id'       => 'mobile_single_layout_style',
							'type'     => 'image_select',
							'compiler' => true,
							'title'    => esc_html__( 'Content layout', 'urus' ),
							'subtitle' => esc_html__( 'Select layout for single product page content.', 'urus' ),
							'options'  => array(
								'style1' => array( 'alt' => esc_html__('Layout 1','urus'),
													'title' => esc_html__('Layout 1','urus'),
													'img' => URUS_IMAGES.'/single_mobile_small_thumb.jpg'
												),
							),
							'default'  => 'style1',
						),
                        array(
                            'id'      => 'mobile_single_show_expert',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Display short description', 'urus' ),
                            'default' => '0',
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        )
					),
				),
				array(
					'title'      => esc_html__('Product Item Style', 'urus'),
					'desc'       => esc_html__('Settings for Product item on Shop page', 'urus'),
					'subsection' => true,
					'fields'     => array(
						array(
							'id'       => 'mobile_product_item_style',
							'type'     => 'image_select',
							'compiler' => true,
							'title'    => esc_html__( 'Product item layout', 'urus' ),
							'subtitle' => esc_html__( 'Select a layout.', 'urus' ),
							'options'  => array(
								'default' => array(
									'title' => esc_html__('Product item grid default','urus'),
									'alt' => esc_html__('Product item grid default','urus'),
									'img' => URUS_IMAGES.'/mobile_product_item_default.jpg'
								)
							),
							'default'  => 'default',
						),
					),
				),
			);
			return apply_filters('urus_settings_section_mobile',$section);
		}
	}
}
