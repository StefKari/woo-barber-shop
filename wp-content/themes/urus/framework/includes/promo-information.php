<?php
if( !class_exists('Urus_Promo_Information')){
    class Urus_Promo_Information{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
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
            add_filter( 'woocommerce_product_data_tabs', array(__CLASS__,'product_edit_tabs') );
            add_filter( 'woocommerce_product_data_panels', array(__CLASS__,'product_edit_tab_content') );
            add_action( 'woocommerce_process_product_meta_simple', array(__CLASS__,'product_edit_tab_save')  );
            add_action( 'woocommerce_process_product_meta_variable', array(__CLASS__,'product_edit_tab_save')  );
            // State that initialization completed.
            self::$initialized = true;
        }
    
        public static function settings($meta_boxes){
        
            $meta_boxes[] = array(
                'id'         => 'urus_product_promo_option',
                'title'      => esc_html__('Product Promo information', 'urus'),
                'post_types' => 'product',
                'fields'     => array(
                    array(
                        'id'               => '_promo_info',
                        'name'             => 'Promo',
                        'type'             => 'wysiwyg',
                    ),
                )
            );
            return $meta_boxes;
        }
    
        public static function product_edit_tabs($tabs){
            $tabs['promo_information'] = array(
                'label'		=> esc_html__( 'Promo Information', 'urus' ),
                'target'	=> 'promo_information',
            );
            return $tabs;
        }
        /**
         * Contents of the Urus Options options product tab.
         */
        public static function product_edit_tab_content(){
            if (isset($_GET['post'])){
                $post_id = $_GET['post'];
            }else{
                global $post;
                $post_id = $post->ID;
            }

            $_promo_info = get_post_meta( $post_id, '_promo_info', true );

            $_promo_info = wpautop($_promo_info);

            $settings = array(
                'media_buttons' => true,
                'textarea_rows' => 10,
                'textarea_name' =>'promo_info'
            );
            ?>
            <div id='promo_information' class='panel woocommerce_options_panel'>
                <div class="options_group">
                    <h4><?php esc_html_e( 'Promo Content', 'urus' ) ?></h4>
                    <?php wp_editor($_promo_info,'_promo_info',$settings);?>
                </div>
                
            </div>
            <?php
        }
    
        public static function product_edit_tab_save($post_id){
            if ( isset( $_POST[ 'promo_info' ] ) ) {
                update_post_meta( $post_id, '_promo_info', wp_kses_post($_POST[ 'promo_info' ]));
            } else {
                delete_post_meta( $post_id, '_promo_info' );
            }
        }
    }
}