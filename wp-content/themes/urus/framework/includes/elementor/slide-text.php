<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
if (!class_exists('Urus_Elementor_Slide_Text')){
	class Urus_Elementor_Slide_Text extends Urus_Elementor {

		public $name = 'slide_text';
		public $title = 'Slide Text';
		public $icon ='eicon-slideshow';


		public function get_script_depends() {
			return [ 'imagesloaded', 'jquery-slick' ];
		}

		public static function get_button_sizes() {
			return [
				'xs' => __( 'Extra Small', 'urus' ),
				'sm' => __( 'Small', 'urus' ),
				'md' => __( 'Medium', 'urus' ),
				'lg' => __( 'Large', 'urus' ),
				'xl' => __( 'Extra Large', 'urus' ),
			];
		}

		protected function _register_controls() {
			$this->start_controls_section(
				'section_slides',
				[
					'label' => __( 'Slides', 'urus' ),
				]
			);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'slides_repeater' );

			$repeater->start_controls_tab( 'background', [ 'label' => __( 'Background', 'urus' ) ] );

			$repeater->add_control(
				'background_color',
				[
					'label' => __( 'Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#bbbbbb',
					'selectors' => [
						'{{WRAPPER}}  {{CURRENT_ITEM}} .slick-slide-bg' => 'background-color: {{VALUE}}',
					],
				]
			);

			$repeater->add_control(
				'background_image',
				[
					'label' => __( 'Image', 'Background Control', 'urus' ),
					'type' => Controls_Manager::MEDIA,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-bg' => 'background-image: url({{URL}})',
					],
				]
			);

			$repeater->add_control(
				'background_size',
				[
					'label' => _x( 'Size', 'Background Control', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'cover',
					'options' => [
						'cover' => _x( 'Cover', 'Background Control', 'urus' ),
						'contain' => _x( 'Contain', 'Background Control', 'urus' ),
						'auto' => _x( 'Auto', 'Background Control', 'urus' ),
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-bg' => 'background-size: {{VALUE}}',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'background_image[url]',
								'operator' => '!=',
								'value' => '',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'background_ken_burns',
				[
					'label' => __( 'Ken Burns Effect', 'urus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => '',
					'separator' => 'before',
					'conditions' => [
						'terms' => [
							[
								'name' => 'background_image[url]',
								'operator' => '!=',
								'value' => '',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'zoom_direction',
				[
					'label' => __( 'Zoom Direction', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'in',
					'options' => [
						'in' => __( 'In', 'urus' ),
						'out' => __( 'Out', 'urus' ),
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'background_ken_burns',
								'operator' => '!=',
								'value' => '',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'background_overlay',
				[
					'label' => __( 'Background Overlay', 'urus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => '',
					'separator' => 'before',
					'conditions' => [
						'terms' => [
							[
								'name' => 'background_image[url]',
								'operator' => '!=',
								'value' => '',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'background_overlay_color',
				[
					'label' => __( 'Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'default' => 'rgba(0,0,0,0.5)',
					'conditions' => [
						'terms' => [
							[
								'name' => 'background_overlay',
								'value' => 'yes',
							],
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-inner .urus-background-overlay' => 'background-color: {{VALUE}}',
					],
				]
			);

			$repeater->add_control(
				'background_overlay_blend_mode',
				[
					'label' => __( 'Blend Mode', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => __( 'Normal', 'urus' ),
						'multiply' => 'Multiply',
						'screen' => 'Screen',
						'overlay' => 'Overlay',
						'darken' => 'Darken',
						'lighten' => 'Lighten',
						'color-dodge' => 'Color Dodge',
						'color-burn' => 'Color Burn',
						'hue' => 'Hue',
						'saturation' => 'Saturation',
						'color' => 'Color',
						'exclusion' => 'Exclusion',
						'luminosity' => 'Luminosity',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'background_overlay',
								'value' => 'yes',
							],
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-inner .urus-background-overlay' => 'mix-blend-mode: {{VALUE}}',
					],
				]
			);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'content', [ 'label' => __( 'Content', 'urus' ) ] );

			$repeater->add_control(
				'heading',
				[
					'label' => __( 'Title & Description', 'urus' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'Slide Heading', 'urus' ),
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'description',
				[
					'label' => __( 'Description', 'urus' ),
					'type' => Controls_Manager::TEXTAREA,
					'default' => __( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'urus' ),
					'show_label' => false,
				]
			);

			$repeater->add_control(
				'button_text',
				[
					'label' => __( 'Button Text', 'urus' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'Click Here', 'urus' ),
				]
			);

			$repeater->add_control(
				'link',
				[
					'label' => __( 'Link', 'urus' ),
					'type' => Controls_Manager::URL,
					'placeholder' => __( 'https://your-link.com', 'urus' ),
				]
			);

			$repeater->add_control(
				'link_click',
				[
					'label' => __( 'Apply Link On', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'slide' => __( 'Whole Slide', 'urus' ),
						'button' => __( 'Button Only', 'urus' ),
					],
					'default' => 'slide',
					'conditions' => [
						'terms' => [
							[
								'name' => 'link[url]',
								'operator' => '!=',
								'value' => '',
							],
						],
					],
				]
			);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'style', [ 'label' => __( 'Style', 'urus' ) ] );

			$repeater->add_control(
				'custom_style',
				[
					'label' => __( 'Custom', 'urus' ),
					'type' => Controls_Manager::SWITCHER,
					'description' => __( 'Set custom style that will only affect this specific slide.', 'urus' ),
				]
			);

			$repeater->add_control(
				'horizontal_position',
				[
					'label' => __( 'Horizontal Position', 'urus' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'urus' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'urus' ),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'urus' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-inner .urus-slide-content' => '{{VALUE}}',
					],
					'selectors_dictionary' => [
						'left' => 'margin-right: auto',
						'center' => 'margin: 0 auto',
						'right' => 'margin-left: auto',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'custom_style',
								'value' => 'yes',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'vertical_position',
				[
					'label' => __( 'Vertical Position', 'urus' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options' => [
						'top' => [
							'title' => __( 'Top', 'urus' ),
							'icon' => 'eicon-v-align-top',
						],
						'middle' => [
							'title' => __( 'Middle', 'urus' ),
							'icon' => 'eicon-v-align-middle',
						],
						'bottom' => [
							'title' => __( 'Bottom', 'urus' ),
							'icon' => 'eicon-v-align-bottom',
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-inner' => 'align-items: {{VALUE}}',
					],
					'selectors_dictionary' => [
						'top' => 'flex-start',
						'middle' => 'center',
						'bottom' => 'flex-end',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'custom_style',
								'value' => 'yes',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'text_align',
				[
					'label' => __( 'Text Align', 'urus' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'urus' ),
							'icon' => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'urus' ),
							'icon' => 'fa fa-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'urus' ),
							'icon' => 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-inner' => 'text-align: {{VALUE}}',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'custom_style',
								'value' => 'yes',
							],
						],
					],
				]
			);

			$repeater->end_controls_section();
			$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'slides',
				[
					'label' => __( 'Slides', 'urus' ),
					'type' => Controls_Manager::REPEATER,
					'show_label' => true,
					'fields' => $repeater->get_controls(),
					'default' => [
						[
							'heading' => __( 'Slide 1 Heading', 'urus' ),
							'description' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'urus' ),
							'button_text' => __( 'Click Here', 'urus' ),
							'background_color' => '#833ca3',
						],
						[
							'heading' => __( 'Slide 2 Heading', 'urus' ),
							'description' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'urus' ),
							'button_text' => __( 'Click Here', 'urus' ),
							'background_color' => '#4054b2',
						],
						[
							'heading' => __( 'Slide 3 Heading', 'urus' ),
							'description' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'urus' ),
							'button_text' => __( 'Click Here', 'urus' ),
							'background_color' => '#1abc9c',
						],
					],
					'title_field' => '{{{ heading }}}',
				]
			);

			$this->add_responsive_control(
				'slides_height',
				[
					'label' => __( 'Height', 'urus' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 100,
							'max' => 1000,
						],
						'vh' => [
							'min' => 10,
							'max' => 100,
						],
					],
					'default' => [
						'size' => 400,
					],
					'size_units' => [ 'px', 'vh', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .slick-slide' => 'height: {{SIZE}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_slider_options',
				[
					'label' => __( 'Slider Options', 'urus' ),
					'type' => Controls_Manager::SECTION,
				]
			);

			$this->add_control(
				'navigation',
				[
					'label' => __( 'Navigation', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'both',
					'options' => [
						'both' => __( 'Arrows and Dots', 'urus' ),
						'arrows' => __( 'Arrows', 'urus' ),
						'dots' => __( 'Dots', 'urus' ),
						'none' => __( 'None', 'urus' ),
					],
				]
			);

			$this->add_control(
				'pause_on_hover',
				[
					'label' => __( 'Pause on Hover', 'urus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label' => __( 'Autoplay', 'urus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'autoplay_speed',
				[
					'label' => __( 'Autoplay Speed', 'urus' ),
					'type' => Controls_Manager::NUMBER,
					'default' => 5000,
					'condition' => [
						'autoplay' => 'yes',
					],
					'selectors' => [
						'{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
					],
				]
			);

			$this->add_control(
				'infinite',
				[
					'label' => __( 'Infinite Loop', 'urus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'transition',
				[
					'label' => __( 'Transition', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'slide',
					'options' => [
						'slide' => __( 'Slide', 'urus' ),
						'fade' => __( 'Fade', 'urus' ),
					],
				]
			);

			$this->add_control(
				'transition_speed',
				[
					'label' => __( 'Transition Speed', 'urus' ) . ' (ms)',
					'type' => Controls_Manager::NUMBER,
					'default' => 500,
				]
			);

			$this->add_control(
				'content_animation',
				[
					'label' => __( 'Content Animation', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'fadeInUp',
					'options' => [
						'' => __( 'None', 'urus' ),
						'fadeInDown' => __( 'Down', 'urus' ),
						'fadeInUp' => __( 'Up', 'urus' ),
						'fadeInRight' => __( 'Right', 'urus' ),
						'fadeInLeft' => __( 'Left', 'urus' ),
						'zoomIn' => __( 'Zoom', 'urus' ),
					],
				]
			);

			$this->add_control(
				'custom_arrow',
				[
					'label' => __( 'Custom arrow', 'urus' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'no',
				]
			);
			$this->add_control(
				'arrow_left',
				[
					'label' => __( 'Icon Arrow Left', 'urus' ),
					'type' => \Elementor\Controls_Manager::ICON,
					'default' => '',
					'options' => $this->urus_elementer_icon(),
					'condition' => [
						'custom_arrow!' => 'no'
					]
				]
			);
			$this->add_control(
				'arrow_right',
				[
					'label' => __( 'Icon Arrow Right', 'urus' ),
					'type' => \Elementor\Controls_Manager::ICON,
					'default' => '',
					'options' => $this->urus_elementer_icon(),
                    'condition' => [
                            'custom_arrow!' => 'no'
                    ]
				]
			);
			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_slides',
				[
					'label' => __( 'Slides', 'urus' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'content_max_width',
				[
					'label' => __( 'Content Width', 'urus' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' => [ '%', 'px' ],
					'default' => [
						'size' => '66',
						'unit' => '%',
					],
					'tablet_default' => [
						'unit' => '%',
					],
					'mobile_default' => [
						'unit' => '%',
					],
					'selectors' => [
						'{{WRAPPER}} .urus-slide-content' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'slides_padding',
				[
					'label' => __( 'Padding', 'urus' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .slick-slide-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'slides_horizontal_position',
				[
					'label' => __( 'Horizontal Position', 'urus' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'default' => 'center',
					'options' => [
						'left' => [
							'title' => __( 'Left', 'urus' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'urus' ),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'urus' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'prefix_class' => 'urus--h-position-',
				]
			);

			$this->add_control(
				'slides_vertical_position',
				[
					'label' => __( 'Vertical Position', 'urus' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'default' => 'middle',
					'options' => [
						'top' => [
							'title' => __( 'Top', 'urus' ),
							'icon' => 'eicon-v-align-top',
						],
						'middle' => [
							'title' => __( 'Middle', 'urus' ),
							'icon' => 'eicon-v-align-middle',
						],
						'bottom' => [
							'title' => __( 'Bottom', 'urus' ),
							'icon' => 'eicon-v-align-bottom',
						],
					],
					'prefix_class' => 'urus--v-position-',
				]
			);

			$this->add_control(
				'slides_text_align',
				[
					'label' => __( 'Text Align', 'urus' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'urus' ),
							'icon' => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'urus' ),
							'icon' => 'fa fa-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'urus' ),
							'icon' => 'fa fa-align-right',
						],
					],
					'default' => 'center',
					'selectors' => [
						'{{WRAPPER}} .slick-slide-inner' => 'text-align: {{VALUE}}',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_title',
				[
					'label' => __( 'Title', 'urus' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'heading_spacing',
				[
					'label' => __( 'Spacing', 'urus' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .slick-slide-inner .urus-slide-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'heading_color',
				[
					'label' => __( 'Text Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-heading' => 'color: {{VALUE}}',

					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'heading_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .urus-slide-heading',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_description',
				[
					'label' => __( 'Description', 'urus' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'description_spacing',
				[
					'label' => __( 'Spacing', 'urus' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .slick-slide-inner .urus-slide-description:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'description_color',
				[
					'label' => __( 'Text Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-description' => 'color: {{VALUE}}',

					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'description_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_2,
					'selector' => '{{WRAPPER}} .urus-slide-description',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_button',
				[
					'label' => __( 'Button', 'urus' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'button_size',
				[
					'label' => __( 'Size', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'sm',
					'options' => self::get_button_sizes(),
				]
			);

			$this->add_control( 'button_color',
				[
					'label' => __( 'Text Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button' => 'color: {{VALUE}}; border-color: {{VALUE}}',

					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'button_typography',
					'selector' => '{{WRAPPER}} .urus-slide-button',
					'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				]
			);

			$this->add_control(
				'button_border_width',
				[
					'label' => __( 'Border Width', 'urus' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 20,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button' => 'border-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'button_border_radius',
				[
					'label' => __( 'Border Radius', 'urus' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
					'separator' => 'after',
				]
			);

			$this->start_controls_tabs( 'button_tabs' );

			$this->start_controls_tab( 'normal', [ 'label' => __( 'Normal', 'urus' ) ] );

			$this->add_control(
				'button_text_color',
				[
					'label' => __( 'Text Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_background_color',
				[
					'label' => __( 'Background Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_border_color',
				[
					'label' => __( 'Border Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab( 'hover', [ 'label' => __( 'Hover', 'urus' ) ] );

			$this->add_control(
				'button_hover_text_color',
				[
					'label' => __( 'Text Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_hover_background_color',
				[
					'label' => __( 'Background Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button:hover' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_hover_border_color',
				[
					'label' => __( 'Border Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slide-button:hover' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_navigation',
				[
					'label' => __( 'Navigation', 'urus' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => [
						'navigation' => [ 'arrows', 'dots', 'both' ],
					],
				]
			);

			$this->add_control(
				'heading_style_arrows',
				[
					'label' => __( 'Arrows', 'urus' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'navigation' => [ 'arrows', 'both' ],
					],
				]
			);

			$this->add_control(
				'arrows_position',
				[
					'label' => __( 'Arrows Position', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'inside',
					'options' => [
						'inside' => __( 'Inside', 'urus' ),
						'outside' => __( 'Outside', 'urus' ),
					],
					'condition' => [
						'navigation' => [ 'arrows', 'both' ],
					],
				]
			);

			$this->add_control(
				'arrows_size',
				[
					'label' => __( 'Arrows Size', 'urus' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 20,
							'max' => 60,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .urus-slides-wrapper .slick-slider .slick-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'navigation' => [ 'arrows', 'both' ],
					],
				]
			);

			$this->add_control(
				'arrows_color',
				[
					'label' => __( 'Arrows Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slides-wrapper .slick-slider .slick-arrow' => 'color: {{VALUE}};',
					],
					'condition' => [
						'navigation' => [ 'arrows', 'both' ],
					],
				]
			);

			$this->add_control(
				'heading_style_dots',
				[
					'label' => __( 'Dots', 'urus' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'navigation' => [ 'dots', 'both' ],
					],
				]
			);

			$this->add_control(
				'dots_position',
				[
					'label' => __( 'Dots Position', 'urus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'inside',
					'options' => [
						'outside' => __( 'Outside', 'urus' ),
						'inside' => __( 'Inside', 'urus' ),
					],
					'condition' => [
						'navigation' => [ 'dots', 'both' ],
					],
				]
			);

			$this->add_control(
				'dots_size',
				[
					'label' => __( 'Dots Size', 'urus' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 5,
							'max' => 15,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .urus-slides-wrapper .urus-slides .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'navigation' => [ 'dots', 'both' ],
					],
				]
			);

			$this->add_control(
				'dots_color',
				[
					'label' => __( 'Dots Color', 'urus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .urus-slides-wrapper .urus-slides .slick-dots li button:before' => 'color: {{VALUE}};',
					],
					'condition' => [
						'navigation' => [ 'dots', 'both' ],
					],
				]
			);

			$this->end_controls_section();
		}

		protected function render() {
			$settings = $this->get_settings();

			if ( empty( $settings['slides'] ) ) {
				return;
			}

			$this->add_render_attribute( 'button', 'class', [ 'urus-button', 'urus-slide-button' ] );

			if ( ! empty( $settings['button_size'] ) ) {
				$this->add_render_attribute( 'button', 'class', 'urus-size-' . $settings['button_size'] );
			}

			$slides = [];
			$slide_count = 0;

			foreach ( $settings['slides'] as $slide ) {
				$slide_html = '';
				$btn_attributes = '';
				$slide_attributes = '';
				$slide_element = 'div';
				$btn_element = 'div';
				$slide_url = $slide['link']['url'];

				if ( ! empty( $slide_url ) ) {
					$this->add_render_attribute( 'slide_link' . $slide_count, 'href', $slide_url );

					if ( $slide['link']['is_external'] ) {
						$this->add_render_attribute( 'slide_link' . $slide_count, 'target', '_blank' );
					}

					if ( 'button' === $slide['link_click'] ) {
						$btn_element = 'a';
						$btn_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
					} else {
						$slide_element = 'a';
						$slide_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
					}
				}

				if ( 'yes' === $slide['background_overlay'] ) {
					$slide_html .= '<div class="urus-background-overlay"></div>';
				}

				$slide_html .= '<div class="urus-slide-content">';

				if ( $slide['heading'] ) {
					$slide_html .= '<div class="urus-slide-heading">' . $slide['heading'] . '</div>';
				}

				if ( $slide['description'] ) {
					$slide_html .= '<div class="urus-slide-description">' . $slide['description'] . '</div>';
				}

				if ( $slide['button_text'] ) {
					$slide_html .= '<' . $btn_element . ' ' . $btn_attributes . ' ' . $this->get_render_attribute_string( 'button' ) . '>' . $slide['button_text'] . '</' . $btn_element . '>';
				}

				$ken_class = '';

				if ( '' != $slide['background_ken_burns'] ) {
					$ken_class = ' urus-ken-' . $slide['zoom_direction'];
				}

				$slide_html .= '</div>';
				$slide_html = '<div class="slick-slide-bg' . $ken_class . '"></div><' . $slide_element . ' ' . $slide_attributes . ' class="slick-slide-inner">' . $slide_html . '</' . $slide_element . '>';
				$slides[] = '<div class="elementor-repeater-item-' . $slide['_id'] . ' slick-slide">' . $slide_html . '</div>';
				$slide_count++;
			}

			$is_rtl = is_rtl();
			$direction = $is_rtl ? 'rtl' : 'ltr';
			$show_dots = ( in_array( $settings['navigation'], [ 'dots', 'both' ] ) );
			$show_arrows = ( in_array( $settings['navigation'], [ 'arrows', 'both' ] ) );

			$slick_options = [
				'slidesToShow' => absint( 1 ),
				'autoplaySpeed' => absint( $settings['autoplay_speed'] ),
				'autoplay' => ( 'yes' === $settings['autoplay'] ),
				'infinite' => ( 'yes' === $settings['infinite'] ),
				'pauseOnHover' => ( 'yes' === $settings['pause_on_hover'] ),
				'speed' => absint( $settings['transition_speed'] ),
				'arrows' => $show_arrows,
				'dots' => $show_dots,
				'rtl' => $is_rtl,
				'prevArrow' => "<div class='prev slick-arrow'><i class='urus-icon urus-icon-prev'></i></div>",
				'nextArrow' => "<div class='next slick-arrow'><i class='urus-icon urus-icon-next'></i></div>",
			];
            if ($settings['custom_arrow'] == "yes"){

                if ($settings['arrow_left']){
	                $slick_options['prevArrow'] = "<div class='prev slick-arrow'><i class='urus-icon ".esc_attr($settings['arrow_left'])."'></i></div>";
                }
	            if ($settings['arrow_right']){
		            $slick_options['nextArrow'] = "<div class='next slick-arrow'><i class='urus-icon ".esc_attr($settings['arrow_right'])."'></i></div>";
	            }
            }
			if ( 'fade' === $settings['transition'] ) {
				$slick_options['fade'] = true;
			}

			$carousel_classes = [ 'urus-slides' ];

			if ( $show_arrows ) {
				$carousel_classes[] = 'slick-arrows-' . $settings['arrows_position'];
			}

			if ( $show_dots ) {
				$carousel_classes[] = 'slick-dots-' . $settings['dots_position'];
			}

			$this->add_render_attribute( 'slides', [
				'class' => $carousel_classes,
				'data-slider_options' => wp_json_encode( $slick_options ),
				'data-animation' => $settings['content_animation'],
			] );

			?>
            <div class="urus-slides-wrapper urus-slick-slider" dir="<?php echo esc_attr( $direction ); ?>">
                <div <?php echo $this->get_render_attribute_string( 'slides' ); ?>>
					<?php echo implode( '', $slides ); ?>
                </div>
            </div>
			<?php
		}

	}
}

