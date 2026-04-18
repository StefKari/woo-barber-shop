<?php
if( !class_exists('Urus_Widgets_Products')){
    class Urus_Widgets_Products extends Urus_Widgets{
        /**
         * Constructor.
         */
        public function __construct() {
            $this->widget_cssclass    = 'woocommerce widget_products urus_widget_products nav-center';
            $this->widget_description = esc_html__( "A list of your store's products.", 'urus' );
            $this->widget_id          = 'urus_woocommerce_products';
            $this->widget_name        = esc_html__( 'Urus: Products', 'urus' );
            $this->settings           = array(
                'title'       => array(
                    'type'  => 'text',
                    'std'   => esc_html__( 'Products', 'urus' ),
                    'label' => esc_html__( 'Title', 'urus' ),
                ),
                'number'      => array(
                    'type'  => 'number',
                    'step'  => 1,
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 5,
                    'label' => esc_html__( 'Number of products to show', 'urus' ),
                ),
                'show'        => array(
                    'type'    => 'select',
                    'std'     => '',
                    'label'   => esc_html__( 'Show', 'urus' ),
                    'options' => array(
                        ''         => esc_html__( 'All products', 'urus' ),
                        'featured' => esc_html__( 'Featured products', 'urus' ),
                        'onsale'   => esc_html__( 'On-sale products', 'urus' ),
                    ),
                ),
                'orderby'     => array(
                    'type'    => 'select',
                    'std'     => 'date',
                    'label'   => esc_html__( 'Order by', 'urus' ),
                    'options' => array(
                        'date'  => esc_html__( 'Date', 'urus' ),
                        'price' => esc_html__( 'Price', 'urus' ),
                        'rand'  => esc_html__( 'Random', 'urus' ),
                        'sales' => esc_html__( 'Sales', 'urus' ),
                    ),
                ),
                'order'       => array(
                    'type'    => 'select',
                    'std'     => 'desc',
                    'label'   => _x( 'Order', 'Sorting order', 'urus' ),
                    'options' => array(
                        'asc'  => esc_html__( 'ASC', 'urus' ),
                        'desc' => esc_html__( 'DESC', 'urus' ),
                    ),
                ),
                'hide_free'   => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => esc_html__( 'Hide free products', 'urus' ),
                ),
                'show_hidden' => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => esc_html__( 'Show hidden products', 'urus' ),
                ),
            );

            parent::__construct();
        }

        /**
         * Query the products and return them.
         *
         * @param  array $args     Arguments.
         * @param  array $instance Widget instance.
         * @return WP_Query
         */
        public function get_products( $args, $instance ) {
            $number                      = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
            $show                        = ! empty( $instance['show'] ) ? sanitize_title( $instance['show'] ) : $this->settings['show']['std'];
            $orderby                     = ! empty( $instance['orderby'] ) ? sanitize_title( $instance['orderby'] ) : $this->settings['orderby']['std'];
            $order                       = ! empty( $instance['order'] ) ? sanitize_title( $instance['order'] ) : $this->settings['order']['std'];
            $product_visibility_term_ids = wc_get_product_visibility_term_ids();

            $query_args = array(
                'posts_per_page' => $number,
                'post_status'    => 'publish',
                'post_type'      => 'product',
                'no_found_rows'  => 1,
                'order'          => $order,
                'meta_query'     => array(),
                'tax_query'      => array(
                    'relation' => 'AND',
                ),
            ); // WPCS: slow query ok.

            if ( empty( $instance['show_hidden'] ) ) {
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'term_taxonomy_id',
                    'terms'    => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
                    'operator' => 'NOT IN',
                );
                $query_args['post_parent'] = 0;
            }

            if ( ! empty( $instance['hide_free'] ) ) {
                $query_args['meta_query'][] = array(
                    'key'     => '_price',
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'DECIMAL',
                );
            }

            if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'term_taxonomy_id',
                        'terms'    => $product_visibility_term_ids['outofstock'],
                        'operator' => 'NOT IN',
                    ),
                ); // WPCS: slow query ok.
            }

            switch ( $show ) {
                case 'featured':
                    $query_args['tax_query'][] = array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'term_taxonomy_id',
                        'terms'    => $product_visibility_term_ids['featured'],
                    );
                    break;
                case 'onsale':
                    $product_ids_on_sale    = wc_get_product_ids_on_sale();
                    $product_ids_on_sale[]  = 0;
                    $query_args['post__in'] = $product_ids_on_sale;
                    break;
            }

            switch ( $orderby ) {
                case 'price':
                    $query_args['meta_key'] = '_price'; // WPCS: slow query ok.
                    $query_args['orderby']  = 'meta_value_num';
                    break;
                case 'rand':
                    $query_args['orderby'] = 'rand';
                    break;
                case 'sales':
                    $query_args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
                    $query_args['orderby']  = 'meta_value_num';
                    break;
                default:
                    $query_args['orderby'] = 'date';
            }
            $urus_products = new WP_Query( apply_filters( 'woocommerce_products_widget_query_args', $query_args ) );
            wp_reset_postdata();
            return $urus_products;
        }

        /**
         * Output widget.
         *
         * @see WP_Widget
         *
         * @param array $args     Arguments.
         * @param array $instance Widget instance.
         */
        public function widget( $args, $instance ) {
            if ( $this->get_cached_widget( $args ) ) {
                return;
            }

            ob_start();

            $products = $this->get_products( $args, $instance );
            if ( $products && $products->have_posts() ) {
                $this->widget_start( $args, $instance );
                $atts = array(
                    'loop'         => 'false',
                    'ts_items'     => 1,
                    'xs_items'     => 1,
                    'sm_items'     => 1,
                    'md_items'     => 1,
                    'lg_items'     => 1,
                    'ls_items'     => 1,
                    'navigation'   => 'true',
                    'slide_margin' => 30
                );
                $carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
                ?>
                <div class=" urus_product_list_widget urus-products-carousel swiper-container urus-swiper " <?php echo Urus_Helper::escaped_html($carousel_settings);?>>
                    <div class=" swiper-wrapper "  >

                    <?php
                    while ( $products->have_posts() ) {
                        $products->the_post();
                        ?>
                        <div class="product-item default swiper-slide">
                            <?php wc_get_template( 'product-styles/content-product-default.php' );?>
                        </div>
                        <?php
                    }

                    ?>
                    </div>
                    <!-- If we need pagination -->
                        <div class="swiper-pagination"></div>
                        
                </div>
                <!-- If we need navigation buttons -->
                <div class="slick-arrow next">
                    <?php echo familab_icons('arrow-right'); ?>
                </div>
                <div class="slick-arrow prev">
                    <?php echo familab_icons('arrow-left'); ?>
                </div>
                <?php

                $this->widget_end( $args );
            }

            wp_reset_postdata();

            echo ''.$this->cache_widget( $args, ob_get_clean() ); // WPCS: XSS ok.
        }
    }
}