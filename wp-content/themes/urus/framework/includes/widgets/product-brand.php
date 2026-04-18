<?php
    if( !class_exists('Urus_Widgets_Product_Brand')){
        class Urus_Widgets_Product_Brand extends Urus_Widgets{
            function __construct(){
                $this->widget_cssclass    = 'urus_widget_brand';
                $this->widget_description = esc_html__( "Display Brand list.", 'urus' );
                $this->widget_id          = 'urus_widget_brand';
                $this->widget_name        = esc_html__( 'Urus: Filter by Brand', 'urus' );
                $this->settings           = array(
                    'title'  => array(
                        'type'  => 'text',
                        'std'   => esc_html__( 'Brand', 'urus' ),
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
                    'hide_empty'         => array(
                        'type'  => 'checkbox',
                        'std'   => 0,
                        'label' => esc_html__( 'Hide empty brand', 'urus' ),
                    ),
                    'display_logo'         => array(
                        'type'  => 'checkbox',
                        'std'   => 0,
                        'label' => esc_html__( 'Show Logo', 'urus' ),
                    ),
                    'use_as_filter'         => array(
                        'type'  => 'checkbox',
                        'std'   => 0,
                        'label' => esc_html__( 'Use as attribute filter', 'urus' ),
                    ),
                );
                
                parent::__construct();
                
            }
            public function widget( $args, $instance ){
                global $wp;
                $this->widget_start($args,$instance);
                $count              = isset( $instance['count'] ) ? $instance['count'] : $this->settings['count']['std'];
                $dropdown           = isset( $instance['dropdown'] ) ? $instance['dropdown'] : $this->settings['dropdown']['std'];
                $orderby            = isset( $instance['orderby'] ) ? $instance['orderby'] : $this->settings['orderby']['std'];
                $hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
                $display_logo        = isset( $instance['display_logo'] ) ? $instance['display_logo'] : $this->settings['display_logo']['std'];
                $use_as_filter        = isset( $instance['use_as_filter'] ) ? $instance['use_as_filter'] : $this->settings['use_as_filter']['std'];
                $args_brand = array(
                    'orderby'           => $orderby,
                    'order'             => 'ASC',
                    'hide_empty'        => $hide_empty,
                    'exclude'           => array(),
                    'exclude_tree'      => array(),
                    'include'           => array(),
                    'number'            => '',
                    'fields'            => 'all',
                    'slug'              => '',
                    'parent'            => 0,
                    'hierarchical'      => true,
                    'child_of'          => 0,
                    'childless'         => false,
                    'get'               => '',
                    'name__like'        => '',
                    'description__like' => '',
                    'pad_counts'        => false,
                    'offset'            => '',
                    'search'            => '',
                    'cache_domain'      => 'core'
                );
                $terms = get_terms('product-brand', $args_brand);
                if( !is_wp_error($terms) && !empty($terms)){
                    if( $dropdown ){
                        $current_link = Urus_Pluggable_WooCommerce::get_current_page_url(true);
                        if ((is_product_category() && !isset($_GET['product_cat']))||$use_as_filter){
                            $form_action = add_query_arg(array(),$current_link);
                        }else{
                            if ( '' === get_option( 'permalink_structure' ) ) {
                                $form_action = remove_query_arg( array( 'page', 'paged', 'product-page' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
                            } else {
                                $form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
                            }
                        }
                        $remove_brand_filter = $current_link;
                        $current_brand_arr = Urus_Pluggable_WooCommerce::get_taxonomy_filter_info('product-brand',$current_link);
                        if ($current_brand_arr && isset($current_brand_arr['remove_filter_link'])){
                            $remove_brand_filter = $current_brand_arr['remove_filter_link'];
                        }
                        ?>
                        <form action="<?php echo esc_url($form_action);?>">
                            <select class="dropdown_product_brand">
                                <option data-action="<?php echo esc_url($remove_brand_filter);?>" value=""><?php esc_html_e('Any Brand','urus');?></option>
                                <?php foreach ($terms as $term):?>
                                    <?php

                                    $selected = false;
                                    $link  = get_term_link($term);
                                    $query_string = Urus_Pluggable_WooCommerce::get_query_string(null,array($term->taxonomy));
                                    $link = add_query_arg($query_string,$link);
                                    if((is_product_category()&& !isset($_GET['product_cat'])) || $use_as_filter){
                                        $link = add_query_arg(array('product-brand'=>$term->slug),$current_link);
                                    }
                                    if ($current_brand_arr && isset($current_brand_arr['slug'])){
                                        if ($current_brand_arr['slug']  == $term->slug){
                                            $selected = true;
                                        }
                                    }
                                    ?>
                                    <option data-action="<?php echo esc_url($link);?>" <?php if( $selected):?> selected="selected" <?php endif;?> value="<?php echo esc_attr($term->slug);?>">
                                        <?php echo esc_html($term->name);?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                            <?php echo wc_query_string_form_fields( null, array( 'product-brand' ), '', true )?>
                        </form>
                        <?php
                        $enable_ajax_filter = Urus_Helper::get_option('enable_ajax_filter',0);
                        if( $enable_ajax_filter == 0){
                            wc_enqueue_js(
                                "
                            jQuery( '.dropdown_product_brand' ).change( function() {
                                if ( jQuery(this).val() != '' ) {
                                    var this_page = '';
                                    var home_url  = '" . esc_js( home_url( '/' ) ) . "';
                                    if ( home_url.indexOf( '?' ) > 0 ) {
                                        this_page = home_url + '&product-brand=' + jQuery(this).val();
                                    } else {
                                        this_page = home_url + '?product-brand=' + jQuery(this).val();
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
                        jQuery(document).on('change','.dropdown_product_brand',function(){
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
                            var dropdown_product_brand = function() {
                                jQuery( '.dropdown_product_brand' ).selectWoo( {
                                    minimumResultsForSearch: 5,
                                    width: '100%',
                                    language: {
                                        noResults: function() {
                                            return '" . esc_js( _x( 'No matches found', 'enhanced select', 'urus' ) ) . "';
                                        }
                                    }
                                } );
                            };
                            dropdown_product_brand();
                            jQuery(document).ajaxComplete(function (event, xhr, settings) {
                                dropdown_product_brand();
                            });
                        }
                        
				        "
                        );
                    }else{
                        ?>
                        <ul class="list-brand">
                            <?php foreach ($terms as $term):?>
                                <?php
                                if ((is_product_category() && !isset($_GET['product_cat']))||$use_as_filter){
                                    $current_link = Urus_Pluggable_WooCommerce::get_current_page_url(true);
                                    $query_string = Urus_Pluggable_WooCommerce::get_query_string(null,array($term->taxonomy));
                                    $current_link = add_query_arg( $query_string,$current_link );
                                    $link = add_query_arg(array('product-brand'=>$term->slug),$current_link);
                                }else{
                                    $link  = get_term_link($term);
                                    $query_string = Urus_Pluggable_WooCommerce::get_query_string(null,array($term->taxonomy));
                                    $link = add_query_arg( $query_string,$link );
                                }
                                $current_brand_arr = Urus_Pluggable_WooCommerce::get_taxonomy_filter_info('product-brand');
                                $class_item = array('brand-item');
                                if ($display_logo){
                                    $class_item[] = 'has_logo';
                                }
                                if ($current_brand_arr && isset($current_brand_arr['slug'])){
                                    if ($current_brand_arr['slug']  == $term->slug){
                                        $class_item[] = 'current-brand';
                                    }
                                }
                                ?>
                                <li class="<?php echo esc_attr( implode( ' ', $class_item ) ); ?>">
                                    <a href="<?php echo esc_url($link);?>">
                                        <?php if($display_logo):?>
                                            <?php
                                            $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                                            $image = Urus_Helper::resize_image($thumbnail_id,150,90,true,true);
                                            echo Urus_Helper::escaped_html($image['img']);
                                            ?>
                                        <?php endif;?>
                                        <?php echo esc_html($term->name);?>
                                        <?php if( $count):?>
                                            <span class="count"><?php echo esc_html($term->count);?></span>
                                        <?php endif;?>
                                    </a>
                                </li>
                            <?php endforeach;?>
                        </ul>
                        <?php
                    }
                }
                $this->widget_end($args);
            }
        }
    }