<?php
if ( ! class_exists( 'Urus_Elementor_Custom_Menu' ) ) {
	class Urus_Elementor_Custom_Menu extends Urus_Elementor {
		public $name = 'custom_menu';
		public $title = 'Custom Menu';
		public $icon = 'eicon-icon-box';

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
			$all_menu = array();
			$menus  = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
			if ( $menus && count( $menus ) > 0 ) {
				foreach ( $menus as $m ) {
					$all_menu[$m->slug] = $m->name;
				}
			}

			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'urus' ),
				]
			);
			$this->add_control(
				'title',
				[
					'label'       => esc_html__( 'Title', 'urus' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'label_block' => true
				]
			);
			$this->add_control(
				'subtitle',
				[
					'label'       => esc_html__( 'Subtitle', 'urus' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'label_block' => true
				]
			);
			$this->add_control(
				'layout',
				[
					'label'       => esc_html__( 'Layout', 'urus' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => [
						'vertical'       => esc_html__( 'Vertical', 'urus' ),
						'inline'         => esc_html__( 'Inline', 'urus' ),
						'inline-special' => esc_html__( 'Inline Special', 'urus' ),
					],
					'default'     => 'vertical',
					'label_block' => true
				]
			);

			$this->add_control(
				'nav_menu',
				[
					'label'       => esc_html__( 'Menu', 'urus' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $all_menu,
					'default'     => 'vertical',
					'label_block' => true,
					'description' => esc_html__( 'Select menu to display.', 'urus' ),
				]
			);
			$this->add_responsive_control(
				'align',
				[
					'label' => __( 'Alignment', 'elementor' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'elementor' ),
							'icon' => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'elementor' ),
							'icon' => 'fa fa-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'elementor' ),
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
			$atts        = $this->get_settings_for_display();
			$css_class    = array( 'urus-custom-menu' );
			$css_class[]  = $atts['layout'];
			$menu  = get_term_by( 'slug', $atts['nav_menu'], 'nav_menu' );
			?>
			<div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
				<?php if( $atts['title']):?>
					<h3 class="title"><a href="#"><?php echo esc_html($atts['title']);?></a></h3>
				<?php endif;?>
				<?php
				if ( !is_wp_error( $menu ) && is_object( $menu ) && !empty( $menu ) ) {
					$nav_menu = ! empty( $menu->term_id) ? wp_get_nav_menu_object( $menu->term_id) : false;
					if(!$nav_menu){
						return;
					}
					$nav_menu_args = array(
						'fallback_cb' => '',
						'menu'        => $nav_menu
					);
					wp_nav_menu( $nav_menu_args);

				} else {
					echo esc_html__( 'No content.', 'urus' );
				}

				?>
			</div>
			<?php
		}
	}
}