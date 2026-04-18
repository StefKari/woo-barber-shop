<?php
if( !class_exists('Urus_Pluggable_Search_Module')){
    class  Urus_Pluggable_Search_Module{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;

        public static  $live_search_key ='_urus_live_search_products';

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
            add_action('get_header',array(__CLASS__,'rebuild_search_index'));
            //self::rebuild_search_index();
            add_action('save_post_product',array(__CLASS__,'_save_post'),PHP_INT_MAX,3);
            add_action( 'wp_ajax_rebuild_search_index', array(__CLASS__,'ajax_rebuild_search_index') );
            add_action( 'wp_ajax_rebuild_search_sku_index', array(__CLASS__,'ajax_rebuild_search_sku_index') );
            self::$initialized = true;
        }

        public static function _save_post($post_id, $post, $update ){
            if ( wp_is_post_revision( $post_id ) )
                return;
            $process_post = get_option('familab_updated_products',array());
            $process_post[] = $post_id;
            update_option('familab_updated_products',$process_post);
        }
        public static function get_products_by_page($page = 0){
            $full_products_list = array();
            global $wpdb;
            //Query all of the posts

            $sql = "SELECT * FROM `{$wpdb->prefix}posts` WHERE 1=1 AND `{$wpdb->prefix}posts`.post_type = 'product' AND (`{$wpdb->prefix}posts`.post_status = 'publish') ORDER BY `{$wpdb->prefix}posts`.post_date DESC, `{$wpdb->prefix}posts`.post_title DESC LIMIT ".($page*100).",100";

            $rows = $wpdb->get_results( $sql );

            if ( $rows ) {
                $full_products_list = get_option('_urus_live_search_products');
                foreach ( $rows as $row ) {
                    //$variant_sku = [];
                    $product = wc_get_product( $row->ID );
                    if (!$product)
                        continue;
                    $lang_info = apply_filters( 'wpml_post_language_details', NULL, $row->ID ) ;
                    $lang_url = '';
                    $product_url = get_permalink( $product->get_id());
                    if (!empty($lang_info)){
                        $lang_url = apply_filters( 'wpml_permalink', $product_url, $lang_info['language_code']);
                    }
                    $attachment_id = get_post_thumbnail_id($row->ID);
                    $product_img = self::get_product_image($attachment_id, "medium");
                    $product_cat = array();
                    $terms = get_the_terms( $row->ID, 'product_cat' );
                    if( !is_wp_error($terms) && !empty($terms)){
                        foreach ( $terms as $term ) {
                            $product_cat[] = $term->slug;
                        }
                    }
                    if( did_action( 'elementor/loaded' )){
                        $pluginElementor = \Elementor\Plugin::instance();
                        $content = $pluginElementor->frontend->get_builder_content($row->post_content);
                    } else {
                        $content = do_shortcode($row->post_content);
                    }
                    if (strpos($content,'vc_row') !== false){
                        $content = strip_shortcodes($content);
                    }
                    $full_products_list[$row->ID] = array(
                        'title'         => $row->post_title,
                        'url'           => ($lang_url != '')? $lang_url : $product_url,
                        'price_html'    => $product->get_price_html(),
                        'content'       => esc_html($content),
                        'excerpt'       => esc_html($row->post_excerpt),
                        'id'            => $row->ID,
                        'img'           => $product_img,
                        'rating'        => wc_get_rating_html(  $product->get_average_rating() , $product->get_rating_count() ),
                        'product_cat'   => $product_cat,
                        'product_sku'   => [],
                        'language'      => $lang_info,
                        'variant_sku'   => []
                    );
                }
            }
            return $full_products_list;
        }
        public static function get_all_product($ids = array()){
            $full_products_list = array();
            global $wpdb;
            $pattern = '/\[(.*?)\]/m';
            //Query all of the posts
            $sql_ids = '';
            if (sizeof($ids) > 0){
                $sql_ids = " AND `{$wpdb->prefix}posts`.id IN (".implode(',',$ids).') ';
            }
            $sql = "SELECT * FROM `{$wpdb->prefix}posts` WHERE 1=1 AND `{$wpdb->prefix}posts`.post_type = 'product'". $sql_ids." AND (`{$wpdb->prefix}posts`.post_status = 'publish') ORDER BY `{$wpdb->prefix}posts`.post_date DESC, `{$wpdb->prefix}posts`.post_title DESC";

            $rows = $wpdb->get_results( $sql );

            if ( $rows ) {
                foreach ( $rows as $row ) {
                    //$variant_sku = [];
                    $product = wc_get_product( $row->ID );
                    if (!$product)
                        continue;
                    $lang_info = apply_filters( 'wpml_post_language_details', NULL, $row->ID ) ;
                    $lang_url = '';
                    $product_url = get_permalink( $product->get_id());
                    if (!empty($lang_info)){
                        $lang_url = apply_filters( 'wpml_permalink', $product_url, $lang_info['language_code']);
                    }
                    $attachment_id = get_post_thumbnail_id($row->ID);
                    $product_img = self::get_product_image($attachment_id, "medium");
                    $product_cat = array();
                    $terms = get_the_terms( $row->ID, 'product_cat' );
                    if( !is_wp_error($terms) && !empty($terms)){
                        foreach ( $terms as $term ) {
                            $product_cat[] = $term->slug;
                        }
                    }
                    /*if ($product->get_type() == 'variable'  ) {
                        $variations = $product->get_available_variations();
                        foreach ($variations as $key => $variation) {
                            $sku = $variation['sku'];
                            $variant_sku[] = $sku;
                        }
                    }*/

                    if( did_action( 'elementor/loaded' )){
                        $pluginElementor = \Elementor\Plugin::instance();
                        $content = $pluginElementor->frontend->get_builder_content($row->post_content);
                    } else {
                        $content = do_shortcode($row->post_content);
                    }
                    $content = preg_replace($pattern,'',$content);
                    $full_products_list[$row->ID] = array(
                        'title'         => $row->post_title,
                        'url'           => ($lang_url != '')? $lang_url : $product_url,
                        'price_html'    => $product->get_price_html(),
                        'content'       => esc_html($content),
                        'excerpt'       => esc_html($row->post_excerpt),
                        'id'            => $row->ID,
                        'img'           => $product_img,
                        'rating'        => wc_get_rating_html(  $product->get_average_rating() , $product->get_rating_count() ),
                        'product_cat'   => $product_cat,
                        'product_sku'   => [],
                        'language'      => $lang_info,
                        'variant_sku'   => []
                    );
                }
            }
            return $full_products_list;
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
        public static function rebuild_search_index(){
            if (Urus_Helper::get_option('auto_rebuild_search_index',false)){
                $search_index_transient = get_transient('search_index_transient');
                if ($search_index_transient === false){
                    if ($process_post = get_option('familab_updated_products',false)){
                        $all_product = get_option(self::$live_search_key,array());
                        if (empty($all_product)){
                            $all_product = self::get_all_product();
                            $all_product = self::search_sku_index($all_product);
                        }else{
                            if (is_array($process_post) && !empty($process_post)){
                                $process_products = self::get_all_product($process_post);
                                $process_products = self::search_sku_index($process_products);
                                $all_product = array_merge($all_product,$process_products);
                            }
                        }
                        update_option(self::$live_search_key,$all_product);
                        delete_option('familab_updated_products');
                    }
                    set_transient('search_index_transient', true, HOUR_IN_SECONDS);
                }
            }
        }
        public static function search_sku_index($products){
            foreach ($products as $p_id => $value){
                $product = wc_get_product($p_id);
                $variant_sku = [];
                if ($product->get_type() == 'variable'  ) {
                    $variations = $product->get_available_variations();
                    foreach ($variations as $key => $variation) {
                        $sku = $variation['sku'];
                        $variant_sku[] = $sku;
                    }
                }
                $products[$p_id]['product_sku'] = $product->get_sku();
                $products[$p_id]['variant_sku'] = $variant_sku;
            }
            return $products;
        }
        public static function ajax_rebuild_search_index(){
            check_ajax_referer( 'urus_ajax_admin', 'security' );
            $page =  isset( $_POST['page'] ) ? $_POST['page'] : 0;
            if ($page == 0){
                delete_option('_urus_live_search_products');
                delete_option('familab_updated_products');
            }
            $result = array();
            $products = self::get_products_by_page($page);
            if ($products && !empty($products)){
                update_option('_urus_live_search_products',$products);
                $total = count((array)$products);
                if (sizeof($products) < 100){
                    $result['finish'] = true;
                }
            } else {
                $full_products_list = get_option('_urus_live_search_products');
                $total = count((array)$full_products_list);
                $result['finish'] = true;
            }
            $result['status'] = true;
            $result['total'] = $total;
            $result['msg'] = 'Synchronization of '.$total.' products completed!';
            return wp_send_json($result);
        }
        public static function ajax_rebuild_search_sku_index(){
            //check_ajax_referer( 'urus_ajax_admin', 'security' );
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }
            $file_products_tmp = get_template_directory().'/assets/products_tmp.json';
            $result = array();
            $result['pos'] = isset( $_POST['pos'] ) ? $_POST['pos'] : 0;
            $products = get_option('_urus_live_search_products');
            if ($result['pos'] == 0){
                $products_waiting = array_keys($products);

            } else {
            	if ( $wp_filesystem->exists( $file_products_tmp ) ) {
                	$products_waiting = json_decode( $wp_filesystem->get_contents( $file_products_tmp ),true );
                } else {
                        $products_waiting = [];
                }
            }
            if (sizeof($products_waiting) > 0){
                $nb = 0;
                $products_tmp = $products_waiting;

                foreach ($products_waiting as $key => $value){
                    $product = wc_get_product($value);
                    $variant_sku = [];
                    $nb++;
                    if ($product->get_type() == 'variable'  ) {
                        $variations = $product->get_available_variations();
                        foreach ($variations as $key_u => $variation) {
                            $sku = $variation['sku'];
                            $variant_sku[] = $sku;
                        }
                    }
                    $products[$value]['product_sku'] = $product->get_sku();
                    $products[$value]['variant_sku'] = $variant_sku;

                    unset($products_tmp[$key]);
                    if ($nb > 99){
                        break;
                    }
                }

                update_option('_urus_live_search_products',$products);
                if (sizeof($products_tmp) > 0){
                	$wp_filesystem->put_contents( $file_products_tmp,json_encode($products_tmp), FS_CHMOD_FILE );
                    $result['finish'] = false;
                } else {
                    unlink($file_products_tmp);
                    $result['finish'] = true;
                }

            }else{
                unlink($file_products_tmp);
                $result['finish'] = true;
            }
            return wp_send_json($result);
        }
    }
}
