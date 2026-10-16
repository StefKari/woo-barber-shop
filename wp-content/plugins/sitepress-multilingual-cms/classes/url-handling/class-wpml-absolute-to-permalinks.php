<?php

use WPML\Blocks\AttributeUrls;
use WPML\Utils\AutoAdjustIds;

class WPML_Absolute_To_Permalinks {

	private $taxonomies_query;
	private $lang;

	private $attribute_replacements = [];

	private $sitepress;

	private $auto_adjust_ids;

	private $strip_gone_targets;

	private $strip_gone_targets_in_theme_render;

	private $met_gone_target = false;

	public function __construct( SitePress $sitepress, ?AutoAdjustIds $auto_adjust_ids = null, $strip_gone_targets = false, $strip_gone_targets_in_theme_render = false ) {
		$this->sitepress                          = $sitepress;
		$this->auto_adjust_ids                    = $auto_adjust_ids ?: new AutoAdjustIds( $sitepress );
		$this->strip_gone_targets                 = (bool) $strip_gone_targets;
		$this->strip_gone_targets_in_theme_render = (bool) $strip_gone_targets_in_theme_render;
	}

	public function convert_text( $text ) {

		$this->lang = $this->sitepress->get_current_language();

		$active_langs_reg_ex = implode(
			'|',
			array_map(
				function ( $code ) {
					return preg_quote( $code, '@' );
				},
				$this->get_url_language_codes()
			)
		);

		if ( ! $this->taxonomies_query ) {
			$this->taxonomies_query = new WPML_WP_Taxonomy_Query( $this->sitepress->get_wp_api() );
		}

		$home    = rtrim( $this->sitepress->get_wp_api()->get_option( 'home' ), '/' );
		$parts   = parse_url( $home );
		$port    = isset( $parts['port'] ) ? ':' . $parts['port'] : '';
		$abshome = $parts['scheme'] . '://' . $parts['host'] . $port;
		$path    = isset( $parts['path'] ) ? ltrim( $parts['path'], '/' ) : '';
		$tx_qvs  = join(
			'|',
			array_map(
				function ( $query_var ) {
					return preg_quote( $query_var, '@' );
				},
				$this->taxonomies_query->get_query_vars()
			)
		);
        $reg_ex  = '@<a([^>]+)?href="((' . preg_quote( $abshome, '@' ) . ')?/' . preg_quote( $path, '@' ) . '/?(' . $active_langs_reg_ex . ')?/?\?(p|page_id|cat_ID|' . $tx_qvs . ')=([^"&#]+))(#?[^"]*)"([^>]+)?>@iu';

		$this->attribute_replacements = [];
		$this->met_gone_target        = false;

		$text = preg_replace_callback( $reg_ex, [ $this, 'show_permalinks_cb' ], $text );

		$text = AttributeUrls::replaceInDelimiters( $text, $this->attribute_replacements );

		$this->attribute_replacements = [];

		return $text;
	}

	public function for_display() {
		$display                     = clone $this;
		$display->strip_gone_targets = true;

		return $display;
	}

	public function met_gone_target() {
		return $this->met_gone_target;
	}

	private function get_url_language_codes() {
		$codes = array_keys( $this->sitepress->get_active_languages() );
		if ( [] === $codes ) {
			return $codes;
		}

		$map   = apply_filters( 'wpml_language_codes_map', array_combine( $codes, $codes ) );
		$codes = is_array( $map )
			? array_values( array_unique( array_merge( $codes, array_map( 'strval', $map ) ) ) )
			: $codes;

		usort(
			$codes,
			function ( $a, $b ) {
				return strlen( $b ) - strlen( $a );
			}
		);

		return $codes;
	}

	function show_permalinks_cb( $matches ) {

		$parts = $this->get_found_parts( $matches );

		$url = $this->resolve_url( $parts );

		if ( $this->sitepress->get_wp_api()->is_wp_error( $url ) || empty( $url ) ) {
			$gone = $this->target_is_gone( $parts );

			$this->met_gone_target = $this->met_gone_target || $gone;

			if ( $gone && ( $this->strip_gone_targets || ( $this->strip_gone_targets_in_theme_render && $this->is_theme_render() ) ) ) {
				return '<a' . $parts->pre_href . $parts->trail . '>';
			}

			return $parts->whole;
		}

		$fragment = $this->get_fragment( $url, $parts );

		if ( 'widget_text' == $this->sitepress->get_wp_api()->current_filter() ) {
			$url = $this->sitepress->convert_url( $url );
		}

		$this->remember_block_attribute_replacement( $parts, $url . $fragment );

		return '<a' . $parts->pre_href . 'href="' . $url . $fragment . '"' . $parts->trail . '>';
	}

	private function is_theme_render() {
		if ( ! did_action( 'template_redirect' ) ) {
			return false;
		}

		$rest = function_exists( 'wp_is_serving_rest_request' )
			? wp_is_serving_rest_request()
			: ( defined( 'REST_REQUEST' ) && REST_REQUEST );

		return ! $rest && ! wp_doing_cron() && ! wpml_is_cli();
	}

	private function remember_block_attribute_replacement( $parts, $resolved ) {
		$sticky = $parts->url . $parts->fragment;

		$this->attribute_replacements[ $sticky ] = $resolved;

		$decoded = str_replace( [ '&#038;', '&amp;' ], '&', $sticky );

		if ( $decoded !== $sticky ) {
			$this->attribute_replacements[ $decoded ] = $resolved;
		}
	}

	private function target_is_gone( $parts ) {
		$tax = $this->taxonomies_query->find( $parts->content_type );

		if ( 'cat_ID' === $parts->content_type ) {
			$tax = 'category';
		}

		if ( $tax ) {
			$term = get_term( (int) $parts->id, $tax );

			return ! $term || is_wp_error( $term );
		}

		return ! $this->sitepress->get_wp_api()->get_post( (int) $parts->id );
	}

	private function get_found_parts( $matches ) {
		return (object) array(
			'whole'        => $matches[0],
			'pre_href'     => $matches[1],
			'url'          => $matches[2],
			'content_type' => $matches[5],
			'id'           => $matches[6],
			'fragment'     => $matches[7],
			'trail'        => isset( $matches[8] ) ? $matches[8] : '',
		);
	}

	private function get_url( $parts ) {
		$tax = $this->taxonomies_query->find( $parts->content_type );

		if ( $parts->content_type == 'cat_ID' ) {
			$url = $this->sitepress->get_wp_api()->get_category_link( $parts->id );
		} elseif ( $tax ) {
			$url = $this->sitepress->get_wp_api()->get_term_link( $parts->id, $tax );
		} else {
			$url = $this->sitepress->get_wp_api()->get_permalink( $parts->id );
		}

		return $url;
	}

	private function resolve_url( $parts ) {
		$blocked     = $this->blocked_target( $parts );
		$original_id = $blocked ? $this->get_original_element_id( $blocked ) : 0;

		if ( ! $original_id || $original_id === $blocked->ID ) {
			return $this->auto_adjust_ids->runWith(
				function () use ( $parts ) {
					return $this->get_url( $parts );
				}
			);
		}

		$fallback     = clone $parts;
		$fallback->id = $original_id;

		return $this->auto_adjust_ids->runWithout(
			function () use ( $fallback ) {
				return $this->get_url( $fallback );
			}
		);
	}

	private function blocked_target( $parts ) {
		if ( 'cat_ID' === $parts->content_type || $this->taxonomies_query->find( $parts->content_type ) ) {
			return null;
		}

		$link_id   = (int) $parts->id;
		$post_type = get_post_type( $link_id );
		if ( ! $post_type ) {
			return null;
		}

		$resolved_id = $this->sitepress->get_object_id( $link_id, $post_type, false, $this->lang );
		$resolved    = get_post( $resolved_id ? (int) $resolved_id : $link_id );

		return $resolved && ! $this->is_linkable( $resolved ) ? $resolved : null;
	}

	private function is_linkable( $post ) {
		$blocked = apply_filters(
			'wpml_link_target_not_convert_post_statuses',
			array( 'draft', 'auto-draft', 'pending', 'future', 'private', 'trash' ),
			$post
		);

		return ! in_array( get_post_status( $post ), (array) $blocked, true );
	}

	private function get_original_element_id( $post ) {
		$type = 'post_' . $post->post_type;
		$trid = $this->sitepress->get_element_trid( $post->ID, $type );

		foreach ( $trid ? (array) $this->sitepress->get_element_translations( $trid, $type, false, true ) : array() as $t ) {
			if ( empty( $t->source_language_code ) ) {
				return (int) $t->element_id;
			}
		}

		return 0;
	}

	private function get_fragment( $url, $parts ) {
		$fragment = $parts->fragment;
		$fragment = $this->remove_query_in_wrong_lang( $fragment );
		if ( is_string( $fragment ) && $fragment != '' ) {
			$fragment = str_replace( '&#038;', '&', $fragment );
			$fragment = str_replace( '&amp;', '&', $fragment );
			if ( $fragment[0] == '&' ) {
				if ( strpos( $fragment, '?' ) === false && strpos( $url, '?' ) === false ) {
					$fragment[0] = '?';
				}
			}

			if ( strpos( $url, '?' ) ) {
				$fragment = $this->check_for_duplicate_lang_query( $fragment, $url );
			}
		}

		return $fragment;
	}

	private function remove_query_in_wrong_lang( $fragment ) {
		if ( is_string( $fragment ) && $fragment != '' ) {
			$fragment = str_replace( '&#038;', '&', $fragment );
			$fragment = str_replace( '&amp;', '&', $fragment );
			$start    = $fragment[0];
			parse_str( substr( $fragment, 1 ), $fragment_query );
			if ( isset( $fragment_query['lang'] ) ) {
				if ( $fragment_query['lang'] != $this->lang ) {
					unset( $fragment_query['lang'] );

					$fragment = build_query( $fragment_query );
					if ( strlen( $fragment ) ) {
						$fragment = $start . $fragment;
					}
				}
			}
		}
		return $fragment;
	}

	private function check_for_duplicate_lang_query( $fragment, $url ) {
		$url_parts = explode( '?', $url );
		parse_str( $url_parts[1], $url_query );

		if ( isset( $url_query['lang'] ) ) {
			parse_str( substr( $fragment, 1 ), $fragment_query );
			if ( isset( $fragment_query['lang'] ) ) {
				unset( $fragment_query['lang'] );
				$fragment = build_query( $fragment_query );
				if ( strlen( $fragment ) ) {
					$fragment = '&' . $fragment;
				}
			}
		}
		return $fragment;
	}
}
