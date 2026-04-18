<div id="shop-page-wapper" class="mobile-shop-content urus-shop-page-content">
    <div class="mobile_shop_filter">
        <div class="mobile_shop_filter_content">
            <a href="#" class="close-mobile-filter" title="<?php esc_attr_e('Close','urus'); ?>">
                <?php echo familab_icons('close');esc_html_e('Close filter','urus'); ?>
            </a>
            <?php dynamic_sidebar('filter-mobile'); ?>
        </div>
    </div>
    <a href="#." class="mobile-filter-overlay"></a>
    <div class="container mobile_shop_heading">
        <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
        <?php do_action('urus_shop_control_top'); ?>
    </div>
    <div class="container">
        <div class="toolbar-products-mobile">
            <div class="mobile_sort_by">
                <?php woocommerce_catalog_ordering(); ?>
            </div>
            <div class="mobile-filter-buttons">
                <a href="#" class="open-mobile-filters"><?php echo familab_icons('filter');esc_html_e( 'Filter', 'urus' ); ?></a>
            </div>
        </div>
    </div>
    <div class="container mobile-shop-products-list">
        <?php if ( woocommerce_product_loop() ) : ?>
            <?php woocommerce_product_loop_start(); ?>
            <?php if ( wc_get_loop_prop( 'total' ) ) : ?>
                <?php while ( have_posts() ) : ?>
                    <?php the_post(); ?>
                    <?php wc_get_template_part( 'content', 'product' ); ?>
                <?php endwhile; ?>
            <?php endif; ?>
            <?php woocommerce_product_loop_end(); ?>
            <?php do_action( 'woocommerce_after_shop_loop' ); ?>
        <?php else : ?>
            <?php do_action( 'woocommerce_no_products_found' ); ?>
        <?php
        endif;
        ?>
    </div>
</div>
<?php get_footer();?>
