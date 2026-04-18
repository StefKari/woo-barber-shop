<?php 
/**
 * Familab Instagram Shop Plugin
 *
 * Carousel style layout
 *
 * This template can be overridden by copying it to yourtheme/familab-instagram-shop/templates/carousel.php.
 * @var $pin_data_array
 * $pin_data_array: saved all pin data as an array()
 * @var $tis_shortcode_type string; pin/modal
 * @var $responsive_class string = '';
 */


$tis_breakpoints_arr = array(
 	'576' => array(
 		'slidesPerView' => $tis_xs_cols
 	),
 	'768' => array(
 		'slidesPerView' => $tis_sm_cols
 	),
 	'992' => array(
 		'slidesPerView' => $tis_md_cols
 	),
 	'1200' => array(
 		'slidesPerView' => $tis_lg_cols
 	)
);

$tis_breakpoints_json = json_encode($tis_breakpoints_arr);

if ($tis_shortcode_type == 'modal') {

	/**
	 * @Filter: tis_modal_html ($id, $pin_data_array) / The modal
	 * 
	 * @Filter: tis_modal_pin_loop ($product_info) / The product loop in side modal
	 * @return:  $html
	 */
	do_action( 'tis_modal',  $id, $pin_data_array );
}

?>
 <div class="tis-image-items carousel-style" data-post-id="<?php echo esc_attr($id); ?>">
 	<!-- Swiper -->
	<div class="swiper-container" 
		data-post-id="<?php echo esc_attr($id); ?>"
	 	data-perview="<?php echo esc_attr( $tis_xl_cols ); ?>" 
	 	data-breakpoints="<?php echo esc_attr($tis_breakpoints_json); ?>"
	 	data-space="<?php echo esc_attr($carousel_space); ?>">
	    <div class="swiper-wrapper">
	    	<?php 
	    	$index = 0;
	    	if (!empty($pin_data_array)) {
	    		foreach ( $pin_data_array as $image_id=>$image_data ) {
		    		?>
		    		<div class="swiper-slide">
			    		<?php
						/**
						* Hook: tis_before_image_loop.
						*
						* @Filter: tis_product_loop_detailed ($product_info)
						* @param $product_info
						* @return $html
						*
						* @Filter: tis_image_loop_text ($html)
						* @return $html
						*/
						do_action( 'tis_image_loop',  $post_meta, $image_id, $image_data, $index );

						/**
						 * Hook: tis_after_image_loop.
						 *
						*/
						?>
		    		</div>
		    		<?php

					$index++;
				}
	    	}
	    	 
	    	?>
	    </div>
	    
	</div>
	<!-- Add Arrows -->
	<div class="post-id-<?php echo esc_attr($id); ?> tis-carousel-arrow tis-carousel-prev"><i class="fa fa-chevron-left"></i></div>
    <div class="post-id-<?php echo esc_attr($id); ?> tis-carousel-arrow tis-carousel-next"> <i class="fa fa-chevron-right"></i></div>
</div>