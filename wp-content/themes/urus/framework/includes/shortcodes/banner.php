<?php
if( !class_exists('Urus_Shortcodes_Banner')){
    class Urus_Shortcodes_Banner extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'banner';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_banner', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_banner', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-banner equal-elem' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $css_class[]  = $atts['banner_hover_effect'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_banner', $atts );
            $owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
            ob_start();
            ?>
            <div class="">
                <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                    <div class="banner-wapper">
                        <?php echo wpb_js_remove_wpautop( $content ); ?>
                    </div>
                </div>
            </div>

            <?php
            return apply_filters('urus_shortcode_banner_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_banner',
                'name'        => esc_html__( 'Banner', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display banner', 'urus' ),

                'is_container' =>true,
                'content_element'         => true,
                'show_settings_on_create' => true,
                'js_view'                 => 'VcColumnView',
                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Hover Effect', 'urus' ),
                        'param_name'  => 'banner_hover_effect',
                        'value'       => array(
                            esc_html__( 'None', 'urus' ) => '',
                            esc_html__( 'Normal', 'urus' ) => 'effect normal-effect',
                            esc_html__( 'Plus zoom', 'urus' ) => 'effect plus-zoom',
                            esc_html__( 'Border zoom', 'urus' ) => 'effect border-zoom',
                            esc_html__( 'Border scale', 'urus' ) => 'effect border-scale',
                        ),
                        'std'         => '',
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