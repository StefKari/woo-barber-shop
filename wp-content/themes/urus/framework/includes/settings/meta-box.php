<?php
if( !class_exists('Urus_Settings_Meta_Box')){
    class Urus_Settings_Meta_Box{
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
                global $pagenow, $post_type, $post;
                if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
                    // Get current post type.
                    if ( !isset( $post_type ) ) {
                        $post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null;
                    }
                    if ( empty( $post_type ) && ( isset( $post ) || isset( $_REQUEST['post'] ) ) ) {
                        $post_type = isset( $post ) ? $post->post_type : get_post_type( $_REQUEST['post'] );
                    }
                    if ( 'urus_pinmap' != $post_type ) {
                        add_filter( 'rwmb_meta_boxes', array( __CLASS__, 'settings' ) );
                }
            }
            // State that initialization completed.
            self::$initialized = true;
        }
        /**
         * Register additional meta boxes.`
         *
         * @param   array  $meta_boxes  Current meta boxes.
         *
         * @return  array
         */
        public static function settings( $meta_boxes ){
            $meta_boxes[] = self::page_settings();
            $meta_boxes[] = self::footer_settings();
            $meta_boxes = apply_filters('urus_meta_box_settings',$meta_boxes);
            return $meta_boxes;
        }
        public static function page_settings(){
            $settings = array(
                'id'         => 'urus_page_option',
                'title'      => esc_html__('Page Options', 'urus'),
                'post_types' => 'page',
                'fields'     => array(
                    array(
                        'id'        => 'enable_page_option',
                        'name'      => 'Enable Page Option?',
                        'type'      => 'switch',
                        'style'     => 'rounded',
                        'on_label'  => 'Yes',
                        'off_label' => 'No',
                        'std' => 0
                    ),
                    array(
                        'id'       => 'page_layout',
                        'name'     => esc_html__( 'Page layout', 'urus' ),
                        'type'     => 'image_select',
                        'options'          => array(
                            'full'  => URUS_IMAGES.'/1column.png',
                            'left'  => URUS_IMAGES.'/2cl.png',
                            'right' => URUS_IMAGES.'/2cr.png',
                        ),
                        'std' => 'left'
                    ),
                    array(
                        'name'    => esc_html__( 'Sidebar for page layout', 'urus' ),
                        'id'      => 'page_used_sidebar',
                        'type'    => 'select',
                        'show_option_none' => true,
                        'options' => Urus_Settings_Options::get_sidebars(),
                        'desc'    => esc_html__( 'Setting sidebar in the area sidebar', 'urus' ),
                        'std' => 'widget-area'
                    ),
                    array(
                        'name' => esc_html__( 'Extra page class', 'urus' ),
                        'desc' => esc_html__( 'If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.', 'urus' ),
                        'id'   => 'page_extra_class',
                        'type' => 'text',
                    ),
                    array(
                        'name'            => esc_html__('Page Heading Style','urus'),
                        'id'              => 'page_heading_style',
                        'type'            => 'select',
                        'options'         => array(
                            'banner' => esc_html__('Banner','urus'),
                            'simple' => esc_html__('Simple','urus')
                        ),
                        // Placeholder text
                        'placeholder'     => esc_html__('Select an Item','urus'),
                        'std' => 'default'
                    ),
                    array(
                        'id'   => 'page_heading_background',
                        'name' => esc_html__( 'Heading Background', 'urus' ),
                        'type' => 'background',
                    )
                )
            );
            $settings = apply_filters('urus_meta_box_page_settings',$settings);
            return $settings;
        }
        public static function footer_settings(){
            $settings = array(
                'id'         => 'urus_footer_option',
                'title'      => esc_html__('Footer Options', 'urus'),
                'post_types' => 'urus-footer',
                'fields'     => array(
                    array(
                        'name'            => esc_html__('Footer Layout','urus'),
                        'id'              => '_footer_layout',
                        'type'            => 'select',
                        'options'         => apply_filters('urus_meta_box_footer_layout',
                            array(
                                'default' => esc_html__('Default','urus'),
                                'dark' => esc_html__('Dark','urus')
                            )
                        ),
                        // Placeholder text
                        'placeholder'     => esc_html__('Select an Item','urus'),
                        'std' => 'default'
                    ),
                    array(
                        'name'            => esc_html__('Footer Width','urus'),
                        'id'              => '_footer_width',
                        'type'            => 'select',
                        'options'         => array(
                            'container' => esc_html__('Normal','urus'),
                            'container-wapper' => esc_html__('Full','urus'),
                            'container-full clearfix' => esc_html__('Full No Padding','urus')
                        ),
                        // Placeholder text
                        'placeholder'     => esc_html__('Select an Item','urus'),
                        'std' => 'default'
                    ),
                )
            );
            $settings = apply_filters('urus_meta_box_footer_settings',$settings);
            return $settings;
        }
    }
}
