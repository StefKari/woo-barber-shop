<?php
if( !class_exists('Urus_Pluggable_WooCommerce')){
    class  Urus_Pluggable_WooCommerce{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;
        protected static $mobile_template = false;
        /**
         * Initialize pluggable functions for Visual Composer.
         * Initialize pluggable functions for Visual Composer.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            add_action('wp',array(__CLASS__,'wp'));
            self::$mobile_template = Urus_Helper::is_mobile_template();

            add_filter( 'body_class', array(__CLASS__,'wc_body_class' ));

            add_filter('woo_sidebar_option_layout',array(__CLASS__,'woo_sidebar_option_layout'));
            add_filter( 'wp_nav_menu_items', array( __CLASS__, 'alidropship_currency_switcher' ), 10, 2 );
            $woo_single_used_layout = Urus_Helper::get_option('woo_single_used_layout','vertical');
            $single_hook_layout = 'default';
            if (isset(Urus_Pluggable::$theme_layout['single_layout'][$woo_single_used_layout]['hook'])){
                $single_hook_layout = Urus_Pluggable::$theme_layout['single_layout'][$woo_single_used_layout]['hook'];
            }
            add_filter( 'woocommerce_enqueue_styles',array(__CLASS__,'dequeue_styles') );

            add_filter('woocommerce_get_image_size_gallery_thumbnail',array('Urus_Pluggable_WooCommerce','woocommerce_get_image_size_gallery_thumbnail'));
            add_filter('woocommerce_get_image_size_single',array('Urus_Pluggable_WooCommerce','woocommerce_get_image_size_single'));

            remove_action('woocommerce_before_main_content','woocommerce_output_content_wrapper',10);
            remove_action('woocommerce_after_main_content','woocommerce_output_content_wrapper_end',10);

            // Shop
            if (self::$mobile_template){
                $single_hook_layout = 'mobile';
                //shop mobile layout
                remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
                remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
                remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
                remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
                remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
                remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

                add_filter( 'loop_shop_per_page',array(__CLASS__,'loop_shop_per_page'), 999 );
                add_action( 'urus_shop_control_top', array(__CLASS__,'shop_list_mode'), 15 );
                // add urus_shop_control_bottom hook.
                add_action('woocommerce_after_shop_loop',array(__CLASS__,'shop_control_bottom'),1);
                //remove default pagination and add to shop control bottom hook
                remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
                $shop_page_pagination =  Urus_Helper::get_option('shop_page_pagination','yes');
                if( !class_exists('PrdctfltrInit') || $shop_page_pagination == 'no'){
                    add_action( 'urus_shop_control_bottom', array(__CLASS__,'woocommerce_pagination'), 10 );
                }
            }else{
                $shop_page_pagination =  Urus_Helper::get_option('shop_page_pagination','yes');
                // shop for desktop
                // Remove breadcrumb default
                remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
                add_action('woocommerce_before_main_content',array(__CLASS__,'woocommerce_shop_heading'),1);
                add_filter('woocommerce_show_page_title',array(__CLASS__,'woocommerce_show_page_title'));
                add_filter( 'woocommerce_breadcrumb_defaults',array(__CLASS__,'woocommerce_breadcrumbs') );
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
                add_action('woocommerce_before_shop_loop',array(__CLASS__,'shop_control_top'),10);
                add_action('urus_shop_control_top','woocommerce_catalog_ordering',25);
                add_action( 'urus_shop_control_top', array(__CLASS__,'shop_column_select'), 20 );
                add_action('urus_shop_control_top',array(__CLASS__,'shop_filter_control'),1);
                add_action('urus_after_shop_control_top',array(__CLASS__,'shop_filter_content'));
                $woo_shop_layout = apply_filters('woo_sidebar_option_layout','left');
                if( $woo_shop_layout!='full'){
                    add_action( 'urus_shop_control_top', 'woocommerce_result_count', 5 );
                    add_action( 'urus_shop_control_top',array(__CLASS__,'sidebar_button'), 1 );
                }
                // add urus_shop_control_bottom hook.
                add_action('woocommerce_after_shop_loop',array(__CLASS__,'shop_control_bottom'),1);
                //remove default pagination and add to shop control bottom hook
                remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
                add_filter( 'loop_shop_per_page',array(__CLASS__,'loop_shop_per_page'), 999 );
                add_action('woocommerce_after_main_content',array(__CLASS__,'shop_categories'),1);
                if( !class_exists('PrdctfltrInit') || $shop_page_pagination == 'no'){
                    add_action( 'urus_shop_control_bottom', array(__CLASS__,'woocommerce_pagination'), 10 );
                }
                add_action('woocommerce_before_shop_loop',array(__CLASS__,'sub_categories_row'),10);
            }

            add_action('urus_after_block_filter',array(__CLASS__,'instant_filter'),10);

            /*SINGLE PRODUCT LAYOUT */
            require_once('woo-single-hook/'.$single_hook_layout.'.php');
            // MINI CART
            add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'cart_link_fragment' ) );
            add_action( 'wp_ajax_urus_remove_from_cart', array(__CLASS__,'remove_from_cart') );
            add_action( 'wp_ajax_nopriv_urus_remove_from_cart', array(__CLASS__,'remove_from_cart') );
            add_action( 'wp_ajax_urus_undo_remove_cart_item', array(__CLASS__,'undo_remove_cart_item') );
            add_action( 'wp_ajax_nopriv_urus_undo_remove_cart_item', array(__CLASS__,'undo_remove_cart_item') );
            remove_action('woocommerce_cart_collaterals','woocommerce_cross_sell_display');
            add_action('woocommerce_after_cart','woocommerce_cross_sell_display');
            // BackEnd
            add_filter( 'woocommerce_product_data_tabs', array(__CLASS__,'product_edit_tabs') );
            add_filter( 'woocommerce_product_data_panels', array(__CLASS__,'product_edit_tab_content') );
            add_action( 'woocommerce_process_product_meta_simple', array(__CLASS__,'product_edit_tab_save')  );
            add_action( 'woocommerce_process_product_meta_variable', array(__CLASS__,'product_edit_tab_save')  );

            add_filter('woocommerce_loop_add_to_cart_args',array(__CLASS__,'woocommerce_loop_add_to_cart_args'),10,2);

            add_filter( 'woocommerce_available_variation', array(__CLASS__,'custom_available_variation'), 1, 3 );

            add_action( 'wp_ajax_nopriv_ajaxlogin', array(__CLASS__,'ajax_login') );

            $shop_layout = Urus_Helper::get_option('shop_layout','simple');
            if( $shop_layout =='simple'){
                add_action('urus_after_shop_heading',array(__CLASS__,'shop_sub_category'),10);
            }
            if( $shop_layout == 'modern'){
                add_action('urus_woocommerce_before_main_content',array(__CLASS__,'shop_sub_category'),1);
            }

            remove_action('woocommerce_before_shop_loop_item','woocommerce_template_loop_product_link_open',10);
            remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_product_link_close',5);
            remove_action('woocommerce_before_shop_loop_item_title','woocommerce_show_product_loop_sale_flash',10);
            add_action('woocommerce_before_shop_loop_item_title',array('Urus_Pluggable_WooCommerce','product_loop_sale_flash'),10);
            add_action('woocommerce_before_shop_loop_item_title',array('Urus_Pluggable_WooCommerce','product_loop_stock_status'),10);

            //Add image to grouped products
            add_action( 'woocommerce_grouped_product_list_before_label',array('Urus_Pluggable_WooCommerce','add_thumb_grouped_product'),10);

            // Loop Hocks

            remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
            add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 5 );
            $woo_product_rating_in_loop = Urus_Helper::get_option('woo_product_rating_in_loop',0);
            if($woo_product_rating_in_loop  == 0){
                remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating',5);
            }

            add_action('urus_product_loop_group_flash_content','woocommerce_show_product_loop_sale_flash',10);
            add_action('urus_product_loop_group_flash_content',array('Urus_Pluggable_WooCommerce','woocommerce_show_product_loop_new_flash'),15);

            remove_action('woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_thumbnail',10);


            add_action('woocommerce_before_shop_loop_item_title',array('Urus_Pluggable_WooCommerce','template_loop_product_thumbnail'),15);

            //Product loop option selector
            add_action('urus_loop_product_select_option',array('Urus_Pluggable_WooCommerce','urus_loop_product_select_option'),15);
            remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10);
            add_action('woocommerce_shop_loop_item_title',array('Urus_Pluggable_WooCommerce','template_loop_product_title'),10);

            $use_custom_placeholder = Urus_Helper::get_option('enable_custom_placeholder',false);


            if ($use_custom_placeholder ) {
                add_filter( 'woocommerce_placeholder_img_src', array(__CLASS__,'urus_custom_woocommerce_placeholder'), 10 );

            }
            add_filter('woocommerce_sale_flash',array(__CLASS__,'urus_sale_flase'),10,3);
            // State that initialization completed.
            self::$initialized = true;
        }

        public static function add_thumb_grouped_product($grouped_product_child){
            $product_attachment_id = $grouped_product_child->get_image_id();
            $product_image = Urus_Helper::get_product_image($product_attachment_id, 'thumbnail');
            ?>
                <td class="urus-group-product-image">
                    <img src="<?php echo esc_attr($product_image[0]); ?>" alt="<?php echo esc_attr($grouped_product_child->get_name()); ?>">
                </td>
            <?php
        }
        public static function sidebar_button(){
            ?>
            <a class="sidebar-button" href="#"><?php esc_html_e('Sidebar','urus');?></a>
            <?php

        }

        public static function urus_sale_flase($html,$post, $product){
            $disable_sale = Urus_Helper::get_option('disable_sale_label',false);
            if ($disable_sale)
                return '';
            $content_type = Urus_Helper::get_option('sale_content','default');
            if ($content_type == 'percent'){
                if ( $product->is_type( 'simple' ) ) {
                    $regular_price = $product->get_regular_price();
                    $sale_price = $product->get_sale_price();
                    $max_percentage = ( ( $regular_price - $sale_price ) / $regular_price ) * 100;
                } elseif ( $product->is_type( 'variable' ) ) {
                    $max_percentage = 0;
                    foreach ( $product->get_children() as $child_id ) {
                        $variation = wc_get_product( $child_id );
                        $price = $variation->get_regular_price();
                        $sale = $variation->get_sale_price();
                        if ( $price != 0 && ! empty( $sale ) ) $percentage = ( $price - $sale ) / $price * 100;
                        if ( $percentage > $max_percentage ) {
                            $max_percentage = $percentage;
                        }
                    }
                }
                if ( $max_percentage > 0 ) return "<span class='onsale sale-perc'>-" . round($max_percentage) . "%</span>";
            }
            return $html;
        }
        public static function urus_custom_woocommerce_placeholder($url_placeholder, $is_thumbnail = false ){

            $crop   = true;
            if ($is_thumbnail) {
                // GET SIZE IMAGE SETTING
                $width  = 150;
                $height = 150;
                $size   = wc_get_image_size( 'thumbnail' );
                if ( $size ) {
                    $width  = $size['width'];
                    $height = $size['height'];
                }
            }else{
                // GET SIZE IMAGE SETTING
                $width  = 680;
                $height = 833;
                $size   = wc_get_image_size( 'shop_catalog' );
                if ( $size ) {
                    $width  = $size['width'];
                    $height = $size['height'];
                }
                $width       = apply_filters( 'urus_shop_product_thumb_width', $width );
                $height      = apply_filters( 'urus_shop_product_thumb_height', $height );
            }

            if ( $size ) {
                $width  = $size['width'];
                $height = $size['height'];
            }
            $width       = apply_filters( 'urus_shop_product_thumb_width', $width );
            $height      = apply_filters( 'urus_shop_product_thumb_height', $height );

            $default_custom_placeholder = array(
                'url'       => URUS_IMAGES . '/product-placeholder.jpg',
                'id'        => '',
                'width'     => $width,
                'height'    => $height,
                'thumbnail' => URUS_IMAGES . '/product-placeholder.jpg',
                'title'     => get_bloginfo('name')
            );
            $custom_placeholder = Urus_Helper::get_option('custom_placeholder_url', $default_custom_placeholder );

            // Add filter

            if ( $custom_placeholder['url'] != '' ) {
                $cropped_img_path = $custom_placeholder['url'];
            }else{
                $cropped_img_path = $default_custom_placeholder['url'];
            }
            $url_placeholder = esc_url($cropped_img_path);

            return $url_placeholder;
        }

        public static function urus_loop_product_select_option(){
            $enable_variation_loop_product = Urus_Helper::get_option('enable_variation_loop_product',0);
            if ( $enable_variation_loop_product == 0 ) return;
            $enable_quick_add_loop_product = Urus_Helper::get_option('enable_quick_add_loop_product',0);
            if ( $enable_quick_add_loop_product == 0 ) return;
            global $product;
            $product_type = $product->get_type();
            if( $product_type != 'variable'){
                return;
            }
            ob_start();
            ?>
            <div class="select-option-extend">
                <div class="variation-form">
                    <?php
                        do_action('urus_variation_swatches_loop_item');
                        do_action('urus_variation_form_loop_item');
                    ?>
                    <div class="woocommerce-variation single_variation"></div>
                    <a href="javascript:void(0);" class="variation-form-submit button small"><?php esc_html_e('Add to cart', 'urus'); ?></a>
                </div>
                <a href="javascript:void(0);" class="close-form"><?php esc_html_e('Close', 'urus'); ?> <?php echo familab_icons('close'); ?> </a>
            </div>
            <?php
            $html = ob_get_clean();
            echo Urus_Helper::escaped_html($html);
        }
        public static function get_style_product_item_buttons(){
            $woo_product_item_background_btn = Urus_Helper::get_option('woo_product_item_background_btn','light');
            $woo_product_item_background_btn = apply_filters('woo_product_item_background_btn',$woo_product_item_background_btn);
            return $woo_product_item_background_btn;
        }

        public static function woo_sidebar_option_layout(){
            $woo_shop_layout = Urus_Helper::get_option('woo_shop_layout','left');
            $woo_shop_used_sidebar = Urus_Helper::get_option('woo_shop_used_sidebar','shop-widget-area');

            if( is_product()){
                $woo_shop_layout = Urus_Helper::get_option('woo_single_layout','left');
                $woo_shop_used_sidebar = Urus_Helper::get_option('woo_single_used_sidebar','shop-widget-area');
                if (self::$mobile_template ) {
                    $woo_shop_layout = 'full';
                }
            }
            if ( !is_active_sidebar( $woo_shop_used_sidebar )){
                $woo_shop_layout = 'full';
            }
            return $woo_shop_layout;
        }
        public static function sub_categories_row(){
            $display_type = woocommerce_get_loop_display_mode();
            $display_type = apply_filters('urus_woocommerce_get_loop_display_mode',$display_type);

            // If displaying categories, append to the loop.
            if ( 'subcategories' === $display_type || 'both' === $display_type ) {
                $before        = '<ul class="urus-categories row" >';
                $after = '</ul>';

                Urus_Pluggable_WooCommerce::woocommerce_output_product_categories ( array(
                    'before'    => $before,
                    'after'     => $after,
                    'parent_id' => is_product_category() ? get_queried_object_id() : 0,
                ) );
            }
        }

        /**
         * Display product sub categories as thumbnails.
         *
         * This is a replacement for woocommerce_product_subcategories which also does some logic
         * based on the loop. This function however just outputs when called.
         *
         * @since 3.3.1
         * @param array $args Arguments.
         * @return boolean
         */
        public static function woocommerce_output_product_categories( $args = array() ) {
            $args = wp_parse_args( $args, array(
                'before'    => apply_filters( 'woocommerce_before_output_product_categories', '' ),
                'after'     => apply_filters( 'woocommerce_after_output_product_categories', '' ),
                'parent_id' => 0,
            ) );

            $product_categories = woocommerce_get_product_subcategories( $args['parent_id'] );

            if ( ! $product_categories ) {
                return false;
            }

            echo e_data($args['before']); // WPCS: XSS ok.

            foreach ( $product_categories as $category ) {
                wc_get_template( 'urus-content-product-cat.php', array(
                    'category' => $category,
                ) );
            }

            echo e_data($args['after']); // WPCS: XSS ok.

            return true;
        }

        public static function urus_show_product_video() {
            global $product;
            $_feature_video_url = get_post_meta($product->get_id(),'_feature_video_url',true);
            if( $_feature_video_url !=''){
                ?>
                <a class="product-video-button" href="<?php echo esc_url($_feature_video_url);?>"><?php esc_html_e('Play Video','urus');?></a>
                <?php
            }
        }
        public static function urus_show_product_360deg() {
            global  $product;
            $images = get_post_meta($product->get_id(),'_gallery_360degree',true);
            $id = $product->get_id();
            $i                = 0;
            $images_js_string = '';
            if( !empty($images)){
                $frames_count     = count( $images );
                ?>
                <a class="product-360-button" href="#single-product-360-view"><?php esc_html_e('360 Degree','urus');?></a>
                <div id="single-product-360-view" class="product-360-view-wrapper mfp-hide">
                    <div class="urus-threed-view threed-id-<?php echo esc_attr( $id ); ?>">
                        <ul class="threed-view-images">
                            <?php if ( count( $images ) > 0 ) {
                                foreach ( $images as $img_id ) {
                                    $i++;
                                    $img              = wp_get_attachment_image_src( $img_id, 'full' );
                                    $images_js_string .= "'" . $img[0] . "'";
                                    $width            = $img[1];
                                    $height           = $img[2];
                                    if ( $i < $frames_count ) {
                                        $images_js_string .= ",";
                                    }
                                }
                            } ?>
                        </ul>
                        <div class="spinner">
                            <span>0%</span>
                        </div>
                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            window.addEventListener('load',
                                function (ev) {
                                    jQuery('.threed-id-<?php echo esc_attr( $id ); ?>').ThreeSixty({
                                        totalFrames: <?php echo esc_attr( $frames_count ); ?>,
                                        endFrame: <?php echo esc_attr( $frames_count ); ?>,
                                        currentFrame: 1,
                                        imgList: '.threed-view-images',
                                        progress: '.spinner',
                                        imgArray: [<?php echo wp_specialchars_decode( $images_js_string ); ?>],
                                        height: <?php echo esc_attr( $height ); ?>,
                                        width: <?php echo esc_attr( $width ); ?>,
                                        responsive: true,
                                        navigation: true
                                    });
                                }, false);
                        });
                    </script>
                </div>
                <?php
            }

        }
        public static function urus_show_product_promo() {
            global  $product;
            $all_metas = get_post_meta($product->get_id(),'_promo_info',true);
            if ($all_metas == ''){
                return;
            }
            ?>
            <div class="single-promo">
                <?php
                    echo do_shortcode($all_metas);
                ?>
            </div>
            <?php
        }
        public static function wc_body_class( $classes ) {
            $classes = (array) $classes;
            if ( is_shop() || is_product_category() || is_tax('product-brand') || is_tax('product_tag')) {
                $shop_heading_style = Urus_Helper::get_option('shop_heading_style','banner');
                $classes[] = 'shop-heading-'.$shop_heading_style;
                $classes[] = 'woocommerce-shop';
            }
            return array_unique( $classes );
        }

        public static function product_mobile_carousel_options($options) {
            $options['animation'] = 'slide';
            $options['controlNav'] = true;
            $options['slideshow'] = true;
          return $options;
        }
        public static function product_mobile_main_container_class($classes) {
            $classes[] = 'urus-single-product-mobile';
          return $classes;
        }

        public static function dequeue_styles( $enqueue_styles ) {
            unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
            unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
            unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
            return $enqueue_styles;
        }

        public static function woocommerce_shop_heading(){
            if( is_product()) return '';
            $shop_heading_style = Urus_Helper::get_option('shop_heading_style','banner');
            $display_categories = Urus_Helper::get_option('display_categories',0);
            $shop_heading_dark = Urus_Helper::get_option('shop_heading_dark',0);
            $display_categories_style = Urus_Helper::get_option('display_categories_style','simple');
            $shop_heading_overlay = Urus_Helper::get_option('shop_heading_overlay',0);

            $class = array('blog-heading shop-heading');
            if(!is_product()){
                $class[] = $shop_heading_style;
            }
            if( is_product()){
                $class[] ='shop-single-heading';
            }

            if( $display_categories ==1 && $shop_heading_style == 'banner'){
                $class[] ='has-categories';
                $class[] ='display-categories-'.$display_categories_style;
            }

            if( $shop_heading_dark == 1){
                $class[] ='dark';
            }
            if( $shop_heading_overlay == 1){
                $class[] ='overlay';
            }
            $shop_heading_background = Urus_Helper::get_option('shop_heading_background',array());

            if( class_exists('WooCommerce') && is_product_category()){
                global  $wp_query;
                $current_cat   = $wp_query->queried_object;
                if( !is_wp_error($current_cat) && isset($current_cat->term_id)){
                    $current_cat_id = $current_cat->term_id;
                    $category_background = absint( get_term_meta( $current_cat_id, 'category_background', true ) );
                    if( $category_background  > 0){
                        if( !empty($shop_heading_background)){
                            $shop_heading_background['background-image'] = wp_get_attachment_url($category_background);
                        }else{
                            $shop_heading_background = array(
                                'background-image' => wp_get_attachment_url($category_background),
                                'background-color' => '',
                                'background-repeat' => 'no-repeat',
                                'background-size' => 'cover',
                                'background-attachment' =>'center center',
                                'background-position' => ''
                            );
                        }
                    }
                }
            }
            $css ="style='";
            if( !empty($shop_heading_background) && $shop_heading_style == 'banner'){
                if( isset($shop_heading_background['background-color']) && $shop_heading_background['background-color']!=''){
                    $css.='background-color:'.$shop_heading_background['background-color'].';';
                }
                if( isset($shop_heading_background['background-repeat']) && $shop_heading_background['background-repeat']!=''){
                    $css.='background-repeat:'.$shop_heading_background['background-repeat'].';';
                }
                if( isset($shop_heading_background['background-size']) && $shop_heading_background['background-size']!=''){
                    $css.='background-size:'.$shop_heading_background['background-size'].';';
                }
                if( isset($shop_heading_background['background-attachment']) && $shop_heading_background['background-attachment']!=''){
                    $css.='background-attachment:'.$shop_heading_background['background-attachment'].';';
                }
                if( isset($shop_heading_background['background-position']) && $shop_heading_background['background-position']!=''){
                    $css.='background-position:'.$shop_heading_background['background-position'].';';
                }
                if( isset($shop_heading_background['background-image']) && $shop_heading_background['background-image']!='') {
                    $css .= 'background-image: url("' . $shop_heading_background['background-image'] . '");';
                }
            }
            $css .="'";
            ?>
            <div <?php echo e_data($css);?> class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
                <div class="inner container">
                    <?php do_action('urus_before_shop_heading');?>
                    <?php if (function_exists('wcfmmp_is_stores_list_page') && wcfmmp_is_stores_list_page()) :?>
                        <h1 class="page-title"><?php single_post_title(); ?></h1>
                    <?php else:?>
                        <?php if(!is_product()):?>
                            <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
                        <?php endif;
                    endif;
                    if(!is_product()){
                        Urus_Pluggable_WooCommerce::woocommerce_breadcrumb();
                    }
                    do_action('urus_after_shop_heading');?>
                </div>
            </div>
            <?php
        }

        public static function woocommerce_single_product_carousel_options($args){
            $args['directionNav'] = true;
            return $args;
        }

        public static function shop_control_top(){
            $loop_display_mode = woocommerce_get_loop_display_mode();
            if( $loop_display_mode == 'subcategories') return '';
            $enable_shop_filter = Urus_Helper::get_option('enable_shop_filter',0);
            $shop_control_class = array('shop-control');
            $shop_control_class[] ='top';
            if( $enable_shop_filter == 1){
                $shop_control_class[]='has-filter';
            }
            $shop_control_class = apply_filters('urus_shop_control_top_class',$shop_control_class);
            ?>
            <div class="urus_shop_control_top_wrapper">
                <div class="<?php echo esc_attr( implode( ' ', $shop_control_class ) ); ?>">
                    <?php
                    /**
                     * urus_shop_control_top hook.
                     * @hooked Urus_Pluggable_WooCommerce::woocommerce_page_title - 1
                     * @hooked woocommerce_catalog_ordering - 10
                     */
                        do_action('urus_shop_control_top');
                    ?>
                </div>
                <?php
                do_action('urus_after_shop_control_top');
                ?>
            </div>
            <?php
        }
        public static function shop_control_bottom(){
            $shop_control_class = array('shop-control');
            $shop_control_class[] ='bottom';
            $shop_control_class = apply_filters('urus_shop_control_bottom_class',$shop_control_class);
            do_action('urus_before_shop_control_bottom');
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $shop_control_class ) ); ?>">
                <?php
                /**
                 * urus_shop_control_bottom hook.
                 * @hooked Urus_Pluggable_WooCommerce::woocommerce_pagination - 10
                 */
                 do_action('urus_shop_control_bottom');
                 ?>
            </div>
            <?php
           do_action('urus_after_shop_control_bottom');
        }

        public static function woocommerce_show_page_title(){
            return false;
        }

        public static function woocommerce_page_title(){
            ?>
            <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
            <?php
        }

        public static  function loop_shop_per_page($perpage){
            $perpage = Urus_Helper::get_option('woo_products_perpage',12);
            return $perpage;
        }

        public static function product_loop_sale_flash(){
            $class = array('flashs');
            $class = apply_filters('urus_product_loop_group_flash',$class);
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
            <?php
            /**
             * urus_product_loop_group_flash_content hook.
             * @hooked woocommerce_show_product_loop_sale_flash - 10
             * @hooked Urus_Pluggable_WooCommerce::woocommerce_show_product_loop_new_flash - 15
             */
            do_action('urus_product_loop_group_flash_content');
            ?>
            </div>
            <?php
            $html = apply_filters('urus_product_loop_group_flash_html_content',ob_get_contents());
            ob_get_clean();
            echo Urus_Helper::escaped_html($html);
        }

        public static function product_loop_stock_status(){
            $display_out_of_stock = Urus_Helper::get_option('display_out_of_stock',false);
            if ($display_out_of_stock){
                global $product;
                $P_stock_status =  $product->get_stock_status();
                if ('outofstock' == $P_stock_status){
                    ?>
                    <div class="urus-stock-status">
                        <span class="on_out_of_stock">
                            <?php esc_html_e( 'Out of stock', 'urus' )?>
                        </span>
                    </div>
                    <?php
                }
            }
        }
        public static function woocommerce_show_product_loop_new_flash(){
            $disable_new = Urus_Helper::get_option('disable_new_label',false);
            if ($disable_new)
                return;
            global $post, $product;
            $postdate 		= get_the_time( 'Y-m-d' );			// Post date
            $postdatestamp 	= strtotime( $postdate );			// Timestamped post date
            $newness 		= Urus_Helper::get_option( 'woo_newness', 7 ); 	// Newness in days as defined by option
            ?>
            <?php if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp ) : ?>
                <?php echo apply_filters( 'woocommerce_new_flash', '<span class="new"><span class="text">' . esc_html__( 'New', 'urus' ) . '</span></span>', $post, $product ); ?>
            <?php endif; ?>
            <?php
        }

        public static function template_loop_product_thumbnail_classic(){
            global $product;
            // GET SIZE IMAGE SETTING
            $width  = 300;
            $height = 300;
            $crop   = true;
            $size   = wc_get_image_size( 'shop_catalog' );
            if ( $size ) {
                $width  = $size['width'];
                $height = $size['height'];
                if ( !$size['crop'] ) {
                    $crop = false;
                }
            }

            $width       = apply_filters( 'urus_shop_product_thumb_width', $width );
            $height      = apply_filters( 'urus_shop_product_thumb_height', $height );
            $enable_lazy = Urus_Helper::get_option( 'theme_use_lazy_load',0 );

            if(isset($_GET['action']) && $_GET['action'] =='elementor'){
                $enable_lazy = false;
            }

            $image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $product->get_id()) , $width, $height, $crop, $enable_lazy);

            $thumb_class = array('thumb-link');
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $thumb_class ) ); ?>">
                <div class='images'>
                    <a class="woocommerce-product-gallery__image" href="<?php the_permalink(); ?>">
                        <figure class="main-thumb" data-id="<?php echo esc_attr($product->get_id());?>">
                            <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                        </figure>
                    </a>
                </div>
            </div>
            <?php
        }

        public static function template_loop_product_thumbnail(){

            $woo_product_item_image = Urus_Helper::get_option('woo_product_item_image','classic');
            $woo_product_item_image  = apply_filters('woo_product_item_image_in_loop',$woo_product_item_image);

            switch ($woo_product_item_image){
                case "slider":
                    Urus_Pluggable_WooCommerce::template_loop_product_image_slider();
                    break;
                case "gallery":
                    Urus_Pluggable_WooCommerce::template_loop_product_image_gallery();
                    break;
                case "zoom":
                    Urus_Pluggable_WooCommerce::template_loop_product_image_zoom();
                    break;
                case "secondary_image":
                    Urus_Pluggable_WooCommerce::template_loop_product_secondary_image();
                    break;
                default:
                    Urus_Pluggable_WooCommerce::template_loop_product_thumbnail_classic();
            }
        }

        public static function template_loop_product_title(){
            echo '<h2 class="woocommerce-loop-product__title product-name"><a href="' .get_the_permalink() . '">'. get_the_title().'</a></h2>';
        }

        public static function template_loop_product_except(){
            echo '<div class="item_decs">'.wp_strip_all_tags(get_the_excerpt()).'</div>';
        }
        public static function template_loop_product_secondary_image(){
            global $product;
            // GET SIZE IMAGE SETTING
            $width  = 300;
            $height = 300;
            $crop   = true;
            $size   = wc_get_image_size( 'shop_catalog' );
            if ( $size ) {
                $width  = $size['width'];
                $height = $size['height'];
                if ( !$size['crop'] ) {
                    $crop = false;
                }
            }
            $width       = apply_filters( 'urus_shop_product_thumb_width', $width );
            $height      = apply_filters( 'urus_shop_product_thumb_height', $height );
            $enable_lazy = Urus_Helper::get_option( 'theme_use_lazy_load',0 );

            if(isset($_GET['action']) && $_GET['action'] =='elementor'){
                $enable_lazy = false;
            }
            $image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $product->get_id()) , $width, $height, $crop, $enable_lazy);

            $thumb_class = array('thumb-link');
            $attachment_ids = $product->get_gallery_image_ids();

            $second_image_thumb = false;
            if( !empty($attachment_ids)  && !wp_is_mobile() ){
                $back_attachment_id = isset($attachment_ids[0])? $attachment_ids[0] :0;
                if($back_attachment_id > 0){
                    $second_image_thumb = Urus_Helper::resize_image($back_attachment_id , $width, $height, $crop, true);
                    $thumb_class[] = 'has-second-image';
                }

            }

            ?>
            <div class="<?php echo esc_attr( implode( ' ', $thumb_class ) ); ?>">
                <div class='images'>
                    <a class="woocommerce-product-gallery__image" href="<?php the_permalink(); ?>">
                        <figure class="main-thumb" data-id="<?php echo esc_attr($product->get_id());?>">
                            <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                        </figure>
                    </a>
                    <?php if ($second_image_thumb):?>
                        <a class="" href="<?php the_permalink(); ?>">
                            <figure class="second-thumb">
                                <?php echo Urus_Helper::escaped_html($second_image_thumb['img']);?>
                            </figure>
                        </a>
                    <?php endif;?>
                </div>
            </div>
            <?php
        }

        public static function template_loop_product_image_zoom(){
            global $product;
            // GET SIZE IMAGE SETTING
            $width  = 300;
            $height = 300;
            $crop   = true;
            $size   = wc_get_image_size( 'shop_catalog' );
            if ( $size ) {
                $width  = $size['width'];
                $height = $size['height'];
                if ( !$size['crop'] ) {
                    $crop = false;
                }
            }
            $width       = apply_filters( 'urus_shop_product_thumb_width', $width );
            $height      = apply_filters( 'urus_shop_product_thumb_height', $height );
            $enable_lazy = Urus_Helper::get_option( 'theme_use_lazy_load',0 );

            if(isset($_GET['action']) && $_GET['action'] =='elementor'){
                $enable_lazy = false;
            }
            $image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $product->get_id()) , $width, $height, $crop, $enable_lazy);

            $thumb_class = array('thumb-link zoom');

            ?>
            <div class="<?php echo esc_attr( implode( ' ', $thumb_class ) ); ?>">
                <div class='images'>
                    <a class="product-item-zoom" href="<?php the_permalink(); ?>" data-src="<?php echo esc_url(wp_get_attachment_url(get_post_thumbnail_id( $product->get_id()))); ?>">
                        <figure class="main-thumb" data-id="<?php echo esc_attr($product->get_id());?>">
                            <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                        </figure>
                    </a>
                </div>
            </div>
            <?php
        }

        public static function template_loop_product_image_slider(){

            global $product;

            // GET SIZE IMAGE SETTING
            $width  = 300;
            $height = 300;
            $crop   = true;
            $size   = wc_get_image_size( 'shop_catalog' );
            if ( $size ) {
                $width  = $size['width'];
                $height = $size['height'];
                if ( !$size['crop'] ) {
                    $crop = false;
                }
            }
            $width       = apply_filters( 'urus_shop_product_thumb_width', $width );
            $height      = apply_filters( 'urus_shop_product_thumb_height', $height );

            $enable_lazy = Urus_Helper::get_option( 'theme_use_lazy_load',0 );

            if(isset($_GET['action']) && $_GET['action'] =='elementor'){
                $enable_lazy = false;
            }

            $attachment_ids = $product->get_gallery_image_ids();
            if ( $attachment_ids) {
                global $urus_product_items_carousel_settings;
                if (empty($urus_product_items_carousel_settings)){
                    $data_slick = array(
                        'loop'         => 'false',
                        'ts_items'     => 1,
                        'xs_items'     => 1,
                        'sm_items'     => 1,
                        'md_items'     => 1,
                        'lg_items'     => 1,
                        'ls_items'     => 1,
                        'navigation'   => 'true',
                        'slide_margin' => 4,
                        'dots' => 'false'
                    );
                    $carousel_settings = Urus_Helper::carousel_data_attributes('',$data_slick);
                    $GLOBALS['urus_product_items_carousel_settings'] = $carousel_settings;
                }else{
                    $carousel_settings = $urus_product_items_carousel_settings;
                }
                $image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $product->get_id()) , $width, $height, $crop, $enable_lazy);
                ?>
                <div class="urus-swiper swiper-container nav-center thumb-link urus-product-item-slider" <?php echo esc_attr($carousel_settings);?> data-main_class = "main-thumb">
                    <div class="swiper-wrapper">
                        <div class="slide_img swiper-slide">
                            <figure data-id="<?php echo esc_attr($product->get_id());?>">
                                <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                            </figure>
                        </div>
                        <?php
                            foreach ( $attachment_ids as $attachment_id ) {
                                $image_thumb = Urus_Helper::resize_image($attachment_id , $width, $height, $crop, $enable_lazy); ?>
                                <div class="slide_img swiper-slide" data-id_img="<?php echo esc_attr($attachment_id); ?>">
                                    <figure data-id="<?php echo esc_attr($product->get_id());?>">
                                        <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                                    </figure>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <div class="slick-arrow next">
                    <?php echo familab_icons('arrow-right'); ?>
                </div>
                <div class="slick-arrow prev">
                    <?php echo familab_icons('arrow-left'); ?>
                </div>
                <?php
            }else{
                $image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $product->get_id()) , $width, $height, $crop, $enable_lazy);
                $thumb_class = array('thumb-link');
                ?>
                <div class="<?php echo esc_attr( implode( ' ', $thumb_class ) ); ?>">
                    <div class='images'>
                        <a class="woocommerce-product-gallery__image" href="<?php the_permalink(); ?>">
                            <figure class="main-thumb" data-id="<?php echo esc_attr($product->get_id());?>">
                                <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                            </figure>
                        </a>
                    </div>
                </div>
                <?php
            }
        }

        public static function template_loop_product_image_gallery(){
            global $product;
            // GET SIZE IMAGE SETTING
            $width  = 300;
            $height = 300;
            $crop   = true;
            $size   = wc_get_image_size( 'shop_catalog' );
            if ( $size ) {
                $width  = $size['width'];
                $height = $size['height'];
                if ( !$size['crop'] ) {
                    $crop = false;
                }
            }
            $width       = apply_filters( 'urus_shop_product_thumb_width', $width );
            $height      = apply_filters( 'urus_shop_product_thumb_height', $height );

            $enable_lazy = Urus_Helper::get_option( 'theme_use_lazy_load',0 );

            if(isset($_GET['action']) && $_GET['action'] =='elementor'){
                $enable_lazy = false;
            }
            $attachment_ids = $product->get_gallery_image_ids();
            if ( $attachment_ids) {
                $main_image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $product->get_id()) , $width, $height, $crop, $enable_lazy);
                ?>
                <div class="swiper-container urus-gallery-top" data-product_id="<?php echo esc_attr($product->get_id());?>">
                    <div class="swiper-wrapper">
                        <div class="slide_img swiper-slide">
                            <a class="item-gallery-lnk" href="<?php the_permalink(); ?>">
                            <figure data-id="<?php echo esc_attr($product->get_id());?>">
                                <?php echo Urus_Helper::escaped_html($main_image_thumb['img']);?>
                            </figure>
                            </a>
                        </div>
                        <?php
                        foreach ( $attachment_ids as $attachment_id ) {
                            $image_thumb = Urus_Helper::resize_image($attachment_id , $width, $height, $crop, $enable_lazy); ?>
                            <div class="slide_img swiper-slide" data-id_img="<?php echo esc_attr($attachment_id); ?>">
                                <figure data-id="<?php echo esc_attr($product->get_id());?>">
                                    <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                                </figure>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="swiper-container urus-gallery-thumbs">
                    <div class="swiper-wrapper">
                        <div class="slide_img swiper-slide">
                            <figure data-id="<?php echo esc_attr($product->get_id());?>">
                                <?php echo Urus_Helper::escaped_html($main_image_thumb['img']);?>
                            </figure>
                        </div>
                        <?php
                        foreach ( $attachment_ids as $attachment_id ) {

                            $image_thumb = Urus_Helper::resize_image($attachment_id , $width, $height, $crop, $enable_lazy); ?>
                            <div class="slide_img swiper-slide">
                                <figure data-id="<?php echo esc_attr($product->get_id());?>">
                                    <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                                </figure>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }else{
                $image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $product->get_id()) , $width, $height, $crop, $enable_lazy);
                $thumb_class = array('thumb-link');
                ?>
                <div class="<?php echo esc_attr( implode( ' ', $thumb_class ) ); ?>">
                    <div class='images'>
                        <a class="woocommerce-product-gallery__image" href="<?php the_permalink(); ?>">
                            <figure class="main-thumb" data-id="<?php echo esc_attr($product->get_id());?>">
                                <?php echo Urus_Helper::escaped_html($image_thumb['img']);?>
                            </figure>
                        </a>
                    </div>
                </div>
                <?php
            }
        }

        public static function template_loop_count_down(){
            global $product;
	        $date = Urus_Pluggable_WooCommerce::get_max_date_sale($product);
	        if ($date){
	            echo "<div class='urus-countdown product-deal-countdown' data-datetime='".date( 'm/j/Y g:i:s', $date)."'></div>";
            }
        }

        public static function woocommerce_breadcrumb(){
            $args = array(
                'delimiter'   => '',
                'wrap_before' => '<ul class="woocommerce-breadcrumb breadcrumbs">',
                'wrap_after'  => '</ul>',
                'before'      => '<li>',
                'after'       => '</li>',
            );
            woocommerce_breadcrumb( $args );
        }

        public static function woocommerce_pagination(){
            global $wp_query;
            $woo_shop_infinite_load = Urus_Helper::get_option('woo_shop_infinite_load','default');
            $max_num_page = $wp_query->max_num_pages;
            $query_paged  = $wp_query->query_vars['paged'];
            if( $query_paged >= 0 && ($query_paged < $max_num_page)){
                $show_button = '1';
            }else{
                $show_button = '0';
            }
            if( $max_num_page <=1){
                $show_button = 0;
            }
            ob_start();
            if ( $wp_query->max_num_pages > 1 ) {
                ?>
                <?php if( $woo_shop_infinite_load =='default'):?>
                <nav class="woocommerce-pagination navigation pagination">
                    <div class="nav-links">
                    <?php
                    echo paginate_links( apply_filters( 'woocommerce_pagination_args', array( 'base' => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ), 'format' => '', 'add_args' => false, 'current' => max( 1, get_query_var( 'paged' ) ), 'total' => $wp_query->max_num_pages, 'prev_text' => '<i class="fa fa-caret-left"></i>', 'next_text' => '<i class="fa fa-caret-right"></i>', 'type' => 'plain', 'end_size' => 3, 'mid_size' => 3, ) ) );
                    ?>
                    </div>
                </nav>
                <?php endif;?>
                <?php if( $woo_shop_infinite_load!='default'): ?>
                <div id="urus-infload-controls">
                    <div class="infload-link"><?php next_posts_link( '&nbsp;' ); ?></div>
                    <div class="infload-controls <?php echo esc_attr($woo_shop_infinite_load)?>-mode <?php if( $show_button == 0):?> hide-btn<?php endif;?>">
                        <?php next_posts_link( esc_html__('Load More','urus') ); ?>
                        <a href="#" class="infload-to-top"><?php esc_html_e( 'All products loaded.', 'urus' ); ?></a>
                    </div>
                </div>
                <?php endif;
            }
		    echo apply_filters( 'urus_custom_woocommerce_pagination', ob_get_clean() );
	    }

        public static function woocommerce_breadcrumbs( $defaults ){
            $defaults['delimiter'] = '';
            $defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb breadcrumbs" itemprop="breadcrumb">';
            $defaults['wrap_after'] = '</nav>';
            $defaults['before'] = '';
            $defaults['after'] = '';
            return $defaults;
        }

        public static function shop_now_button(){
            ?>
            <a class="shop-now-button" href="<?php the_permalink();?>"><?php esc_html_e('Shop now','urus');?> <i class="fa fa-caret-right"></i></a>
            <?php
        }

        public static function woocommerce_product_get_rating_html($html, $rating, $count){
            $html  = '<div class="star-rating">';
            $html .= wc_get_star_rating_html( $rating, $count );
            $html .= '</div>';
            return $html;
        }

        public static function shop_column_select(){
            $woo_shop_list_style = Urus_Helper::get_option_c('woo_shop_list_style','grid');
            if( $woo_shop_list_style =='list' || $woo_shop_list_style =='masonry') return '';
            $woo_lg_items = Urus_Helper::get_option_c('woo_lg_items',4);
            $query_string = Urus_Pluggable_WooCommerce::get_query_string(null,array('woo_lg_items'));
            $current_link = self::get_current_page_url();
            $link =add_query_arg($query_string,$current_link);
            $woo_shop_layout = Urus_Helper::get_option('woo_shop_layout','left');
            ?>
            <div class="urus-shop-column shop-action">
                <a class="show_shop_action switch-column hint--top hint-bounce" aria-label="<?php esc_html_e('Column number','urus')?>" href="#" ><?php echo familab_icons('grid');?></a>
                <div class="shop_action_container">
                    <a class="switch-column <?php if($woo_lg_items==6):?>selected<?php endif;?>" href="<?php echo esc_url(add_query_arg(array('woo_lg_items'=>'2'),$link));?>" data-col="6"><?php esc_html_e('2','urus');?></a>
                    <a class="switch-column <?php if($woo_lg_items==4):?>selected<?php endif;?>" href="<?php echo esc_url(add_query_arg(array('woo_lg_items'=>'4'),$link));?>"  data-col="4"><?php esc_html_e('3','urus');?></a>
                    <a class="switch-column <?php if($woo_lg_items==3):?>selected<?php endif;?>" href="<?php echo esc_url(add_query_arg(array('woo_lg_items'=>'3'),$link));?>" data-col="3"><?php esc_html_e('4','urus');?></a>
                    <?php if( $woo_shop_layout =='full'):?>
                        <a class="switch-column <?php if($woo_lg_items==15):?>selected<?php endif;?>" href="<?php echo esc_url(add_query_arg(array('woo_lg_items'=>'15'),$link));?>" data-col="15"><?php esc_html_e('5','urus');?></a>
                    <?php endif;?>
                </div>
            </div>
            <?php
        }

        public static function shop_list_mode(){
            $query_string = Urus_Pluggable_WooCommerce::get_query_string(null,array('woo_shop_list_style'));
            $current_link = self::get_current_page_url();
            $link =add_query_arg($query_string,$current_link);
            $woo_shop_list_style = Urus_Helper::get_option('woo_shop_list_style','grid');
            ?>
            <div class="shop-list-mode shop-action">
                <a class="switch-mod grid <?php if($woo_shop_list_style =='grid'):?>selected<?php endif;?>" href="<?php echo esc_url(add_query_arg(array('woo_shop_list_style' => 'grid'),$link));?>"><?php echo familab_icons('grid');?><?php esc_html_e('Grid','urus');?></a>
                <a class="switch-mod list <?php if($woo_shop_list_style =='list'):?>selected<?php endif;?>" href="<?php echo esc_url(add_query_arg(array('woo_shop_list_style' => 'list'),$link));?>"><?php echo familab_icons('list');?><?php esc_html_e('List','urus');?></a>
            </div>
            <?php
        }

        public static function shop_products_per_page(){
            $woo_products_perpage = Urus_Helper::get_option('woo_products_perpage',12);
            $default = array(
                    "$woo_products_perpage" => $woo_products_perpage.' '.esc_html__('Items','urus')
            );
            $extend = array(
                '16' => esc_html__('16 Items','urus'),
                '20' => esc_html__('20 Items','urus'),
                '24' => esc_html__('24 Items','urus'),
            );

            $woo_products_perpage_list = $default + $extend;
            ?>
                <form class="urus-shop-products-per-page" action="">
                    <?php esc_html_e('View:','urus');?>
                    <?php if( !empty($woo_products_perpage_list)):?>
                    <select name="woo_products_perpage" id="urus-shop-products-per-page"  onchange="this.form.submit();" >
                        <?php foreach ( $woo_products_perpage_list as $key => $item):?>
                        <option<?php if( $woo_products_perpage == $key ):?> selected <?php endif;?> value="<?php echo esc_attr($key)?>"><?php echo esc_html($item)?></option>
                        <?php endforeach;?>
                    </select>
                    <?php endif;?>
                    <?php esc_html_e('per page','urus');?>
                    <?php wc_query_string_form_fields(null,array('woo_products_perpage'));?>
                </form>
            <?php
        }

        public static function shop_filter_control(){
            $woo_sidebar_option_layout = apply_filters('woo_sidebar_option_layout','left');
            $shop_filter_style = Urus_Helper::get_option('shop_filter_style','dropdown');
            $control_class = array();
            $control_class[] = 'shop-action';
            $control_class[] = 'auto-clear';
            $control_class[] = 'block-filter-'.$shop_filter_style;
            $control_class = apply_filters('urus_shop_control_class',$control_class);
            if ($woo_sidebar_option_layout == 'full'){
            // Get widgets active
            ?>
            <div class="<?php echo esc_attr(implode(' ', $control_class)); ?>">
                <?php if( $shop_filter_style =='accordion'):?>
                    <?php
                    $widget_active = Urus_Pluggable_WooCommerce::get_widgets_active('accordion_filter');
                    ?>
                    <ul class="urus-filter-accordion">
                          <li class="labels-filter-text"><?php esc_html_e('Filter by:','urus');?></li>
                          <?php if( !empty($widget_active)):?>
                          <?php foreach ( $widget_active as $widget):?>
                          <li class="widget-toggle" data-id="<?php echo esc_attr($widget['key']);?>"><a href="#<?php echo esc_attr($widget['key']);?>"><?php echo esc_html($widget['title']);?></a></li>
                          <?php endforeach;?>
                          <?php endif;?>
                         <?php if(is_active_sidebar('accordion_filter_all')):?>
                             <li class="all-filter">
                                 <a href="#"><?php esc_html_e('All Filters','urus');?></a>
                             </li>
                         <?php endif;?>
                    </ul>
                    <?php if(is_active_sidebar('accordion_filter_all')):?>
                        <?php
                            $widget_active = Urus_Pluggable_WooCommerce::get_widgets_active('accordion_filter_all');
                         ?>
                         <?php if( !empty($widget_active)):?>
                        <div class="accordion-filter-all canvas-box">
                            <div class="accordion-filter-all-content">
                                <div class="sidedrawer__head">
                                    <a class="sidedrawer__heading_prev" href="#"><?php esc_html_e('Prev','urus');?></a>
                                    <span class="sidedrawer__heading" data-title="<?php esc_attr_e('Filter & Sort','urus');?>"><?php esc_html_e('Filter & Sort','urus');?></span>
                                    <a class="sidedrawer__heading_close" href="#"><?php esc_html_e('Prev','urus');?></a>
                                </div>
                                <div class="sidedrawer__inner">
                                    <ul class="list-widget">
                                        <?php foreach ($widget_active as $widget):?>
                                            <li class="widget-list-item" data-id="<?php echo esc_attr($widget['key']);?>"><a data-title="<?php echo esc_attr($widget['title']);?>" href="#<?php echo esc_attr($widget['key']);?>"><?php echo esc_html($widget['title']);?></a></li>
                                        <?php endforeach;?>
                                    </ul>
                                    <?php dynamic_sidebar('accordion_filter_all'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="canvas-filter-overlay"></div>
                        <?php endif;?>
                    <?php endif;?>
                <?php elseif($shop_filter_style =='step_filter'):?>
                    <?php woocommerce_result_count();?>
                <?php else:?>
                <a class="<?php echo e_data($shop_filter_style).'-link';?> show-filter-btn" href="#"><?php echo familab_icons('filter');esc_html_e('Filters','urus');?></a>
                <?php
                    self::filter_active();
                endif;?>
            </div>
            <?php
            }
        }

        public static function get_widgets_active($sidebar){
            $widgets = wp_get_sidebars_widgets();
            $widgets =  $widgets[$sidebar];
            $widget_active = array();
            if( !empty($widgets )){
                foreach ( $widgets as $key){
                    $arr = explode('-',$key);

                    if( isset($arr[0]) && $arr[0] !=''){
                        $widget_settings = get_option('widget_'.$arr[0]);
                        if(!empty($widget_settings) && isset($widget_settings[$arr[1]])){
                            $setting = $widget_settings[$arr[1]];
                            $widget_active[] = array(
                              'key' =>       $key,
                              'title' =>     ($setting['title']) ? $setting['title']:'',
                              'settings' => (!empty($setting)) ? $setting : array()
                            );
                        }
                    }

                }
            }
            return $widget_active;
        }

        public static function shop_filter_content(){
            $woo_sidebar_option_layout = apply_filters('woo_sidebar_option_layout','left');
            $shop_filter_style = Urus_Helper::get_option('shop_filter_style','dropdown');
            if( $shop_filter_style =='accordion'){
                ?>
                <div class="urus-block-filter-wapper filter-accordion-content">
                    <?php dynamic_sidebar('accordion_filter'); ?>
                    <?php do_action('urus_after_block_filter');?>
                </div>
                <?php
                self::filter_accordion_active();
                return;
            }
            if ($woo_sidebar_option_layout == 'full'){
                $b_class =  array();
                $b_class[] = 'filter-'.$shop_filter_style.'-content';
                $b_class = apply_filters('urus_filter_content_class',$b_class);
                if ($shop_filter_style == 'drawer'){ ?>
                    <!-- open drawer filter -->
                    <div class="urus_page_content drawer-fillter-wrapper">
                        <div class="filter-drawer-content">
                    <?php
                        add_action('urus_before_shop_control_bottom',function(){
                        ?>
                        </div>
                        </div>
                        <!-- close drawer filter -->
                        <?php
                    });
                }elseif ($shop_filter_style == 'canvas'){
                    $b_class[] = 'canvas-box';
                }
                ?>
                <div class="urus-block-filter-wapper <?php echo esc_attr(implode(' ', $b_class)); ?>">
                    <div class="urus_filter_content_wrapper">
                        <div class="filter-block-head">
                            <span class="text"><?php esc_html_e('Filter', 'urus'); ?></span>
                            <a href="#" class="close-block-filter-<?php echo esc_attr($shop_filter_style);?>"><?php esc_html_e('Close', 'urus'); ?></a>
                        </div>
                        <div class="block-content-waper">
                            <?php if($shop_filter_style=='step_filter'):?>
                                <div class="block-row urus_filter_content row">
                                    <?php dynamic_sidebar('step_filter'); ?>
                                    <?php
                                        $enable_instant_filter = Urus_Helper::get_option('enable_instant_filter',0);
                                        $reset_link = apply_filters('urus_filter_reset_url',self::get_current_page_url(true));
                                        $query_string = Urus_Pluggable_WooCommerce::get_query_string(null, array());
                                        $filter_link = add_query_arg($query_string,Urus_Helper::get_current_page_url());
                                    ?>
                                    <?php if( $enable_instant_filter == 0):?>
                                    <div class="col-sm-15 filter-actions">
                                        <a data-filter="all" id="urus-filter-action" class="button filter" href="<?php echo esc_url($filter_link);?>"><?php esc_html_e('Filter','urus');?></a>
                                        <a data-filter="all" id="urus-filter-reset-action" class="button reset" href="<?php echo esc_url($reset_link);?>"><?php esc_html_e('Reset','urus');?></a>
                                    </div>
                                    <?php endif;?>
                                </div>
                            <?php endif;?>
                            <?php if($shop_filter_style=='dropdown'):?>
                            <div class="block-row urus_filter_content row">
                                <?php dynamic_sidebar('dropdown_filter'); ?>
                            </div>
                            <?php endif;?>
                            <?php if($shop_filter_style =='drawer' || $shop_filter_style =='canvas'):?>
                            <div class="block-row urus_filter_content">
                                <?php dynamic_sidebar('drawer_filter'); ?>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <?php do_action('urus_after_block_filter');?>
                </div>
                <?php if ($shop_filter_style == 'canvas'): ?>
                     <div class="canvas-filter-overlay"></div>
                <?php   endif; ?>
                <?php
            }
        }
        public static function get_categories( $args ,$deprecated = ''){
            $args['taxonomy'] ='product_cat';

            return get_terms( $args,$deprecated);
        }

        /**
         * Get filtered min price for current products.
         * @return int
        */
        public static function get_filtered_price( $args ) {
            global $wpdb, $wp_the_query;

            if ( ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
                $tax_query[] = array(
                    'taxonomy' => $args['taxonomy'],
                    'terms'    => $args['term'],
                    'field'    => 'slug',
                );
            }else{
                $args       = $wp_the_query->query_vars;
            }

            $tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
            $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

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

            if ( $search = WC_Query::get_main_search_query_sql() ) {
                $sql .= ' AND ' . $search;
            }

            return $wpdb->get_row( $sql );
        }

        public static function get_query_string( $values = null, $exclude = array(), $extends = array()){
            if ( is_null( $values ) ) {
                $values = $_GET; // WPCS: input var ok, CSRF ok.
            } elseif ( is_string( $values ) ) {
                $url_parts = wp_parse_url( $values );
                $values    = array();
                if ( ! empty( $url_parts['query'] ) ) {
                    parse_str( $url_parts['query'], $values );
                }
            }
            if( !empty($exclude)) {
                foreach ($exclude as $key => $value) {
                    unset($values[$value]);
                }
            }
            if( !empty($extends)){
                foreach ($extends as $key => $value){
                    $values[$key]= $value;
                }
            }
            unset($values['_pjax']);
            return $values;
        }
        public static function get_catalog_ordering() {
            $show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
            $catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
                'menu_order' => esc_html__( 'Default sorting', 'urus' ),
                'popularity' => esc_html__( 'Sort by popularity', 'urus' ),
                'rating'     => esc_html__( 'Sort by average rating', 'urus' ),
                'date'       => esc_html__( 'Sort by newness', 'urus' ),
                'price'      => esc_html__( 'Sort by price: low to high', 'urus' ),
                'price-desc' => esc_html__( 'Sort by price: high to low', 'urus' ),
            ) );

            $default_orderby = wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', '' ) );
            $orderby         = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

            if ( wc_get_loop_prop( 'is_search' ) ) {
                $catalog_orderby_options = array_merge( array( 'relevance' => esc_html__( 'Relevance', 'urus' ) ), $catalog_orderby_options );

                unset( $catalog_orderby_options['menu_order'] );
            }

            if ( ! $show_default_orderby ) {
                unset( $catalog_orderby_options['menu_order'] );
            }

            if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
                unset( $catalog_orderby_options['rating'] );
            }

            if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
                $orderby = current( array_keys( $catalog_orderby_options ) );
            }
            return array(
                'selected' => $orderby,
                'options' => $catalog_orderby_options
            );
        }

        public static function get_attributes( $atts ){
            global $wpdb;
            $attributes = array();
            if( !empty($atts) ){

                $i=0;
                foreach ($atts as $key => $value) {
                    $args = array(
                        'hide_empty' => 1,
                    );
                    $attribute_taxonomie                 = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies where attribute_name= '".$value."' order by attribute_name ASC;" );
                    $list_attributes                     = get_terms( wc_attribute_taxonomy_name($value),$args );

                    $attributes[$i]['attribute_taxonomie'] = $attribute_taxonomie;
                    $attributes[$i]['list_attributes']    = $list_attributes ;
                    $i++;
                }
            }
            return $attributes;
        }

        public static function shop_categories(){
            $enable_shop_categories = Urus_Helper::get_option('enable_shop_categories',0);
            if( $enable_shop_categories == 0  || is_product()) return;
            wc_get_template_part('products', 'categories' );
        }

        public static function single_category_display(){
            global  $product;
            ?>
            <div class="product-cat">
                <?php echo wc_get_product_category_list( $product->get_id(), '/', '' );?>
            </div>
            <?php
        }
        public static function woocommerce_get_image_size_gallery_thumbnail( $size ){
            $size['width']  = absint( wc_get_theme_support( 'gallery_thumbnail_image_width', 200 ) );
            //woocommerce_thumbnail_image_width
            $cropping      = get_option( 'woocommerce_thumbnail_cropping', '1:1' );

            if ( 'uncropped' === $cropping ) {
                $size['height'] = '';
                $size['crop']   = 0;
            } elseif ( 'custom' === $cropping ) {
                $width          = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
                $height         = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
                $size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
                $size['crop']   = 1;
            } else {
                $cropping_split = explode( ':', $cropping );
                $width          = max( 1, current( $cropping_split ) );
                $height         = max( 1, end( $cropping_split ) );
                $size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
                $size['crop']   = 1;
            }
            return $size;
        }
        public static function woocommerce_get_image_size_single( $size ){
            $size['width']  = absint(get_option('woocommerce_single_image_width','1000'));
            //woocommerce_thumbnail_image_width
            $cropping      = get_option( 'woocommerce_thumbnail_cropping', '1:1' );
            if ( 'uncropped' === $cropping ) {
                $size['height'] = '';
                $size['crop']   = 0;
            } elseif ( 'custom' === $cropping ) {
                $width          = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
                $height         = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
                $size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
                $size['crop']   = 1;
            } else {
                $cropping_split = explode( ':', $cropping );
                $width          = max( 1, current( $cropping_split ) );
                $height         = max( 1, end( $cropping_split ) );
                $size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
                $size['crop']   = 1;
            }
            return $size;
        }

        public static function hiden_tab_content_title( $title){
            return '';
        }

        public static function getProducts( $atts, $args = array(), $ignore_sticky_posts = 1 ){
            extract( $atts );
            $target            = isset( $target ) ? $target : 'recent-product';
            $meta_query        = WC()->query->get_meta_query();
            $tax_query         = WC()->query->get_tax_query();
            $args['post_type'] = 'product';
            if ( isset( $atts['taxonomy'] ) and $atts['taxonomy'] ) {
                $tax_query[] = array(
                    'taxonomy' => 'product_cat',
                    'terms'    => is_array( $atts['taxonomy'] ) ? array_map( 'sanitize_title', $atts['taxonomy'] ) : array_map( 'sanitize_title', explode( ',', $atts['taxonomy'] ) ),
                    'field'    => 'slug',
                    'operator' => 'IN',
                );
            }
            $args['post_status']         = 'publish';
            $args['ignore_sticky_posts'] = $ignore_sticky_posts;
            $args['suppress_filter']     = true;
            if ( isset( $atts['per_page'] ) && $atts['per_page'] ) {
                $args['posts_per_page'] = $atts['per_page'];
            }
            $ordering_args = WC()->query->get_catalog_ordering_args();
            $orderby       = isset( $atts['orderby'] ) ? $atts['orderby'] : $ordering_args['orderby'];
            $order         = isset( $atts['order'] ) ? $atts['order'] : $ordering_args['order'];
            $meta_key      = isset( $atts['meta_key'] ) ? $atts['meta_key'] : $ordering_args['meta_key'];
            switch ( $target ):
                case 'best-selling' :
                    $args['meta_key'] = 'total_sales';
                    $args['orderby']  = 'meta_value_num';
                    $args['order']    = $order;
                    break;
                case 'top-rated' :
                    $args['meta_key'] = '_wc_average_rating';
                    $args['orderby']  = 'meta_value_num';
                    $args['order']    = $order;
                    break;
                case 'product-category' :
                    $args['orderby']  = $orderby;
                    $args['order']    = $order;
                    $args['meta_key'] = $meta_key;
                    break;
                case 'products' :
                    $args['posts_per_page'] = -1;
                    if ( !empty( $ids ) ) {
                        $args['post__in'] = array_map( 'trim', explode( ',', $ids ) );
                        $args['orderby']  = 'post__in';
                    }
                    if ( !empty( $skus ) ) {
                        $meta_query[] = array(
                            'key'     => '_sku',
                            'value'   => array_map( 'trim', explode( ',', $skus ) ),
                            'compare' => 'IN',
                        );
                    }
                    break;
                case 'featured_products' :
                    $tax_query[] = array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                        'operator' => 'IN',
                    );
                    break;
                case 'product_attribute' :
                    $tax_query[] = array(
                        array(
                            'taxonomy' => strstr( $atts['attribute'], 'pa_' ) ? sanitize_title( $atts['attribute'] ) : 'pa_' . sanitize_title( $atts['attribute'] ),
                            'terms'    => $atts['filter'] ? array_map( 'sanitize_title', explode( ',', $atts['filter'] ) ) : array(),
                            'field'    => 'slug',
                            'operator' => 'IN',
                        ),
                    );
                    break;
                case 'on_new' :
                    $newness            =  Urus_Helper::get_option('woo_newness',20);
                    $args['date_query'] = array(
                        array(
                            'after'     => '' . $newness . ' days ago',
                            'inclusive' => true,
                        ),
                    );
                    if ( $orderby == '_sale_price' ) {
                        $orderby = 'date';
                        $order   = 'DESC';
                    }
                    $args['orderby'] = $orderby;
                    $args['order']   = $order;
                    break;
                case 'on_sale' :
                    $product_ids_on_sale = wc_get_product_ids_on_sale();
                    $args['post__in']    = array_merge( array( 0 ), $product_ids_on_sale );
                    if ( $orderby == '_sale_price' ) {
                        $orderby = 'date';
                        $order   = 'DESC';
                    }
                    $args['orderby'] = $orderby;
                    $args['order']   = $order;
                    break;
                default :
                    $args['orderby'] = $orderby;
                    $args['order']   = $order;
                    if ( isset( $ordering_args['meta_key'] ) ) {
                        $args['meta_key'] = $ordering_args['meta_key'];
                    }
                    WC()->query->remove_ordering_args();
                    break;
            endswitch;
            $args['meta_query'] = $meta_query;
            $args['tax_query']  = $tax_query;

            $urus_products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
            wp_reset_postdata();
            return $urus_products;
        }
        public static function header_left_control(){
            $used_header = Urus_Helper::get_option('used_header','default');
            echo '<ul class="header-control-menu urus-nav">';
            if( $used_header =='style3' || $used_header =='style10'){
                echo Urus_Pluggable_WooCommerce::menu_bar_link('menu');
            }else{
                echo Urus_Pluggable_WooCommerce::menu_bar_link('sidebar');
                echo Urus_Pluggable_WooCommerce::search_link();
            }

            echo '</ul>';
        }
        public static function header_control(){
            $used_header = Urus_Helper::get_option('used_header','default');
            echo '<ul class="header-control-menu urus-nav">';
            if( $used_header!='style1' && $used_header!='style8'){
                echo Urus_Pluggable_WooCommerce::search_link();
            }
            echo Urus_Pluggable_WooCommerce::userlink();
            echo Urus_Pluggable_WooCommerce::wishlist_link();
            echo Urus_Pluggable_WooCommerce::header_cart_menu();

            if( $used_header=='style8'){
                echo Urus_Pluggable_WooCommerce::menu_bar_link('menu');
            }
            echo '</ul>';
        }
        public static function menu_bar_link( $type="sidebar"){

            ob_start();
            ?>
            <li class="menu-item menu-bar-item <?php echo esc_attr($type);?>">
                <a href="#">
                    <span class="text"><?php esc_html_e('Menu','urus');?></span>
                    <span class="text-close hidden"><?php esc_html_e('Close','urus');?></span>
                </a>
            <?php if($type=='sidebar'):?>
            <div class="header-sidebar-fixed">
                <a class="close-header-sidebar" href="#"><?php esc_html_e('Close','urus');?></a>
                <?php if ( is_active_sidebar( 'header-sidebar' ) ) : ?>
                    <div class="header-sidebar__inner">
                        <?php dynamic_sidebar( 'header-sidebar' ); ?>
                    </div>
                <?php endif;?>
            </div>
            <?php endif;?>
            <?php if($type =='menu'):?>
                <div class="header-sidebar-fixed display-menu">

                    <div class="header-sidebar__inner">
                        <a href="#" class="prev-menu hidden"><?php esc_html_e('Prev','urus');?></a>

                        <?php
                        if(has_nav_menu('bar_menu')){
                            wp_nav_menu( array(
                                'menu'            => 'bar_menu',
                                'theme_location'  => 'bar_menu',
                                'container'       => '',
                                'container_class' => '',
                                'container_id'    => '',
                                'menu_class'      => 'menu urus-clone-mobile-menu menu-morph',
                                'walker' =>new Urus_Walker()
                            ));
                        }

                        ?>


                    </div>
                </div>
            <?php endif;?>
            </li>
            <?php
            $html = ob_get_clean();
            return $html;
        }
        public static function wishlist_link(){
            $enable_header_wishlist = Urus_Helper::get_option('enable_header_wishlist',true);
            if (!$enable_header_wishlist || $enable_header_wishlist == 0){
                return '';
            }
            $wishlist_enable = Urus_Helper::get_option('enable_familab_wishlist',0);
            $class = array('menu-item menu-wishlist-item with-icon');
            $enable_wishlist_icon =  Urus_Helper::get_option('enable_wishlist_icon',true);
            if ($enable_wishlist_icon){
                $class[] = 'wishlist-icon';
            }
            if ($wishlist_enable) {
                $class[] = 'js-urus-wishlist';
                //js-urus-wishlist
                ob_start();
                ?>
                <li class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
                    <a href="javascript:void(0);">
                        <span class="text"><?php esc_html_e('Wishlist','urus')?></span>
                    </a>
                </li>
                <?php
                $html = ob_get_clean();
            }else{
                if( !class_exists('YITH_WCWL')) return '';
                $wishlist_link = get_permalink( get_option('yith_wcwl_wishlist_page_id') );
                ob_start();
                ?>
                <li class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
                    <a href="<?php echo esc_url($wishlist_link);?>">

                        <span class="text"><?php esc_html_e('Wishlist','urus')?></span>
                    </a>
                </li>
                <?php
                $html = ob_get_clean();
            }


            return $html;
        }
        public static function userlink(){
            if( !class_exists('WooCommerce')){
                return;
            }
            $enable_header_account = Urus_Helper::get_option('enable_header_account',true);
            if (!$enable_header_account || $enable_header_account == 0){
                return '';
            }


            $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
            $myaccount_link = get_permalink( $myaccount_page_id );
            $class = array('menu-item menu-myaccount-item with-icon');
            $enable_account_icon =  Urus_Helper::get_option('enable_account_icon',false);
            if ($enable_account_icon){
                $class[] = 'account-icon';
            }
            if( !is_user_logged_in() && !is_page($myaccount_page_id)){
                $myaccount_link = '#urus-login-form-popup';
                $class[] ='popup';
            }
            ob_start();
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">

                <?php if( !is_user_logged_in() && !is_page($myaccount_page_id)):?>
                    <a href="<?php echo esc_url($myaccount_link);?>"><span class="text"><?php esc_html_e('Login','urus');?></span></a>
                    <?php wc_get_template_part('login', 'form' );?>
                <?php else:?>
                    <a href="<?php echo esc_url($myaccount_link);?>"><span class="text"><?php esc_html_e('Account','urus');?></span></a>
                <?php endif;?>
            </li>
            <?php
            $html = ob_get_clean();
            return $html;
        }
        public static function header_cart_menu(){
            if( !class_exists('WooCommerce')) return;
            $mini_cart_style = Urus_Helper::get_option('mini_cart_style','drawer');
            if ($mini_cart_style != 'drawer'){
                $item_class = array('menu-item menu-cart-item with-icon');
            }else{
                 $item_class = array('menu-item menu-cart-item with-icon cart-type-drawer');
            }
            $enable_bag_icon = Urus_Helper::get_option('enable_bag_icon',false);
            if ($enable_bag_icon){
                $item_class[] = 'cart-icon';
            }
            //global $woocommerce;
            ob_start();
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $item_class ) ); ?>">
                <?php Urus_Pluggable_WooCommerce::header_cart_link();?>
                <?php if ($mini_cart_style == 'drawer' || Urus_Helper::is_mobile_template() ){
                    // Do not add mini cart here
                }else{
                    ?>
                    <div class="sub-menu cart-dropdown urus-mini-cart-content">
                        <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
                    </div>
                    <?php
                }
                ?>
            </li>
            <?php
            $html = ob_get_clean();
            return $html;
        }

        public static function header_block_mini_cart(){
            ?>
            <div class="block-mini-cart menu-cart-item">
                <?php Urus_Pluggable_WooCommerce::header_cart_link();?>
                <div class="sub-menu">
                    <?php the_widget( 'WC_Widget_Cart', 'title=' );?>
                </div>
            </div>
            <?php
        }
        public static function header_cart_link(){
            global $woocommerce;
            $mini_cart_style = Urus_Helper::get_option('mini_cart_style','dropdown');
            $cart_contents_count = WC()->cart->cart_contents_count;
            $couter_class = ($cart_contents_count == 0)? ' empty':'';
            ?>
            <a class="cart-link <?php if($mini_cart_style == 'drawer' || Urus_Helper::is_mobile_template() ) echo 'js-drawer-open-cart'; ?>" href="<?php echo wc_get_cart_url();?>">
                <span class="icon">
                    <span class="icon-count<?php  echo $couter_class; ?>"><span class="cart-counter"><?php echo $cart_contents_count; ?></span></span>
                </span>
                <span class="text"><?php esc_html_e('Bag','urus');?></span>
            </a>
            <?php
        }
        public static function cart_link_fragment( $fragments ){
            $cart_contents_count = WC()->cart->cart_contents_count;
            ob_start();
            echo '<span class="cart-counter">'.$cart_contents_count.'</span>';
            $fragments['.cart-counter'] = ob_get_clean();

            $fragments['urus-minicart'] = array(
                'count' => $cart_contents_count,
                'total' => WC()->cart->get_cart_total()
            );
            return $fragments;
        }

        public static function search_link( $click = false){
            $enable_header_search = Urus_Helper::get_option('enable_header_search',true);
            if (!$enable_header_search || $enable_header_search == 0){
                return '';
            }
            $search_form_style = Urus_Helper::get_option('search_form_style','inline');

            if( $search_form_style == 'popup' ){
                $click = true;
            }
            $class = array('menu-item menu-search-link with-icon');
            $a_c = '';
            if( $click ){
                $class[] = 'menu-search-link-click';
                $a_c = 'js-drawer-open-top';
            }else{
                $class[] = 'menu-search-link-hover';
            }
            $search_form_close_button = false;
            $show_title = false;
            $show_category = false;
            if( $click ){
                $search_form_close_button = true;
                $show_title = true;
            }
            ob_start();
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
                <a class="<?php echo e_data($a_c); ?>" href="javascript:void(0)"><span class="text"><?php esc_html_e('Search','urus');?></span></a>
                <?php self::header_search_form($search_form_close_button,$show_title,$show_category);?>
            </li>
            <?php
            $html = ob_get_clean();
            return $html;
        }

        public static function header_search_form( $close = false,$show_title = false, $show_category = false, $placeholder = "", $button_seacrh = false){
            $form_class=  array('form-search');
            $placeholder = !empty($placeholder) ? $placeholder : esc_html__('Search anything','urus');
            if( $show_category){
                $form_class[]='has-category';
            }
            ?>
            <form method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>" class="<?php echo esc_attr( implode( ' ', $form_class ) ); ?>">
                <div class="seach-box-wapper">
                    <?php if( $show_title):?>
                        <h3 class="form-title"><?php esc_html_e('Search','urus');?></h3>
                    <?php endif;?>
                    <div class="serach-box results-search">
                        <div class="box-inner">
                            <div class="serchfield-waper">
                                <input autocomplete="off" type="search" class="serchfield"  name="s" value ="<?php echo esc_attr( get_search_query() );?>"  placeholder="<?php echo esc_attr($placeholder);?>">
                            </div>
                            <?php if( class_exists( 'WooCommerce' ) ): ?>
                                <input type="hidden" name="post_type" value="product" />
                                <?php if( $show_category ):?>
                                    <?php
                                    $selected = '';
                                    if( isset( $_GET['product_cat']) && $_GET['product_cat'] ){
                                        $selected = $_GET['product_cat'];
                                    }
                                    $args = array(
                                        'show_option_none' => esc_html__( 'All Categories', 'urus' ),
                                        'taxonomy'          => 'product_cat',
                                        'class'             => 'categori-search-option',
                                        'hide_empty'        => 1,
                                        'orderby'           => 'name',
                                        'order'             => "asc",
                                        'tab_index'         => true,
                                        'hierarchical'      => true,
                                        'id'                => rand(),
                                        'name'              => 'product_cat',
                                        'value_field'       => 'slug',
                                        'selected'          => $selected,
                                        'option_none_value' => '0',
                                    );
                                    ?>
                                    <div class="form-search-categories">
                                        <?php wp_dropdown_categories( $args ); ?>
                                    </div>
                                <?php endif;?>
                            <?php endif; ?>
                        </div>
                        <?php if(!$button_seacrh): ?>
                            <a href="javascript:void(0);" class="button-search"><?php echo familab_icons('search'); esc_html_e('Search','urus');?></a>
                        <?php else: ?>
                            <button type="submit" class="button-search"><?php echo familab_icons('search'); esc_html_e('Search','urus');?></button>
                        <?php endif; ?>
                </div>
                    <?php if ($close){ ?>
                        <a href="#" class="close_search_btn" title="<?php esc_attr_e('Close','urus'); ?>">
                            <?php echo familab_icons('close');esc_html_e('Close','urus') ?>
                        </a>
                    <?php  } ?>
                </div>
            </form>
            <?php
        }

        public static function is_display_login_form(){
            $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
            $woocommerce_checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
            $woocommerce_enable_checkout_login_reminder = get_option('woocommerce_enable_checkout_login_reminder');

            if( is_page()){
                $page_id = get_queried_object_id();
                if($page_id == $myaccount_page_id) return false;
                if($page_id == $woocommerce_checkout_page_id && $woocommerce_enable_checkout_login_reminder =='yes') return false;
            }
            return true;
        }

        public static function ajax_fillter(){
           $fromUrl = isset($_POST['fromUrl']) ? $_POST['fromUrl'] :'';
           if( $fromUrl !=""){
                $results = wp_remote_get($fromUrl);
                echo  ''.$results['body'];
           }
           wp_die();
        }

        public static  function add_cart_single_ajax(){
            $products = isset( $_POST['data'] ) ? $_POST['data'] : array();
            $post_data = $_POST['data'];
            $missing_attributes = array();
            if ( !empty( $products ) ) {
                $product_id = $products['product_id'];
                $variation_id       = empty( $products['variation_id'] ) ? '' : absint( wp_unslash( $products['variation_id'] ) ); // phpcs:ignore
                $quantity   = empty( $products['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $products['quantity'] ) ); // phpcs:ignore
                if ( isset(  $variation_id ) && isset( $product_id ) ) {
                    $product = wc_get_product( $product_id );

                    // Gather posted attributes.
                    $posted_attributes = array();

                    foreach ( $product->get_attributes() as $attribute ) {
                        if ( ! $attribute['is_variation'] ) {
                            continue;
                        }
                        $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );

                        if ( isset( $post_data[ $attribute_key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
                            if ( $attribute['is_taxonomy'] ) {
                                // Don't use wc_clean as it destroys sanitized characters.
                                $value = sanitize_title( wp_unslash( $post_data[ $attribute_key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
                            } else {
                                $value = html_entity_decode( wc_clean( wp_unslash( $post_data[ $attribute_key ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
                            }

                            $posted_attributes[ $attribute_key ] = $value;
                        }
                    }

                    // Check the data we have is valid.
                    $variation_data = wc_get_product_variation_attributes( $variation_id );
                    foreach ( $product->get_attributes() as $attribute ) {
                        if ( ! $attribute['is_variation'] ) {
                            continue;
                        }

                        // Get valid value from variation data.
                        $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
                        $valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ] : '';

                        /**
                         * If the attribute value was posted, check if it's valid.
                         *
                         * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
                         */
                        if ( isset( $posted_attributes[ $attribute_key ] ) ) {
                            $value = $posted_attributes[ $attribute_key ];

                            // Allow if valid or show error.
                            if ( $valid_value === $value ) {
                                $variations[ $attribute_key ] = $value;
                            } elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs(), true ) ) {
                                // If valid values are empty, this is an 'any' variation so get all possible values.
                                $variations[ $attribute_key ] = $value;
                            } else {
                                /* translators: %s: Attribute name. */
                                throw new Exception( sprintf( esc_html__( 'Invalid value posted for %s', 'urus' ), wc_attribute_label( $attribute['name'] ) ) );
                            }
                        } elseif ( '' === $valid_value ) {
                            $missing_attributes[] = wc_attribute_label( $attribute['name'] );
                        }
                    }
                    WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations );
                } elseif ( isset( $products['quantity'] ) && is_array( $products['quantity'] ) && !empty( $products['quantity'] ) ) {
                    foreach ( $products['quantity'] as $product_id => $quantity ) {
                        if ( $quantity > 0 )
                            WC()->cart->add_to_cart( $product_id, $quantity );
                    }
                } else {
                    if ( isset( $products['product_id'] ) && isset( $products['quantity'] ) && is_numeric( $products['quantity'] ) )
                        WC()->cart->add_to_cart( $products['product_id'], $products['quantity'] );
                }
                WC_AJAX::get_refreshed_fragments();
            }
            wp_die();
        }

        public static function single_product_bottom(){
            $woo_single_layout = Urus_Helper::get_option('woo_single_layout','left');
            $col_class = array('col');
            $is_upsell = Urus_Pluggable_WooCommerce::has_upsell_products();
            if( !$is_upsell){
                $col_class[] = 'col-sm-12';
            }else{
                $col_class[] = 'col-sm-12 col-md-6';
            }
            if( $woo_single_layout !='full'){
                $col_class = array('col col-sm-12');
            }
            ?>
            <div class="urus-product-bottom">
                <div class="row">
                    <div class="<?php echo esc_attr( implode( ' ', $col_class ) ); ?>">
                        <?php woocommerce_output_related_products();?>
                    </div>
                    <?php if( $is_upsell ):?>
                    <div class="<?php echo esc_attr( implode( ' ', $col_class ) ); ?>">
                        <?php woocommerce_upsell_display();?>
                    </div>
                    <?php endif;?>
                </div>
            </div>
            <?php

        }

        public static function has_upsell_products(){
            global  $product;
            if ( ! $product ) {
                return false;
            }
            $ids = $product->get_upsell_ids();
            if( !empty($ids)) return true;

            return false;
        }

        public static function woocommerce_output_related_products_args($args){
            $args['posts_per_page'] = 6;
            return $args;
        }

        public static function urus_get_gallery_image_html( $attachment_id, $main_image = false ) {
            $flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
            $gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
            $thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
            $image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
            $full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
            $thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
            $full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
            $image             = wp_get_attachment_image(
                $attachment_id,
                $image_size,
                false,
                apply_filters(
                    'woocommerce_gallery_image_html_attachment_image_params',
                    array(
                        'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
                        'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
                        'data-src'                => esc_url( $full_src[0] ),
                        'data-large_image'        => esc_url( $full_src[0] ),
                        'data-large_image_width'  => esc_attr( $full_src[1] ),
                        'data-large_image_height' => esc_attr( $full_src[2] ),
                        'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
                    ),
                    $attachment_id,
                    $image_size,
                    $main_image
                )
            );
            return $image;
        }

        public static function woocommerce_show_product_gallery_thumbnails(){
            global $product;
            $post_thumbnail_id = $product->get_image_id();
            $attachment_ids = $product->get_gallery_image_ids();
            if( has_post_thumbnail() ){
                array_unshift($attachment_ids,$post_thumbnail_id);
            }
            if ( $attachment_ids) {
                ob_start();
                foreach ( $attachment_ids as $attachment_id ) {?>
                    <div class="slide_img swiper-slide" data-id_img="<?php echo esc_attr($attachment_id); ?>">
                        <figure data-id="<?php echo esc_attr($product->get_id());?>">
                            <?php echo self::urus_get_gallery_image_html($attachment_id);?>
                        </figure>
                    </div>
                    <?php
                }
                $output_text = ob_get_clean();
                ?>
                <div class="swiper-container urus-gallery-top nav-center" data-nbs="4" data-space="15" data-product_id="<?php echo esc_attr($product->get_id());?>">
                    <div class="swiper-wrapper">
                        <?php
                            echo e_data($output_text);
                        ?>
                    </div>
                    <div class="slick-arrow next">
                        <?php echo familab_icons('arrow-right'); ?>
                    </div>
                    <div class="slick-arrow prev">
                        <?php echo familab_icons('arrow-left'); ?>
                    </div>
                </div>

                <div class="swiper-container urus-gallery-thumbs">
                    <div class="swiper-wrapper">
                        <?php
                        echo e_data($output_text);
                        ?>
                    </div>
                </div>
                <?php
            }else{
                $thumb_class = array('thumb-link');
                ?>
                <div class="<?php echo esc_attr( implode( ' ', $thumb_class ) ); ?>">
                    <div class='images'>
                        <a class="woocommerce-product-gallery__image" href="<?php the_permalink(); ?>">
                            <figure class="main-thumb" data-id="<?php echo esc_attr($product->get_id());?>">
                                <?php echo self::urus_get_gallery_image_html(get_post_thumbnail_id( $product->get_id()));?>
                            </figure>
                        </a>
                    </div>
                </div>
                <?php
            }
        }

        public static function woocommerce_show_product_thumbnails(){
            global  $product;
            $post_thumbnail_id = $product->get_image_id();
            $attachment_ids = $product->get_gallery_image_ids();
            if( has_post_thumbnail() ){
               array_unshift($attachment_ids,$post_thumbnail_id);
            }
            if( !empty($attachment_ids)){
                ?>
                <div class="product-thumbnail product-thumbnail-grid woocommerce-product-gallery urus-product-gallery" >
                    <div class="single-product-gallery">
                        <?php foreach ($attachment_ids as $attachment_id):?>
                            <div class="single-product-gallery-item" data-id_img="<?php echo esc_attr($attachment_id);?>">
                                <?php echo wc_get_gallery_image_html($attachment_id);?>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
                <?php
            }
        }

        public static function woocommerce_show_product_large_slider_thumbnails(){
            global  $product;

            $post_thumbnail_id = $product->get_image_id();

            $attachment_ids = $product->get_gallery_image_ids();
            if( has_post_thumbnail() ){
                array_unshift($attachment_ids,$post_thumbnail_id);
            }

            $atts = array(
                'loop'         => 'false',
                'ts_items'     => 1,
                'xs_items'     => 1,
                'sm_items'     => 1,
                'md_items'     => 1,
                'lg_items'     => 1,
                'ls_items'     => 1,
                'navigation'   => 'true',
                'slide_margin' => 20,
                'dots'          => 'true'
            );
            $carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
            ?>
            <?php if(!empty($attachment_ids)):?>
                <div class="woocommerce-product-gallery">
                    <div class="woocommerce-product-gallery__wrapper single-product-gallery__slider__wrapper large-thumbnail-slick swiper-container urus-swiper" <?php echo esc_attr($carousel_settings);?> >
                        <div class="swiper-wrapper">
                            <?php foreach ($attachment_ids as $attachment_id):
                                $class = array('single-product-gallery-item swiper-slide');
                                ?>
                                <div class="<?php echo esc_attr( implode( ' ', $class ) ); ?>" data-id_img="<?php echo esc_attr($attachment_id); ?>">
                                    <?php echo wc_get_gallery_image_html($attachment_id);?>
                                </div>
                            <?php endforeach;?>
                            ?>
                        </div>
                        <!-- If we need pagination -->
                        <div class="swiper-pagination"></div>
                    </div>

                    <!-- If we need navigation buttons -->
                    <div class="slick-arrow next">
                        <?php echo familab_icons('arrow-right3'); ?>
                    </div>
                    <div class="slick-arrow prev">
                        <?php echo familab_icons('arrow-left3'); ?>
                    </div>
                </div>
            <?php endif;?>
            <?php
        }

        public static function woocommerce_show_product_centered_slider_thumbnails(){
            global  $product;

            $post_thumbnail_id = $product->get_image_id();

            $attachment_ids = $product->get_gallery_image_ids();
            if( has_post_thumbnail() ){
                array_unshift($attachment_ids,$post_thumbnail_id);
            }
            $atts = array(
                'loop'         => 'false',
                'ts_items'     => 2,
                'xs_items'     => 2,
                'sm_items'     => 2,
                'md_items'     => 3,
                'lg_items'     => 3,
                'ls_items'     => 3,
                'navigation'   => 'false',
                'slide_margin' => 30,
                'dots'          => 'true'
            );
            $carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
            ?>
            <?php if(!empty($attachment_ids)):?>
                <div class="woocommerce-product-gallery">
                    <div class="woocommerce-product-gallery__wrapper single-product-gallery__slider__wrapper centered-thumbnail-slick swiper-container urus-swiper" <?php echo esc_attr($carousel_settings);?> >
                        <div class="swiper-wrapper">
                            <?php foreach ($attachment_ids as $attachment_id):
                                $class = array('single-product-gallery-item swiper-slide');
                                ?>
                                <div class="<?php echo esc_attr( implode( ' ', $class ) ); ?>" data-id_img="<?php echo esc_attr($attachment_id); ?>">
                                    <?php echo wc_get_gallery_image_html($attachment_id);?>
                                </div>
                            <?php endforeach;?>
                        </div>
                        <!-- If we need pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

            <?php endif;?>
            <?php
        }

        public static function urus_ajax_infload(){
            $fromUrl = isset($_POST['fromUrl']) ? $_POST['fromUrl'] :'';
            if( $fromUrl !=""){
                $results = wp_remote_get($fromUrl);
                echo  ''.$results['body'];
            }
            wp_die();
        }

        /**
         * Get current page URL with various filtering props supported by WC.
         *
         * @return string
         * @since  3.3.0
         */
        public static function get_current_page_url( $skip_query = false) {
            global $current_filtering_page_url;
            if (empty($current_filtering_page_url)){
                if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
                    $link = home_url('/');
                } elseif ( is_shop() ) {
                    $link = get_permalink( wc_get_page_id( 'shop' ) );
                } elseif ( is_product_category() && !isset($_GET['product_cat']) ) {
                    $link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
                    if( is_wp_error($link)){
                        $link ='';
                    }
                } elseif ( is_product_tag() ) {
                    $link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
                } else {
                    $link = Urus_Helper::get_current_page_url();
                }
                $GLOBALS['current_filtering_page_url'] = $link;
            }else{
                $link = $current_filtering_page_url;
            }
            if($skip_query){
                return $link;
            }
            global $current_filtering_page_url_with_query;
            // Min/Max.
            if (empty($current_filtering_page_url_with_query)){
                if ( isset( $_GET['min_price'] ) ) {
                    $link = add_query_arg( 'min_price', wc_clean( wp_unslash( $_GET['min_price'] ) ), $link );
                }
                if ( isset( $_GET['max_price'] ) ) {
                    $link = add_query_arg( 'max_price', wc_clean( wp_unslash( $_GET['max_price'] ) ), $link );
                }
                // Order by.
                if ( isset( $_GET['orderby'] ) ) {
                    $link = add_query_arg( 'orderby', wc_clean( wp_unslash( $_GET['orderby'] ) ), $link );
                }
                /**
                 * Search Arg.
                 * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
                 */
                if ( get_search_query() ) {
                    $link = add_query_arg( 's', rawurlencode(get_search_query() ), $link );
                }
                // Post Type Arg.
                if ( isset( $_GET['post_type'] ) ) {
                    $link = add_query_arg( 'post_type', wc_clean( wp_unslash( $_GET['post_type'] ) ), $link );
                }
                // Min Rating Arg.
                if ( isset( $_GET['rating_filter'] ) ) {
                    $link = add_query_arg( 'rating_filter', wc_clean( wp_unslash( $_GET['rating_filter'] ) ), $link );
                }
                // All current filters.
                if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
                    foreach ( $_chosen_attributes as $name => $data ) {
                        $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );
                        if ( ! empty( $data['terms'] ) ) {
                            $link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                        }
                        if ( 'or' === $data['query_type'] ) {
                            $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                        }
                    }
                }
                if (isset($_GET['product_cat'])){
                    $link = add_query_arg( 'product_cat', wc_clean( wp_unslash( $_GET['product_cat'] ) ), $link );
                }
                if (isset($_GET['product-brand'])){
                    $link = add_query_arg( 'product-brand', wc_clean( wp_unslash( $_GET['product-brand'] ) ), $link );
                }
                $GLOBALS['current_filtering_page_url_with_query'] = $link;
            }else{
                $link = $current_filtering_page_url_with_query;
            }
            return $link;
        }

        public static function woocommerce_template_single_meta(){
            global $product;
            ?>
            <div class="product_meta">
                <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
                <span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'urus' ); ?> <span class="sku"><?php echo ''.( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'urus' ); ?></span></span>
            <?php endif; ?>
            </div>
            <?php
        }

        public static function woocommerce_share(){
            $single_product_share = Urus_Helper::get_option('single_product_share',0);
            if( $single_product_share == 0) return '';
            ?>
            <div class="product-item-share">
                <label><?php esc_html_e('Share :','urus');?></label>
                <a class="hint--bounce hint--top" aria-label="Facebook" title="<?php echo sprintf( esc_attr__( 'Share "%s" on Facebook', 'urus'), get_the_title() ); ?>" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>&display=popup" target="_blank">
                    <i class="fa fa-facebook"></i>
                </a>
                <a class="hint--bounce hint--top" aria-label="Twitter" title="<?php echo sprintf( esc_attr__( 'Post status "%s" on Twitter', 'urus'), get_the_title() ); ?>" href="https://twitter.com/home?status=<?php the_permalink(); ?>" target="_blank">
                    <i class="fa fa-twitter"></i>
                </a>
                <a class="hint--bounce hint--top" aria-label="Google Plus" title="<?php echo sprintf( esc_attr__( 'Share "%s" on Google Plus', 'urus'), get_the_title() ); ?>"  href="https://plus.google.com/share?url=<?php the_permalink(); ?>" target="_blank">
                    <i class="fa fa-google-plus"></i>
                </a>
                <a class="hint--bounce hint--top" aria-label="Pinterest" title="<?php echo sprintf( esc_attr__( 'Pin "%s" on Pinterest', 'urus'), get_the_title() ); ?>" href="https://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&amp;media=<?php echo esc_url( get_the_post_thumbnail_url('full')); ?>&amp;description=<?php echo urlencode( get_the_excerpt() ); ?>" target="_blank">
                    <i class="fa fa-pinterest"></i>
                </a>
                <a class="hint--bounce hint--top" aria-label="Linkedin" title="<?php echo sprintf( esc_attr__( 'Share "%s" on LinkedIn', 'urus'), get_the_title() ); ?>"  href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink(); ?>&amp;title=<?php echo urlencode( get_the_title() ); ?>&amp;summary=<?php echo urlencode( get_the_excerpt() ); ?>&amp;source=<?php echo urlencode( get_bloginfo( 'name' ) ); ?>" target="_blank">
                    <i class="fa fa-linkedin"></i>
                </a>
            </div>
            <?php

        }

        public static function single_nav(){
            $single_product_navigation = Urus_Helper::get_option('single_product_navigation',0);
            if( $single_product_navigation == 0) return;
            if( !is_product()) return'';
            $next_post = get_next_post();
            $previous_post = get_previous_post();
            ?>
            <ul class="single-nav-wrapper">
                <?php
                $prev_product = false;
                if( !empty($previous_post) ){
                    $prev_product = wc_get_product( $previous_post->ID );
                }
                $prev_class = array('single-product-nav','single-nav__prev-item');
                if (!$prev_product || empty($prev_product)){
                    $prev_class[] = 'disable';
                }
                ?>
                <li class="<?php echo esc_attr( implode( ' ', $prev_class ) ); ?>">
                    <?php if( !empty($prev_product) && $prev_product):
                        $image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $prev_product->get_id()) , 80, 80, true, true);
                        $prev_lnk = get_permalink( $previous_post->ID );
                    ?>
                    <a href="<?php echo esc_url($prev_lnk); ?>"><?php esc_html_e('Previous product','urus');echo familab_icons('arrow-left'); ?></a>
                    <div class="item-wrapper">
                        <div class="item-detail">
                            <div class="thumb">
                                <a href="<?php echo esc_url($prev_lnk); ?>"><?php echo Urus_Helper::escaped_html($image_thumb['img']);?></a>
                            </div>
                            <div class="info">
                                <h5 class="product-name"><a href="<?php echo esc_url( $prev_lnk ); ?>"><?php echo get_the_title($previous_post->ID );?></a></h5>
                                <?php if ( $price_html = $prev_product->get_price_html() ) : ?>
                                    <span class="price"><?php echo Urus_Helper::escaped_html($price_html); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php else:?>
                        <a href="javascript:void(0)"><?php esc_html_e('Previous product','urus');echo familab_icons('arrow-left'); ?></a>
                    <?php endif;?>
                </li>

                <li class="single-product-nav single-nav__back">
                    <a class="hint--top hint-bounce" aria-label="<?php echo esc_attr__('Back to shop','urus');?>" href="<?php echo esc_url(get_permalink( wc_get_page_id( 'shop' ) ));?>"><?php esc_html_e('Go Back','urus');echo familab_icons('grid'); ?></a>
                </li>
                <?php
                $next_product = false;
                if( !empty($next_post) ){
                    $next_product = wc_get_product( $next_post->ID );
                }
                $next_class = array('single-product-nav','single-nav__prev-item');
                if (!$next_product || empty($next_product)){
                    $next_class[] = 'disable';
                }
                ?>
                <li class="<?php echo esc_attr( implode( ' ', $next_class ) ); ?>">
                    <?php if( !empty($next_product) && $next_product):
                        $next_image_thumb = Urus_Helper::resize_image(get_post_thumbnail_id( $next_product->get_id()) , 80, 80, true, true);
                        $next_lnk = get_permalink( $next_post->ID );
                        ?>
                        <a href="<?php echo esc_url( $next_lnk ); ?>"><?php esc_html_e('Next product','urus');echo familab_icons('arrow-right'); ?></a>
                    <div class="item-wrapper">
                        <div class="item-detail">
                            <div class="thumb">
                                <a href="<?php echo esc_url( $next_lnk ); ?>"><?php echo Urus_Helper::escaped_html($next_image_thumb['img']);?></a>
                            </div>
                            <div class="info">
                                <h5 class="product-name"><a href="<?php echo esc_url( $next_lnk ); ?>"><?php echo get_the_title($next_post->ID );?></a></h5>
                                <?php if ( $price_html = $next_product->get_price_html() ) : ?>
                                    <span class="price"><?php echo Urus_Helper::escaped_html($price_html); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php else:?>
                        <a href="javascript:void(0)"><?php esc_html_e('Next product','urus');echo familab_icons('arrow-right'); ?></a>
                    <?php endif;?>
                </li>
            </ul>
            <?php
        }

        /**
         * AJAX remove from cart.
         */
        public static function remove_from_cart() {
            ob_start();

            $cart_item_key = wc_clean( $_POST['cart_item_key'] );
            if ( $cart_item_key && false !== WC()->cart->remove_cart_item( $cart_item_key ) ) {
                WC_AJAX::get_refreshed_fragments();
            } else {
                wp_send_json_error();
            }
        }

        public static function undo_remove_cart_item(){
            ob_start();
            // Undo Cart Item.
            $cart_item_key = wc_clean( $_POST['cart_item_key'] );

            if($cart_item_key && false !== WC()->cart->restore_cart_item( $cart_item_key ) ){
                WC_AJAX::get_refreshed_fragments();
            }else{
                wp_send_json_error();
            }
        }

        public static function product_edit_tabs($tabs){
            $tabs['urus_options'] = array(
                'label'		=> esc_html__( 'Feature Video', 'urus' ),
                'target'	=> '_feature_video',
            );
            return $tabs;
        }
        /**
         * Contents of the Urus Options options product tab.
         */
        public static function product_edit_tab_content(){
            global $post;
            ?>
            <div id='_feature_video' class='panel woocommerce_options_panel'>
                <div class="options_group">
                    <?php
                        woocommerce_wp_text_input( array(
                            'id'				=> '_feature_video_url',
                            'label'				=> esc_html__( 'Video URL', 'urus' ),
                            'desc_tip'			=> 'true',
                            'description'		=> esc_html__( 'Enter the url video.', 'urus' ),
                            'type' 				=> 'text',
                            'custom_attributes'	=> array(
                                'placeholder'	=> 'http://',
                            ),
                        ) );
                    ?>
                </div>
            </div>
            <?php
        }

        public static function product_edit_tab_save($post_id){
            $_feature_video_url = isset($_POST['_feature_video_url']) ? $_POST['_feature_video_url'] :'';
            update_post_meta($post_id,'_feature_video_url',$_feature_video_url);
        }

        public static function single_mobile_tabs(){
            wc_get_template_part('single-product/tabs/mobile-tabs' );
        }

        public static function single_canvas_tabs_title(){
            wc_get_template_part('single-product/tabs/canvas-tabs-title' );
        }

        public static function single_canvas_tabs(){
            wc_get_template_part('single-product/tabs/canvas-tabs' );
        }

        public static function get_max_date_sale( $product ) {
            global $urus_product_data_sale;
            $product_id = $product->get_id();
            if (empty($urus_product_data_sale)){
                $product_data_sale = array();
            }else{
                $product_data_sale = $urus_product_data_sale;
            }
            if (isset($product_data_sale[$product_id])){
                return $product_data_sale[$product_id];
            }
            global $wpdb;
            $time = 0;
            // Get variations
            if ('variable' == $product->get_type()) {
                $variation_ids = $product->get_children();
                $sale_price_dates_to = false;
                if (!empty($variation_ids)) {
                    $sql = "
                        SELECT
                        meta_value
                        FROM $wpdb->postmeta
                        WHERE meta_key = '_sale_price_dates_to' and post_id IN(" . join(',', $variation_ids) . ")
                        ORDER BY meta_value DESC
                        LIMIT 1
                    ";
                    $sale_price_dates_to = $wpdb->get_var($sql);
                    if ($sale_price_dates_to == '') {
                        $sale_price_dates_to = '0';
                    }
                }
            } else {
                $sale_price_dates_to = get_post_meta( $product_id, '_sale_price_dates_to', true );
                if($sale_price_dates_to == ''){
                    $sale_price_dates_to = '0';
                }
            }
            $product_data_sale[$product_id] = $sale_price_dates_to;
            $GLOBALS['urus_product_data_sale'] = $product_data_sale;
            return $sale_price_dates_to;
         }

        public static function woocommerce_loop_add_to_cart_args($args, $product){
            $args['attributes']['aria-label'] = $product->add_to_cart_text();
            return $args;
        }

        public static  function custom_available_variation( $data, $product, $variation ){

            if ( has_filter( 'urus_shop_product_thumb_width' ) && has_filter( 'urus_shop_product_thumb_height' ) ) {
                // GET SIZE IMAGE SETTING
                $width  = 300;
                $height = 300;
                $size   = wc_get_image_size( 'shop_catalog' );
                if ( $size ) {
                    $width  = $size['width'];
                    $height = $size['height'];
                }
                $width                      = apply_filters( 'urus_shop_product_thumb_width', $width );
                $height                     = apply_filters( 'urus_shop_product_thumb_height', $height );

                $image_variable             = Urus_Helper::resize_image($data['image_id'],$width,$height,true,false);

                $data['image']['src']       = $image_variable['url'];
                $data['image']['url']       = $image_variable['url'];
                $data['image']['full_src']  = $image_variable['url'];
                $data['image']['thumb_src'] = $image_variable['url'];
                $data['image']['srcset']    = $image_variable['url'];
                $data['image']['src_w']     = $width;
                $data['image']['src_h']     = $height;
            }

            return $data;

        }

        public static function ajax_login(){

            // First check the nonce, if it fails the function will break
            check_ajax_referer( 'urus-ajax-login-nonce', 'security' );

            // Nonce is checked, get the POST data and sign user on
            $info = array();
            $info['user_login'] = $_POST['username'];
            $info['user_password'] = $_POST['password'];
            $info['remember'] = true;

            $user_signon = wp_signon( $info, false );
            if ( is_wp_error($user_signon) ){
                wp_send_json(array('loggedin'=>false, 'message'=>esc_html__('Wrong username or password.','urus')));
            } else {
                wp_send_json(array('loggedin'=>true, 'message'=>esc_html__('Login successful, redirecting...','urus')));
            }
            die();
        }
        public static function single_product_coundown(){
            global  $product;
            $date = Urus_Pluggable_WooCommerce::get_max_date_sale($product);
            if( $date > 0){
                ?>
                <div class="single-product-deal-countdown">
                    <h4 class="urus-countdown-title">
                        <?php
                            echo esc_html_e('Hurry Up ! Deals end in :','urus');
                        ?>
                    </h4>
                    <div class="urus-countdown " data-datetime="<?php echo date( 'm/j/Y g:i:s', $date); ?>"></div>
                </div>
                <div class="urus-deal-add-to-cart">
                    <?php
                    woocommerce_quantity_input( array(
                        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
                    ) );
                    ?>
                    <button type="button" class="urus-single-add-to-cart-deal alt button">
                        <?php esc_html_e('Add to cart','urus'); ?>
                    </button>
                </div>
                <?php
                do_action('urus_compare_button');
            }
        }
        public static function get_taxonomy_filter_info($taxonomy = '',$link = '',$add_query = false){
            global $wp;
            $result = false;
            if ($taxonomy == ''){
                return false;
            }
            if (isset($_GET[$taxonomy])){
                $result = array();
                $result['taxonomy'] = $taxonomy;
                $result['slug'] = $_GET[$taxonomy];
                if ($link != '')
                    $result['remove_filter_link'] = remove_query_arg($taxonomy,$link);
            }else{
                $current_tax_arr = explode(DIRECTORY_SEPARATOR,$wp->request);
                if (sizeof($current_tax_arr)> 1){
                    $permalinks = get_option( 'woocommerce_permalinks' ,array());
                    $category_base = isset($permalinks['category_base'])? $permalinks['category_base']:'';
                    if ($current_tax_arr[0] == $taxonomy || ($current_tax_arr[0] == $category_base && $taxonomy='product_cat')){
                        $result = array();
                        $result['taxonomy'] = $taxonomy;
                        $result['slug'] = $current_tax_arr[1];
                        if ($link != ''){
                            $result['remove_filter_link'] = get_permalink( wc_get_page_id( 'shop' ));
                            if ($add_query){
                                $query_string = self::get_query_string(null,array($taxonomy));
                                $result['remove_filter_link'] = add_query_arg($query_string,$result['remove_filter_link']);
                            }
                        }
                    }
                }
            }
            return $result;
        }
        public static function filter_active(){
            if ( ! is_shop() && ! is_product_taxonomy() ) {
                return;
            }
            $shop_filter_style = Urus_Helper::get_option('shop_filter_style','dropdown');
            if( $shop_filter_style =='accordion'){
                return;
            }
            $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
            $min_price          = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
            $max_price          = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
            $rating_filter      = isset( $_GET['rating_filter'] ) ? array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['rating_filter'] ) ) ) ) : array(); // WPCS: sanitization ok, input var ok, CSRF ok.
            $base_link = apply_filters('urus_filter_reset_url',self::get_current_page_url());
            $cate_slug = get_query_var( 'product_cat' );
            $brand_slug = get_query_var( 'product-brand' );
            if ($cate_slug || $brand_slug ||  0 < count( $_chosen_attributes ) || 0 < $min_price || 0 < $max_price || ! empty( $rating_filter ) ) {
                echo '<div class="urus-filter-active"><ul>';
                // Attributes.
                $taxonomy_slug ='';
                if ($cate_slug && $filter_cat = self::get_taxonomy_filter_info('product_cat',$base_link,true)){
                    if (isset($filter_cat['remove_filter_link'])){
                        if ($cat_term = get_term_by('slug',$filter_cat['slug'],$filter_cat['taxonomy'])){
                            echo '<li class="filtered_item"><span class="attribute-label">'.esc_html__('Category','urus').':</span><span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($filter_cat['remove_filter_link']) . '">' . esc_html($cat_term->name) . '</a></span></li>';
                        }
                    }
                }
                if ($brand_slug && $filter_brand = self::get_taxonomy_filter_info('product-brand',$base_link,true)){
                    if (isset($filter_brand['remove_filter_link'])){
                        if ($brand_term = get_term_by('slug',$filter_brand['slug'],$filter_brand['taxonomy'])){
                            echo '<li class="filtered_item"><span class="attribute-label">'.esc_html__('Brand','urus').':</span><span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($filter_brand['remove_filter_link']) . '">' . esc_html($brand_term->name) . '</a></span></li>';
                        }
                    }
                }
                if (!empty($_chosen_attributes)) {
                    foreach ($_chosen_attributes as $taxonomy => $data) {
                        $str_filter = '';
                        if($taxonomy != $taxonomy_slug){
                            $taxonomy_name = wc_attribute_label( $taxonomy );
                            $taxonomy_slug = $taxonomy;
                            echo '<li class="filtered_item"><span class="attribute-label">'.$taxonomy_name.':</span>';
                            $str_filter = '</li>';
                        }
                        foreach ($data['terms'] as $term_slug) {
                            $term = get_term_by('slug', $term_slug, $taxonomy);
                            if (!$term) {
                                continue;
                            }
                            $filter_name = 'filter_' . sanitize_title(str_replace('pa_', '', $taxonomy));
                            $current_filter = isset($_GET[$filter_name]) ? explode(',', wc_clean(wp_unslash($_GET[$filter_name]))) : array(); // WPCS: input var ok, CSRF ok.
                            $current_filter = array_map('sanitize_title', $current_filter);
                            $new_filter = array_diff($current_filter, array($term_slug));

                            $link = remove_query_arg(array('add-to-cart', $filter_name), $base_link);

                            if (count($new_filter) > 0) {
                                $link = add_query_arg($filter_name, implode(',', $new_filter), $link);
                            }
                            echo '<span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($link) . '">' . esc_html($term->name) . '</a></span>';
                        }
                        echo Urus_Helper::escaped_html($str_filter);
                    }
                }
                $str_filter = '';
                if( $min_price || $max_price){
                    echo '<li class="filtered_item"> <span class="attribute-label"><a href="#">'.esc_html__('Price','urus').':</a></span>';
                    $str_filter = '</li>';
                }
                if ($min_price) {
                    $link = remove_query_arg('min_price', $base_link);
                    /* translators: %s: minimum price */
                    echo '<span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($link) . '">' . sprintf(esc_html__('Min %s', 'urus'), wc_price($min_price)) . '</a></span>'; // WPCS: XSS ok.
                }
                if ($max_price) {
                    $link = remove_query_arg('max_price', $base_link);
                    /* translators: %s: maximum price */
                    echo '<span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($link) . '">' . sprintf(esc_html__('Max %s', 'urus'), wc_price($max_price)) . '</a></span>'; // WPCS: XSS ok.
                }
                echo Urus_Helper::escaped_html($str_filter);
                if (!empty($rating_filter)) {
                    echo '<li class="filtered_item"><span class="attribute-label"><a href="#">'.esc_html__('Rating','urus').':</a></span>';
                    foreach ($rating_filter as $rating) {
                        $link_ratings = implode(',', array_diff($rating_filter, array($rating)));
                        $link = $link_ratings ? add_query_arg('rating_filter', $link_ratings) : remove_query_arg('rating_filter', $base_link);
                        /* translators: %s: rating */
                        echo '<span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($link) . '">' . sprintf(esc_html__('Rated %s out of 5', 'urus'), esc_html($rating)) . '</a></span>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
                $reset_link = apply_filters('urus_filter_reset_url',self::get_current_page_url(true));
                ?>
                <a data-filter="all" id="urus-filter-reset-action" class="button reset" href="<?php echo esc_url($reset_link);?>"><?php esc_html_e('Reset Filter','urus');?></a>
                </div>
                <?php
            }
        }

        public static function filter_accordion_active(){
            if ( ! is_shop() && ! is_product_taxonomy() ) {
                return;
            }
            $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
            $min_price          = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
            $max_price          = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
            $rating_filter      = isset( $_GET['rating_filter'] ) ? array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['rating_filter'] ) ) ) ) : array(); // WPCS: sanitization ok, input var ok, CSRF ok.
            $base_link = apply_filters('urus_filter_reset_url',self::get_current_page_url());
            if ( 0 < count( $_chosen_attributes ) || 0 < $min_price || 0 < $max_price || ! empty( $rating_filter ) ) {
                echo '<div class="urus-filter-active"><ul>';
                // Attributes.
                $taxonomy_slug ='';
                if (!empty($_chosen_attributes)) {
                    foreach ($_chosen_attributes as $taxonomy => $data) {
                        $str_filter = '';
                        if($taxonomy != $taxonomy_slug){
                            $taxonomy_name = wc_attribute_label( $taxonomy );
                            $taxonomy_slug = $taxonomy;
                            echo '<li class="filtered_item"><span class="attribute-label">'.$taxonomy_name.':</span>';
                            $str_filter = '</li>';
                        }
                        foreach ($data['terms'] as $term_slug) {
                            $term = get_term_by('slug', $term_slug, $taxonomy);
                            if (!$term) {
                                continue;
                            }

                            $filter_name = 'filter_' . sanitize_title(str_replace('pa_', '', $taxonomy));
                            $current_filter = isset($_GET[$filter_name]) ? explode(',', wc_clean(wp_unslash($_GET[$filter_name]))) : array(); // WPCS: input var ok, CSRF ok.
                            $current_filter = array_map('sanitize_title', $current_filter);
                            $new_filter = array_diff($current_filter, array($term_slug));

                            $link = remove_query_arg(array('add-to-cart', $filter_name), $base_link);

                            if (count($new_filter) > 0) {
                                $link = add_query_arg($filter_name, implode(',', $new_filter), $link);
                            }
                            echo '<span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($link) . '">' . esc_html($term->name) . '</a></span>';
                        }
                        echo Urus_Helper::escaped_html($str_filter);
                    }
                }
                $str_filter = '';
                if( $min_price || $max_price){
                    echo '<li class="filtered_item"> <span class="attribute-label"><a href="#">'.esc_html__('Price','urus').':</a></span>';
                    $str_filter = '</li>';
                }
                if ($min_price) {
                    $link = remove_query_arg('min_price', $base_link);
                    /* translators: %s: minimum price */
                    echo '<span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($link) . '">' . sprintf(esc_html__('Min %s', 'urus'), wc_price($min_price)) . '</a></span>'; // WPCS: XSS ok.
                }
                if ($max_price) {
                    $link = remove_query_arg('max_price', $base_link);
                    /* translators: %s: maximum price */
                    echo '<span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($link) . '">' . sprintf(esc_html__('Max %s', 'urus'), wc_price($max_price)) . '</a></span>'; // WPCS: XSS ok.
                }
                echo Urus_Helper::escaped_html($str_filter);
                if (!empty($rating_filter)) {
                    echo '<li class="filtered_item"><span class="attribute-label"><a href="#">'.esc_html__('Rating','urus').':</a></span>';
                    foreach ($rating_filter as $rating) {
                        $link_ratings = implode(',', array_diff($rating_filter, array($rating)));
                        $link = $link_ratings ? add_query_arg('rating_filter', $link_ratings) : remove_query_arg('rating_filter', $base_link);
                        /* translators: %s: rating */
                        echo '<span class="chosen"><a rel="nofollow" aria-label="' . esc_attr__('Remove filter', 'urus') . '" href="' . esc_url($link) . '">' . sprintf(esc_html__('Rated %s out of 5', 'urus'), esc_html($rating)) . '</a></span>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
                $reset_link = apply_filters('urus_filter_reset_url',self::get_current_page_url(true));
                ?>
                <a data-filter="all" id="urus-filter-reset-action" class="button reset" href="<?php echo esc_url($reset_link);?>"><?php esc_html_e('Reset Filter','urus');?></a>
                </div>
                <?php
            }
        }

        public static function check_request_by_pjax(){
            if(function_exists('check_request_by_pjax')){
                return check_request_by_pjax();
            }
            return false;
        }

        public static function wp(){
            if(Urus_Pluggable_WooCommerce::check_request_by_pjax()){
                remove_all_actions('wp_head');
                remove_all_actions('wp_footer');
            }
        }

        public static function instant_filter(){
            $enable_instant_filter = Urus_Helper::get_option('enable_instant_filter',0);
            $shop_filter_style = Urus_Helper::get_option('shop_filter_style','dropdown');
            if( $enable_instant_filter == 0 && !in_array($shop_filter_style,array('accordion','step_filter'))){
                $query_string = Urus_Pluggable_WooCommerce::get_query_string(null, array());
                $filter_link = add_query_arg($query_string,Urus_Helper::get_current_page_url());
                ?>
                <div class="filter-actions">
                    <a data-filter="all" id="urus-filter-action" class="button filter" href="<?php echo esc_url($filter_link);?>"><?php esc_html_e('Filter','urus');?></a>
                </div>
                <?php
            }
        }

        public static function shop_sub_category(){
            $shop_heading_style = Urus_Helper::get_option('shop_heading_style','simple');
            $display_categories = Urus_Helper::get_option('display_categories',0);
            $display_categories_style = Urus_Helper::get_option('display_categories_style',0);
            $display_categories_query = Urus_Helper::get_option('display_categories_query','only_main_cat');

            if( in_array($shop_heading_style,array('banner')) && $display_categories ==1){
                global  $wp_query;
                if( !is_product()){
                    $args = array(
                        'orderby'           => 'name',
                        'order'             => 'ASC',
                        'hide_empty'        => true,
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
                    if ( is_tax( 'product_cat' )  && $display_categories_query =='show_sub_cat') {
                        $current_cat   = $wp_query->queried_object;
                        if( !is_wp_error($current_cat) && isset($current_cat->term_id)){
                            $args['parent'] = $current_cat->term_id;
                        }


                    }

                    $terms = get_terms('product_cat', $args);
                    if( !is_wp_error($terms) && !empty($terms)){

                        $atts = array(
                            'loop'         => 'false',
                            'ts_items'     => 1,
                            'xs_items'     => 2,
                            'sm_items'     => 2,
                            'md_items'     => 3,
                            'lg_items'     => 4,
                            'ls_items'     => 4,
                            'navigation'   => 'true',
                            'slide_margin' => 10,
                            'dots' => 'false'
                        );
                        if( $display_categories_style == 'mini'){
                            $atts['slide_margin'] = 0;
                        }
                        $carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
                        $class = array('product-subcategory-wapper nav-center');
                        $class[] = $display_categories_style;
                        $current_cat   = $wp_query->queried_object;

                        ?>
                        <div class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
                            <div class="inner">
                                <div class="product-subcategory swiper-container urus-swiper "  <?php echo esc_attr($carousel_settings);?>>
                                    <div class="swiper-wrapper">
                                        <?php foreach ( $terms as $term):?>
                                            <?php
                                            $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                                            $image = Urus_Helper::resize_image($thumbnail_id,96,96,true,true);
                                            $link  = get_term_link($term);
                                            $link = apply_filters('urus_shop_sub_categories_link',$link);


                                            $class_item = array('sub-cat-item swiper-slide');
                                            if( !is_wp_error($current_cat) && isset($current_cat->term_id)){
                                                if( $term->term_id == $current_cat->term_id ){
                                                    $class_item[] = 'current-cat';
                                                }
                                            }
                                            ?>
                                            <div class="<?php echo esc_attr( implode( ' ', $class_item ) ); ?>">
                                                <div class="inner">
                                                    <div class="image">
                                                        <a href="<?php echo esc_url($link)?>"><?php echo Urus_Helper::escaped_html($image['img']);?></a>
                                                    </div>
                                                    <div class="info">
                                                        <h3 class="name"><a data-filter="all" href="<?php echo esc_url($link)?>"><?php echo esc_html( $term->name);?></a></h3>
                                                        <span class="count"><?php echo esc_html( $term->count);?> <?php esc_html_e('Products','urus');?></span>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php endforeach;?>
                                    </div>
                                </div>
                                <!-- If we need navigation buttons -->
                                <div class="slick-arrow next">
                                    <?php echo familab_icons('arrow-right'); ?>
                                </div>
                                <div class="slick-arrow prev">
                                    <?php echo familab_icons('arrow-left'); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                }
            }
        }

        public static function get_product_loop_hint_class($button){
            $woo_product_item_layout = Urus_Helper::get_option('woo_product_item_layout','classic');
            $woo_product_item_layout = apply_filters('product_loop_hint_clas_woo_product_item_layout',$woo_product_item_layout);
            $class = array(
                'add_to_cart' => '',
                'quick_view' => '',
                'compare' => '',
                'wishlist' => ''
            );
            switch ($woo_product_item_layout){
                case 'default':
                    $class = array(
                        'add_to_cart' => 'hint--top hint-bounce',
                        'quick_view'  => 'hint--top-left hint-bounce',
                        'compare'     => 'hint--bounce hint--top',
                        'wishlist'    => 'hint--top-right hint-bounce'
                    );
                    break;
                case 'classic':
                    $class = array(
                        'add_to_cart' => 'hint--top hint-bounce',
                        'quick_view'  => 'hint--top-left hint-bounce',
                        'compare'     => 'hint--bounce hint--top',
                        'wishlist'    => 'hint--top-right hint-bounce'
                    );
                break;
                case 'cart_and_icon':
                    $class = array(
                        'add_to_cart' => '',
                        'quick_view'  => 'hint--bounce hint--top',
                        'compare'     => 'hint--bounce hint--top',
                        'wishlist'    => 'hint--bounce hint--top'
                    );
                break;
                case 'full':
                    $class = array(
                        'add_to_cart' => 'hint--bounce hint--top',
                        'quick_view'  => 'hint--bounce hint--top-left',
                        'compare'     => 'hint--bounce hint--top-right',
                        'wishlist'    => ''
                    );
                break;
                case 'vertical_icon':
                    $class = array(
                        'add_to_cart' => 'hint--bounce hint--left',
                        'quick_view'  => 'hint--bounce hint--left',
                        'compare'     => 'hint--bounce hint--left',
                        'wishlist'    => 'hint--bounce hint--left'
                    );
                break;
                case 'info_on_img':
                    $class = array(
                        'add_to_cart' => 'hint--bounce hint--left',
                        'quick_view'  => 'hint--bounce hint--left',
                        'compare'     => 'hint--bounce hint--left',
                        'wishlist'    => 'hint--bounce hint--left'
                    );
                    break;
                case 'overlay_info':
                $class = array(
                        'add_to_cart' => 'hint--bounce hint--top',
                        'quick_view'  => 'hint--bounce hint--top',
                        'compare'     => 'hint--bounce hint--top',
                        'wishlist'    => 'hint--bounce hint--top'
                    );
                    break;
                case 'overlay_center':
                $class = array(
                        'add_to_cart' => 'hint--bounce hint--top',
                        'quick_view'  => 'hint--bounce hint--top',
                        'compare'     => 'hint--bounce hint--top',
                        'wishlist'    => 'hint--bounce hint--top'
                    );
                    break;
            }

            return isset($class[$button]) ? $class[$button]:'';
        }
        public static function alidropship_currency_switcher($items, $args) {
            if ($args->theme_location == 'control_menu' && shortcode_exists('adsw_currency_switcher')){
                $items = '<li class="menu-item menu-item-has-children">'.do_shortcode('[adsw_currency_switcher title="'.__('Currency','urus').'"]').'</li>' . $items;
            }
            return $items;
        }
    }
}
