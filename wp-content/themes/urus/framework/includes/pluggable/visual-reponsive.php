<?php
if( !class_exists('Urus_Pluggable_Visual_Reponsive')){
    class  Urus_Pluggable_Visual_Reponsive{
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
        /**
         * Meta key.
         *
         * @var  string
         */
        public static $css_key = '_urus_vc_shortcode_custom_css';
        public static $shortcode = '';

        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            /* CUSTOM CSS EDITOR */
            $f ='vc_add_'.'shortcode_param';
            add_action( 'vc_after_mapping', array( __CLASS__, 'vc_add_param_all_shortcode' ) );
            $f( 'html_markup', array( __CLASS__, 'html_markup_field' ) );
            add_action( 'save_post', array( __CLASS__, 'update_post' ),20 );

            add_filter( 'vc_shortcodes_css_class', array( __CLASS__, 'vc_change_element_class_name' ), 10, 3 );

            // State that initialization completed.
            self::$initialized = true;
        }
        public static function get_shortcodes_custom_css(  ) {
            $css ='';
            $id_page    = '';
            $inline_css = array();
            if ( is_front_page() || is_home() ) {
                $id_page = get_queried_object_id();
            } else if ( is_singular() ) {
                if ( !$id_page ) {
                    $id_page = get_the_ID();
                }
            } elseif ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
                $id_page = get_option( 'woocommerce_shop_page_id' );
            }
            if ( $id_page != '' ) {
                $inline_css[] = get_post_meta( $id_page, self::$css_key, true );
                if ( !empty( $inline_css ) ) {
                    $css .= implode( ' ', $inline_css );
                }
            }

            return $css;
        }

        public static function update_post( $post_id ){
            if ( !wp_is_post_revision( $post_id ) ) {
                // Set and replace content.
                $post = Urus_Pluggable_Visual_Reponsive::replace_post( $post_id );
                if ( $post ) {
                    // Generate custom CSS.
                    $css = Urus_Pluggable_Visual_Reponsive::buildShortcodesCustomCss( $post->post_content );
                    // Update post to post meta.
                    Urus_Pluggable_Visual_Reponsive::save_post( $post );
                    // Update save CSS to post meta.
                    Urus_Pluggable_Visual_Reponsive::save_css_postmeta( $post_id, $css );
                    do_action( 'urus_vc_save_post', $post_id );
                } else {
                    Urus_Pluggable_Visual_Reponsive::save_css_postmeta( $post_id, '' );
                }
            }
        }

        public static function replace_post( $post_id){
            // Get post.
            $post = get_post( $post_id );
            if ( $post ) {
                $post->post_content = preg_replace_callback(
                    '/(urus_vc_custom_id)="[^"]+"/',
                    array( __CLASS__, 'shortcode_replace_post_callback' ),
                    $post->post_content
                );
            }

            return $post;
        }
        public static function save_post( $post ){
            // Sanitize post data for inserting into database.
            if (is_object( $post ) ){
                $post->post_content = self::urus_stripslashes($post->post_content);
                $post_content =  $post->post_content;
            } elseif (is_array($post)){
                $post['post_content'] = self::urus_stripslashes($post['post_content']);
                $post_content =  $post['post_content'];
            }
            //$data = sanitize_post( $post, 'db' );
            $data = sanitize_post( $post, 'raw' );
            // Update post content.
            global $wpdb;
            $wpdb->query( "UPDATE {$wpdb->posts} SET post_content = '" . esc_sql($post_content) . "' WHERE ID = {$data->ID};" );
            // Update post cache.
            wp_cache_replace( $data->ID, $data, 'posts' );
        }
        public static function urus_stripslashes($html) {
            $html = htmlspecialchars_decode($html);
            $html = str_replace(array('"\"', '\""'), '"', $html);
            $html = stripslashes($html);
            return $html;
        }
        public static function shortcode_replace_post_callback($matches){
            // Generate a random string to use as element ID.
            $id = 'urus_vc_custom_' . uniqid();
            return $matches[1] . '="' . $id . '"';
        }
        public static function save_css_postmeta( $post_id, $css){
            if ( $post_id && self::$css_key ) {
                if ( !$css ) {
                    delete_post_meta( $post_id, self::$css_key );
                } else {
                    update_post_meta( $post_id, self::$css_key, preg_replace( '/[\t\r\n]/', '', $css ) );
                }
            }
        }

        public static function buildShortcodesCustomCss( $content){
            $css = '';
            WPBMap::addAllMappedShortcodes();
            if ( preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes ) ) {
                foreach ( $shortcodes[2] as $index => $tag ) {
                    $atts  = shortcode_parse_atts( trim( $shortcodes[3][$index] ) );
                    $shortcode = explode( '_', $tag );
                    $shortcode = end( $shortcode );
                    if ( strpos( $tag, 'urus_' ) !== false ) {
                        $class = 'Urus_Shortcodes_' . implode( '_', array_map( 'ucfirst', explode( '-', $shortcode ) ) );
                        if ( class_exists( $class ) ) {
                            $css .= $class::add_css_generate( $atts );
                        }
                    }
                    $css .= self::add_css_editor( $atts, $tag );
                }
                foreach ( $shortcodes[5] as $shortcode_content ) {
                    $css .= self::buildShortcodesCustomCss( $shortcode_content );
                }
            }

            return $css;
        }
        public static function add_css_editor($atts, $tag){
            $css          = '';
            $main_css     = '';
            $inner_css    = '';
            $target_class = '';
            if ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) {
                $inner_css = ' > .vc_column-inner ';
            }
            $editor_names = Urus_Pluggable_Visual_Reponsive::responsive_screens();
            /* generate main css */
            if ( isset( $atts['css'] ) && $atts['css'] != '' )
                $main_css = str_replace( "{", "{$inner_css}{", $atts['css'] );
            
            if ( !empty( $editor_names ) && isset( $atts['urus_vc_custom_id'] ) ) {
                arsort( $editor_names );
                $shortcode_id = '.' . $atts['urus_vc_custom_id'];
                foreach ( $editor_names as $key => $data ) {
                    $generate_css = '';
                    if ( $key == 'desktop' ) {
                        $main_css   = '';
                        $param_name = "css";
                    } else {
                        $param_name = "css_{$key}";
                    }
                    if ( isset( $atts["width_unit_{$key}"] ) ) {
                        ${'unit_css_'.$key} = $atts["width_unit_{$key}"] != 'none' ? $atts["width_unit_{$key}"] : '';
                    } else {
                        ${'unit_css_'.$key} = '%';
                    }
                    /* TARGET CHILD */
                    if ( isset( $atts["target_main_{$key}"] ) && $atts["target_main_{$key}"] != '' ) {
                        $target_main  = trim( strip_tags( $atts["target_main_{$key}"] ) );
                        $inner_css    .= " {$target_main} ";
                        $target_class .= " {$target_main} ";
                    }
                    /* SCREEN CSS */
                    if ( isset( $atts[$param_name] ) && $atts[$param_name] != '' )
                        $generate_css .= str_replace( "{", "{$inner_css}{", $atts[$param_name] );
                    /* FONT CSS */
                    if ( isset( $atts["responsive_font_{$key}"] ) && self::generate_style_font( $atts["responsive_font_{$key}"] ) != '' )
                        $generate_css .= "{$shortcode_id}{$inner_css}, {$shortcode_id}{$inner_css} .title{".self::generate_style_font( $atts["responsive_font_{$key}"] )."}";
                    /* STYLE WIDTH CSS */
                    if ( isset( $atts["width_rows_{$key}"] ) && $atts["width_rows_{$key}"] != '' ) {
                        $generate_css .= "{$shortcode_id}{$target_class}{width: {$atts["width_rows_{$key}"]}{${'unit_css_'.$key}} !important}";
                    }
                    /* DISABLE BACKGROUND CSS */
                    if ( isset( $atts["disable_bg_{$key}"] ) && $atts["disable_bg_{$key}"] == 'yes' )
                        $generate_css .= "{$shortcode_id}{$inner_css}{background-image: none !important;}";
                    /* DISABLE ELEMENT CSS */
                    if ( isset( $atts["disable_element_{$key}"] ) && $atts["disable_element_{$key}"] == 'yes' )
                        $generate_css .= "{$shortcode_id}{$inner_css}{display: none !important;}";
                    /* LETTER SPACING CSS */
                    if ( isset( $atts["letter_spacing_{$key}"] ) && $atts["letter_spacing_{$key}"] != '' ){
                        if( is_numeric($atts["letter_spacing_{$key}"])){
                            $generate_css .= "{$shortcode_id}{$inner_css}{letter-spacing: {$atts["letter_spacing_{$key}"]}px !important;}";
                        }else{
                            $generate_css .= "{$shortcode_id}{$inner_css}{letter-spacing: {$atts["letter_spacing_{$key}"]} !important;}";
                        }
                        
                    }
                    
                    
                    /* GOOGLE FONT */
                    $google_fonts_data = array();
                    if ( isset( $atts["google_fonts_{$key}"] ) )
                        $google_fonts_data = self::get_google_font_data( $tag, $atts, "google_fonts_{$key}" );
                    if ( ( !isset( $atts["use_theme_fonts_{$key}"] ) || 'yes' !== $atts["use_theme_fonts_{$key}"] ) && !empty( $google_fonts_data ) && isset( $google_fonts_data['values'], $google_fonts_data['values']['font_family'], $google_fonts_data['values']['font_style'] ) ) {
                        $google_fonts_family = explode( ':', $google_fonts_data['values']['font_family'] );
                        $styles              = array();
                        $styles[]            = 'font-family:' . $google_fonts_family[0];
                        $google_fonts_styles = explode( ':', $google_fonts_data['values']['font_style'] );
                        $styles[]            = 'font-weight:' . $google_fonts_styles[1];
                        $styles[]            = 'font-style:' . $google_fonts_styles[2];
                        if ( !empty( $styles ) ) {
                            $generate_css .= "{$shortcode_id}{$inner_css}{" . implode( ';', $styles ) . "}";
                        }
                    }
                    /* TARGET CHILD */
                    if ( isset( $atts["target_child_{$key}"] ) && $atts["target_child_{$key}"] != '' ) {
                        $target_child = trim( strip_tags( $atts["target_child_{$key}"] ) );
                        $target_class = " {$target_child} ";
                    }
                    /* CUSTOM CSS */
                    if ( isset( $atts["custom_css_{$key}"] ) ) {
                        $custom_css   = trim( strip_tags( $atts["custom_css_{$key}"] ) );
                        $generate_css .= "{$shortcode_id}{$target_class}{{$custom_css}}";
                    }
                    /* GERENERATE MEDIA */
                    if ( $generate_css != '' ) {
                        if ( $data['screen'] < 999999 ) {
                            $css .= "@media ({$data['media']}: {$data['screen']}px){{$generate_css}}";
                        } else {
                            $css .= $generate_css;
                        }
                    }
                }
            }
            $css .= $main_css;

            return $css;
        }
        public static function generate_style_font(  $container_data  ){
            $style_font_data     = array();
            $styles              = array();
            $font_container_data = explode( '|', $container_data );
            foreach ( $font_container_data as $value ) {
                if ( $value != '' ) {
                    $data_style                      = explode( ':', $value );
                    $style_font_data[$data_style[0]] = $data_style[1];
                }
            }
            foreach ( $style_font_data as $key => $value ) {
                if ( 'tag' !== $key && strlen( $value ) ) {
                    if ( preg_match( '/description/', $key ) ) {
                        continue;
                    }
                    if ( 'font_size' === $key || 'line_height' === $key ) {
                        $value = preg_replace( '/\s+/', '', $value );
                    }
                    if ( 'font_size' === $key ) {
                        $pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
                        // allowed metrics: http://www.w3schools.com/cssref/css_units.asp
                        $regexr = preg_match( $pattern, $value, $matches );
                        $value  = isset( $matches[1] ) ? (float)$matches[1] : (float)$value;
                        $unit   = isset( $matches[2] ) ? $matches[2] : 'px';
                        $value  = $value . $unit;
                    }
                    if ( strlen( $value ) > 0 ) {
                        $styles[] = str_replace( '_', '-', $key ) . ': ' . urldecode( $value );
                    }
                }
            }

            return !empty( $styles ) ? implode( ' !important;', $styles ) . ' !important;' : '';

        }

        public static function get_google_font_data(  $tag,$atts, $key = 'google_fonts'){
            extract( $atts );
            $google_fonts_field          = WPBMap::getParam( $tag, $key );
            $google_fonts_obj            = new Vc_Google_Fonts();
            $google_fonts_field_settings = isset( $google_fonts_field['settings'], $google_fonts_field['settings']['fields'] ) ? $google_fonts_field['settings']['fields'] : array();
            $google_fonts_data           = strlen( $atts[$key] ) > 0 ? $google_fonts_obj->_vc_google_fonts_parse_attributes( $google_fonts_field_settings, $atts[$key] ) : '';

            return $google_fonts_data;
        }
        public static function vc_add_param_all_shortcode(){
            global $shortcode_tags;
            $editor_names = Urus_Pluggable_Visual_Reponsive::responsive_screens();
            WPBMap::addAllMappedShortcodes();
            if ( count( $shortcode_tags ) > 0 && !empty( $editor_names ) ) {
                foreach ( $shortcode_tags as $tag => $function ) {
                    if ( strpos( $tag, 'vc_wp' ) === false && $tag != 'vc_btn' && $tag != 'vc_tta_section' && $tag != 'vc_icon' ) {
                        /* UPDATE POST META */
                        vc_remove_param( $tag, 'css' );
                        add_filter( 'vc_base_build_shortcodes_custom_css', '__return_empty_string' );
                        add_filter( 'vc_font_container_output_data', array( __CLASS__, 'change_font_container_output_data' ), 10, 4 );

                        /* MARKUP HTML TAB */
                        $html_tab = '<div class="plugin-tabs-css">';
                        foreach ( $editor_names as $key => $data ) {
                            $name     = ucfirst( $data['name'] );
                            $active   = ( $key == 'desktop' ) ? ' active' : '';
                            $html_tab .= "<span class='tab-item {$key}{$active}' data-tabs='{$key}'>{$name}</span>";
                        }
                        $html_tab .= '</div>';
                        /* MARKUP HTML TITLE */
                        $html_title = '<div class="tabs-title">';
                        $html_title .= "<h3 class='title'>" . esc_html__( 'Advanced Options', 'urus' ) . "</h3>";
                        $html_title .= '</div>';

                        $attributes = array(
                            array(
                                'type'        => 'textfield',
                                'heading'     => esc_html__( 'Extra class name', 'urus' ),
                                'param_name'  => 'el_class',
                                'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'urus' ),
                            ),
                            array(
                                'param_name' => 'hidden_markup_01',
                                'type'       => 'html_markup',
                                'markup'     => $html_tab,
                                'group'      => esc_html__( 'Design Options', 'urus' ),
                            ),
                            array(
                                'param_name'       => 'urus_vc_custom_id',
                                'heading'          => esc_html__( 'Hidden ID', 'urus' ),
                                'type'             => 'uniqid',
                                'edit_field_class' => 'hidden',
                            ),
                        );
                        /* CSS EDITOR */
                        if ( !empty( $editor_names ) )
                            foreach ( $editor_names as $key => $data ) {
                                $advanced_editor   = array();
                                $name              = ucfirst( $data['name'] );
                                $hidden            = ( $key != 'desktop' ) ? ' hidden' : '';
                                $param_name        = ( $key == 'desktop' ) ? "css" : "css_{$key}";
                                $screen            = $data['screen'] < 999999 ? " ( {$data['media']}: {$data['screen']}px )" : '';
                                $attributes_editor = array(
                                    /* CSS EDITOR */
                                    array(
                                        'type'             => 'textfield',
                                        'heading'          => esc_html__( 'Target Main', 'urus' ),
                                        'param_name'       => "target_main_{$key}",
                                        'description'      => esc_html__( 'Enter Child Target Element Name for This Screen.', 'urus' ),
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 {$key}{$hidden}",
                                    ),
                                    array(
                                        'type'             => 'css_editor',
                                        'heading'          => 'Screen '.$name.$screen,
                                        'param_name'       => $param_name,
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 {$key}{$hidden}",
                                    ),
                                );
                                $advanced_editor = array(
                                    array(
                                        'param_name'       => "hidden_markup_{$key}",
                                        'type'             => 'html_markup',
                                        'markup'           => $html_title,
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 {$key}{$hidden}",
                                    ),
                                    /* CHECKBOX BACKGROUND */
                                    array(
                                        'type'             => 'checkbox',
                                        'heading'          => esc_html__( 'Disable Background', 'urus' ),
                                        'param_name'       => "disable_bg_{$key}",
                                        'value'            => array(
                                            "<label for='disable_bg_{$key}-yes'></label>" => 'yes',
                                        ),
                                        'edit_field_class' => "urus-vc-checkbox-field vc_col-xs-12 vc_col-sm-6 {$key}{$hidden}",
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                    ),
                                    /* CHECKBOX DISPLAY NONE */
                                    array(
                                        'type'             => 'checkbox',
                                        'heading'          => esc_html__( 'Disable Element', 'urus' ),
                                        'param_name'       => "disable_element_{$key}",
                                        'value'            => array(
                                            "<label for='disable_element_{$key}-yes'></label>" => 'yes',
                                        ),
                                        'edit_field_class' => "urus-vc-checkbox-field vc_col-xs-12 vc_col-sm-6 {$key}{$hidden}",
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                    ),
                                    /* WIDTH CONTAINER */
                                    array(
                                        'type'             => 'textfield',
                                        'heading'          => 'Width on '.$name,
                                        'param_name'       => "width_rows_{$key}",
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 vc_col-sm-4 {$key}{$hidden}",
                                    ),
                                    /* UNIT CSS WIDTH */
                                    array(
                                        'type'             => 'dropdown',
                                        'heading'          => esc_html__( 'Unit', 'urus' ),
                                        'param_name'       => "width_unit_{$key}",
                                        'value'            => array(
                                            esc_html__( 'Percent (%)', 'urus' )     => '%',
                                            esc_html__( 'Pixel (px)', 'urus' )      => 'px',
                                            esc_html__( 'Em (em)', 'urus' )         => 'em',
                                            esc_html__( 'View Width (vw)', 'urus' ) => 'vw',
                                            esc_html__( 'Custom Width', 'urus' )    => 'none',
                                        ),
                                        'std'              => '%',
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 vc_col-sm-4 {$key}{$hidden}",
                                    ),
                                    /* TEXT FONT */
                                    array(
                                        'type'             => 'textfield',
                                        'heading'          => esc_html__( 'Letter Spacing', 'urus' ),
                                        'param_name'       => "letter_spacing_{$key}",
                                        'description'      => esc_html__( 'Enter letter spacing.', 'urus' ),
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 vc_col-sm-4 {$key}{$hidden}",
                                    ),
                                    array(
                                        'type'             => 'font_container',
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'param_name'       => "responsive_font_{$key}",
                                        'edit_field_class' => "vc_col-xs-12 vc_col-sm-8 {$key}{$hidden}",
                                        'settings'         => array(
                                            'fields' => array(
                                                'text_align',
                                                'font_size',
                                                'line_height',
                                                'color',
                                                'text_align_description'  => esc_html__( 'Select text alignment.', 'urus' ),
                                                'font_size_description'   => esc_html__( 'Enter font size.', 'urus' ),
                                                'line_height_description' => esc_html__( 'Enter line height.', 'urus' ),
                                                'color_description'       => esc_html__( 'Select heading color.', 'urus' ),
                                            ),
                                        ),
                                    ),
                                    array(
                                        'type'             => 'checkbox',
                                        'heading'          => esc_html__( 'Use theme default font family?', 'urus' ),
                                        'param_name'       => "use_theme_fonts_{$key}",
                                        'value'            => array(
                                            "<label for='use_theme_fonts_{$key}-yes'></label>" => 'yes',
                                        ),
                                        'std'              => 'yes',
                                        'description'      => esc_html__( 'Use font family from the theme.', 'urus' ),
                                        'edit_field_class' => "urus-vc-checkbox-field vc_col-xs-12 vc_col-sm-4 {$key}{$hidden}",
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                    ),
                                    array(
                                        'type'             => 'google_fonts',
                                        'param_name'       => "google_fonts_{$key}",
                                        'value'            => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                                        'settings'         => array(
                                            'fields' => array(
                                                'font_family_description' => esc_html__( 'Select font family.', 'urus' ),
                                                'font_style_description'  => esc_html__( 'Select font styling.', 'urus' ),
                                            ),
                                        ),
                                        'dependency'       => array(
                                            'element'            => "use_theme_fonts_{$key}",
                                            'value_not_equal_to' => 'yes',
                                        ),
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 vc_col-sm-8 {$key}{$hidden}",
                                    ),
                                    /* CUSTOM CSS */
                                    array(
                                        'type'             => 'textfield',
                                        'heading'          => esc_html__( 'Target Child', 'urus' ),
                                        'param_name'       => "target_child_{$key}",
                                        'description'      => esc_html__( 'Enter Child Target Name.', 'urus' ),
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 {$key}{$hidden}",
                                    ),
                                    array(
                                        'type'             => 'textarea',
                                        'heading'          => esc_html__( 'Custom CSS', 'urus' ),
                                        'param_name'       => "custom_css_{$key}",
                                        'description'      => esc_html__( 'Enter css Properties.', 'urus' ),
                                        'group'            => esc_html__( 'Design Options', 'urus' ),
                                        'edit_field_class' => "vc_col-xs-12 {$key}{$hidden}",
                                    ),
                                );
                                $attributes = array_merge( $attributes, $attributes_editor, $advanced_editor );
                            }
                    } else {
                        $attributes = array(
                            array(
                                'type'        => 'textfield',
                                'heading'     => esc_html__( 'Extra class name', 'urus' ),
                                'param_name'  => 'el_class',
                                'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'urus' ),
                            ),
                            array(
                                'param_name'       => 'urus_vc_custom_id',
                                'heading'          => esc_html__( 'Hidden ID', 'urus' ),
                                'type'             => 'uniqid',
                                'edit_field_class' => 'hidden',
                            ),
                        );
                    }
                    vc_add_params( $tag, $attributes );
                }
            }
        }
        public static function html_markup_field($settings, $value){
            return $settings['markup'];
        }
        
        public static function responsive_screens(){
            $screens = array(
                'desktop' => array(
                    'screen' => 999999,
                    'name'   => 'Desktop',
                    'media'  => 'max-width',
                ),
                'laptop'  => array(
                    'screen' => 1499,
                    'name'   => 'Laptop',
                    'media'  => 'max-width',
                ),
                'tablet'  => array(
                    'screen' => 1199,
                    'name'   => 'Tablet',
                    'media'  => 'max-width',
                ),
                'ipad'    => array(
                    'screen' => 991,
                    'name'   => 'Ipad',
                    'media'  => 'max-width',
                ),
                'mobile'  => array(
                    'screen' => 767,
                    'name'   => 'Mobile',
                    'media'  => 'max-width',
                ),
                'small_mobile'  => array(
                    'screen' => 480,
                    'name'   => 'Small Mobile',
                    'media'  => 'max-width',
                ),
            );
            return apply_filters('urus_visual_composor_responsive_screens',$screens);
        }

        public static function change_font_container_output_data( $data, $fields, $values, $settings ){
            if ( isset( $fields['text_align'] ) ) {
                $data['text_align'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . esc_html__( 'Text align', 'urus' ) . '</div>
                    <div class="vc_font_container_form_field-text_align-container">
                        <select class="vc_font_container_form_field-text_align-select">
                            <option value="" class="" ' . ( '' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . esc_html__( 'None', 'urus' ) . '</option>
                            <option value="left" class="left" ' . ( 'left' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Left', 'urus' ) . '</option>
                            <option value="right" class="right" ' . ( 'right' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Right', 'urus' ) . '</option>
                            <option value="center" class="center" ' . ( 'center' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Center', 'urus' ) . '</option>
                            <option value="justify" class="justify" ' . ( 'justify' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Justify', 'urus' ) . '</option>
                        </select>
                    </div>';
                if ( isset( $fields['text_align_description'] ) && strlen( $fields['text_align_description'] ) > 0 ) {
                    $data['text_align'] .= '
                    <span class="vc_description clear">' . $fields['text_align_description'] . '</span>
                    ';
                }
                $data['text_align'] .= '</div>';
            }

            return $data;
        }
        public static function vc_change_element_class_name( $class_string, $tag, $atts ) {
            $editor_names = Urus_Pluggable_Visual_Reponsive::responsive_screens();
            $atts         = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( $tag, $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $google_fonts_data = array();
            $class_string      = array( $class_string );
            $class_string[]    = isset( $atts['el_class'] ) ? $atts['el_class'] : '';
            $class_string[]    = isset( $atts['urus_vc_custom_id'] ) ? $atts['urus_vc_custom_id'] : '';
            $settings          = get_option( 'wpb_js_google_fonts_subsets' );
            if ( is_array( $settings ) && !empty( $settings ) ) {
                $subsets = '&subset=' . implode( ',', $settings );
            } else {
                $subsets = '';
            }
            if ( strpos( $tag, 'vc_wp' ) === false && $tag != 'vc_btn' && $tag != 'vc_tta_section' && $tag != 'vc_icon' ) {
                $class_string[] = isset( $atts["css"] ) ? vc_shortcode_custom_css_class( $atts["css"], '' ) : '';
            }
            if ( !empty( $editor_names ) )
                foreach ( $editor_names as $key => $data ) {
                    $class_string[] = ( isset( $atts["css_{$key}"] ) && $key != 'desktop' ) ? vc_shortcode_custom_css_class( $atts["css_{$key}"], '' ) : '';
                    /* GOOGLE FONT */
                    if ( isset( $atts["google_fonts_{$key}"] ) )
                        $google_fonts_data = self::get_google_font_data( $tag, $atts, "google_fonts_{$key}" );
                    if ( ( !isset( $atts["use_theme_fonts_{$key}"] ) || 'yes' !== $atts["use_theme_fonts_{$key}"] ) && isset( $google_fonts_data['values']['font_family'] ) ) {
                        wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets );
                    }
                }

            return preg_replace( '/\s+/', ' ', implode( ' ', $class_string ) );
        }
        
    }
}