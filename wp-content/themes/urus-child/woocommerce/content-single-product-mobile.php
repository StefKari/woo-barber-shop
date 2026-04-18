<?php 
global $product;
$mobile_single_sticky_btn = Urus_Helper::get_option('mobile_single_sticky_btn', 1);
$ajax_available = false;
if ( $product->is_type( 'external' ) || $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
    $ajax_available = false;
}else{
    if ( $product->is_purchasable() ) {
        $ajax_available = true;
    }
}
$class = array();
global $product;
$attachment_ids = $product->get_gallery_image_ids();
$class[] ='product';
if( empty($attachment_ids)){
    $class[] ='no-gallery-image';
}
$mobile_single_layout_style = Urus_Helper::get_option('mobile_single_layout_style','style1');
    $class[] = 'mobile-single-product-layout-'.$mobile_single_layout_style;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class($class); ?>>
    <div class="urus-single-product-top clearfix">
        <div class="urus-product-gallery__wrapper clearfix">
            <?php
            /**
             * Hook: woocommerce_before_single_product_summary.
             *
             * @hooked woocommerce_show_product_sale_flash - 10
             * @hooked woocommerce_show_product_images - 20
             */
            do_action( 'woocommerce_before_single_product_summary' );
            ?>
        </div>
        <div  class="summary entry-summary clearfix">
            <div id="summary" >
                <div class="summary__inner__wapper">
                    <div class="summary__inner clearfix">
                        <?php
                        /**
                         * Hook: woocommerce_single_product_summary.
                         *
                         * 
                         * @hooked woocommerce_template_single_rating - 10
                         * 
                         * @removed woocommerce_template_single_excerpt - 20
                         * @hooked woocommerce_template_single_add_to_cart - 30
                         * @hooked woocommerce_template_single_meta - 40
                         * @hooked woocommerce_template_single_sharing - 50
                         * @hooked WC_Structured_Data::generate_product_data() - 60
                         * @hooked woocommerce_template_single_price - 20
                         * @hooked woocommerce_template_single_title - 30
                         */
                        do_action( 'woocommerce_single_product_summary' );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    /**
     * Hook: woocommerce_after_single_product_summary.
     *
     * @hooked woocommerce_output_product_data_tabs - 10
     * @hooked woocommerce_upsell_display - 15
     * @hooked woocommerce_output_related_products - 20
     */
    do_action( 'woocommerce_after_single_product_summary' );
    ?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>

<?php if ($mobile_single_sticky_btn): ?>
    <?php if ($product->is_purchasable() ||  $product->is_type( 'external' ) ):?>
    <div class="single-product-fixed-btn">
        <div class="buttons-wrapper">
            <?php 
                if ( $product->is_in_stock() ) {?>
                    <a  href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" 
                        data-quantity="1" 
                        class="<?php echo (esc_attr($ajax_available))? 'ajax_add_to_cart add_to_cart_button':''; ?> add-to-cart-fixed-btn urus-single-add-to-cart-btn button <?php echo 'product-type-'.esc_attr($product->get_type()); ?>"
                        data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" 
                        data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" 
                        rel="nofollow" tabindex="0">
                        <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                    </a>
                    <?php if ( $product->is_type( 'variable' ) ): ?>
                       <div class="add_to_cart_extend">
                            
                       </div>
                    <?php endif; ?>
            <?php }else{ ?>
                     <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" 
                        data-quantity="1" 
                        class="disabled add-to-cart-fixed-btn urus-single-add-to-cart-btn button add-to-cart-out-of-stock <?php echo 'product-type-'.esc_attr($product->get_type()); ?>"
                        data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" 
                        data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" 
                        rel="nofollow" tabindex="0" disabled="">
                          <?php esc_html_e( 'Out Of Stock', 'urus' ); ?>
                    </a>
            <?php }?>
            <button type="button" class="js-drawer-open-cart mobile-open-cart">
                <?php echo familab_icons('cart'); ?>
                <span class="icon-count"><span class="cart-counter">0</span></span>
            </button>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>


   
