<?php
if( !class_exists('Urus_Shortcodes_Progress')){
    class Urus_Shortcodes_Progress extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'progress';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_progress', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $percent = (isset($atts['percent']) && is_numeric($atts['percent'])) ? $atts['percent'] :0;
            $start_deg = (isset($atts['deg']) && is_numeric($atts['deg'])) ? $atts['deg'] :0;
            if( $start_deg > 360){
                $start_deg = 360;
            }
            if( $percent <=0) $percent = 1;
            if( $percent > 100) $percent = 100;
            $class = '.'.$atts['urus_custom_id'];
            $deg = $percent*3.6;
            $deg2 =0;
            if( $deg > 180){
                $deg2 =  $deg - 180;
                $deg = 180;

            }


            
            $css = '
            @keyframes loading-'.$atts['urus_custom_id'].'{
                0%{
                    -webkit-transform: rotate(0deg);
                    transform: rotate(0deg);
                }
                100%{
                    -webkit-transform: rotate('.$deg.'deg);
                    transform: rotate('.$deg.'deg);
                }
            }
            @keyframes loading2-'.$atts['urus_custom_id'].'{
                0%{
                    -webkit-transform: rotate(0deg);
                    transform: rotate(0deg);
                }
                100%{
                    -webkit-transform: rotate('.$deg2.'deg);
                    transform: rotate('.$deg2.'deg);
                }
            }
            ';
            if( $percent <=50 ){
                $css .= $class.' .progress .progress-left{ display:none;}';
            }
            $css .= $class.' .progress .progress-right .progress-bar{ animation: loading-'.$atts['urus_custom_id'].' 1.8s linear forwards;}';
            $css .= $class.' .progress .progress-left .progress-bar{  animation: loading2-'.$atts['urus_custom_id'].' 1.2s linear forwards 1.8s;}';
            return apply_filters( 'urus_shortcodes_progress_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_progress', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-progress-bar' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_title', $atts );
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="progress-wrap">
                    <div class="progress black">
                        <span class="progress-left">
                            <span class="progress-bar"></span>
                        </span>
                                <span class="progress-right">
                            <span class="progress-bar"></span>
                        </span>
                    </div>
                    <div class="progress-value">
                        <?php if( $atts['icon']):?>
                        <span class="icon <?php echo esc_attr($atts['icon']);?>"></span>
                        <?php endif;?>
                        <?php if( $atts['number']):?>
                        <span class="number"><?php echo esc_html($atts['number']);?></span>
                        <?php endif;?>
                        <?php if( $atts['text']):?>
                        <span class="text"><?php echo esc_html($atts['text']);?></span>
                        <?php endif;?>
                    </div>
                </div>
                <?php if( $atts['title']):?>
                <h3 class="title"><?php echo esc_html($atts['title']);?></h3>
                <?php endif;?>
            </div>
            <?php
            return apply_filters('urus_shortcode_progress_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_progress',
                'name'        => esc_html__( 'Progress Bar', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Progress Bar', 'urus' ),
                'params'      => array(
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                    ),
                    array(
                        'param_name'  => 'icon',
                        'heading'     => esc_html__( 'Icon', 'urus' ),
                        'type'        => 'iconpicker',
                        'settings'    => array(
                            'emptyIcon' => false,
                            'type'      => 'fontawesome',
                        ),
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Number', 'urus' ),
                        'param_name'    => 'number',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Text', 'urus' ),
                        'param_name'    => 'text',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__('percent', 'urus'),
                        'param_name'  => 'percent',
                        'admin_label' => true,
                        'default'     => 50
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