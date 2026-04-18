<?php
if( !class_exists('Familab_Core_Variation_Swatches')){
    class Familab_Core_Variation_Swatches{
        public $meta_key  ='familab_variation_swatches';
        public function __construct() {
            $enable_theme_variation_swatches = true;

            $enable_theme_variation_swatches = apply_filters('enable_theme_variation_swatches',$enable_theme_variation_swatches);

            if( !$enable_theme_variation_swatches) return;
            if ( is_admin() ) {
                add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
                add_action( 'woocommerce_product_option_terms', array( $this, 'woocommerce_product_option_terms' ), 10, 3 );
                add_action( 'current_screen', array( $this, 'init_attribute_image_selector' ) );
                add_action( 'created_term', array( $this, 'woocommerce_attribute_thumbnail_field_save' ), 10, 3 );
                add_action( 'edit_term', array( $this, 'woocommerce_attribute_thumbnail_field_save' ), 10, 3 );
            }

            add_filter( 'product_attributes_type_selector', array( $this, 'product_attributes_type_selector' ) );

            add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'wc_variation_attribute_options' ), 99, 2 );

            add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ),11 );

            // Single settings

            add_action('woocommerce_variable_product_before_variations',array(__CLASS__,'woocommerce_variable_product_before_variations'));

            add_action( 'woocommerce_process_product_meta_simple', array(__CLASS__,'product_edit_tab_save')  );
            add_action( 'woocommerce_process_product_meta_variable', array(__CLASS__,'product_edit_tab_save')  );
            add_action( 'woocommerce_save_product_variation', array(__CLASS__,'save_variation'), 10, 2 );

            add_action( 'woocommerce_product_after_variable_attributes', array(__CLASS__,'extend_variation_form'), 10, 3 );
        }

        public function scripts(){
            wp_enqueue_script( 'wc-add-to-cart-variation' );
            wp_enqueue_script( 'familabcore-attributes-swatches', FAMILAB_CORE_PLUGIN_URL . 'assets/js/variation-swatches.js', array(), '1.0', true );
        }

        public function product_attributes_type_selector( $types ){
            $custom_types = array(
                'swatches_style' => esc_html__( 'Swatches', 'familabcore' ),
            );

            return array_merge( $types, $custom_types );
        }

        public function init_attribute_image_selector(){
            global $woocommerce, $_wp_additional_image_sizes;
            $screen = get_current_screen();
            if ( strpos( $screen->id, 'pa_' ) !== false ) :

                $attribute_taxonomies = $this->wc_get_attribute_taxonomies();
                if ( $attribute_taxonomies ) {
                    foreach ( $attribute_taxonomies as $tax ) {
                        if ( $tax->attribute_type == 'swatches_style' ) {
                            add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( $this, 'woocommerce_add_attribute_thumbnail_field' ) );
                            add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', array( $this, 'woocommerce_edit_attributre_thumbnail_field' ), 10, 2 );
                            add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', array( $this, 'woocommerce_product_attribute_columns' ) );
                            add_filter( 'manage_pa_' . $tax->attribute_name . '_custom_column', array( $this, 'woocommerce_product_attribute_column' ), 10, 3 );
                        }
                    }
                }
            endif;
        }

        //Saves the product attribute taxonomy term data
        public function woocommerce_attribute_thumbnail_field_save( $term_id, $tt_id, $taxonomy ) {
            if ( isset( $_POST['product_attribute_meta'] ) ) {
                $metas = $_POST['product_attribute_meta'];
                if ( isset( $metas[$this->meta_key] ) ) {
                    $data  = $metas[$this->meta_key];
                    $photo = isset( $data['photo'] ) ? $data['photo'] : '';
                    $color = isset( $data['color'] ) ? $data['color'] : '';
                    $type  = isset( $data['type'] ) ? $data['type'] : '';
                    update_term_meta( $term_id, $taxonomy . '_' . $this->meta_key . '_type', $type );
                    update_term_meta( $term_id, $taxonomy . '_' . $this->meta_key . '_photo', $photo );
                    update_term_meta( $term_id, $taxonomy . '_' . $this->meta_key . '_color', $color );
                }
            }
        }

        public function woocommerce_add_attribute_thumbnail_field(){
            global $woocommerce;
            ?>
            <div class="form-field ">
                <label for="product_attribute_type_<?php echo $this->meta_key; ?>"><?php esc_html_e( 'Type', 'familabcore' ) ?></label>
                <select name="product_attribute_meta[<?php echo $this->meta_key; ?>][type]"
                        id="product_attribute_type_<?php echo $this->meta_key; ?>" class="postform">
                    <option value="-1"><?php esc_html_e( 'None', 'familabcore' ) ?></option>
                    <option value="color"><?php esc_html_e( 'Color', 'familabcore' ) ?></option>
                    <option value="photo"><?php esc_html_e( 'Photo', 'familabcore' ) ?></option>
                </select>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery('#product_attribute_type_<?php echo $this->meta_key; ?>').change(function () {
                            jQuery('.field-active').hide().removeClass('field-active');
                            jQuery('.field-' + jQuery(this).val()).slideDown().addClass('field-active');
                        });
                        jQuery('.woo-color').wpColorPicker();
                    });
                </script>
            </div>
            <div class="form-field swatch-field field-color section-color-swatch"
                 style="overflow:visible;display:none;">
                <div id="swatch-color" class="<?php echo sanitize_title( $this->meta_key ); ?>-color">
                    <label><?php esc_html_e( 'Color', 'familabcore' ); ?></label>
                    <div id="product_attribute_color_<?php echo $this->meta_key; ?>_picker" class="colorSelector">
                        <div></div>
                    </div>
                    <label>
                        <input class="woo-color text" id="product_attribute_color_<?php echo $this->meta_key; ?>" type="text" name="product_attribute_meta[<?php echo $this->meta_key; ?>][color]" value="#000000"/>
                    </label>
                </div>
            </div>
            <div class="form-field swatch-field field-photo" style="overflow:visible;display:none;">
                <div id="swatch-photo" class="<?php echo sanitize_title( $this->meta_key ); ?>-photo">
                    <label><?php esc_html_e( 'Thumbnail', 'familabcore' ); ?></label>
                    <div id="product_attribute_thumbnail_<?php echo $this->meta_key; ?>"
                         style="float:left;margin-right:10px;">
                        <img src="<?php echo $woocommerce->plugin_url() . '/assets/images/placeholder.png' ?>" width="150" height="150"/>
                    </div>
                    <div style="line-height:60px;">
                        <input type="hidden" id="product_attribute_<?php echo $this->meta_key; ?>"
                               name="product_attribute_meta[<?php echo $this->meta_key; ?>][photo]"/>
                        <button type="submit"
                                class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'familabcore' ); ?></button>
                        <button type="submit"
                                class="remove_image_button button"><?php esc_html_e( 'Remove image', 'familabcore' ); ?></button>
                    </div>
                    <script type="text/javascript">

                        jQuery('body').on('click','.upload_image_button', function (e) {
                            e.preventDefault();
                            var button                      = jQuery(this),
                                id                          = button.closest('.submenu-item-bg').find('.process_custom_images');
                            wp.media.editor.send.attachment = function (props, attachment) {
                                id.val(attachment.id);
                                jQuery('#product_attribute_<?php echo $this->meta_key; ?>').val(attachment.id);
                                jQuery('#product_attribute_thumbnail_<?php echo $this->meta_key; ?> img').attr('src', attachment.url );
                            };
                            wp.media.editor.open(button);
                            return false;
                        });
                        jQuery('body').on('click','.upload_image_button', function () {
                            jQuery('#product_attribute_thumbnail_<?php echo $this->meta_key; ?> img').attr('src', '<?php echo $woocommerce->plugin_url() . '/assets/images/placeholder.png'; ?>');
                            jQuery('#product_attribute_<?php echo $this->meta_key; ?>').val('');
                            return false;
                        });
                    </script>
                    <div class="clear"></div>
                </div>
            </div>
            <?php
        }
        public function woocommerce_edit_attributre_thumbnail_field($term, $taxonomy){
            global $woocommerce;
            $type = get_term_meta($term->term_id,$taxonomy . '_' . $this->meta_key . '_type',true);
            $color               = get_term_meta( $term->term_id, $taxonomy . '_' . $this->meta_key . '_color', true );
            $photo  = get_term_meta( $term->term_id, $taxonomy . '_' . $this->meta_key . '_photo', true );
            $image       = '';
            ?>
            <tr class="form-field ">
                <th scope="row" valign="top"><label><?php esc_html_e( 'Type', 'familabcore' ); ?></label></th>
                <td>
                    <label>
                        <select name="product_attribute_meta[<?php echo $this->meta_key; ?>][type]" id="product_attribute_swatchtype_<?php echo $this->meta_key; ?>" class="postform">
                            <option <?php selected( 'none', $type ); ?> value="-1"><?php esc_html_e( 'None', 'familabcore' ); ?></option>
                            <option <?php selected( 'color', $type ); ?> value="color"><?php esc_html_e( 'Color', 'familabcore' ); ?></option>
                            <option <?php selected( 'photo', $type ); ?> value="photo"><?php esc_html_e( 'Photo', 'familabcore' ); ?></option>
                        </select>
                    </label>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('#product_attribute_swatchtype_<?php echo $this->meta_key; ?>').change(function () {
                                jQuery('.swatch-field-active').hide().removeClass('swatch-field-active');
                                jQuery('.swatch-field-' + jQuery(this).val()).show().addClass('swatch-field-active');
                            });
                            jQuery('.woo-color').wpColorPicker();
                        });
                    </script>
                </td>
            </tr>
            <?php $style = $type != 'color' ? 'display:none;' : ''; ?>
            <tr class="form-field swatch-field swatch-field-color section-color-swatch"
                style="overflow:visible;<?php echo $style; ?>">
                <th scope="row" valign="top"><label><?php esc_html_e( 'Color', 'familabcore' ); ?></label></th>
                <td>
                    <div id="swatch-color" class="<?php echo sanitize_title( $this->meta_key ); ?>-color">
                        <div id="product_attribute_color_<?php echo $this->meta_key; ?>_picker" class="colorSelector">
                            <div></div>
                        </div>
                        <label>
                            <input class="woo-color text"
                                   id="product_attribute_color_<?php echo $this->meta_key; ?>"
                                   type="text"
                                   name="product_attribute_meta[<?php echo $this->meta_key; ?>][color]"
                                   value="<?php echo $color; ?>"/>
                        </label>
                    </div>
                </td>
            </tr>
            <?php $style = $type != 'photo' ? 'display:none;' : ''; ?>
            <tr class="form-field swatch-field swatch-field-photo" style="overflow:visible;<?php echo $style; ?>">
                <th scope="row" valign="top"><label><?php esc_html_e( 'Photo', 'familabcore' ); ?></label></th>
                <td>
                    <div id="product_attribute_thumbnail_<?php echo $this->meta_key; ?>" style="float:left;margin-right:10px;">
                        <?php echo wp_get_attachment_image($photo);?>

                    </div>
                    <div style="line-height:60px;">
                        <input type="hidden" id="product_attribute_<?php echo $this->meta_key; ?>" name="product_attribute_meta[<?php echo $this->meta_key; ?>][photo]" value="<?php echo $photo; ?>"/>
                        <button type="submit" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'familabcore' ); ?></button>
                        <button type="submit" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'familabcore' ); ?></button>
                    </div>
                    <script type="text/javascript">
                        jQuery('body').on('click','.upload_image_button', function (e) {
                            e.preventDefault();
                            var button                      = jQuery(this),
                                id                          = button.closest('.submenu-item-bg').find('.process_custom_images');
                            wp.media.editor.send.attachment = function (props, attachment) {
                                id.val(attachment.id);
                                jQuery('#product_attribute_<?php echo $this->meta_key; ?>').val(attachment.id);
                                jQuery('#product_attribute_thumbnail_<?php echo $this->meta_key; ?>').html('<img src="'+attachment.url+'" width="150" height="150" alt="">');

                            };
                            wp.media.editor.open(button);
                            return false;
                        });
                        jQuery('body').on('click','.remove_image_button', function () {
                            jQuery('#product_attribute_thumbnail_<?php echo $this->meta_key; ?> img').attr('src', '<?php echo $woocommerce->plugin_url() . '/assets/images/placeholder.png'; ?>');
                            jQuery('#product_attribute_<?php echo $this->meta_key; ?>').val('');
                            return false;
                        });
                    </script>
                    <div class="clear"></div>
                </td>
            </tr>
            <?php
        }
        public function woocommerce_product_attribute_columns($columns){
            $new_columns                  = array();
            $new_columns['cb']            = $columns['cb'];
            $new_columns[$this->meta_key] = esc_html__( 'Swatches', 'familabcore' );
            unset( $columns['cb'] );
            $columns = array_merge( $new_columns, $columns );

            return $columns;
        }
        public function woocommerce_product_attribute_column($columns, $column, $id){

            if ( $column == $this->meta_key){
                $type   = get_term_meta($id,$_REQUEST['taxonomy'] . '_' . $this->meta_key . '_type',true);
                $color  = get_term_meta( $id, $_REQUEST['taxonomy'] . '_' . $this->meta_key . '_color', true );
                $photo  = get_term_meta( $id, $_REQUEST['taxonomy'] . '_' . $this->meta_key . '_photo', true );
                if( $type =='color'){
                    $columns     .= '<span style="display: inline-block; width:30px; height: 30px; background-color: '.$color.';"></span>';
                }elseif($type =='photo'){
                    $url= wp_get_attachment_image_url($photo);
                    $columns     .= '<img width="30" src="'.$url.'"/>';
                }

            }
            return $columns;
        }

        public function wc_get_attribute_taxonomies(){
            global $woocommerce;
            if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
                return wc_get_attribute_taxonomies();
            } else {
                return $woocommerce->get_attribute_taxonomies();
            }
        }
        public function woocommerce_product_option_terms( $attribute_taxonomy, $i ) {
            global $post, $thepostid, $product_object;

            ?>
            <?php if ( 'swatches_style' === $attribute_taxonomy->attribute_type ) : ?>
                        <?php
                $taxonomy = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
                $product_id = $thepostid;
                if ( is_null( $thepostid ) && isset( $_POST[ 'post_id' ] ) ) {
                $product_id = absint( $_POST[ 'post_id' ] );
                }
                        $args      = array(
                            'orderby'    => 'name',
                            'hide_empty' => 0,
                        );
                ?>
                <select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'familabcore' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i; ?>][]">
                    <?php
                        $all_terms = get_terms( $taxonomy, apply_filters( 'woocommerce_product_attribute_terms', $args ) );
                        if ( $all_terms ) :
                            foreach ( $all_terms as $term ) :
                                echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( has_term( absint( $term->term_id ), $taxonomy, $product_id ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
                            endforeach;
                        endif;
                        ?>
                    </select>
                <button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'familabcore' ); ?></button>
                <button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'familabcore' ); ?></button>
                <button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'familabcore' ); ?></button>
            <?php endif; ?>
            <?php
        }
        public function admin_scripts(){
            $screen = get_current_screen();
            if ( strpos( $screen->id, 'pa_' ) !== false ) {
                wp_enqueue_script( 'thickbox' );
                wp_enqueue_style( 'thickbox' );
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker' );
                if ( function_exists( 'wp_enqueue_media' ) ) {
                    wp_enqueue_media();
                }
            }
        }
        public function wc_variation_attribute_options( $html, $args ) {

            $attribute_swatch_width  = apply_filters('woocommerce_variation_swatches_image_width',40);
            $attribute_swatch_height = apply_filters('woocommerce_variation_swatches_image_height',40);

            $args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
                'options'          => false,
                'attribute'        => false,
                'product'          => false,
                'selected'         => false,
                'name'             => '',
                'id'               => '',
                'class'            => '',
                'show_option_none' => esc_html__( 'Choose an option', 'familabcore' ),
            ) );
            // Get selected value.
            if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
                $selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
                $args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
            }

            $options               = $args['options'];
            $product               = $args['product'];
            $attribute             = $args['attribute'];
            $name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
            $id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
            $class                 = $args['class'];
            $show_option_none      = (bool) $args['show_option_none'];
            $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : esc_html__( 'Choose an option', 'familabcore' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

            if ( empty( $options ) && !empty( $product ) && !empty( $attribute ) ) {
                $attributes = $product->get_variation_attributes();
                $options    = $attributes[$attribute];
            }

            if( !empty( $product )){
                $extend_attribute_for_variation_swatches = get_post_meta($product->get_id(),'extend_attribute_for_variation_swatches',true);

                if( $attribute == $extend_attribute_for_variation_swatches){
                    $available_variations = $product->get_available_variations();
                }

            }
            if ( !empty( $options ) ) {
                if ( $product && taxonomy_exists( $attribute ) ) {
                    $attribute_taxonomy = $this->get_product_attribute( $attribute );

                    $attributetype = $attribute_taxonomy['type'];
                    if( isset($extend_attribute_for_variation_swatches) && $attribute == $extend_attribute_for_variation_swatches){
                        $attributetype ='swatches_style_extend';
                    }
                    $show_option_none_text = sprintf(__("Choose a %s", 'familabcore'), $attribute_taxonomy['name']);
                    $class.=' '.$attributetype;
                    $html               = '<select data-attributetype="' . $attributetype . '" data-id="' . esc_attr( $id ) . '" class="attribute-select ' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
                    $html               .= '<option data-type="" data-' . esc_attr( $id ) . '="" value="">' . esc_html( $show_option_none_text ) . '</option>';
                    // Get terms if this is a taxonomy - ordered. We need the names too.
                    $terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
                    foreach ( $terms as $term ) {
                        if ( in_array( $term->slug, $options, true ) ) {
                            // For color attribute

                            $data_type  = get_term_meta( $term->term_id, $term->taxonomy . '_' . $this->meta_key . '_type', true );
                            $data_color = get_term_meta( $term->term_id, $term->taxonomy . '_' . $this->meta_key . '_color', true );
                            $option_highlight = false;
                            if ($data_color != ''){
                                $rgb = Familab_Core::hexToRgb($data_color);
                                $lumdiff = Familab_Core::lumdiff($rgb['r'],$rgb['g'],$rgb['b'],255,255,255);
                                if( $lumdiff <= 1.5){
                                    $option_highlight = true;
                                }
                            }
                            $data_photo = get_term_meta( $term->term_id, $term->taxonomy . '_' . $this->meta_key . '_photo', true );
                            $data_tooltip =  $term->name;
                            $photo_url = apply_filters('woocommerce_variation_swatches_image_url',wp_get_attachment_url( $data_photo ),$data_photo,$attribute_swatch_width,$attribute_swatch_height);

                            if( isset($extend_attribute_for_variation_swatches) && $attribute == $extend_attribute_for_variation_swatches){
                                foreach ($available_variations as $variation){
                                    $key = 'attribute_'.$extend_attribute_for_variation_swatches;
                                    $attributes = isset($variation['attributes']) ? $variation['attributes'] : array();
                                    if( array_key_exists($key,$attributes)  ){
                                        if( $attributes[$key] == $term->slug){
                                            $data_type ='photo';
                                            $photo_url = isset($variation['image']['gallery_thumbnail_src']) ? $variation['image']['gallery_thumbnail_src']: wc_placeholder_img_src() ;
                                            $attribute_swatch_width = isset($variation['image']['gallery_thumbnail_src_w']) ? $variation['image']['gallery_thumbnail_src_w'] : 100;
                                            $attribute_swatch_height = isset($variation['image']['gallery_thumbnail_src_h']) ? $variation['image']['gallery_thumbnail_src_h'] : 100;

                                            break;
                                        }

                                    }
                                }
                            }


                            if ( $data_type == 'color' ) {
                                $html .= '<option data-highlight="'.$option_highlight.'" data-tooltip="'.$data_tooltip.'" data-width="' . $attribute_swatch_width . '" data-height="' . $attribute_swatch_height . '" data-type="' . esc_attr( $data_type ) . '" data-' . esc_attr( $id ) . '="' . esc_attr( $data_color ) . '" value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
                            } elseif ( $data_type == 'photo' ) {
                                $html .= '<option data-tooltip="'.$data_tooltip.'" data-width="' . $attribute_swatch_width . '" data-height="' . $attribute_swatch_height . '" data-type="' . esc_attr( $data_type ) . '" data-url="' . esc_attr( $photo_url ) . '" data-' . esc_attr( $id ) . '=" url(' . esc_url( $photo_url ) . ') " value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
                            } else {
                                $data_type = '';
                                $html .= '<option data-tooltip="'.$data_tooltip.'"  data-type="' . esc_attr( $data_type ) . '" data-' . esc_attr( $id ) . '="' . esc_attr( $term->slug ) . '" value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
                            }

                        }
                    }
                    $html .= '</select>';
                    $html .= '<div class="data-val attribute-' . esc_attr( $id ) . '" data-attributetype="' . $attribute_taxonomy['type'] . '"></div>';
                } else {
                    return $html;
                }
            }

            return $html;
        }
        public function get_product_attribute( $attribute ) {
            global $wpdb;
            $attribute_name = str_replace( 'pa_', '', $attribute );
            try {
                $attribute = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s", $attribute_name ) );
                if ( is_wp_error( $attribute ) || is_null( $attribute ) ) {
                    throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_id', __( 'A product attribute with the provided ID could not be found', 'familabcore' ), 404 );
                }
                $product_attribute = array(
                    'id'           => intval( $attribute->attribute_id ),
                    'name'         => $attribute->attribute_label,
                    'slug'         => wc_attribute_taxonomy_name( $attribute->attribute_name ),
                    'type'         => $attribute->attribute_type,
                    'order_by'     => $attribute->attribute_orderby,
                    'has_archives' => (bool)$attribute->attribute_public,
                );

                return $product_attribute;
            } catch ( WC_API_Exception $e ) {
                return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
            }
        }

        public static function woocommerce_variable_product_before_variations(){
            global $post, $wpdb, $product_object;
            $variation_attributes   = array_filter( $product_object->get_attributes(), array( __CLASS__, 'filter_variation_attributes' ) );

            if(empty($variation_attributes)){
                return '';
            }
            $options = array('' => esc_html__('None','familabcore'));
            foreach ($variation_attributes as $attribute){
                $name = $attribute->get_name();
                $options[sanitize_title($name)] = wc_attribute_label($name);
            }
            $value = get_post_meta($post->ID,'extend_attribute_for_variation_swatches',true);
            ?>
            <div class="toolbar">
                <strong><?php esc_html_e( 'Image Swatches', 'familabcore' ); ?>: <?php echo wc_help_tip( __( 'Select attribute which will show as image swatch.', 'familabcore' ) ); ?></strong>
                <?php
                    woocommerce_wp_select( array(
                        'id'      => 'extend_attribute_for_variation_swatches',
                        'label'   => '',
                        'options' =>  $options, //this is where I am having trouble
                        'value'   =>  $value,
                    ) );
                ?>
                <script>
                    ;(function ($) {
                        $(document).on('change','#extend_attribute_for_variation_swatches',function () {
                            $('#variable_product_options .wc_input_price').trigger('change');
                            var value = $(this).find('option:selected').val();

                            $('#variable_product_options input[name="extend_attribute_for_variation_swatches"]').val(value);
                        })
                    })(jQuery);
                </script>
                <style>
                    #extend_attribute_for_variation_swatches{
                        width: 300px;
                        max-width: 100%;
                    }
                </style>
            </div>
            <?php

        }

        /**
         * Filter callback for finding variation attributes.
         *
         * @param  WC_Product_Attribute $attribute
         * @return bool
         */
        private static function filter_variation_attributes( $attribute ) {
            return true === $attribute->get_variation();
        }

        public static function product_edit_tab_save($post_id){

            if ( isset( $_POST[ 'extend_attribute_for_variation_swatches' ] ) ) {
                if ( $_POST[ 'extend_attribute_for_variation_swatches' ]!='' ) {
                    update_post_meta( $post_id, 'extend_attribute_for_variation_swatches', $_POST[ 'extend_attribute_for_variation_swatches' ]);
                } else {
                    delete_post_meta( $post_id, 'extend_attribute_for_variation_swatches' );
                }
            } else {
                delete_post_meta( $post_id, 'extend_attribute_for_variation_swatches' );
            }
        }
        public static function save_variation($variation_id, $i){
            $post_id = isset($_POST['product_id']) ? $_POST['product_id'] :0;

            if ( isset( $_POST[ 'extend_attribute_for_variation_swatches' ] ) && $post_id > 0 ) {
                if ( $_POST[ 'extend_attribute_for_variation_swatches' ]!='' ) {
                    update_post_meta( $post_id, 'extend_attribute_for_variation_swatches', $_POST[ 'extend_attribute_for_variation_swatches' ]);
                } else {
                    delete_post_meta( $post_id, 'extend_attribute_for_variation_swatches' );
                }
            } else {
                delete_post_meta( $post_id, 'extend_attribute_for_variation_swatches' );
            }
        }

        public static function extend_variation_form($loop, $variation_data, $variation){
            global  $post;

            $value = get_post_meta($post->ID,'extend_attribute_for_variation_swatches',true);
            ?>
            <input type="hidden" name="extend_attribute_for_variation_swatches" value="<?php echo esc_attr($value)?>">
            <input type="hidden" name="product_id" value="<?php echo esc_attr($post->ID)?>">
            <?php
        }
    }
}
