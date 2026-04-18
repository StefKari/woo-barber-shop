<?php
if( !class_exists('Urus_Settings_Options_Footer')){
    class  Urus_Settings_Options_Footer{
        public static function get(){
            $section = array(
                array(
                    'title'  => esc_html__( 'Footer', 'urus' ),
                    'desc'   => esc_html__( 'Footer Settings', 'urus' ),
                    'icon'   => 'el-icon-credit-card',
                    'fields' => array(
                        array(
                            'id'       => 'theme_use_footer_builder',
                            'type'     => 'switch',
                            'title'    => esc_html__('Use Footer Builder','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'=>'footer_used',
                            'type' => 'select',
                            'data' => 'posts',
                            'args' => array('post_type' => array('urus-footer'), 'posts_per_page' => -1,'orderby' => 'title', 'order' => 'ASC'),
                            'title' => esc_html__('Footer Display', 'urus'),
                            'required' => array( 'theme_use_footer_builder', '=', true )
                        ),
    
                        array(
                            'id'       => 'disable_footer_builder_mobile',
                            'type'     => 'switch',
                            'title'    => esc_html__('Disable Footer Builder on Mobile','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'               => 'footer_copyright',
                            'type'             => 'editor',
                            'title'            => esc_html__('Copyright', 'urus'),
                            'default'          => '© 2019 Urus - All Rights Reserved',
                            'args'   => array(
                                'teeny'            => true,
                                'textarea_rows'    => 10
                            )
                        ),
                    )
                )
            );
            return apply_filters('urus_settings_section_footer',$section);
        }
    }
}