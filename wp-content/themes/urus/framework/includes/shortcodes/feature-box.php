<?php
if( !class_exists('Urus_Shortcodes_Feature_Box')){
    class Urus_Shortcodes_Feature_Box extends Urus_Shortcodes{

        public $shortcode = 'feature_box';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_feature_box', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_feature_box_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_feature_box', $atts ) : $atts;
            $icon ='';
            extract( $atts );
            $css_class    = array( 'urus-feature-box' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_feature_box', $atts );
            ob_start();


            if ( $atts['type']) {
                $icon = $atts['icon_' . $atts['type']];
                vc_icon_element_fonts_enqueue( $atts['type'] );
            }
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="icon">
                    <span class="<?php echo esc_attr($icon);?>"></span>
                </div>
                <div class="content">
                    <?php if( $atts['title']):?>
                    <h3 class="title"><?php echo esc_html( $atts['title']);?></h3>
                    <?php endif;?>
                    <?php if( $atts['subtitle']):?>
                    <div class="subtitle"><?php echo esc_html( $atts['subtitle']);?></div>
                    <?php endif;?>
                </div>
            </div>
            <?php
            return apply_filters('urus_shortcode_feature_box_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){
            $icon_types = array(
                esc_html__( 'Font Awesome', 'urus' ) => 'fontawesome',
                esc_html__( 'Material', 'urus' )     => 'material',
            );

            $icon_types = apply_filters('urus_vc_icon_types', $icon_types);

            $params    = array(
                'base'        => 'urus_feature_box',
                'name'        => esc_html__( 'Feature box', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display a Feature box', 'urus' ),
                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'param_name'  => 'layout',
                        'value'       => array(
                            esc_html__( 'Default', 'urus' ) => 'default',
                            esc_html__( 'Layout 01', 'urus' ) => 'layout1',
                            esc_html__( 'Layout 02', 'urus' ) => 'layout2',
                            esc_html__( 'Layout 03', 'urus' ) => 'layout3',
                            esc_html__( 'Layout 04', 'urus' ) => 'layout4',
                            esc_html__( 'Layout 05', 'urus' ) => 'layout5',
                        ),
                        'std'         => 'default',
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Sub title', 'urus' ),
                        'param_name'    => 'subtitle',
                        'admin_label'   => true,
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
                $icon_params[] = array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Icon library', 'urus' ),
                    'value'       => $icon_types,
                    'admin_label' => true,
                    'param_name'  => 'type',
                    'description' => esc_html__( 'Select icon library.', 'urus' ),
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