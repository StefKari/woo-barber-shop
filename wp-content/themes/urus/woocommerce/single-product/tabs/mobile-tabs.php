<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $tabs = apply_filters( 'woocommerce_product_tabs', array() );
    if ( ! empty( $tabs ) ) : ?>
        <div class="woocommerce-tabs-mobile clearfix">
            <ul class="tabs-mobile" role="tablist">
                <?php foreach ( $tabs as $key => $tab ) : ?>
                    <li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
                        <a data-tab_id="tab-<?php echo esc_attr( $key ); ?>" href="#"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php foreach ( $tabs as $key => $tab ) : ?>
                <div class="wc-tab-mobile" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
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
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
