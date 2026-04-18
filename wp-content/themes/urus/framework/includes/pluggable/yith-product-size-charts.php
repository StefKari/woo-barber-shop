<?php
if( !class_exists('Urus_Pluggable_Yith_Product_Size_Charts')){
    class Urus_Pluggable_Yith_Product_Size_Charts{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;

        /**
         * Initialize pluggable functions for Visual Composer.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }


            add_action('woocommerce_single_product_summary',array(__CLASS__,'size_chart_button'),25);
            add_action( 'wp_ajax_urus_popup_size_chart', array(__CLASS__,'popup_content') );
            add_action( 'wp_ajax_nopriv_urus_popup_size_chart', array(__CLASS__,'popup_content') );
    
    
            add_filter( 'woocommerce_product_data_tabs', array(__CLASS__,'product_edit_tabs') );
            add_filter( 'woocommerce_product_data_panels', array(__CLASS__,'product_edit_tab_content') );
            add_action( 'woocommerce_process_product_meta_simple', array(__CLASS__,'product_edit_tab_save')  );
            add_action( 'woocommerce_process_product_meta_variable', array(__CLASS__,'product_edit_tab_save')  );

            // State that initialization completed.
            self::$initialized = true;
        }
        public static function size_chart_button(){
            global $product;
            $_product_size_chart = get_post_meta($product->get_id(),'_product_size_chart',true);
            if( empty($_product_size_chart) || $_product_size_chart <=0) return;
            ?>
            <div class="product_size_chart__wapper">
                <a data-id="<?php echo esc_attr($_product_size_chart);?>" href="#"><?php esc_html_e('Size Guide','urus');?></a>
            </div>
            <?php
        }

        public static function popup_content(){
            $id = isset($_POST['id']) ? $_POST['id'] :0;
            $table_meta = get_post_meta( $id, '_table_meta', true );
            $args = array(
                'table_meta' => $table_meta
            );
            wc_get_template( 'product/table.php', $args, YITH_WCPSC_TEMPLATE_PATH . '/', YITH_WCPSC_TEMPLATE_PATH . '/' );
            wp_die();
        }

       

    
        public static function product_edit_tabs($tabs){
            $tabs['product_size_chart'] = array(
                'label'		=> esc_html__( 'Size Chart', 'urus' ),
                'target'	=> 'product_size_chart',
            );
            return $tabs;
        }
        /**
         * Contents of the Urus Options options product tab.
         */
        public static function product_edit_tab_content(){
            global $post;
            $options = array(0 => esc_html__('Select a item','urus'));
            $args = array(
                'post_type' => 'yith-wcpsc-wc-chart',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $posts = new WP_Query( $args );
    
            if ( $posts -> have_posts() ) {
                while ( $posts -> have_posts() ) {
                    $posts->the_post();
                    $options[get_the_ID()] = get_the_title();
            
                }
            }
            wp_reset_postdata();
            if (isset($_GET['post'])){
                $post_id = $_GET['post'];
            }else{
                $post_id = $post->ID;
            }
            $value = get_post_meta($post_id,'_product_size_chart',true);
            ?>
            <div id='product_size_chart' class='panel woocommerce_options_panel'>
                <div class="options_group">
                    <?php
                        woocommerce_wp_select( array(
                            'id'      => '_product_size_chart',
                            'label'   => esc_html__( 'Size Chart', 'urus' ),
                            'options' =>  $options, //this is where I am having trouble
                            'value'   => $value,
                        ) );
                    ?>
                </div>
            </div>
            <?php
        }
    
        public static function product_edit_tab_save($post_id){
            $_product_size_chart = isset($_POST['_product_size_chart']) ? $_POST['_product_size_chart'] :'';
            update_post_meta($post_id,'_product_size_chart',$_product_size_chart);
        }
    
    }
}