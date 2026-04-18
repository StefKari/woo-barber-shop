<?php
if (!class_exists('Urus_Shortcodes_Slide')){
    class Urus_Shortcodes_Slide extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'slide';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_slide', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_slide', $atts ) : $atts;

            extract( $atts );
            $css_class    = array( 'urus-slide equal-container'  );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $css_class[]  = isset($atts['owl_nav_position']) ? $atts['owl_nav_position'] : "";
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_slide', $atts );
            $owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="urus-slide-wapper swiper-container urus-swiper custom-slide " <?php echo esc_attr( $owl_settings ); ?>>
                    <div class="swiper-wrapper">
                        <?php echo wpb_js_remove_wpautop( $content ); ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>
                </div>
                <!-- If we need navigation buttons -->
                <div class="slick-arrow next">
                    <span class="urus-icon-next"></span>
                </div>
                <div class="slick-arrow prev">
                    
                    <span class="urus-icon-prev"></span>
                </div>
            </div>
            <?php
            return apply_filters('urus_shortcode_slide_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_slide',
                'name'        => esc_html__( 'Slide', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display slide', 'urus' ),

                'as_parent'               => array(
                    'only' => 'vc_single_image, vc_custom_heading,vc_column_text,urus_banner,urus_category,urus_special_banner',
                ),
                'content_element'         => true,
                'show_settings_on_create' => true,

                'js_view'                 => 'VcColumnView',

                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'param_name'  => 'layout',
                        'value'       => array(
                            esc_html__( 'Default', 'urus' )     => 'default',
                            esc_html__('Layout 01', 'urus')      => 'layout1',
                        ),
                        'std'         => 'default',
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'urus'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'urus' ),
                    ),
                ),
            );
            $params['params'] = array_merge(
                $params['params'],
                Urus_Pluggable_Visual_Composer::vc_carousel(  )
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}