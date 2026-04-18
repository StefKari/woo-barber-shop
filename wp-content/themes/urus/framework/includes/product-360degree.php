<?php
if( !class_exists('Urus_Product_360degree')){
    class Urus_Product_360degree{
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
    
            // enqueue needed scripts
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts' ) );
            // State that initialization completed.
            self::$initialized = true;
        }
    
        public static function scripts(){
            wp_enqueue_media();
            wp_enqueue_script( 'product-gallery-360', get_theme_file_uri( '/assets/js/admin/product-gallery-360.js' ), array( 'jquery' ), '1.0.0', true );
        
            wp_localize_script( 'product-gallery-360', 'product_gallery_360', array(
                'labels' => array(
                    'upload_file_frame_title' => esc_html__( 'Choose an images', 'urus' ),
                    'upload_file_frame_button' => esc_html__( 'Use images', 'urus' )
                ),
                'wc_placeholder_img_src' => wc_placeholder_img_src()
            ) );
        }
    
        
        public static function product_edit_tabs($tabs){
            $tabs['gallery_360degree'] = array(
                'label'		=> esc_html__( 'Gallery 360', 'urus' ),
                'target'	=> 'gallery_360degree',
            );
            return $tabs;
        }
        /**
         * Contents of the Urus Options options product tab.
         */
        public static function product_edit_tab_content(){
            global $post;
            $gallery_images = get_post_meta( $post->ID, '_gallery_360degree', true );
            $post_id = $post->ID;
            ?>
            <div id='gallery_360degree' class='panel woocommerce_options_panel'>
                <div class="options_group">
                    <h4><?php esc_html_e( 'Image Gallery', 'urus' ) ?></h4>
                    <ul id="product-gallery-images-<?php echo esc_attr($post_id)?>" class="product-gallery-images">
                        <?php if( !empty($gallery_images) && is_array($gallery_images)):?>
                            <?php foreach ( $gallery_images as $attachment):?>
                                <li class="image">
                                    <?php echo wp_get_attachment_image($attachment);?>
                                    <input type="hidden" name="_gallery_360degree[<?php echo esc_attr($post_id); ?>][]" value="<?php echo esc_attr($attachment); ?>">
                                    <a href="#" class="delete remove-product-gallery-image"><span class="dashicons dashicons-dismiss"></span></a>
                                </li>
                            <?php endforeach;?>
                        <?php endif;?>
                    </ul>
                    <div>
                        <button data-id="<?php echo esc_attr($post_id);?>" id="product-gallery-upload-<?php echo esc_attr($post_id);?>"  type="button" class="product-gallery-upload button"><?php esc_html_e( 'Upload/Add images', 'urus' ); ?></button>
                    </div>
                </div>
            </div>
            <?php
        }
    
        public static function product_edit_tab_save($post_id){
            if ( isset( $_POST[ '_gallery_360degree' ] ) ) {
                if ( isset( $_POST[ '_gallery_360degree' ][ $post_id ] ) ) {
                    update_post_meta( $post_id, '_gallery_360degree', $_POST[ '_gallery_360degree' ][ $post_id ] );
                } else {
                    delete_post_meta( $post_id, '_gallery_360degree' );
                }
            } else {
                delete_post_meta( $post_id, '_gallery_360degree' );
            }
        }
    
    }
    
    
}