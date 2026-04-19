<?php

add_action('after_setup_theme', function() {
    require_once get_stylesheet_directory() . '/inc/attribute-filter/loader.php';
});
add_action( 'wp_enqueue_scripts', 'urus_child_enqueue_styles' );

if( !function_exists('urus_child_enqueue_styles')){
    function urus_child_enqueue_styles() {
        $parent_style ='urus-prent';
        wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css');
        wp_enqueue_style( 'urus-child',
            get_stylesheet_directory_uri() . '/style.css',
            array( $parent_style ),
            wp_get_theme()->get('Version')
        );
    }
}

function urus_related_products_carousel_settings($atts,$woo_single_layout) {
  if( $woo_single_layout == 'full'){
      $atts['md_items'] = 4;
      $atts['lg_items'] = 6;

  }else{
      $atts['md_items'] = 3;
      $atts['lg_items'] = 4;

  }
  return $atts;
}
add_filter('urus_related_products_carousel_settings','urus_related_products_carousel_settings',10,2);


// ************* Remove default Posts type since no blog *************

// Remove side menu
add_action( 'admin_menu', 'remove_default_post_type' );

function remove_default_post_type() {
    remove_menu_page( 'edit.php' );
}

// Remove +New post in top Admin Menu Bar
add_action( 'admin_bar_menu', 'remove_default_post_type_menu_bar', 999 );

function remove_default_post_type_menu_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'new-post' );
}

// Remove Quick Draft Dashboard Widget
add_action( 'wp_dashboard_setup', 'remove_draft_widget', 999 );

function remove_draft_widget(){
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
}

// End remove post type

function so_38878702_remove_hook(){
   remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
}
add_action( 'woocommerce_single_product_summary', 'so_38878702_remove_hook', 1 );  

function so_38878702_add_hook(){
    $brands = wp_get_post_terms( get_the_ID(), 'pwb-brand' );

	if ( ! is_wp_error( $brands ) ) {

		if ( sizeof( $brands ) > 0 ) {

			$show_as = get_option( 'wc_pwb_admin_tab_brands_in_single' );

			if ( $show_as != 'no' ) {

                echo '<div class="pwb-single-product-brands-container">';
				    echo '<div class="pwb-single-product-brands pwb-clearfix">';

				    if ( $show_as == 'brand_link' ) {
				    	$before_brands_links  = '<span class="pwb-text-before-brands-links">';
				    	$before_brands_links .= apply_filters( 'pwb_text_before_brands_links', esc_html__( 'Brands', 'perfect-woocommerce-brands' ) );
				    	$before_brands_links .= ':</span>';
				    	echo wp_kses_post( apply_filters( 'pwb_html_before_brands_links', $before_brands_links ) );
				    }

				    foreach ( $brands as $brand ) {
				    	$brand_link    = get_term_link( $brand->term_id, 'pwb-brand' );
				    	$attachment_id = get_term_meta( $brand->term_id, 'pwb_brand_image', 1 );

				    	$image_size          = 'thumbnail';
				    	$image_size_selected = get_option( 'wc_pwb_admin_tab_brand_logo_size', 'thumbnail' );
				    	if ( $image_size_selected != false ) {
				    		$image_size = $image_size_selected;
				    	}

				    	$attachment_html = wp_get_attachment_image( $attachment_id, $image_size );

				    	if ( ! empty( $attachment_html ) && $show_as == 'brand_image' || ! empty( $attachment_html ) && ! $show_as ) {
				    		echo '<a href="' . esc_url( $brand_link ) . '" title="' . esc_attr( $brand->name ) . '">' . $attachment_html . '</a>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				    	} else {
				    		echo '<a href="' . esc_url( $brand_link ) . '" title="' . esc_html__( 'View brand', 'perfect-woocommerce-brands' ) . '">' . esc_html( $brand->name ) . '</a>';
				    	}
				    }
				    echo '</div>';

				    the_title( '<h1 class="product_title entry-title">', '</h1>' );

                echo '</div>';
			}
		}
	}
}
add_action( 'woocommerce_single_product_summary', 'so_38878702_add_hook', 5 );




/* ------------------------------------
/* ------------------------------------
*********  ALL TRANSLATIONS ***********
---------------------------------------
---------------------------------------
**/


/**
 *  Order Received Translate
 */
add_filter(  'gettext',  'change_specific_received_order', 10, 3 );
add_filter(  'ngettext',  'change_specific_received_order', 10, 3 );
function change_specific_received_order( $translated, $text, $domain  ) {
    switch ($text) {
        case 'Order received' :
            $translated = __( 'Porudžbina primljena', $domain );
            break;
        case 'Thank you. Your order has been received.' :
            $translated = __( 'Hvala Vam. Vaša narudžbina je primljena.', $domain );
            break;
        case 'Order number:' :
            $translated = __( 'Broj narudžbine:', $domain );
            break;
        case 'Date:' :
            $translated = __( 'Datum:', $domain );
            break;
        case 'Total:' :
            $translated = __( 'Ukupno:', $domain );
            break;
        case 'Payment method:' :
            $translated = __( 'Način plaćanja:', $domain );
            break;
        case 'Cash on delivery' :
            $translated = __( 'Plaćanje po dostavi', $domain );
            break;
        case 'Order details' :
            $translated = __( 'Detalji narudžbine', $domain );
            break;
        case 'Billing address' :
            $translated = __( 'Adresa za naplatu', $domain );
            break;
        case 'No products were found matching your selection.' :
            $translated = __( 'Nije pronađen nijedan proizvod koji odgovara vašem odabiru.', $domain );
            break;
        case 'Downloads' :
            $translated = __( 'Preuzeti proizvodi', $domain );
            break;
        case 'Orders' :
            $translated = __( 'Porudžbine', $domain );
            break;
        case 'Dashboard' :
            $translated = __( 'Kontrolna tabla', $domain );
            break;
        case 'Addresses' :
            $translated = __( 'Adrese', $domain );
            break;
        case 'Account details' :
            $translated = __( 'Detalji naloga', $domain );
            break;
        case 'Logout' :
            $translated = __( 'Izlogujte se', $domain );
            break;
        case 'Login' :
            $translated = __( 'Ulogujte se', $domain );
            break;
        case 'Register' :
            $translated = __( 'Registrujte se', $domain );
            break;
        case 'Username or email address' :
            $translated = __( 'Username i email adresa', $domain );
            break;
        case 'Remember me' :
            $translated = __( 'Zapamti me', $domain );
            break;
        case 'Lost your pasword?' :
            $translated = __( 'Zaboravili ste šifru?', $domain );
            break;
    }
    return $translated;
}



/**
 *  Cart Translate
 */
add_filter(  'gettext',  'change_specific_add_to_cart_notice', 10, 3 );
add_filter(  'ngettext',  'change_specific_add_to_cart_notice', 10, 3 );
function change_specific_add_to_cart_notice( $translated, $text, $domain  ) {
    switch ($text) {
        case 'You cannot add another "%s" to your cart.' :
            $translated = __( 'Ne možete da dodate "%s" u Vašu korpu.', $domain );
            break;
        case 'Your cart is currently empty.' :
            $translated = __( 'Vaša korpa je trenutno prazna.', $domain );
            break;
        case '%s has been added to your cart.' :
            $translated = __( '"%s" je dodat u korpu.', $domain );
            break;
        case '%s removed.' :
            $translated = __( '"%s" je uklonjen.', $domain );
            break;
        case 'Undo?' :
            $translated = __( 'Unazad?', $domain );
            break;
        case 'Return to shop' :
            $translated = __( 'Nazad na kupovinu', $domain );
            break;
        case 'Cancelled by the customer' :
            $translated = __( 'Otkazano od strane mušterije', $domain );
            break;
        case 'View cart' :
            $translated = __( 'Pogledaj korpu', $domain );
            break;
        case 'Checkout' :
            $translated = __( 'Idi na plaćanje', $domain );
            break;
        case 'Product successfully removed.' :
            $translated = __( 'Proizvod uspešno uklonjen', $domain );
            break;
        case 'Cart updated.' :
            $translated = __( 'Korpa ažurirana', $domain );
            break;
        case 'Subtotal:' :
            $translated = __( 'Svega:', $domain );
            break;
    }
    return $translated;
}


/**
 *  MiniCart Translate
 */
function miniCart( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Subtotal:' :
            $translated_text = __( 'Svega:', 'woocommerce' );
            break;

        case 'View cart' :
            $translated_text = __( 'Pregled korpe', 'woocommerce' );
            break;

        case 'Checkout' :
            $translated_text = __( 'Plaćanje', 'woocommerce' );
            break;
    }
    return $translated_text;
}


/**
 *  Checkout Page Translate + Additions
 */
function checkoutPageTranslate( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Billing details' :
            $translated_text = __( 'Detalji za naplatu', 'woocommerce' );
            break;

        case 'Additional information' :
            $translated_text = __( 'Dodatne informacije', 'woocommerce' );
            break;

        case 'First name' :
            $translated_text = __( 'Ime', 'woocommerce' );
            break;

        case 'Last name' :
            $translated_text = __( 'Prezime ', 'woocommerce' );
            break;

        case 'Country / Region' :
            $translated_text = __( 'Država / Region', 'woocommerce' );
            break;

        case 'Street address' :
            $translated_text = __( 'Ulica i kućni broj', 'woocommerce' );
            break;

        case 'District' :
            $translated_text = __( 'Mesto', 'woocommerce' );
            break;

        case 'Town / City' :
            $translated_text = __( 'Grad', 'woocommerce' );
            break;

        case 'State' :
            $translated_text = __( 'Država', 'woocommerce' );
            break;

        case 'ZIP Code' :
            $translated_text = __( 'Poštanski broj', 'woocommerce' );
            break;

        case 'Phone' :
            $translated_text = __( 'Telefon', 'woocommerce' );
            break;

        case 'Email address' :
            $translated_text = __( 'Adresa e-pošte', 'woocommerce' );
            break;

        case 'Order notes' :
            $translated_text = __( 'Napomene o narudžbini (opciono)', 'woocommerce' );
            break;

        case 'Your order' :
            $translated_text = __( 'Vaša narudžbina', 'woocommerce' );
            break;

        case 'Place order' :
            $translated_text = __( 'Naručite', 'woocommerce' );
            break;

        case 'Product name' :
            $translated_text = __( 'Proizvod', 'woocommerce' );
            break;

        case 'Default sorting' :
            $translated_text = __( 'Podrazumevano sortiranje', 'woocommerce' );
            break;

        case 'Sort by popularity' :
            $translated_text = __( 'Po popularnosti', 'woocommerce' );
            break;

        case 'Sort by latest' :
            $translated_text = __( 'Po datumu', 'woocommerce' );
            break;

        case 'Sort by price: low to high' :
            $translated_text = __( 'Sortiraj po ceni: od manje ka većoj', 'woocommerce' );
            break;

        case 'Sort by price: high to low' :
            $translated_text = __( 'Sortiraj po ceni: od veće ka manjoj', 'woocommerce' );
            break;

        case 'Ship to a different address?' :
            $translated_text = __( 'Dostava na drugu adresu?', 'woocommerce' );
            break;

    }
    return $translated_text;
}
add_filter( 'gettext', 'checkoutPageTranslate', 20, 3 );


/**
 *  Wishlist Page Translate
 */
function wishlistPageTranslate( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Product name' :
            $translated_text = __( 'Proizvod', 'woocommerce' );
            break;

        case 'Unit price' :
            $translated_text = __( 'Cena', 'woocommerce' );
            break;

        case 'Stock status' :
            $translated_text = __( 'Dostupnost', 'woocommerce' );
            break;

        case 'In Stock' :
            $translated_text = __( 'Na stanju', 'woocommerce' );
            break;

        case 'Remove' :
            $translated_text = __( 'Ukloni', 'woocommerce' );
            break;

        case 'Add to cart' :
            $translated_text = __( 'Dodaj u korpu', 'woocommerce' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'wishlistPageTranslate', 20, 3 );



/**
 *  Cart Page Translate
 */
function cartPageTranslate( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Update cart' :
            $translated_text = __( 'Ažuriraj', 'woocommerce' );
            break;

        case 'Proceed to checkout' :
            $translated_text = __( 'Nastavi ka plaćanju', 'woocommerce' );
            break;

        case 'Total' :
            $translated_text = __( 'Ukupno', 'woocommerce' );
            break;

        case 'Cart totals' :
            $translated_text = __( 'Ukupna vrednost korpe', 'woocommerce' );
            break;

        case 'Subtotal' :
            $translated_text = __( 'Svega', 'woocommerce' );
            break;
        case 'Subtotal:' :
            $translated_text = __( 'Svega:', 'woocommerce' );
            break;

        case 'Quantity' :
            $translated_text = __( 'Količina', 'woocommerce' );
            break;

        case 'Price' :
            $translated_text = __( 'Cena', 'woocommerce' );
            break;

        case 'Product' :
            $translated_text = __( 'Proizvod', 'woocommerce' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'cartPageTranslate', 20, 3 );


/**
 * Remove shipping calculator from cart page
 */

// Disable address display in cart
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

// Remove calculator
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_shipping_calculator', 20 );
add_action( 'wp_head', 'remove_shipping_calculator_cart' );
function remove_shipping_calculator_cart() {
    if ( is_cart() ) {
        remove_action( 'woocommerce_cart_collaterals', 'woocommerce_shipping_calculator', 20 );
    }
}

// Disable fields
add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_false' );
add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );

// Remove text messages
add_filter( 'gettext', 'remove_shipping_messages_cart', 20, 3 );
function remove_shipping_messages_cart( $translated_text, $text, $domain ) {
    if ( is_cart() ) {
        switch ( $text ) {
            case 'Shipping options will be updated during checkout.' :
                $translated_text = '';
                break;
            case 'Calculate shipping' :
                $translated_text = '';
                break;
            case 'Change address' :
                $translated_text = '';
                break;
            case 'Shipping to' :
                $translated_text = '';
                break;
        }
    }
    return $translated_text;
}

/**
 * Translate Shipping to Dostava - Cart & Checkout
 */
add_filter( 'woocommerce_shipping_package_name', 'translate_shipping_to_dostava' );
function translate_shipping_to_dostava( $package_name ) {
    return str_replace( 'Shipping', 'Dostava', $package_name );
}

// Alternative method - Direct translation
add_filter( 'gettext', 'force_translate_shipping', 999, 3 );
add_filter( 'ngettext', 'force_translate_shipping', 999, 3 );
function force_translate_shipping( $translated, $text, $domain ) {
    if ( $domain === 'woocommerce' || $domain === 'default' ) {
        if ( $text === 'Shipping' ) {
            return 'Dostava';
        }
        if ( $text === 'Shipping:' ) {
            return 'Dostava:';
        }
    }
    return $translated;
}

// Another method - directly in cart totals
add_filter( 'woocommerce_cart_shipping_method_full_label', 'change_shipping_label', 10, 2 );
function change_shipping_label( $label, $method ) {
    $label = str_replace( 'Shipping', 'Dostava', $label );
    return $label;
}

// 1. ADD CUSTOM FIELD TO WOOCOMMERCE SETTINGS
add_filter('woocommerce_general_settings', 'add_promo_text_setting');
function add_promo_text_setting($settings) {
    $promo_setting = array(
        array(
            'title' => __('Promotion message', 'woocommerce'),
            'desc'  => __('Text that will be displayed after product meta', 'woocommerce'),
            'id'    => 'wc_promo_text_single',
            'type'  => 'textarea',
            'css'   => 'min-width:300px;',
            'default' => 'Besplatna dostava do kraja meseca! 🚚',
        ),
        array(
            'title' => __('Show sticker on listing', 'woocommerce'),
            'desc'  => __('Show free delivery sticker on product listings', 'woocommerce'),
            'id'    => 'wc_show_delivery_sticker',
            'type'  => 'checkbox',
            'default' => 'no',
        ),
    );

    return array_merge($settings, $promo_setting);
}

// 2. SHOW AFTER PRODUCT META
add_action('woocommerce_single_product_summary', 'display_promo_text_after_meta', 45);
function display_promo_text_after_meta() {
    $promo_text = get_option('wc_promo_text_single', 'Cena dostave iznosi od 450 do 550 RSD, u zavisnosti od lokacije. Za porudžbine u vrednosti iznad 5.000 RSD, dostava je besplatna. 🚚');

    if (!empty($promo_text)) {
        echo '<p class="promo-text-box">' . wp_kses_post($promo_text) . '</p>';
    }
}

// 3. SHOW STICKER ON PRODUCT LISTING
add_action('woocommerce_before_shop_loop_item_title', 'display_delivery_sticker', 10);
function display_delivery_sticker() {
    $show_sticker = get_option('wc_show_delivery_sticker', 'no');

    if ($show_sticker === 'yes') {
        echo '<div class="delivery-sticker-wrapper">';
        echo '<img src="https://www.barbers.rs/wp-content/uploads/stickers/besplatna_dostava.png" alt="Besplatna dostava" class="delivery-sticker" />';
        echo '</div>';
    }
}