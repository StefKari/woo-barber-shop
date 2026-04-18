<?php 
/**
 * Familab Instagram Shop Plugin
 *
 * Grid style layout
 *
 * This template can be overridden by copying it to yourtheme/familab-instagram-shop/templates/grid.php.
 * @var $pin_data_array
 * $pin_data_array: saved all pin data as an array()
 * @var $tis_shortcode_type string; pin/modal
 * @var $responsive_class string = '';
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
 <div class="tis-image-items grid-style row" data-post-id="<?php echo esc_attr($id); ?>">
	<?php 
	$index = 0;

	foreach ( $pin_data_array as $image_id=>$image_data ) {
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

		$index++;
	} 
	?>
</div>