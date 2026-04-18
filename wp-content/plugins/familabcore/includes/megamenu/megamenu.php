<?php
if( !class_exists('Familab_Core_Megamenu')){
    class Familab_Core_Megamenu{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected $initialized = false;

        /**
         * Initialize pluggable functions for Visual Composer.
         *
         * @return  void
         */
        public function __construct() {
            // Do nothing if pluggable functions already initialized.
            if ( $this->initialized ) {
                return;
            }
            add_action( 'init', array( $this, '_register_post_type' ), 100, 0 );
            add_action( 'admin_init', array( $this, '_add_vc_capability' ) );
            add_action( 'add_meta_boxes', array( $this, '_add_meta_box' ) );
            add_action( 'in_admin_header', array( $this, '_add_loading_spinner' ), 0, 0 );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999 );

            add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_walker' ),PHP_INT_MAX );

            Familab_Core_Megamenu_Settings::initialize();
            // State that initialization completed.
            $this->initialized = true;
        }
        public function enqueue_scripts ($hook_suffix){

            if ( ( $hook_suffix === 'post-new.php' || $hook_suffix === 'post.php' ) ) {
                if ( $GLOBALS['post']->post_type === 'familab_menu' ) {
                    wp_enqueue_style( 'familabcore-mega-memu-item', FAMILAB_CORE_PLUGIN_URL . '/assets/css/menu-item.css' );
                    wp_enqueue_script( 'familabcore-mega-memu-item', FAMILAB_CORE_PLUGIN_URL. '/assets/js/menu-item.js', array( 'jquery' ), '1.0' );
                }


            }
            if ( $hook_suffix == 'nav-menus.php' ) {
                wp_enqueue_media();
                wp_enqueue_style( 'font-awesome', FAMILAB_CORE_PLUGIN_URL . '/assets/3rd-party/font-awesome/css/font-awesome.min.css' );
                wp_enqueue_style( 'magnific-popup', FAMILAB_CORE_PLUGIN_URL . '/assets/3rd-party/magnific-popup/magnific-popup.css' );
                wp_enqueue_script( 'magnific-popup', FAMILAB_CORE_PLUGIN_URL  . '/assets/3rd-party/magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), '1.0' );

                wp_enqueue_style( 'familabcore-nav-menu', FAMILAB_CORE_PLUGIN_URL  . '/assets/css/nav-menu.css' );
                wp_enqueue_script( 'familabcore-nav-menu', FAMILAB_CORE_PLUGIN_URL  . '/assets/js/nav-menu.js', array( 'jquery' ), '1.0' );
            }

            wp_localize_script( 'familabcore-menu-item', 'familab_menu', array(
                    'ajaxurl'  => admin_url( 'admin-ajax.php' ),
                    'security' => wp_create_nonce( 'familab_menu' ),
                )
            );
            wp_localize_script( 'familabcore-nav-menu', 'familab_nav_menu', array(
                    'ajaxurl'  => admin_url( 'admin-ajax.php' ),
                    'security' => wp_create_nonce( 'familab_nav_menu' ),
                    'post_url' => admin_url( 'post.php' ),
                )
            );


        }

        public function _register_post_type(){
            $labels = array(
                'name'          => esc_html__( 'Mega Menu Items', 'familabcore' ),
                'singular_name' => esc_html__( 'Mega Menu Item', 'familabcore' ),
                'all_items'     => esc_html__( 'All Menu Items', 'familabcore' ),
            );
            $args = array(
                'labels'       => $labels,
                'public'       => true,
                'show_ui'      => true,
                'show_in_menu' => false,
                'supports'     => array( 'title', 'editor' ),
                'exclude_from_search' => true,
            );
            register_post_type( 'familab_menu', $args );
        }

        /**
         * Add users' capability for VC
         *
         * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
         */
        public static  function _add_vc_capability(){
            global $current_user;
            $current_user->allcaps['vc_access_rules_post_types/familab_menu'] = true;
            $current_user->allcaps['vc_access_rules_post_types']           = 'custom';
            if ( in_array( 'administrator', $current_user->caps ) ) {
                $cap = get_role( 'administrator' );
                $cap->add_cap( 'vc_access_rules_post_types', 'custom' );
                $cap->add_cap( 'vc_access_rules_presets', true );
                $cap->add_cap( 'vc_access_rules_settings', true );
                $cap->add_cap( 'vc_access_rules_templates', true );
                $cap->add_cap( 'vc_access_rules_shortcodes', true );
                $cap->add_cap( 'vc_access_rules_grid_builder', true );
                $cap->add_cap( 'vc_access_rules_post_settings', true );
                $cap->add_cap( 'vc_access_rules_backend_editor', true );
                $cap->add_cap( 'vc_access_rules_frontend_editor', true );
                $cap->add_cap( 'vc_access_rules_post_types/post', true );
                $cap->add_cap( 'vc_access_rules_post_types/page', true );
                $cap->add_cap( 'vc_access_rules_post_types/familab_menu', true );
            }
        }

        /**
         * Add metabox
         *
         * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
         *
         * @param    object    \WP_Post
         */
        public function _add_meta_box( $post ){
            add_meta_box(
                'familab_menu_meta_box',
                esc_html__( 'Menu Item Settings', 'familabcore' ),
                array( $this, '_render' ),
                'familab_menu',
                'normal',
                'high'
            );
        }

        public function _render(){

            global $post;
            $item_id = isset( $_REQUEST['familab_menu_item_id'] ) ? absint( $_REQUEST['familab_menu_item_id'] ) : 0;
            ?>
            <input type="text" name="familab_menu_item_id" value="<?php echo esc_attr( $item_id ) ?>">
            <?php
        }
        public function _add_loading_spinner(){
            global $post;
            if ( !$post || $post->post_type !== 'familab_menu' ) {
                return;
            }
            ?>
            <div class="familabcore-mega-menu-loading">
                <div class="vc-mdl">
                    <span class="spinner"></span>
                </div>
            </div><?php
        }

        public function edit_walker(){
            return 'Familab_Core_Megamenu_Edit';
        }
    }
}