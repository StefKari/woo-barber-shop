<?php
if( !class_exists('Urus_Elementor_title')){
	class Urus_Elementor_Title extends Urus_Elementor{
		public $name ='title';
		public $title ='Title';
		public $icon ='eicon-type-tool';
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
			$this->add_control(
				'title',
				[
					'label' => esc_html__( 'Title', 'urus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your title', 'urus' ),
				]
			);
			$this->add_control(
				'title-style',
				[
					'label' => esc_html__( 'Title style', 'urus' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'default' => esc_html__( 'Default', 'urus' ),
						'title-dash-style1' => esc_html__( 'Layout 01', 'urus' ),
						'title-dash-style2' => esc_html__( 'Layout 02', 'urus' ),
						'layout3' => esc_html__( 'Layout 03', 'urus' ),
						'layout4' => esc_html__( 'Layout 04', 'urus' ),
						'layout5' => esc_html__( 'Layout 05', 'urus' ),
						'layout6' => esc_html__( 'Layout 06', 'urus' ),
						'layout7' => esc_html__( 'Layout 07', 'urus' ),
					],
					'default' => 'default',
				]
			);
			$this->add_control(
				'subtitle',
				[
					'label' => esc_html__( 'Sub Title', 'urus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your text', 'urus' ),
                    'condition' => array(
                            'title-style!' => array('layout6', 'layout7'),
                    )
				]
			);
			$this->add_control(
				'title_color',
				[
					'label' => __( 'Title Color', 'elementor' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'scheme' => [
						'type' => \Elementor\Scheme_Color::get_type(),
						'value' => \Elementor\Scheme_Color::COLOR_1,
					],
					'selectors' => [
						// Stronger selector to avoid section style from overwriting
						'{{WRAPPER}} .title' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'typography',
					'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_3,
					'selector' => '{{WRAPPER}} .title',
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
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

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
			$atts = $this->get_settings_for_display();
			$css_class    = array( 'urus-section-title' );
			$css_class[]  = isset( $atts['title-style'] ) ? $atts['title-style'] : '';
			$title = isset($atts['title']) ? $atts['title'] : "";
			?>
			<div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
				<?php if( $title):?>
					<h3 class="title"><?php echo esc_html($title);?></h3>
				<?php endif;?>
				<?php if($atts['subtitle']):?>
					<div class="subtitle"><?php echo esc_html($atts['subtitle']);?></div>
				<?php endif;?>
			</div>
			<?php
		}

	}
}