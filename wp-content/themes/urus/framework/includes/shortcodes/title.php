<?php
if( !class_exists('Urus_Shortcodes_Title')){
    class Urus_Shortcodes_Title extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'title';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_title', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_title', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-section-title' );
            $css_class[]  = isset( $atts['text_align'] ) ? $atts['text_align'] : '';
            $css_class[]  = isset( $atts['title-style'] ) ? $atts['title-style'] : '';
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_title', $atts );
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if( $title):?>
                    <h3 class="title"><?php echo esc_html($title);?></h3>
                <?php endif;?>
                <?php if($atts['subtitle']):?>
                    <div class="subtitle"><?php echo esc_html($atts['subtitle']);?></div>
                <?php endif;?>
            </div>
            <?php
            return apply_filters('urus_shortcode_title_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_title',
                'name'        => esc_html__( 'Title', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Section Title', 'urus' ),
                'params'      => array(
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                    ),
	                array(
		                'type'        => 'dropdown',
		                'heading'     => esc_html__( 'Title Style', 'urus' ),
		                'param_name'  => 'title-style',
		                'value'       => array(
			                esc_html__( 'Default', 'urus' ) => 'default',
			                esc_html__('Layout 01', 'urus') => 'title-dash-style1',
			                esc_html__('Layout 02', 'urus')  => 'title-dash-style2',
			                esc_html__('Layout 03', 'urus')  => 'layout3',
			                esc_html__('Layout 04', 'urus')  => 'layout4',
			                esc_html__('Layout 05', 'urus')  => 'layout5',
			                esc_html__('Layout 06', 'urus')  => 'layout6',
		                ),
		                'std'         => '',
	                ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Sub Title', 'urus' ),
                        'param_name'    => 'subtitle',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Text align', 'urus' ),
                        'param_name'  => 'text_align',
                        'value'       => array(
                            esc_html__( 'Left', 'urus' ) => 'text-left',
                            esc_html__('Center', 'urus') => 'text-center',
                            esc_html__('Right', 'urus')  => 'text-right',
                        ),
                        'std'         => 'text-center',
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