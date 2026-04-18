<?php
if(!class_exists('Urus_Shortcodes_Socials')){
    class Urus_Shortcodes_Socials extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'socials';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_socials', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_socials', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-socials' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_socials', $atts );
            $socials = explode(',', $atts['use_socials']);
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if($atts['title']):?>
                    <h3 class="title"><?php echo esc_html($atts['title']);?></h3>
                <?php endif;?>
                <?php if($atts['subtitle']):?>
                    <div class="subtitle"><?php echo esc_html($atts['subtitle']);?></div>
                <?php endif;?>
                <?php if(!empty($socials)):?>
                <div class="socials clearfix">
                    <?php foreach ($socials as $social):?>
                        <?php Urus_Helper::display_social($social);?>
                    <?php endforeach;?>
                </div>
                <?php endif;?>
            </div>
            <?php
            return apply_filters('urus_shortcode_slide_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){
            $socials = array();
            $all_socials = Urus_Helper::get_all_social();
            if( $all_socials ){
                foreach ($all_socials as $key =>  $social)
                    $socials[$social['name']] = $key;
            }
            $params    = array(
                'base'        => 'urus_socials',
                'name'        => esc_html__( 'Socials', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display socials', 'urus' ),
                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'param_name'  => 'layout',
                        'value'       => array(
                            esc_html__( 'Default', 'urus' ) => 'default',
                            esc_html__('Layout 01', 'urus') => 'layout1',
                            esc_html__('Layout 02', 'urus') => 'layout2',
                            esc_html__('Layout 03', 'urus') => 'layout3',
                            esc_html__('Layout 04', 'urus') => 'layout4',
                        ),
                        'std'         => 'defaultr',
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
                        'type'        => 'checkbox',
                        'heading'     => esc_html__( 'Display on', 'urus' ),
                        'param_name'  => 'use_socials',
                        'class'         => 'checkbox-display-block',
                        'value'       => $socials,
                        'admin_label'   =>  true,
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