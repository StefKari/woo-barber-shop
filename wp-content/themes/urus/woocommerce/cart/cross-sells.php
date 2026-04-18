<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$atts = array(
    'loop'         => 'false',
    'ts_items'     => 2,
    'xs_items'     => 2,
    'sm_items'     => 3,
    'md_items'     => 3,
    'lg_items'     => 4,
    'ls_items'     => 4,
    'navigation'   => 'false',
    'slide_margin' => 30
);
$atts['responsive_settings'] = array(
    '1500' => array(
    ),
    '1200' => array(
    ),
    '992' => array(
    ),
    '768' => array(
        'slide_margin' => 20
    ),
    '480' => array(
        'slide_margin' => 15
    )
);
$atts = apply_filters('_related_products_carousel_settings',$atts);
$carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
$woo_product_item_layout= Urus_Helper::get_option('woo_product_item_layout','classic');
$product_item_class = array('product-item');
$product_item_class [] = $woo_product_item_layout;
if ( $cross_sells ) : ?>

    <div class="cross-sells urus-box-products">
        <?php
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'urus' ) );
        
		if ( $heading ) :
			?>
			<div class="box-head">
				<h2><?php echo esc_html( $heading ); ?></h2>
			</div>
		<?php endif; ?>
        
        <div class="urus-products urus-products-carousel swiper-container urus-swiper" <?php echo esc_attr($carousel_settings);?>>
            <div class="swiper-wrapper">
                <?php foreach ( $cross_sells as $cross_sell ) : ?>
                    <div class="swiper-slide">
                        <?php
                        $post_object = get_post( $cross_sell->get_id() );

                        setup_postdata( $GLOBALS['post'] =& $post_object );

                        ?>
                        <div <?php post_class($product_item_class); ?>>
                            <?php
                            wc_get_template_part('product-styles/content-product', 'default' );

                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
            
        </div>
        <!-- If we need navigation buttons -->
        <div class="slick-arrow next">
            <?php echo familab_icons('arrow-right'); ?>
        </div>
        <div class="slick-arrow prev">
            <?php echo familab_icons('arrow-left'); ?>
        </div>
    </div>
<?php
   endif;

wp_reset_postdata();
