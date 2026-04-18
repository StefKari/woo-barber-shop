<?php
if( !class_exists('Urus_Woo_Variation_Gallery')){
    class Urus_Woo_Variation_Gallery{
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
            // enqueue needed scripts
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts' ) );
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'frontend_scripts' ) );

            add_action( 'woocommerce_product_after_variable_attributes', array(__CLASS__,'gallery_admin_html'), 10, 3 );
            add_action( 'woocommerce_save_product_variation', array(__CLASS__,'save_variation_gallery'), 10, 2 );


            add_filter( 'woocommerce_available_variation', array(__CLASS__,'available_variation_gallery'), 90, 3 );
            add_action('woocommerce_after_variations_form',array(__CLASS__,'woocommerce_after_variations_form'));

            add_action('woocommerce_variable_product_before_variations',array(__CLASS__,'woocommerce_variable_product_before_variations'));
            add_action( 'woocommerce_process_product_meta_simple', array(__CLASS__,'product_edit_tab_save')  );
            add_action( 'woocommerce_process_product_meta_variable', array(__CLASS__,'product_edit_tab_save')  );
        
            // State that initialization completed.
            self::$initialized = true;
        }

        public static function scripts(){
            wp_enqueue_media();
            wp_enqueue_script( 'woo-variation-gallery', get_theme_file_uri( '/assets/js/admin/woo-variation-gallery.js' ), array( 'jquery' ), '1.0.0', true );

            wp_localize_script( 'woo-variation-gallery', 'woo_variation_gallery', array(
                'labels' => array(
                    'upload_file_frame_title' => esc_html__( 'Choose an images', 'urus' ),
                    'upload_file_frame_button' => esc_html__( 'Use images', 'urus' )
                ),
                'wc_placeholder_img_src' => wc_placeholder_img_src()
            ) );
        }

        public static function frontend_scripts(){
            if( is_product()){
                global $post;
                $woo_single_used_layout = Urus_Helper::get_option('woo_single_used_layout','vertical');

                $_enable_variation_gallery = Urus_Helper::get_post_meta($post->ID,'_enable_variation_gallery','no');


                if( $_enable_variation_gallery == 'no'){
                    return;
                }

                wp_enqueue_script( 'urus-variation-gallery', get_theme_file_uri( '/assets/js/woo-variation-gallery.js' ), array( 'jquery' ), '1.0.0', true );

                wp_localize_script( 'urus-variation-gallery', 'urus_variation_gallery', array(
                    'woo_single_used_layout' => $woo_single_used_layout
                ) );
            }

        }

        public static function gallery_admin_html($loop, $variation_data, $variation ){
            $variation_id   = absint( $variation->ID );
            $gallery_images = get_post_meta( $variation_id, 'urus_woo_variation_gallery_images', true );


            ?>
            <div class="form-row form-row-full woo-variation-gallery-wrapper">
                <h4><?php esc_html_e( 'Image Gallery', 'urus' ) ?></h4>
                <ul id="woo-variation-gallery-images-<?php echo esc_attr($variation_id)?>" class="woo-variation-gallery-images">
                <?php if( !empty($gallery_images)):?>
                    <?php foreach ( $gallery_images as $attachment):?>
                        <li class="image">
                            <?php echo wp_get_attachment_image($attachment);?>
                            <input type="hidden" name="urus_woo_variation_gallery_images[<?php echo esc_attr($variation_id) ?>][]" value="<?php echo esc_attr($attachment) ?>">
                            <a href="#" class="delete remove-woo-variation-gallery-image"><span class="dashicons dashicons-dismiss"></span></a>
                        </li>
                    <?php endforeach;?>
                <?php endif;?>
                </ul>
                <div>
                    <button data-id="<?php echo esc_attr($variation_id);?>" id="woo-variation-gallery-upload-<?php echo esc_attr($variation_id);?>"  type="button" class="woo-variation-gallery-upload button"><?php esc_html_e( 'Upload/Add images', 'urus' ); ?></button>
                </div>
            </div>
            <?php
        }

        public static function save_variation_gallery($variation_id, $i ){
            if ( isset( $_POST[ 'urus_woo_variation_gallery_images' ] ) ) {
                if ( isset( $_POST[ 'urus_woo_variation_gallery_images' ][ $variation_id ] ) ) {
                    update_post_meta( $variation_id, 'urus_woo_variation_gallery_images', $_POST[ 'urus_woo_variation_gallery_images' ][ $variation_id ] );
                } else {
                    delete_post_meta( $variation_id, 'urus_woo_variation_gallery_images' );
                }
            } else {
                delete_post_meta( $variation_id, 'urus_woo_variation_gallery_images' );
            }
        }

        public static function available_variation_gallery( $available_variation, $variationProductObject, $variation ) {

            $product_id                   = absint( $variation->get_parent_id() );
            $variation_id                 = absint( $variation->get_id() );
            $variation_image_id           = absint( $variation->get_image_id() );
            $has_variation_gallery_images = (bool) get_post_meta( $variation_id, 'urus_woo_variation_gallery_images', true );
            $product                      = wc_get_product( $product_id );



            if ( $has_variation_gallery_images ) {
                $gallery_images = (array) get_post_meta( $variation_id, 'urus_woo_variation_gallery_images', true );
            } else {
                $gallery_images = $product->get_gallery_image_ids();
            }


            if ( $variation_image_id ) {
                // Add Variation Default Image
                array_unshift( $gallery_images, $variation->get_image_id() );
            } else {
                // Add Product Default Image
                if ( has_post_thumbnail( $product_id ) ) {
                    array_unshift( $gallery_images, get_post_thumbnail_id( $product_id ) );
                }
            }

            $available_variation[ 'variation_gallery_images' ] = array();

            foreach ( $gallery_images as $i => $variation_gallery_image_id ) {
                $product_attachment_props = wc_get_product_attachment_props( $variation_gallery_image_id );

                $available_variation[ 'variation_gallery_images' ][ $i ]                = $product_attachment_props;
                $available_variation[ 'variation_gallery_images' ][ $i ][ 'image_id' ]  = $variation_gallery_image_id;
                $available_variation[ 'variation_gallery_images' ][ $i ][ 'image' ] = '<img data-large_image="'.$product_attachment_props['full_src'].'" data-large_image_width="'.$product_attachment_props['full_src_w'].'" data-large_image_height="'.$product_attachment_props['full_src_h'].'" data-caption="'.$product_attachment_props['caption'].'" src="'.$product_attachment_props['src'].'" title="'.$product_attachment_props['title'].'" alt="'.$product_attachment_props['caption'].'">';
            }

            return $available_variation;
        }

        public static function woocommerce_after_variations_form(){
            global $product;
            $variation_gallery_images = Urus_Woo_Variation_Gallery::get_default_gallery_images($product->get_id());
            $variation_gallery_images = wp_json_encode($variation_gallery_images);
            ?>
            <div id="variation_gallery_defaut_images" class="hidden" data-variation_gallery_images="<?php echo esc_attr($variation_gallery_images);?>"></div>
            <?php
        }

        public static function get_default_gallery_images( $product_id){
            $product        = wc_get_product( $product_id );
            $gallery_images = $product->get_gallery_image_ids();

            $images = array();

            if ( has_post_thumbnail( $product_id ) ) {
                array_unshift( $gallery_images, get_post_thumbnail_id( $product_id ) );
            }

            if ( is_array( $gallery_images ) && ! empty( $gallery_images ) ) {

                foreach ( $gallery_images as $i => $image_id ) {

                    $product_attachment_props = wc_get_product_attachment_props( $image_id );

                    $images[ $i ]                = $product_attachment_props;
                    $images[ $i ][ 'image_id' ]  = $image_id;
                    $images[ $i ][ 'image' ] = '<img data-large_image="'.$product_attachment_props['full_src'].'" data-large_image_width="'.$product_attachment_props['full_src_w'].'" data-large_image_height="'.$product_attachment_props['full_src_h'].'" data-caption="'.$product_attachment_props['caption'].'" src="'.$product_attachment_props['src'].'" title="'.$product_attachment_props['title'].'" alt="'.$product_attachment_props['title'].'">';
                }
            }
            return $images;
        }

        public static function woocommerce_variable_product_before_variations(){

            ?>
            <div class="toolbar">
                <strong><?php esc_html_e( 'Show Variant Gallery on Select', 'urus' ); ?>: <?php echo wc_help_tip( esc_html__( 'This will show only selected variant\'s image gallery on attribute select instead of all product images.', 'urus' ) ); ?></strong>
                <?php
                    woocommerce_wp_checkbox(
                        array(
                            'id'            => '_enable_variation_gallery',
                            'label'         => ''
                        )
                    );
                ?>
                <script>
                    ;(function ($) {
                        $(document).on('change','#_enable_variation_gallery',function () {
                            $('#variable_product_options .wc_input_price').trigger('change');
                        })
                    })(jQuery);
                </script>
            </div>
            <?php

        }
        public static function product_edit_tab_save($post_id){
            $_enable_variation_gallery = isset( $_POST['_enable_variation_gallery'] ) ? 'yes' : 'no';

            update_post_meta( $post_id, '_enable_variation_gallery',$_enable_variation_gallery);
        }
    }
}
