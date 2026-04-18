<?php
if( !class_exists('Urus_Brand')){
    class Urus_Brand{
        /**
         * Taxonomy slug
         *
         * @var string
         * @since 1.0.0
         */
        public static $brands_taxonomy = 'product-brand';
    
        /**
         * Rewrite for brands
         *
         * @var string
         * @since 1.2.0
         */
        public static $brands_rewrite = 'product-brands';
        
        
        protected static $initialized = false;
        /**
         * Initialize pluggable functions.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            // register brand taxonomy
            add_action( 'init', array( __CLASS__, 'registertaxonomy' ) );
            add_action( 'after_switch_theme', 'flush_rewrite_rules' );
    
            add_action( 'init', array( __CLASS__, 'init_brand_taxonomy_fields' ), 15 );
            // enqueue needed scripts
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts' ) );
    
            add_action( 'created_term', array( __CLASS__, 'save_brand_taxonomy_fields' ), 10, 3 );
            add_action( 'edit_term', array( __CLASS__, 'save_brand_taxonomy_fields' ), 10, 3 );
    
            // add taxonomy columns
            add_action( 'init', array( __CLASS__, 'init_brand_taxonomy_columns' ), 15 );
            
            add_action('woocommerce_after_single_product_summary',array(__CLASS__,'related_brand'),50);
            
            // State that initialization completed.
            self::$initialized = true;
        }
    
        /**
         * Register taxonomy for brands
         *
         * @return void
         * @since 1.0.0
         */
        public static function registertaxonomy(){
            self::$brands_taxonomy = apply_filters( 'urus_taxonomy_slug', self::$brands_taxonomy );
        
            $taxonomy_labels = array(
                'name' => apply_filters( 'urus_taxonomy_label_name', esc_html__( 'Brands', 'urus' ) ),
                'singular_name'     => esc_html__( 'Brand', 'urus' ),
                'all_items'         => esc_html__( 'All Brands', 'urus' ),
                'edit_item'         => esc_html__( 'Edit Brand', 'urus' ),
                'view_item'         => esc_html__( 'View Brand', 'urus' ),
                'update_item'       => esc_html__( 'Update Brand', 'urus' ),
                'add_new_item'      => esc_html__( 'Add New Brand', 'urus' ),
                'new_item_name'     => esc_html__( 'New Brand Name', 'urus' ),
                'parent_item'       => esc_html__( 'Parent Brand', 'urus' ),
                'parent_item_colon' => esc_html__( 'Parent Brand:', 'urus' ),
                'search_items'      => esc_html__( 'Search Brands', 'urus' ),
                'separate_items_with_commas' => esc_html__( 'Separate brands with commas', 'urus' ),
                'not_found'         => esc_html__( 'No Brands Found', 'urus' )
            );
        
            $taxonomy_args = array(
                'label' => apply_filters( 'urus_taxonomy_label', esc_html__( 'Brands', 'urus' ) ),
                'labels' => apply_filters( 'urus_taxonomy_labels', $taxonomy_labels ),
                'public' => true,
                'show_admin_column' => true,
                'hierarchical' => true,
                'capabilities' => apply_filters( 'urus_taxonomy_capabilities', array(
                        'manage_terms' => 'manage_product_terms',
                        'edit_terms'   => 'edit_product_terms',
                        'delete_terms' => 'delete_product_terms',
                        'assign_terms' => 'assign_product_terms',
                    )
                ),
                'update_count_callback' => '_wc_term_recount',
            );
        
            $object_type = apply_filters( 'urus_taxonomy_object_type', 'product' );
            $fc ='register'.'_taxonomy';
    
            $fc( self::$brands_taxonomy, $object_type, $taxonomy_args );
        
            if( is_array( $object_type ) && ! empty( $object_type ) ){
                foreach( $object_type as $type ){
                    register_taxonomy_for_object_type( self::$brands_taxonomy, $type );
                    
                }
            }
            else{
                register_taxonomy_for_object_type( self::$brands_taxonomy, $object_type );
            }
        }
        
        public static function scripts(){
            $screen = get_current_screen();
            if( $screen->id == 'edit-' . self::$brands_taxonomy ){
                wp_enqueue_media();
                wp_enqueue_script( 'urus-brand-admin', get_theme_file_uri( '/assets/js/admin/brand_admin.js' ), array( 'jquery' ), '1.0.0', true );
        
                wp_localize_script( 'urus-brand-admin', 'urus_brand', array(
                    'labels' => array(
                        'upload_file_frame_title' => esc_html__( 'Choose an image', 'urus' ),
                        'upload_file_frame_button' => esc_html__( 'Use image', 'urus' )
                    ),
                    'wc_placeholder_img_src' => wc_placeholder_img_src()
                ) );
            }
        }
        
        public static function init_brand_taxonomy_fields(){
            add_action( self::$brands_taxonomy . '_add_form_fields', array( __CLASS__, 'add_brand_taxonomy_fields' ), 15, 1 );
            add_action( self::$brands_taxonomy . '_edit_form_fields', array( __CLASS__, 'edit_brand_taxonomy_fields' ), 15, 1 );
        }
    
        /**
         * Prints custom term fields on "Add Brand" page
         *
         * @param $term string Current taxonomy id
         *
         * @return void
         * @since 1.0.0
         */
        public static function add_brand_taxonomy_fields( $term ) {
            ?>
            <div class="form-field">
                <label><?php esc_html_e( 'Logo', 'urus' ); ?></label>
                <div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" width="60px" height="60px" /></div>
                <div style="line-height:60px;">
                    <input type="hidden" id="product_brand_thumbnail_id" class="urus_upload_image_id" name="product_brand_thumbnail_id" />
                    <button id="product_brand_thumbnail_upload" type="button" class="urus_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'urus' ); ?></button>
                    <button id="product_brand_thumbnail_remove" type="button" class="urus_remove_image_button button"><?php esc_html_e( 'Remove image', 'urus' ); ?></button>
                </div>
                <div class="clear"></div>
            </div>
            <?php
        }
    
        /**
         * Prints custom term fields on "Edit Brand" page
         *
         * @param $term string Current taxonomy id
         *
         * @return void
         * @since 1.0.0
         */
        public static function edit_brand_taxonomy_fields( $term ) {
            $thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
            $image = $thumbnail_id ? wp_get_attachment_thumb_url( $thumbnail_id ) : wc_placeholder_img_src();
            ?>
            <tr class="form-field">
                <td><?php esc_html_e( 'Logo', 'urus' ); ?></td>
                <td>
                    <div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo esc_url($image); ?>" width="60px" height="60px" /></div>
                    <div style="line-height:60px;">
                        <input type="hidden" id="product_brand_thumbnail_id" class="urus_upload_image_id" name="product_brand_thumbnail_id" value="<?php echo esc_attr($thumbnail_id);?>" />
                        <button id="product_brand_thumbnail_upload" type="button" class="urus_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'urus' ); ?></button>
                        <button id="product_brand_thumbnail_remove" type="button" class="urus_remove_image_button button"><?php esc_html_e( 'Remove image', 'urus' ); ?></button>
                    </div>
                    <div class="clear"></div>
                </td>
                
            </tr>
            <?php
        }
    
        /**
         * Save custom term fields
         *
         * @param $term_id int Currently saved term id
         * @param $tt_id int|string Term Taxonomy id
         * @param $taxonomy string Current taxonomy slug
         *
         * @return void
         * @since 1.0.0
         */
        public static function save_brand_taxonomy_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
            if ( isset( $_POST['product_brand_thumbnail_id'] ) && self::$brands_taxonomy === $taxonomy ) {
                update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['product_brand_thumbnail_id'] ) );
            }
        }
    
        /**
         * Add custom columns to brand taxonomy table
         *
         * @return void
         * @since 1.0.0
         */
        public static function init_brand_taxonomy_columns() {
            add_filter( 'manage_edit-' . self::$brands_taxonomy . '_columns', array( __CLASS__, 'brand_taxonomy_columns' ), 15 );
            add_filter( 'manage_' . self::$brands_taxonomy . '_custom_column', array( __CLASS__, 'brand_taxonomy_column' ), 15, 3 );
        }
    
        /**
         * Register custom columns for "Add Brand" taxonomy view
         *
         * @param $columns mixed Old columns
         *
         * @return mixed Filtered array of columns
         * @since 1.0.0
         */
        public static function brand_taxonomy_columns( $columns ) {
            $new_columns          = array();
            if( isset( $columns['cb'] ) ) {
                $new_columns['cb'] = $columns['cb'];
                unset( $columns['cb'] );
            }
        
            $new_columns['thumb'] = esc_html__( 'Logo', 'urus' );
        
            return array_merge( $new_columns, $columns );
        }
    
        /**
         * Prints custom columns for "Add Brand" taxonomy view
         *
         * @param $columns mixed Array of columns to print
         * @param $column string Id of current column
         * @param $id int id of term being printed
         *
         * @return string Output for the columns
         */
        public static function brand_taxonomy_column( $columns, $column, $id ) {
        
            if ( 'thumb' == $column ) {
            
                $thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );
            
                if ( $thumbnail_id ) {
                    $image = wp_get_attachment_thumb_url( $thumbnail_id );
                } else {
                    $image = wc_placeholder_img_src();
                }
            
                $image = str_replace( ' ', '%20', $image );
            
                $columns = '<img src="' . esc_url( $image ) . '" alt="' . esc_html__( 'Logo', 'urus' ) . '" class="wp-post-image" height="48" width="48" />';
            
            }
        
            return $columns;
        }
        public static function display_product_brand_list( ){
            global  $product;
    
            $terms = get_the_terms( $product->get_id(), 'product-brand' );
            if( is_wp_error( $terms )  || empty($terms)){
                return '';
            }
            ?>
            <div class="product-brands">
                <label><?php esc_html_e('Brand:','urus');?></label>
                <ul class="list">
                    <?php
                        foreach ( $terms as $term ) {
                            $link = get_term_link( $term, 'product-brand' );
                            ?>
                            <li>
                                <a href="<?php echo esc_url($link)?>">
                                    <?php
                                        $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                                        $image = Urus_Helper::resize_image($thumbnail_id,150,90,true,true);
                                        echo Urus_Helper::escaped_html($image['img']);
                                    ?>
                                </a>
                            </li>
                            <?php
                        }
                    ?>
                </ul>
            </div>
            <?php
        }
        
        public static function related_brand(){
            global  $product;
           if( !is_product()) return;
           
    
            $terms = get_the_terms( $product->get_id(), 'product-brand' );
            
            if( is_wp_error( $terms )  || empty($terms)){
                return '';
            }
            $taxonomy ='product-brand';
            $term_ids = wp_get_post_terms( get_the_id(), $taxonomy, array('fields' => 'ids') );
            
            $args = array(
                'post_type'             => 'product',
                'post_status'           => 'publish',
                'ignore_sticky_posts'   => 1,
                'posts_per_page'        => 10, // Limit: two products
                'post__not_in'          => array( $product->get_id() ), // Excluding current product
                'tax_query'             => array(
                        array(
                            'taxonomy'      => $taxonomy,
                            'field'         => 'term_id', // can be 'term_id', 'slug' or 'name'
                            'terms'         => $term_ids,
                        )
                )
            );
            $query =new WP_Query($args);
    
            $woo_single_layout = Urus_Helper::get_option('woo_single_layout','left');
            $woo_product_item_layout = Urus_Helper::get_option('woo_product_item_layout','default');
    
            if( $woo_single_layout == 'full'){
                $atts = array(
                    'loop'         => 'false',
                    'ts_items'     => 2,
                    'xs_items'     => 3,
                    'sm_items'     => 3,
                    'md_items'     => 3,
                    'lg_items'     => 4,
                    'ls_items'     => 4,
                    'navigation'   => 'false',
                    'slide_margin' => 40,
                    'dots' => 'true'
                );
        
            }else{
                $atts = array(
                    'loop'         => 'false',
                    'ts_items'     => 2,
                    'xs_items'     => 2,
                    'sm_items'     => 3,
                    'md_items'     => 4,
                    'lg_items'     => 4,
                    'ls_items'     => 4,
                    'navigation'   => 'false',
                    'slide_margin' => 40,
                    'dots' => 'true'
                );
        
            }
            $atts['responsive_settings'] = array(
                '1500' => array(
                ),
                '1200' => array(
                ),
                '992' => array(
                    'slide_margin' => 20
                ),
                '768' => array(
                    'slide_margin' => 20
                ),
                '480' => array(
                    'slide_margin' => 15
                )
            );
    
    
    
    
            $atts = apply_filters('urus_related_products_carousel_settings',$atts, $woo_single_layout);
            $carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
    
            $product_item_class = array('product-item');
            $product_item_class[] = $woo_product_item_layout;
           ?>
            <?php if ( $query->have_posts() ):?>
            <div class="related_brand urus-box-products container">
                <div class="box-head">
                    <h2 class="title"><?php esc_html_e('Related Brand','urus');?></h2>
                </div>
                <div class="urus-products urus-products-carousel swiper-container urus-swiper nav-center" <?php echo esc_attr($carousel_settings);?>>
                    <div class="swiper-wrapper">
                        <?php while( $query->have_posts() ):
                            $query->the_post();?>
                            <div class="swiper-slide">
                                <div <?php post_class($product_item_class); ?>>
                                    <?php
                                        wc_get_template_part('product-styles/content-product', $woo_product_item_layout );
                                    ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
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
            </div>
            <?php endif;?>
           <?php
            wp_reset_postdata();
        }
    }
}