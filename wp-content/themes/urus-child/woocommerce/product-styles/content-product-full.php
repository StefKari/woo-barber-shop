<div class="product-inner">
    <?php
    /**
     * woocommerce_before_shop_loop_item hook.
     *
     * @hooked woocommerce_template_loop_product_link_open - 10
     */
    do_action( 'woocommerce_before_shop_loop_item' );
    ?>
    <div class="product-thumb images">
        <?php
        /**
         * woocommerce_before_shop_loop_item_title hook.
         *
         * @hooked Urus_Pluggable_WooCommerce::product_loop_sale_flash - 10
         * @hooked Urus_Pluggable_WooCommerce::template_loop_product_thumbnail - 15
         */
        do_action('woocommerce_before_shop_loop_item_title' );
        do_action('urus_variation_swatches_loop_item');
        do_action('urus_variation_form_loop_item');
        do_action('urus_function_shop_loop_item_wishlist');
        do_action('urus_loop_product_select_option');
        ?>
    </div>
    <div class="product-info">
        <?php
        /**
         * woocommerce_shop_loop_item_title hook.
         *
         * @hooked Urus_Pluggable_WooCommerce::template_loop_product_title - 10
         */
        do_action( 'woocommerce_shop_loop_item_title' );

        /**
         * woocommerce_after_shop_loop_item_title hook.
         *
         * @hooked woocommerce_template_loop_rating - 5
         * @hooked woocommerce_template_loop_price - 10
         */
         do_action( 'woocommerce_after_shop_loop_item_title' );
        ?>
        <div class="buttons group-buttons">
        
            <?php
                
                /**
                 * woocommerce_after_shop_loop_item hook.
                 *
                 * @hooked woocommerce_template_loop_add_to_cart - 10
                 */
                do_action('urus_function_shop_loop_item_compare');
                do_action( 'woocommerce_after_shop_loop_item' );
                do_action('urus_function_shop_loop_item_quickview');
            ?>
        </div>
        <div class="product-bottom">
            <div class="item_decs"><?php the_excerpt();?></div>
            <div class="buttons group-buttons">
        
                <?php
                    /**
                     * woocommerce_after_shop_loop_item hook.
                     *
                     * @hooked woocommerce_template_loop_add_to_cart - 10
                     */
                    do_action('urus_function_shop_loop_item_compare');
                    do_action( 'woocommerce_after_shop_loop_item' );
                    do_action('urus_function_shop_loop_item_quickview');
                ?>
            </div>
        </div>
        
    </div>

</div>