<?php
if ( ! class_exists( 'Urus_Elementor_Special_Banner' ) ) {
	class Urus_Elementor_Special_Banner extends Urus_Elementor {
		public $name = 'special_banner';
		public $title = 'Special Banner';
		public $icon = 'eicon-image';

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
					'label' => esc_html__( 'Layout', 'urus' ),
				]
			);
			$this->add_control( 'template', [
				'label'   => esc_html__( 'Template', 'urus' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'single'   => esc_html__( 'Single', 'urus' ),
					'carousel' => esc_html__( 'Carousel', 'urus' ),
				],
				'default' => 'single',
			] );
			$arr_controls = [
				'layout'      => [
					'label'   => esc_html__( 'Layout', 'urus' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'default' => esc_html__( 'Default', 'urus' ),
						'style1'  => esc_html__( 'Layout 01', 'urus' ),
						'style2'  => esc_html__( 'Layout 02', 'urus' ),
						'style3'  => esc_html__( 'Layout 03', 'urus' ),
						'style4'  => esc_html__( 'Layout 04', 'urus' ),
						'style5'  => esc_html__( 'Layout 05', 'urus' ),
						'style6'  => esc_html__( 'Layout 06', 'urus' ),
						'style7'  => esc_html__( 'Layout 07', 'urus' ),
						'style8'  => esc_html__( 'Layout 08', 'urus' ),
						'style9'  => esc_html__( 'Layout 09', 'urus' ),
						'style10' => esc_html__( 'Layout 10', 'urus' ),
						'style11' => esc_html__( 'Layout 11', 'urus' ),
						'style12' => esc_html__( 'Layout 12', 'urus' ),
						'style13' => esc_html__( 'Layout 13', 'urus' ),
						'style14' => esc_html__( 'Layout 14', 'urus' ),
						'style15' => esc_html__( 'Layout 15', 'urus' ),
						'style16' => esc_html__( 'Layout 16', 'urus' ),
					],
					'default' => 'default',
				],
				'block_align' => [
					'label'   => esc_html__( 'Block Align', 'urus' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'left'   => esc_html__( 'Left', 'urus' ),
						'center' => esc_html__( 'Center', 'urus' ),
						'right'  => esc_html__( 'Right', 'urus' ),
					],
					'default' => 'left',
				],
				'image'       => [
					'label'   => esc_html__( 'Choose Image', 'urus' ),
					'type'    => \Elementor\Controls_Manager::MEDIA,
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					]
				],
				'title'       => [
					'label'       => esc_html__( 'Title', 'urus' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your title', 'urus' ),
					'condition'   => array(
						'layout!' => "style13"
					)
				],
				'subtitle'    => [
					'label'       => esc_html__( 'Sub Title', 'urus' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your text', 'urus' ),
					'condition'   => array(
						'layout!' => array(
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
						)
					)
				],
				'label_text'  => [
					'label'       => esc_html__( 'Label text', 'urus' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your text', 'urus' ),
					'condition'   => array(
						'layout' => array(
							'style4',
							'style15',
							'style16',
						)
					)
				],
				'link'        => [
					'label'       => esc_html__( 'link', 'urus' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your link', 'urus' ),
				],
				'button_text' => [
					'label'       => esc_html__( 'Button Text', 'urus' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Enter your text', 'urus' ),
				]

			];
			$repeater     = new \Elementor\Repeater();

			foreach ( $arr_controls as $key => $val ) {
			    $tmp = $val;
				$tmp['condition'] = isset($tmp['condition']) ? array_merge($tmp['condition'],['template' => 'single'] ) : array('template' => 'single');
				$this->add_control( $key, $tmp );
				$repeater->add_control( $key,  $val);
			}
			$this->add_control(
				'list',
				[
					'label'       => __( 'List banner', 'urus' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => 'Item',
					'condition'   => array(
						'template' => 'carousel'
					),
				]
			);
			$this->end_controls_section();

			$this->start_controls_section(
				'carousel_settings_section',
				[
					'label' => esc_html__( 'Carousel Settings', 'urus' ),
                    'condition' => array(
                            'template!' => 'single'
                    )
				]
			);
			$carousel_settings = Urus_Pluggable_Elementor::elementor_carousel( 'template', 'carousel' );

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
			$atts           = $this->get_settings_for_display();
			$css_class      = array( 'urus_special_banner' );
			$css_class[]    = $atts['layout'];
			$css_class[]    = $atts['block_align'];
			$text_link      = array( 'style1', 'style2', 'style4', 'style7', 'style10', 'style11' );
			$nav_position   = isset( $atts['owl_nav_position'] ) ? $atts['owl_nav_position'] : "";
			$owl_dots_style = isset( $atts['owl_dots_style'] ) ? $atts['owl_dots_style'] : "";
			?>
			<?php if ( $atts['template'] == "carousel" ): ?>
				<?php
				$owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
				?>
                <div class="urus-slide <?php echo esc_attr( $nav_position ) ?>">
                    <div class="urus-slide-wapper swiper-container urus-swiper custom-slide" <?php echo esc_attr( $owl_settings ); ?> <?php echo esc_attr( $owl_settings ); ?>
                         data-dots-style="<?php echo esc_attr( $owl_dots_style ) ?>">
                        <div class="swiper-wrapper">
							<?php foreach ( $atts['list'] as $key => $banner ):
								$css_class = array( 'urus_special_banner', $banner['layout'], $banner['block_align'] );
								?>
                                <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">

									<?php if ( $banner['image'] ): ?>
                                        <div class="image">
											<?php echo wp_get_attachment_image( $banner['image']['id'], 'full' ); ?>
											<?php if ( in_array( $banner['layout'], $text_link ) ): ?>
                                                <a class="link" href="<?php echo esc_url( $banner['link'] ); ?>"></a>
											<?php endif; ?>
											<?php if ( $banner['layout'] == 'style7' && $banner['label_text'] ): ?>
                                                <span class="text-label">
                                                    <?php echo esc_html( $banner['label_text'] ); ?>
                                                </span>
											<?php endif; ?>
                                        </div>
									<?php endif; ?>
                                    <div class="content-banner">
										<?php if ( $banner['subtitle'] && $banner['layout'] == "style1" ): ?>
                                            <span class="subtitle">
                                                <?php echo esc_html( $banner['subtitle'] ); ?>
                                            </span>
										<?php endif; ?>
										<?php if ( $banner['title'] ): ?>
                                            <h3 class="title">
												<?php echo esc_html( $banner['title'] ); ?>
                                            </h3>
										<?php endif; ?>
										<?php if ( $banner['subtitle'] && $banner['layout'] != "default" && $banner['layout'] != "style1" ): ?>
                                            <span class="subtitle">
                                                <?php echo esc_html( $banner['subtitle'] ); ?>
                                            </span>
										<?php endif; ?>
										<?php if ( $banner['label_text'] && ( $banner['layout'] == 'style15' || $banner['layout'] == 'style16' ) ): ?>
                                            <a href="<?php echo esc_url( $banner['link'] ); ?>" class="label_text">
												<?php echo esc_html( $banner['label_text'] ); ?>
                                            </a>
										<?php endif; ?>
										<?php if ( $banner['button_text'] ): ?>
                                            <a class="banner-button button"
                                               href="<?php echo esc_url( $banner['link'] ); ?>"><?php echo esc_html( $banner['button_text'] ); ?></a>
										<?php endif; ?>
										<?php if ( $banner['label_text'] && $banner['layout'] != 'style7' && $banner['layout'] != 'style15' && $banner['layout'] != 'style16' ): ?>

                                            <a href="<?php echo esc_url( $banner['link'] ); ?>" class="label_text">
												<?php echo esc_html( $banner['label_text'] ); ?>
                                            </a>
										<?php endif; ?>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
	                    <?php if ( $atts['owl_dots'] == "true" ): ?>
                            <div class="swiper-pagination"></div>
	                    <?php endif; ?>
                        <!-- If we need navigation buttons -->
                    </div>
	                <?php if ( $atts['owl_navigation'] == "true" ): ?>
                        <div class="slick-arrow next">
                            <span class="urus-icon-next"></span>
                        </div>
                        <div class="slick-arrow prev">
                            <span class="urus-icon-prev"></span>
                        </div>
	                <?php endif; ?>
                </div>
			<?php else: ?>
                <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">

					<?php if ( $atts['image'] ): ?>
                        <div class="image">
							<?php echo wp_get_attachment_image( $atts['image']['id'], 'full' ); ?>
							<?php if ( in_array( $atts['layout'], $text_link ) ): ?>
                                <a class="link" href="<?php echo esc_url( $atts['link'] ); ?>"></a>
							<?php endif; ?>
							<?php if ( $atts['layout'] == 'style7' && $atts['label_text'] ): ?>
                                <span class="text-label">
                            <?php echo esc_html( $atts['label_text'] ); ?>
                        </span>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
                    <div class="content-banner">
						<?php if ( $atts['subtitle'] && $atts['layout'] == "style1" ): ?>
                            <span class="subtitle">
                            <?php echo esc_html( $atts['subtitle'] ); ?>
                        </span>
						<?php endif; ?>
						<?php if ( $atts['title'] ): ?>
                            <h3 class="title">
								<?php echo esc_html( $atts['title'] ); ?>
                            </h3>
						<?php endif; ?>
						<?php if ( $atts['subtitle'] && $atts['layout'] != "default" && $atts['layout'] != "style1" ): ?>
                            <span class="subtitle">
                            <?php echo esc_html( $atts['subtitle'] ); ?>
                        </span>
						<?php endif; ?>
						<?php if ( $atts['label_text'] && ( $atts['layout'] == 'style15' || $atts['layout'] == 'style16' ) ): ?>
                            <a href="<?php echo esc_url( $atts['link'] ); ?>" class="label_text">
								<?php echo esc_html( $atts['label_text'] ); ?>
                            </a>
						<?php endif; ?>
						<?php if ( $atts['button_text'] ): ?>
                            <a class="banner-button button"
                               href="<?php echo esc_url( $atts['link'] ); ?>"><?php echo esc_html( $atts['button_text'] ); ?></a>
						<?php endif; ?>
						<?php if ( $atts['label_text'] && $atts['layout'] != 'style7' && $atts['layout'] != 'style15' && $atts['layout'] != 'style16' ): ?>

                            <a href="<?php echo esc_url( $atts['link'] ); ?>" class="label_text">
								<?php echo esc_html( $atts['label_text'] ); ?>
                            </a>
						<?php endif; ?>
                    </div>
                </div>
			<?php endif; ?>
			<?php
		}

		/**
		 * Render the widget output in the editor.
		 *
		 * Written as a Backbone JavaScript template and used to generate the live preview.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 */
		protected function _content_template() {
			?>
            <#
            var css_class    = 'urus_special_banner '+ settings.layout + ' '+ settings.block_align;
            var title ='';
            if( settings.title){
            title = settings.title;
            }
            #>
            <div class="{{{css_class}}}">
                <# if(settings.image.url){ #>
                <div class="image">
                    <img src="{{{ settings.image.url }}}" alt="{{{ title }}}">
                </div>
                <# } #>
                <div class="content-banner">
                    <# if( settings.title){#>
                    <h3 class="title">
                        <a class="link__hover" title="{{{ settings.title }}}" href="{{{ settings.link }}}">
                            {{{ settings.title }}}
                        </a>
                    </h3>
                    <# } #>
                    <# if( settings.subtitle){ #>
                    <h6 class="subtitle">{{{ settings.subtitle }}}</h6>
                    <# } #>
                    <# if( settings.layout =='layout1'){#>
                    <a class="banner-button" href="{{{ settings.link  }}}">{{{ settings.button_text }}}</a>
                    <# } #>

                    <# if( settings.label_text ) {#>
                    <span class="label_text"><span class="text">{{{ settings.label_text }}}</span></span>
                    <# } #>
                </div>
            </div>
			<?php
		}
	}
}