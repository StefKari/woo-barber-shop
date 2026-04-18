<?php
if (!class_exists('Urus_Shortcodes_Special_Banner')){
    class Urus_Shortcodes_Special_Banner extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'special_banner';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_special_banner', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            
            $css = '';
            $class ='.'.$atts['urus_custom_id'];
	        $atts['label_color'] = empty($atts['label_color']) ? "#000" : $atts['label_color'];
            $css .= $class.' .label_text{ color:'.$atts['label_color'].';}';

	        wp_register_style( 'urus_special_banner', false );
	        wp_enqueue_style( 'urus_special_banner' );
	        wp_add_inline_style( 'urus_special_banner', $css );
	        return;
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_special_banner', $atts ) : $atts;
            self::add_css_generate($atts);
            extract( $atts );
            $css_class    = array( 'urus_special_banner' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_special_banner', $atts );
            $text_link = array('style1', 'style2', 'style4', 'style7', 'style10', 'style11');
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">

                <?php if($atts['image']):?>
                <div class="image">
                    <?php echo wp_get_attachment_image($atts['image'],'full');?>
                    <?php if(in_array($atts['layout'], $text_link)):?>
                        <a class="link" href="<?php echo esc_url($atts['link']);?>"></a>
                    <?php endif;?>
                    <?php if( $atts['layout'] == 'style7' && $atts['label_text']):?>
                        <span class="text-label">
                            <?php echo esc_html($atts['label_text']);?>
                        </span>
                    <?php endif;?>
                </div>
                <?php endif;?>
                <div class="content-banner">
                    <?php if( $atts['subtitle'] && $atts['layout'] == "style1"):?>
                        <span class="subtitle">
                            <?php echo esc_html($atts['subtitle']);?>
                        </span>
                    <?php endif;?>
                    <?php if( $atts['title']):?>
                        <h3 class="title">
                            <?php echo esc_html($atts['title']);?>
                        </h3>
                    <?php endif;?>
                    <?php if( $atts['subtitle'] && $atts['layout'] != "default" && $atts['layout'] != "style1"):?>
                        <span class="subtitle">
                            <?php echo esc_html($atts['subtitle']);?>
                        </span>
                    <?php endif;?>
	                <?php if( $atts['label_text'] && ($atts['layout'] == 'style15' || $atts['layout'] == 'style16')):?>
                        <a href="<?php echo esc_url($atts['link']);?>" class="label_text">
			                <?php echo esc_html($atts['label_text']);?>
                        </a>
	                <?php endif;?>
	                <?php if($atts['button_text']):?>
                        <a class="banner-button button" href="<?php echo esc_url($atts['link']);?>"><?php echo esc_html($atts['button_text']);?></a>
	                <?php endif;?>
	                <?php if( $atts['label_text'] && $atts['layout'] !='style7' && $atts['layout'] != 'style15' && $atts['layout'] != 'style16'):?>
		                
                        <a href="<?php echo esc_url($atts['link']);?>" class="label_text">
			                <?php echo esc_html($atts['label_text']);?>
                        </a>
	                <?php endif;?>
                </div>
            </div>
            <?php
            return apply_filters('urus_shortcode_special_banner_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_special_banner',
                'name'        => esc_html__( 'Special Banner', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Special Banner', 'urus' ),
                'params'      => array(
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Layout', 'urus' ),
                        'param_name' => 'layout',
                        'value'      => array(
                            esc_html__( 'Default', 'urus' )   => 'default',
                            esc_html__( 'Style 01', 'urus' )   => 'style1',
                            esc_html__( 'Style 02', 'urus' )   => 'style2',
                            esc_html__( 'Style 03', 'urus' )   => 'style3',
                            esc_html__( 'Style 04', 'urus' )   => 'style4',
                            esc_html__( 'Style 05', 'urus' )   => 'style5',
                            esc_html__( 'Style 06', 'urus' )   => 'style6',
                            esc_html__( 'Style 07', 'urus' )   => 'style7',
                            esc_html__( 'Style 08', 'urus' )   => 'style8',
                            esc_html__( 'Style 09', 'urus' )   => 'style9',
                            esc_html__( 'Style 10', 'urus' )   => 'style10',
                            esc_html__( 'Style 11', 'urus' )   => 'style11',
                            esc_html__( 'Style 12', 'urus' )   => 'style12',
                            esc_html__( 'Style 13', 'urus' )   => 'style13',
                            esc_html__( 'Style 14', 'urus' )   => 'style14',
                            esc_html__( 'Style 15', 'urus' )   => 'style15',
                            esc_html__( 'Style 16', 'urus' )   => 'style16',
                        ),
                        'std'        => 'default',
                    ),
                    array(
                        "type"        => 'attach_image',
                        "heading"     => esc_html__('Image', 'urus'),
                        "param_name"  => 'image',
                        "value"       => '',
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                        'dependency'  => array(
	                        'element'            => 'layout',
	                        'value_not_equal_to' => array(
		                        'style13',
	                        ),
                        ),
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Subtitle', 'urus' ),
                        'param_name'  => 'subtitle',
                        'dependency'  => array(
                            'element'            => 'layout',
                            'value_not_equal_to' => array(
                                'default',
                                'style2',
                                'style4',
                                'style5',
                                'style8',
                                'style10',
                                'style11',
	                            'style12',
	                            'style13',
	                            'style14',
                            ),
                        ),
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Label text', 'urus' ),
                        'param_name'  => 'label_text',
                        'dependency'  => array(
                            'element'            => 'layout',
                            'value' => array(
                                'style4',
                                'style15',
                                'style16',
                            ),
                        ),
                    ),
	                array(
		                'type'        => 'colorpicker',
		                'heading'     => esc_html__( 'Color label text', 'urus' ),
		                'param_name'  => 'label_color',
		                'dependency'  => array(
			                'element'            => 'layout',
			                'value' => array(
				                'style4',
			                ),
		                ),
	                ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Link', 'urus' ),
                        'param_name'    => 'link',
                        'default' => '#',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Button text', 'urus' ),
                        'param_name'    => 'button_text',
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
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}