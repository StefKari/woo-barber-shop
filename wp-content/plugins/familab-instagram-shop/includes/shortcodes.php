<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( !class_exists('Tis_Shortcode')){
    class Tis_Shortcode{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;
        /**
         * Initialize pluggable functions.
         *
         * @return  void
         */
        public static function initialize() {
        	// Do nothing if functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            add_shortcode( 'tis',  array( __CLASS__, 'render_shortcode'));
            /**
	         * @Filter: tis_shortcode_output ($atts)
	         * @Filter: tis_shortcode_att
	         */
            //Add popup modal
           	add_action( 'tis_modal', array( __CLASS__, 'tis_modal'), 10, 2 );

            //Add image loop
         	add_action( 'tis_image_loop', array( __CLASS__, 'tis_image_loop'), 10, 4 );
            self::$initialized = true;
        }
        public static function tis_image_loop( $post_meta, $image_id , $image_data , $index ){
        	$html = self::image_loop($post_meta, $image_id, $image_data , $index);
        	echo apply_filters( 'tis_image_loop_html', $html, $post_meta, $image_id, $image_data);
        }
        public static function tis_modal( $id, $pin_data_array ){
        	$html = self::modal_html($id, $pin_data_array);
        	echo apply_filters( 'tis_modal_html', $html, $id, $pin_data_array );
        }
        /**
		 * Familab Instagram Shop Plugin
		 *
		 * Shortcode Render
		 *
		 * These shortcode template can be overridden by copying it to yourtheme/familab-instagram-shop/templates/[template-name].php.
		 *
		 */
        public static function render_shortcode( $atts ){
            $atts = apply_filters('tis_shortcode_atts',$atts);
            extract(
				shortcode_atts(
					array(
						'id'       => 0,
						'tis_xl_cols'  => 4,
						'tis_lg_cols'  => 4,
						'tis_md_cols'  => 3,
						'tis_sm_cols'  => 2,
						'tis_xs_cols'  => 2,
						'css'      => '',
						'tis_style'=> 'grid',
						'tis_type'=> 'pin',
						'image_resolution' => 'large',
						'carousel_space' => 0
					), $atts
				)
			);
			$responsive_class = 'col-'. $tis_xs_cols .' '
								.'col-sm-'. $tis_sm_cols .' '
								.'col-md-'. $tis_md_cols .' '
								.'col-lg-'. $tis_lg_cols .' '
								.'col-xl-'. $tis_xl_cols;
			$single_img_html = '';
			$html            = '';
			$json_pin_data  = get_post_meta( $id, 'tis_pin_data', true );
			$json_pin_data  = get_post_meta( $id, 'tis_pin_data', true );
    		$tis_shortcode_type = (!empty($tis_type)) ? $tis_type : 'pin';
			$pin_data_array = json_decode( $json_pin_data );
			$has_image = false;
			if ( trim( $json_pin_data ) != '' ) {
				$has_image = true;
			}else{
				$has_image = false;
			}
			$post_meta = array(
				'id' => $id,
				'tis_type' => $tis_shortcode_type,
				'tis_style' => $tis_style,
				'responsive_class' => $responsive_class,
				'image_resolution' => $image_resolution
			);
			ob_start();
			if($has_image){
				$current_theme_dir = get_template_directory();
				$theme_file = $current_theme_dir .'/familab-instagram-shop/templates/'.$tis_style.'.php';
				$plugin_file = TIS_DIR_PATH . 'templates/'.$tis_style.'.php';
				do_action('tis_before_shortcode');
				if (file_exists($theme_file)) {
					// Theme file found, use the theme file template instead
					 require_once $theme_file;
				}else{
					// Theme file not found, use the plugin template file
					if ( file_exists($plugin_file) ) {
						require_once $plugin_file;
					}else{
						?>
						 <div class="tis-error-field no-image p-3 mb-2 bg-warning text-dark" style="<?php echo esc_attr($warning_class); ?>">
					    	<?php esc_html_e( 'Failed to open template file, please check again', 'familab-instagram-shop' ); ?>
					    	<br>
					    	<?php echo esc_html( $plugin_file ); ?>
					    </div>
						<?php
					}
				}
				do_action('tis_after_shortcode');
			}else{
				?>
			    <div class="tis-error-field no-image p-3 mb-2 bg-warning text-dark" style="<?php echo esc_attr($warning_class); ?>">
			    	<?php esc_html_e( 'No image chosen', 'familab-instagram-shop' ); ?>
			    </div>
				<?php
			}
			$html = ob_get_clean();
            return apply_filters('tis_shortcode_output', $html , $atts);
        }
        /**
		 * Get shortcode string
		 * @return: string
		 */
        public static function get_shortcode_string($post_id){
        	$tis_style = get_post_meta( $post_id, 'tis_style', true );
        	$get_tis_style = (!empty($tis_style)) ? $tis_style : 'grid';
        	$tis_type = get_post_meta( $post_id, 'tis_type', true );
        	$get_tis_type = (!empty($tis_type)) ? $tis_type : 'pin';
			$carousel_space = get_post_meta( $post_id, 'carousel_space', true );
			$get_carousel_space = (!empty($carousel_space)) ? $carousel_space : 0;
			$image_resolution = get_post_meta( $post_id, 'image_resolution', true );
			$get_image_resolution = (!empty($image_resolution)) ? $image_resolution : 'large';
			$carousel_param = '';
			if ($get_tis_style == 'carousel') {
				$carousel_param = ' carousel_space='.$get_carousel_space;
			}
			$shortcode             = htmlentities2( '[tis id=' . $post_id . ' image_resolution='.$get_image_resolution.' tis_type='. $get_tis_type .' tis_style=' .$get_tis_style. ' '. $carousel_param . ']');
			$use_custom_responsive = get_post_meta( $post_id, 'tis_use_custom_responsive', true );
			$items_on_screen_meta_keys_default_vals = array(
				'tis_xl_cols'  => 4,
				'tis_lg_cols'  => 3,
				'tis_md_cols'  => 2,
				'tis_sm_cols'  => 2,
				'tis_xs_cols' => 1
			);
			if ( trim( $use_custom_responsive ) == 'on' ) {
				foreach ( $items_on_screen_meta_keys_default_vals as $items_on_screen_meta_key => $default_items_on_screen ) {
					${$items_on_screen_meta_key} = get_post_meta( $post_id, $items_on_screen_meta_key, true );
					if ( trim( ${$items_on_screen_meta_key} ) == '' ) {
						${$items_on_screen_meta_key} = $default_items_on_screen;
					}
				}
				$shortcode = '[tis id=' . $post_id . ' tis_xl_cols=' . $tis_xl_cols . ' tis_lg_cols=' . $tis_lg_cols . ' tis_md_cols=' . $tis_md_cols . ' tis_sm_cols=' . $tis_sm_cols . ' tis_xs_cols=' . $tis_xs_cols . ' image_resolution='.$get_image_resolution.' tis_type='. $get_tis_type . ' tis_style=' .$get_tis_style.' '. $carousel_param . ']';
				$shortcode = htmlentities2( $shortcode );
			}
			return $shortcode;
        }
        /**
		 * Instagram Image loop
		 *
		 */
        public static function image_loop($post_meta, $image_id, $image_data = array(), $index = 0){
            if (!$image_id){
                return;
            }
        	$html ='';
        	extract( $post_meta );
        	ob_start();
        	do_action( 'tis_before_image_loop' );
        	if ( ! empty($image_data) ) {
        		if ($tis_style != 'grid') {
        			$responsive_class = '';
        		}
                $image_data = (object) $image_data;
        		if (isset($image_data->products)){
                    $products_arr = $image_data->products;
                } else {
                    $products_arr = array();
                }
        		$product_string = json_encode($products_arr);
	        	$tis_resolution = '';
				switch ($image_resolution) {
					case 'thumb':
						$tis_resolution = $image_data->thumb;
						break;
					case 'medium':
						$tis_resolution = $image_data->low_res;
						break;
					case 'large':
						$tis_resolution = $image_data->src;
						break;
					default:
						$tis_resolution = $image_data->src;
						break;
				}
        		?>
        		<div class="<?php echo esc_attr($responsive_class);  ?> single-image  shortcode-type-<?php echo esc_attr( $tis_type ); ?>"
        			data-id="<?php echo esc_attr($image_id);  ?>"
		        	data-link="<?php echo esc_attr($image_data->link);  ?>"
		        	data-src="<?php echo esc_attr($image_data->src); ?>"
		        	data-width="<?php echo esc_attr($image_data->width); ?>"
		        	data-height="<?php echo esc_attr($image_data->height); ?>"
		        	data-caption="<?php echo esc_attr($image_data->caption); ?>"
		        	data-products="<?php echo esc_attr($product_string); ?>"
		        	data-index="<?php echo esc_attr($index); ?>"
        			>
		        	<div class="single-image-holder">
						<img class="lazyload"
							width="<?php echo esc_attr($image_data->width) ?>"
							height="<?php echo esc_attr($image_data->height); ?>"
							src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
							data-src="<?php echo esc_url($tis_resolution); ?>"
							data-sizes="auto"
                            alt=""
							>
						<?php
						if ($tis_type == 'pin') {
							if (!empty($products_arr)) {
								?>
								<div class="tis-product-pins">
								<?php
								foreach ( $products_arr as $product_info ) {
									/**
									* Hooked tis_product_loop_detailed ($product_info)
									* @param $product_info
									* @return $html
									*/
									self::product_loop_detailed($product_info);
								}
								?>
								</div>
								<?php
							}
						}else{
							?>
			        		<div class="tis-text-container">
			        			<div class="text-content">
			        				<?php
			        				$output_text = '';
			        				ob_start();
			        				?>
                                    <a class="instagram-shop-modal-popup" href="#familab-instagram-shop-<?php echo esc_attr( $id ); ?>">
                                        <span class="icon-instagram"></span>
                                        <?php echo (!empty($products_arr)) ? esc_html__('Shop the look', 'familab-instagram-shop') : ''; ?>
                                    </a>
				        			<?php
				        			$output_text = ob_get_clean();
				        			echo apply_filters( 'tis_image_loop_text', $output_text, $products_arr );
				        			?>
			        			</div>
			        		</div>
		        			<?php
						}
		        		?>
					</div>
				</div>
				<?php
			}
			do_action( 'tis_after_image_loop' );
			$html = ob_get_clean();
        	return $html;
        }
        /**
		 * Instagram Modal Type
		 *
		*/
        public static function modal_html($id, $pin_data_array){
        	$html = '';
        	ob_start();
        	?>
        	<div class="tis-modal-popup mfp-hide" id="familab-instagram-shop-<?php echo esc_attr( $id ); ?>">
				<div class="tis-modal-wrapper">
					<div class="tis-modal-content">
						<!-- Swiper -->
						<div class="swiper-container post-id-<?php echo esc_attr($id); ?>" data-post-id="<?php echo esc_attr($id); ?>">
						    <div class="swiper-wrapper">
						    	<?php foreach ( $pin_data_array as $image_id=>$image_data ) {
					    			$i = 0;
									$product_string = json_encode($image_data->products);
									$products_arr = $image_data->products;
								?>
									<div class="swiper-slide">
										<div class="tis-modal-item">
											<div class="tis-modal-product-pins tis-product-pins modal-left">
												<div class="product-pins-wrapper">
													<img src="data:image/gif;base64,R0lGODlhAQABAPAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" data-src="<?php echo esc_attr($image_data->src); ?>" alt="" class="lazyload tis-modal-image">
													<?php
														if (!empty($products_arr)) {
															foreach ( $products_arr as $product_info ) {
																$tis_product_id = $product_info[0];
																$post_info = get_post_status( $tis_product_id );
																$tis_product = wc_get_product( $tis_product_id );
																if ( ! empty($tis_product) ):
												                    $tis_product_title = $tis_product->get_title();
																	?>
																	<a href="javascript:void(0);" class="single-pin clearfix <?php echo esc_attr($product_info[4]); ?>"
																		data-product-id="<?php echo esc_attr($tis_product_id); ?>"
																		style="	top:<?php echo esc_attr($product_info[2]); ?>;
																				left:<?php echo esc_attr($product_info[1]); ?>;">
																		<div class="pin-text">
																			<?php echo esc_html($product_info[3]); ?>
																		</div>
																		<div class="single-pin-product popup-right"><?php echo esc_html($tis_product_title); ?></div>
																	</a>
																	<?php
																endif;
															}
														}
													?>
												</div>
											</div>
											<div class="modal-right">
                                                <div class="modal-right-content">
                                                    <div class="products-content">
                                                        <?php
                                                            if (!empty($products_arr)) {
                                                                foreach ( $products_arr as $product_info ) {
                                                                    /**
                                                                     * @Filter: tis_modal_pin_loop ($product_info)
                                                                     */
                                                                    self::modal_pin_loop($product_info);
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                    <?php if($image_data->caption):?>
                                                        <div class="img-description-content">
                                                            <?php
                                                            if ( base64_encode(base64_decode($image_data->caption, true)) === $image_data->caption){
															    echo wpautop( base64_decode($image_data->caption) );
															} else {
															    echo $image_data->caption;
															}
                                                            ?>
                                                        </div>
                                                    <?php endif;?>
                                                    <?php if($image_data->link):?>
                                                        <div class="img-url-content">
                                                            <a href="<?php echo esc_url($image_data->link); ?>" target="_blank">
                                                                <i class=" fa fa-instagram"></i>
                                                                <?php
                                                                    esc_html_e('View on Instagram', 'familab-instagram-shop');
                                                                ?>
                                                            </a>
                                                        </div>
                                                    <?php endif;?>
                                                </div>
											</div>
										</div>
									</div>
						     	<?php $i++; } ?>
						    </div>
						</div>
					</div>
				</div>
                <!-- Add Arrows -->
                <div class="tis-modal-nav-group">
                    <div class="tis-modal-nav prev tis-modal-prev"></div>
                    <div class="tis-modal-nav next tis-modal-next"></div>
                </div>
			</div>
        	<?php
        	$html = ob_get_clean();
        	return $html;
        }
        /**
		 * Loop for product pin detailed
		 *
		*/
        public static function product_loop_detailed($product_info){
        	$tis_product_id = $product_info[0];
        	$product = wc_get_product( $tis_product_id );
        	ob_start();
			if (!empty($product)) {
				$tis_attachment_id = get_post_thumbnail_id($tis_product_id);
                $tis_product_img = Tis_Functions::get_product_image($tis_attachment_id, 'thumbnail');
                $tis_product_rating_html = wc_get_rating_html(  $product->get_average_rating() , $product->get_rating_count() );
                $tis_product_price_html = $product->get_price_html();
                $tis_product_url = get_permalink( $tis_product_id );
                $tis_product_title = $product->get_title();
                $tis_product_excerpt = get_the_excerpt( $tis_product_id );
                $tis_product_content = get_the_content( $tis_product_id );
                if ( $product->is_type( 'simple' ) || $product->is_type( 'external' ) ) {
                	if ( $product->is_in_stock() ){
		       			$add_to_cart_btn_html = '<a href="' . esc_url( $product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $tis_product_id ) . '" data-quantity="1" class="ajax_add_to_cart add_to_cart_button button product-type-' . esc_attr( $product->get_type() ) . '">' . esc_html( $product->single_add_to_cart_text() ). '</a>';
		        	} else {
		        		$add_to_cart_btn_html = '<a href="'.esc_url( $product->get_permalink() ).'" class=" button product-type-' . esc_attr( $product->get_type() ) . '">' . esc_html_e( 'Out Of Stock', 'familab-instagram-shop' ) . '</a>';
		        	}
				}else {
					$add_to_cart_btn_html = '<a href="' . esc_url( $product->get_permalink() ) . '" data-product_id="' . esc_attr( $tis_product_id ) . '" data-quantity="1" class="add_to_cart_button button product-type-' . esc_attr( $product->get_type() ) . '">' . esc_html__( 'Select Options', 'familab-instagram-shop' ) . '</a>';
				}
                ?>
                <div class="single-pin clearfix <?php echo esc_attr($product_info[4]); ?>"
					data-pin-style="<?php echo esc_attr($product_info[4]); ?>"
					data-product-id="<?php echo esc_attr($product_info[0]); ?>"
					data-number="<?php echo esc_attr($product_info[3]); ?>"
					style="top: <?php echo esc_attr($product_info[2]); ?>; left: <?php echo esc_attr($product_info[1]); ?>;">
					<div class="pin-text"><?php echo esc_html($product_info[3]); ?></div>
					<div class="single-pin-product product-item popup-right ">
						<a class="close-popup" href="javascript:void(0);">
							<i class="fa fa-times"></i>
						</a>
						<div class="single-pin-product-header">
							<h3 class="single-product-title woocommerce-loop-product__title">
								<a href="<?php echo esc_attr($tis_product_url); ?>"><?php echo esc_html($tis_product_title); ?></a>
							</h3>
							<div class="single-product-info">
								<div class="single-product-price">
									<?php echo $tis_product_price_html; ?>
								</div>
								<div class="single-product-rating">
									<?php echo $tis_product_rating_html; ?>
								</div>
							</div>
						</div>
						<div class="single-pin-product-main">
							<div class="col-left">
								<div class="single-pin-product-thumbnail">
									<a href="<?php echo esc_attr($tis_product_url); ?>">
										<img width="90" height="110" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="<?php echo esc_attr($tis_product_img[0]); ?>" class="tis-thumbnail lazyload img-responsive" alt="<?php echo esc_html($tis_product_title); ?>">
									</a>
								</div>
							</div>
							<div class="col-right">
								<div class="single-pin-product-sort-desc">
									<p>
										<?php
										echo ($tis_product_excerpt != '') ? wp_trim_words( $tis_product_excerpt, 10, '...' ) : " ";
										 ?>
									</p>
								</div>
							</div>
						</div>
						<div class="single-pin-product-footer">
							<?php echo $add_to_cart_btn_html ?>
						</div>
					</div>
				</div>
                <?php
			}
        	$html = ob_get_clean();
        	echo apply_filters( 'tis_product_loop_detailed', $html, $product_info ); ;
        }
        /**
		 * Loop for product in modal
		 *
		*/
        public static function modal_pin_loop($product_info){
        	$tis_product_id = $product_info[0];
        	$product = wc_get_product( $tis_product_id );
        	ob_start();
			if ( ! empty( $product ) ) {
				$tis_attachment_id = get_post_thumbnail_id($tis_product_id);
                $tis_product_img = Tis_Functions::get_product_image($tis_attachment_id, 'shop_thumbnail');
                $tis_product_url = get_permalink( $tis_product_id );
                $tis_product_title = $product->get_title();
                if ( $product->is_type( 'simple' ) || $product->is_type( 'external' ) ) {
                	if ( $product->is_in_stock() ){
		       			$add_to_cart_btn_html = '<a href="' . esc_url( $product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $tis_product_id ) . '" data-quantity="1" class="ajax_add_to_cart add_to_cart_button product-type-' . esc_attr( $product->get_type() ) . '">' . esc_html( $product->single_add_to_cart_text() ). '</a>';
		        	} else {
		        		$add_to_cart_btn_html = '<a href="'.esc_url( $product->get_permalink() ).'" class="  product-type-' . esc_attr( $product->get_type() ) . '">' . esc_html_e( 'Out Of Stock', 'familab-instagram-shop' ) . '</a>';
		        	}
				}else {
					$add_to_cart_btn_html = '<a href="' . esc_url( $product->get_permalink() ) . '" data-product_id="' . esc_attr( $tis_product_id ) . '" data-quantity="1" class="add_to_cart_button product-type-' . esc_attr( $product->get_type() ) . '">' . esc_html__( 'Select Options', 'familab-instagram-shop' ) . '</a>';
				}
				?>
				<div class="single-modal-product product-item" data-product-id="<?php echo esc_attr($tis_product_id); ?>">
                    <div class="thumb">
                        <a href="<?php echo esc_url($tis_product_url); ?>">
                            <img  class="lazyload img-responsive" alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="<?php echo esc_attr( $tis_product_img[0]); ?>">
                        </a>
                    </div>
                    <div class="info">
                        <h3 class="single-modal-product-title woocommerce-loop-product__title">
                            <a href="<?php echo esc_url($tis_product_url); ?>"><?php echo $tis_product_title; ?></a>
                        </h3>
                        <div class="single-modal-product-price">
                            <?php echo $product->get_price_html(); ?>
                        </div>
                        <?php
                            echo wc_get_rating_html(  $product->get_average_rating() , $product->get_rating_count() );
                        ?>
                    </div>
				</div>
				<?php
			}
        	$html = ob_get_clean();
        	echo apply_filters( 'tis_modal_pin_loop', $html );
        }
    }
}
