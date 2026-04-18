<?php
if (!class_exists('Urus_Shortcodes_Full_Search')){
	class Urus_Shortcodes_Full_Search extends Urus_Shortcodes{
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'full_search';
		function __construct(){
			parent::__construct();
			add_action( 'vc_before_init', array($this, 'vc_map'));
		}

		static public function add_css_generate( $atts ){
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_full_search', $atts ) : $atts;
			// Extract shortcode parameters.
			extract( $atts );

			$css = '';
			$class ='.'.$atts['urus_custom_id'];
			$css .= $class.' .label_text{ color:'.$atts['label_color'].'!important;}';
			$css .= $class.' .label_text:after{ background-color:'.$atts['label_color'].'!important;}';

			return apply_filters( 'urus_shortcodes_full_search_css_render', $css, $atts );
		}
		public function output_html( $atts, $content = null ){
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_full_search', $atts ) : $atts;
			extract( $atts );
			$css_class    = array( 'urus_full_search' );
			$css_class[]  = $atts['el_class'];
			$css_class[]  = $atts['urus_custom_id'];
			$class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
			$css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_full_search', $atts );
			$title = isset($atts['title']) && !empty($atts['title']) ? $atts['title'] : "";
			$link = isset($atts['link']) && !empty($atts['link']) ? $atts['link'] : "#";
			$button = isset($atts['button_text']) && !empty($atts['button_text']) ? $atts['button_text'] : "";
			$placeholder = isset($atts['placeholder']) && !empty($atts['placeholder']) ? $atts['placeholder'] : "";
			ob_start();
			?>
			<div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
				<div class="search-wrapper">
					<?php if ($title): ?>
                        <h3 class="title"><?php echo esc_html($title); ?></h3>
					<?php endif; ?>
					<?php
					Urus_Pluggable_WooCommerce::header_search_form( false, false, true , $placeholder, true);
					?>
					<?php if ($button): ?>
                        <div class="inner-button">
                            <a href="<? echo esc_html($link) ?>" class="button button-link"><?php echo esc_html($button) ?></a>
                        </div>
					<?php endif; ?>
                </div>
			</div>
			<?php
			return apply_filters('urus_shortcode_full_search_output', ob_get_clean(), $atts, $content);
		}
		public function vc_map(){

			$params    = array(
				'base'        => 'urus_full_search',
				'name'        => esc_html__( 'Search Form', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
				'category'    => esc_html__( 'Urus Elements', 'urus' ),
				'description' => esc_html__( 'Display Product Search Form', 'urus' ),
				'params'      => array(
					array(
						'type'          => 'textfield',
						'heading'       => esc_html__( 'Title', 'urus' ),
						'param_name'    => 'title',
						'admin_label'   => true,
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
						'type'          => 'textfield',
						'heading'       => esc_html__( 'Placeholder on input search', 'urus' ),
						'param_name'    => 'placeholder',
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