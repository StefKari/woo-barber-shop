<?php
if( !class_exists('Urus_Pluggable_Visual_Composer')){
    class Urus_Pluggable_Visual_Composer{
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
            // Custom vc_custom_heading
            vc_remove_param( "vc_custom_heading", "text" );
            $attributes = array(
                'type' => 'textarea_raw_html',
                'heading' => esc_html__( 'Text', 'urus' ),
                'param_name' => 'text',
                'holder' => 'div',
                'value' => esc_html__( 'This is custom heading element', 'urus' ),
                'description' => esc_html__( 'Note: If you are using non-latin characters be sure to activate them under Settings/Visual Composer/General Settings.', 'urus' ),
                'dependency' => array(
                    'element' => 'source',
                    'is_empty' => true,
                ),
                'weight' => 1
            );
            vc_add_param( 'vc_custom_heading', $attributes ); // Note: 'vc_message' was used as a base for "Message box" element
            add_filter('shortcode_atts_vc_custom_heading',array( __CLASS__,'output_vc_custom_heading'),10,4);
            add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 999 );
            add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
            self::add_params();
            add_action( 'vc_after_mapping', array( __CLASS__, 'add_param_all_shortcode' ) );
            add_filter( 'vc_autocomplete_urus_products_ids_callback', array( __CLASS__, 'productIdAutocompleteSuggester' ), 10, 1 );
            add_filter( 'vc_autocomplete_urus_products_ids_render', array( __CLASS__, 'productIdAutocompleteRender' ), 10, 1 );

            add_filter( 'vc_autocomplete_urus_product_deal_id_callback', array( __CLASS__, 'productIdAutocompleteSuggester' ), 10, 1 );
            add_filter( 'vc_autocomplete_urus_product_deal_id_render', array( __CLASS__, 'productIdAutocompleteRender' ), 10, 1 );
            add_filter( 'vc_autocomplete_urus_categories_product_cats_callback', array( __CLASS__, 'product_catsAutocompleteSuggester' ), 10, 1 );
            add_filter( 'vc_autocomplete_urus_categories_product_cats_render', array( __CLASS__, 'product_catsAutocompleteRender' ), 10, 1 );
            add_filter('vc_iconpicker-type-fontawesome',array(__CLASS__,'vc_iconpicker_type_fontawesome'));
            add_action( 'wp_ajax_urus_get_tabs_shortcode', array(__CLASS__,'get_tabs_shortcode') );
            add_action( 'wp_ajax_nopriv_urus_get_tabs_shortcode', array(__CLASS__,'get_tabs_shortcode') );

            add_filter('vc_google_fonts_get_fonts_filter',array(__CLASS__,'vc_google_fonts_get_fonts_filter'));
            add_action( 'vc_load_default_templates_action',array(__CLASS__,'add_custom_template_for_vc') ); // Hook in
            // State that initialization completed.
            self::$initialized = true;
        }
        public static function product_catsAutocompleteSuggester($query){
            $results         = array();
            $args = array(
                'hide_empty' => 0,
                'name__like' => $query
            );
            $categories = Urus_Pluggable_WooCommerce::get_categories($args);
            if(!empty($categories)){
                foreach ($categories as $cat){
                    $parent = isset($cat->parent) ? $cat->parent :0;
                    $label = '';
                    if($parent >0){
                        $cat_parent = get_term($parent);
                        if( !empty($cat_parent)){
                            $label .=$cat_parent->name.' - ';
                        }
                    }
                    $label .= $cat->name;
                    $data = array(
                        'value' => $cat->slug,
                        'label' => $label
                    );
                    $results[] = $data;
                }
            }
            return $results;
        }
        public static function product_catsAutocompleteRender($query){
            $query = trim( $query['value'] ); // get value from requested
            $results         = array();
            $args = array(
                'hide_empty' => 0,
            );
            $args['slug'] = explode(',',$query);
            $categories = Urus_Pluggable_WooCommerce::get_categories($args);
            if(!empty($categories)){
                foreach ($categories as $cat){
                    $data = array(
                        'value' => $cat->slug,
                        'label' => $cat->name
                    );
                    return $data;
                }
            }
            return false;
        }
        /**
         * Suggester for autocomplete by id/name/title/sku
         * @since 4.4
         *
         * @param $query
         *
         * @return array - id's from products with title/sku.
         */
        public static function productIdAutocompleteSuggester( $query ) {
            global $wpdb;
            $product_id      = (int)$query;
            $post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT a.ID AS id, a.post_title AS title, b.meta_value AS sku
					FROM {$wpdb->posts} AS a
					LEFT JOIN ( SELECT meta_value, post_id  FROM {$wpdb->postmeta} WHERE `meta_key` = '_sku' ) AS b ON b.post_id = a.ID
					WHERE a.post_type = 'product' AND ( a.ID = '%d' OR b.meta_value LIKE '%%%s%%' OR a.post_title LIKE '%%%s%%' )", $product_id > 0 ? $product_id : -1, stripslashes( $query ), stripslashes( $query )
            ), ARRAY_A
            );
            $results         = array();
            if ( is_array( $post_meta_infos ) && !empty( $post_meta_infos ) ) {
                foreach ( $post_meta_infos as $value ) {
                    $data          = array();
                    $data['value'] = $value['id'];
                    $data['label'] = esc_html__( 'Id', 'urus' ) . ': ' . $value['id'] . ( ( strlen( $value['title'] ) > 0 ) ? ' - ' . esc_html__( 'Title', 'urus' ) . ': ' . $value['title'] : '' ) . ( ( strlen( $value['sku'] ) > 0 ) ? ' - ' . esc_html__( 'Sku', 'urus' ) . ': ' . $value['sku'] : '' );
                    $results[]     = $data;
                }
            }
            return $results;
        }
        /**
         * Find product by id
         * @since 4.4
         *
         * @param $query
         *
         * @return bool|array
         */
        public static function productIdAutocompleteRender( $query ) {
            $query = trim( $query['value'] ); // get value from requested
            if ( !empty( $query ) ) {
                // get product
                $product_object = wc_get_product( (int)$query );
                if ( is_object( $product_object ) ) {
                    $product_sku         = $product_object->get_sku();
                    $product_title       = $product_object->get_title();
                    $product_id          = $product_object->get_id();
                    $product_sku_display = '';
                    if ( !empty( $product_sku ) ) {
                        $product_sku_display = ' - ' . esc_html__( 'Sku', 'urus' ) . ': ' . $product_sku;
                    }
                    $product_title_display = '';
                    if ( !empty( $product_title ) ) {
                        $product_title_display = ' - ' . esc_html__( 'Title', 'urus' ) . ': ' . $product_title;
                    }
                    $product_id_display = esc_html__( 'Id', 'urus' ) . ': ' . $product_id;
                    $data               = array();
                    $data['value']      = $product_id;
                    $data['label']      = $product_id_display . $product_title_display . $product_sku_display;
                    return !empty( $data ) ? $data : false;
                }
                return false;
            }
            return false;
        }
        public static function add_param_all_shortcode(){
            global $shortcode_tags;
            WPBMap::addAllMappedShortcodes();
            if( !empty( $shortcode_tags)){
                foreach ( $shortcode_tags as $tag => $function ) {
                    $attributes = array(
                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__( 'Extra class name', 'urus' ),
                            'param_name'  => 'el_class',
                            'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'urus' ),
                        ),
                        array(
                            'param_name'       => 'urus_custom_id',
                            'heading'          => esc_html__( 'Hidden ID', 'urus' ),
                            'type'             => 'uniqid',
                            'edit_field_class' => 'hidden',
                        ),
                    );
                    vc_add_params( $tag, $attributes );
                }
            }
        }
        public static function admin_init(){
            // Code here
        }
        public static function enqueue_scripts(){
            wp_enqueue_script( 'chosen', URUS_THEME_URI  . '/assets/js/admin/chosen.min.js', array( 'jquery' ));
            wp_enqueue_style( 'chosen', URUS_THEME_URI  . '/assets/css/admin/chosen.min.css' );
        }
        public static function add_params(){
            $fnc ='vc_add_'.'shortcode_param';
            $fnc( 'taxonomy', array( __CLASS__, 'add_taxonomy_param' ), URUS_THEME_URI  . '/assets/js/admin/visual-composer/fields/taxonomy.js' );
            $fnc( 'multiselect', array( __CLASS__, 'add_multiselect_param' ), URUS_THEME_URI  . '/assets/js/admin/visual-composer/fields/taxonomy.js' );
            $fnc( 'number', array( __CLASS__, 'add_number_param' ) );
            $fnc( 'datepicker', array( __CLASS__, 'add_datepicker_param' ), URUS_THEME_URI  . '/assets/js/admin/visual-composer/fields/datepicker.js' );
            $fnc( 'uniqid', array( __CLASS__, 'add_uniqid_param' ) );
            return true;
        }
        public static function output_vc_custom_heading( $out, $pairs, $atts, $shortcode){
            $func_d = 'base64_'.'decode';
            $func_e = 'base64_'.'encode';
            $data = strip_tags( $out['text'] );
            if ( $func_e($func_d($data, true)) === $data){
                $out['text'] =  rawurldecode( $func_d( strip_tags( $out['text'] ) ) );
            }
            return $out;
        }
        /**
         * Add 'taxonomy' field param
         * @param $settings
         * @param $value
         * @return string
         */
        public static function add_taxonomy_param( $settings, $value ){
            $dependency = '';
            $value_arr  = $value;
            if ( !is_array( $value_arr ) ) {
                $value_arr = array_map( 'trim', explode( ',', $value_arr ) );
            }
            $output = '';
            if ( isset( $settings['options']['hide_empty'] ) && $settings['options']['hide_empty'] == true ) {
                $settings['options']['hide_empty'] = 1;
            } else {
                $settings['options']['hide_empty'] = 0;
            }
            if ( !empty( $settings['options']['taxonomy'] ) ) {
                $terms_fields = array();
                if ( isset( $settings['options']['placeholder'] ) && $settings['options']['placeholder'] ) {
                    $terms_fields[] = "<option value=''>" . $settings['options']['placeholder'] . "</option>";
                }
                $terms = get_terms( $settings['options']['taxonomy'],
                    array(
                        'hierarchical' => 1,
                        'hide_empty'   => $settings['options']['hide_empty'],
                    )
                );
                if ( $terms && !is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $selected       = ( in_array( $term->slug, $value_arr ) ) ? ' selected="selected"' : '';
                        $terms_fields[] = "<option value='{$term->slug}' {$selected}>{$term->name}</option>";
                    }
                }
                $size     = ( !empty( $settings['options']['size'] ) ) ? 'size="' . $settings['options']['size'] . '"' : '';
                $multiple = ( !empty( $settings['options']['multiple'] ) ) ? 'multiple="multiple"' : '';
                $uniqeID  = uniqid();
                $output   = '<select style="width:100%;" id="vc_taxonomy-' . $uniqeID . '" ' . $multiple . ' ' . $size . ' name="' . $settings['param_name'] . '" class="urus_vc_taxonomy wpb_vc_param_value wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field" ' . $dependency . '>'
                    . implode( $terms_fields )
                    . '</select>';
            }
            return $output;
        }
        /**
         * Add 'multiselect' field param
         * @param $settings
         * @param $value
         * @return string
         */
        public static function add_multiselect_param( $settings, $value ){
            $dependency = '';
            $value_arr  = $value;
            if ( !is_array( $value_arr ) ) {
                $value_arr = array_map( 'trim', explode( ',', $value_arr ) );
            }
            $option_fields = array();
            if ( !empty( $settings['value'] ) ) {
                foreach ( $settings['value'] as $key => $item ) {
                    $selected        = ( in_array( $item, $value_arr ) ) ? ' selected="selected"' : '';
                    $option_fields[] = "<option value='{$item}' {$selected}>{$key}</option>";
                }
            }
            $size     = ( !empty( $settings['options']['size'] ) ) ? 'size="' . $settings['options']['size'] . '"' : '';
            $multiple = ( !empty( $settings['options']['multiple'] ) ) ? 'multiple="multiple"' : '';
            $uniqeID  = uniqid();
            $output   = '<select style="width:100%;" id="vc_taxonomy-' . $uniqeID . '" ' . $multiple . ' ' . $size . ' name="' . $settings['param_name'] . '" class="urus_vc_taxonomy wpb_vc_param_value wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field" ' . $dependency . '>'
                . implode( $option_fields )
                . '</select>';
            return $output;
        }
        /**
         * Add 'number' field param
         * @param $settings
         * @param $value
         * @return string
         */
        public static function add_number_param( $settings, $value ){
            $dependency = '';
            $param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
            $type       = isset( $settings['type '] ) ? $settings['type'] : '';
            $min        = isset( $settings['min'] ) ? $settings['min'] : '';
            $max        = isset( $settings['max'] ) ? $settings['max'] : '';
            $suffix     = isset( $settings['suffix'] ) ? $settings['suffix'] : '';
            $class      = isset( $settings['class'] ) ? $settings['class'] : '';
            if ( !$value && $value == '' && isset( $settings['value'] ) && $settings['value'] != '' ) {
                $value = $settings['value'];
            }
            $output = '<input type="number" min="' . esc_attr( $min ) . '" max="' . esc_attr( $max ) . '" class="wpb_vc_param_value textfield ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . esc_attr( $value ) . '" ' . $dependency . ' style="max-width:100px; margin-right: 10px;line-height:23px;height:auto;" />' . $suffix;
            return $output;
        }
        /**
         * Add 'datepicker' field param
         * @param $settings
         * @param $value
         * @return string
         */
        public static function add_datepicker_param( $settings, $value ){
            $dependency   = '';
            $current_date = date( 'm/d/Y h:i:s', time() );
            $param_name   = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
            $type         = isset( $settings['type '] ) ? $settings['type'] : '';
            $class        = isset( $settings['class'] ) ? $settings['class'] : '';
            $default      = isset( $settings['std'] ) ? $settings['std'] : $current_date;
            if ( !$value ) {
                $value = $default;
            }
            $date_time  = explode( ' ', $value );
            $main_class = $param_name . ' ' . $type . ' ' . $class;
            ob_start();
            ?>
            <div class="vc-date-time-picker" xmlns="http://www.w3.org/1999/html">
                <label class="urus-vc-field-date" <?php echo esc_attr( $dependency ); ?>>
                    <input value="<?php echo esc_attr( $date_time[0] ); ?>" type="text" class="textfield vc-field-date"
                           style="margin-right:10px;width: auto">
                    <span><?php echo esc_html__( 'mm/dd/yy', 'urus' ); ?></span>
                    <textarea class="cs-datepicker-options hidden">{"dateFormat":"m\/d\/yy"}</textarea>
                </label>
                <label>
                    <input value="<?php echo esc_attr( $date_time[1] ); ?>" type="time" class="textfield vc-field-time"
                           style="width: auto;margin-left:10px;margin-right:10px;">
                    <span><?php echo esc_html__( 'hh:mm:ss', 'urus' ); ?></span>
                </label>
                <input name="<?php echo esc_attr( $param_name ); ?>"
                       value="<?php echo esc_attr( $value ); ?>"
                       type="text"
                       class="hidden wpb_vc_param_value textfield vc-field-date-value <?php echo esc_attr( $main_class ); ?>">
            </div>
            <?php
            return $output = ob_get_clean();
        }
        /**
         * Add 'uniqid' field param
         * @param $settings
         * @param $value
         * @return string
         */
        public static function add_uniqid_param( $settings, $value ){
            if ( !$value ) {
                $value = 'urus_custom_id_' . uniqid();
            }
            $output = '<input type="text" class="wpb_vc_param_value wpb-textinput ' . $settings['param_name'] . ' textfield" name="' . $settings['param_name'] . '" value="' . esc_attr( $value ) . '" />';
            return $output;
        }
        public static function vc_carousel( $dependency = null, $value_dependency = null ){
            $data_value      = array();
            $data_carousel   = array(
                'owl_number_row'       => array(
                    'type'       => 'dropdown',
                    'value'      => array(
                        esc_html__( '1 Row', 'urus' )  => '1',
                        esc_html__( '2 Rows', 'urus' ) => '2',
                        esc_html__( '3 Rows', 'urus' ) => '3',
                        esc_html__( '4 Rows', 'urus' ) => '4',
                        esc_html__( '5 Rows', 'urus' ) => '5',
                        esc_html__( '6 Rows', 'urus' ) => '6',
                    ),
                    'std'        => '1',
                    'heading'    => esc_html__( 'The number of rows which are shown on block', 'urus' ),
                    'param_name' => 'owl_number_row',
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency' => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_rows_space'       => array(
                    'type'       => 'dropdown',
                    'heading'    => esc_html__( 'Rows space', 'urus' ),
                    'param_name' => 'owl_rows_space',
                    'value'      => array(
                        esc_html__( 'Default', 'urus' ) => 'rows-space-0',
                        esc_html__( '10px', 'urus' )    => 'rows-space-10',
                        esc_html__( '20px', 'urus' )    => 'rows-space-20',
                        esc_html__( '30px', 'urus' )    => 'rows-space-30',
                        esc_html__( '40px', 'urus' )    => 'rows-space-40',
                        esc_html__( '50px', 'urus' )    => 'rows-space-50',
                        esc_html__( '60px', 'urus' )    => 'rows-space-60',
                        esc_html__( '70px', 'urus' )    => 'rows-space-70',
                        esc_html__( '80px', 'urus' )    => 'rows-space-80',
                        esc_html__( '90px', 'urus' )    => 'rows-space-90',
                        esc_html__( '100px', 'urus' )   => 'rows-space-100',
                    ),
                    'std'        => 'rows-space-0',
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency' => array(
                        'element' => 'owl_number_row', 'value' => array( '2', '3', '4', '5', '6' ),
                    ),
                ),
                'owl_fade'      => array(
                    'type'       => 'dropdown',
                    'value'      => array(
                        esc_html__( 'Yes', 'urus' ) => 'true',
                        esc_html__( 'No', 'urus' )  => 'false',
                    ),
                    'std'        => 'false',
                    'heading'    => esc_html__( 'Fade', 'urus' ),
                    'param_name' => 'owl_fade',
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency' => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_center_mode'      => array(
                    'type'       => 'dropdown',
                    'value'      => array(
                        esc_html__( 'Yes', 'urus' ) => 'true',
                        esc_html__( 'No', 'urus' )  => 'false',
                    ),
                    'std'        => 'false',
                    'heading'    => esc_html__( 'Center Mode', 'urus' ),
                    'param_name' => 'owl_center_mode',
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency' => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_center_padding'   => array(
                    'type'        => 'number',
                    'heading'     => esc_html__( 'Center Padding', 'urus' ),
                    'param_name'  => 'owl_center_padding',
                    'value'       => '50',
                    'min'         => 0,
                    'suffix'      => esc_html__( 'Pixel', 'urus' ),
                    'description' => esc_html__( 'Distance( or space) between 2 item', 'urus' ),
                    'group'       => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency'  => array(
                        'element' => 'owl_center_mode', 'value' => array( 'true' ),
                    ),
                ),
                'owl_vertical'         => array(
                    'type'       => 'dropdown',
                    'value'      => array(
                        esc_html__( 'Yes', 'urus' ) => 'true',
                        esc_html__( 'No', 'urus' )  => 'false',
                    ),
                    'std'        => 'false',
                    'heading'    => esc_html__( 'Vertical Mode', 'urus' ),
                    'param_name' => 'owl_vertical',
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency' => array(
                        'element' => $dependency,
                        'value' => array( $value_dependency ),
                    ),
                ),
                'owl_verticalswiping'  => array(
                    'type'       => 'dropdown',
                    'value'      => array(
                        esc_html__( 'Yes', 'urus' ) => 'true',
                        esc_html__( 'No', 'urus' )  => 'false',
                    ),
                    'std'        => 'false',
                    'heading'    => esc_html__( 'verticalSwiping', 'urus' ),
                    'param_name' => 'owl_verticalswiping',
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency' => array(
                        'element' => 'owl_vertical', 'value' => array( 'true' ),
                    ),
                ),
                'owl_autoplay'         => array(
                    'type'       => 'dropdown',
                    'value'      => array(
                        esc_html__( 'Yes', 'urus' ) => 'true',
                        esc_html__( 'No', 'urus' )  => 'false',
                    ),
                    'std'        => 'false',
                    'heading'    => esc_html__( 'AutoPlay', 'urus' ),
                    'param_name' => 'owl_autoplay',
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency' => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_autoplayspeed'    => array(
                    'type'        => 'number',
                    'heading'     => esc_html__( 'Autoplay Speed', 'urus' ),
                    'param_name'  => 'owl_autoplayspeed',
                    'value'       => '1000',
                    'min'         => 0,
                    'suffix'      => esc_html__( 'milliseconds', 'urus' ),
                    'description' => esc_html__( 'Autoplay speed in milliseconds', 'urus' ),
                    'group'       => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency'  => array(
                        'element' => 'owl_autoplay', 'value' => array( 'true' ),
                    ),
                ),
                'owl_navigation'       => array(
                    'type'        => 'dropdown',
                    'value'       => array(
                        esc_html__( 'No', 'urus' )  => 'false',
                        esc_html__( 'Yes', 'urus' ) => 'true',
                    ),
                    'std'         => 'true',
                    'heading'     => esc_html__( 'Navigation', 'urus' ),
                    'param_name'  => 'owl_navigation',
                    'description' => esc_html__( "Show buton 'next' and 'prev' buttons.", 'urus' ),
                    'group'       => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_navigation_style' => array(
                    'type'       => 'dropdown',
                    'heading'    => esc_html__( 'Navigation style', 'urus' ),
                    'param_name' => 'owl_navigation_style',
                    'value'      => array(
                        esc_html__( 'Default', 'urus' ) => '',
                        esc_html__( 'Style 01', 'urus' ) => 'style1',
                        esc_html__( 'Style 02', 'urus' ) => 'style2',
                        esc_html__( 'Style 03', 'urus' ) => 'style3',
                        esc_html__( 'Style 04', 'urus' ) => 'style4',
                        esc_html__( 'Style 05', 'urus' ) => 'style5',
                        esc_html__( 'Style 06', 'urus' ) => 'style6',

                    ),
                    'std'        => '',
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency' => array( 'element' => 'owl_navigation', 'value' => array( 'true' ) ),
                ),
                'owl_nav_position'      => array(
	                'type'       => 'dropdown',
	                'heading'    => esc_html__( 'Navigation position', 'urus' ),
	                'param_name' => 'owl_nav_position',
	                'value'      => array(
		                esc_html__( 'Default', 'urus' ) => '',
		                esc_html__( 'Navigation on top', 'urus' )  => 'nav-top',
		                esc_html__( 'Navigation center', 'urus' )  => 'nav-center',
	                ),
	                'std'        => 'nav-center',
	                'group'      => esc_html__( 'Carousel Settings', 'urus' ),
	                'dependency' => array( 'element' => 'owl_navigation', 'value' => array( 'true' ) ),
                ),
                'owl_dots'             => array(
                    'type'        => 'dropdown',
                    'value'       => array(
                        esc_html__( 'No', 'urus' )  => 'false',
                        esc_html__( 'Yes', 'urus' ) => 'true',
                    ),
                    'std'         => 'false',
                    'heading'     => esc_html__( 'Dots', 'urus' ),
                    'param_name'  => 'owl_dots',
                    'description' => esc_html__( "Show dots buttons.", 'urus' ),
                    'group'       => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_dots_style'             => array(
	                'type'        => 'dropdown',
	                'value'       => array(
		                esc_html__( 'Default', 'urus' )  => '',
		                esc_html__( 'Style 1', 'urus' ) => 'style1',
		                esc_html__( 'Style 2', 'urus' ) => 'style2',
		                esc_html__( 'Style 3', 'urus' ) => 'style3',
	                ),
	                'std'         => '',
	                'heading'     => esc_html__( 'Dots Style', 'urus' ),
	                'param_name'  => 'owl_dots_style',
	                'group'       => esc_html__( 'Carousel Settings', 'urus' ),
	                'dependency'  => array(
		                'element' => 'owl_dots', 'value' => array( 'true' ),
	                ),
                ),
                'owl_loop'             => array(
                    'type'        => 'dropdown',
                    'value'       => array(
                        esc_html__( 'Yes', 'urus' ) => 'true',
                        esc_html__( 'No', 'urus' )  => 'false',
                    ),
                    'std'         => 'false',
                    'heading'     => esc_html__( 'Loop', 'urus' ),
                    'param_name'  => 'owl_loop',
                    'description' => esc_html__( 'Inifnity loop. Duplicate last and first items to get loop illusion.', 'urus' ),
                    'group'       => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_slidespeed'       => array(
                    'type'        => 'number',
                    'heading'     => esc_html__( 'Slide Speed', 'urus' ),
                    'param_name'  => 'owl_slidespeed',
                    'value'       => '300',
                    'min'         => 0,
                    'suffix'      => esc_html__( 'milliseconds', 'urus' ),
                    'description' => esc_html__( 'Slide speed in milliseconds', 'urus' ),
                    'group'       => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_slide_margin'     => array(
                    'type'        => 'number',
                    'heading'     => esc_html__( 'Margin', 'urus' ),
                    'param_name'  => 'owl_slide_margin',
                    'value'       => '30',
                    'min'         => 0,
                    'suffix'      => esc_html__( 'Pixel', 'urus' ),
                    'description' => esc_html__( 'Distance( or space) between 2 item', 'urus' ),
                    'group'       => esc_html__( 'Carousel Settings', 'urus' ),
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'owl_ls_items'         => array(
                    'type'       => 'number',
                    'heading'    => esc_html__( 'The items on desktop (Screen resolution of device >= 1500px )', 'urus' ),
                    'param_name' => 'owl_ls_items',
                    'value'      => '4',
                    'suffix'     => esc_html__( 'item(s)', 'urus' ),
                    'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                    'min'        => 1,
                    'dependency' => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
            );
            $data_responsive = Urus_Pluggable_Visual_Composer::data_responsive_carousel();
            if ( !empty( $data_responsive ) ) {
                arsort( $data_responsive );
                foreach ( $data_responsive as $key => $item ) {
                    if ( $item['screen'] == 1500 ) {
                        $std = '4';
                    } elseif ( $item['screen'] == 1200 ) {
                        $std = '3';
                    } elseif ( $item['screen'] == 992 || $item['screen'] == 768 ) {
                        $std = '2';
                    } elseif ( $item['screen'] == 480 ) {
                        $std = '1';
                    }
                    $data_carousel["owl_{$item['name']}"] = array(
                        'type'       => 'number',
                        'heading'    => $item['title'],
                        'param_name' => "owl_{$item['name']}",
                        'value'      => isset( $std ) ? $std : '',
                        'suffix'     => esc_html__( 'item(s)', 'urus' ),
                        'group'      => esc_html__( 'Carousel Settings', 'urus' ),
                        'min'        => 1,
                        'dependency' => array(
                            'element' => $dependency, 'value' => array( $value_dependency ),
                        ),
                    );
                }
            }
            $data_carousel = apply_filters( 'vc_options_carousel', $data_carousel, $dependency, $value_dependency );
            if ( $dependency == null && $value_dependency == null ) {
                $match = array(
                    'owl_navigation_style',
                    'owl_autoplayspeed',
                    'owl_rows_space',
                    'owl_verticalswiping',
                    'owl_center_padding',
                );
                foreach ( $data_carousel as $value ) {
                    if ( !in_array( $value['param_name'], $match ) ) {
                        unset( $value['dependency'] );
                    }
                    $data_value[] = $value;
                }
            } else {
                foreach ( $data_carousel as $value ) {
                    $data_value[] = $value;
                }
            }
            return $data_value;
        }
        public static function vc_bootstrap( $dependency = null, $value_dependency = null )
        {
            $data_value     = array();
            $data_bootstrap = array(
                'boostrap_rows_space' => array(
                    'type'       => 'dropdown',
                    'heading'    => esc_html__( 'Rows space', 'urus' ),
                    'param_name' => 'boostrap_rows_space',
                    'value'      => array(
                        esc_html__( 'Default', 'urus' ) => 'rows-space-0',
                        esc_html__( '10px', 'urus' )    => 'rows-space-10',
                        esc_html__( '20px', 'urus' )    => 'rows-space-20',
                        esc_html__( '30px', 'urus' )    => 'rows-space-30',
                        esc_html__( '40px', 'urus' )    => 'rows-space-40',
                        esc_html__( '50px', 'urus' )    => 'rows-space-50',
                        esc_html__( '60px', 'urus' )    => 'rows-space-60',
                        esc_html__( '70px', 'urus' )    => 'rows-space-70',
                        esc_html__( '80px', 'urus' )    => 'rows-space-80',
                        esc_html__( '90px', 'urus' )    => 'rows-space-90',
                        esc_html__( '100px', 'urus' )   => 'rows-space-100',
                    ),
                    'std'        => 'rows-space-0',
                    'group'      => esc_html__( 'Boostrap settings', 'urus' ),
                    'dependency' => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'boostrap_bg_items'   => array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Items per row on Desktop', 'urus' ),
                    'param_name'  => 'boostrap_bg_items',
                    'value'       => array(
                        esc_html__( '1 item', 'urus' )  => '12',
                        esc_html__( '2 items', 'urus' ) => '6',
                        esc_html__( '3 items', 'urus' ) => '4',
                        esc_html__( '4 items', 'urus' ) => '3',
                        esc_html__( '5 items', 'urus' ) => '15',
                        esc_html__( '6 items', 'urus' ) => '2',
                    ),
                    'description' => esc_html__( '(Item per row on screen resolution of device >= 1500px )', 'urus' ),
                    'group'       => esc_html__( 'Boostrap settings', 'urus' ),
                    'std'         => '12',
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'boostrap_lg_items'   => array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Items per row on Desktop', 'urus' ),
                    'param_name'  => 'boostrap_lg_items',
                    'value'       => array(
                        esc_html__( '1 item', 'urus' )  => '12',
                        esc_html__( '2 items', 'urus' ) => '6',
                        esc_html__( '3 items', 'urus' ) => '4',
                        esc_html__( '4 items', 'urus' ) => '3',
                        esc_html__( '5 items', 'urus' ) => '15',
                        esc_html__( '6 items', 'urus' ) => '2',
                    ),
                    'description' => esc_html__( '(Item per row on screen resolution of device >= 1200px and < 1500px )', 'urus' ),
                    'group'       => esc_html__( 'Boostrap settings', 'urus' ),
                    'std'         => '12',
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'boostrap_md_items'   => array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Items per row on landscape tablet', 'urus' ),
                    'param_name'  => 'boostrap_md_items',
                    'value'       => array(
                        esc_html__( '1 item', 'urus' )  => '12',
                        esc_html__( '2 items', 'urus' ) => '6',
                        esc_html__( '3 items', 'urus' ) => '4',
                        esc_html__( '4 items', 'urus' ) => '3',
                        esc_html__( '5 items', 'urus' ) => '15',
                        esc_html__( '6 items', 'urus' ) => '2',
                    ),
                    'description' => esc_html__( '(Item per row on screen resolution of device >=992px and < 1200px )', 'urus' ),
                    'group'       => esc_html__( 'Boostrap settings', 'urus' ),
                    'std'         => '12',
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'boostrap_sm_items'   => array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Items per row on portrait tablet', 'urus' ),
                    'param_name'  => 'boostrap_sm_items',
                    'value'       => array(
                        esc_html__( '1 item', 'urus' )  => '12',
                        esc_html__( '2 items', 'urus' ) => '6',
                        esc_html__( '3 items', 'urus' ) => '4',
                        esc_html__( '4 items', 'urus' ) => '3',
                        esc_html__( '5 items', 'urus' ) => '15',
                        esc_html__( '6 items', 'urus' ) => '2',
                    ),
                    'description' => esc_html__( '(Item per row on screen resolution of device >=768px and < 992px )', 'urus' ),
                    'group'       => esc_html__( 'Boostrap settings', 'urus' ),
                    'std'         => '12',
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'boostrap_xs_items'   => array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Items per row on Mobile', 'urus' ),
                    'param_name'  => 'boostrap_xs_items',
                    'value'       => array(
                        esc_html__( '1 item', 'urus' )  => '12',
                        esc_html__( '2 items', 'urus' ) => '6',
                        esc_html__( '3 items', 'urus' ) => '4',
                        esc_html__( '4 items', 'urus' ) => '3',
                        esc_html__( '5 items', 'urus' ) => '15',
                        esc_html__( '6 items', 'urus' ) => '2',
                    ),
                    'description' => esc_html__( '(Item per row on screen resolution of device >=480  add < 768px )', 'urus' ),
                    'group'       => esc_html__( 'Boostrap settings', 'urus' ),
                    'std'         => '12',
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
                'boostrap_ts_items'   => array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Items per row on Mobile', 'urus' ),
                    'param_name'  => 'boostrap_ts_items',
                    'value'       => array(
                        esc_html__( '1 item', 'urus' )  => '12',
                        esc_html__( '2 items', 'urus' ) => '6',
                        esc_html__( '3 items', 'urus' ) => '4',
                        esc_html__( '4 items', 'urus' ) => '3',
                        esc_html__( '5 items', 'urus' ) => '15',
                        esc_html__( '6 items', 'urus' ) => '2',
                    ),
                    'description' => esc_html__( '(Item per row on screen resolution of device < 480px)', 'urus' ),
                    'group'       => esc_html__( 'Boostrap settings', 'urus' ),
                    'std'         => '12',
                    'dependency'  => array(
                        'element' => $dependency, 'value' => array( $value_dependency ),
                    ),
                ),
            );
            $data_bootstrap = apply_filters( 'vc_options_bootstrap', $data_bootstrap, $dependency, $value_dependency );
            if ( $dependency == null && $value_dependency == null ) {
                foreach ( $data_bootstrap as $value ) {
                    unset( $value['dependency'] );
                    $data_value[] = $value;
                }
            } else {
                foreach ( $data_bootstrap as $value ) {
                    $data_value[] = $value;
                }
            }
            return $data_value;
        }
        public static function data_responsive_carousel()
        {
            $responsive = array(
                'desktop'          => array(
                    'screen'   => 1500,
                    'name'     => 'lg_items',
                    'title'    => esc_html__( 'The items on desktop (Screen resolution of device >= 1200px and < 1500px )', 'urus' ),
                    'settings' => array(),
                ),
                'laptop'           => array(
                    'screen'   => 1200,
                    'name'     => 'md_items',
                    'title'    => esc_html__( 'The items on desktop (Screen resolution of device >= 992px < 1200px )', 'urus' ),
                    'settings' => array(),
                ),
                'tablet'           => array(
                    'screen'   => 992,
                    'name'     => 'sm_items',
                    'title'    => esc_html__( 'The items on tablet (Screen resolution of device >=768px and < 992px )', 'urus' ),
                    'settings' => array(),
                ),
                'mobile_landscape' => array(
                    'screen'   => 768,
                    'name'     => 'xs_items',
                    'title'    => esc_html__( 'The items on mobile landscape(Screen resolution of device >=480px and < 768px)', 'urus' ),
                    'settings' => array(),
                ),
                'mobile'           => array(
                    'screen'   => 480,
                    'name'     => 'ts_items',
                    'title'    => esc_html__( 'The items on mobile (Screen resolution of device < 480px)', 'urus' ),
                    'settings' => array(),
                ),
            );
            return apply_filters( 'urus_carousel_responsive_screen', $responsive );
        }
        public static function vc_iconpicker_type_fontawesome( $fonts){
            $fonts['Extend Icons'] = array(
                array( 'urus-icon  urus-icon-prev' => 'Click' ),
                array( 'urus-icon urus-icon-next' => 'Click' ),
                array( 'urus-icon urus-icon-down' => 'Click' ),
                array( 'urus-icon urus-icon-up' => 'Click' ),
                array( 'urus-icon  urus-icon-prev-1' => 'Click' ),
                array( 'urus-icon urus-icon-next-1' => 'Click' ),
                array( 'urus-icon urus-icon-search' => 'Click' ),
                array( 'urus-icon urus-icon-heart' => 'Click' ),
                array( 'urus-icon urus-icon-envelope-1' => 'Click' ),
                array( 'urus-icon  urus-icon-box' => 'Click' ),
                array( 'urus-icon urus-icon-return-1' => 'Click' ),
                array( 'urus-icon urus-icon-shield-1' => 'Click' ),
                array( 'urus-icon  urus-icon-instagam' => 'Click' ),
                array( 'urus-icon urus-icon-close' => 'Click' ),
                array( 'urus-icon urus-icon-plus' => 'Click' ),
                array( 'urus-icon urus-icon-minus' => 'Click' ),
                array( 'urus-icon  urus-icon-play' => 'Click' ),
                array( 'urus-icon urus-icon-360' => 'Click' ),
                array( 'urus-icon urus-icon-ruler' => 'Click' ),
                array( 'urus-icon urus-icon-full-screen' => 'Click' ),
                array( 'urus-icon urus-icon-compare' => 'Click' ),
                array( 'urus-icon urus-icon-check' => 'Click' ),
                array( 'urus-icon  urus-icon-up-4' => 'Click' ),
                array( 'urus-icon urus-icon-down-4' => 'Click' ),
                array( 'urus-icon urus-icon-prev-3' => 'Click' ),
                array( 'urus-icon urus-icon-next-3' => 'Click' ),
                array( 'urus-icon urus-icon-up-3' => 'Click' ),
                array( 'urus-icon urus-icon-down-3' => 'Click' ),
                array( 'urus-icon urus-icon-cart' => 'Click' ),
                array( 'urus-icon urus-icon-envelope' => 'Click' ),
                array( 'urus-icon urus-icon-user' => 'Click' ),
                array( 'urus-icon urus-icon-reload' => 'Click' ),
                array( 'urus-icon urus-icon-grid' => 'Click' ),
	            array( 'urus-icon urus-icon-filter' => 'Click' ),
                array( 'urus-icon urus-icon-list' => 'Click' ),
                array( 'urus-icon urus-icon-customer-1' => 'Click' ),
                array( 'urus-icon urus-icon-lock' => 'Click' ),
                array( 'urus-icon urus-icon-truck' => 'Click' ),
                array( 'urus-icon urus-icon-percentage' => 'Click' ),
                array( 'urus-icon urus-icon-pause' => 'Click' ),
                array( 'urus-icon urus-icon-customer' => 'Click' ),
                array( 'urus-icon urus-icon-shield-1-1' => 'Click' ),
                array( 'urus-icon urus-icon-return' => 'Click' ),
                array( 'urus-icon urus-icon-prev-4' => 'Click' ),
                array( 'urus-icon urus-icon-next-4' => 'Click' ),
                array( 'urus-icon urus-icon-table' => 'Click' ),
                array( 'urus-icon urus-icon-diamon' => 'Click' ),
                array( 'urus-icon urus-icon-user-1' => 'Click' ),
                array( 'urus-icon urus-icon-down-2' => 'Click' ),
                array( 'urus-icon urus-icon-up-2' => 'Click' ),
                array( 'urus-icon urus-icon-prev-2' => 'Click' ),
                array( 'urus-icon urus-icon-next-2' => 'Click' ),
	            array( 'urus-icon urus-icon-up-1' => 'Click' ),
                array( 'urus-icon urus-icon-down-1' => 'Click' ),
                array( 'urus-icon urus-icon-close-1' => 'Click' ),
                array( 'urus-icon urus-icon-diamon-2' => 'Click' ),
                array( 'urus-icon urus-icon-diamond-1' => 'Click' ),
                array( 'urus-icon urus-icon-gift' => 'Click' ),
                array( 'urus-icon urus-icon-instagram-1' => 'Click' ),
                array( 'urus-icon urus-icon-refund' => 'Click' ),
                array( 'urus-icon urus-icon-shield' => 'Click' ),
                array( 'urus-icon urus-icon-shirt' => 'Click' ),
                array( 'urus-icon urus-icon-truck-1' => 'Click' ),
                array( 'urus-icon urus-icon-up-5' => 'Click' ),
                array( 'urus-icon urus-icon-down-5' => 'Click' ),
                array( 'urus-icon urus-icon-prev-5' => 'Click' ),
                array( 'urus-icon urus-icon-next-5' => 'Click' ),
                array( 'urus-icon urus-icon-air' => 'Click' ),
                array( 'urus-icon urus-icon-clock' => 'Click' ),
                array( 'urus-icon urus-icon-envelope-2' => 'Click' ),
                array( 'urus-icon urus-icon-phone' => 'Click' ),
                array( 'urus-icon urus-icon-point' => 'Click' ),
                array( 'urus-icon urus-icon-truck-2' => 'Click' ),
                array( 'urus-icon urus-icon-garantia' => 'Click' ),
                array( 'urus-icon urus-icon-quote' => 'Click' ),
                array( 'urus-icon urus-icon-filter-1' => 'Click' ),
                array( 'urus-icon urus-icon-exit' => 'Click' ),
                array( 'urus-icon urus-icon-line' => 'Click' ),
            );
            return $fonts;
        }
        public static function get_tabs_shortcode(){
            $response = array(
                'html'    => '',
                'message' => '',
                'success' => 'no',
            );
            check_ajax_referer( 'urus_ajax_frontend', 'security' );
            $section_id = isset( $_POST['section_id'] ) ? $_POST['section_id'] : '';
            $id         = isset( $_POST['id'] ) ? $_POST['id'] : '';
            WPBMap::addAllMappedShortcodes();
            $response['html']    = wpb_js_remove_wpautop( Urus_Pluggable_Visual_Composer::detected_shortcode( $id, $section_id ) );
            $response['success'] = 'ok';
            wp_send_json( $response );
            die();
        }
        public static  function detected_shortcode( $id, $tab_id = null ) {
            $post              = get_post( $id );
            $content           = preg_replace( '/\s+/', ' ', $post->post_content );
            $shortcode_section = '';
            preg_match_all( '/\[vc_tta_section(.*?)vc_tta_section\]/', $content, $matches );
            if ( $matches[0] && is_array( $matches[0] ) && count( $matches[0] ) > 0 ) {
                foreach ( $matches[0] as $key => $value ) {
                    preg_match_all( '/tab_id="([^"]+)"/', $matches[0][$key], $matches_ids );
                    foreach ( $matches_ids[1] as $matches_id ) {
                        if ( $tab_id == $matches_id ) {
                            $shortcode_section = $value;
                        }
                    }
                }
            }
            return $shortcode_section;
        }
        public static function vc_google_fonts_get_fonts_filter( $fonts_list ){
            $poppins = new stdClass();
            $poppins->font_family = 'Poppins';
            $poppins->font_types = '300 light regular:300:normal,400 regular:400:normal,500 bold regular:500:normal,600 bold regular:600:normal,700 bold regular:700:normal';
            $poppins->font_styles = 'regular';
            $poppins->font_family_description = esc_html__( 'Select font family', 'urus' );
            $poppins->font_style_description = esc_html__( 'Select font styling', 'urus' );
            $fonts_list[] = $poppins;

            //
            $Amatic = new stdClass();
            $Amatic->font_family = 'Amatic SC';
            $Amatic->font_types = '400 regular:400:normal,700 bold regular:700:normal';
            $Amatic->font_styles = 'regular';
            $Amatic->font_family_description = esc_html__( 'Select font family', 'urus' );
            $Amatic->font_style_description = esc_html__( 'Select font styling', 'urus' );
            $fonts_list[] = $Amatic;


            $Prata = new stdClass();
            $Prata->font_family = 'Prata';
            $Prata->font_types = '400 regular:400:normal';
            $Prata->font_styles = 'regular';
            $Prata->font_family_description = esc_html__( 'Select font family', 'urus' );
            $Prata->font_style_description = esc_html__( 'Select font styling', 'urus' );
            $fonts_list[] = $Prata;

            return $fonts_list;

        }
        public static function add_custom_template_for_vc(){
            $file_path = URUS_THEME_DIR . '/vc_template.json';
            if( file_exists($file_path)){
                $file_url = URUS_THEME_URI . '/vc_template.json';
                $option_content  = wp_remote_get( $file_url );
                if( $option_content && !is_wp_error($option_content) ){
                    $option_content  = $option_content['body'];
                    $f ='json_'.'decode';
                    $options_configs = $f( $option_content, true );
                    if( !empty($options_configs)){
                        foreach ($options_configs as $config){
                            vc_add_default_templates( $config );
                        }
                    }
                }
            }
        }
    }
}
class WPBakeryShortCode_Urus_Slide extends WPBakeryShortCodesContainer
{
}
class WPBakeryShortCode_Urus_Banner extends WPBakeryShortCodesContainer
{
}
class WPBakeryShortCode_Urus_Container extends WPBakeryShortCodesContainer
{
}
VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Accordion' );
class WPBakeryShortCode_Urus_Tab extends WPBakeryShortCode_VC_Tta_Accordion
{
}
