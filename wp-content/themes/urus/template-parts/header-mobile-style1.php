<div class="familab-header-mobile style1">
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
       <?php echo do_shortcode('[aws_search_form]'); ?>
       <?php if(class_exists('WooCommerce')):?>

      <a class="header-icon cart-link js-drawer-open-cart" href="javascript:void(0);">
          <span class="icon">
              <?php echo familab_icons('cart'); ?>
              <span class="icon-count"><span class="cart-counter"><?php echo WC()->cart->cart_contents_count; ?></span></span>
          </span>
      </a>
       <?php endif;?>

       <!--<a href="javascript:void(0);" class="header-icon js-drawer-open-top" aria-controls="Familab_SearchDrawer" aria-expanded="false">
           <?php //echo familab_icons('search'); ?>

       </a> -->
   </div>

   <!-- MOBILE CART DRAWER -->
</div>
