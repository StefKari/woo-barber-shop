<?php
if( !class_exists('Tis_Visual_Composer')){
    class Tis_Visual_Composer{
        protected static $initialized = false;
        /**
         * Initialize pluggable functions.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            add_action( 'vc_before_init', array(__CLASS__, 'vc_map'));
           add_shortcode('tis_vc',array(__CLASS__,'output_html'));
        
            self::$initialized = true;
        }
        
        public static function output_html($atts, $content = null){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'tis_vc', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'tis-vc' );
            $css_class[]  = $atts['el_class'];;
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'tis_vc', $atts );
        
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php echo do_shortcode(Tis_Shortcode::get_shortcode_string($atts['instagram']));?>
            </div>
    
            <?php
            return apply_filters('tis_vc_output', ob_get_clean(), $atts, $content);
        }
        
        public static function vc_map(){
            $args = array(
                'posts_per_page'   => -1,
                'offset'           => 0,
                'post_type'        => 'familab-instagram',
                'post_status'      => 'publish',
                'suppress_filters' => true,
            );
            $posts_array = get_posts( $args );
            $settings = array();
            if( !empty($posts_array)){
                foreach ($posts_array as $post){
                    $settings[$post->post_title] = $post->ID;
                }
            }
            
            $params    = array(
                'base'        => 'tis_vc',
                'name'        => esc_html__( 'Instagram Shop', 'eveland' ),
                'icon'        => '',
                'category'    => esc_html__( 'Familab Plugins', 'eveland' ),
                'description' => esc_html__( 'Display as Instagram shop', 'eveland' ),
                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Instagram', 'eveland' ),
                        'value'       => $settings,
                        'admin_label' => true,
                        'param_name'  => 'instagram',
                        'description' => esc_html__( 'Select menu to display.', 'eveland' ),
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'eveland'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'eveland' ),
                    ),
                ),
            );
            $params = apply_filters('tis_vc_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}
Tis_Visual_Composer::initialize();