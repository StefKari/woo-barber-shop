<?php
if( !class_exists('Urus_Popup')){
    class Urus_Popup{
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
            add_action( 'init', array( __CLASS__, 'post_type' ), 999 );
            add_filter( 'body_class', array( __CLASS__, 'body_class' ) );

            add_action( 'wp_ajax_urus_popup_load_content', array( __CLASS__, 'get_content_popup' ) );
            add_action( 'wp_ajax_nopriv_urus_popup_load_content', array( __CLASS__, 'get_content_popup' ) );
            
            add_filter('urus_meta_box_settings',array(__CLASS__,'popup_settings'));
    
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ));

            // State that initialization completed.
            self::$initialized = true;
        }
        
        public static function scripts(){
            wp_enqueue_style( 'urus-popup',  get_theme_file_uri( '/assets/css/popup.css' ), array(), '1.0.0' );
            $delay_time = Urus_Helper::get_option('popup_delay_time',0);
            $current_page_id = Urus_Popup::get_page_current();
            if( is_page()){
                $popup_display_page = get_post_meta( $current_page_id,'_urus_popup_builder_display_page', true );
                // Check settings meta box
                if ( is_numeric( $popup_display_page ) && $popup_display_page > 0 ) {
                    $delay_time = get_post_meta( $current_page_id,'_popup_delay_time', true );
                }
                
            }
            wp_enqueue_script( 'urus-popup', get_theme_file_uri( '/assets/js/popup.js' ), array( 'jquery' ), false, true );
            wp_localize_script( 'urus-popup', 'urus_popup_frontend', array(
                    'ajaxurl'             => admin_url( 'admin-ajax.php' ),
                    'security'            => wp_create_nonce( 'urus_popup_frontend' ),
                    'enable_popup'        => Urus_Helper::get_option('enable_popup',0),
                    'delay_time'          => $delay_time,
                    'enable_popup_mobile' => Urus_Helper::get_option('enable_popup_mobile',0),
                    'pages_display'       => Urus_Helper::get_option('popup_pages_display',array()),
                    'current_page_id'     => $current_page_id,
                )
            );
        }

        public static function body_class( $class ){
            if( self::check_page_enable_popup()){
                $class[] = 'urus-popup-on';
            }
            return $class;
        }

        public static function check_page_enable_popup(){
            if ( isset( $_GET['action'] ) && $_GET['action'] == 'yith-woocompare-view-table' ) {
                return false;
            }
            $enable_popup = Urus_Helper::get_option('enable_popup',0);
            $popup_pages_display = Urus_Helper::get_option('popup_pages_display',array());
            if ( $enable_popup == 1 ) {
                $page_id       = self::get_page_current();
                if ( !empty( $popup_pages_display ) && in_array( $page_id, $popup_pages_display ) ) {
                    return true;
                }
                $popup_display_page = get_post_meta( $page_id,'_urus_popup_builder_display_page', true );
                // Check settings meta box
                if ( is_numeric( $popup_display_page ) && $popup_display_page > 0 ) {
                    return true;
                }
            }

            return false;
        }

        public static function get_page_current() {
            $page_id = 0;
            if ( is_front_page() && is_home() ) {
                // Default homepage
            } elseif ( is_front_page() ) {
                $page_id = get_option( 'page_on_front' );
            } elseif ( is_home() ) {
                $page_id = get_option( 'page_for_posts' );
            } elseif ( is_page() ) {
                $page_id = get_the_ID();
            }
            if ( class_exists( 'WooCommerce' ) ) {
                if ( is_shop() ) {
                    $page_id = get_option( 'woocommerce_shop_page_id' );
                }
            }

            return $page_id;
        }

        public static function get_content_popup(){
            $current_page_id    = $_POST['current_page_id'];
            $popup_id           = Urus_Helper::get_option('popup_used',0);
            $popup_display_page = get_post_meta( $current_page_id,'_urus_popup_builder_display_page', true );
            
            // Check settings meta box
            if ( is_numeric( $popup_display_page ) && $popup_display_page > 0 ) {
                $popup_id = $popup_display_page;
            }
            if ( class_exists( 'Vc_Manager' ) ) {
                WPBMap::addAllMappedShortcodes();
            }
            $popup_effect = get_post_meta( $popup_id, '_urus_popup_builder_effect', true );
            $data         = array(
                'display_effect' => $popup_effect,
                'content'        => '',
            );
            ob_start(); ?>
            <div class="urus-popup">
                <?php
                $query = new WP_Query( array( 'p' => $popup_id, 'post_type' => 'urus-popup', 'posts_per_page' => 1 ) );
                if ( $query->have_posts() ):
                    while ( $query->have_posts() ): $query->the_post(); ?>
                        <?php the_content(); ?>
                    <?php endwhile;
                endif;
                wp_reset_postdata();
                ?>
            </div>
            <?php
            $data['content'] = apply_filters( 'urus_popup_output_content', ob_get_clean() );
            wp_send_json( $data );
            wp_die();
        }

        public static function post_type()  {
            $args = array(
                'labels'              => array(
                    'name'               => esc_html__( 'Popup Builder', 'urus' ),
                    'singular_name'      => esc_html__( 'Popup menu item', 'urus' ),
                    'add_new'            => esc_html__( 'Add new', 'urus' ),
                    'add_new_item'       => esc_html__( 'Add new Popup item', 'urus' ),
                    'edit_item'          => esc_html__( 'Edit Popup item', 'urus' ),
                    'new_item'           => esc_html__( 'New Popup item', 'urus' ),
                    'view_item'          => esc_html__( 'View Popup item', 'urus' ),
                    'search_items'       => esc_html__( 'Search Popup items', 'urus' ),
                    'not_found'          => esc_html__( 'No Popup items found', 'urus' ),
                    'not_found_in_trash' => esc_html__( 'No Popup items found in trash', 'urus' ),
                    'parent_item_colon'  => esc_html__( 'Parent Popup item:', 'urus' ),
                    'menu_name'          => esc_html__( 'Popup Builder', 'urus' ),
                ),
                'hierarchical'        => false,
                'supports'            => array( 'title', 'editor' ),
                'rewrite'             => true,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => 'urus-intro',
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => false,
                'menu_position'       => 14,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'menu_icon'           => 'dashicons-welcome-widgets-menus',
            );
            $f ='register'.'_post_type';
            $f( 'urus-popup', $args );
        }
        public static function popup_settings($meta_boxes){
            $meta_boxes[] = array(
                'id'         => 'urus_popup_option',
                'title'      => esc_html__('Popup Options', 'urus'),
                'post_types' => 'urus-popup',
                'fields'     => array(
                    array(
                        'name'            => esc_html__('Display Effect','urus'),
                        'id'              => '_urus_popup_builder_effect',
                        'type'            => 'select',
                        'options'         => array(
                            'mfp-zoom-in' => esc_html__('Zoom in','urus'),
                            'mfp-newspaper' => esc_html__('Newspaper','urus'),
                            'mfp-move-horizontal' => esc_html__('Move Horizontal','urus'),
                            'mfp-move-from-top' => esc_html__('Move From Top','urus'),
                            'mfp-3d-unfold' => esc_html__('3d Unfold','urus'),
                            'mfp-zoom-out' => esc_html__('Zoom out','urus'),
                        ),
                        'placeholder'     => esc_html__('Select an Item','urus'),
                        'std' => 'zoom-in'
                    ),
                )
            );
            $enable_popup = Urus_Helper::get_option('enable_popup',0);
            if( $enable_popup ){
                $meta_boxes[] = array(
                    'id'         => 'urus_popup_page_option',
                    'title'      => esc_html__('Popup Options', 'urus'),
                    'post_types' => 'page',
                    'fields'     => array(
                        array(
                            'name'        => esc_html__('Popup Display','urus'),
                            'id'          => '_urus_popup_builder_display_page',
                            'type'        => 'post',
                            'post_type'   => 'urus-popup',
                            // Field type.
                            'field_type'  => 'select_advanced',
                            'placeholder' => esc_html__('Select a Popup','urus'),
                            'query_args'  => array(
                                'post_status'    => 'publish',
                                'posts_per_page' => - 1,
                            ),
                        ),
                        array(
                            'name' => esc_html__( 'Delay Time ( millisecond )', 'urus' ),
                            'id'   => '_popup_delay_time',
                            'type' => 'text',
                        ),
        
                    )
                );
            }
            
            return $meta_boxes;
        }
    }
}