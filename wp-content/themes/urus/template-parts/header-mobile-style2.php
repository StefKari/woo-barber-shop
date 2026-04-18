<div class="familab-header-mobile style2">
    <div class="header-inner">
        <div class="mobile-header-left">
            <a href="javascript:void(0);" class="open-mobile-nav  js-drawer-open-left" aria-controls="Familab_MobileMenu" aria-expanded="false">

                <div class="menubar-mobile-icon">
                    <div class="icon-inner">
                        <span></span>
                    </div>
                </div>
            </a>
        </div>
        <!-- MOBILE NAV -->
        <div class="mobile-header-logo">
            <div class="logo">
                <?php Urus_Helper::get_logo('mobile');?>
            </div>
        </div>
        <!-- HEADER MOBILE LOGO -->
        <div class="mobile-button-group">
            <a class="header-icon cart-link js-drawer-open-cart" href="javascript:void(0);">

          <span class="icon">
              <?php echo familab_icons('cart'); ?>
              <span class="icon-count"><span class="cart-counter"><?php echo WC()->cart->cart_contents_count; ?></span></span>
          </span>
            </a>
        </div>
        <!-- MOBILE CART DRAWER -->
    </div>


    <div class="mobile-search-input">
    <?php
        $show_search_category = Urus_Helper::get_option('show_search_category',false);
        $instant_search = Urus_Helper::get_option('enable_instant_search',true);

        echo Urus_Pluggable_WooCommerce::header_search_form(false, false, $show_search_category);
        if ($instant_search) {
            ?>
            <div class="inline-search-result-wrapper">
                <div class="inline-search-result"  data-products="" data-ipp="">
                    <!-- Js handle -->
                </div>
            </div>
            <?php
        }
    ?>
    </div>
    <!-- MOBILE Search input -->
</div>
