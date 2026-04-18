<?php
if( !class_exists('Urus_Shortcodes_Products')){
    class Urus_Shortcodes_Products extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'products';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));

            add_action( 'wp_ajax_urus_products_fronted_load_more', array($this,'load_more') );
            add_action( 'wp_ajax_nopriv_urus_products_fronted_load_more', array($this,'load_more') );
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_products', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_products_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_products', $atts ) : $atts;
            $product_style = $sub_title = $title = $product_custom_thumb_height = $product_custom_thumb_width = $product_image_size = $layout = $owl_settings = $owl_layout = $boostrap_layout = $custom_layout = $liststyle =  $owl_rows_space = $boostrap_rows_space = $boostrap_bg_items = $boostrap_lg_items = $boostrap_md_items = $boostrap_sm_items = $boostrap_xs_items = $boostrap_ts_items = $class_list_items = $class_item = '';
            $image_size = array();
            extract( $atts );
            $css_class    = array( 'urus-products' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_products', $atts );
            //class thumb (css position arrows)
            $class_thumb = 'product-thumb';
            $owl_nav_position = isset($atts['owl_nav_position']) ? $atts['owl_nav_position'] : "";
            $owl_dots_style =  isset($atts['owl_dots_style']) ? $atts['owl_dots_style'] : "";
            /* Product Size */
            if ( $atts['product_image_size'] ){
                if( $atts['product_image_size'] == 'custom'){
                    $thumb_width = $atts['product_custom_thumb_width'];
                    $thumb_height = $atts['product_custom_thumb_height'];
                }else{
                    $product_image_size = explode("x",$product_image_size);
                    $thumb_width = $product_image_size[0];
                    $thumb_height = $product_image_size[1];
                }
                if($thumb_width > 0){
                    $func_width = function () use ($thumb_width){
                        return $thumb_width;
                    };
                    add_filter( 'urus_shop_product_thumb_width', $func_width,9999);
                }
                if($thumb_height > 0){
                    $func_height = function () use ($thumb_height){
                        return $thumb_height;
                    };
                    add_filter( 'urus_shop_product_thumb_height', $func_height);
                }
            }
            if( $atts['product_image_style']){
                $product_image_style = $atts['product_image_style'];
                $func_product_image_style = function () use ($product_image_style){
                    return $product_image_style;
                };
                add_filter( 'woo_product_item_image_in_loop', $func_product_image_style);
            }
            
            if( $atts['woo_product_item_background_btn']){
                $woo_product_item_background_btn = $atts['woo_product_item_background_btn'];
                $func_woo_product_item_background_btn = function () use ($woo_product_item_background_btn){
                    return $woo_product_item_background_btn;
                };
                add_filter( 'woo_product_item_background_btn', $func_woo_product_item_background_btn);
            }
            
            if( $atts['product_style']){
                $product_style = $atts['product_style'];
                $func_product_style = function () use ($product_style){
                    return $product_style;
                };
                add_filter( 'product_loop_hint_clas_woo_product_item_layout', $func_product_style);
            }

            $products = Urus_Pluggable_WooCommerce::getProducts($atts);
            $total_product = $products->post_count;
            $product_item_class   = array( 'product-item', $atts[ 'target' ] );
            $product_item_class[] = $atts[ 'product_style' ];

            $product_list_class = array();
            $owl_settings       = '';
            if ( $atts[ 'liststyle' ] == 'grid' ) {
                $product_list_class[] = 'product-list-grid row auto-clear equal-container better-height ';

                $product_item_class[] = $atts[ 'boostrap_rows_space' ];
                $product_item_class[] = 'col-bg-' . $atts[ 'boostrap_bg_items' ];
                $product_item_class[] = 'col-lg-' . $atts[ 'boostrap_lg_items' ];
                $product_item_class[] = 'col-md-' . $atts[ 'boostrap_md_items' ];
                $product_item_class[] = 'col-sm-' . $atts[ 'boostrap_sm_items' ];
                $product_item_class[] = 'col-' . $atts[ 'boostrap_ts_items' ];
            }
            if ( $atts[ 'liststyle' ] == 'owl' ) {
                if ( $total_product < $atts[ 'owl_lg_items' ] ) {
                    $atts[ 'owl_loop' ] = 'false';
                }
                $product_list_class[] = 'product-list-owl swiper-container urus-swiper';
                $product_item_class[] = $atts[ 'owl_rows_space' ];
	            $product_list_class[] = $owl_nav_position;
	            $css_class[] =  $owl_nav_position;
	            $owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
            }
            if ( $atts[ 'liststyle' ] == 'masonry' ) {
                $data_masonry ="data-settings='[{ \"itemSelector\": \".grid-item\", \"columnWidth\": \".grid-sizer\" }]'";
                $product_list_class[] = 'products product-list-masonry urus-masonry';
                $product_item_class[] = 'grid-item';
            }
    
           
            $show_button = false;
            $max_num_page = $products->max_num_pages;
            $query_paged  = $products->query_vars['paged'];
            if( $query_paged >= 0 && ($query_paged < $max_num_page)){
                $show_button = true;
            }else{
                $show_button = false;
            }
            if( $max_num_page <=1){
                $show_button = false;
            }
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if ($atts['title']):?>
                    <h3 class="block-title"><?php echo  esc_html($atts['title']);?></h3>
                <?php endif;?>
                <?php if ( $products->have_posts() ): ?>
                    <?php if ( $atts[ 'liststyle' ] == 'grid' ): ?>
                        <ul class="<?php echo esc_attr( implode( ' ', $product_list_class ) ); ?>">
                            <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                                <li <?php post_class( $product_item_class ); ?>>
                                    <?php wc_get_template_part( 'product-styles/content-product', $atts[ 'product_style' ] ); ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <!-- OWL Products -->
                        <?php if( $atts['enable_loadmore'] == 'yes' && $show_button == true):?>
                            <div class="loadmore-wapper <?php echo esc_attr( $atts['loadmore_style']);?>">
                                <a data-atts="<?php echo esc_attr(wp_json_encode($atts));?>" data-page="2" class="loadmore-button" href="#"><?php echo esc_html( $atts['loadmore_text']);?></a>
                            </div>
                        <?php endif;?>
                    <?php elseif ( $atts[ 'liststyle' ] == 'owl' ) : ?>
                        <div class="slide-inner">
                            <div class="<?php echo esc_attr( implode( ' ', $product_list_class ) ); ?>" <?php echo esc_attr( $owl_settings ); ?> data-dots-style="<?php echo esc_attr( $owl_dots_style ) ?>" data-thumb="<?php echo esc_attr( $class_thumb ); ?>" data-height="<?php echo esc_attr($thumb_height);?>">
                                <div class="swiper-wrapper">
                                    <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                                        <div class="swiper-slide">
                                            <div <?php post_class( $product_item_class ); ?>>
                                                <?php wc_get_template_part( 'product-styles/content-product', $atts[ 'product_style' ] ); ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                            <!-- If we need navigation buttons -->
                            <div class="slick-arrow next">
                                <?php echo familab_icons('arrow-right'); ?>
                            </div>
                            <div class="slick-arrow prev">
                                <?php echo familab_icons('arrow-left'); ?>
                            </div>
                        </div>
                    <?php elseif ( $atts[ 'liststyle' ] == 'masonry' ) : ?>
                        <ul class="<?php echo esc_attr( implode( ' ', $product_list_class ) ); ?>" <?php echo e_data($data_masonry);?> >
                            <li class="grid-sizer"></li>
                            <?php $index = 0;?>
                            <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                                <?php
                                $index++;
                                $item_clas_2x = '';
                                if( in_array($index,array(1,4,7,10,14,16,20,22))){
    
                                    $item_clas_2x ='grid-item--width2x';
                                }
                                ?>
                                <li class="<?php echo esc_attr( implode( ' ', $product_item_class ) ); echo ' '.esc_attr($item_clas_2x); ?>">
                                    <?php wc_get_template_part( 'product-styles/content-product', $atts[ 'product_style' ] ); ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <p>
                        <strong><?php esc_html_e( 'No Product', 'urus' ); ?></strong>
                    </p>
                <?php endif; ?>
            </div>
            <?php
            wp_reset_postdata();
            return apply_filters('urus_shortcode_products_output', ob_get_clean(), $atts, $content);
        }

        /**
         *
         */
        public function load_more(){
            $data = '';
            $type = 'done';
            $atts = $_POST['atts'];
            $page = $_POST['page'];
            extract( $atts );
            $args = array(
                'paged'               => $page
            );
            /* Product Size */
            if ( $atts['product_image_size'] ){
                if( $atts['product_image_size'] == 'custom'){ $thumb_width = $atts['product_custom_thumb_width'];
                    $thumb_height = $atts['product_custom_thumb_height'];
                }else{
                    $product_image_size = explode("x",$product_image_size);
                    $thumb_width = $product_image_size[0];
                    $thumb_height = $product_image_size[1];
                }
                if($thumb_width > 0){

                    $func_width = function () use ($thumb_width){
                        return $thumb_width;
                    };
                    add_filter( 'urus_shop_product_thumb_width', $func_width);
                }
                if($thumb_height > 0){
                    $func_height = function () use ($thumb_height){
                        return $thumb_height;
                    };

                    if( $atts[ 'liststyle' ] == 'masonry'){
                        $func_height = function () use ($thumb_height){
                            return false;
                        };
                    }
                    add_filter( 'urus_shop_product_thumb_height', $func_height);
                }
            }
    
            if( $atts['product_image_style']){
                $product_image_style = $atts['product_image_style'];
                $func_product_image_style = function () use ($product_image_style){
                    return $product_image_style;
                };
                add_filter( 'woo_product_item_image_in_loop', $func_product_image_style);
            }
    
            if( $atts['woo_product_item_background_btn']){
                $woo_product_item_background_btn = $atts['woo_product_item_background_btn'];
                $func_woo_product_item_background_btn = function () use ($woo_product_item_background_btn){
                    return $woo_product_item_background_btn;
                };
                add_filter( 'woo_product_item_background_btn', $func_woo_product_item_background_btn);
            }
    
            if( $atts['product_style']){
                $product_style = $atts['product_style'];
                $func_product_style = function () use ($product_style){
                    return $product_style;
                };
                add_filter( 'product_loop_hint_clas_woo_product_item_layout', $func_product_style);
            }

            $products = Urus_Pluggable_WooCommerce::getProducts($atts,$args);

            $product_item_class   = array( 'product-item');
            $product_item_class[] = $atts[ 'product_style' ];

            $product_list_class = array();
            if ( $atts[ 'liststyle' ] == 'grid' ) {
                $product_list_class[] = 'product-list-grid row auto-clear equal-container better-height ';

                $product_item_class[] = $atts[ 'boostrap_rows_space' ];
                $product_item_class[] = 'col-bg-' . $atts[ 'boostrap_bg_items' ];
                $product_item_class[] = 'col-lg-' . $atts[ 'boostrap_lg_items' ];
                $product_item_class[] = 'col-md-' . $atts[ 'boostrap_md_items' ];
                $product_item_class[] = 'col-sm-' . $atts[ 'boostrap_sm_items' ];
                $product_item_class[] = 'col-' . $atts[ 'boostrap_ts_items' ];
            }

            $max_num_page = $products->max_num_pages;
            $query_paged  = $products->query_vars['paged'];
            if( $query_paged >= 0 && ($query_paged < $max_num_page)){
                $show_button = '1';
            }else{
                $show_button = '0';
            }
            if( $max_num_page <=1){
                $show_button = 0;
            }
            $max_num_page = $products->max_num_pages;
            $query_paged  = $products->query_vars['paged'];
            if( $query_paged >= 0 && ($query_paged < $max_num_page)){
                $show_button = '1';
            }else{
                $show_button = '0';
            }
            if( $max_num_page <=1){
                $show_button = 0;
            }
            ob_start();
            ?>
            <?php if( $products->have_posts()): ?>
                <?php if( $atts['liststyle'] == 'grid'):?>
                    <?php while ( $products->have_posts() ) : $products->the_post();  ?>
                        <li <?php post_class( $product_item_class );?>>
                            <?php wc_get_template_part( 'product-styles/content-product', $atts[ 'product_style' ] ); ?>
                        </li>
                    <?php endwhile;?>
                <?php endif;?>
            <?php endif;?>
            <?php
            $data = ob_get_clean();
            $results = array('type'=>$type,'data'=>$data,'show_button'=>$show_button);
            wp_send_json($results);
            wp_die();


            wp_die();
        }

        public function vc_map(){
            if (!function_exists('vc_map'))
                return false;
            $attributes_tax = array();
            if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
                $attributes_tax = wc_get_attribute_taxonomies();
            }
            $attributes = array();
            if ( is_array( $attributes_tax ) && count( $attributes_tax ) > 0 ) {
                foreach ( $attributes_tax as $attribute ) {
                    $attributes[$attribute->attribute_label] = $attribute->attribute_name;
                }
            }
            // CUSTOM PRODUCT SIZE
            $product_size_width_list = array();
            $width                   = 300;
            $height                  = 300;
            $crop                    = 1;
            if ( function_exists( 'wc_get_image_size' ) ) {
                $size   = wc_get_image_size( 'shop_catalog' );
                $width  = isset( $size['width'] ) ? $size['width'] : $width;
                $height = isset( $size['height'] ) ? $size['height'] : $height;
                $crop   = isset( $size['crop'] ) ? $size['crop'] : $crop;
            }
            for ( $i = 100; $i < $width; $i = $i + 10 ) {
                array_push( $product_size_width_list, $i );
            }
            $product_size_list                         = array();
            $product_size_list[$width . 'x' . $height] = $width . 'x' . $height;
            
            
            foreach ( $product_size_width_list as $k => $w ) {
                $w = intval( $w );
                if ( isset( $width ) && $width > 0 ) {
                    $h = round( $height * $w / $width );
                } else {
                    $h = $w;
                }
                $product_size_list[$w . 'x' . $h] = $w . 'x' . $h;
            }
            $product_size_list['Custom'] = 'custom';
            

            $params    = array(
                'base'        => 'urus_products',
                'name'        => esc_html__( 'Products', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Products', 'urus' ),
                'params'      => array(
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Title', 'urus' ),
                        'param_name'  => 'title',
                        'admin_label' => true,
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Product List style', 'urus' ),
                        'param_name'  => 'liststyle',
                        'value'       => array(
                            esc_html__( 'Grid', 'urus' )       => 'grid',
                            esc_html__( 'Carousel', 'urus') => 'owl',
                            esc_html__( 'Masonry', 'urus') => 'masonry',
                        ),
                        'description' => esc_html__( 'Select a style for list', 'urus' ),
                        'std'         => 'grid',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Product item style', 'urus' ),
                        'param_name'  => 'product_style',
                        'value'       => array(
                            esc_html__( 'Default', 'urus' )             => 'default',
                            esc_html__( 'Classic', 'urus' )             => 'classic',
                            esc_html__( 'Icons - Add to cart', 'urus' )             => 'cart_and_icon',
                            esc_html__( 'Full Info', 'urus' )           => 'full',
                            esc_html__( 'Vertical Icon', 'urus' )       => 'vertical_icon',
                            esc_html__( 'Only Image', 'urus' )          => 'info_on_img',
                            esc_html__( 'Overlay Info', 'urus' )        => 'overlay_info',
                            esc_html__( 'Overlay Center', 'urus' )      => 'overlay_center',
                            esc_html__( 'Countdown', 'urus' )      => 'countdown',
                        ),
                        'std'         => 'default',
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Product Image style', 'urus' ),
                        'param_name'  => 'product_image_style',
                        'value'       => array(
                            esc_html__( 'Classic', 'urus' )             => 'classic',
                            esc_html__( 'Gallery', 'urus' )             => 'gallery',
                            esc_html__( 'Slider', 'urus' )              => 'slider',
                            esc_html__( 'Zoom', 'urus' )                => 'zoom',
                            esc_html__( 'Secondary Image', 'urus' )     => 'secondary_image',
                        ),
                        'std'         => 'classic',
                        'dependency' => array(
                            'element' => 'product_style',
                            'value'   => array( 'classic','cart_and_icon','full','vertical_icon','default' ),
                        ),
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Group button background', 'urus' ),
                        'param_name'  => 'woo_product_item_background_btn',
                        'value'       => array(
                            esc_html__( 'Light', 'urus' )             => 'light',
                            esc_html__( 'Dark', 'urus' )             => 'dark',
                        ),
                        'std'         => 'light',
                        'dependency' => array(
                            'element' => 'product_style',
                            'value'   => array( 'classic','default'),
                        ),
                    ),
                    
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Image size', 'urus' ),
                        'param_name'  => 'product_image_size',
                        'value'       => $product_size_list,
                        'description' => esc_html__( 'Select a size for product', 'urus' ),
                    ),

                    array(
                        'type'       => 'number',
                        'heading'    => esc_html__( 'Width', 'urus' ),
                        'param_name' => 'product_custom_thumb_width',
                        'value'      => $width,
                        'suffix'     => esc_html__( 'px', 'urus' ),
                        'dependency' => array(
                            'element' => 'product_image_size',
                            'value'   => array( 'custom' ),
                        ),
                    ),
                    array(
                        'type'       => 'number',
                        'heading'    => esc_html__( 'Height', 'urus' ),
                        'param_name' => 'product_custom_thumb_height',
                        'value'      => $height,
                        'suffix'     => esc_html__( 'px', 'urus' ),
                        'dependency' => array(
                            'element' => 'product_image_size',
                            'value'   => array( 'custom' ),
                        ),
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Enable Load More', 'urus' ),
                        'param_name'  => 'enable_loadmore',
                        'value'       => array(
                            esc_html__('Yes', 'urus')            => 'yes',
                            esc_html__('No', 'urus')             => 'no',
                        ),
                        'admin_label' => true,
                        'std'         => 'no',
                        "dependency"  => array(
                            "element" => "liststyle", "value" => array('grid')
                        ),
                    ),
                    array(
                        'param_name' => 'loadmore_style',
                        'heading'    => esc_html__( 'Loadmore style', 'urus' ),
                        'type'       => 'dropdown',
                        'value'      => array(
                            esc_html__( 'Default', 'urus' ) => 'default',
                            esc_html__( 'Style 01', 'urus' ) => 'style1',
                        ),
                        "dependency"  => array(
                            "element" => "enable_loadmore", "value" => array('yes')
                        ),
                    ),
                    array(
                        "type"        => "textfield",
                        "heading"     => esc_html__("Load More Text", 'urus'),
                        "param_name"  => "loadmore_text",
                        "value"       => 'Load More',
                        "dependency"  => array("element" => "enable_loadmore", "value" => array( 'yes' )),
                    ),
                    /* Products */
                    array(
                        'type'        => 'taxonomy',
                        'heading'     => esc_html__( 'Product Category', 'urus' ),
                        'param_name'  => 'taxonomy',
                        'options'     => array(
                            'multiple'   => true,
                            'hide_empty' => true,
                            'taxonomy'   => 'product_cat',
                        ),
                        'placeholder' => esc_html__( 'Choose category', 'urus' ),
                        'description' => esc_html__( 'Note: If you want to narrow output, select category(s) above. Only selected categories will be displayed.', 'urus' ),
                        'group'       => esc_html__( 'Products options', 'urus' ),
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Target', 'urus' ),
                        'param_name'  => 'target',
                        'value'       => array(
                            esc_html__( 'Best Selling Products', 'urus' ) => 'best-selling',
                            esc_html__( 'Top Rated Products', 'urus' )    => 'top-rated',
                            esc_html__( 'Recent Products', 'urus' )       => 'recent-product',
                            esc_html__( 'Product Category', 'urus' )      => 'product-category',
                            esc_html__( 'Products', 'urus' )              => 'products',
                            esc_html__( 'Featured Products', 'urus' )     => 'featured_products',
                            esc_html__( 'On Sale', 'urus' )               => 'on_sale',
                            esc_html__( 'On New', 'urus' )                => 'on_new',
                        ),
                        'description' => esc_html__( 'Choose the target to filter products', 'urus' ),
                        'std'         => 'recent-product',
                        'group'       => esc_html__( 'Products options', 'urus' ),
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Order by', 'urus' ),
                        'param_name'  => 'orderby',
                        'value'       => array(
                            esc_html__( 'Date', 'urus' )          => 'date',
                            esc_html__( 'ID', 'urus' )            => 'ID',
                            esc_html__( 'Author', 'urus' )        => 'author',
                            esc_html__( 'Title', 'urus' )         => 'title',
                            esc_html__( 'Modified', 'urus' )      => 'modified',
                            esc_html__( 'Random', 'urus' )        => 'rand',
                            esc_html__( 'Comment count', 'urus' ) => 'comment_count',
                            esc_html__( 'Menu order', 'urus' )    => 'menu_order',
                            esc_html__( 'Sale price', 'urus' )    => '_sale_price',
                        ),
                        'std'         => 'date',
                        'description' => esc_html__( 'Select how to sort.', 'urus' ),
                        'dependency'  => array(
                            'element'            => 'target',
                            'value_not_equal_to' => array(
                                'products',
                            ),
                        ),
                        'group'       => esc_html__( 'Products options', 'urus' ),
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Order', 'urus' ),
                        'param_name'  => 'order',
                        'value'       => array(
                            esc_html__( 'ASC', 'urus' )  => 'ASC',
                            esc_html__( 'DESC', 'urus' ) => 'DESC',
                        ),
                        'std'         => 'DESC',
                        'description' => esc_html__( 'Designates the ascending or descending order.', 'urus' ),
                        'dependency'  => array(
                            'element'            => 'target',
                            'value_not_equal_to' => array(
                                'products',
                            ),
                        ),
                        'group'       => esc_html__( 'Products options', 'urus' ),
                    ),
                    array(
                        'type'       => 'number',
                        'heading'    => esc_html__( 'Product per page', 'urus' ),
                        'param_name' => 'per_page',
                        'value'      => 6,
                        'dependency' => array(
                            'element'            => 'target',
                            'value_not_equal_to' => array(
                                'products',
                            ),
                        ),
                        'group'      => esc_html__( 'Products options', 'urus' ),
                    ),
                    array(
                        'type'        => 'autocomplete',
                        'heading'     => esc_html__( 'Products', 'urus' ),
                        'param_name'  => 'ids',
                        'settings'    => array(
                            'multiple'      => true,
                            'sortable'      => true,
                            'unique_values' => true,
                        ),
                        'save_always' => true,
                        'description' => esc_html__( 'Enter List of Products', 'urus' ),
                        'dependency'  => array(
                            'element' => 'target',
                            'value'   => array( 'products' ),
                        ),
                        'group'       => esc_html__( 'Products options', 'urus' ),
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'urus'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'urus' ),
                    ),
                ),
            );
            $params['params'] = array_merge(
                $params['params'],
                Urus_Pluggable_Visual_Composer::vc_carousel( 'liststyle', 'owl' ),
                Urus_Pluggable_Visual_Composer::vc_bootstrap( 'liststyle', 'grid' )
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }

    }
}