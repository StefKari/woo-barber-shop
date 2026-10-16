<?php
/**
 * Plugin Name: Barbers Performance
 * Description: Optimizacija front-enda bez promene izgleda: odloženo izvršavanje skripti (defer), učitavanje skripti za praćenje na prvu interakciju, asinhroni CSS sa kritičnim CSS-om, preload LCP slike i ispravke pristupačnosti (ARIA, alt tekstovi) bez promene izgleda.
 * Version: 1.0.0
 * Requires PHP: 8.1
 * Author: Barbers
 * Text Domain: barbers-performance
 */

defined( 'ABSPATH' ) || exit;

define( 'BARBERS_PERF_DIR', __DIR__ );
define( 'BARBERS_PERF_URL', plugins_url( '', __FILE__ ) );

require_once __DIR__ . '/includes/class-html-optimizer.php';
require_once __DIR__ . '/includes/class-assets.php';
require_once __DIR__ . '/includes/class-a11y.php';
require_once __DIR__ . '/includes/class-css-bundler.php';

Barbers_Perf_Assets::init();
Barbers_Perf_A11y::init();
Barbers_Perf_HTML_Optimizer::init();

/*
 * The theme's PJAX sorting/filtering always requests "&paged=1", which WordPress answers with a
 * 301 to the same URL without it: an extra uncached round trip (~1.5 s) on every sort. Serve that
 * URL directly instead; the page still declares the clean URL as canonical (Yoast).
 */
add_filter(
	'redirect_canonical',
	static function ( $redirect_url, $requested_url ) {
		if ( $redirect_url && isset( $_GET['paged'] ) && '1' === $_GET['paged']
			&& remove_query_arg( 'paged', $requested_url ) === remove_query_arg( 'paged', $redirect_url ) ) {
			return false;
		}
		return $redirect_url;
	},
	10,
	2
);
