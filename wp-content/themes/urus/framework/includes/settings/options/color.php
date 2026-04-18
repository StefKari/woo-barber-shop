<?php
if( !class_exists('Urus_Settings_Options_Color')){
    class Urus_Settings_Options_Color{
        public static function get(){
            $section = array(
                array(
                    'title'  => esc_html__( 'Color', 'urus' ),
                    'desc'   => esc_html__( 'Color Settings', 'urus' ),
                    'icon'   => 'el-icon-credit-card',
                    'fields' => array(
                        array(
                            'id'       => 'main_color',
                            'type'     => 'color',
                            'title'    => esc_html__('Main site Color', 'urus'),
                            'subtitle' => esc_html__('Select a color for site','urus'),
                            'default'  => '#83b735',
                            'transparent' => false,
                            'validate' => 'color',
                        ),

                    )
                )
            );
            return apply_filters('urus_settings_section_general',$section);
        }
    }
}