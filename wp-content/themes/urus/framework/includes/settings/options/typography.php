<?php
if( !class_exists('Urus_Settings_Options_Typography')){
    class  Urus_Settings_Options_Typography{
        public static function get(){
            $section = array(
                array(
                    'icon'   => 'el-icon-font',
                    'title'  => esc_html__( 'Typography Options', 'urus' ),
                    'fields' => array(
                        array(
                            'id'       => 'opt_typography_body_font',
                            'type'     => 'typography',
                            'title'    => esc_html__( 'Body Font Setting', 'urus' ),
                            'subtitle' => esc_html__( 'Specify the body font properties.', 'urus' ),
                            'google'   => true,
                            'output'   => 'body',
                        ),
                        array(
                            'id'       => 'opt_typography_h1_font',
                            'type'     => 'typography',
                            'title'    => esc_html__( 'Heading 1(H1) Font Setting', 'urus' ),
                            'subtitle' => esc_html__( 'Specify the H1 tag font properties.', 'urus' ),
                            'google'   => true,
                            'output'   => 'h1',
                        ),

                        array(
                            'id'       => 'opt_typography_h2_font',
                            'type'     => 'typography',
                            'title'    => esc_html__( 'Heading 2(H2) Font Setting', 'urus' ),
                            'subtitle' => esc_html__( 'Specify the H2 tag font properties.', 'urus' ),
                            'google'   => true,
                            'output'   => 'h2',
                        ),

                        array(
                            'id'       => 'opt_typography_h3_font',
                            'type'     => 'typography',
                            'title'    => esc_html__( 'Heading 3(H3) Font Setting', 'urus' ),
                            'subtitle' => esc_html__( 'Specify the H3 tag font properties.', 'urus' ),
                            'google'   => true,
                            'output'   => 'h3',
                        ),

                        array(
                            'id'       => 'opt_typography_h4_font',
                            'type'     => 'typography',
                            'title'    => esc_html__( 'Heading 4(H4) Font Setting', 'urus' ),
                            'subtitle' => esc_html__( 'Specify the H4 tag font properties.', 'urus' ),
                            'google'   => true,
                            'output'   => 'h4',
                        ),

                        array(
                            'id'       => 'opt_typography_h5_font',
                            'type'     => 'typography',
                            'title'    => esc_html__( 'Heading 5(H5) Font Setting', 'urus' ),
                            'subtitle' => esc_html__( 'Specify the H5 tag font properties.', 'urus' ),
                            'google'   => true,
                            'output'   => 'h5',
                        ),

                        array(
                            'id'       => 'opt_typography_h6_font',
                            'type'     => 'typography',
                            'title'    => esc_html__( 'Heading 6(H6) Font Setting', 'urus' ),
                            'subtitle' => esc_html__( 'Specify the H6 tag font properties.', 'urus' ),
                            'google'   => true,
                            'output'   => 'h6',
                        ),
                    ),
                )
            );

            return apply_filters('urus_settings_section_typography',$section);
        }
    }
}