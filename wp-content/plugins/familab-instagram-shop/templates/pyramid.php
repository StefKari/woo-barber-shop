<?php 
/**
 * Familab Instagram Shop Plugin
 *
 * Pyramid style layout
 *
 * This template can be overridden by copying it to yourtheme/familab-instagram-shop/templates/pyramid.php.
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
 <div class="tis-image-items pyramid-style row no-gutters" data-post-id="<?php echo esc_attr($id); ?>">
 	<div class="col-12 col-lg-2 first-group">
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
		    case 0:
		        echo '</div>';
				echo '<div class="col-12 col-lg-2 second-group">';
		        break;
		    case 2:
		        echo '</div>';
				echo '<div class="col-12 col-lg-4 third-group">';
		        break;
		    case 5:
		        echo '</div>';
				echo '<div class="col-12 col-lg-4 fourth-group">';
		        break;
		    case 7:
		       	echo '</div>';
				echo '<div class="col-6 col-lg-2">';
		        break;	    
		    default:

		    	if ( $index != $array_length-1 && $index > 7 ) {
		    		$normal_index = intval($array_length) - 8;
		    		$remainder = $normal_index % 6;
		    		$classy_index = $array_length - $remainder;
					echo '</div>';
					if ( $index >= $classy_index - 1 ) {
						echo '<div class="col-6 col-lg-2 last-elements">';
		    		}else{
						echo '<div class="col-6 col-lg-2">';
		    		}	
				}
		    	break;
		}
		$index++;
	} 
	?>
	</div>
</div>

<?php 