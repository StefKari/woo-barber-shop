<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists('Tis_Functions')){
    class Tis_Functions{

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
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }


            // Add ajax actions
            add_action( 'wp_ajax_tis_load_products', array(__CLASS__,'load_more_products') );
            add_action( 'wp_ajax_tis_load_single_product', array(__CLASS__,'load_single_product') );
            add_action( 'wp_ajax_tis_load_more_instagram', array(__CLASS__,'load_more_instagram') );
            add_action( 'wp_ajax_tis_refresh_instagram', array(__CLASS__,'refresh_instagram') );
            
            self::$initialized = true;

        }

        /**
		* Validate the Instagram token
		*/
		public static function token_validate(){
			$tis_ins_token = get_option('tis_ins_token');
			$ins_user_id = explode(".", $tis_ins_token)[0];
			if ($tis_ins_token == '') {
				return;
			}
			$instagram_api_url = 'https://api.instagram.com/v1/users/' . esc_attr( $ins_user_id ) . '/media/recent/?access_token=' . esc_attr( $tis_ins_token ) . '&count=1';
	        // Get remote HTML file

			$response = wp_remote_get( $instagram_api_url );
			ob_start();
			if ( ! is_wp_error( $response ) ) {
				$response_body = json_decode( $response['body'] );
				if (isset($response_body->meta)){
					$r_body = $response_body->meta;
				}else{
					$r_body = $response_body;
				}
				if ( $r_body->code !== 200 ) {
					?>
					<div class="text-danger tis-error-message"> 
						<?php echo esc_html($r_body->error_message); ?>
					</div>
					<?php 
				}else{
					?>
					<div class="text-success"> 
						<?php esc_html_e('You Instagram has successfully linked', 'familab-instagram-shop'); ?>
					</div>
					<?php 
				}
			} else {
				$error_string = $response->get_error_message();
				?>
				<div class="text-danger tis-error-message"><?php echo esc_html($error_string); ?></div>
				<?php
			}
			$html = ob_get_clean();
			echo $html;
		}

    	/**
	 	* Get product image
	 	**/
        public static function get_product_image($attachment_id, $size = 'thumbnail', $icon = false ){
			$thumb = wp_get_attachment_image_src(  $attachment_id ,$size, $icon );
			if ($thumb == false) {
				$no_image_src = TIS_IMG_URL . 'no_image.png';
				$no_image = [$no_image_src, 150,150, false];
				return $no_image;
			}else{
				return $thumb;
			}
		}

		/**
	 	* Ajax load more products
	 	**/
	 	public static function load_more_products(){

			$full_products_list = array();
			$full_products_list['total'] =  0;
			$full_products_list['perpage'] =  0;
			$full_products_list['items'] = array();

			if ( ! class_exists( 'WooCommerce' ) ) {
				return $full_products_list;
			}
			$page =  (isset($_POST['page'])) ? $_POST['page'] : 1;
		    $perpage = (isset($_POST['perpage'])) ? $_POST['perpage'] : 10;
		    $search_term = (isset($_POST['q'])) ? $_POST['q'] : "";

		  	//WP_Query arguments
			$args = array(
				'post_type'              => array( 'product' ),
				'post_status'            => array( 'publish' ),
				's'                      => $search_term,
				'paged'                  => $page,
				'posts_per_page'         => $perpage
			);


			$rows = new WP_Query( $args ); // The Query


			if ( $rows->post_count != 0 ) {
				foreach ( $rows->posts as $row ) {
					
					$product              = new WC_Product( $row->ID );
					$attachment_id = get_post_thumbnail_id($product->get_id());
					$product_img = self::get_product_image($attachment_id);
					$full_products_list['items'][] = array(
						'title'      => $product->get_title(),
						'price_html' => $product->get_price_html(),
						'id'         => $product->get_id(),
						'img'		 => $product_img[0]
					);
				}
				$full_products_list['total'] = $rows->found_posts;
				$full_products_list['perpage'] =  $perpage;
			}

			wp_reset_postdata();
			wp_send_json($full_products_list);
			wp_die();
		}

		/**
	 	* Ajax Load single product info
	 	**/
		public static function load_single_product(){
			$return_values = array(); 
			if ( isset($_POST['q'] ) ) {
				$product_id = $_POST['q'];
				$product = wc_get_product( $product_id );
				if ( ! empty($product) ) {
					$attachment_id = get_post_thumbnail_id($product->get_id());
					$product_img = self::get_product_image($attachment_id);
					
					$return_values['img'] = $product_img[0];
					$return_values['title'] =  $product->get_title();
					$return_values['id'] =  $product->get_id();
				}
			}
			wp_send_json($return_values);
			wp_die();
		}

		/**
	 	* Ajax load more Instagram images
	 	**/
		public static function load_more_instagram() {
			global $tis;
			$response = array(
				'html'     => '',
				'err'      => 'no',
				'has_more' => 'no',
				'next_url' => ''
			);
			$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

			if ( ! wp_verify_nonce( $nonce, 'tis_load_more' ) ) {
				$response['err'] = 'yes';
				wp_send_json( $response );
			}
			 
			$next_page_url = isset( $_POST['next_page_url'] ) ? trim( $_POST['next_page_url'] ) : '';


			if ( $next_page_url == '' ) {
				$response['err'] = 'yes';
				wp_send_json( $response );
				wp_die();
			}
			
			$response = wp_remote_get( $next_page_url );

			ob_start();
			if ( ! is_wp_error( $response ) ) {//WP remote get success
				$response_body = json_decode( $response['body'] );
				if (isset($response_body->meta)){
					$r_body = $response_body->meta;
				}else{
					$r_body = $response_body;
				}
				if ( $r_body->code !== 200 ) {//Instagram Response status error
					?>
					<div class="text-danger tis-error-message"> 
						<?php echo $r_body->error_message; ?>
					</div>
					<?php 
				}else{//Instagram Response status success
					if (isset($response_body->data)) {
						$items_as_objects = $response_body->data;
						$items            = array();
						foreach ( $items_as_objects as $item_object ) {
							if ( isset( $item_object->images->standard_resolution ) ) {
								$item['id']     = $item_object->id;
								$item['link']   = $item_object->link;
								$item['src']    = $item_object->images->standard_resolution->url;
								$item['low_res']    = $item_object->images->low_resolution->url;
								$item['thumb']    = $item_object->images->thumbnail->url;
								$item['width']  = $item_object->images->standard_resolution->width;
								$item['height'] = $item_object->images->standard_resolution->height;
								$item['caption'] = (isset($item_object->caption->text)) ? esc_html($item_object->caption->text) : '' ;
								$items[]        = $item;
							} else {
								$item['id']     = $item_object->id;
								$item['link']   = $item_object->link;
								$item['src']    = $item_object->images->low_resolution->url;
								$item['low_res']    = $item_object->images->low_resolution->url;
								$item['thumb']    = $item_object->images->thumbnail->url;
								$item['width']  = $item_object->images->low_resolution->width;
								$item['height'] = $item_object->images->low_resolution->height;
								$item['caption'] = (isset($item_object->caption->text)) ? esc_html($item_object->caption->text) : '' ;
								$items[]        = $item;
							}
						}
						// Store datas in transient, expire after 1 hour
						//set_transient( $transient_var, $items, 60 * 60 );
					}
					if ( isset( $items ) ) {
						
						if ( ! empty( $items ) ) {
							foreach ( $items as $item ) { 
								?>
		                        <div class="instagram-item" 
		                        	data-id="<?php echo esc_attr($item['id']);  ?>"
		                        	data-link="<?php echo esc_attr($item['link']);  ?>"
		                        	data-src="<?php echo esc_attr($item['src']); ?>" 
		                        	data-low-res="<?php echo esc_attr($item['low_res']); ?>" 
		                        	data-width="<?php echo esc_attr($item['width']); ?>" 
		                        	data-height="<?php echo esc_attr($item['height']); ?>" 
		                        	data-caption="<?php echo esc_attr($item['caption']); ?>" 
		                        >
		                        	<input type="checkbox" class="tis-checkbox" name="selected_item_<?php echo $item['id'];?>">

		                            <img width="<?php echo esc_attr( $item['width'] ); ?>"
		                                 height="<?php echo esc_attr( $item['height'] ); ?>"
		                                 src="<?php echo esc_url( $item['thumb'] ); ?>" alt="<?php echo esc_attr( $item['caption'] );  ?>"/>
		                        </div>
								<?php 
							}
						}
					}
					if ( isset( $response_body->pagination ) ) {
						if ( trim( $response_body->pagination->next_url ) != '' ) {
							$response['has_more'] = 'yes';
							$response['next_url'] = $response_body->pagination->next_url;
						}
					}
				}
			} else {//Wp remote get failed
				$error_string = $response->get_error_message();
				?>
					<div class="text-danger tis-error-message"> 
						<?php echo  $error_string; ?>
					</div>
				<?php
			}
			$response['html'] .= ob_get_clean();
			wp_send_json( $response );
			wp_die();
		}

		/**
		* Get Instagram Images
		*/
		public static function get_instagram_images($tis_ins_token = ''){
			$return_html = '';
			ob_start();
			if ($tis_ins_token != '') {
				$ins_user_id = explode(".", $tis_ins_token)[0];
				$transient_var     = $ins_user_id;
				//DELETE TRANSIENT IN CASE OF DEV
				delete_transient( $transient_var );
				// Check for transient, if none, grab remote HTML file
				if ( false === ( $items = get_transient( $transient_var ) ) ) {
					$instagram_api_url = 'https://api.instagram.com/v1/users/' . esc_attr( $ins_user_id ) . '/media/recent/?access_token=' . esc_attr( $tis_ins_token ) . '&count=20';
			        // Get remote HTML file
					$response = wp_remote_get( $instagram_api_url );

					if ( ! is_wp_error( $response ) ) {
						$response_body = json_decode( $response['body'] );

						if (isset($response_body->meta)){
							$r_body = $response_body->meta;
						}else{
							$r_body = $response_body;
						}
						if ( $r_body->code !== 200 ) {
							?>
								<div class="text-danger tis-error-message"> 
									<div class="text-danger tis-error-message"> 
										<?php echo $r_body->error_message; ?>
									</div>
									<a class="btn btn-primary" href="<?php echo admin_url('admin.php?page=familab-instagram-shop'); ?>">
										<?php _e('Go to Instagram settings', 'familab-instagram-shop')  ?>
									</a>
								</div>
							<?php
						}
						if (isset($response_body->data)) {
							$items_as_objects = $response_body->data;
							$items            = array();
							foreach ( $items_as_objects as $item_object ) {
								if ( isset( $item_object->images->standard_resolution ) ) {
									$item['id']     = $item_object->id;
									$item['link']   = $item_object->link;
									$item['src']    = $item_object->images->standard_resolution->url;
									$item['low_res']    = $item_object->images->low_resolution->url;
									$item['thumb']    = $item_object->images->thumbnail->url;
									$item['width']  = $item_object->images->standard_resolution->width;
									$item['height'] = $item_object->images->standard_resolution->height;
									$item['caption'] = (isset($item_object->caption->text)) ? esc_html($item_object->caption->text) : '' ;
									$items[]        = $item;
								} else {
									$item['id']     = $item_object->id;
									$item['link']   = $item_object->link;
									$item['src']    = $item_object->images->low_resolution->url;
									$item['low_res']    = $item_object->images->low_resolution->url;
									$item['thumb']    = $item_object->images->thumbnail->url;
									$item['width']  = $item_object->images->low_resolution->width;
									$item['height'] = $item_object->images->low_resolution->height;
									$item['caption'] = (isset($item_object->caption->text)) ? esc_html($item_object->caption->text) : '' ;
									$items[]        = $item;
								}
							}
							
							// Store datas in transient, expire after 1 hour
							set_transient( $transient_var, $items, 60 * 60 );
						}
					} else {
						$error_string = $response->get_error_message();
						?>
							<div class="text-danger tis-error-message"> 
								<?php echo  $error_string; ?>
							</div>
						<?php
					}
				}
				if ( isset( $items ) ) {
					if ( ! empty( $items ) ) {
						?>
						<div class="instagram-items clearfix">
						<?php
						foreach ( $items as $item ) { 
							?>
	                        <div class="instagram-item" 
	                        	data-id="<?php echo esc_attr($item['id']);  ?>"
	                        	data-link="<?php echo esc_attr($item['link']);  ?>"
	                        	data-src="<?php echo esc_attr($item['src']); ?>" 
	                        	data-low-res="<?php echo esc_attr($item['low_res']); ?>" 
	                        	data-thumb="<?php echo esc_attr($item['thumb']);  ?>"
	                        	data-width="<?php echo esc_attr($item['width']); ?>" 
	                        	data-height="<?php echo esc_attr($item['height']); ?>" 
	                        	data-caption="<?php echo esc_attr($item['caption']); ?>" 
	                        >
	                        	<input type="checkbox" class="tis-checkbox" name="selected_item_<?php echo $item['id'];?>">
	                            <img width="<?php echo esc_attr( $item['width'] ); ?>"
                                     height="<?php echo esc_attr( $item['height'] ); ?>"
                                     src="<?php echo esc_url( $item['thumb'] ); ?>" alt="<?php echo esc_attr( $item['caption'] );  ?>"/>
	                        </div>
							<?php 
						}
						?>
						</div>
						<?php
					}
				}
				if ( isset( $response_body->pagination ) ) {
					if (isset( $response_body->pagination->next_url )) {
						if ( trim( $response_body->pagination->next_url ) != '' && !empty($response_body->pagination) ) {
							$load_more_nonce = wp_create_nonce( 'tis_load_more' );
							?>
							<div class="col-12 text-center">
								<input type="hidden" class="tis-load-more-nonce"
	                               value="<?php echo esc_attr( $load_more_nonce ); ?>">
								<a href="javascript:void(0);" 
									class="tis-insta-loadmore btn btn-info mt-2"
									data-next-url="<?php echo $response_body->pagination->next_url; ?>">
									<span class="load-more-loader">
										<i class="fa fa-refresh fa-spin fa-2x"></i>
									</span>
									<span class="load-more-text">
										<?php esc_html_e('Load more', 'familab-instagram-shop'); ?>
									</span>
								</a>
							</div>
							<?php
						}
					}
				}
				$return_html = ob_get_clean();
			}else{
				$return_html = esc_html_e('Invalid Instagram Token','familab-instagram-shop');
			}

			echo apply_filters('tis_get_instagram_images', $return_html);
		}

		/**
		* Refresh Instagram Images
		*/
		public static function refresh_instagram() {

			$expired_list = $_POST['expired_list'];
			$post_id = $_POST['post_id'];
			$tis_ins_token = get_option('tis_ins_token', false);
			if (!empty($expired_list) && $tis_ins_token != false ) {
				$pin_data_str    = get_post_meta( $post_id, 'tis_pin_data', true );
				$pin_data_array = json_decode( $pin_data_str );
				$respond_array = false;
				foreach ($expired_list as $image_id) {
					$request_url = 'https://api.instagram.com/v1/media/'.$image_id.'?access_token='.$tis_ins_token;
					$response = wp_remote_get( $request_url );
					if ( ! is_wp_error( $response ) ) {
						$response_body = json_decode( $response['body'] );
						$image_data = $response_body->data;
						$respond_array[$image_id] = array(
							'src' => $image_data->images->standard_resolution->url,
							'thumb' => $image_data->images->thumbnail->url,
							'low_res' =>  $image_data->images->low_resolution->url
						);
						$pin_data_array->$image_id->src = $image_data->images->standard_resolution->url;
						$pin_data_array->$image_id->thumb = $image_data->images->thumbnail->url;
						$pin_data_array->$image_id->low_res = $image_data->images->low_resolution->url;
					}
				}
				update_post_meta( $post_id, 'tis_pin_data', json_encode($pin_data_array) );
				wp_send_json( $respond_array );
				wp_die();
				
			}
			
		}
    }
}