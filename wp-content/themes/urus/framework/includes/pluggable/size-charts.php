<?php
if( !class_exists('Urus_Pluggable_Size_Charts')){
    class Urus_Pluggable_Size_Charts{
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


            add_filter('urus_meta_box_settings',array(__CLASS__,'settings'));

            add_action('woocommerce_before_add_to_cart_button',array(__CLASS__,'size_chart_button'),10);
            add_action( 'wp_ajax_urus_popup_size_chart', array(__CLASS__,'popup_content') );
            add_action( 'wp_ajax_nopriv_urus_popup_size_chart', array(__CLASS__,'popup_content') );
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

        public static function settings($meta_boxes){
            $options = array();
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

            $meta_boxes[] = array(
                'id'         => 'urus_product_option',
                'title'      => esc_html__('Product Options', 'urus'),
                'post_types' => 'product',
                'fields'     => array(
                    array(
                        'name'            => esc_html__('Size Chart','urus'),
                        'id'              => '_product_size_chart',
                        'type'            => 'select',
                        'options'         => $options,
                        // Placeholder text
                        'placeholder'     => esc_html__('Select an Size Chart','urus'),
                        'std' => 'default'
                    ),

                )
            );
            return $meta_boxes;
        }

        public static function add_settings(){
            $args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $posts = new WP_Query( $args );

            if ( $posts -> have_posts() ) {
                while ( $posts -> have_posts() ) {
                    $posts->the_post();
                    update_post_meta(get_the_ID(),'_product_size_chart',1666);

                }
            }
            wp_reset_postdata();
        }
    }
}