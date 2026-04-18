<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;
$disable_sale = Urus_Helper::get_option('disable_sale_label',false);
?>
<?php if ( $product->is_on_sale() && !$disable_sale ) : ?>
	<?php
		$content_type = Urus_Helper::get_option('sale_content','default');
		if ($content_type == 'percent'){
			if ( $product->is_type( 'simple' ) ) {
				$regular_price = $product->get_regular_price();
				$sale_price = $product->get_sale_price();
				$max_percentage = ( ( $regular_price - $sale_price ) / $regular_price ) * 100;
			} elseif ( $product->is_type( 'variable' ) ) {
				$max_percentage = 0;
				foreach ( $product->get_children() as $child_id ) {
					$variation = wc_get_product( $child_id );
					$price = $variation->get_regular_price();
					$sale = $variation->get_sale_price();
					if ( $price != 0 && ! empty( $sale ) ) $percentage = ( $price - $sale ) / $price * 100;
					if ( $percentage > $max_percentage ) {
						$max_percentage = $percentage;
					}
				}
			}
			if ( $max_percentage > 0 ) echo "<span class='onsale sale-perc'>-" . round($max_percentage) . "%</span>";
		}else{
			echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>', $post, $product );
		}
	?>
<?php endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
