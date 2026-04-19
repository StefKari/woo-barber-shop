<?php
defined( 'ABSPATH' ) || exit;

/**
 * Frontend: vizuelni filter po atributu iznad liste proizvoda.
 * Ubacuje se hook-om, bez override-a template fajlova.
 */
class Barbers_Attribute_Filter_Frontend {

    private string $taxonomy;
    private string $meta_key = 'barbers_attribute_image_id';

    public function __construct( string $attribute_slug ) {
        $this->taxonomy = wc_attribute_taxonomy_name( $attribute_slug );
        $this->hooks();
    }

    private function hooks(): void {
        // Desktop hook
        add_action( 'woocommerce_before_shop_loop',        [ $this, 'render_filter_desktop' ], 5 );
        add_action( 'woocommerce_before_subcategory_list', [ $this, 'render_filter_desktop' ], 5 );
        // Urus mobilni template — hook se okida i na desktopu, pa proveravamo
        add_action( 'urus_shop_control_top',               [ $this, 'render_filter_mobile' ], 5 );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function render_filter_desktop(): void {
        if ( class_exists( 'Urus_Helper' ) && Urus_Helper::is_mobile_template() ) {
            return;
        }
        $this->render_filter();
    }

    public function render_filter_mobile(): void {
        if ( ! class_exists( 'Urus_Helper' ) || ! Urus_Helper::is_mobile_template() ) {
            return;
        }
        $this->render_filter();
    }

    public function enqueue_assets(): void {
        if ( ! is_shop() && ! is_product_taxonomy() && ! is_product_category() ) {
            return;
        }
        wp_enqueue_style(
            'barbers-attribute-filter',
            get_stylesheet_directory_uri() . '/assets/css/attribute-filter.css',
            [],
            '1.0.0'
        );
        wp_enqueue_script(
            'barbers-attribute-filter',
            get_stylesheet_directory_uri() . '/assets/js/attribute-filter.js',
            [ 'jquery' ],
            '1.0.0',
            true
        );
    }

    public function render_filter(): void {
        if ( ! is_shop() && ! is_product_taxonomy() && ! is_product_category() ) {
            return;
        }

        $terms = $this->get_terms();
        if ( empty( $terms ) ) {
            return;
        }

        $active_slug    = $this->get_active_slug();
        $attribute_name = str_replace( 'pa_', '', $this->taxonomy );
        $base_url       = $this->get_base_url();

        echo '<div class="barbers-attr-filter" data-attribute="' . esc_attr( $attribute_name ) . '">';
        echo '<ul class="barbers-attr-filter__list">';

        foreach ( $terms as $term ) {
            $is_active = ( $active_slug === $term->slug );
            $url       = $is_active
                ? remove_query_arg( 'filter_' . $attribute_name, $base_url )
                : add_query_arg( 'filter_' . $attribute_name, $term->slug, $base_url );

            $image_id  = absint( get_term_meta( $term->term_id, $this->meta_key, true ) );
            $image_url = $image_id ? wp_get_attachment_image_url( $image_id, [ 100, 100 ] ) : '';

            echo '<li class="barbers-attr-filter__item' . ( $is_active ? ' is-active' : '' ) . '">';
            echo '<a href="' . esc_url( $url ) . '" class="barbers-attr-filter__link">';

            if ( $image_url ) {
                echo '<span class="barbers-attr-filter__img-wrap">';
                echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $term->name ) . '" width="100" height="100" loading="lazy">';
                echo '</span>';
            }

            echo '<span class="barbers-attr-filter__label">' . esc_html( $term->name ) . '</span>';
            echo '</a>';
            echo '</li>';
        }

        echo '</ul>';
        echo '</div>';
    }

    private function get_terms(): array {
        $args = [
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ];

        // Ako smo na stranici kategorije, filtriraj termine samo za tu kategoriju
        if ( is_product_category() ) {
            $current_term = get_queried_object();
            if ( $current_term instanceof WP_Term ) {
                $args['object_ids'] = $this->get_product_ids_in_category( $current_term->term_id );
            }
        }

        $terms = get_terms( $args );
        return is_wp_error( $terms ) ? [] : $terms;
    }

    private function get_product_ids_in_category( int $term_id ): array {
        $query = new WP_Query( [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [ [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $term_id,
                'include_children' => true,
            ] ],
        ] );
        return $query->posts ?: [];
    }

    private function get_active_slug(): string {
        $attribute_name = str_replace( 'pa_', '', $this->taxonomy );
        $param          = 'filter_' . $attribute_name;
        return isset( $_GET[ $param ] ) ? sanitize_title( wp_unslash( $_GET[ $param ] ) ) : '';
    }

    private function get_base_url(): string {
        global $wp;
        return home_url( $wp->request );
    }
}