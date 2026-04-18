<?php
if( !class_exists('Urus_Shortcodes_Custom_Menu')){
    class Urus_Shortcodes_Custom_Menu extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'custom_menu';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_custom_menu', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_custom_menu', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-custom-menu' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_custom_menu', $atts );
            $menu  = get_term_by( 'slug', $atts['nav_menu'], 'nav_menu' );


            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if( $atts['title']):?>
                    <h3 class="title"><a href="#"><?php echo esc_html($atts['title']);?></a></h3>
                <?php endif;?>
                <?php


                if ( !is_wp_error( $menu ) && is_object( $menu ) && !empty( $menu ) ) {
                    $nav_menu = ! empty( $menu->term_id) ? wp_get_nav_menu_object( $menu->term_id) : false;

                    if(!$nav_menu){
                        return;
                    }

                    $nav_menu_args = array(
                        'fallback_cb' => '',
                        'menu'        => $nav_menu
                    );
                    wp_nav_menu( $nav_menu_args);

                } else {
                    echo esc_html__( 'No content.', 'urus' );
                }

                ?>
            </div>
            <?php
            return apply_filters('urus_shortcode_custom_menu_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){
            $all_menu = array();
            $menus    = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
            if ( $menus && count( $menus ) > 0 ) {
                foreach ( $menus as $m ) {
                    $all_menu[$m->name] = $m->slug;
                }
            }
            $params    = array(
                'base'        => 'urus_custom_menu',
                'name'        => esc_html__( 'Custom Menu', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Custom Menu', 'urus' ),
                'params'      => array(
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Menu', 'urus' ),
                        'value'       => $all_menu,
                        'admin_label' => true,
                        'param_name'  => 'nav_menu',
                        'description' => esc_html__( 'Select menu to display.', 'urus' ),
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'value'       => array(
                            esc_html__( 'Vertical', 'urus') => 'vertical',
                            esc_html__( 'Vertical Simple', 'urus') => 'vertical vertical-simple',
                            esc_html__( 'Vertical Line', 'urus') => 'vertical vertical-simple-line',
                            esc_html__('Inline', 'urus')    => 'inline',
                            esc_html__('Inline Simple', 'urus')    => 'inline inline-simple',
                            esc_html__('Inline Special', 'urus')    => 'inline inline-special',
                            esc_html__('Inline Special 2', 'urus')    => 'inline inline-special-1',
                        ),
                        'admin_label' => true,
                        'param_name'  => 'layout',
                        'description' => esc_html__('Select layout.', 'urus'),
                        'std'         => 'vertical'
                    ),

                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'urus'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'urus' ),
                    ),
                ),
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}
