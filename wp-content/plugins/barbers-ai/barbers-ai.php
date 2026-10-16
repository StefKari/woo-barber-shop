<?php
/**
 * Plugin Name: Barbers AI Discovery
 * Description: Izlaže prodavnicu AI agentima: /.well-known/ai-catalog.json (ARD 1.0), agent skill opis prodavnice i WebMCP anotacije formi. llms.txt generiše Yoast SEO.
 * Version: 1.0.0
 * Requires PHP: 8.1
 * Author: Barbers
 * Text Domain: barbers-ai
 */

defined( 'ABSPATH' ) || exit;

final class Barbers_AI_Discovery {

	const CATALOG_PATH = '/.well-known/ai-catalog.json';
	const SKILL_PATH   = '/.well-known/agent-skills/barbers-shop/SKILL.md';

	/**
	 * WebMCP annotations for known forms: CSS-like matcher => [toolname, tooldescription].
	 * Matchers are checked against the opening <form> tag.
	 */
	const FORM_TOOLS = array(
		'id="familab-search-mobile"' => array( 'search_products', 'Pretraži proizvode prodavnice Barbers po nazivu, opisu ili šifri (SKU). Vraća stranicu sa rezultatima pretrage.' ),
		'class="cart"'               => array( 'add_to_cart', 'Dodaj prikazani proizvod u korpu u izabranoj količini (i varijanti, ako postoji).' ),
		'wpcf7-form'                 => array( 'send_contact_message', 'Pošalji poruku prodavnici Barbers putem kontakt forme (ime, email adresa i poruka).' ),
		'woocommerce-form-login'     => array( 'customer_login', 'Prijava kupca na korisnički nalog prodavnice Barbers.' ),
		'woocommerce-form-register'  => array( 'customer_register', 'Registracija novog korisničkog naloga u prodavnici Barbers.' ),
		'woocommerce-ordering'       => array( 'sort_products', 'Promeni redosled prikaza proizvoda u listi (popularnost, ocena, najnovije, cena).' ),
		'woocommerce-cart-form'      => array( 'update_cart', 'Izmeni količine proizvoda u korpi ili ukloni proizvode iz korpe.' ),
		'checkout woocommerce-checkout' => array( 'place_order', 'Unesi podatke za dostavu i naplatu i završi porudžbinu (plaćanje pouzećem).' ),
		'woocommerce-ResetPassword'  => array( 'reset_password', 'Zatraži link za resetovanje lozinke korisničkog naloga.' ),
		'method="get" action="'      => array( 'filter_products', 'Filtriraj listu proizvoda po izabranim kriterijumima.' ),
	);

	public static function init() {
		// Static copies are served directly by the web server; the dynamic route is the fallback.
		add_action( 'parse_request', array( __CLASS__, 'maybe_serve_document' ), 0 );
		add_action( 'barbers_ai_refresh_static', array( __CLASS__, 'write_static_files' ) );
		add_action( 'created_product_cat', array( __CLASS__, 'schedule_refresh' ) );
		add_action( 'edited_product_cat', array( __CLASS__, 'schedule_refresh' ) );
		add_action( 'delete_product_cat', array( __CLASS__, 'schedule_refresh' ) );
		add_action( 'update_option_blogname', array( __CLASS__, 'schedule_refresh' ) );
		add_action( 'update_option_home', array( __CLASS__, 'schedule_refresh' ) );
		if ( ! wp_next_scheduled( 'barbers_ai_refresh_static' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'barbers_ai_refresh_static' );
		}
		add_action( 'wp_head', array( __CLASS__, 'print_head_link' ), 2 );
		add_action( 'send_headers', array( __CLASS__, 'send_link_header' ) );
		add_action( 'template_redirect', array( __CLASS__, 'start_form_annotation' ), 0 );
	}

	private static function request_path() {
		$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
		$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		if ( '' !== $home && '/' !== $home && str_starts_with( $path, untrailingslashit( $home ) ) ) {
			$path = substr( $path, strlen( untrailingslashit( $home ) ) );
		}
		return $path;
	}

	public static function maybe_serve_document() {
		$path = self::request_path();
		if ( self::CATALOG_PATH === $path ) {
			self::send( wp_json_encode( self::catalog(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), 'application/ai-catalog+json; charset=utf-8' );
		}
		if ( self::SKILL_PATH === $path ) {
			self::send( self::skill_markdown(), 'text/markdown; charset=utf-8' );
		}
	}

	public static function schedule_refresh() {
		if ( ! wp_next_scheduled( 'barbers_ai_refresh_static' ) || wp_next_scheduled( 'barbers_ai_refresh_static' ) > time() + MINUTE_IN_SECONDS ) {
			wp_schedule_single_event( time() + 10, 'barbers_ai_refresh_static' );
		}
	}

	/**
	 * Writes the discovery documents as static files under the web root (/.well-known/...).
	 */
	public static function write_static_files() {
		$files = array(
			self::CATALOG_PATH => wp_json_encode( self::catalog(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
			self::SKILL_PATH   => self::skill_markdown(),
		);
		foreach ( $files as $path => $body ) {
			$file = untrailingslashit( ABSPATH ) . $path;
			if ( ! wp_mkdir_p( dirname( $file ) ) || false === file_put_contents( $file, $body ) ) {
				return false;
			}
		}
		return true;
	}

	public static function activate() {
		self::write_static_files();
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( 'barbers_ai_refresh_static' );
		foreach ( array( self::CATALOG_PATH, self::SKILL_PATH ) as $path ) {
			$file = untrailingslashit( ABSPATH ) . $path;
			if ( file_exists( $file ) ) {
				wp_delete_file( $file );
			}
		}
	}

	private static function send( $body, $content_type ) {
		status_header( 200 );
		header( 'Content-Type: ' . $content_type );
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Cache-Control: public, max-age=3600' );
		header( 'X-Robots-Tag: noindex' );
		echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON/Markdown document.
		exit;
	}

	private static function site_name() {
		return wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	}

	/**
	 * ai-catalog.json following the Agentic Resource Discovery (ARD) 1.0 spec.
	 */
	public static function catalog() {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		$host = preg_replace( '/^www\./', '', (string) $host );
		$logo = get_site_icon_url( 512 );

		$host_info = array(
			'displayName'      => 'Barbers – ' . self::site_name(),
			'identifier'       => 'did:web:' . $host,
			'documentationUrl' => home_url( '/llms.txt' ),
		);
		if ( $logo ) {
			$host_info['logoUrl'] = $logo;
		}

		$entry = array(
			'identifier'            => 'urn:air:' . $host . ':shop:barbers-shop',
			'displayName'           => 'Barbers online prodavnica',
			'type'                  => 'text/markdown; profile="urn:air:agent-skills"',
			'url'                   => home_url( self::SKILL_PATH ),
			'description'           => 'Uputstvo za AI agente: pretraga i pregled proizvoda za negu brade, kose i brijanje, cene u RSD, dostava po Srbiji i poručivanje u prodavnici Barbers.',
			'tags'                  => array( 'ecommerce', 'woocommerce', 'kozmetika za bradu', 'kozmetika za kosu', 'brijanje', 'Srbija' ),
			'capabilities'          => array( 'ProductSearch', 'ProductCatalog', 'ProductCategories', 'ShippingInfo', 'Ordering' ),
			'representativeQueries' => array(
				'Gde mogu da kupim ulje za bradu u Srbiji?',
				'Koje pomade za kosu Reuzel su dostupne i koliko koštaju?',
				'Koliko košta dostava i od kog iznosa je besplatna?',
				'Preporuči kozmetiku za negu brade',
			),
			'version'               => '1.0.0',
			'metadata'              => array(
				'currency' => get_option( 'woocommerce_currency', 'RSD' ),
				'language' => get_bloginfo( 'language' ),
				'storeApi' => rest_url( 'wc/store/v1/' ),
				'llmsTxt'  => home_url( '/llms.txt' ),
			),
		);

		return array(
			'specVersion' => '1.0',
			'host'        => $host_info,
			'entries'     => array( $entry ),
		);
	}

	/**
	 * Agent skill: how to use the shop programmatically and what the store policies are.
	 */
	public static function skill_markdown() {
		$store_api = rest_url( 'wc/store/v1/' );
		$shop      = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		$cart      = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
		$checkout  = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
		$contact   = home_url( '/kontakt/' );
		$email     = get_option( 'woocommerce_email_from_address', get_option( 'admin_email' ) );

		$categories = array();
		$terms      = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => true,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$link = get_term_link( $term );
				if ( ! is_wp_error( $link ) ) {
					$categories[] = '- [' . $term->name . '](' . $link . ')';
				}
			}
		}

		$md  = "---\n";
		$md .= "name: barbers-shop\n";
		$md .= 'description: Pretraga proizvoda, cene, dostava i poručivanje u online prodavnici Barbers (kozmetika za bradu, kosu i brijanje, Srbija).' . "\n";
		$md .= "---\n\n";
		$md .= '# ' . self::site_name() . " – vodič za AI agente\n\n";
		$md .= "Barbers je online prodavnica kozmetike i opreme za negu brade, brkova i kose i za brijanje. Cene su u dinarima (RSD), isporuka je na teritoriji Srbije.\n\n";
		$md .= "## Pretraga i pregled proizvoda\n\n";
		$md .= "Koristi javni WooCommerce Store API (samo čitanje, bez autentifikacije):\n\n";
		$md .= '- Pretraga: `GET ' . $store_api . "products?search=ulje%20za%20bradu&per_page=20`\n";
		$md .= '- Proizvod po ID-u: `GET ' . $store_api . "products/{id}`\n";
		$md .= '- Kategorije: `GET ' . $store_api . "products/categories`\n";
		$md .= '- Proizvodi iz kategorije: `GET ' . $store_api . "products?category={slug}`\n\n";
		$md .= "Cene u odgovoru su u najmanjoj jedinici valute (`prices.currency_minor_unit`). Polje `permalink` vodi na stranicu proizvoda.\n\n";
		if ( $categories ) {
			$md .= "## Glavne kategorije\n\n" . implode( "\n", $categories ) . "\n\n";
		}
		$md .= "## Poručivanje\n\n";
		$md .= '1. Otvori stranicu proizvoda (iz [prodavnice](' . $shop . ") ili iz pretrage) i dodaj ga u korpu.\n";
		$md .= '2. Pregledaj [korpu](' . $cart . ").\n";
		$md .= '3. Završi porudžbinu na [stranici za plaćanje](' . $checkout . ") – kupac unosi podatke za dostavu. Porudžbinu uvek potvrđuje kupac.\n\n";
		$md .= "## Dostava i plaćanje\n\n";
		$md .= "- Dostava na teritoriji Srbije: od 450 do 550 RSD, u zavisnosti od lokacije.\n";
		$md .= "- Za porudžbine iznad 5.000 RSD dostava je besplatna.\n";
		$md .= "- Plaćanje: pouzećem (gotovinom prilikom preuzimanja).\n\n";
		$md .= "## Kontakt\n\n";
		$md .= '- [Kontakt stranica](' . $contact . ")\n";
		if ( $email ) {
			$md .= '- Email: ' . $email . "\n";
		}
		$md .= "\n## Dodatno\n\n- [llms.txt](" . home_url( '/llms.txt' ) . ") – pregled sadržaja sajta za jezičke modele.\n";

		return $md;
	}

	public static function print_head_link() {
		echo '<link rel="ai-catalog" type="application/ai-catalog+json" href="' . esc_url( home_url( self::CATALOG_PATH ) ) . '" />' . "\n";
	}

	public static function send_link_header() {
		if ( ! headers_sent() ) {
			header( 'Link: <' . esc_url_raw( home_url( self::CATALOG_PATH ) ) . '>; rel="ai-catalog"; type="application/ai-catalog+json"', false );
		}
	}

	public static function start_form_annotation() {
		if ( is_admin() || wp_doing_ajax() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		ob_start( array( __CLASS__, 'annotate_forms' ) );
	}

	/**
	 * Adds declarative WebMCP attributes (toolname/tooldescription) to known forms.
	 */
	public static function annotate_forms( $html ) {
		if ( ! is_string( $html ) || false === stripos( $html, '<form' ) ) {
			return $html;
		}
		$used = array();
		return preg_replace_callback(
			'/<form\b[^>]*>/i',
			static function ( $m ) use ( &$used ) {
				$tag = $m[0];
				if ( false !== stripos( $tag, 'toolname=' ) ) {
					return $tag;
				}
				foreach ( self::FORM_TOOLS as $needle => $tool ) {
					if ( false === strpos( $tag, $needle ) ) {
						continue;
					}
					// Tool names must be unique per page.
					$name = $tool[0];
					$n    = 2;
					while ( isset( $used[ $name ] ) ) {
						$name = $tool[0] . '_' . $n++;
					}
					$used[ $name ] = true;
					return substr( $tag, 0, -1 ) . ' toolname="' . esc_attr( $name ) . '" tooldescription="' . esc_attr( $tool[1] ) . '">';
				}
				return $tag;
			},
			$html
		);
	}
}

Barbers_AI_Discovery::init();
register_activation_hook( __FILE__, array( 'Barbers_AI_Discovery', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Barbers_AI_Discovery', 'deactivate' ) );
