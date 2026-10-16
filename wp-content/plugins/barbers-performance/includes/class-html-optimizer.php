<?php
/**
 * Front-end HTML post-processing: script deferral/delay, async CSS with critical CSS,
 * resource hints. Everything here only changes *when* resources load, never what is rendered.
 *
 * Debug: append ?barbers_noopt=1 to a URL to get the unoptimized page.
 */

defined( 'ABSPATH' ) || exit;

final class Barbers_Perf_HTML_Optimizer {

	/**
	 * External scripts (matched against src) loaded only after the first user interaction.
	 */
	const DELAY_SRC = array(
		'googletagmanager.com/',
		'google-analytics.com/',
		'connect.facebook.net/',
		'clarity.ms/',
		'hs-scripts.com/',
		'hs-analytics.net/',
		'hsforms.net/',
		'hs-banner.com/',
		'doubleclick.net/',
		'google.com/recaptcha/',
		'gstatic.com/recaptcha/',
		'contact-form-7/modules/recaptcha/',
		'youtube.com/iframe_api',
		'youtube.com/s/player/',
	);

	/**
	 * Inline scripts (matched against their content) loaded only after the first user interaction.
	 */
	const DELAY_INLINE = array(
		'googletagmanager.com/gtm.js',
		"fbq('init'",
		'clarity.ms/tag',
	);

	/**
	 * Script types that are data, not code, and must be left untouched.
	 */
	const CODE_TYPES = array( '', 'text/javascript', 'application/javascript', 'text/ecmascript', 'application/ecmascript' );

	public static function init() {
		// Started on init:99 so this buffer wraps Smart Slider's (init:100), whose callback injects
		// its stylesheets; WP Super Cache's buffer starts earlier and stores the optimized output.
		add_action( 'init', array( __CLASS__, 'start' ), 99 );
	}

	public static function enabled() {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || is_feed() || is_embed() || is_customize_preview() ) {
			return false;
		}
		if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			return false;
		}
		if ( isset( $_GET['barbers_noopt'] ) || isset( $_GET['vc_editable'] ) || isset( $_GET['fl_builder'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}
		return (bool) apply_filters( 'barbers_perf_enabled', true );
	}

	public static function start() {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return;
		}
		ob_start( array( __CLASS__, 'process' ) );
	}

	public static function process( $html ) {
		if ( ! is_string( $html ) || strlen( $html ) < 255 || false === stripos( $html, '</html>' ) || false === stripos( $html, '<head' ) ) {
			return $html;
		}
		// The request type (REST, feed, preview, ...) is only known now, not when the buffer started.
		if ( ! did_action( 'template_redirect' ) || ! self::enabled() ) {
			return $html;
		}

		$html = Barbers_Perf_Assets::local_upload_urls( $html );
		$html = Barbers_Perf_A11y::process( $html );
		$html = self::strip_unused_contact_form_assets( $html );
		$html = Barbers_Perf_CSS_Bundler::process( $html );
		$html = Barbers_Perf_Assets::optimize_css( $html );
		$html = self::optimize_scripts( $html );
		$html = Barbers_Perf_Assets::start_hero_animations( $html );
		$html = Barbers_Perf_Assets::early_scroll_animations( $html );
		$html = Barbers_Perf_Assets::smart_slider_first_paint( $html );
		$html = Barbers_Perf_Assets::optimize_images( $html );
		$html = Barbers_Perf_Assets::eager_first_images( $html );
		$html = Barbers_Perf_Assets::fix_px_dimensions( $html );
		$html = Barbers_Perf_Assets::responsive_logo( $html );
		$html = Barbers_Perf_Assets::size_images( $html );
		$html = Barbers_Perf_Assets::size_gallery_images( $html );
		$html = Barbers_Perf_Assets::lazy_footer_images( $html );
		$html = Barbers_Perf_Assets::show_product_gallery( $html );
		$html = Barbers_Perf_Assets::add_resource_hints( $html );

		return $html;
	}

	/**
	 * Contact Form 7 (and its reCAPTCHA) is enqueued on every page but only needed where a form is rendered.
	 */
	private static function strip_unused_contact_form_assets( $html ) {
		if ( false !== strpos( $html, 'wpcf7-form' ) ) {
			return $html;
		}
		$patterns = array(
			'#<link[^>]+id=[\'"]contact-form-7-css[\'"][^>]*>\s*#i',
			'#<script[^>]+id=[\'"](contact-form-7-js(-translations|-before|-after)?|swv-js|wpcf7-recaptcha-js(-before|-after)?|google-recaptcha-js)[\'"][^>]*>.*?</script>\s*#is',
		);
		return preg_replace( $patterns, '', $html );
	}

	/**
	 * Parses an HTML attribute string into a lowercase-keyed array.
	 */
	public static function parse_attrs( $attrs ) {
		$out = array();
		if ( preg_match_all( '/([a-zA-Z_:][-a-zA-Z0-9_:.]*)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+)))?/', $attrs, $m, PREG_SET_ORDER ) ) {
			foreach ( $m as $a ) {
				$value                    = isset( $a[4] ) && '' !== $a[4] ? $a[4] : ( isset( $a[3] ) && '' !== $a[3] ? $a[3] : ( $a[2] ?? '' ) );
				$out[ strtolower( $a[1] ) ] = html_entity_decode( $value, ENT_QUOTES );
			}
		}
		return $out;
	}

	public static function build_attrs( $attrs ) {
		$s = '';
		foreach ( $attrs as $k => $v ) {
			$s .= ( true === $v ) ? ' ' . $k : ' ' . $k . '="' . esc_attr( $v ) . '"';
		}
		return $s;
	}

	private static function matches( $haystack, $needles ) {
		foreach ( $needles as $needle ) {
			if ( false !== stripos( $haystack, $needle ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Scripts are parked (not fetched) and executed later by a small loader, in document order:
	 * - first-party scripts ("later"): after the page has loaded (+1.5 s) or on the first interaction;
	 * - tracking/chat/captcha ("delay"): on the first user interaction only, after the first-party ones.
	 * The page is fully rendered by HTML + CSS before any of them runs.
	 */
	private static function optimize_scripts( $html ) {
		$parked = 0;

		$html = preg_replace_callback(
			'#<script\b([^>]*)>(.*?)</script>#is',
			static function ( $m ) use ( &$parked ) {
				$attrs = self::parse_attrs( $m[1] );
				$body  = $m[2];
				$type  = strtolower( trim( $attrs['type'] ?? '' ) );

				if ( ! in_array( $type, self::CODE_TYPES, true ) ) {
					return $m[0];
				}
				// Scripts explicitly marked to stay synchronous (e.g. the GTM4WP dataLayer setup).
				if ( isset( $attrs['data-no-optimize'] ) || isset( $attrs['data-pagespeed-no-defer'] ) || ( isset( $attrs['data-cfasync'] ) && 'false' === $attrs['data-cfasync'] ) ) {
					return $m[0];
				}

				$src = $attrs['src'] ?? '';
				if ( '' === $src && '' === trim( $body ) ) {
					return $m[0];
				}
				$third = '' !== $src ? self::matches( $src, self::DELAY_SRC ) : self::matches( $body, self::DELAY_INLINE );
				$kind  = $third ? 'text/barbers-delay' : 'text/barbers-later';
				++$parked;
				unset( $attrs['src'], $attrs['async'], $attrs['defer'], $attrs['type'] );
				return '<script type="' . $kind . '"' . ( '' !== $src ? ' data-src="' . esc_attr( $src ) . '"' : '' ) . self::build_attrs( $attrs ) . '>' . ( '' !== $src ? '' : $body ) . '</script>';
			},
			$html
		);

		if ( $parked ) {
			$html = preg_replace( '#</body>#i', self::script_loader() . '</body>', $html, 1 );
		}
		return $html;
	}

	/**
	 * Runs parked scripts in document order: all are inserted at once with async=false (inline
	 * ones as data: URIs), so they download in parallel and execute in order. While first-party
	 * scripts run, document.readyState
	 * reports "loading" and DOMContentLoaded/load/readystatechange listeners are collected; they are
	 * fired (in registration order) once every script has executed, so jQuery ready handlers and
	 * window load handlers behave exactly as on a normal page load. Each held listener, and each
	 * jQuery ready callback, runs in its own task so the work is not one long main-thread block.
	 */
	private static function script_loader() {
		$js = <<<'JS'
(function(){var w=window,d=document,laterStarted=false,laterDone=false,wantDelay=false,delayStarted=false,
act=['keydown','mousedown','touchstart','wheel','pointerdown'];
function list(kind){return [].slice.call(d.querySelectorAll('script[type="'+kind+'"]'));}
var readyPatch="(function($){if(!$||!$.fn||$.fn.__bReady)return;var r=$.fn.ready;$.fn.ready=function(fn){return r.call(this,typeof fn==='function'?function(){var a=arguments,t=this;setTimeout(function(){fn.apply(t,a);});}:fn);};$.fn.__bReady=1;})(window.jQuery);";
function exec(items,done){var n=items.length;if(!n){done();return;}
function add(s,old){s.async=false;s.onload=s.onerror=function(){if(--n===0)done();};old.parentNode.replaceChild(s,old);}
items.forEach(function(old){var s=d.createElement('script');
[].slice.call(old.attributes).forEach(function(a){if(a.name!=='type'&&a.name!=='data-src')s.setAttribute(a.name,a.value);});
s.src=old.dataset.src||('data:text/javascript;charset=utf-8,'+encodeURIComponent(old.text));add(s,old);
if(/\/jquery\/jquery(\.min)?\.js/.test(s.src)){var p=d.createElement('script'),m=d.createComment('');s.parentNode.insertBefore(m,s.nextSibling);n++;
p.src='data:text/javascript;charset=utf-8,'+encodeURIComponent(readyPatch);add(p,m);}});}
function startLater(){if(laterStarted)return;laterStarted=true;
if(d.readyState==='loading'){d.addEventListener('DOMContentLoaded',function(){laterStarted=false;startLater();});return;}
var held=[],addW=w.addEventListener,addD=d.addEventListener,types={DOMContentLoaded:1,load:1,readystatechange:1};
function hold(add,target){return function(t,fn,o){if(types[t]&&fn){held.push([target,t,fn]);return;}return add.call(this,t,fn,o);};}
w.addEventListener=hold(addW,w);d.addEventListener=hold(addD,d);
Object.defineProperty(d,'readyState',{configurable:true,get:function(){return 'loading';}});
exec(list('text/barbers-later'),function(){w.addEventListener=addW;d.addEventListener=addD;delete d.readyState;
var q=[];['readystatechange','DOMContentLoaded','load'].forEach(function(t){held.forEach(function(h){if(h[1]===t)q.push(h);});});
(function step(){var h=q.shift();if(!h){laterDone=true;if(wantDelay)startDelay();return;}var e=new Event(h[1]);
try{typeof h[2]==='function'?h[2].call(h[0],e):h[2].handleEvent(e);}catch(err){setTimeout(function(){throw err;});}setTimeout(step);})();});}
function startDelay(){wantDelay=true;if(!laterDone){startLater();return;}if(delayStarted)return;delayStarted=true;
var ric=w.requestIdleCallback||function(f){return setTimeout(f,1);};
setTimeout(function(){ric(function(){
var addW=w.addEventListener,addD=d.addEventListener;
function now(add,target){return function(t,fn,o){if((t==='DOMContentLoaded'||t==='load')&&fn){setTimeout(function(){var e=new Event(t);typeof fn==='function'?fn.call(target,e):fn.handleEvent(e);});return;}return add.call(this,t,fn,o);};}
w.addEventListener=now(addW,w);d.addEventListener=now(addD,d);exec(list('text/barbers-delay'),function(){});},{timeout:4000});},2000);}
function interact(){act.forEach(function(e){w.removeEventListener(e,interact,{passive:true});});startLater();startDelay();}
act.forEach(function(e){w.addEventListener(e,interact,{passive:true});});
w.addEventListener('scroll',function(){startLater();},{passive:true,once:true});
function onLoad(){setTimeout(startLater,1500);}
if(d.readyState==='complete'){onLoad();}else{w.addEventListener('load',onLoad);}})();
JS;
		return '<script id="barbers-script-loader">' . $js . '</script>';
	}
}
