<?php
	if ( ! class_exists( 'Urus_Elementor_Button' ) ) {
		/*
		 * Overwrite options Class \Elementor\Widget_Button
		 */
		class Urus_Elementor_Button extends \Elementor\Widget_Button {
			public function get_categories() {
				return [ 'urus' ];
			}

			public function get_title() {
				return __( 'Urus: Button', 'elementor' );
			}

			protected function _register_controls() {
				$this->start_controls_section(
					'section_button',
					[
						'label' => __( 'Button', 'urus' ),
					]
				);

				$this->add_control(
					'button_type',
					[
						'label' => __( 'Type', 'urus' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'default' => '',
						'options' => [
							'' => __( 'Default', 'urus' ),
							'info' => __( 'Info', 'urus' ),
							'success' => __( 'Success', 'urus' ),
							'warning' => __( 'Warning', 'urus' ),
							'danger' => __( 'Danger', 'urus' ),
						],
						'prefix_class' => 'elementor-button-',
					]
				);

				$this->add_control(
					'text',
					[
						'label' => __( 'Text', 'urus' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'dynamic' => [
							'active' => true,
						],
						'default' => __( 'Click here', 'urus' ),
						'placeholder' => __( 'Click here', 'urus' ),
					]
				);

				$this->add_control(
					'link',
					[
						'label' => __( 'Link', 'urus' ),
						'type' => \Elementor\Controls_Manager::URL,
						'dynamic' => [
							'active' => true,
						],
						'placeholder' => __( 'https://your-link.com', 'urus' ),
						'default' => [
							'url' => '#',
						],
					]
				);

				$this->add_responsive_control(
					'align',
					[
						'label' => __( 'Alignment', 'urus' ),
						'type' => \Elementor\Controls_Manager::CHOOSE,
						'options' => [
							'left'    => [
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
							'justify' => [
								'title' => __( 'Justified', 'urus' ),
								'icon' => 'fa fa-align-justify',
							],
						],
						'prefix_class' => 'elementor%s-align-',
						'default' => '',
					]
				);

				$this->add_control(
					'size',
					[
						'label' => __( 'Size', 'urus' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'default' => 'sm',
						'options' => self::get_button_sizes(),
						'style_transfer' => true,
					]
				);

				$this->add_control(
					'icon',
					[
						'label' => __( 'Icon', 'urus' ),
						'type' => \Elementor\Controls_Manager::ICON,
						'label_block' => true,
						'default' => '',
						'options' => Urus_Elementor::urus_elementer_icon()
					]
				);

				$this->add_control(
					'icon_align',
					[
						'label' => __( 'Icon Position', 'urus' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'default' => 'left',
						'options' => [
							'left' => __( 'Before', 'urus' ),
							'right' => __( 'After', 'urus' ),
						],
						'condition' => [
							'icon!' => '',
						],
					]
				);

				$this->add_control(
					'icon_indent',
					[
						'label' => __( 'Icon Spacing', 'urus' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'max' => 50,
							],
						],
						'condition' => [
							'icon!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'view',
					[
						'label' => __( 'View', 'urus' ),
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'default' => 'traditional',
					]
				);

				$this->add_control(
					'button_css_id',
					[
						'label' => __( 'Button ID', 'urus' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'dynamic' => [
							'active' => true,
						],
						'default' => '',
						'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'urus' ),
						'label_block' => false,
						'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'urus' ),
						'separator' => 'before',

					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_style',
					[
						'label' => __( 'Button', 'urus' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'typography',
						'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_4,
						'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button',
					]
				);

				$this->start_controls_tabs( 'tabs_button_style' );

				$this->start_controls_tab(
					'tab_button_normal',
					[
						'label' => __( 'Normal', 'urus' ),
					]
				);

				$this->add_control(
					'button_text_color',
					[
						'label' => __( 'Text Color', 'urus' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'background_color',
					[
						'label' => __( 'Background Color', 'urus' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'scheme' => [
							'type' => \Elementor\Scheme_Color::get_type(),
							'value' => \Elementor\Scheme_Color::COLOR_4,
						],
						'selectors' => [
							'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_button_hover',
					[
						'label' => __( 'Hover', 'urus' ),
					]
				);

				$this->add_control(
					'hover_color',
					[
						'label' => __( 'Text Color', 'urus' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_background_hover_color',
					[
						'label' => __( 'Background Color', 'urus' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_hover_border_color',
					[
						'label' => __( 'Border Color', 'urus' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'condition' => [
							'border_border!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'hover_animation',
					[
						'label' => __( 'Hover Animation', 'urus' ),
						'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' => 'border',
						'selector' => '{{WRAPPER}} .elementor-button',
						'separator' => 'before',
					]
				);

				$this->add_control(
					'border_radius',
					[
						'label' => __( 'Border Radius', 'urus' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors' => [
							'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'button_box_shadow',
						'selector' => '{{WRAPPER}} .elementor-button',
					]
				);

				$this->add_responsive_control(
					'text_padding',
					[
						'label' => __( 'Padding', 'urus' ),
						'type' => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', '%' ],
						'selectors' => [
							'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'separator' => 'before',
					]
				);

				$this->end_controls_section();
			}
		}
	}
?>