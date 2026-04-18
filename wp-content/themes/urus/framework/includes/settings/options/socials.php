<?php
if( !class_exists('Urus_Settings_Options_Socials')){
    class Urus_Settings_Options_Socials{
        public static function get(){
            $section = array(
                array(
                    'title'  => esc_html__( 'Socials', 'urus' ),
                    'desc'   => esc_html__( 'Socials Settings', 'urus' ),
                    'icon'   => 'el-icon-credit-card',
                    'fields' => array(

                    )
                ),
            );
            return apply_filters('urus_settings_section_socials',$section);
        }
    }
}