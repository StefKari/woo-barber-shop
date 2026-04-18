<?php
if(!class_exists('Urus_Footer')){
    class Urus_Footer{
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

            add_action( 'init', array( __CLASS__, '_register_post_type' ), 100, 0 );
            add_action('get_header',array(__CLASS__,'footer_hook_content'));
            add_action( 'wp_enqueue_scripts', array(__CLASS__,'inline_css'),99);

            // State that initialization completed.
            self::$initialized = true;
        }

        public static function footer_hook_content(){
            $enable_boxed = Urus_Helper::get_option('enable_boxed',0);

            if( $enable_boxed == 1){
                add_action('urus_after_site_content',array(__CLASS__,'display_content'));
            }else{
                if (is_page_template('templates/fullscreen.php')){
                    add_action('urus_fullscreen_footer',array(__CLASS__,'display_content'));
                }else{
                    add_action('wp_footer',array(__CLASS__,'display_content'));
                }
            }
            if( did_action( 'elementor/loaded' )){
                self::display_content(false);
                add_action('urus_elementor_widget_footer',array(__CLASS__,'display_content'));
            }

        }

        public static function display_elementor_content(){
            echo self::display_content(false);
        }

        public static function _register_post_type(){
            $args = array(
                'labels'              => array(
                    'name'               => esc_html__( 'Footers', 'urus' ),
                    'singular_name'      => esc_html__( 'Footers', 'urus' ),
                    'add_new'            => esc_html__( 'Add New', 'urus' ),
                    'add_new_item'       => esc_html__( 'Add new footer', 'urus' ),
                    'edit_item'          => esc_html__( 'Edit footer', 'urus' ),
                    'new_item'           => esc_html__( 'New footer', 'urus' ),
                    'view_item'          => esc_html__( 'View footer', 'urus' ),
                    'search_items'       => esc_html__( 'Search template footer', 'urus' ),
                    'not_found'          => esc_html__( 'No template items found', 'urus' ),
                    'not_found_in_trash' => esc_html__( 'No template items found in trash', 'urus' ),
                    'parent_item_colon'  => esc_html__( 'Parent template item:', 'urus' ),
                    'menu_name'          => esc_html__( 'Footer Builder', 'urus' ),
                ),
                'hierarchical'        => false,
                'description'         => esc_html__( 'To Build Template Footer.', 'urus' ),
                'supports'            => array(
                    'title',
                    'editor',
                    'thumbnail',
                    'revisions',
                ),
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu' => 'urus-intro',
                'menu_position'       => 10,
                'show_in_nav_menus'   => false,
                'exclude_from_search' => true,
                'has_archive'         => false,
                'query_var'           => true,
                'can_export'          => true,
                'rewrite'             => false,
                'capability_type'     => 'page',
            );
            $func = 'register_'.'post_type';
            $func( 'urus-footer', $args );
        }

        public static function display_content($print_content = true){
            global $urus_footer_content;
            if ($print_content !== false){
                $print_content = true;
            }
            if (empty($urus_footer_content)){
                ob_start();
                $footer_used = Urus_Helper::get_option('footer_used',0);
                $theme_use_footer_builder = Urus_Helper::get_option('theme_use_footer_builder',0);
                $disable_footer_builder_mobile = Urus_Helper::get_option('disable_footer_builder_mobile',0);
                $footer_copyright = Urus_Helper::get_option('footer_copyright','© 2019 Urus - All Rights Reserved');

                if( $disable_footer_builder_mobile == 1  && Urus_Mobile_Detect::isMobile()){
                    $theme_use_footer_builder = 0;
                }
                if( $theme_use_footer_builder == 0){
                    ?>
                    <footer class="footer no-builder">
                        <div class="container">
                            <div class="copyright"><?php echo Urus_Helper::escaped_html($footer_copyright);?></div>
                        </div>
                    </footer>
                    <?php
                }else{
                    if( !$footer_used || $footer_used <= 0 ) return;
                    $_footer_layout  = Urus_Helper::get_post_meta($footer_used,'_footer_layout','default');
                    $_footer_width  = Urus_Helper::get_post_meta($footer_used,'_footer_width','container-wapper');
                    $footer_class = array('footer');
                    $footer_class[] = $_footer_layout;
                    $footer_container_class = array($_footer_width);
                    $footer_class = apply_filters('urus_footer_class',$footer_class);
                    $footer_container_class = apply_filters('urus_footer_container_class',$footer_container_class);
                    ?>
                    <footer class="<?php echo esc_attr( implode( ' ', $footer_class ) ); ?>">
                        <?php do_action('urus_before_footer_content');?>
                            <div class="<?php echo esc_attr( implode( ' ', $footer_container_class ) ); ?>">
                                <?php
                                if( did_action( 'elementor/loaded' )){
                                    $pluginElementor = \Elementor\Plugin::instance();
                                    $foot_content = $pluginElementor->frontend->get_builder_content($footer_used);
                                } else {
                                    $foot_content = do_shortcode(get_post_field('post_content', $footer_used));
                                }
                                echo e_data($foot_content);
                                ?>
                            </div>
                        <?php do_action('urus_after_footer_content');?>
                    </footer>
                    <?php
                    wp_reset_postdata();
                }
                $urus_footer_content = ob_get_clean();
                $GLOBALS['urus_footer_content'] =  $urus_footer_content;
            }
            if ($print_content)
                echo e_data($urus_footer_content);
            else
                return $urus_footer_content;
        }

        public static function get_custom_css(){
            $footer_used = Urus_Helper::get_option('footer_used',0);
            $vc_css = get_post_meta($footer_used,'_wpb_shortcodes_custom_css',true);
            $vc_css .= get_post_meta($footer_used,'_urus_vc_shortcode_custom_css',true);
            $vc_css .= get_post_meta($footer_used,'_urus_shortcode_custom_css',true);

            return $vc_css;

        }
        public static function inline_css(){
            $css = Urus_Footer::get_custom_css();
            $css = preg_replace( '/\s+/', ' ', $css );
            wp_add_inline_style( 'urus', $css );
        }
    }
}
