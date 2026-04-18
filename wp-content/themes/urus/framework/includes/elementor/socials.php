<?php
if( !class_exists('Urus_Elementor_Socials')){
	class Urus_Elementor_Socials extends Urus_Elementor {
		public $name ='urus-socials';
		public $title ='Social icon';
		public $icon ='eicon-social-icons';
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
			$socials = array();
			$all_socials = Urus_Helper::get_all_social();
			if( $all_socials ){
				foreach ($all_socials as $key =>  $social)
					$socials[$key] = $social['name'];
			}

			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'urus' ),
				]
			);
			$this->add_control(
				'layout',
				[
					'label' => esc_html__( 'Layout', 'urus' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'default' => esc_html__( 'Default', 'urus' ),
						'layout1' => esc_html__( 'Layout 01', 'urus' ),
						'layout2' => esc_html__( 'Layout 02', 'urus' ),
						'layout3' => esc_html__( 'Layout 03', 'urus' ),
						'layout4' => esc_html__( 'Layout 04', 'urus' ),
					],
					'default' => 'default',
					'label_block'=> true
				]
			);
			$this->add_control(
				'title',
				[
					'label' => esc_html__( 'Title', 'urus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your title', 'urus' ),
					'label_block'=> true
				]
			);
			$this->add_control(
				'subtitle',
				[
					'label' => esc_html__( 'Subtitle', 'urus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your title', 'urus' ),
					'label_block'=> true

				]
			);
			$this->add_control(
				'use_socials',
				[
					'label' => esc_html__( 'Layout', 'urus' ),
					'type' => \Elementor\Controls_Manager::SELECT2,
					'options' => $socials,
					'multiple' => true,
					'label_block'=> true
				]
			);
			$this->add_responsive_control(
				'align',
				[
					'label' => __( 'Alignment', 'urus' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
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
			$css_class    = array( 'urus-socials' );
			$css_class[]  = $atts['layout'];
			$socials = $atts['use_socials'];
			?>
			<div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
				<?php if($atts['title']):?>
					<h3 class="title"><?php echo esc_html($atts['title']);?></h3>
				<?php endif;?>
				<?php if($atts['subtitle']):?>
					<div class="subtitle"><?php echo esc_html($atts['subtitle']);?></div>
				<?php endif;?>
				<?php if(!empty($socials)):?>
					<div class="socials clearfix">
						<?php if (is_array($socials)): ?>
							<?php foreach ($socials as $social):?>
								<?php Urus_Helper::display_social($social);?>
							<?php endforeach;?>
						<?php else: ?>
							<?php Urus_Helper::display_social($socials);?>
						<?php endif ?>
					</div>
				<?php endif;?>
			</div>
			<?php
		}
	}
}