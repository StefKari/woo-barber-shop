<?php
if( !class_exists('Urus_Pluggable_Woo_Variation_Swatches')){
    class Urus_Pluggable_Woo_Variation_Swatches{
        public static $attribute_group = array();
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
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
            if( !Urus_Mobile_Detect::isMobile()){
                add_action('urus_variation_swatches_loop_item',array(__CLASS__,'variation_swatches_loop_item'),11);
                add_action('urus_variation_form_loop_item',array(__CLASS__,'urus_variation_form_loop_item'),11);
            }

            add_filter('urus_settings_section_field_product_items',array(__CLASS__,'settings'),100,1);
            // State that initialization completed.
            self::$initialized = true;
        }
        public static function variation_swatches_loop_item(){
            $enable_variation_loop_product = Urus_Helper::get_option('enable_variation_loop_product',0);
            if ( $enable_variation_loop_product == 0 ) return;
            $attribute_use_in_loop_product = Urus_Helper::get_option('attribute_use_in_loop_product','');
            global $product;
            wp_enqueue_script( 'wc-add-to-cart-variation' );
            $html = '';
            if ( $product->get_type() == 'variable' ){
                ob_start();
                ?>
                <div class="product-loop-variations_swatch_attribute" data-attribute="<?php echo esc_attr($attribute_use_in_loop_product);?>">
                </div>
                <?php
                $html = ob_get_clean();
            }
            echo apply_filters('urus_loop_woo_variation_swatches',$html);
        }
        public static function urus_variation_form_loop_item(){
            $enable_variation_loop_product = Urus_Helper::get_option('enable_variation_loop_product',0);
            if ( $enable_variation_loop_product == 0 ) return;
            global $product;
            $html = '';
            if ( $product->get_type() == 'variable' ){
                $id_product = $product->get_id();
                $get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
                $available_variations = $get_variations ? $product->get_available_variations() : false;
                $variations_json = wp_json_encode( $available_variations );
                $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
                $attributes = $product->get_variation_attributes();
                ob_start();
                ?>
                <form class="variations_form cart other-variation" action="<?php echo esc_url( get_permalink() ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $id_product ); ?>" data-product_variations="<?php echo e_data($variations_attr); // WPCS: XSS ok. ?>">
                    <div class="product-loop-variations variations ">
                        <?php
                        foreach ( $attributes as $attribute_name => $options ){
                            $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );
                            wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
                        }
                        ?>
                    </div>
                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr(   $id_product ); ?>">
                    <input type="hidden" name="product_id" value="<?php echo esc_attr(   $id_product ); ?>">
                    <input type="hidden" name="variation_id" value="0">
                </form>
                <?php
                $html = ob_get_clean();
            }
            echo apply_filters('urus_loop_woo_variation_form',$html);
        }
        public static function settings( $settings){
            $attributes_settings = array();
            $attributes = Urus_Pluggable_Woo_Variation_Swatches::wc_get_attribute_taxonomies();

            if( !empty($attributes)){
                foreach ($attributes as  $attribute){
                    $attributes_settings[$attribute->attribute_name] = $attribute->attribute_label;
                }
            }
            $settings[] = array(
                        'id'       => 'enable_variation_loop_product',
                        'type'     => 'switch',
                        'title'    => esc_html__('Enable Variation','urus'),
                        'default'  => false
                    );
            $settings[] = array(
                        'id'       => 'attribute_use_in_loop_product',
                        'type'     => 'select',
                        'title'    => esc_html__('Attribute display', 'urus'),
                        'subtitle' => esc_html__('Select a attribute display in product loop', 'urus'),
                        'options'  => $attributes_settings,
                        'required' => array(
                            array( 'enable_variation_loop_product', '=', array(1)),
                        )
            );
            $settings[] = array(
                'id'       => 'enable_quick_add_loop_product',
                'type'     => 'switch',
                'title'    => esc_html__('Enable Quick Buy','urus'),
                'default'  => false,
                'required' => array(
                    array( 'enable_variation_loop_product', '=', array(1)),
                )
            );
            return $settings;

        }
        /**
         * Get attribute
         * @return array
         */
        public static function wc_get_attribute_taxonomies(){
            global $woocommerce;
            if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
                return wc_get_attribute_taxonomies();
            } else {
                return $woocommerce->get_attribute_taxonomies();
            }
        }
    }
}
