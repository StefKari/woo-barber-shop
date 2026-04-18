<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>
    <div class="woocommerce-canvas-tabs-content clearfix">
        <?php foreach ( $tabs as $key => $tab ) : ?>
            <div class="wc-tab-canvas" id="tab-<?php echo esc_attr( $key ); ?>" data-title_id="tab-title-<?php echo esc_attr( $key ); ?>">
                <div class="tab_detail">
                    <div class="tab-head">
                        <span class="title"><?php echo esc_html( $tab['title'] );?></span>
                        <a class="close-tab" href="#">
                            <?php familab_icons('close') ?>
                            <?php esc_html_e('Close','urus');?></a>
                    </div>
                    <div class="tab__inner">
                        <?php if ( isset( $tab['callback'] ) ) { call_user_func( $tab['callback'], $key, $tab ); } ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
