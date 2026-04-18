<div class="urus-single-product-top sticky-layout clearfix">
   
    <div class="urus-product-gallery__wrapper clearfix">
        <div class="urus-product-gallery__wrapper__inner clearfix">
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
    </div>
    <div  class="summary entry-summary clearfix">
        <div id="summary" >
            <div class="summary__inner__wapper">
                <div class="summary__inner clearfix">
                    <?php
                        /**
                         * Hook: woocommerce_single_product_summary.
                         *
                         * @hooked woocommerce_template_single_title - 5
                         * @hooked woocommerce_template_single_rating - 10
                         * @hooked woocommerce_template_single_price - 10
                         * @hooked woocommerce_template_single_excerpt - 20
                         * @hooked woocommerce_template_single_add_to_cart - 30
                         * @hooked woocommerce_template_single_meta - 40
                         * @hooked woocommerce_template_single_sharing - 50
                         * @hooked WC_Structured_Data::generate_product_data() - 60
                         */
                        do_action( 'woocommerce_single_product_summary' );
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
do_action('urus_sticky_single_product_tab');
?>