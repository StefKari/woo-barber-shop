<?php
if ( ! class_exists( 'Urus_Elementor_Category' ) ) {
	class Urus_Elementor_Category extends Urus_Elementor {
		public $name = 'category';
		public $title = 'Product Category';
		public $icon = 'eicon-product-categories';

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
			$all_category = get_terms( array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			) );
			$categories   = array();
			if ( ! empty( $all_category ) ) {
				foreach ( $all_category as $cat ) {
					$categories[ $cat->slug ] = $cat->name;
				}
			}

			$arr_control = array(
				'layout'         => [
					'label'   => esc_html__( 'Layout', 'urus' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'default'  => esc_html__( 'Default', 'urus' ),
						'layout1'  => esc_html__( 'Layout 01', 'urus' ),
						'layout2'  => esc_html__( 'Layout 02', 'urus' ),
						'layout3'  => esc_html__( 'Layout 03', 'urus' ),
						'layout4'  => esc_html__( 'Layout 04', 'urus' ),
						'layout5'  => esc_html__( 'Layout 05', 'urus' ),
						'layout6'  => esc_html__( 'Layout 06', 'urus' ),
						'layout7'  => esc_html__( 'Layout 07', 'urus' ),
						'layout8'  => esc_html__( 'Layout 08', 'urus' ),
						'layout9'  => esc_html__( 'Layout 09', 'urus' ),
						'layout10' => esc_html__( 'Layout 10', 'urus' ),
						'layout11' => esc_html__( 'Layout 11', 'urus' ),
					],
					'default' => 'default',
				],
				'taxonomy'       =>
					[
						'label'    => esc_html__( 'Product Category', 'urus' ),
						'type'     => \Elementor\Controls_Manager::SELECT,
						'multiple' => true,
						'options'  => $categories
					],
				'list_cate'      =>
					[
						'label'    => esc_html__( 'List Child Of Category', 'urus' ),
						'type'     => \Elementor\Controls_Manager::SELECT2,
						'multiple' => true,
						'options'  => $categories
					],
				'category_image' =>
					[
						'label'   => esc_html__( 'Image', 'urus' ),
						'type'    => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => \Elementor\Utils::get_placeholder_image_src(),
						]
					],
			);

			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'urus' ),
				]
			);
			$this->add_control(
				'template', [
					'label'   => esc_html__( 'Template', 'urus' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'single'   => esc_html__( 'Single', 'urus' ),
						'multiple' => esc_html__( 'Multiple with Carousel', 'urus' ),
					],
					'default' => 'single',
				]
			);

			foreach ( $arr_control as $key => $val ) {
				$val['condition'] = array( 'template' => 'single' );
				if ( $key == 'list_cate' ) {
					$val['condition'] = array( 'layout' => 'layout11', 'template' => 'single' );
				}
				$this->add_control( $key, $val );
			}

			$repeater = new \Elementor\Repeater();

			foreach ( $arr_control as $key => $val ) {
				if ( $key == 'list_cate' ) {
					$val['condition'] = array( 'layout' => 'layout11' );
				}
				$repeater->add_control( $key, $val );
			}

			$this->add_control(
				'list',
				[
					'label'       => __( 'Repeater List', 'urus' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => 'Item',
					'condition'   => array(
						'template' => 'multiple'
					),
				]
			);
			$this->end_controls_section();

			$this->start_controls_section(
				'carousel_settings_section',
				[
					'label' => esc_html__( 'Carousel Settings', 'urus' ),
				]
			);
			$carousel_settings = Urus_Pluggable_Elementor::elementor_carousel( 'template', 'multiple' );

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
			$css_class      = array( 'urus-category urus-elementer-category' );
			$css_class[]    = $atts['layout'];
			$nav_position    = isset( $atts['owl_nav_position'] ) ? $atts['owl_nav_position'] : "";
			$owl_dots_style = isset( $atts['owl_dots_style'] ) ? $atts['owl_dots_style'] : "";
			$category       = $atts['taxonomy'];
			$btn_shopnow    = array( 'layout1', 'layout2', 'layout4', 'layout9', 'layout11' );
			$cat_carousel   = $atts['list'];
			?>
			<?php if ( $atts['template'] == "multiple" ): ?>
				<?php
				$owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
				?>
            <div class="urus-slide <?php echo esc_attr($nav_position) ?>">
                <div class="urus-slide-wapper swiper-container urus-swiper custom-slide" <?php echo esc_attr( $owl_settings ); ?> <?php echo esc_attr( $owl_settings ); ?>
                     data-dots-style="<?php echo esc_attr( $owl_dots_style ) ?>">
                    <div class="swiper-wrapper">
						<?php foreach ( $cat_carousel as $key => $val ): ?>
							<?php
							$tmp_cat       = get_term_by( 'slug', $val['taxonomy'], 'product_cat' );
							$tmp_link      = get_term_link( $tmp_cat );
							$category_name = $tmp_cat->name;
							if ( isset( $val['title'] ) && $val['title'] != '' ) {
								$category_name = $val['title'];
							}
							$list_cat  = $val['list_cate'];
							$image     = $this->get_image_cat( $tmp_cat, $val['category_image'] );
							$css_class = array( $val['layout'], 'urus-category urus-elementer-category', "swiper-slide" );
							?>
                            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                                <div class="product-category-item category-item">
                                    <div class="inner">
                                        <div class="thumb">
                                            <a href="<?php echo esc_url( $tmp_link ); ?>">
                                                <figure>
													<?php echo Urus_Helper::escaped_html( $image['img'] ); ?>
                                                </figure>
                                            </a>
                                        </div>
                                        <div class="info">
											<?php if ( $val['layout'] == "layout4" ): ?>
                                            <div class="info-inner">
											<?php endif; ?>
                                                <h3 class="category-name">
                                                    <a class="link__hover"
                                                       href="<?php echo esc_url( $tmp_link ); ?>">
														<?php echo esc_html( $category_name ); ?>
                                                    </a>
                                                </h3>
												<?php if ( $val['layout'] == "default" ): ?>
                                                    <div class="count"><?php echo esc_html( $tmp_cat->count ) . ' '; ?><?php esc_html_e( 'products', 'urus' ); ?></div>
												<?php endif; ?>
												<?php if ( in_array( $val['layout'], $btn_shopnow ) ): ?>
                                                    <a href="<?php echo esc_url( $tmp_link ) ?>"
                                                       title="<?php echo esc_html( $tmp_cat->name ) ?>"
                                                       class="button-link"><?php esc_html_e( 'Shop now', 'urus' ); ?></a>
												<?php endif; ?>
											<?php if ( $val['layout'] == "layout4" ): ?>
                                            </div>
										    <?php endif; ?>
                                        </div>
										<?php if ( ! empty( $list_cat ) && $val['layout'] == "layout11" ): ?>
                                            <div class="list-cat-child">
                                                <ul class="inner-list">
													<?php $list_cat_chil = $this->list_cat_child( $list_cat ); ?>
													<?php foreach ( $list_cat_chil as $cat_key => $cat_val ): ?>
                                                        <li class="cat-item"><a
                                                                    href="<?php echo esc_url( $cat_val->term_link ); ?>"><?php echo esc_html( $cat_val->name ) ?></a>
                                                        </li>
													<?php endforeach; ?>
                                                </ul>
                                            </div>
										<?php endif; ?>
                                    </div>
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
				<?php if ( ! empty( $category ) ): ?>
					<?php
					$tmp_cat       = get_term_by( 'slug', $category, 'product_cat' );
					$tmp_link      = get_term_link( $tmp_cat );
					$category_name = $tmp_cat->name;
					if ( isset( $atts['title'] ) && $atts['title'] != '' ) {
						$category_name = $atts['title'];
					}
					$list_cat = $atts['list_cate'];
					$image    = $this->get_image_cat( $tmp_cat, $atts['category_image'] );
					?>
                    <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                        <div class="product-category-item category-item">
                            <div class="inner">
                                <div class="thumb">
                                    <a href="<?php echo esc_url( $tmp_link ); ?>">
                                        <figure>
											<?php echo Urus_Helper::escaped_html( $image['img'] ); ?>
                                        </figure>
                                    </a>
                                </div>
                                <div class="info">
									<?php if ( $atts['layout'] == "layout4" ): ?>
                                    <div class="info-inner">
										<?php endif; ?>
                                        <h3 class="category-name">
                                            <a class="link__hover" href="<?php echo esc_url( $tmp_link ); ?>">
												<?php echo esc_html( $category_name ); ?>
                                            </a>
                                        </h3>
										<?php if ( $atts['layout'] == "default" ): ?>
                                            <div class="count"><?php echo esc_html( $tmp_cat->count ) . ' '; ?><?php esc_html_e( 'products', 'urus' ); ?></div>
										<?php endif; ?>
										<?php if ( in_array( $atts['layout'], $btn_shopnow ) ): ?>
                                            <a href="<?php echo esc_url( $tmp_link ) ?>"
                                               title="<?php echo esc_html( $tmp_cat->name ) ?>"
                                               class="button-link"><?php esc_html_e( 'Shop now', 'urus' ); ?></a>
										<?php endif; ?>
										<?php if ( $atts['layout'] == "layout4" ): ?>
                                    </div>
								<?php endif; ?>
                                </div>
								<?php if ( ! empty( $list_cat ) && $atts['layout'] == "layout11" ): ?>
                                    <div class="list-cat-child">
                                        <ul class="inner-list">
											<?php
											$list_cat_chil = $this->list_cat_child( $list_cat );
											?>
											<?php foreach ( $list_cat_chil as $key => $val ): ?>
                                                <li class="cat-item"><a
                                                            href="<?php echo esc_url( $val->term_link ); ?>"><?php echo esc_html( $val->name ) ?></a>
                                                </li>
											<?php endforeach; ?>
                                        </ul>

                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>
			<?php endif; ?>

			<?php
		}

		public function list_cat_child( $list_cat = '' ) {
			$list_cat_chil = array();
			if ( ! empty( $list_cat ) ) {
				if ( is_array( $list_cat ) ) {
					foreach ( $list_cat as $key => $val ) {
						$cat_tem            = get_term_by( 'slug', $val, 'product_cat' );
						$term_link          = get_term_link( $cat_tem->slug, 'product_cat' );
						$cat_tem->term_link = $term_link;
						array_push( $list_cat_chil, $cat_tem );
					}
				} else {
					$cat_tem            = get_term_by( 'slug', $list_cat, 'product_cat' );
					$term_link          = get_term_link( $cat_tem->slug, 'product_cat' );
					$cat_tem->term_link = $term_link;
					array_push( $list_cat_chil, $cat_tem );
				}
			}

			return $list_cat_chil;
		}

		public function get_image_cat( $term_category, $image_feild ) {
			if ( isset( $image_feild['id'] ) && $image_feild['id'] > 0 ) {
				$image = Urus_Helper::resize_image( $image_feild['id'], false, false, true, true );
			} else {
				$thumbnail_id = get_term_meta( $term_category->term_id, 'thumbnail_id', true );
				$image        = Urus_Helper::resize_image( $thumbnail_id, false, false, true, true );
			}

			return $image;
		}
	}
}