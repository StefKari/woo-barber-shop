<?php
if( !class_exists('Urus_Widgets_Filter_Category')){
    class Urus_Widgets_Filter_Category extends Urus_Widgets{
        /**
         * Category ancestors.
         *
         * @var array
         */
        public $cat_ancestors;

        /**
         * Current Category.
         *
         * @var bool
         */
        public $current_cat;

        /**
         * Constructor.
         */
        public function __construct() {
            $this->widget_cssclass    = 'woocommerce widget_product_categories';
            $this->widget_description = esc_html__( 'A list or dropdown of product categories.', 'urus' );
            $this->widget_id          = 'urus_woocommerce_product_categories';
            $this->widget_name        = esc_html__( 'Urus: Product Categories', 'urus' );
            $this->settings           = array(
                'title'              => array(
                    'type'  => 'text',
                    'std'   => esc_html__( 'Product categories', 'urus' ),
                    'label' => esc_html__( 'Title', 'urus' ),
                ),
                'orderby'            => array(
                    'type'    => 'select',
                    'std'     => 'name',
                    'label'   => esc_html__( 'Order by', 'urus' ),
                    'options' => array(
                        'order' => esc_html__( 'Category order', 'urus' ),
                        'name'  => esc_html__( 'Name', 'urus' ),
                    ),
                ),
                'dropdown'           => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => esc_html__( 'Show as dropdown', 'urus' ),
                ),
                'count'              => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => esc_html__( 'Show product counts', 'urus' ),
                ),
                'hierarchical'       => array(
                    'type'  => 'checkbox',
                    'std'   => 1,
                    'label' => esc_html__( 'Show hierarchy', 'urus' ),
                ),
                'show_children_only' => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => esc_html__( 'Only show children of the current category', 'urus' ),
                ),
                'hide_empty'         => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => esc_html__( 'Hide empty categories', 'urus' ),
                ),
                'max_depth'          => array(
                    'type'  => 'text',
                    'std'   => '',
                    'label' => esc_html__( 'Maximum depth', 'urus' ),
                ),
                'use_as_filter'         => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => esc_html__( 'Use as attribute filter', 'urus' ),
                ),
            );

            parent::__construct();
        }

        /**
         * Output widget.
         *
         * @see WP_Widget
         * @param array $args     Widget arguments.
         * @param array $instance Widget instance.
         */
        public function widget( $args, $instance ) {
            global $wp_query, $post,$wp;

            $count              = isset( $instance['count'] ) ? $instance['count'] : $this->settings['count']['std'];
            $hierarchical       = isset( $instance['hierarchical'] ) ? $instance['hierarchical'] : $this->settings['hierarchical']['std'];
            $show_children_only = isset( $instance['show_children_only'] ) ? $instance['show_children_only'] : $this->settings['show_children_only']['std'];
            $dropdown           = isset( $instance['dropdown'] ) ? $instance['dropdown'] : $this->settings['dropdown']['std'];
            $orderby            = isset( $instance['orderby'] ) ? $instance['orderby'] : $this->settings['orderby']['std'];
            $hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
            $use_as_filter      = isset( $instance['use_as_filter'] ) ? $instance['use_as_filter'] : $this->settings['use_as_filter']['std'];
            $dropdown_args      = array(
                'hide_empty' => $hide_empty,
            );
            $list_args          = array(
                'show_count'   => $count,
                'hierarchical' => $hierarchical,
                'taxonomy'     => 'product_cat',
                'hide_empty'   => $hide_empty,
            );
            $max_depth          = absint( isset( $instance['max_depth'] ) ? $instance['max_depth'] : $this->settings['max_depth']['std'] );

            $list_args['menu_order'] = false;
            $dropdown_args['depth']  = $max_depth;
            $list_args['depth']      = $max_depth;

            if ( 'order' === $orderby ) {
                $list_args['menu_order'] = 'asc';
            } else {
                $list_args['orderby'] = 'title';
            }

            $this->current_cat   = false;
            $this->cat_ancestors = array();

            if ( is_tax( 'product_cat' ) ) {
                $this->current_cat   = $wp_query->queried_object;
                $this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'product_cat' );

            } elseif ( is_singular( 'product' ) ) {
                $terms = wc_get_product_terms(
                    $post->ID, 'product_cat', apply_filters(
                        'woocommerce_product_categories_widget_product_terms_args', array(
                            'orderby' => 'parent',
                            'order'   => 'DESC',
                        )
                    )
                );
                if ( $terms ) {
                    $main_term           = apply_filters( 'woocommerce_product_categories_widget_main_term', $terms[0], $terms );
                    $this->current_cat   = $main_term;
                    $this->cat_ancestors = get_ancestors( $main_term->term_id, 'product_cat' );
                }
            }

            // Show Siblings and Children Only.
            if ( $show_children_only && $this->current_cat ) {
                if ( $hierarchical ) {
                    $include = array_merge(
                        $this->cat_ancestors,
                        array( $this->current_cat->term_id ),
                        get_terms(
                            'product_cat',
                            array(
                                'fields'       => 'ids',
                                'parent'       => 0,
                                'hierarchical' => true,
                                'hide_empty'   => false,
                            )
                        ),
                        get_terms(
                            'product_cat',
                            array(
                                'fields'       => 'ids',
                                'parent'       => $this->current_cat->term_id,
                                'hierarchical' => true,
                                'hide_empty'   => false,
                            )
                        )
                    );
                    // Gather siblings of ancestors.
                    if ( $this->cat_ancestors ) {
                        foreach ( $this->cat_ancestors as $ancestor ) {
                            $include = array_merge(
                                $include, get_terms(
                                    'product_cat',
                                    array(
                                        'fields'       => 'ids',
                                        'parent'       => $ancestor,
                                        'hierarchical' => false,
                                        'hide_empty'   => false,
                                    )
                                )
                            );
                        }
                    }
                } else {
                    // Direct children.
                    $include = get_terms(
                        'product_cat',
                        array(
                            'fields'       => 'ids',
                            'parent'       => $this->current_cat->term_id,
                            'hierarchical' => true,
                            'hide_empty'   => false,
                        )
                    );
                }

                $list_args['include']     = implode( ',', $include );
                $dropdown_args['include'] = $list_args['include'];

                if ( empty( $include ) ) {
                    return;
                }
            } elseif ( $show_children_only ) {
                $dropdown_args['depth']        = 1;
                $dropdown_args['child_of']     = 0;
                $dropdown_args['hierarchical'] = 1;
                $list_args['depth']            = 1;
                $list_args['child_of']         = 0;
                $list_args['hierarchical']     = 1;
            }

            $this->widget_start( $args, $instance );

            if ( $dropdown ) {
                if ( '' === get_option( 'permalink_structure' ) ) {
                    $form_action = remove_query_arg( array( 'page', 'paged', 'product-page' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
                } else {
                    $form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
                }
                ?>
                <form method="get" action="<?php echo esc_url($form_action);?>">
                <?php
                wc_product_dropdown_categories(
                    apply_filters(
                        'woocommerce_product_categories_widget_dropdown_args', wp_parse_args(
                            $dropdown_args, array(
                                'show_count'         => $count,
                                'hierarchical'       => $hierarchical,
                                'show_uncategorized' => 0,
                                'orderby'            => $orderby,
                                'selected'           => $this->current_cat ? $this->current_cat->slug : (isset($_GET['product_cat']) ? $_GET['product_cat']: ""),
                                'show_option_none' => esc_html__('Any Category','urus'),
                                'walker' => new Urus_Product_Cat_Dropdown_Walker(),
                                'name' => null
                            )
                        )
                    )
                );
                echo wc_query_string_form_fields( null, array( 'product_cat' ), '', true )
                ?>
                </form>
                <?php
                $enable_ajax_filter = Urus_Helper::get_option('enable_ajax_filter',0);
                if( $enable_ajax_filter == 0){
                    wc_enqueue_js(
                        "
                        jQuery( '.dropdown_product_cat' ).change( function() {
                            if ( jQuery(this).val() != '' ) {
                                var this_page = '';
                                var home_url  = '" . esc_js( home_url( '/' ) ) . "';
                                if ( home_url.indexOf( '?' ) > 0 ) {
                                    this_page = home_url + '&product_cat=' + jQuery(this).val();
                                } else {
                                    this_page = home_url + '?product_cat=' + jQuery(this).val();
                                }
                                location.href = this_page;
                            }
                        });
                    "
                     );
                }
                wc_enqueue_js(
                    "
                        // Update value on change.
                        var shop_url ='".get_permalink( wc_get_page_id( 'shop' ) )."';
                        jQuery(document).on('change','.dropdown_product_cat',function(){
                            var action = jQuery(this).find(':selected').data('action');
                            if (typeof action === \"undefined\") {
                                action = shop_url;
                            }
                            jQuery(this).closest('form').attr('action',action);
                        });
                    "
                );
                wc_enqueue_js(
                    "
				// Use Select2 enhancement if possible
				if ( jQuery().selectWoo ) {
					var dropdown_product_cat = function() {
						jQuery( '.dropdown_product_cat' ).selectWoo( {
							minimumResultsForSearch: 5,
							width: '100%',
							language: {
								noResults: function() {
									return '" . esc_js( _x( 'No matches found', 'enhanced select', 'urus' ) ) . "';
								}
							}
						} );
					};
					dropdown_product_cat();
					jQuery(document).ajaxComplete(function (event, xhr, settings) {
				        dropdown_product_cat();
                    });
				}				
				");

            } else {

                $list_args['walker']                     = new Urus_Product_Cat_Walker(array('use_as_filter'=>$use_as_filter));
                $list_args['title_li']                   = '';
                $list_args['pad_counts']                 = 1;
                $list_args['show_option_none']           = esc_html__( 'No product categories exist.', 'urus' );
                $list_args['current_category']           = ( $this->current_cat ) ? $this->current_cat->term_id : '';
                $list_args['current_category_ancestors'] = $this->cat_ancestors;
                $list_args['max_depth']                  = $max_depth;

                echo '<ul class="product-categories">';

                wp_list_categories( apply_filters( 'woocommerce_product_categories_widget_args', $list_args ) );

                echo '</ul>';
                echo '<input class="filter-selected" type="hidden" name="product_cat" value="">';
            }

            $this->widget_end( $args );
        }
    }
}
