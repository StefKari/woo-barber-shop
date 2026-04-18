<?php
if( !class_exists('Urus_Shortcodes_Button')){
    class Urus_Shortcodes_Button extends Urus_Shortcodes {
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'button';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_button', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            $class = '.'.$atts['urus_custom_id'];
            if( isset($atts['hover_color']) && $atts['hover_color'] !=''){
                $css .=$class.':hover{ color:'.$atts['hover_color'].' !important;}';
            }
            if( isset($atts['hover_background']) && $atts['hover_background'] !=''){
                $css .=$class.':hover{ background-color:'.$atts['hover_background'].' !important;}';
            }
            if( isset($atts['hover_border']) && $atts['hover_border'] !=''){
                $css .=$class.':hover{ border-color:'.$atts['hover_border'].' !important;}';
            }
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_button', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-button' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_button', $atts );

            $align = isset( $atts['text_align'] ) ? $atts['text_align'] : '';
            if( $atts['layout'] =='default'){
                $css_class[] ='button';
            }
    
            if ( $atts['type']) {
                $icon = $atts['icon_' . $atts['type']];
                vc_icon_element_fonts_enqueue( $atts['type'] );
            }
            ob_start();
            ?>
            <div class="urus-button-wapper <?php echo esc_attr($align);?>">
                <a href="<?php echo esc_url($atts['link']);?>" class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                    <?php echo esc_html($atts['title']);?>
                    <?php if( $atts['use_icon'] && $atts['use_icon'] =='yes'):?>
                    <span class="button-icon <?php echo esc_attr($icon);?>"></span>
                    <?php endif;?>
                </a>
                
            </div>

            <?php
            return apply_filters('urus_shortcode_title_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){
    
            $icon_types = array(
                esc_html__( 'Font Awesome', 'urus' ) => 'fontawesome',
                esc_html__( 'Material', 'urus' )     => 'material',
            );

            $params    = array(
                'base'        => 'urus_button',
                'name'        => esc_html__( 'Button', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Button', 'urus' ),
                'params'      => array(
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'param_name'  => 'layout',
                        'value'       => array(
                            esc_html__( 'Default', 'urus' )         => 'default',
                            esc_html__('Text', 'urus')              => 'text',
                            esc_html__('Text With Icon', 'urus')    => 'text-icon',
                            esc_html__('Text Underline - Short', 'urus')         => 'text-line',
                            esc_html__('Text Underline - Full', 'urus')         => 'text-line1',
                        ),
                        'std'         => 'default',
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__('Link', 'urus'),
                        'param_name'  => 'link',
                        'admin_label' => true,
                        'std'         =>'#'
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
                        "type" => "colorpicker",
                        "class" => "",
                        "heading" => esc_html__( "Color", 'urus' ),
                        "param_name" => "hover_color",
                        "value" => '', //Default Red color
                        "description" => esc_html__( "Choose color", 'urus' ),
                        'group'      => esc_html__( 'Hover', 'urus' ),
                    ),
                    array(
                        "type" => "colorpicker",
                        "class" => "",
                        "heading" => esc_html__( "Background Color", 'urus' ),
                        "param_name" => "hover_background",
                        "value" => '', //Default Red color
                        "description" => esc_html__( "Choose color", 'urus' ),
                        'group'      => esc_html__( 'Hover', 'urus' ),
                    ),
                    array(
                        "type" => "colorpicker",
                        "class" => "",
                        "heading" => esc_html__( "Border Color", 'urus' ),
                        "param_name" => "hover_border",
                        "value" => '', //Default Red color
                        "description" => esc_html__( "Choose color", 'urus' ),
                        'group'      => esc_html__( 'Hover', 'urus' ),
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'urus'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'urus' ),
                    ),
                ),
            );
    
            $icon_params= array();
            if($icon_types){
                $icon_params [] = array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Use Icon', 'urus' ),
                    'param_name'  => 'use_icon',
                    'value'       => array(
                        esc_html__( 'Yes', 'urus' ) => 'yes',
                        esc_html__('No', 'urus') => 'no',
                    ),
                    'std'         => 'no',
                );
                $icon_params[] = array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Icon library', 'urus' ),
                    'value'       => $icon_types,
                    'admin_label' => true,
                    'param_name'  => 'type',
                    'description' => esc_html__( 'Select icon library.', 'urus' ),
                    'dependency'  => array(
                        'element' => 'use_icon',
                        'value'   => array( 'yes' ),
                    ),
                );
                
                foreach ($icon_types as $icon_type){
                    $icon_params[] = array(
                        'param_name'  => 'icon_'.$icon_type,
                        'heading'     => esc_html__( 'Icon', 'urus' ),
                        'type'        => 'iconpicker',
                        'settings'    => array(
                            'emptyIcon' => false,
                            'type'      => $icon_type,
                        ),
                        'dependency'  => array(
                            'element' => 'type',
                            'value'   => $icon_type,
                        ),
                    );
                }
            }
            $params['params'] = array_merge(
                $params['params'],
                $icon_params
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}