<?php
if( !class_exists('Urus_Category_Description')){
    class  Urus_Category_Description{
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
            add_action( 'init', array( __CLASS__, '_register_post_type' ), 100, 0 );
    
            add_action( 'init', array( __CLASS__, 'init_category_taxonomy_fields' ), 15 );
    
            add_action( 'created_term', array( __CLASS__, 'save_category_taxonomy_fields' ), 10, 3 );
            add_action( 'edit_term', array( __CLASS__, 'save_category_taxonomy_fields' ), 10, 3 );
            add_action('woocommerce_archive_description',array(__CLASS__,'display_content'),10);
            remove_action('woocommerce_archive_description','woocommerce_taxonomy_archive_description',10);
    
            add_action( 'wp_enqueue_scripts', array(__CLASS__,'inline_css'),999);
    
            // State that initialization completed.
            self::$initialized = true;
        }
    
        public static function _register_post_type(){
            $args = array(
                'labels'              => array(
                    'name'               => esc_html__( 'Category Description', 'urus' ),
                    'singular_name'      => esc_html__( 'Category Description', 'urus' ),
                    'add_new'            => esc_html__( 'Add New', 'urus' ),
                    'add_new_item'       => esc_html__( 'Add new Category Description', 'urus' ),
                    'edit_item'          => esc_html__( 'Edit Category Description', 'urus' ),
                    'new_item'           => esc_html__( 'New Category Description', 'urus' ),
                    'view_item'          => esc_html__( 'View Category Description', 'urus' ),
                    'search_items'       => esc_html__( 'Search template Category Description', 'urus' ),
                    'not_found'          => esc_html__( 'No template items found', 'urus' ),
                    'not_found_in_trash' => esc_html__( 'No template items found in trash', 'urus' ),
                    'parent_item_colon'  => esc_html__( 'Parent template item:', 'urus' ),
                    'menu_name'          => esc_html__( 'Category Description', 'urus' ),
                ),
                'hierarchical'        => false,
                'description'         => esc_html__( 'To Build Category Description.', 'urus' ),
                'supports'            => array(
                    'title',
                    'editor',
                    'thumbnail',
                    'revisions',
                ),
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu' => 'edit.php?post_type=product',
                'menu_position'       => 10,
                'show_in_nav_menus'   => false,
                'exclude_from_search' => true,
                'has_archive'         => false,
                'query_var'           => true,
                'can_export'          => true,
                'rewrite'             => false,
                'capability_type'     => 'page',
            );
            $func = 'register_'.'post_type';
            $func( 'category_description', $args );
        }
        
        public static function init_category_taxonomy_fields(){
            add_action( 'product_cat_add_form_fields', array( __CLASS__, 'add_product_cat_taxonomy_fields' ), 15, 1 );
            add_action( 'product_cat_edit_form_fields', array( __CLASS__, 'edit_product_cat_taxonomy_fields' ), 15, 1 );
        }
        public static function add_product_cat_taxonomy_fields($term){
            $list = Urus_Category_Description::get_all_category_description();
            ?>
            <div class="form-field">
                <label><?php esc_html_e( 'Category Description', 'urus' ); ?></label>
                <select name="category_description" id="category_description">
                    <?php foreach ( $list as $key => $item):?>
                        <option value="<?php echo esc_attr($key)?>"><?php echo esc_html($item);?></option>
                    <?php endforeach;?>
                </select>
                <div class="clear"></div>
            </div>
            <?php
        }
        public static function edit_product_cat_taxonomy_fields($term){
            $list = Urus_Category_Description::get_all_category_description();
            $category_description = absint( get_term_meta( $term->term_id, 'category_description', true ) );
            ?>
            <tr class="form-field">
                <th><?php esc_html_e( 'Category Description', 'urus' ); ?></th>
                <td>
                    <select name="category_description" id="category_description">
                        <?php foreach ( $list as $key => $item):?>
                            <option <?php if( $category_description == $key):?> selected="selected" <?php endif;?>value="<?php echo esc_attr($key)?>"><?php echo esc_html($item);?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <?php
        }
        
        public static function get_all_category_description(){
            $query = new WP_Query( array( 'post_type' => 'category_description', 'posts_per_page' => -1 ) );
            $list = array(''  => esc_html__('Select an ietm','urus'));
            if( $query->have_posts()){
                while ($query->have_posts()){
                    $query->the_post();
                    $list[get_the_ID()] = get_the_title();
                }
            }
            wp_reset_postdata();
            return $list;
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
        public static function save_category_taxonomy_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
            if ( isset( $_POST['category_description'] ) && 'product_cat' === $taxonomy ) {
                update_term_meta( $term_id, 'category_description', absint( $_POST['category_description'] ) );
            }
        }
        
        public static function display_content(){
            global  $wp_query;
            if( !is_product_category()) return;
            $current_cat_id = 0;
            $current_cat   = $wp_query->queried_object;
            
         
            if( !is_wp_error($current_cat) && isset($current_cat->term_id)){
                $current_cat_id = $current_cat->term_id;
            }
            
            if( $current_cat_id > 0){
    
                $category_description = absint( get_term_meta( $current_cat_id, 'category_description', true ) );
                
                if( $category_description <= 0) return;
                
                $query = new WP_Query( array( 'p'=> $category_description,'post_type' => 'category_description', 'posts_per_page' => 1 ) );
                
                if( $query->have_posts()){
                    while ($query->have_posts()){
                        $query->the_post();
                        ?>
                        <div class="category-description">
                            <?php the_content();?>
                        </div>
                        <?php
                    }
                }
                wp_reset_postdata();
            }
        }
    
        public static function get_custom_css(){
            $vc_css ='';
            $args = array(
                'post_type' => 'category_description',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $posts = new WP_Query( $args );
            
            if ( $posts -> have_posts() ) {
                while ( $posts -> have_posts() ) {
                    $posts->the_post();
                    $vc_css .= get_post_meta(get_the_ID(),'_wpb_shortcodes_custom_css',true);
                    $vc_css .= get_post_meta(get_the_ID(),'_urus_vc_shortcode_custom_css',true);
                    $vc_css .= get_post_meta(get_the_ID(),'_urus_shortcode_custom_css',true);
                }
            }
            wp_reset_postdata();
        
            return $vc_css;
        
        }
        public static function inline_css(){
            $css = Urus_Category_Description::get_custom_css();
            
            $css = preg_replace( '/\s+/', ' ', $css );
            wp_add_inline_style( 'urus', $css );
        }
    }
}