<?php
if( !class_exists('Urus_Shortcodes_Newsletter')){
    class Urus_Shortcodes_Newsletter extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'newsletter';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_newsletter', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_newsletter_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_newsletter', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-newsletter' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $css_class[]  = self::getCSSAnimation($css_animation);
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_newsletter', $atts );
            $newsletter_inner = array('layout3', 'layout4');
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if(in_array($atts['layout'], $newsletter_inner)): ?>
                    <div class="newsletter-inner">
                <?php endif; ?>
                   <?php if($atts['title']):?>
                       <h3 class="title"><?php echo esc_html($atts['title']);?></h3>
                   <?php endif;?>
                    <?php if($atts['subtitle']):?>
                        <div class="subtitle"><?php echo esc_html($atts['subtitle']);?></div>
                    <?php endif;?>
                   <div class="urus-newsletter-form">
                       <input type="email" name="email" class="form-field" placeholder="<?php echo esc_attr($atts['placeholder']);?>">
                       <button class="newsletter-form-button"><?php echo esc_html($atts['button_text']);?></button>
                   </div>
                 <?php if(in_array($atts['layout'], $newsletter_inner)): ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return apply_filters('urus_shortcode_newsletter_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_newsletter',
                'name'        => esc_html__( 'Newsletter Form', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Section newsletter form', 'urus' ),
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
                            esc_html__( 'Layout 06', 'urus' ) => 'layout6',
                            esc_html__( 'Layout 07', 'urus' ) => 'layout7',
                            esc_html__( 'Layout 08', 'urus' ) => 'layout8',
                            esc_html__( 'Layout 09', 'urus' ) => 'layout9',
                            esc_html__( 'Layout 10', 'urus' ) => 'layout10',
                            esc_html__( 'Layout 11', 'urus' ) => 'layout11',
                            esc_html__( 'Layout 12', 'urus' ) => 'layout12',
                        ),
                        'std'         => 'default',
                        'admin_label'   => true,
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
                        'type'       => 'textfield',
                        'heading'    => esc_html__('Placeholder', 'urus'),
                        'param_name' => 'placeholder',
                        'std'        => 'Enter your email address'
                    ),
                    array(
                        'type'       => 'textfield',
                        'heading'    => esc_html__('Button Text', 'urus'),
                        'param_name' => 'button_text',
                        'std'        =>'Sign up'
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