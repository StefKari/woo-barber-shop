<?php

use \Elementor\Repeater;

if ( ! class_exists( 'Urus_Elementor_Slide_Gallery' ) ) {
	class Urus_Elementor_Slide_Gallery extends Urus_Elementor {
		public $name = 'slide_gallery';
		public $title = 'Slide Gallery';
		public $icon = 'eicon-slide';

		/**
		 * Register the widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 */
		protected function _register_controls() {
			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'urus' ),
				]
			);
			$this->add_control( 'layout', [
				'label'   => esc_html__( 'Layout', 'urus' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'urus' ),
					'layout1' => esc_html__( 'Layout 01', 'urus' ),
				],
				'default' => 'default',
			] );

			$repeater = new Repeater();

			$repeater->add_control( 'image', [
				'label'   => __( 'Choose Image', 'urus' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			] );
			$repeater->add_responsive_control(
				'align',
				[
					'label'     => __( 'Alignment', 'urus' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => [
						'left'   => [
							'title' => __( 'Left', 'elementor' ),
							'icon'  => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'elementor' ),
							'icon'  => 'fa fa-align-center',
						],
						'right'  => [
							'title' => __( 'Right', 'elementor' ),
							'icon'  => 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .single-image_wrapper' => 'text-align: {{VALUE}};',
					],
                    'default' => 'center'
				]
			);
			$repeater->add_control(
				'img_link',
				[
					'label'         => __( 'Link', 'urus' ),
					'type'          => \Elementor\Controls_Manager::URL,
					'placeholder'   => __( 'https://your-link.com', 'urus' ),
					'show_external' => true,
					'default'       => [
						'url'         => '#',
						'is_external' => true,
						'nofollow'    => true,
					],
				]
			);
			$repeater->add_control( 'target_link', [
				'label'   => esc_html__( 'Target', 'urus' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'_blank'  => esc_html__( 'Blank', 'urus' ),
					'_self'   => esc_html__( 'Self', 'urus' ),
					'_parent' => esc_html__( 'Parent', 'urus' ),
					'_top'    => esc_html__( 'Top', 'urus' ),
				],
				'default' => '_blank',
			] );
			$this->add_control(
				'list',
				[
					'label'       => __( 'Image List', 'plugin-domain' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
					],
					'title_field' => 'Item',
				]
			);
			$this->end_controls_section();

			$this->start_controls_section(
				'carousel_settings_section',
				[
					'label' => esc_html__( 'Carousel Settings', 'urus' ),
				]
			);
			$carousel_settings = Urus_Pluggable_Elementor::elementor_carousel();

			foreach ( $carousel_settings as $key => $value ) {
				$this->add_control( $key, $value );
			}
			$this->end_controls_section();

		}

		/**
		 * Render the widget output on the frontend.
		 *
		 * Written in PHP and used to generate the final HTML.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 */
		protected function render() {
			$atts         = $this->get_settings_for_display();
			$css_class    = array( 'urus-slide equal-container' );
			$css_class[]  = $atts['layout'];
			$css_class[]  = isset( $atts['owl_nav_position'] ) ? $atts['owl_nav_position'] : "";
			$owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="urus-slide-wapper swiper-container urus-swiper custom-slide " <?php echo esc_attr( $owl_settings ); ?>>
                    <div class="swiper-wrapper">
						<?php
						foreach ( $atts['list'] as $index => $value ):
							$img_link = $value['img_link'];
							$target_link = $value['target_link'];
							$this->add_render_attribute( 'link', 'class', 'img-link' );
							$this->add_render_attribute( 'link', 'href', $value['img_link'] );
							$this->add_render_attribute( 'link', 'target', $value['target_link'] );
							?>
                            <div class="swiper-slide">
                                <figure class="single-image_wrapper">
                                    <a <?php echo $this->get_render_attribute_string( 'link' ) ?>>
                                        <figure>
											<?php echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $value, 'thumbnail', 'image' ); ?>
                                        </figure>
                                    </a>
                                </figure>
                            </div>
						<?php endforeach; ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>
                </div>
                <!-- If we need navigation buttons -->
                <div class="slick-arrow next">
                    <span class="urus-icon-next"></span>
                </div>
                <div class="slick-arrow prev">
                    <span class="urus-icon-prev"></span>
                </div>
            </div>
			<?php
		}
	}
}