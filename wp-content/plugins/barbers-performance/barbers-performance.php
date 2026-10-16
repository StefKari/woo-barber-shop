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
