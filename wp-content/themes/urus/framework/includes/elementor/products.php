<?php
 if( !class_exists('Urus_Elementor_Products')){
     class Urus_Elementor_Products extends Urus_Elementor {
         public $name ='products';
         public $title ='Products';
         public $icon ='eicon-products';
         /**
          * Register the widget controls.
          *
          * Adds different input fields to allow the user to change and customize the widget settings.
          *
          * @since 1.0.0
          *
          * @access protected
          */
         protected function _register_controls() {
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
             $product_size_list['custom'] = 'Custom';
             
             //
             $all_category = get_terms( array(
                 'taxonomy' => 'product_cat',
                 'hide_empty' => false,
             ) );
             $categories = array();
             if( !empty($all_category) ){
                 foreach ( $all_category as  $cat){
                     $categories[$cat->slug] = $cat->name;
                 }
             }
    
             
             // Layouts
             $this->start_controls_section(
                 'layout_section',
                 [
                     'label' => esc_html__( 'Layout', 'urus' ),
                 ]
             );
             $this->add_control(
                 'title',
                 [
                     'label' => esc_html__( 'Title', 'urus' ),
                     'type' => \Elementor\Controls_Manager::TEXT,
                     'placeholder' => esc_html__( 'Enter title', 'urus' ),
                 ]
             );
             $this->add_control(
                 'liststyle',
                 [
                     'label' => esc_html__( 'Product List style', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT,
                     'options' => [
                         'grid' => esc_html__( 'Grid Layout', 'urus' ),
                         'owl' => esc_html__( 'Owl Carousel', 'urus' ),
                         'masonry' => esc_html__( 'Masonry', 'urus' ),
                     ],
                     'default' => 'grid',
                 ]
             );
	         $this->add_control(
		         'product_style',
		         [
			         'label' => esc_html__( 'Product item style', 'urus' ),
			         'type' => \Elementor\Controls_Manager::SELECT,
			         'options' => [
				         'default' => esc_html__( 'Default', 'urus' ),
				         'classic' => esc_html__( 'Classic', 'urus' ),
				         'cart_and_icon' => esc_html__( 'Icons - Add to cart', 'urus' ),
				         'full' => esc_html__( 'Full Info', 'urus' ),
				         'vertical_icon' => esc_html__( 'Vertical Icon', 'urus' ),
				         'info_on_img' => esc_html__( 'Only Image', 'urus' ),
				         'overlay_info' => esc_html__( 'Overlay Info', 'urus' ),
				         'overlay_center' => esc_html__( 'Overlay Center', 'urus' ),
				         'countdown' => esc_html__( 'Countdown', 'urus' ),
			         ],
			         'default' => 'default',
		         ]
	         );
	         $this->add_control(
		         'product_image_style',
		         [
			         'label' => esc_html__( 'Product Image style', 'urus' ),
			         'type' => \Elementor\Controls_Manager::SELECT,
			         'options' => [
			              'classic' => esc_html__( 'Classic', 'urus' ),
				         'gallery' => esc_html__( 'Gallery', 'urus' ),
				         'slider' => esc_html__( 'Slider', 'urus' ),
				         'zoom' => esc_html__( 'Zoom', 'urus' ),
				         'secondary_image' => esc_html__( 'Secondary Image', 'urus' ),
			         ],
			         'default' => 'classic',
			         'condition' => array(
				         'product_style' =>array( 'classic','cart_and_icon','full','vertical_icon','default' ),
			         )
		         ]
	         );
	         $this->add_control(
		         'woo_product_item_background_btn',
		         [
			         'label' => esc_html__( 'Group button background', 'urus' ),
			         'type' => \Elementor\Controls_Manager::SELECT,
			         'options' => array(
				         'light' => esc_html__( 'Light', 'urus' ),
				         'dark' => esc_html__( 'Dark', 'urus' ),
                     ),
                     'default' => 'light',
			         'condition' => array(
				         'product_style' => array( 'classic','default')
			         )
		         ]
	         );
             $this->add_control(
                 'product_image_size',
                 [
                     'label' => esc_html__( 'Image size', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT,
                     'options' =>$product_size_list,
                 ]
             );
             $this->add_control(
                 'product_custom_thumb_width',
                 [
                     'label' => esc_html__( 'Width', 'urus' ),
                     'type' => \Elementor\Controls_Manager::TEXT,
                     'placeholder' => esc_html__( 'Unit px', 'urus' ),
                     'condition' => array(
                         'product_image_size' => 'custom'
                     )
                 ]
             );
             $this->add_control(
                 'product_custom_thumb_height',
                 [
                     'label' => esc_html__( 'Height', 'urus' ),
                     'type' => \Elementor\Controls_Manager::TEXT,
                     'placeholder' => esc_html__( 'Unit px', 'urus' ),
                     'condition' => array(
                         'product_image_size' => 'custom'
                     )
                 ]
             );
             $this->add_control(
                 'enable_loadmore',
                 [
                     'label' => esc_html__( 'Enable Load More', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT,
                     'options' => [
                         'yes' => esc_html__( 'Yes', 'urus' ),
                         'no' => esc_html__( 'No', 'urus' ),
                     ],
                     'default' => 'no',
                     'condition' => array(
                         'liststyle' => 'grid'
                     ),
                 ]
             );
             $this->add_control(
                 'loadmore_style',
                 [
                     'label' => esc_html__( 'Loadmore style', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT,
                     'options' => [
                         'default' => esc_html__( 'Default', 'urus' ),
                         'style1' => esc_html__( 'Style 1', 'urus' ),
                     ],
                     'default' => 'default',
                     'condition' => array(
                         'enable_loadmore' => 'yes'
                     )
                 ]
             );
             $this->add_control(
                 'loadmore_text',
                 [
                     'label' => esc_html__( 'Loadmore Text', 'urus' ),
                     'type' => \Elementor\Controls_Manager::TEXT,
                     'condition' => array(
                         'enable_loadmore' => 'yes'
                     ),
                     'default' => esc_html__('Load More','urus')
                 ]
             );
        
             $this->end_controls_section();
             
             // Products
             $this->start_controls_section(
                 'products_section',
                 [
                     'label' => esc_html__( 'Products', 'urus' ),
                 ]
             );
             $this->add_control(
                 'taxonomy',
                 [
                     'label' => esc_html__( 'Product Category', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT2,
                     'multiple' => true,
                     'options' => $categories
                 ]
             );
             $this->add_control(
                 'target',
                 [
                     'label' => esc_html__( 'Target', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT,
                     'options' => [
                         'best-selling' => esc_html__( 'Best Selling Products', 'urus' ),
                         'top-rated' => esc_html__( 'Top Rated Products', 'urus' ),
                         'recent-product' => esc_html__( 'Recent Products', 'urus' ),
                         'product-category' => esc_html__( 'Product Category', 'urus' ),
                         'products'=> esc_html__( 'Products', 'urus' ),
                         'featured_products' => esc_html__( 'Featured Products', 'urus' ),
                         'on_sale' => esc_html__( 'On Sale', 'urus' ) ,
                         'on_new' => esc_html__( 'On New', 'urus' ),
                     ],
                     'default' => 'recent-product',
                 ]
             );
             $this->add_control(
                 'orderby',
                 [
                     'label' => esc_html__( 'Orderby', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT,
                     'options' => [
                         'date' => esc_html__( 'Date', 'urus' ),
                         'ID' => esc_html__( 'ID', 'urus' ),
                         'author' => esc_html__( 'Author', 'urus' ),
                         'title' => esc_html__( 'Title', 'urus' ),
                         'modified'=> esc_html__( 'Modified', 'urus' ),
                         'rand' => esc_html__( 'Rand', 'urus' ),
                         'comment_count' => esc_html__( 'Comment Count', 'urus' ) ,
                         'menu_order' => esc_html__( 'Menu Order', 'urus' ),
                         '_sale_price' => esc_html__( 'Sale Price', 'urus' ),
                     ],
                     'default' => 'date',
                 ]
             );
             $this->add_control(
                 'order',
                 [
                     'label' => esc_html__( 'Order', 'urus' ),
                     'type' => \Elementor\Controls_Manager::SELECT,
                     'options' => [
                         'ASC' => esc_html__( 'ASC', 'urus' ),
                         'DESC' => esc_html__( 'DESC', 'urus' ),
                     ],
                     'default' => 'DESC',
                 ]
             );
             $this->add_control(
                 'per_page',
                 [
                     'label' => esc_html__( 'Product per page', 'urus' ),
                     'type' => \Elementor\Controls_Manager::NUMBER,
                     'min' => 1,
                     'max' => 100,
                     'step' => 1,
                     'default' => 6,
                 ]
             );
             $this->add_control(
                 'ids',
                 [
                     'label' => esc_html__( 'Produucts (IDs)', 'urus' ),
                     'type' => \Elementor\Controls_Manager::TEXT,
                     'condition' => array(
                         'target' => 'products'
                     ),
                     'description' => esc_html__('Ex: 1,2,3','urus')
                 ]
             );
             $this->end_controls_section();
             
             // Grid Layout
             $this->start_controls_section(
                 'grid_setting_section',
                 [
                     'label' => esc_html__( 'Grid settings', 'urus' ),
                 ]
             );
             $bootstrap_settings = Urus_Pluggable_Elementor::elementor_bootstrap('liststyle','grid');
             foreach ($bootstrap_settings as $key => $value){
                 $this->add_control($key,$value);
             }
             $this->end_controls_section();
             // Carousel Layout
             $this->start_controls_section(
                 'carousel_settings_section',
                 [
                     'label' => esc_html__( 'Carousel Settings', 'urus' ),
                 ]
             );
             $carousel_settings = Urus_Pluggable_Elementor::elementor_carousel('liststyle','owl');
             
             foreach ( $carousel_settings as $key => $value){
                 $this->add_control($key,$value);
             }
             $this->end_controls_section();
    
    
         }
         /**
          * Render the widget output on the frontend.
          *
          * Written in PHP and used to generate the final HTML.
          *
          * @since 1.0.0
          *
          * @access protected
          */
         protected function render() {
             $atts = $this->get_settings_for_display();
	         $product_style = $sub_title = $title = $product_custom_thumb_height = $product_custom_thumb_width = $product_image_size = $layout = $owl_settings = $owl_layout = $boostrap_layout = $custom_layout = $liststyle =  $owl_rows_space = $boostrap_rows_space = $boostrap_bg_items = $boostrap_lg_items = $boostrap_md_items = $boostrap_sm_items = $boostrap_xs_items = $boostrap_ts_items = $class_list_items = $class_item = $thumb_height = '';
	         $image_size = array();
	         extract( $atts );
	         $css_class    = array( 'urus-products' );
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

		         if ((isset($atts[ 'owl_lg_items' ]) && $total_product < $atts[ 'owl_lg' ] ) || isset($atts[ 'owl_lg' ]) && $total_product < $atts[ 'owl_lg' ] ) {
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

             $product_item_class = apply_filters('urus_product_items_class', $product_item_class);

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
                             <div class="<?php echo esc_attr( implode( ' ', $product_list_class ) ); ?>" <?php echo esc_attr( $owl_settings ); ?> data-dots-style="<?php echo esc_attr( $owl_dots_style ) ?>" data-height="<?php echo esc_attr($thumb_height);?>">
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
         }
     }
     
 }