<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>
    <div class="woocommerce-canvas-tabs clearfix">
        <ul class="canvas-tabs-title" role="tablist">
            <?php foreach ( $tabs as $key => $tab ) : ?>
                <li class="<?php echo esc_attr( $key ); ?>_tab cv_tab_title" id="tab-title-<?php echo esc_attr( $key ); ?>">
                    <a data-tab_id="tab-<?php echo esc_attr( $key ); ?>" href="#"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
