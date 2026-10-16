<?php
/**
 * Accessibility / crawlability fixes on the theme markup that do not change how the page looks.
 */

defined( 'ABSPATH' ) || exit;

final class Barbers_Perf_A11y {

	/**
	 * aria-label for icon-only links of the theme (matched by a class in the opening tag).
	 */
	const ICON_LINK_LABELS = array(
		'open-mobile-nav'         => 'Otvori meni',
		'js-drawer-open-top'      => 'Pretraga',
		'js-drawer-open-cart'     => 'Korpa',
		'js-drawer-close'         => 'Zatvori',
		'btn_clear_text'          => 'Obriši pretragu',
		'js-urus-wishlist'        => 'Lista želja',
		'backtotop'               => 'Nazad na vrh',
		'quantity-plus'           => 'Povećaj količinu',
		'quantity-minus'          => 'Smanji količinu',
	);

	/**
	 * Names for links that only wrap an image without alt text, matched by link target.
	 */
	const LINK_TARGET_LABELS = array(
		'play.google.com' => 'Preuzmite aplikaciju na Google Play',
		'apps.apple.com'  => 'Preuzmite aplikaciju na App Store',
		'itunes.apple.com' => 'Preuzmite aplikaciju na App Store',
	);

	public static function init() {
		add_filter( 'wp_get_attachment_image_attributes', array( __CLASS__, 'product_image_alt' ), 20, 2 );
		add_filter( 'urus_get_attachment_image_attributes', array( __CLASS__, 'product_image_alt' ), 20, 2 );
		// The mini cart is replaced by AJAX fragments, which do not pass through the page output buffer.
		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'process_fragments' ), 999 );
	}

	public static function process_fragments( $fragments ) {
		if ( is_array( $fragments ) ) {
			foreach ( $fragments as $selector => $html ) {
				if ( is_string( $html ) ) {
					$fragments[ $selector ] = self::process( $html );
				}
			}
		}
		return $fragments;
	}

	/**
	 * Product images without an alt text get the product name.
	 */
	public static function product_image_alt( $attr, $attachment ) {
		if ( ! empty( $attr['alt'] ) || is_admin() ) {
			return $attr;
		}
		global $product;
		// Featured image of the current post in a loop (blog list, single post): use the post title.
		if ( $attachment instanceof WP_Post && in_the_loop() && (int) get_post_thumbnail_id() === (int) $attachment->ID ) {
			$attr['alt'] = wp_strip_all_tags( get_the_title() );
			return $attr;
		}
		if ( $product instanceof WC_Product ) {
			$attr['alt'] = wp_strip_all_tags( $product->get_name() );
		} elseif ( $attachment instanceof WP_Post && $attachment->post_parent && 'product' === get_post_type( $attachment->post_parent ) ) {
			$attr['alt'] = wp_strip_all_tags( get_the_title( $attachment->post_parent ) );
		}
		return $attr;
	}

	public static function process( $html ) {
		// wp_kses (used by Perfect WooCommerce Brands) strips "data:" from the theme's lazy SVG
		// placeholder, leaving a relative URL that the browser requests and gets a 404 for.
		$html = str_replace( 'src="image/svg+xml;', 'src="data:image/svg+xml;', $html );
		$html = self::fix_mobile_menu_tabs( $html );
		$html = self::label_icon_links( $html );
		$html = self::label_image_links( $html );
		$html = self::alt_for_badge_images( $html );
		$html = self::main_landmark( $html );
		$html = self::crawlable_void_links( $html );
		$html = self::label_void_links( $html );
		$html = self::widget_heading_levels( $html );
		return $html;
	}

	/**
	 * JS button links whose text is hidden with CSS (e.g. the desktop wishlist icon) get that
	 * text as aria-label, so they keep an accessible name.
	 */
	private static function label_void_links( $html ) {
		return preg_replace_callback(
			'#<a\b((?![^>]*aria-label=)[^>]*data-barbers-void="1"[^>]*)>(.*?)</a>#is',
			static function ( $m ) {
				$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $m[2] ) ) );
				return '' === $text ? $m[0] : '<a' . $m[1] . ' aria-label="' . esc_attr( $text ) . '">' . $m[2] . '</a>';
			},
			$html
		);
	}

	/**
	 * Sidebar widget titles are <h6> right after the page <h1>; exposing them as level 2
	 * headings keeps a logical heading outline without touching the markup or styles.
	 */
	private static function widget_heading_levels( $html ) {
		return preg_replace( '#<h6\b(?![^>]*\baria-level=)([^>]*\bclass="[^"]*\bwidgettitle\b)#i', '<h6 role="heading" aria-level="2"$1', $html );
	}

	/**
	 * <ul role="tablist"> in the mobile menu: the <li> wrappers must not expose a list item role.
	 */
	private static function fix_mobile_menu_tabs( $html ) {
		return preg_replace_callback(
			'#(<ul\b[^>]*role="tablist"[^>]*>)(.*?)(</ul>)#is',
			static function ( $m ) {
				$items = preg_replace( '#<li\b(?![^>]*\brole=)#i', '<li role="presentation"', $m[2] );
				return $m[1] . $items . $m[3];
			},
			$html
		);
	}

	private static function label_icon_links( $html ) {
		// "Load more" link of the theme's infinite product loading (its text comes from CSS).
		$html = preg_replace( '#(<div class="infload-link">\s*<a\b)(?![^>]*aria-label)#i', '$1 aria-label="Učitaj još proizvoda"', $html );
		return preg_replace_callback(
			'#<a\b([^>]*)>#i',
			static function ( $m ) {
				if ( false !== stripos( $m[1], 'aria-label=' ) ) {
					return $m[0];
				}
				foreach ( self::ICON_LINK_LABELS as $class => $label ) {
					if ( preg_match( '#\bclass="[^"]*\b' . preg_quote( $class, '#' ) . '\b#', $m[1] ) ) {
						return '<a' . $m[1] . ' aria-label="' . esc_attr( $label ) . '">';
					}
				}
				return $m[0];
			},
			$html
		);
	}

	/**
	 * Links whose only content is an image with an empty alt get the alt of a sibling image
	 * or, as a fallback, a name derived from the link target.
	 */
	private static function label_image_links( $html ) {
		// Content made only of images / icon elements; the closing "</a" may lack its ">" (theme footer typo).
		return preg_replace_callback(
			'#<a\b([^>]*\bhref="([^"]*)"[^>]*)>((?:\s*<(?:img|span|i)\b[^>]*>(?:\s*</(?:span|i)>)?)+\s*)(?=</a)#i',
			static function ( $m ) {
				if ( false !== stripos( $m[1], 'aria-label=' ) || preg_match( '#\balt="[^"]+"#', $m[3] ) || '' !== trim( wp_strip_all_tags( $m[3] ) ) ) {
					return $m[0];
				}
				$label = '';
				foreach ( self::LINK_TARGET_LABELS as $needle => $text ) {
					if ( false !== stripos( $m[2], $needle ) ) {
						$label = $text;
						break;
					}
				}
				if ( '' === $label ) {
					$slug = basename( trim( (string) wp_parse_url( $m[2], PHP_URL_PATH ), '/' ) );
					if ( '' === $slug ) {
						return $m[0];
					}
					$label = ucfirst( str_replace( array( '-', '_' ), ' ', urldecode( $slug ) ) );
				}
				return '<a' . $m[1] . ' aria-label="' . esc_attr( $label ) . '">' . $m[3];
			},
			$html
		);
	}

	/**
	 * Images without any alt attribute (e.g. app store badges in the footer) get one from the file name.
	 */
	private static function alt_for_badge_images( $html ) {
		return preg_replace_callback(
			'#<img\b(?![^>]*\balt=)([^>]*\bsrc="([^"]+)"[^>]*)>#i',
			static function ( $m ) {
				$name = pathinfo( (string) wp_parse_url( $m[2], PHP_URL_PATH ), PATHINFO_FILENAME );
				$name = trim( preg_replace( '/[-_]+|\blogo\b|\d+x\d+/i', ' ', $name ) );
				return '<img alt="' . esc_attr( $name ) . '"' . $m[1] . '>';
			},
			$html
		);
	}

	private static function main_landmark( $html ) {
		if ( preg_match( '#<main\b|role="main"#i', $html ) ) {
			return $html;
		}
		return preg_replace( '#<div\b([^>]*\bclass="(?:fullwidth-template|main-container|mobile-shop-content)\b[^"]*")#', '<div role="main"$1', $html, 1 );
	}

	/**
	 * Links used as JS buttons (href="javascript:void(0);") are not crawlable. They become
	 * href="#" and a capture-phase listener keeps them from navigating, exactly like before.
	 */
	private static function crawlable_void_links( $html ) {
		$count = 0;
		$html  = preg_replace( '#href="javascript:void\(0\);?"#i', 'href="#" data-barbers-void="1"', $html, -1, $count );
		if ( $count ) {
			$js   = "document.addEventListener('click',function(e){var a=e.target.closest&&e.target.closest('a[data-barbers-void]');if(a)e.preventDefault();},true);";
			$html = preg_replace( '#</head>#i', '<script data-no-optimize="1">' . $js . '</script></head>', $html, 1 );
		}
		return $html;
	}
}
