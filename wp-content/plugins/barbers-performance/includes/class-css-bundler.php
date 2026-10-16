<?php
/**
 * Re-cuts runs of consecutive local stylesheets into evenly sized chunks (~40 KB).
 *
 * Only stylesheets that directly follow each other (nothing but whitespace in between) and share
 * the same media are combined, so the order of every rule relative to inline <style> blocks and
 * remote stylesheets is unchanged. Large files are split and small ones merged; the loader then
 * switches the chunks on one per frame, which keeps every style recalculation task short.
 */

defined( 'ABSPATH' ) || exit;

final class Barbers_Perf_CSS_Bundler {

	const DIR = 'cache/barbers-css';

	/**
	 * Target size of one CSS chunk in bytes.
	 */
	const CHUNK = 40000;

	public static function process( $html ) {
		$html = self::drop_duplicate_stylesheets( $html );
		return preg_replace_callback(
			'#(?:<link\b[^>]*\brel=[\'"]stylesheet[\'"][^>]*>\s*)+#i',
			array( __CLASS__, 'bundle_run' ),
			$html
		);
	}

	/**
	 * The same file enqueued twice (urus-child style.css under two handles) is removed everywhere
	 * but at its last position: the last copy re-applies every rule anyway, so the cascade is unchanged.
	 */
	private static function drop_duplicate_stylesheets( $html ) {
		if ( ! preg_match_all( '#<link\b[^>]*\brel=[\'"]stylesheet[\'"][^>]*>#i', $html, $m ) ) {
			return $html;
		}
		$last = array();
		foreach ( $m[0] as $i => $tag ) {
			$attrs = Barbers_Perf_HTML_Optimizer::parse_attrs( $tag );
			$path  = (string) wp_parse_url( html_entity_decode( $attrs['href'] ?? '' ), PHP_URL_PATH );
			if ( '' !== $path ) {
				$last[ $path . '|' . ( $attrs['media'] ?? 'all' ) ] = $i;
			}
		}
		foreach ( $m[0] as $i => $tag ) {
			$attrs = Barbers_Perf_HTML_Optimizer::parse_attrs( $tag );
			$path  = (string) wp_parse_url( html_entity_decode( $attrs['href'] ?? '' ), PHP_URL_PATH );
			$key   = $path . '|' . ( $attrs['media'] ?? 'all' );
			if ( '' !== $path && $last[ $key ] !== $i ) {
				$pos = strpos( $html, $tag );
				if ( false !== $pos ) {
					$html = substr_replace( $html, '', $pos, strlen( $tag ) );
				}
			}
		}
		return $html;
	}

	private static function bundle_run( $m ) {
		preg_match_all( '#<link\b[^>]*>#i', $m[0], $tags );
		$out   = '';
		$group = array();
		$media = null;
		foreach ( $tags[0] as $tag ) {
			$attrs = Barbers_Perf_HTML_Optimizer::parse_attrs( $tag );
			$file  = self::local_file( $attrs['href'] ?? '' );
			$tmedia = $attrs['media'] ?? 'all';
			if ( ! $file || isset( $attrs['data-no-optimize'] ) || self::has_import( $file ) ) {
				$out  .= self::flush( $group, $media );
				$group = array();
				$out  .= $tag;
				continue;
			}
			if ( $group && $tmedia !== $media ) {
				$out  .= self::flush( $group, $media );
				$group = array();
			}
			$media   = $tmedia;
			$group[] = array( 'tag' => $tag, 'file' => $file, 'href' => $attrs['href'] );
		}
		$out .= self::flush( $group, $media );
		return $out . "\n";
	}

	/**
	 * Returns the bundle <link> for a group, or the original tags when bundling is not possible.
	 */
	private static function flush( $group, $media ) {
		if ( ! $group ) {
			return '';
		}
		if ( 1 === count( $group ) && filesize( $group[0]['file'] ) <= self::CHUNK ) {
			return $group[0]['tag'];
		}
		$urls = self::chunk_urls( $group );
		if ( ! $urls ) {
			return implode( '', array_column( $group, 'tag' ) );
		}
		$out = '';
		foreach ( $urls as $i => $url ) {
			$out .= '<link rel="stylesheet" id="barbers-css-' . esc_attr( substr( md5( $url ), 0, 8 ) ) . '" href="' . esc_url( $url ) . '" media="' . esc_attr( $media ) . '" />';
		}
		return $out;
	}

	/**
	 * Writes the group's CSS as consecutive chunks of at most ~CHUNK bytes and returns their URLs.
	 * Chunks are cut only between top-level rules, so every chunk is valid on its own and the
	 * rule order is unchanged. Small chunks keep each style recalculation short when they are
	 * switched on one per frame.
	 */
	private static function chunk_urls( $group ) {
		$key = '';
		foreach ( $group as $item ) {
			$key .= $item['file'] . ':' . filemtime( $item['file'] ) . '|';
		}
		// A new set of purged stylesheets must produce new bundles.
		$purged_index = BARBERS_PERF_DIR . '/purged/index.json';
		$key         .= is_file( $purged_index ) ? 'purged:' . filemtime( $purged_index ) : '';
		$hash     = substr( md5( $key . self::CHUNK ), 0, 16 );
		$dir      = WP_CONTENT_DIR . '/' . self::DIR;
		$manifest = $dir . '/' . $hash . '.json';
		if ( file_exists( $manifest ) ) {
			$names = json_decode( (string) file_get_contents( $manifest ), true );
		} else {
			if ( ! wp_mkdir_p( $dir ) ) {
				return false;
			}
			$css = '';
			foreach ( $group as $item ) {
				$css .= '/* ' . basename( $item['file'] ) . " */\n" . self::rebase( self::source_css( $item['file'] ), $item['href'] ) . "\n";
			}
			$names = array();
			foreach ( self::split_css( $css, self::CHUNK ) as $i => $chunk ) {
				$name = $hash . '-' . $i . '.css';
				if ( false === file_put_contents( $dir . '/' . $name, $chunk ) ) {
					return false;
				}
				$names[] = $name;
			}
			$tmp = $manifest . '.' . uniqid( '', true ) . '.tmp';
			if ( false === file_put_contents( $tmp, wp_json_encode( $names ) ) || ! rename( $tmp, $manifest ) ) {
				return false;
			}
		}
		if ( ! is_array( $names ) || ! $names ) {
			return false;
		}
		return array_map(
			static function ( $name ) {
				return content_url( self::DIR . '/' . $name );
			},
			$names
		);
	}

	/**
	 * Splits CSS into pieces of roughly $max bytes at top-level rule boundaries (outside any
	 * block, string or comment).
	 */
	private static function split_css( $css, $max ) {
		$chunks = array();
		$start  = 0;
		$depth  = 0;
		$len    = strlen( $css );
		for ( $i = 0; $i < $len; $i++ ) {
			$c = $css[ $i ];
			if ( '/' === $c && '*' === ( $css[ $i + 1 ] ?? '' ) ) {
				$end = strpos( $css, '*/', $i + 2 );
				$i   = false === $end ? $len : $end + 1;
				continue;
			}
			if ( '"' === $c || "'" === $c ) {
				for ( $i++; $i < $len && $css[ $i ] !== $c; $i++ ) {
					if ( '\\' === $css[ $i ] ) {
						$i++;
					}
				}
				continue;
			}
			if ( '{' === $c ) {
				$depth++;
			} elseif ( '}' === $c ) {
				$depth = max( 0, $depth - 1 );
				if ( 0 === $depth && $i + 1 - $start >= $max ) {
					$chunks[] = substr( $css, $start, $i + 1 - $start );
					$start    = $i + 1;
				}
			}
		}
		if ( $start < $len && '' !== trim( substr( $css, $start ) ) ) {
			$chunks[] = substr( $css, $start );
		}
		return $chunks;
	}

	/**
	 * Contents of a stylesheet, or of its purged copy (rules the site never uses removed, generated
	 * by tools/purge/build.mjs) while the original file is still exactly the one that was purged.
	 * After a theme/plugin update the md5 no longer matches and the full original is used again.
	 */
	private static function source_css( $file ) {
		static $index = null;
		if ( null === $index ) {
			$path  = BARBERS_PERF_DIR . '/purged/index.json';
			$index = is_file( $path ) ? (array) json_decode( (string) file_get_contents( $path ), true ) : array();
		}
		$css = (string) file_get_contents( $file );
		$rel = ltrim( substr( wp_normalize_path( $file ), strlen( wp_normalize_path( untrailingslashit( ABSPATH ) ) ) ), '/' );
		$hit = $index[ $rel ] ?? null;
		if ( $hit && md5( $css ) === $hit['md5'] && is_readable( BARBERS_PERF_DIR . '/purged/' . $hit['file'] ) ) {
			return (string) file_get_contents( BARBERS_PERF_DIR . '/purged/' . $hit['file'] );
		}
		return $css;
	}

	/**
	 * Makes relative url() references absolute so they keep working from the bundle location.
	 */
	private static function rebase( $css, $href ) {
		$css  = preg_replace( '#@charset\s+[\'"][^\'"]*[\'"]\s*;#i', '', $css );
		$base = set_url_scheme( preg_replace( '#[?\#].*$#', '', $href ) );
		return preg_replace_callback(
			'#url\(\s*([\'"]?)([^\'")]+)\1\s*\)#i',
			static function ( $m ) use ( $base ) {
				$u = trim( $m[2] );
				if ( preg_match( '#^(data:|https?:|//|\#|/)#i', $u ) ) {
					return $m[0];
				}
				return 'url(' . $m[1] . WP_Http::make_absolute_url( $u, $base ) . $m[1] . ')';
			},
			$css
		);
	}

	private static function has_import( $file ) {
		$head = (string) file_get_contents( $file, false, null, 0, 4096 );
		return false !== stripos( $head, '@import' );
	}

	private static function local_file( $href ) {
		if ( '' === $href ) {
			return false;
		}
		$href = html_entity_decode( $href );
		$host = wp_parse_url( set_url_scheme( $href ), PHP_URL_HOST );
		if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
			return false;
		}
		$path = (string) wp_parse_url( $href, PHP_URL_PATH );
		if ( ! preg_match( '#\.css$#i', $path ) ) {
			return false;
		}
		$home_path = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
		$file      = untrailingslashit( ABSPATH ) . '/' . ltrim( substr( $path, strlen( $home_path ) ), '/' );
		return is_readable( $file ) ? $file : false;
	}
}
