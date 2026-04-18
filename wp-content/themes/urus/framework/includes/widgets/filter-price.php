<?php
if( !class_exists('Urus_Widgets_Filter_Price')){
    class Urus_Widgets_Filter_Price extends Urus_Widgets{
        /**
         * Constructor.
         */
        public function __construct() {
            $this->widget_cssclass    = 'woocommerce widget_price_filter urus_widget_price_filter';
            $this->widget_description = esc_html__( 'Display a slider to filter products in your store by price.', 'urus' );
            $this->widget_id          = 'urus_woocommerce_price_filter';
            $this->widget_name        = esc_html__( 'Urus: Filter Products by Price', 'urus' );
            $this->settings           = array(
                'title' => array(
                    'type'  => 'text',
                    'std'   => esc_html__( 'Filter by price', 'urus' ),
                    'label' => esc_html__( 'Title', 'urus' ),
                ),
                'display_type' => array(
                    'type'    => 'select',
                    'std'     => 'slide',
                    'label'   => esc_html__( 'Display type', 'urus' ),
                    'options' => array(
                        'slide'          => esc_html__('Slide', 'urus'),
                        'selected_price' => esc_html__( 'Selected price', 'urus' ),
                        'dropdown' => esc_html__( 'Dropdown', 'urus' ),
                    ),
                ),
                'number_of_price_ranges' => array(
                    'type'  => 'text',
                    'std'   => 5,
                    'label' => esc_html__( 'Number of Price Ranges', 'urus' ),
                ),
            );
            $suffix                   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting' . $suffix . '.js', array( 'jquery' ), '0.4.2' );
            wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch' . $suffix . '.js', array( 'jquery-ui-slider' ), WC_VERSION, true );
            wp_register_script( 'wc-price-slider', WC()->plugin_url() . '/assets/js/frontend/price-slider' . $suffix . '.js', array( 'jquery-ui-slider', 'wc-jquery-ui-touchpunch', 'accounting' ), WC_VERSION, true );
            wp_localize_script(
                'wc-price-slider', 'woocommerce_price_slider_params', array(
                    'currency_format_num_decimals' => 0,
                    'currency_format_symbol'       => get_woocommerce_currency_symbol(),
                    'currency_format_decimal_sep'  => esc_attr( wc_get_price_decimal_separator() ),
                    'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
                    'currency_format'              => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
                )
            );

            if ( is_customize_preview() ) {
                wp_enqueue_script( 'wc-price-slider' );
            }

            parent::__construct();
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
            $display_type       = isset( $instance['display_type'] ) ? $instance['display_type'] : $this->settings['display_type']['std'];
            $number_of_price_ranges       = isset( $instance['number_of_price_ranges'] ) ? $instance['number_of_price_ranges'] : $this->settings['display_type']['std'];
            global $wp;

            if ( ! is_shop() && ! is_product_taxonomy() ) {
                return;
            }

            $min_price = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : null; // WPCS: input var ok, CSRF ok.
            $max_price = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : null; // WPCS: input var ok, CSRF ok.
            if ( ! wc()->query->get_main_query()->post_count && null === $min_price && null === $max_price ) {
                return;
            }

            wp_enqueue_script( 'wc-price-slider' );

            // Find min and max price in current result set.
            $prices = $this->get_filtered_price();
            $min    = floor( $prices->min_price );
            $max    = ceil( $prices->max_price );

            if ( $min === $max ) {
                return;
            }

            $this->widget_start( $args, $instance );

            if ( '' === get_option( 'permalink_structure' ) ) {
                $form_action = remove_query_arg( array( 'page', 'paged', 'product-page' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
            } else {
                $form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
            }
            $min_price = null !== $min_price ? $min_price : apply_filters( 'woocommerce_price_filter_widget_min_amount', $min );
            $max_price = null !== $max_price ? $max_price : apply_filters( 'woocommerce_price_filter_widget_max_amount', $max );
            if( 'selected_price' === $display_type) {
                $show_button_clear = false;
    
                if ($max <= $number_of_price_ranges) {
                    $number_of_price_ranges = $min;
                }
                $distance = round(($max) / $number_of_price_ranges);
                $currency_symbol = get_woocommerce_currency_symbol();
                $query_string = apply_filters('urus_widget_current_page_url',array());
                $curent_url = add_query_arg($query_string, Urus_Pluggable_WooCommerce::get_current_page_url());
                ?>
                <ul class="fiter-prices fiter-prices-link">
                    <?php
                        for ($i = $min; $i < $max; $i += $distance) {
                            $price_min = $i;
                            $price_max = $i + $distance;
                            if ($price_max > $max) {
                                $price_max = $max;
                            }
                            $price_args = array(
                                'min_price' => $price_min,
                                'max_price' => $price_max,
                            );
                            $item_price_link = add_query_arg($price_args, $curent_url);
                            $selected = false;
                            if (isset($_GET['min_price']) && $_GET['min_price'] == $price_min && isset($_GET['max_price']) && $_GET['max_price'] == $price_max) {
                                $selected = true;
                                $show_button_clear = true;
                            }
                            ?>
                            <?php if ($max > $min): ?>
                                <li class="<?php if ($selected): ?>selected<?php endif; ?>">
                                    <a class="distance_price" href="<?php echo esc_attr($item_price_link); ?>">
                                        <span><?php echo esc_html($currency_symbol . $price_min . " - " . $currency_symbol . $price_max); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php
                        }
                    ?>
                </ul>
                <?php if ($show_button_clear && Urus_Helper::get_option('enable_instant_filter', 0) == 1): ?>
                    <?php
                    $clear_link = add_query_arg($query_string, Urus_Helper::get_current_page_url());
                    ?>
                    <a href="<?php echo esc_url($clear_link); ?>"
                       class="button clear-button"><?php esc_html_e('Clear', 'urus'); ?></a>
                <?php endif; ?>
                <?php
            }elseif('dropdown' === $display_type){
                if ($max <= $number_of_price_ranges) {
                    $number_of_price_ranges = $min;
                }
                $distance = round(($max) / $number_of_price_ranges);
                $currency_symbol = get_woocommerce_currency_symbol();
                echo '<form  method="get" action="' . esc_url( $form_action ) . '"><select class="filter_dropdown_price" >';
                echo '<option>'.esc_html__('Any Price','urus').'</option>';
                        for ($i = $min; $i < $max; $i += $distance) {
                            $price_min = $i;
                            $price_max = $i + $distance;
                            if ($price_max > $max) {
                                $price_max = $max;
                            }
                            $selected = false;
                            if (isset($_GET['min_price']) && $_GET['min_price'] == $price_min && isset($_GET['max_price']) && $_GET['max_price'] == $price_max) {
                                $selected = true;
                            }
                            ?>
                            <?php if ($max > $min): ?>
                                <option data-min="<?php echo esc_attr($price_min)?>" data-max="<?php echo esc_attr($price_max)?>" <?php if ($selected): ?> selected="selected" <?php endif; ?> value="">
                                    <?php echo esc_html($currency_symbol . $price_min . " - " . $currency_symbol . $price_max); ?>
                                </option>
                            <?php endif; ?>
                            <?php
                        }
                echo '</select>';
                        echo wc_query_string_form_fields( null, array( 'min_price', 'max_price' ), '', true );
                        echo '<input type="hidden" id="min_price" name="min_price" value="' . esc_attr( $min_price ) . '" data-min="'. esc_attr( apply_filters( 'woocommerce_price_filter_widget_min_amount', $min ) ).'" />
                        <input type="hidden" id="max_price" name="max_price" value="' . esc_attr( $max_price ) . '" data-max="'. esc_attr( apply_filters( 'woocommerce_price_filter_widget_max_amount', $max ) ).'" />';
                        echo '</form>';
                $enable_ajax_filter = Urus_Helper::get_option('enable_ajax_filter',0);
                if( $enable_ajax_filter == 0){
                    wc_enqueue_js(
                        "
                    // Update value on change.
                    jQuery( '.filter_dropdown_price' ).change( function() {
    
                        // Submit form on change if standard dropdown.
                        if ( ! jQuery( this ).attr( 'multiple' ) ) {
                            jQuery( this ).closest( 'form' ).submit();
                        }
                    });"
                    );
                }
                wc_enqueue_js(
                    "
                    
                    jQuery(document).on('change','.filter_dropdown_price',function(){
                        var min = jQuery(this).find(':selected').data('min');
                        var max = jQuery(this).find(':selected').data('max');
                        jQuery( '#min_price' ).val( min );
                        jQuery( '#max_price' ).val( max );
                    });
                    
                    "
                );
                wc_enqueue_js(
                    "
				// Use Select2 enhancement if possible
				if ( jQuery().selectWoo ) {
					var filter_price_select = function() {
						jQuery( '.filter_dropdown_price' ).selectWoo( {
							minimumResultsForSearch: 5,
							width: '100%',
							language: {
								noResults: function() {
									return '" . esc_js( _x( 'No matches found', 'enhanced select', 'urus' ) ) . "';
								}
							}
						} );
					};
					filter_price_select();
				}
				jQuery(document).ajaxComplete(function (event, xhr, settings) {
				    filter_price_select();
                });
				"
                );
            }else{
                echo '<form method="get" action="' . esc_url( $form_action ) . '">
                <div class="price_slider_wrapper">
                    <div class="price_slider" style="display:none;"></div>
                    <div class="price_slider_amount">
                        <input type="text"  id="min_price" name="min_price" value="' . esc_attr( $min_price ) . '" data-min="' . esc_attr( apply_filters( 'woocommerce_price_filter_widget_min_amount', $min ) ) . '" placeholder="' . esc_attr__( 'Min price', 'urus' ) . '" />
                        <input type="text" id="max_price" name="max_price" value="' . esc_attr( $max_price ) . '" data-max="' . esc_attr( apply_filters( 'woocommerce_price_filter_widget_max_amount', $max ) ) . '" placeholder="' . esc_attr__( 'Max price', 'urus' ) . '" />
                        
                        <div class="price_label" style="display:none;">
                            ' . esc_html__( 'Price:', 'urus' ) . ' <span class="from"></span> &mdash; <span class="to"></span>
                        </div>
                        ' . wc_query_string_form_fields( null, array( 'min_price', 'max_price' ), '', true ) . '
                        <div class="clear"></div>
                    </div>
                </div>
                </form>'; // WPCS: XSS ok.
            }
            $this->widget_end( $args );
        }

        /**
         * Get filtered min price for current products.
         *
         * @return int
         */
        protected function get_filtered_price() {
            global $wpdb;
            $args       = wc()->query->get_main_query()->query_vars;
            $tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
            $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();
            if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
                $tax_query[] = array(
                    'taxonomy' => $args['taxonomy'],
                    'terms'    => array( $args['term'] ),
                    'field'    => 'slug',
                );
            }
            foreach ( $meta_query + $tax_query as $key => $query ) {
                if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
                    unset( $meta_query[ $key ] );
                }
            }
            $meta_query = new WP_Meta_Query( $meta_query );
            $tax_query  = new WP_Tax_Query( $tax_query );
            $meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
            $tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
            $sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
            $sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
            $sql .= " 	WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
			AND {$wpdb->posts}.post_status = 'publish'
			AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
			AND price_meta.meta_value > '' ";
            $sql .= $tax_query_sql['where'] . $meta_query_sql['where'];
            $search = WC_Query::get_main_search_query_sql();
            if ( $search ) {
                $sql .= ' AND ' . $search;
            }
            return $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
        }
    }
}