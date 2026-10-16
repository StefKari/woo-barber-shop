<?php
/**
 * CSS, image and font related optimizations used by the HTML optimizer.
 */

defined( 'ABSPATH' ) || exit;

final class Barbers_Perf_Assets {

	public static function init() {
		// Emoji detection script/styles are not needed: the theme does not rely on Twemoji images.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		// Static files are cached for a year, so every local asset URL carries its modification time.
		add_filter( 'style_loader_src', array( __CLASS__, 'version_by_mtime' ), 20 );
		add_filter( 'script_loader_src', array( __CLASS__, 'minified_script' ), 15 );
		add_filter( 'script_loader_src', array( __CLASS__, 'version_by_mtime' ), 20 );
	}

	/**
	 * Sources whose .min.js copy we generate ourselves (terser). Other vendor .min.js files are
	 * not used: some themes edit the source and leave an outdated minified file next to it
	 * (urus jquery.sticky.min.js lacks FmSticky), and git checkouts make mtimes meaningless.
	 */
	const MINIFIED_SOURCES = array(
		'/wp-content/themes/urus/assets/js/functions.js',
		'/wp-content/themes/urus/assets/3rd-party/lazyload/jquery.lazy.js',
	);

	/**
	 * Serves foo.min.js instead of foo.js for the files in MINIFIED_SOURCES when the minified
	 * copy is not older than the source (so an edited source is never shadowed by a stale file).
	 */
	public static function minified_script( $src ) {
		if ( ! is_string( $src ) || is_admin() || ! preg_match( '#^(.*?)(?<!\.min)\.js(\?.*)?$#', $src, $m ) ) {
			return $src;
		}
		if ( ! in_array( wp_parse_url( $m[1] . '.js', PHP_URL_PATH ), self::MINIFIED_SOURCES, true ) ) {
			return $src;
		}
		$path = wp_parse_url( $m[1] . '.js', PHP_URL_PATH );
		$host = wp_parse_url( $src, PHP_URL_HOST );
		if ( ! $path || ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) ) {
			return $src;
		}
		$home_path = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
		$file      = untrailingslashit( ABSPATH ) . '/' . ltrim( substr( $path, strlen( $home_path ) ), '/' );
		$min       = substr( $file, 0, -3 ) . '.min.js';
		if ( is_file( $file ) && is_file( $min ) && filemtime( $min ) >= filemtime( $file ) ) {
			return $m[1] . '.min.js' . ( $m[2] ?? '' );
		}
		return $src;
	}

	public static function version_by_mtime( $src ) {
		if ( ! is_string( $src ) || '' === $src || is_admin() ) {
			return $src;
		}
		$path = wp_parse_url( $src, PHP_URL_PATH );
		$host = wp_parse_url( $src, PHP_URL_HOST );
		if ( ! $path || ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) ) {
			return $src;
		}
		$home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$file      = untrailingslashit( ABSPATH ) . '/' . ltrim( substr( $path, strlen( rtrim( $home_path, '/' ) ) ), '/' );
		if ( ! is_file( $file ) ) {
			return $src;
		}
		parse_str( (string) wp_parse_url( $src, PHP_URL_QUERY ), $query );
		$version = ( isset( $query['ver'] ) ? $query['ver'] . '-' : '' ) . filemtime( $file );
		return add_query_arg( 'ver', $version, $src );
	}

	/**
	 * Theme options and widgets contain absolute upload URLs of the production domain. On any
	 * other environment (dev/staging) they are served from the local copy, so they are same-origin
	 * and get the local WebP versions. On production this is a no-op.
	 */
	public static function local_upload_urls( $html ) {
		$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
		if ( in_array( $host, array( 'www.barbers.rs', 'barbers.rs' ), true ) ) {
			return $html;
		}
		$local = trailingslashit( content_url( 'uploads' ) );
		return str_replace(
			array( 'https://www.barbers.rs/wp-content/uploads/', 'https://barbers.rs/wp-content/uploads/' ),
			$local,
			$html
		);
	}

	/**
	 * Template group used to pick a critical CSS file.
	 */
	public static function page_type() {
		if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
			return 'woo-flow';
		}
		if ( is_front_page() ) {
			return 'home';
		}
		if ( function_exists( 'is_product' ) && is_product() ) {
			return 'product';
		}
		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
			return 'shop';
		}
		if ( is_singular( array( 'post', 'blog' ) ) ) {
			return 'post';
		}
		if ( is_home() || is_post_type_archive( 'blog' ) || is_category() || is_tag() ) {
			return 'blog';
		}
		if ( is_404() ) {
			return '404';
		}
		if ( is_search() ) {
			return 'search';
		}
		return 'page';
	}

	/**
	 * With a critical CSS file for this template, inline it and load the full stylesheets
	 * without blocking rendering. Without one, stylesheets stay render-blocking (safe default).
	 */
	public static function optimize_css( $html ) {
		// Google Fonts: let text render with a fallback font while the web font loads.
		$html = preg_replace_callback(
			'#(https?:)?//fonts\.googleapis\.com/css2?\?[^"\']+#i',
			static function ( $m ) {
				return false === stripos( $m[0], 'display=' ) ? $m[0] . '&#038;display=swap' : $m[0];
			},
			$html
		);

		// Device specific file first (the theme has a separate mobile template), then the shared one.
		$base = BARBERS_PERF_DIR . '/critical/' . self::page_type();
		$file = $base . ( wp_is_mobile() ? '-mobile' : '-desktop' ) . '.css';
		if ( ! is_readable( $file ) ) {
			$file = $base . '.css';
		}
		if ( ! is_readable( $file ) || apply_filters( 'barbers_perf_disable_async_css', false ) ) {
			return $html;
		}
		$critical = trim( (string) file_get_contents( $file ) );
		if ( '' === $critical ) {
			return $html;
		}

		$html = preg_replace_callback(
			'#<link\b([^>]*\brel=[\'"]stylesheet[\'"][^>]*)/?>#i',
			static function ( $m ) {
				$attrs = Barbers_Perf_HTML_Optimizer::parse_attrs( $m[1] );
				if ( empty( $attrs['href'] ) || isset( $attrs['data-no-optimize'] ) ) {
					return $m[0];
				}
				$media = $attrs['media'] ?? 'all';
				if ( 'print' === $media ) {
					return $m[0];
				}
				// An unknown rel is not fetched; the loader below turns it into a stylesheet later.
				$attrs['rel'] = 'barbers-css';
				return '<link' . Barbers_Perf_HTML_Optimizer::build_attrs( $attrs ) . ' /><noscript>' . $m[0] . '</noscript>';
			},
			$html
		);

		return preg_replace( '#(<head\b[^>]*>)#i', '$1<style id="barbers-critical-css">' . $critical . '</style>' . self::css_loader(), $html, 1 );
	}

	/**
	 * The full stylesheets are applied after the page has loaded, or immediately on the first
	 * user interaction (scroll, touch, mouse, keyboard). Above-the-fold content is styled by the
	 * critical CSS meanwhile; the links stay in their original document positions, so the final
	 * cascade is exactly the same as with normal stylesheets.
	 */
	private static function css_loader() {
		// Once every stylesheet has loaded, a resize event lets sliders/sticky elements that were
		// initialised with only the critical CSS recalculate their sizes.
		$js = "(function(){var done=false,ev=['scroll','wheel','touchstart','mousedown','keydown'];"
			// All stylesheets download in parallel as media=print (not applied to the screen) and are
			// switched to their real media together, so the page never shows a partially styled state.
			. "function apply(){var ls=[].slice.call(document.querySelectorAll('link[rel=\"barbers-css\"]')),n=ls.length;if(!n)return;"
			. "ls.forEach(function(l){l.dataset.media=l.media||'all';l.media='print';l.onload=l.onerror=function(){if(--n===0)all();};l.rel='stylesheet';});"
			// Transitions are suspended while the full CSS is switched on: otherwise every property that
			// differs from the critical CSS starts a (non-composited) transition on the main thread.
			. "function all(){var s=document.createElement('style');s.textContent='*,*::before,*::after{transition:none!important}';document.head.appendChild(s);"
			. "ls.forEach(function(l){l.media=l.dataset.media;});requestAnimationFrame(function(){dispatchEvent(new Event('resize'));requestAnimationFrame(function(){s.remove();});});}}"
			. "function go(){if(done)return;done=true;ev.forEach(function(e){removeEventListener(e,go,{passive:true});});"
			. "if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',apply);}else{apply();}}"
			. "ev.forEach(function(e){addEventListener(e,go,{passive:true});});"
			. "function later(){setTimeout(go,1000);}if(document.readyState==='complete'){later();}else{addEventListener('load',later);}})();";
		return '<script id="barbers-css-loader" data-no-optimize="1">' . $js . '</script>';
	}

	/**
	 * WPBakery reveals "animate when almost visible" rows (opacity: 0) from its waypoint script,
	 * which now runs late. A tiny inline IntersectionObserver adds the same classes at the same
	 * point (element top at 85% of the viewport), so rows fade in as soon as they are visible.
	 * The two animations used on the site are defined inline, identical to animate.css.
	 */
	public static function early_scroll_animations( $html ) {
		if ( false === strpos( $html, 'wpb_animate_when_almost_visible' ) ) {
			return $html;
		}
		$css = '.wpb_animate_when_almost_visible{opacity:0}.wpb_animate_when_almost_visible:not(.wpb_start_animation){animation:none}.wpb_start_animation{opacity:1}'
			. '.animated{animation-duration:1s;animation-fill-mode:both}'
			. '@keyframes fadeIn{from{opacity:0}to{opacity:1}}.fadeIn{animation-name:fadeIn}'
			. '@keyframes fadeInUp{from{opacity:0;transform:translate3d(0,100%,0)}to{opacity:1;transform:translate3d(0,0,0)}}.fadeInUp{animation-name:fadeInUp}';
		$js  = "(function(){if(!('IntersectionObserver' in window))return;var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('wpb_start_animation','animated');io.unobserve(e.target);}});},{rootMargin:'0px 0px -15% 0px'});"
			. "[].forEach.call(document.querySelectorAll('.wpb_animate_when_almost_visible:not(.wpb_start_animation)'),function(el){io.observe(el);});})();";
		$html = preg_replace( '#</head>#i', '<style id="barbers-wpb-animations">' . $css . '</style></head>', $html, 1 );
		return preg_replace( '#</body>#i', '<script id="barbers-wpb-animations-js" data-no-optimize="1">' . $js . '</script></body>', $html, 1 );
	}

	/**
	 * WPBakery hides rows with "animate when almost visible" (opacity: 0) until its waypoint
	 * script adds `wpb_start_animation animated`. For the hero rows at the top of the home page
	 * the same classes are added server side, so the identical fade-in starts with the first
	 * paint instead of after all JavaScript has loaded.
	 */
	public static function start_hero_animations( $html ) {
		if ( ! is_front_page() ) {
			return $html;
		}
		$count = 0;
		return preg_replace_callback(
			'#<div class="(vc_row [^"]*\bwpb_animate_when_almost_visible\b[^"]*)"#',
			static function ( $m ) use ( &$count ) {
				if ( ++$count > 2 || false !== strpos( $m[1], 'wpb_start_animation' ) ) {
					return $m[0];
				}
				// Shown immediately, without the 1 s fade-in: the slider image inside is the LCP
				// element and an opacity animation keeps the browser from reporting it as painted.
				// The animation name class is removed so the late waypoint script cannot replay it.
				$classes = preg_replace( '#\s+\b(fadeIn|fadeInUp)\b(?!-)#', '', $m[1] );
				return '<div class="' . $classes . ' wpb_start_animation"';
			},
			$html
		);
	}

	/**
	 * The theme lazy loads every image through JavaScript (data-src + SVG placeholder), which
	 * delays the first, above-the-fold images (often the LCP element on shop pages) until all
	 * scripts have run. The first images get their real source right away.
	 */
	public static function eager_first_images( $html, $count = 4 ) {
		$done = 0;
		return preg_replace_callback(
			'#<img\b[^>]*\bclass="[^"]*\blazy\b[^"]*"[^>]*>#i',
			static function ( $m ) use ( &$done, $count ) {
				if ( $done >= $count || ! preg_match( '#\sdata-src="([^"]+)"#', $m[0], $src ) || ! preg_match( '#\ssrc="data:image/svg#', $m[0] ) ) {
					return $m[0];
				}
				$tag = preg_replace( '#\ssrc="data:image/svg[^"]*"#', ' src="' . $src[1] . '"', $m[0] );
				$tag = str_replace( $src[0], '', $tag );
				$tag = preg_replace( '#(\bclass="[^"]*?)\s*\blazy\b#', '$1', $tag );
				$tag = preg_replace( '#\sloading="lazy"#', '', $tag );
				// Without the SVG placeholder the loading image needs an explicit width (see size_gallery_images()).
				// The width only reserves space while loading and is dropped once the image is in.
				if ( preg_match( '#\bwidth="(\d+)"#', $tag, $w ) && false === stripos( $tag, ' style="' ) ) {
					$tag = str_replace( '<img ', '<img style="width:' . (int) $w[1] . 'px;" ', $tag );
					if ( false === stripos( $tag, ' onload=' ) ) {
						$tag = str_replace( '<img ', '<img onload="this.style.removeProperty(&#039;width&#039;)" ', $tag );
					}
				}
				if ( 0 === $done && false === strpos( $tag, 'fetchpriority=' ) ) {
					$tag = str_replace( '<img ', '<img fetchpriority="high" ', $tag );
				}
				++$done;
				return $tag;
			},
			$html
		);
	}

	/**
	 * Images without width/height (theme logo, placeholders, badges) have no box until they load,
	 * which shifts the layout. An aspect-ratio taken from the real file reserves the space; unlike
	 * width/height attributes it never overrides sizes set by the theme CSS.
	 */
	/**
	 * WooCommerce prints the product gallery with opacity:0 and fades it in from JavaScript.
	 * Scripts now run after the page has loaded, so the gallery (the LCP element on product pages)
	 * is shown right away instead; its layout is the same with or without the gallery script.
	 */
	public static function show_product_gallery( $html ) {
		return preg_replace(
			'#(<div\b[^>]*\bclass="[^"]*\bwoocommerce-product-gallery\b[^"]*"[^>]*?)\s*style="opacity:\s*0;\s*transition:\s*opacity\s*\.25s\s*ease-in-out;?"#i',
			'$1',
			$html
		);
	}

	/**
	 * Footer badges are printed with width="90px" (not a valid attribute value) and no height:
	 * use a plain number and add the matching height from the image file.
	 */
	public static function fix_px_dimensions( $html ) {
		return preg_replace_callback(
			'#<img\b(?![^>]*\bheight=)[^>]*\bwidth="(\d+)px"[^>]*>#i',
			static function ( $m ) {
				$tag = str_replace( 'width="' . $m[1] . 'px"', 'width="' . $m[1] . '"', $m[0] );
				$src = preg_match( '#\ssrc="([^"]+)"#', $tag, $s ) ? $s[1] : '';
				$size = $src ? self::upload_image_size( $src ) : false;
				if ( $size && $size[0] > 0 ) {
					$tag = str_replace( '<img ', '<img height="' . (int) round( $m[1] * $size[1] / $size[0] ) . '" ', $tag );
				}
				return $tag;
			},
			$html
		);
	}

	/**
	 * The theme logo is printed from a theme option as the full 1028px file and shown ~160-200px
	 * wide. A srcset with the existing smaller sizes of the same file lets the browser pick one.
	 */
	public static function responsive_logo( $html ) {
		return preg_replace_callback(
			'#<img\b(?![^>]*\bsrcset=)[^>]*\bclass="[^"]*\b_rw\b[^"]*"[^>]*>#i',
			static function ( $m ) {
				if ( ! preg_match( '#\ssrc="([^"]+)"#', $m[0], $src ) ) {
					return $m[0];
				}
				static $cache = array();
				if ( ! isset( $cache[ $src[1] ] ) ) {
					$id     = attachment_url_to_postid( set_url_scheme( $src[1] ) );
					$srcset = $id ? (string) wp_get_attachment_image_srcset( $id, 'full' ) : '';
					// Extra 400px variant (made for the logo) when present next to the original.
					$file = $id ? get_attached_file( $id ) : '';
					if ( $srcset && $file ) {
						$extra = preg_replace( '#(\.[a-z]+)$#i', '-400x198$1', $file );
						if ( file_exists( $extra ) ) {
							$srcset .= ', ' . preg_replace( '#(\.[a-z]+)$#i', '-400x198$1', wp_get_attachment_url( $id ) ) . ' 400w';
						}
					}
					$cache[ $src[1] ] = $srcset;
				}
				if ( '' === $cache[ $src[1] ] ) {
					return $m[0];
				}
				return str_replace( '<img ', '<img srcset="' . esc_attr( $cache[ $src[1] ] ) . '" sizes="(max-width: 1024px) 200px, 160px" ', $m[0] );
			},
			$html
		);
	}

	/**
	 * Footer images (app store badges, logos) are far below the fold but were requested
	 * immediately; native lazy loading defers them until the visitor scrolls near them.
	 */
	public static function lazy_footer_images( $html ) {
		return preg_replace_callback(
			'#<footer\b.*?</footer>#is',
			static function ( $m ) {
				return preg_replace( '#<img\b(?![^>]*\bloading=)#i', '<img loading="lazy"', $m[0] );
			},
			$html
		);
	}

	/**
	 * [width, height] of an image in the uploads folder, from its URL.
	 */
	private static function upload_image_size( $src ) {
		if ( ! preg_match( '#/wp-content/uploads/([^"?\#]+\.(?:jpe?g|png|gif|webp))#i', $src, $rel ) ) {
			return false;
		}
		$uploads = wp_upload_dir( null, false );
		return self::image_size( trailingslashit( $uploads['basedir'] ) . urldecode( $rel[1] ) );
	}

	public static function size_images( $html ) {
		return preg_replace_callback(
			'#<img\b(?![^>]*\bwidth=)(?![^>]*aspect-ratio)[^>]*\bsrc="([^"]+)"[^>]*>#i',
			static function ( $m ) {
				$size = self::upload_image_size( $m[1] );
				if ( ! $size ) {
					return $m[0];
				}
				$ratio = 'aspect-ratio:' . $size[0] . '/' . $size[1] . ';';
				// The theme logo is sized by its container (max-width:100%, height:auto); real
				// width/height attributes give it that box before the file has loaded.
				if ( preg_match( '#\bclass="[^"]*\b_rw\b#', $m[0] ) ) {
					$ratio .= 'height:auto;';
					$m[0]   = str_replace( '<img ', '<img width="' . $size[0] . '" height="' . $size[1] . '" ', $m[0] );
				}
				if ( preg_match( '#\sstyle="([^"]*)"#i', $m[0] ) ) {
					return preg_replace( '#\sstyle="#i', ' style="' . $ratio, $m[0], 1 );
				}
				return str_replace( '<img ', '<img style="' . $ratio . '" ', $m[0] );
			},
			$html
		);
	}

	/**
	 * The theme styles images with width:auto/height:auto, so a product gallery image that is
	 * still loading is drawn 30x30 and everything below jumps when it arrives. An explicit
	 * width equal to the image's own width (still capped by the theme's max-width:100%) gives
	 * the same final size and reserves the box from the first paint.
	 */
	public static function size_gallery_images( $html ) {
		return preg_replace_callback(
			'#(<div[^>]*\bwoocommerce-product-gallery__image\b[^>]*>\s*(?:<a\b[^>]*>\s*)?(?:<figure\b[^>]*>\s*)?)(<img\b[^>]*>)#i',
			static function ( $m ) {
				$img = $m[2];
				if ( preg_match( '#\bwidth="(\d+)"#', $img, $w ) && preg_match( '#\bheight="\d+"#', $img ) ) {
					$width = (int) $w[1];
				} else {
					// e.g. the WooCommerce placeholder image, printed without dimensions.
					$size = preg_match( '#\ssrc="([^"]+)"#', $img, $src ) ? self::upload_image_size( $src[1] ) : false;
					if ( ! $size ) {
						return $m[0];
					}
					$width = $size[0];
				}
				// The site CSS forces width:auto!important here, so the reserving width must be
				// !important too; it is removed on load and the theme rules apply unchanged.
				$style = 'width:' . $width . 'px!important;';
				$img   = preg_match( '#\sstyle="#i', $img )
					? preg_replace( '#\sstyle="#i', ' style="' . $style, $img, 1 )
					: str_replace( '<img ', '<img style="' . $style . '" ', $img );
				if ( false === stripos( $img, ' onload=' ) ) {
					$img = str_replace( '<img ', '<img onload="this.style.removeProperty(&#039;width&#039;)" ', $img );
				}
				return $m[1] . $img;
			},
			$html
		);
	}

	/**
	 * Cached [width, height] of a local image file.
	 */
	private static function image_size( $file ) {
		static $sizes = null;
		if ( null === $sizes ) {
			$sizes = get_transient( 'barbers_perf_image_sizes' );
			$sizes = is_array( $sizes ) ? $sizes : array();
			add_action(
				'shutdown',
				static function () use ( &$sizes ) {
					set_transient( 'barbers_perf_image_sizes', array_slice( $sizes, -500, null, true ), WEEK_IN_SECONDS );
				}
			);
		}
		$key = md5( $file );
		if ( ! array_key_exists( $key, $sizes ) ) {
			$info          = is_readable( $file ) ? getimagesize( $file ) : false;
			$sizes[ $key ] = $info ? array( (int) $info[0], (int) $info[1] ) : false;
		}
		return $sizes[ $key ];
	}

	/**
	 * Above-the-fold slider images must not be lazy loaded; they are the LCP element.
	 */
	public static function optimize_images( $html ) {
		$preloads = array();
		$html     = preg_replace_callback(
			'#<img\b[^>]*\bdata-skip-lazy="1"[^>]*>#i',
			static function ( $m ) use ( &$preloads ) {
				$tag = $m[0];
				if ( ! preg_match( '#\ssrc="([^"]+/wp-content/uploads/[^"]+)"#', $tag, $src ) ) {
					return $tag;
				}
				// The slider for the other device type is hidden: keep it lazy so it is not downloaded.
				$media = self::slider_image_media( $src[1] );
				if ( $media && ( wp_is_mobile() xor false !== strpos( $media, 'max-width' ) ) ) {
					return $tag;
				}
				$tag = preg_replace( '#\sloading="lazy"#', '', $tag );
				$tag = preg_replace( '#\sdecoding="async"#', '', $tag );
				if ( false === strpos( $tag, 'fetchpriority=' ) ) {
					$tag = str_replace( '<img ', '<img fetchpriority="high" ', $tag );
				}
				$preloads[ $src[1] ] = true;
				return $tag;
			},
			$html
		);

		if ( $preloads ) {
			$links = '';
			foreach ( array_slice( array_keys( $preloads ), 0, 2 ) as $src ) {
				$media  = self::slider_image_media( $src );
				$links .= '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url( $src ) . '"' . ( $media ? ' media="' . esc_attr( $media ) . '"' : '' ) . ' />';
			}
			$html = preg_replace( '#(<head\b[^>]*>)#i', '$1' . $links, $html, 1 );
		}
		return $html;
	}

	/**
	 * The home slider has separate desktop and mobile images; preload only the one that is shown.
	 */
	private static function slider_image_media( $src ) {
		if ( false !== stripos( $src, '-mob' ) ) {
			return '(max-width: 767px)';
		}
		if ( false !== stripos( $src, '-desktop' ) ) {
			return '(min-width: 768px)';
		}
		return '';
	}

	/**
	 * Smart Slider 3 keeps every slide at translateX(-100000px) until its JavaScript runs.
	 * Showing the first slide in place before that makes the hero image paint immediately;
	 * once the slider is initialised (.n2-ss-loaded) its own styles take over unchanged.
	 */
	public static function smart_slider_first_paint( $html ) {
		if ( false === strpos( $html, 'n2-ss-slider' ) ) {
			return $html;
		}
		$css = '.n2-ss-slider:not(.n2-ss-loaded) .n2-ss-slide-background[data-public-id="1"],.n2-ss-slider:not(.n2-ss-loaded) .n2-ss-slide[data-first="1"]{transform:none!important}';
		return preg_replace( '#</head>#i', '<style id="barbers-ss3-first-paint">' . $css . '</style></head>', $html, 1 );
	}

	public static function add_resource_hints( $html ) {
		$hints = '';
		if ( false !== strpos( $html, 'fonts.googleapis.com' ) ) {
			$hints .= '<link rel="preconnect" href="https://fonts.googleapis.com" /><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />';
		}
		return $hints ? preg_replace( '#(<head\b[^>]*>)#i', '$1' . $hints, $html, 1 ) : $html;
	}
}
