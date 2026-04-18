<?php
    if( !class_exists('Urus_Category_Background')){
        class  Urus_Category_Background{
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
                
                add_action( 'init', array( __CLASS__, 'init_category_taxonomy_fields' ), 15 );
                
                add_action( 'created_term', array( __CLASS__, 'save_category_taxonomy_fields' ), 10, 3 );
                add_action( 'edit_term', array( __CLASS__, 'save_category_taxonomy_fields' ), 10, 3 );
    
                // enqueue needed scripts
                add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts' ) );
                
                // State that initialization completed.
                self::$initialized = true;
            }
            public static function scripts(){
                $screen = get_current_screen();
                if( $screen->id == 'edit-product_cat' ){
                    wp_enqueue_media();
                    wp_enqueue_script( 'urus-category-admin', get_theme_file_uri( '/assets/js/admin/category-admin.js' ), array( 'jquery' ), '1.0.0', true );
        
                    wp_localize_script( 'urus-category-admin', 'urus_category', array(
                        'labels' => array(
                            'upload_file_frame_title' => esc_html__( 'Choose an image', 'urus' ),
                            'upload_file_frame_button' => esc_html__( 'Use image', 'urus' )
                        ),
                        'wc_placeholder_img_src' => wc_placeholder_img_src()
                    ) );
                }
            }
            
            
            public static function init_category_taxonomy_fields(){
                add_action( 'product_cat_add_form_fields', array( __CLASS__, 'add_product_cat_taxonomy_fields' ), 15, 1 );
                add_action( 'product_cat_edit_form_fields', array( __CLASS__, 'edit_product_cat_taxonomy_fields' ), 15, 1 );
            }
            public static function add_product_cat_taxonomy_fields($term){
                ?>
                <div class="form-field">
                    <label><?php esc_html_e( 'Background', 'urus' ); ?></label>
                    <div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" width="60px" height="60px" /></div>
                    <div style="line-height:60px;">
                        <input type="hidden" id="category_background" class="category_background" name="category_background" />
                        <button id="category_background_upload" type="button" class="urus_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'urus' ); ?></button>
                        <button id="category_background_remove" type="button" class="urus_remove_image_button button"><?php esc_html_e( 'Remove image', 'urus' ); ?></button>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php
            }
            public static function edit_product_cat_taxonomy_fields($term){
                $thumbnail_id = absint( get_term_meta( $term->term_id, 'category_background', true ) );
                $image = $thumbnail_id ? wp_get_attachment_thumb_url( $thumbnail_id ) : wc_placeholder_img_src();
                ?>
                <tr class="form-field">
                    <th><?php esc_html_e( 'Background', 'urus' ); ?></th>
                    <td>
                        <div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo esc_url($image); ?>" width="60px" height="60px" /></div>
                        <div style="line-height:60px;">
                            <input type="hidden" id="category_background" class="category_background" name="category_background" value="<?php echo esc_attr($thumbnail_id);?>" />
                            <button id="category_background_upload" type="button" class="urus_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'urus' ); ?></button>
                            <button id="category_background_remove" type="button" class="urus_remove_image_button button"><?php esc_html_e( 'Remove image', 'urus' ); ?></button>
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
            public static function save_category_taxonomy_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
                if ( isset( $_POST['category_background'] ) && 'product_cat' === $taxonomy ) {
                    update_term_meta( $term_id, 'category_background', absint( $_POST['category_background'] ) );
                }
            }
        }
    }