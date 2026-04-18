<?php
if ( ! class_exists( 'Urus_Elementor_icon' ) ) {
	/*
	 * Overwrite options Class \Elementor\Widget_Icon
	 */

	class Urus_Elementor_icon extends \Elementor\Widget_Icon {

		public function get_title() {
			return __( 'Urus: Icon', 'elementor' );
		}

		public function get_categories() {
			return [ 'urus' ];
		}

		/**
		 * Register icon widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function _register_controls() {
			$this->start_controls_section(
				'section_icon',
				[
					'label' => __( 'Icon', 'urus' ),
				]
			);

			$this->add_control(
				'icon',
				[
					'label'   => __( 'Icon', 'urus' ),
					'type'    => \Elementor\Controls_Manager::ICON,
					'default' => 'fa fa-star',
                    'options' => Urus_Elementor::urus_elementer_icon(),
				]
			);

			$this->add_control(
				'view',
				[
					'label'        => __( 'View', 'urus' ),
					'type'         => \Elementor\Controls_Manager::SELECT,
					'options'      => [
						'default' => __( 'Default', 'urus' ),
						'stacked' => __( 'Stacked', 'urus' ),
						'framed'  => __( 'Framed', 'urus' ),
					],
					'default'      => 'default',
					'prefix_class' => 'elementor-view-',
				]
			);

			$this->add_control(
				'shape',
				[
					'label'        => __( 'Shape', 'urus' ),
					'type'         => \Elementor\Controls_Manager::SELECT,
					'options'      => [
						'circle' => __( 'Circle', 'urus' ),
						'square' => __( 'Square', 'urus' ),
					],
					'default'      => 'circle',
					'condition'    => [
						'view!' => 'default',
					],
					'prefix_class' => 'elementor-shape-',
				]
			);

			$this->add_control(
				'link',
				[
					'label'       => __( 'Link', 'urus' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'dynamic'     => [
						'active' => true,
					],
					'placeholder' => __( 'https://your-link.com', 'urus' ),
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label'     => __( 'Alignment', 'urus' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => [
						'left'   => [
							'title' => __( 'Left', 'urus' ),
							'icon'  => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'urus' ),
							'icon'  => 'fa fa-align-center',
						],
						'right'  => [
							'title' => __( 'Right', 'urus' ),
							'icon'  => 'fa fa-align-right',
						],
					],
					'default'   => 'center',
					'selectors' => [
						'{{WRAPPER}} .elementor-icon-wrapper' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_icon',
				[
					'label' => __( 'Icon', 'urus' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->start_controls_tabs( 'icon_colors' );

			$this->start_controls_tab(
				'icon_colors_normal',
				[
					'label' => __( 'Normal', 'urus' ),
				]
			);

			$this->add_control(
				'primary_color',
				[
					'label'     => __( 'Primary Color', 'urus' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}}.elementor-view-stacked .elementor-icon'                                                    => 'background-color: {{VALUE}};',
						'{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					],
					'scheme'    => [
						'type'  => \Elementor\Scheme_Color::get_type(),
						'value' => \Elementor\Scheme_Color::COLOR_1,
					],
				]
			);

			$this->add_control(
				'secondary_color',
				[
					'label'     => __( 'Secondary Color', 'urus' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => '',
					'condition' => [
						'view!' => 'default',
					],
					'selectors' => [
						'{{WRAPPER}}.elementor-view-framed .elementor-icon'  => 'background-color: {{VALUE}};',
						'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'icon_colors_hover',
				[
					'label' => __( 'Hover', 'urus' ),
				]
			);

			$this->add_control(
				'hover_primary_color',
				[
					'label'     => __( 'Primary Color', 'urus' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}}.elementor-view-stacked .elementor-icon:hover'                                                          => 'background-color: {{VALUE}};',
						'{{WRAPPER}}.elementor-view-framed .elementor-icon:hover, {{WRAPPER}}.elementor-view-default .elementor-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'hover_secondary_color',
				[
					'label'     => __( 'Secondary Color', 'urus' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => '',
					'condition' => [
						'view!' => 'default',
					],
					'selectors' => [
						'{{WRAPPER}}.elementor-view-framed .elementor-icon:hover'  => 'background-color: {{VALUE}};',
						'{{WRAPPER}}.elementor-view-stacked .elementor-icon:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'hover_animation',
				[
					'label' => __( 'Hover Animation', 'urus' ),
					'type'  => \Elementor\Controls_Manager::HOVER_ANIMATION,
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'size',
				[
					'label'     => __( 'Size', 'urus' ),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 6,
							'max' => 300,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'icon_padding',
				[
					'label'     => __( 'Padding', 'urus' ),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
					],
					'range'     => [
						'em' => [
							'min' => 0,
							'max' => 5,
						],
					],
					'condition' => [
						'view!' => 'default',
					],
				]
			);

			$this->add_control(
				'rotate',
				[
					'label'     => __( 'Rotate', 'urus' ),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'default'   => [
						'size' => 0,
						'unit' => 'deg',
					],
					'selectors' => [
						'{{WRAPPER}} .elementor-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
					],
				]
			);

			$this->add_control(
				'border_width',
				[
					'label'     => __( 'Border Width', 'urus' ),
					'type'      => \Elementor\Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'view' => 'framed',
					],
				]
			);

			$this->add_control(
				'border_radius',
				[
					'label'      => __( 'Border Radius', 'urus' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						'view!' => 'default',
					],
				]
			);

			$this->end_controls_section();
		}

	}
}