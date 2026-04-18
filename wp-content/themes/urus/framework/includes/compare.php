<?php
if( !class_exists('Urus_Compare')){
    class Urus_Compare{
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
        public static $limitItems = 4;
        public static $products_list = array();
        public static $cookie_name ='urus_compare';
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized || !class_exists('WooCommerce') ) {
                return;
            }
       
            add_filter('urus_settings_section_woocommerce',array(__CLASS__,'settings'),101,1);
            $enable_compare = Urus_Helper::get_option('enable_compare',0);
            if( $enable_compare == 0 ) return;
            
            add_action('urus_function_shop_loop_item_compare',array(__CLASS__,'button'));

            add_action('woocommerce_single_product_summary',array(__CLASS__,'button'),31);
            add_action('urus_compare_button',array(__CLASS__,'button'));
            add_action('wp_footer',array(__CLASS__,'display_wrapper'));
            add_action( 'wp_ajax_urus_compare_products', array(__CLASS__,'urus_compare_products') );
            add_action( 'wp_ajax_nopriv_urus_compare_products', array(__CLASS__,'urus_compare_products') );
            
            self::$initialized = true;
        }
        public static function settings($settings){
            $settings[] = array(
                'title'        => esc_html__( 'Compare', 'urus' ),
                'subsection'   => true,
                'fields'       => array(
                    array(
                        'id'       => 'enable_compare',
                        'type'     => 'switch',
                        'title'    => esc_html__('Enable Compare','urus'),
                        'default'  => true
                    ),
                )
            );
            return $settings;
        }
        
        public static function button(){
            global $product;
            $product_id = $product->get_id();
            $product_info = [];
            $product_info['id'] = $product_id;
            $product_info['title'] = $product->get_title();
            $product_info['url'] = get_permalink( $product_id );
            $attachment_id = get_post_thumbnail_id($product_id);
            $product_img = self::get_product_image($attachment_id);
            $product_info['thumb'] = $product_img;
            $hint_class = Urus_Pluggable_WooCommerce::get_product_loop_hint_class('compare');
            ?>
            <div class="compare-button <?php echo esc_attr($hint_class);?>" aria-label="<?php echo esc_attr__('Compare','urus');?>">
                <a href="javascript:void(0);" class="urus-compare" rel="nofollow"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-product-info="<?php echo esc_attr( wp_json_encode($product_info) ); ?>"
                ><?php  esc_html_e('Add To Compare','urus');?></a>
            </div>
            <?php
        }
        public static function display_wrapper(){
            if( is_shop() || is_product() || is_product_category() ){
                ?>
                <div class="compare-toggle hint--bounce hint--left" aria-label="<?php echo esc_attr__('Compare','urus');?>">
                    <a href="javascript:void(0);" class="compare-panel-btn">
                        <?php esc_html_e( 'Compare', 'urus' ); ?>
                    </a>
                </div>
                <?php
            }
            ?>
            <div id="urus-compare" data-products="{}">
                <div class="urus-compare-wrapper">
                    <div class="compare-loader">
                    </div>
                    <?php Urus_Compare::display_sort_compare_table();?>
                </div>
            </div>
            <?php
            
        }
        public static function urus_compare_products(){
            $data = $_POST['data'];
            $products_array = [];
            foreach ($data as $product_id) {
                $product = new WC_Product( $product_id );
                $product_info['id'] =  $product->get_id();
                $attachment_id = get_post_thumbnail_id($product_id);
                $product_img = self::get_product_image($attachment_id);
                $product_info['thumb'] = $product_img;
                $product_info['title'] = $product->get_title();
                $product_info['url'] = get_permalink( $product_id );
                $product_info['price'] = $product->get_price_html();
                $product_info['type'] = $product->get_type();
                $product_info['desc'] = $product->get_short_description();
                $product_info['type'] = $product->get_type();
                $product_info['available'] = $product->get_stock_status();
                $product_attrs = $product->get_attributes();
                $product_info['attribute_values'] = [];
                $product_info['attribute_label'] = [];
                $attribute_values = [];
                foreach ($product_attrs as $attribute ) :
                    $attr_slug = $attribute->get_name();
                    $attribute_label = wc_attribute_label( $attr_slug );
                    $product_info['attribute_label'][$attr_slug] = $attribute_label;
                    $values = array();
                    if ( $attribute->is_taxonomy() ) {
                        $attribute_taxonomy = $attribute->get_taxonomy_object();
                        $attribute_values = wc_get_product_terms( $product->get_id(), $attr_slug, array( 'fields' => 'all' ) );
                        foreach ( $attribute_values as $attribute_value ) {
                            $value_name = esc_html( $attribute_value->name );
                            $product_info['attribute_values'][$attr_slug][] = $value_name;
                        }
                    }
                endforeach;
                $products_array[] = $product_info;
            }
            wp_send_json($products_array);
            wp_die();
        }
        public static function display_sort_compare_table(){
          $default_placeholder_src = URUS_IMAGES.'/placeholder.png';
          $use_custom_placeholder = Urus_Helper::get_option('enable_custom_placeholder',false);
          $placeholder_url = '';
          if ($use_custom_placeholder) {
            $placeholder_url = Urus_Pluggable_WooCommerce::urus_custom_woocommerce_placeholder( $default_placeholder_src , true);
          }else{
            $placeholder_url = $default_placeholder_src;
          }
            ?>
            <div class="compare-heading row">
                <div class="col-3 compare_heading_title">
                    <h4>
                        <?php esc_html_e( 'Compare products', 'urus' ); ?>
                    </h4>
                    <div class="begin-compare-btn"><?php esc_html_e( 'Show All', 'urus' ); ?></div>
                </div>
                <div class="col-9 d-flex">
                    <div class="compare-close-btn button small">
                        <i class="urus-icon urus-icon-down"></i>
                    </div>
                    <div class="comparing-products d-flex">
                        <?php
                        for ($i=0; $i < 4; $i++) { 
                        ?>
                            <div class="single-compare-item placeholder" data-product-id="" data-placeholder="<?php echo esc_attr( $placeholder_url ); ?>">
                               <div class="compare-remove">
                                  <a href="javascript:void(0);" class="remove_from_compare" title="<?php esc_attr_e( 'Remove from compare', 'urus' ); ?>">
                                    <i class="urus-icon urus-icon-close"></i>
                                  </a>
                               </div>
                               <div class="product-thumbnail-hover">
                                   <a href="javascript:void(0);">
                                       <img alt="product" width="400" height="511" src="<?php echo esc_url( $placeholder_url ); ?>" class="big-thumb-img" >
                                       <div class="product-title"></div>
                                   </a>
                                </div>
                               <div class="product-small-thumbnail">
                                   <a href="javascript:void(0);">
                                       <img alt="product" width="117" height="150" src="<?php echo esc_url( $placeholder_url ); ?>" class="small-thumb-img" >
                                   </a>
                               </div>
                               
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="compare-buttons">
                        <div class="clear-btn"><?php esc_html_e( 'Clear', 'urus' ); ?></div>
                        <div class="begin-compare-btn button"><?php esc_html_e( 'Compare', 'urus' ); ?></div>
                    </div>
                </div>
            </div>
            <div class="compare-table">
                <div class="compare-minimize-btn button small">
                    <i class="fa fa-chevron-down"></i>
                </div>
               <table class="">
                   <tbody>
                      <tr class="image">
                         <th>
                            <?php esc_html_e( 'Product', 'urus' ); ?>                        
                         </th>
                      </tr>
                      <tr class="price">
                         <th>
                            <?php esc_html_e( 'Price', 'urus' ); ?>                                            
                         </th>
                      </tr>
                      <tr class="add-to-cart">
                         <th>
                            <?php esc_html_e( 'Add to cart', 'urus' ); ?>                                         
                         </th>
                      </tr>
                      <tr class="description">
                         <th>
                            <?php esc_html_e( 'Description', 'urus' ); ?>                                            
                         </th>
                      </tr>
                      <tr class="stock">
                         <th>
                            <?php esc_html_e( 'Availability', 'urus' ); ?>                                            
                         </th>
                      </tr>
                      <tr class="remove-item">
                         <th>&nbsp;</th>
                      </tr>
                   </tbody>
                </table>
            </div>
            <?php
        }
        public static function get_product_image($attachment_id, $size = 'thumbnail', $icon = false ){
            $thumb = wp_get_attachment_image_src(  $attachment_id ,$size, $icon );
            if ($thumb == false) {
                $width = 150;
                $height = 150;
                $placeholder_url="";
                $use_custom_placeholder = Urus_Helper::get_option('enable_custom_placeholder',false);
                if ($use_custom_placeholder ) {
                    $placeholder_url = Urus_Pluggable_WooCommerce::urus_custom_woocommerce_placeholder( "" , true);
                }else{
                    $placeholder_url = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $width . "%20" . $height . "%27%2F%3E";
                }
                return $placeholder_url;
            }else{
                return $thumb[0];
            }
        }
        
    }
}