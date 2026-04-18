<?php
if( !class_exists('Urus_Settings_Options_General')){
    class Urus_Settings_Options_General{
        public static function get(){
            $section = array(
                // theme feature
                array(
                    'title'            => esc_html__( 'Theme Feature', 'urus' ),
                    'id'               => 'general',
                    'desc'             => esc_html__( 'This General Setings', 'urus' ),
                    'customizer_width' => '400px',
                    'icon'             => 'el el-home',
                    'fields'           => array(
                        array(
		                    'id'       => 'theme_use_lazy_load',
		                    'type'     => 'switch',
		                    'title'    => esc_html__('Enable Lazyload','urus'),
		                    'default'  => false
	                    ),
                        array(
                            'id'       => 'theme_use_placeholder',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable Placeholder','urus'),
                            'default'  => false
                        ),

                        array(
                            'id'       => 'custom_js',
                            'type'     => 'ace_editor',
                            'title'    => esc_html__( 'Custom JS ', 'urus' ),
                            'subtitle' => esc_html__( 'Paste your custom JS code here.', 'urus' ),
                            'mode'     => 'javascript',
                            'theme'    => 'chrome',
                            'desc'     => 'Custom javascript code',
                        )
                    )
                ),
				//logo setting
				array(
					'title'      => esc_html__('Logo', 'urus'),
					'desc'       => esc_html__('Logo Settings', 'urus'),
					'subsection' => true,
					'fields'     => array(
						array(
							'id'       => 'logo',
							'type'     => 'media',
							'url'      => true,
							'title'    => esc_html__( 'Logo', 'urus' ),
							'compiler' => 'true',
							'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'urus' ),
							'subtitle' => esc_html__( 'Upload any media using the WordPress native uploader', 'urus' ),
							'default'  => array(
                                'url'       => URUS_IMAGES . '/logo.svg',
                                'id'        => '',
                                'width'     => '97',
                                'height'    => '52',
                                'thumbnail' => '',
                                'title'     => get_bloginfo('name')
                            ),
						),
                        array(
                            'id'            => 'logo_width',
                            'type'          => 'slider',
                            'title'         => esc_html__( 'Logo width', 'urus' ),
                            'subtitle'      => esc_html__( 'Set width for logo area in the header', 'urus' ),
                            'desc'          => esc_html__( 'Unit (px)', 'urus' ),
                            'default'       => 97,
                            'min'           => 97,
                            'step'          => 1,
                            'max'           => 600,
                            'display_value' => 'label'
                        ),
						array(
							'id'            => 'logo_padding_left',
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
							'id'            => 'logo_padding_right',
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
							'id'            => 'logo_padding_top',
							'type'          => 'slider',
							'title'         => esc_html__( 'Padding Top', 'urus' ),
							'desc'          => esc_html__( 'Unit (px)', 'urus' ),
							'default'       => 15,
							'min'           => 0,
							'step'          => 1,
							'max'           => 50,
							'display_value' => 'label'
						),
						array(
							'id'            => 'logo_padding_bottom',
							'type'          => 'slider',
							'title'         => esc_html__( 'Padding Bottom', 'urus' ),
							'desc'          => esc_html__( 'Unit (px)', 'urus' ),
							'default'       => 15,
							'min'           => 0,
							'step'          => 1,
							'max'           => 50,
							'display_value' => 'label'
						),
					),
				),
                //socials
                array(
                    'title'      => esc_html__('Socials', 'urus'),
                    'desc'       => esc_html__('Social Settings', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'       => 'opt_twitter_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Twitter', 'urus' ),
                            'default'  => 'https://twitter.com',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_fb_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Facebook', 'urus' ),
                            'default'  => 'https://facebook.com',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_google_plus_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Google Plus', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_dribbble_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Dribbble', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_behance_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Behance', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_tumblr_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Tumblr', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_instagram_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Instagram', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_pinterest_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Pinterest', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_youtube_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Youtube', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_vimeo_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Vimeo', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_linkedin_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Linkedin', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                        array(
                            'id'       => 'opt_rss_link',
                            'type'     => 'text',
                            'title'    => esc_html__( 'RSS', 'urus' ),
                            'default'  => '',
                            'validate' => 'url',
                        ),
                    ),
                ),
                //mailchimp
                array(
                    'title'      => esc_html__('Mailchimp ', 'urus'),
                    'desc'       => esc_html__('Mailchimp Settings', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id' => 'mailchimp-section-start',
                            'type' => 'section',
                            'title' => esc_html__('Api Options', 'urus'),
                            'subtitle' => esc_html__('Setting Mailchimp APi.', 'urus'),
                            'indent' => true
                        ),
                        array(
                            'id'       => 'mailchimp_api_key',
                            'type'     => 'text',
                            'title'    => esc_html__( 'API Key', 'urus' ),
                            'default'  => '',
                            'desc' => 'The API key for connecting with your MailChimp account. <a href="https://admin.mailchimp.com/account/api">Get your API key here.</a>'
                        ),
                        array(
                            'id'       => 'mailchimp_list_id',
                            'type'     => 'text',
                            'title'    => esc_html__( 'List ID', 'urus' ),
                        ),
                        array(
                            'id'       => 'mailchimp_success_message',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Success message', 'urus' ),
                            'std'      => esc_html__('Thanks for Subscribe!','urus')
                        ),
                        array(
                            'id'     => 'mailchimp-sticky-section-end',
                            'type'   => 'section',
                            'indent' => false,
                        ),
                    ),
                ),
                //back to top
                array(
                    'title'      => esc_html__('Back To Top Button ', 'urus'),
                    'desc'       => esc_html__('Back To Top Button Settings', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'       => 'enable_back_to_top_button',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable Back To Top Button','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'       => 'back_to_top_button_layout',
                            'type'     => 'select',
                            'title'    => esc_html__('Button Layout', 'urus'),
                            'subtitle' => esc_html__('Select a button layout', 'urus'),
                            'options'  => apply_filters('urus_settings_back_to_top_button_layout',
                                array(
                                    'default' => esc_html__('Circle','urus'),
                                    'percent_circle' => esc_html__('Percent Circle','urus')
                                )
                            ),
                            'default'  => 'default',
                            'required' => array( 'enable_back_to_top_button', '=', true )
                        ),
                    ),
                ),
                //popup
                array(
                    'title'      => esc_html__('Popup', 'urus'),
                    'desc'       => esc_html__('Popup Settings', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'       => 'enable_popup',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable Popup','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'       => 'enable_popup_mobile',
                            'type'     => 'switch',
                            'title'    => esc_html__('Enable On Mobile','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'       => 'popup_delay_time',
                            'type'     => 'text',
                            'title'    => esc_html__( 'Delay Time ( millisecond )', 'urus' ),
                        ),
                        array(
                            'id'=>'popup_used',
                            'type' => 'select',
                            'data' => 'posts',
                            'args' => array('post_type' => array('urus-popup'), 'posts_per_page' => -1,'orderby' => 'title', 'order' => 'ASC'),
                            'title' => esc_html__('Popup Display', 'urus'),
                            'required' => array( 'enable_popup', '=', true )
                        ),
                        array(
                            'id'=>'popup_pages_display',
                            'type' => 'select',
                            'multi' =>true,
                            'data' => 'posts',
                            'args' => array('post_type' => array('page'), 'posts_per_page' => -1,'orderby' => 'title', 'order' => 'ASC'),
                            'title' => esc_html__('Page Display', 'urus'),
                            'required' => array( 'enable_popup', '=', true )
                        ),
                    ),
                ),
                //Page preloader
                array(
                    'title'      => esc_html__('Page Preloader', 'urus'),
                    'desc'       => esc_html__('Page Preloader Settings', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'       => 'enable_page_preloader',
                            'type'     => 'switch',
                            'title'    => esc_html__('Use Preloader','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'       => 'preloader_style',
                            'type'     => 'select',
                            'title'    => esc_html__('Infinite Load', 'urus'),
                            'subtitle' => esc_html__('	Configure "infinite" product loading.', 'urus'),
                            'options'  =>  array(
                                'audio' => esc_html__('Audio','urus'),
                                'ball-triangle' => esc_html__('Ball Triangle','urus'),
                                'bars' => esc_html__('Bars','urus'),
                                'circles' => esc_html__('Circles','urus'),
                                'grid' => esc_html__('Grid','urus'),
                                'hearts' => esc_html__('Hearts','urus'),
                                'oval' => esc_html__('Oval','urus'),
                                'puff' => esc_html__('Puff','urus'),
                                'rings' => esc_html__('Rings','urus'),
                                'spinning-circles' => esc_html__('Spinning Circles','urus'),
                                'tail-spin' => esc_html__('Tail Spin','urus'),
                                'three-dots' => esc_html__('Three Dots','urus'),
                            ),
                            'default'  => 'audio',
                            'required' => array('enable_page_preloader','=',true),
                        ),
                        array(
                            'id'       => 'preloader_background_color',
                            'type'     => 'color_rgba',
                            'title'    => esc_html__('Preloader Background Color', 'urus'),
                            'default'   => array(
                                'color'     => '#83b735',
                                'alpha'     => 1,
                                'rgba'=>'rgba(131,183,53,1)'
                            ),
                            'required' => array('enable_page_preloader','=',true),
                        ),
                    ),
                ),
                //Search functions
                array(
                    'title'      => esc_html__('Search', 'urus'),
                    'desc'       => esc_html__('Search settings', 'urus'),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'       => 'theme_search_sku',
                            'type'     => 'switch',
                            'title'    => esc_html__('Allows Search products by SKU','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'       => 'theme_search_clear',
                            'type'     => 'switch',
                            'title'    => esc_html__('Show clear input button','urus'),
                            'default'  => true
                        ),
                        array(
                            'id'       => 'theme_use_search_page',
                            'type'     => 'switch',
                            'title'    => esc_html__('Redirect to search page on submit','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'       => 'theme_search_type',
                            'type'     => 'select',
                            'title'    => esc_html__('Search result type', 'urus'),
                            'subtitle' => esc_html__('Select type of search result will be returned', 'urus'),
                            'options'  => array(
                                                'product' => esc_html__('Products','urus'),
                                                'page' => esc_html__('Blog posts','urus')
                                           ),
                            'default'  => 'product',
                            'required' => array( 'theme_use_search_page', '=', true )
                        ),
                        array(
                            'id'       => 'auto_rebuild_search_index',
                            'type'     => 'switch',
                            'title'    => esc_html__('Auto sync product index','urus'),
                            'subtitle'     => __('Automatic syncing your products index for instant search module
.', 'urus'),
                            'default'  => true
                        ),
                        array(
                            'id'       => 'rebuild_search_index_btn',
                            'type'     => 'raw',
                            'full_width' => false,
                            'title'    => __('Manually sync product index', 'urus'),
                            'subtitle'     => __('Manually syncing your products index for instant search module.', 'urus'),
                            'content'  => '<button type="button" id="rebuild_search_index_action">'.esc_html__('Sync now','urus').'</button><div class="rebuild_search_index_notification"></div>',
                        )
                    )
                )
            );
            return apply_filters('urus_settings_section_general',$section);
        }
    }
}
