<?php
defined( 'ABSPATH' ) || exit;

// Slug atributa u WooCommerce admin-u (bez pa_ prefiksa)
// Ako je atribut nazvan "Tip artikla", slug je obično "tip-artikla"
define( 'BARBERS_FILTER_ATTRIBUTE', 'tip-artikla' );

require_once __DIR__ . '/class-admin.php';
require_once __DIR__ . '/class-frontend.php';

if ( is_admin() ) {
    new Barbers_Attribute_Image_Admin( BARBERS_FILTER_ATTRIBUTE );
}

add_action( 'init', function () {
    new Barbers_Attribute_Filter_Frontend( BARBERS_FILTER_ATTRIBUTE );
} );