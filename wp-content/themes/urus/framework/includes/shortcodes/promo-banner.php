<?php
if( !class_exists('Urus_Shortcodes_Promo_Banner')){
    class Urus_Shortcodes_Promo_Banner extends Urus_Shortcodes {
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'promo_banner';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }
        
        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'promo_banner', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            $class = '.'.$atts['urus_custom_id'];
            
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'promo_banner', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-promo-banner' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'promo_banner', $atts );
            
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                @
            </div>
            
            <?php
            return apply_filters('urus_shortcode_promo_banner_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){
            
            $params    = array(
                'base'        => 'promo_banner',
                'name'        => esc_html__( 'Promo Banner', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Promo Banner', 'urus' ),
                'params'      => array(
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__('Link', 'urus'),
                        'param_name'  => 'link',
                        'admin_label' => true,
                        'std'         =>'#'
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