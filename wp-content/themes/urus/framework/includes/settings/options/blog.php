<?php
if( !class_exists('Urus_Settings_Options_Blog')){
    class Urus_Settings_Options_Blog{

        public static function get(){
            $section = array(
                array(
                    'title'            => esc_html__( 'Blog', 'urus' ),
                    'id'               => 'blog',
                    'desc'             => esc_html__( 'This Blog Setings', 'urus' ),
                    'customizer_width' => '400px',
                    'icon'             => 'el-icon-th-list',
                    'fields'     => array(
                        array(
                            'id'       => 'blog_layout',
                            'type'     => 'image_select',
                            'compiler' => true,
                            'title'    => esc_html__( 'Blog Layout', 'urus' ),
                            'subtitle' => esc_html__( 'Select a layout.', 'urus' ),
                            'options'  => array(
                                'left'  => array( 'alt' => 'Left Sidebar', 'img' => URUS_IMAGES.'/2cl.png' ),
                                'right' => array( 'alt' => 'Right Sidebar', 'img' => URUS_IMAGES.'/2cr.png' ),
                                'full'  => array( 'alt' => 'Full Width', 'img' => URUS_IMAGES.'/1column.png' ),
                            ),
                            'default'  => 'left',
                        ),
                        array(
                            'id'      => 'blog_used_sidebar',
                            'type'    => 'select',
                            'multi'   => false,
                            'title'   => esc_html__( 'Blog Sidebar', 'urus' ),
                            'options' => Urus_Settings_Options::get_sidebars(),
                            'default' => 'widget-area',
                            'required' => array('blog_layout','=',array('left','right'))
                        ),
                        array(
                            'id'      => 'blog_heading_style',
                            'type'    => 'select',
                            'multi'   => false,
                            'title'   => esc_html__( 'Blog Heading Style', 'urus' ),
                            'options' => array(
                                'simple' => esc_html__('Simple', 'urus'),
                                'banner' => esc_html__('Banner', 'urus'),
                            ),
                            'default' => 'banner',
                        ),
                        array(
                            'id'       => 'blog_heading_background',
                            'type'     => 'background',
                            'title'    => esc_html__('Blog Heading Background', 'urus'),
                            'subtitle' => esc_html__('Heading background with image, color, etc.', 'urus'),
                            'output'   => '.blog-heading.banner',
                            'required' => array('blog_heading_style','=',array('banner'))
                        ),
                        array(
                            'id'      => 'blog_list_style',
                            'type'    => 'select',
                            'multi'   => false,
                            'title'   => esc_html__( 'Blog Layout Style', 'urus' ),
                            'options' => array(
                                'classic' => esc_html__('Classic Style', 'urus'),
                                'grid' => esc_html__('Grid Style', 'urus'),
                                'standard' => esc_html__('Standard', 'urus'),
                            ),
                            'default' => 'classic',
                        ),
                        array(
                            'id'       => 'enable_post_item_share',
                            'type'     => 'switch',
                            'title'    => esc_html__('Post Shares','urus'),
                            'subtitle' => esc_html__('	Enable share button in loop post.','urus'),
                            'default'  => false
                        ),
                    ),
                ),
                array(
                    'title'            => esc_html__( 'Single Post', 'urus' ),
                    'id'               => 'single-blog',
                    'desc'             => esc_html__( 'This Blog Setings', 'urus' ),
                    'customizer_width' => '400px',
                    'subsection'       => true,
                    'fields'     => array(
                        array(
                            'id'       => 'single_blog_layout',
                            'type'     => 'image_select',
                            'compiler' => true,
                            'title'    => esc_html__( 'Blog Layout', 'urus' ),
                            'subtitle' => esc_html__( 'Select a layout.', 'urus' ),
                            'options'  => array(
                                'left'  => array( 'alt' => 'Left Sidebar', 'img' => URUS_IMAGES.'/2cl.png' ),
                                'right' => array( 'alt' => 'Right Sidebar', 'img' => URUS_IMAGES.'/2cr.png' ),
                                'full'  => array( 'alt' => 'Full Width', 'img' => URUS_IMAGES.'/1column.png' ),
                            ),
                            'default'  => 'left',
                        ),
                        array(
                            'id'      => 'single_blog_used_sidebar',
                            'type'    => 'select',
                            'multi'   => false,
                            'title'   => esc_html__( 'Blog Sidebar', 'urus' ),
                            'options' => Urus_Settings_Options::get_sidebars(),
                            'default' => 'widget-area',
                            'required' => array('single_blog_layout','=',array('left','right'))
                        ),
                        array(
                            'id'       => 'enable_post_navigation',
                            'type'     => 'switch',
                            'title'    => esc_html__('Post Navigation','urus'),
                            'default'  => true
                        ),

                        array(
                            'id'       => 'author_bio',
                            'type'     => 'switch',
                            'title'    => esc_html__('Author Bio','urus'),
                            'default'  => false
                        ),
                        array(
                            'id'       => 'enable_post_related',
                            'type'     => 'switch',
                            'title'    => esc_html__('Post Related','urus'),
                            'default'  => false
                        ),

                    )
                )
            );
            return apply_filters('urus_settings_section_blog',$section);
        }
    }
}
