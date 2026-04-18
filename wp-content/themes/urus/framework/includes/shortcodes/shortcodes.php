<?php
if( !class_exists('Urus_Shortcodes')){
    class Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = '';
        /**
         * Register shortcode with WordPress.
         *
         * @return  void
         */
        /**
         * Meta key.
         *
         * @var  string
         */
        protected $css_key = '_urus_shortcode_custom_css';

        public function __construct(){
            if ( !empty( $this->shortcode ) ) {
                $func = 'add_'.'shortcode';
                $func( "urus_{$this->shortcode}", array( $this, 'output_html' ) );
            }
            //add_action( 'save_post', array( $this, 'update_post' ) );
        }
        /**
         * Replace and save custom css to post meta.
         *
         * @param   int $post_id
         *
         * @return  void
         */
        public function update_post( $post_id )
        {
            if ( !isset( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
                return;
            }
            // Set and replace content.
            $post = $this->replace_post( $post_id );
            if ( $post ) {
                // Generate custom CSS.
                $css = $this->shortcodes_custom_css( $post->post_content );
                // Update post and save CSS to post meta.
                $this->save_post( $post );
                $this->save_css_postmeta( $post_id, $css );
            } else {
                $this->save_css_postmeta( $post_id, '' );
            }
        }

        /**
         * Replace shortcode used in a post with real content.
         *
         * @param   int $post_id Post ID.
         *
         * @return  WP_Post object or null.
         */
        public function replace_post( $post_id )
        {
            // Get post.
            $post = get_post( $post_id );
            if ( $post ) {
                $post->post_content = preg_replace_callback(
                    '/(urus_custom_id)="[^"]+"/',
                    array( $this, 'shortcode_replace_post_callback' ),
                    $post->post_content
                );
            }

            return $post;
        }

        function shortcode_replace_post_callback( $matches )
        {
            // Generate a random string to use as element ID.
            $id = 'urus_custom_' . uniqid();

            return $matches[1] . '="' . $id . '"';
        }

        /**
         * Parse shortcode custom css string.
         *
         * @param   string $content
         * @return  string
         */
        public function shortcodes_custom_css( $content )
        {
            $css = '';
            WPBMap::addAllMappedShortcodes();
            if ( preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes ) ) {
                foreach ( $shortcodes[2] as $index => $tag ) {
                    $atts      = shortcode_parse_atts( trim( $shortcodes[3][$index] ) );
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
                    $css .= self::shortcodes_custom_css( $shortcode_content );
                }
            }

            return $css;
        }
        /**
         * Generate custom CSS.
         *
         * @param   array $atts Shortcode parameters.
         *
         * @return  string
         */
        public static  function add_css_generate( $atts ){
            return '';
        }
        /**
         * Update post data content.
         *
         * @param   array $post WP_Post object.
         *
         * @return  void
         */
        public function save_post( $post )
        {
            // Sanitize post data for inserting into database.
            $data = sanitize_post( $post, 'db' );
            // Update post content.
            global $wpdb;
            $wpdb->query( "UPDATE {$wpdb->posts} SET post_content = '" . esc_sql( $data->post_content ) . "' WHERE ID = {$data->ID};" );
            // Update post cache.
            $data = sanitize_post( $post, 'raw' );
            wp_cache_replace( $data->ID, $data, 'posts' );
        }

        /**
         * Update extra post meta.
         *
         * @param   int $post_id Post ID.
         * @param   string $css Custom CSS.
         *
         * @return  void
         */
        public function save_css_postmeta( $post_id, $css ) {
            if ( $post_id && $this->css_key ) {
                if ( empty( $css ) ) {
                    delete_post_meta( $post_id, $this->css_key );
                } else {
                    update_post_meta( $post_id, $this->css_key, preg_replace( '/[\t\r\n]/', '', $css ) );
                }
            }
        }
        public function add_css_editor( $atts, $tag ){
            return '';
        }

        public function output_html( $atts, $content = null ){
            return '';
        }

        function get_all_attributes( $tag, $text ){
            preg_match_all( '/' . get_shortcode_regex() . '/s', $text, $matches );
            $out               = array();
            $shortcode_content = array();
            if ( isset( $matches[5] ) ) {
                $shortcode_content = $matches[5];
            }
            if ( isset( $matches[2] ) ) {
                $i = 0;
                foreach ( (array)$matches[2] as $key => $value ) {
                    if ( $tag === $value ) {
                        $out[$i]            = shortcode_parse_atts( $matches[3][$key] );
                        $out[$i]['content'] = $matches[5][$key];
                    }
                    $i++;
                }
            }

            return $out;
        }

       /**
         * @param $css_animation
         *
         * @return string
         */
        public static function getCSSAnimation( $css_animation ) {
            $output = '';
            if ( '' !== $css_animation && 'none' !== $css_animation ) {
                wp_enqueue_script( 'waypoints' );
                wp_enqueue_style( 'animate-css' );
                $output = ' wpb_animate_when_almost_visible wpb_' . $css_animation . ' ' . $css_animation;
            }

            return $output;
        }
        function constructIcon( $section ) {
            vc_icon_element_fonts_enqueue( $section['i_type'] );
            $class = 'vc_tta-icon';
            if ( isset( $section['i_icon_' . $section['i_type']] ) ) {
                $class .= ' ' . $section['i_icon_' . $section['i_type']];
            } else {
                $class .= ' fa fa-adjust';
            }

            return '<i class="' . $class . '"></i>';
        }
    }
}
