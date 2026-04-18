<?php
$woocommerce_enable_myaccount_registration = get_option('woocommerce_enable_myaccount_registration', 'no');
?>
<div id="Familab_MobileMenu" class="nav-drawer drawer style1">
    <ul class="nav nav-tabs mobile-nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active show" data-toggle="tab" href="#homeMobileMenu" role="tab"
               aria-controls="homeMobileMenu" aria-selected="true">
                <div class="menubar-mobile-icon">
                    <div class="icon-inner">
                        <span></span>
                    </div>
                </div>
                <div class="nav-text">
                    <?php esc_html_e('Menu', 'urus'); ?>
                </div>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#customerLogin" role="tab" aria-controls="customerLogin"
               aria-selected="false">
                <?php echo familab_icons('user') ?>
                <div class="nav-text account-text">
                    <?php esc_html_e('Account', 'urus'); ?>
                </div>
            </a>
        </li>
    </ul>
    <div class="tab-content mobile-nav-content">
        <div class="tab-pane active show" id="homeMobileMenu" role="tabpanel">
            <div class="js-slinky-menu" style="position: relative;">
                <?php
                    if( has_nav_menu('mobile_menu')){
                        wp_nav_menu(array(
                            'menu' => 'mobile_menu',
                            'theme_location' => 'mobile_menu',
                            'container' => '',
                            'container_class' => '',
                            'container_id' => '',
                            'menu_class' => 'urus-mobile-menu ',
                            'walker' => new Urus_SLinky_Walker()
                        ));
                    }

                ?>
            </div>
            <?php
                do_action('urus_after_mobile_menu');
            ?>
        </div>
        <div class="tab-pane " id="customerLogin" role="tabpanel">
            <div class="familab-loader urus-loader"></div>
            <div class="panel_content">
                <?php if (is_user_logged_in()): ?>
                    <?php if(class_exists('WooCommerce')):?>
                        <ul class="user_links">
                            <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
                                <?php $ajax_logout = ($endpoint === 'customer-logout' ? ' ajax-log-out' : ''); ?>
                                <li class="<?php echo wc_get_account_menu_item_classes($endpoint);
                                    echo e_data($ajax_logout); ?>">
                                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"><?php echo esc_html($label); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else:?>
                        <ul class="user_links">
                            <li><a href="<?php echo esc_url(get_edit_user_link());?>"><?php esc_html_e('Profile', 'urus'); ?></a></li>
                            <li><a href="<?php echo esc_url(wp_logout_url( get_permalink() )); ?>"><?php esc_html_e('Logout', 'urus'); ?></a></li>
                        </ul>
                    <?php endif;?>
                <?php else: ?>
                    <div class="user_login">
                        <h4 class="customer_login_title"> <?php echo familab_icons('user') ?></h4>
                        <div class="familab-login-input">
                            <div class="drawer-login-fail"></div>
                            <label for="Customer_username"> <?php esc_html_e('Username', 'urus'); ?> *</label>
                            <input type="text" class="" name="username" id="Customer_username" value="" placeholder="<?php esc_attr_e('Username', 'urus'); ?>">
                            <label for="Customer_password"><?php esc_html_e('Password', 'urus'); ?> *</label>
                            <input class="" type="password" name="password" id="Customer_password" placeholder="<?php esc_attr_e('Password', 'urus'); ?>">
                            <div class="text-left">
                                <a  href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-password"><?php esc_html_e('Forgot password?', 'urus'); ?></a>
                                <input type="button" class="familab-button-login" name="login"
                                       value="<?php esc_html_e('Login', 'urus'); ?>">
                                <?php if ($woocommerce_enable_myaccount_registration == "yes"): ?>
                                    <div class="spec"><span><?php esc_html_e('Or', 'urus'); ?></span></div>
                                    <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="mobile-register-title"><?php esc_html_e('Register now', 'urus'); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="close-menu  js-drawer-close">
        <?php echo familab_icons('close') ?>
        <?php esc_html_e('Close', 'urus'); ?>
    </div>
</div>
<div id="Familab_SearchDrawer" class="search-drawer drawer style2">
    <?php
        $search_redirect =  Urus_Helper::get_option('theme_use_search_page', false);
        $search_sku =  Urus_Helper::get_option('theme_search_sku', false);
        $use_clear_button = Urus_Helper::get_option('theme_search_clear',true);
     ?>
    <form id="familab-search-mobile" data-current-action=""  data-search-sku="<?php echo esc_attr(( $search_sku == true ) ?'true':'false'); ?>" data-form-submit="<?php echo esc_attr(( $search_redirect == true ) ?'true':'false'); ?>" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php
            $search_settings = Urus_Helper::get_option('theme_search_type', 'product');
            if ($search_settings == 'product') {
                echo '<input type="hidden" name="post_type" value="product" />';
            }
         ?>

        <div class="search-drawer-header">

            <div class="drawer_input">

                <input type="search" name="s" placeholder="<?php esc_attr_e('Enter your keyword...','urus');?>" data-last-search-field="">
                <?php
                if ($use_clear_button) {
                ?>
                    <a href="javascript:void(0);" class="btn_clear_text" title="<?php esc_attr__( 'Clear text', 'urus' ); ?>"> <?php echo familab_icons('close'); ?> </a>
                <?php
                }
                ?>
                <button type="submit" class="drawer_submit js-btn-ajax-search">
                  <?php echo familab_icons('search'); ?>
                </button>
            </div>
        </div>
        <div class="search-drawer-inner">
            <div class="familab-loader urus-loader"></div>
            <div id="search_drawer_content">
                <div class="search-result"></div>
            </div>
        </div>
    </form>
    <input type="hidden" id="familab-ins-value" name="familab-ins-value">
    <div class="drawer_back">
        <a href="javascript:void(0);" class="js-drawer-close">
            <?php echo familab_icons('close'); ?>
            <span class="text"><?php esc_html_e('Close','urus');?></span>
        </a>
    </div>
</div>
<div id="Familab_CartDrawer" class="cart-drawer drawer style1">
    <div class="urus-mini-cart-content">
        <div class="widget_shopping_cart_content"></div>
    </div>
</div>
