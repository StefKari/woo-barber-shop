<?php 
/**
 * Familab Instagram Shop Plugin
 *
 * Masorny style layout 03
 *
 * This template can be overridden by copying it to yourtheme/familab-instagram-shop/templates/masorny3.php.
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
 <div class="tis-image-items masorny-style3 row no-gutters" data-post-id="<?php echo esc_attr($id); ?>">
 	<div class="col-12 col-md-3 small-group tis-col-15">
	<?php 
	$index = 0;
	$array_length = count((array)$pin_data_array);
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
		?>
		
		<?php
		do_action( 'tis_image_loop',  $post_meta, $image_id, $image_data, $index );
		/**
		 * Hook: tis_after_image_loop.
		 *
		*/
		switch ($index) {
			case 1:
		        echo '</div>';
				echo '<div class="col-12 col-md-6 big-group tis-col-15">';
		        break;
		    case 2:
		        echo '</div>';
				echo '<div class="col-12 col-md-3 small-group tis-col-15">';
		        break;
		    case 4:
		        echo '</div>';
				echo '<div class="col-12 col-md-3 small-group tis-col-15">';
		        break;
		}
		$index++;
		if ($index == 5 ) {
			$index = 0;
		}
	} 
	?>
	</div>
</div>

<?php 