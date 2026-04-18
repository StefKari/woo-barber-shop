<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists('Tis_Edit_Page')) {
	class Tis_Edit_Page{
        public static function modal_edit(){
        	ob_start();
        	?>
	      	<div class="modal-header">
		        <h5 class="modal-title">
		        	<?php esc_html_e( 'Single pin settings', 'familab-instagram-shop' ); ?>	
		        </h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		    </div>
		    <div class="modal-body">
		      		<div class="pin-product-div">
	  					<select id="tis-product-select" data-perpage="10"></select>
	    				<img class="tis-product-preview" width="80" data-src="<?php echo esc_attr(TIS_IMG_URL . 'no_image.png'); ?>" src="<?php echo esc_attr(TIS_IMG_URL . 'no_image.png'); ?>">
	    			</div>
	    			<div class="pin-style-div">
		      			<?php 
		      			/**
						 * @Filter: tis_pin_style
						 * @param   array $tis_pin_style
						 * @return  $html
		      			*/
		      			echo self::pin_style_select();
		      			?>
		      		</div>
		      		<div class="pin-text-div">
		      			<label>
		      				<?php esc_html_e( 'Set pin text', 'familab-instagram-shop' ); ?>
		        			<input type="text" class="tis-pin-text" name="tis-pin-text" value="1">
		        		</label>
		      		</div>
		    </div>
		    <div class="modal-footer">
		      	<button type="button" class="btn-sm d-none btn btn-danger tis-modal-delete">
		      		<?php esc_html_e( 'Remove Pin', 'familab-instagram-shop' ); ?>
		      	</button>
		        <button type="button" class="btn button-primary tis-modal-save">
		        	<?php esc_html_e( 'Save changes', 'familab-instagram-shop' ); ?>
		        </button>
		    </div>
        	<?php
        	$html = ob_get_clean();
        	echo $html;
        }

        /**
		 * Generate pin style select for Modal pin edit
		 */
        public static function pin_style_select(){
        	
        	$tis_pin_style = array(
			    'style1' => __('Style 1','familab-instagram-shop'),
			    'style2' => __('Style 2','familab-instagram-shop'),
			    'style3' => __('Style 3','familab-instagram-shop'),
			    'style4' => __('Style 4','familab-instagram-shop')
			);
			apply_filters( 'tis_pin_style', $tis_pin_style );
			ob_start();
        	?>
  			<label>
  				<?php esc_html_e( 'Select pin style', 'familab-instagram-shop' ); ?>
    			<select class="tis-pin-style">
    				<?php 
    				foreach ($tis_pin_style as $key => $value) {
    				 	?>
    				 	<option value="<?php echo esc_attr( $key ); ?>">
    				 		<?php echo esc_html( $value ); ?>    			
    				 	</option>
    				 	<?php
    				 } ?>
		     	</select>
		     	<div class="pin-preview">
      				<div class="single-pin style1">1</div>
      			</div>
    		</label>
        	<?php
        	$html = ob_get_clean();
        	return $html;
        }

        public static function drawer_tab_images($tis_ins_token){
        	ob_start();
        	?>
        	<div class="tis-drawer-images">
			 	<h3 class="tis-drawer-title"><?php esc_html_e( 'Select images', 'familab-instagram-shop' ); ?></h3>
			 	<div class="tis-popup-close btn btn-danger btn-sm"><i class="fa fa-times"></i></div>
			  	<div class="tis-js-instagram-images">
					<div class="insta-images-wrapper">
				 		<?php Tis_Functions::get_instagram_images($tis_ins_token);  ?> 
				 	</div>
				</div>
    		</div>
        	<?php
        	$html = ob_get_clean();
        	echo $html;
        }

        public static function drawer_tab_settings(){
        	global $post;

        	/**
			 * Generate default shortcode type
			 */
			$default_tis_type = array(
			    'pin' => __('Pin map', 'familab-instagram-shop'),
			    'modal' => __('Popup Modal', 'familab-instagram-shop')
			);
			$default_tis_type = apply_filters('tis_type_settings',$default_tis_type);

			/**
			 * Generate default shortcode layout style
			 * These template can be found in plugins/familab-instagram-shop/templates/[tis_layout].php.
			 */
			$default_tis_layout = array(
			    'grid' => __('Grid Layout', 'familab-instagram-shop'),
			    'masorny' => __('Masorny Layout', 'familab-instagram-shop'),
			    'masorny2' => __('Masorny Layout 2', 'familab-instagram-shop'),
			    'masorny3' => __('Masorny Layout 3', 'familab-instagram-shop'),
			    'carousel' => __('Carousel Layout', 'familab-instagram-shop'),
			    'pyramid' => __('Pyramid Layout', 'familab-instagram-shop')
			);
			$default_tis_layout = apply_filters('tis_layout_settings',$default_tis_layout);


        	$shortcode = Tis_Shortcode::get_shortcode_string($post->ID);
        	$tis_type = get_post_meta( $post->ID, 'tis_type', true );
        	$get_tis_type = (!empty($tis_type)) ? $tis_type : 'pin';
        	$tis_style = get_post_meta( $post->ID, 'tis_style', true );
        	$get_tis_style = (!empty($tis_style)) ? $tis_style : 'grid';
        	$use_custom_responsive = get_post_meta( $post->ID, 'tis_use_custom_responsive', true );
        	$tis_resolution = get_post_meta( $post->ID, 'tis_resolution', true );
			$get_tis_resolution = (!empty($tis_resolution)) ? $tis_resolution : 'large';
			$carousel_space = get_post_meta( $post->ID, 'carousel_space', true );
			$get_carousel_space = (!empty($carousel_space)) ? $carousel_space : 0;

			

        	ob_start();
        	?>
        	<div class="tis-drawer-settings">
    			<h3 class="tis-drawer-title"><?php esc_html_e( 'Shortcode settings', 'familab-instagram-shop' ); ?></h3>
    			<div class="tis-popup-close btn btn-danger btn-sm"><i class="fa fa-times"></i></div>
    			<div class="shortcode-wrap col-12">
    				<div class="shortcode-copy-group">
	                    <label for="tis_short_code_copy">
	                    	<span class="short-code-instruction"><?php esc_html_e( 'Please remember to update your changes before copy the shortcode here', 'familab-instagram-shop' ); ?></span>
	                    </label>
	                    <div class="input-group mb-3">
		                    <input type="text" id="tis_short_code_copy" class="tis-shortcode form-control" readonly
		                           value="<?php echo esc_attr( $shortcode ); ?>"
		                    >
		                    <div class="input-group-append">
		                    	<button type="button" class="tis_copy_shortcode btn btn-info"><i class="fa fa-copy"></i> <?php esc_html_e('Copy code', 'familab-instagram-shop') ?></button>
		                    </div>
	                    </div>
	                    <p class="tis-copy-notice"><?php esc_html_e('Shortcode has been copied to clipboard', 'familab-instagram-shop'); ?> </p>
	                </div>
	                <?php if( !empty($default_tis_type)):?>
	                <div class="shortcode-type-select mb-3">
            
	                	<label for="tis-select-type"><?php esc_html_e( 'Select shortcode type', 'familab-instagram-shop' ); ?></label>
	                	<select class="form-control" name="tis_type" id="tis-select-type">
                            <?php foreach ($default_tis_type as $key => $value):?>
	                		<option value="<?php echo esc_attr($key);?>" <?php echo ($get_tis_type == $key)?'selected="selected"':''; ?>><?php echo esc_html($value); ?></option>
	                		<?php endforeach;?>
	                	</select>
	                </div>
                    <?php endif;?>

                    <?php if( !empty($default_tis_layout)):?>
	                <div class="shortcode-style-select mb-3">
            
	                	<label for="tis-select-style"><?php esc_html_e( 'Select layout', 'familab-instagram-shop' ); ?></label>
	                	<select class="form-control" name="tis_style" id="tis-select-style">
                            <?php foreach ($default_tis_layout as $key => $value):?>
	                		<option value="<?php echo esc_attr($key);?>" <?php echo ($get_tis_style == $key)?'selected="selected"':''; ?>><?php echo esc_html($value); ?></option>
	                		<?php endforeach;?>
	                	</select>
	                </div>
                    <?php endif;?>
	                <div class="shortcode-resolution-select mb-3">
	                	<label for="tis-select-resolution"><?php esc_html_e( 'Choose image resolution', 'familab-instagram-shop' ); ?></label>
	                	<select class="form-control" name="tis_resolution" id="tis-select-resolution">
	                		<option value="thumb" <?php echo ($get_tis_resolution=='thumb')?'selected="selected"':''; ?>><?php esc_html_e( 'Low resolution', 'familab-instagram-shop' ); ?></option>
	                		<option value="medium" <?php echo ($get_tis_resolution=='medium')?'selected="selected"':''; ?>><?php esc_html_e( 'Medium resolution', 'familab-instagram-shop' ); ?></option>
	                		<option value="large" <?php echo ($get_tis_resolution=='large')?'selected="selected"':''; ?>><?php esc_html_e( 'High resolution', 'familab-instagram-shop' ); ?></option>
	                		
	                	</select>
	                </div>
	                 <div class="shortcode-carousel-space mb-3" <?php echo ($get_tis_style != 'carousel') ? 'style="display: none;"' : '' ; ?>>
	                	<label for="tis_carousel_space">
	                    	<?php esc_html_e( 'Space between items (In pixel)', 'familab-instagram-shop' ); ?>
	                    	<input type="number" min="0"
		                    	name="carousel_space" 
		                    	id="tis_carousel_space" 
		                    	value="<?php echo esc_attr($get_carousel_space); ?>"
		                    >
	                    </label>
	                </div>

	                <div class="shortcode-use-custom-responsive" <?php echo ($get_tis_style == 'masorny' || $get_tis_style == 'pyramid' || $get_tis_style == 'masorny2' || $get_tis_style == 'masorny3' ) ? ' style="display: none;"' : ''; ?>>
	                	<input class="hidden-settings" type="hidden" name="responsive_dropdown" value="<?php echo esc_attr(self::responsive_dropdown($post->ID)); ?>">
	                    <input class="hidden-settings" type="hidden" name="responsive_input" value="<?php echo esc_attr(self::responsive_input($post->ID)); ?>">
	                    <label for="tis_use_custom_responsive">
	                    	<?php esc_html_e( 'Use custom responsive breakpoints', 'familab-instagram-shop' ); ?>
	                    	<input type="checkbox" 
		                    	name="tis_use_custom_responsive" 
		                    	id="tis_use_custom_responsive" 
		                    	<?php echo ($use_custom_responsive == 'on')?'checked="checked"' : ''; ?>
		                    >
	                    </label>
	                    
	                    <div class="tis-settings-responsive row" <?php echo ($use_custom_responsive == 'on')?'' : 'style="display: none;"'; ?>>
	                    	
	                    	<?php 
	                    		if ($tis_style == 'carousel') {
	                    			echo self::responsive_input($post->ID);
	                    		}else{
	                    			echo self::responsive_dropdown($post->ID);	
	                    		}
	                    		
	                    	?>
						</div>
					</div>
                </div>
    		</div>
        	<?php
        	$html = ob_get_clean();
        	echo $html;
        }
        
        public static function single_image_loop($image_item, $value){
        	$product_string = json_encode($value->products);
			$products_arr = $value->products;
			ob_start();
			?>
			<div class="col-4 single-image"
    			data-id="<?php echo esc_attr($image_item);  ?>"
            	data-link="<?php echo esc_attr($value->link);  ?>"
            	data-src="<?php echo esc_attr($value->src); ?>" 
            	data-low-res="<?php echo esc_attr($value->low_res); ?>"
            	data-thumb="<?php echo esc_attr($value->thumb); ?>" 
            	data-width="<?php echo esc_attr($value->width); ?>" 
            	data-height="<?php echo esc_attr($value->height); ?>" 
            	data-caption="<?php echo esc_attr($value->caption); ?>"
            	data-products="<?php echo esc_attr($product_string); ?>"
            	>
            	<div class="single-image-action">
            		<div class="tis-move-image btn-info" title="<?php echo esc_attr_e('Order Image', 'familab-instagram-shop'); ?>"><i class="fa fa-arrows"></i></div>
            		<div class="tis-remove-image btn-danger" title="<?php echo esc_attr_e('Remove Image', 'familab-instagram-shop'); ?>"><i class="fa fa-trash"></i></div>
            	</div>
            	<div class="single-image-holder">
					<img width="<?php echo esc_attr($value->width) ?>" height="<?php echo esc_attr($value->height); ?>" src="<?php echo esc_attr($value->src); ?>" alt="">
					<?php 
						if (!empty($products_arr)) {
							foreach ( $products_arr as $product ) { 
					?>
								<div class="single-pin <?php echo esc_attr($product[4]); ?>" data-pin-style="<?php echo esc_attr($product[4]); ?>" data-product-id="<?php echo esc_attr($product[0]); ?>" data-number="<?php echo esc_attr($product[3]); ?>" style="top: <?php echo esc_attr($product[2]); ?>; left: <?php echo esc_attr($product[1]); ?>;"><?php echo esc_attr($product[3]); ?></div>
					<?php 
							}
						}
					?>
					
				</div>
			</div>
			<?php
			$html = ob_get_clean();
			echo $html;
        }

        public static function responsive_input($post_id){
        	$html_options = array();
        	$items_on_screen_meta_keys_default_vals = array(
				'tis_xl_cols'  => 4,
				'tis_lg_cols'  => 3,
				'tis_md_cols'  => 2,
				'tis_sm_cols'  => 2,
				'tis_xs_cols' => 1
			);
			foreach ( $items_on_screen_meta_keys_default_vals as $items_on_screen_meta_key => $default_items_on_screen ) {
				${$items_on_screen_meta_key} = get_post_meta( $post_id, $items_on_screen_meta_key, true );
				
				switch ($items_on_screen_meta_key) {
				    case 'tis_xl_cols':
				        $tis_screen_name = __('Screen Desktop', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width ≥1200px', 'familab-instagram-shop');
				        break;
				    case 'tis_lg_cols':
				        $tis_screen_name = __('Screen Laptop', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width ≥992px and < 1200px', 'familab-instagram-shop');
				        break;
				    case 'tis_md_cols':
				        $tis_screen_name = __('Screen Tablet', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width ≥768px and < 992px', 'familab-instagram-shop');
				        break;
				    case 'tis_sm_cols':
				       	$tis_screen_name = __('Screen Mobile', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width ≥576px and < 768px', 'familab-instagram-shop');
				        break;
				    case 'tis_xs_cols':
				        $tis_screen_name = __('Screen Small Mobile', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width < 576px', 'familab-instagram-shop');
				        break;
				}
				
				$single_html_option = '';
				$single_html_option .= '<div class="col-12 mb-1">';
					$single_html_option .= '<div class="tis-settings-label">'. $tis_screen_name . '</div>';
					$single_html_option .= '<div class="tis-settings-select">';
						$single_html_option .= '<input class="" name="'.$items_on_screen_meta_key.'" value="'.${$items_on_screen_meta_key}.'">';
						$single_html_option .= '<span class="tis-settings-description">' . $tis_screen_desc . '</span>';
					$single_html_option .= '</div>';
				$single_html_option .= '</div>';
				$html_options[] = $single_html_option;
			}
			ob_start();
	        foreach ($html_options as $option) {
	    		echo $option;
	    	}
	    	$html = ob_get_clean();
	    	return $html;
        }
        public static function responsive_dropdown($post_id){
        	$html_options = array();
        	$items_on_screen_meta_keys_default_vals = array(
				'tis_xl_cols'  => 4,
				'tis_lg_cols'  => 3,
				'tis_md_cols'  => 2,
				'tis_sm_cols'  => 2,
				'tis_xs_cols' => 1
			);
			foreach ( $items_on_screen_meta_keys_default_vals as $items_on_screen_meta_key => $default_items_on_screen ) {
				${$items_on_screen_meta_key} = get_post_meta( $post_id, $items_on_screen_meta_key, true );
				
				switch ($items_on_screen_meta_key) {
				    case 'tis_xl_cols':
				        $tis_screen_name = __('Screen Desktop', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width ≥1200px', 'familab-instagram-shop');
				        break;
				    case 'tis_lg_cols':
				        $tis_screen_name = __('Screen Laptop', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width ≥992px and < 1200px', 'familab-instagram-shop');
				        break;
				    case 'tis_md_cols':
				        $tis_screen_name = __('Screen Tablet', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width ≥768px and < 992px', 'familab-instagram-shop');
				        break;
				    case 'tis_sm_cols':
				       	$tis_screen_name = __('Screen Mobile', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width ≥576px and < 768px', 'familab-instagram-shop');
				        break;
				    case 'tis_xs_cols':
				        $tis_screen_name = __('Screen Small Mobile', 'familab-instagram-shop');
				        $tis_screen_desc = __('Number of images on screen width < 576px', 'familab-instagram-shop');
				        break;
				}
				
				$single_html_option = '';
				$single_html_option .= '<div class="col-12 mb-1">';
					$single_html_option .= '<div class="tis-settings-label">'. $tis_screen_name . '</div>';
					$single_html_option .= '<div class="tis-settings-select">';
						$single_html_option .= '<select class="" name="'.$items_on_screen_meta_key.'">';
						$tis_breakpoints = array(6,5,4,3,2,1);
						foreach ($tis_breakpoints as $option_value) {
							$option_text = $option_value;
							if ($option_value == 5 ) {
								$option_value = 15;
							}else{
								$option_value = 12/intval($option_value);
							}

							$single_html_option .=  '<option ';
							if(${$items_on_screen_meta_key} == $option_value){
								$single_html_option .= ' selected="selected" ';
							}
							$single_html_option .= ' value="' . $option_value . '" > ' . $option_text .' ';
							$single_html_option .= __( 'Items', 'familab-instagram-shop' ) . '</option>';
						}
						$single_html_option .= '</select>';
						$single_html_option .= '<span class="tis-settings-description">' . $tis_screen_desc . '</span>';
					$single_html_option .= '</div>';
				$single_html_option .= '</div>';
				$html_options[] = $single_html_option;
			}
			ob_start();
	        foreach ($html_options as $option) {
	    		echo $option;
	    	}
	    	$html = ob_get_clean();
	    	return $html;
        }
    }
}

new Tis_Edit_Page();
