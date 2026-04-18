<?php
if( !class_exists('Urus_Elementor_Testimonial')){
	class Urus_Elementor_Testimonial extends Urus_Elementor{
		public $name ='urus-testimonial';
		public $title ='Testimonial';
		public $icon ='eicon-testimonial-carousel';
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

			$repeater = new \Elementor\Repeater();

			$repeater->add_control( 'layout',[
					'label'   => esc_html__( 'Layout', 'urus' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
							'default'  => esc_html__( 'Default', 'urus' ),
							'layout1'  => esc_html__( 'Layout 01', 'urus' ),
							'layout2'  => esc_html__( 'Layout 02', 'urus' ),
					],
					'default' => 'default',
				]
			);
			$repeater->add_control( 'image', [
				'label'   => esc_html__( 'Image', 'urus' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				]
			]);
			$repeater->add_control( 'name', [
				'label'   => esc_html__( 'Name', 'urus' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
			] );
			$repeater->add_control( 'position', [
				'label'   => esc_html__( 'Position', 'urus' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
			] );
			$repeater->add_control( 'title', [
				'label'   => esc_html__( 'Title', 'urus' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'condition' => array(
					'layout' => 'layout2'
				)
			] );
			$repeater->add_control( 'text', [
				'label'   => esc_html__( 'Text', 'urus' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
			] );
			$this->add_control(
				'list',
				[
					'label'       => __( 'List Item', 'urus' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => 'Item',
				]
			);

			$this->end_controls_section();
			/// Carousel Layout
			$this->start_controls_section(
				'carousel_settings_section',
				[
					'label' => esc_html__( 'Carousel Settings', 'urus' ),
				]
			);
			$this->add_control(
				'liststyle',
				[
					'label' => esc_html__( 'View', 'urus' ),
					'type' => \Elementor\Controls_Manager::HIDDEN,
					'default' => 'owl',
				]
			);

			$carousel_settings = Urus_Pluggable_Elementor::elementor_carousel('liststyle','owl');

			foreach ( $carousel_settings as $key => $value){
				$this->add_control($key,$value);
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
			$atts = $this->get_settings_for_display();
			$css_class    = array( 'urus-testimonials' );
			$nav_position = isset($atts['owl_nav_position']) ? $atts['owl_nav_position'] : "";
			$owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
			$owl_dots_style =  isset($atts['owl_dots_style']) ? $atts['owl_dots_style'] : "";
			$testimonials = $atts['list'];
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
												<?php echo wp_get_attachment_image($testimonial['image']['id'],'full');?>
											</div>
										<?php endif;?>
										<div class="content">
											<div class="text"><?php echo esc_html($testimonial['text']);?></div>
											<?php if( $testimonial['image'] && $testimonial['layout'] != "default"):?>
												<div class="image">
													<?php echo wp_get_attachment_image($testimonial['image']['id'],'full');?>
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
		}

	}
}