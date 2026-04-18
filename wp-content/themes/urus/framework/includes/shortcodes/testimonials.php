<?php
if( !class_exists('Urus_Shortcodes_Testimonials')){
    class Urus_Shortcodes_Testimonials extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'testimonials';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_testimonials', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_testimonials_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_testimonials', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-testimonials' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_testimonials', $atts );
            ob_start();
            $nav_position = isset($atts['owl_nav_position']) ? $atts['owl_nav_position'] : "";
            $testimonials= vc_param_group_parse_atts( $atts['testimonials'] );
            $owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
	        $owl_dots_style =  isset($atts['owl_dots_style']) ? $atts['owl_dots_style'] : "";
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if( !empty($testimonials)):?>
                    <div class="testimonials swiper-container urus-swiper <?php echo esc_attr($nav_position) ?>" data-dots-style="<?php echo esc_attr( $owl_dots_style ) ?>" <?php echo esc_attr( $owl_settings ); ?>>
                        <div class="swiper-wrapper">
                            <?php foreach ($testimonials as $testimonial): ?>
                                <div class="swiper-slide">
                                    <div class="testimonial-item <?php echo esc_attr($testimonial['layout']) ?>">
	                                    <?php if ($testimonial['layout'] == "layout2" && $testimonial['title']): ?>
                                            <h3 class="title"><?php echo esc_html($testimonial['title']); ?></h3>
	                                    <?php endif; ?>
                                        <?php if( $testimonial['image'] && $testimonial['layout'] == "default"):?>
                                            <div class="image">
                                                <?php echo wp_get_attachment_image($testimonial['image'],'full');?>
                                            </div>
                                        <?php endif;?>
                                        <div class="content">
                                            <div class="text"><?php echo esc_html($testimonial['text']);?></div>
	                                        <?php if( $testimonial['image'] && $testimonial['layout'] != "default"):?>
                                                <div class="image">
			                                        <?php echo wp_get_attachment_image($testimonial['image'],'full');?>
                                                </div>
	                                        <?php endif;?>
                                            <h6 class="name">
                                                <span><?php echo esc_html($testimonial['name']);?></span>
                                                <span><?php echo esc_html($testimonial['position']);?></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;?>
                        </div>
                        <!-- If we need pagination -->
                        <div class="swiper-pagination"></div>
                        
                    </div>
                    <!-- If we need navigation buttons -->
                    <div class="slick-arrow next">
                        <?php echo familab_icons('arrow-right'); ?>
                    </div>
                    <div class="slick-arrow prev">
                        <?php echo familab_icons('arrow-left'); ?>
                    </div>
                <?php endif;?>
            </div>

            <?php
            return apply_filters('urus_shortcode_testimonials_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_testimonials',
                'name'        => esc_html__( 'Testimonials', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display testimonials', 'urus' ),
                'params'      => array(
                    array(
                        'type' => 'param_group',
                        'value' => '',
                        'param_name' => 'testimonials',
                        // Note params is mapped inside param-group:
                        'params' => array(
	                        array(
		                        "type"        => 'dropdown',
		                        "heading"     => esc_html__('Layout', 'urus'),
		                        "param_name"  => 'layout',
		                        "value"       => array(
			                        esc_html__( 'Default', 'urus' ) => 'default',
			                        esc_html__( 'Layout 1', 'urus' ) => 'layout1',
			                        esc_html__( 'Layout 2', 'urus' ) => 'layout2',
                                ),
		                        'std'         => 'default',
	                        ),
                            array(
                                "type"        => 'attach_image',
                                "heading"     => esc_html__('Image', 'urus'),
                                "param_name"  => 'image',
                                "value"       => '',
                            ),
                            array(
                                'type'       => 'textfield',
                                'value'      => '',
                                'heading'    => esc_html__('Name', 'urus'),
                                'param_name' => 'name',
                            ),
                            array(
                                'type'       => 'textfield',
                                'value'      => '',
                                'heading'    => esc_html__('Position', 'urus'),
                                'param_name' => 'position',
                            ),
	                        array(
		                        'type'       => 'textfield',
		                        'value'      => '',
		                        'heading'    => esc_html__('Title', 'urus'),
		                        'param_name' => 'title',
		                        'dependency'  => array(
			                        'element'            => 'layout',
			                        'value' => array(
				                        'layout2',
			                        ),
		                        ),
	                        ),
                            array(
                                'type'       => 'textarea',
                                'value'      => '',
                                'heading'    => esc_html__('Text', 'urus'),
                                'param_name' => 'text',
                            )
                        )
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