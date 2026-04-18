<?php
if( !class_exists('Urus_Shortcodes_Container')){
    class Urus_Shortcodes_Container extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'container';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_container', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_container_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_container', $atts ) : $atts;

            extract( $atts );
            $css_class    = array( 'urus-container' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $css_class[]  = self::getCSSAnimation($css_animation);
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_container', $atts );
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="<?php echo esc_attr($atts['layout']);?> clearfix">
                    <?php echo wpb_js_remove_wpautop( $content ); ?>
                </div>
            </div>
            <?php
            return apply_filters('urus_shortcode_container_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_container',
                'name'        => esc_html__( 'Container', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display block', 'urus' ),
                'content_element'         => true,
                'show_settings_on_create' => true,
                'is_container' =>true,
                'js_view'                 => 'VcColumnView',
                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'value'       => array(
                            esc_html__( 'Default', 'urus') => 'container',
                            esc_html__( 'Medium', 'urus') => 'container-medium',
                            esc_html__( 'Large', 'urus') => 'container-wapper',
                            esc_html__( 'FullWidth', 'urus') => 'container-full',
                        ),
                        'admin_label' => true,
                        'param_name'  => 'layout',
                        'description' => esc_html__('Select layout.', 'urus'),
                        'std'         => 'container'
                    ),
                    vc_map_add_css_animation(),
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