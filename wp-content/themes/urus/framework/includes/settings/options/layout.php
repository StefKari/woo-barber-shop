<?php
if( !class_exists('Urus_Settings_Options_Layout')){
    class  Urus_Settings_Options_Layout{
        public static function get(){
            $section = array(
                array(
                    'title'  => esc_html__( 'Layouts', 'urus' ),
                    'desc'   => esc_html__( 'Layout Settings', 'urus' ),
                    'icon'   => 'el-icon-credit-card',
                    'fields' => array(
                        array(
                            'id'      => 'enable_boxed',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Boxed Layout', 'urus' ),
                            'default' => 0,
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        ),
                        array(
                            'id'       => 'body_background',
                            'type'     => 'background',
                            'title'    => esc_html__('Body Background', 'urus'),
                            'subtitle' => esc_html__('Heading background with image, color, etc.', 'urus'),
                            'output'   => '',
                        ),
                        array(
                            'id'            => 'content_width',
                            'type'          => 'slider',
                            'title'         => esc_html__( 'Content Width', 'urus' ),
                            'subtitle'      => esc_html__( 'Set the maximum allowed width for content.', 'urus' ),
                            'desc'          => esc_html__( 'Unit (px)', 'urus' ),
                            'default'       => 1400,
                            'min'           => 760,
                            'step'          => 1,
                            'max'           => 1920,
                            'display_value' => 'label'
                        ),
                        array(
                            'id'            => 'gutter_width',
                            'type'          => 'slider',
                            'title'         => esc_html__( 'Gutter Width', 'urus' ),
                            'subtitle'      => esc_html__( 'The width of the space between columns', 'urus' ),
                            'desc'          => esc_html__( 'Unit (px)', 'urus' ),
                            'default'       => 40,
                            'min'           => 10,
                            'step'          => 2,
                            'max'           => 60,
                            'display_value' => 'label'
                        ),
                    )
                ),
                array(
                    'title'  => esc_html__( 'Sidebars', 'urus' ),
                    'desc'   => esc_html__( 'Sidebars Layout Settings', 'urus' ),
                    'subsection' => true,
                    'fields' => array(
                        array(
                            'id'       => 'sidebar_layout',
                            'type'     => 'select',
                            'title'    => esc_html__('Sidebar Layout', 'urus'),
                            'subtitle' => esc_html__('Select a sidebar layout', 'urus'),
                            'options'  => apply_filters('urus_settings_sidebar_layout',
                                array(
                                    'default' => esc_html__('Default','urus'),
                                )
                            ),
                            'default'  => 'default',
                        ),
                        array(
                            'id'=>'extend_sidebar',
                            'type' => 'multi_text',
                            'title' => esc_html__('Custom Sidebars', 'urus'),
                            'subtitle' => esc_html__('Add extend sidebars', 'urus'),
                        ),
                        array(
                            'id'      => 'enable_sticky_sidebar',
                            'type'    => 'switch',
                            'title'   => esc_html__( 'Sticky sidebar', 'urus' ),
                            'default' => 0,
                            'on'      => esc_html__( 'On', 'urus' ),
                            'off'     => esc_html__( 'Off', 'urus' ),
                        ),
                    )
                )
            );
            return apply_filters('urus_settings_section_layout',$section);
        }
    }
}
