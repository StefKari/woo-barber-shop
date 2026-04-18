<?php
/**
 * Plugin Name: Familab Instagram Shop
 * Plugin URI: https://familab.net
 * Description: Flexible plugin which get images from your Instagram automatically
 * Version: 1.0.5
 * Author: Familab
 * Author URI: https://familab.net
 * License: GPLv2 or later
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}
if(!class_exists('Familab_Instagram_Shop')) {
    class Familab_Instagram_Shop {
    	/**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;
        /**
         * Initialize Familab_Instagram_Shop functions.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if Familab_Instagram_Shop functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            self::setup_constants();
			add_action( 'admin_menu', array(__CLASS__ , 'tis_create_menu' ) , 1);
			add_action( 'admin_menu', array(__CLASS__ , 'setting_menu' ) , 100);
            add_action( 'plugins_loaded',array(__CLASS__, 'tis_auto_refresh_images' ));
			add_action( 'plugins_loaded', array( __CLASS__ , 'tis_load_textdomain' ) );
			self::includes();
			add_action( 'admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts_and_styles' ) );
			add_action( 'wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts_and_styles' ) );
			add_action( 'admin_init', array(__CLASS__,'register_mysettings')  );
			Tis_Functions::initialize();
			Tis_Shortcode::initialize();
            self::$initialized = true;
        }
		/**
		 * Check if required plugins is installed
		 *
		 */
		public static function plugin_check() {
			if( ! class_exists('WooCommerce')){
				ob_start();
				?>
				<div class="tis-content-wrapper clearfix">
					 <div class="tis-error-field p-3 mb-2 bg-warning text-dark" style="<?php echo esc_attr($warning_class); ?>">
				    	<?php esc_html_e( 'Please install WooCommerce Plugin first before using this plugin', 'familab-instagram-shop' ); ?>
				    </div>
				</div>
				<?php
				$html = ob_get_clean();
				echo $html;
				wp_die();
            }
		}
		/**
		 * Define constant if not already set.
		 *
		 * @param string      $name  Constant name.
		 * @param string|bool $value Constant value.
		 */
		private static function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
		/**
		* Setup Constants
		*/
		public static function setup_constants() {
			// Plugin version.
			if ( !defined( 'TIS_VERSION' ) ) {
				self::define( 'TIS_VERSION', '1.0' );
			}
			if ( !defined( 'TIS_BASE_URL' ) ) {
				self::define( 'TIS_BASE_URL', trailingslashit( plugins_url( 'familab-instagram-shop' ) ) );
			}
			if ( !defined( 'TIS_DIR_PATH' ) ) {
				self::define( 'TIS_DIR_PATH', plugin_dir_path( __FILE__ ) );
			}
			if ( !defined( 'TIS_CSS_URL' ) ) {
				self::define( 'TIS_CSS_URL', TIS_BASE_URL . 'assets/css/' );
			}
			if ( !defined( 'TIS_JS_URL' ) ) {
				self::define( 'TIS_JS_URL', TIS_BASE_URL . 'assets/js/' );
			}
			if ( !defined( 'TIS_IMG_URL' ) ) {
				self::define( 'TIS_IMG_URL', TIS_BASE_URL . 'assets/images/' );
			}
		}
		/**
		* Load plugin textdomain
		*/
		public static function tis_load_textdomain() {
			load_plugin_textdomain( 'familab-instagram-shop', false, TIS_DIR_PATH . 'languages' );
		}
		/**
		* Register Settings
		*/
	    public static function register_mysettings() {
		    register_setting( 'tis-settings-group', 'tis_ins_token' );
	    }
		/**
		* Include Files
		*/
		public static function includes() {
			/*
			 * Register post type
			 */
			require_once( TIS_DIR_PATH . 'includes/register-post-type.php' );
			/*
			 * Load functions
			 */
			require_once( TIS_DIR_PATH . 'includes/functions.php' );
			/**
			 * Load shortcodes
			 */
			require_once( TIS_DIR_PATH . 'includes/shortcodes.php' );
            if ( class_exists( 'VC_Manager' ) ) {
                require_once( TIS_DIR_PATH . 'includes/visual-composer.php' );
            }
		}
	    /**
		* Create admin menu
		*/
	    public static function tis_create_menu() {
	    	add_menu_page( 
	    		 'Instagram Shop', 
	    		 'Instagram Shop', 
	    		 'manage_options', 
	    		 'familab-instagram-shop', 
	    		array(__CLASS__,'tis_settings_page'), 
	    		 TIS_IMG_URL.'logo21.png', 
	    		 3 
	    	);
	    }
	    public static function setting_menu(){
            remove_submenu_page( 'familab-instagram-shop', 'familab-instagram-shop' );
            add_submenu_page(
                'familab-instagram-shop',
                'Instagram Settings',
                'Settings',
                'manage_options',
                'familab-instagram-shop',
                array(__CLASS__,'tis_settings_page') );
        }
	    /**
		* Render settings page
		*/
	    public static function tis_settings_page(){
	    	require_once( TIS_DIR_PATH . 'pages/settings-page.php' );
		}
	    /**
		* Enqueue Style Sheet & JS Backend
		*/
	    public static function enqueue_admin_scripts_and_styles(){
			global $tis, $pagenow, $post_type;
			$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
			$post_pages =  array( 'edit.php', 'post.php', 'post-new.php' );
	        if ( $page == 'familab-instagram-shop' || (in_array($pagenow, $post_pages) && 'familab-instagram' == $post_type ) )
	        {
	        	wp_register_style( 'bootstrap', TIS_CSS_URL.'bootstrap.min.css', array() , '4.1.3' );
		        wp_enqueue_style( 'bootstrap' );
		        wp_register_script('bootstrap', TIS_JS_URL.'bootstrap.min.js', array() , '4.1.3' );
	        	wp_enqueue_script( 'bootstrap' );
	        	wp_register_style( 'tis-backend-css', TIS_CSS_URL.'backend.css', array() , TIS_VERSION );
	        	wp_enqueue_style( 'tis-backend-css' );
	        }
			if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
				if ( 'familab-instagram' == $post_type ) {
					wp_register_style( 'font-awesome', TIS_CSS_URL.'font-awesome.min.css', array() , '4.7.0' );
	       			wp_enqueue_style( 'font-awesome' );
	       			wp_register_style( 'select2', TIS_CSS_URL.'select2.min.css', array() , '4.0.6' );
	       			wp_enqueue_style( 'select2' );
	       			wp_register_script( 'select2-js', TIS_JS_URL . 'select2.full.min.js', array( 'jquery' ), '4.0.6', true );
					wp_enqueue_script( 'select2-js' );
					wp_enqueue_script( 'jquery-ui-sortable' );
					wp_enqueue_script( 'jquery-ui-draggable' );
					if ( 'edit.php' != $pagenow ) {
						wp_register_script( 'tis-backend', TIS_JS_URL . 'tis-backend.js', array( 'jquery' ), date("h:i:s"), true );
						$localize_array = self::tis_localize();
						wp_enqueue_script( 'tis-backend' );
						// Localize the script with new data
						wp_localize_script( 'tis-backend', 'tis_strings', $localize_array );
					}
				}
			}
		}
		/**
		* Enqueue Style Sheet & JS Front end
		*/
	    public static function enqueue_scripts_and_styles(){
        	wp_register_style( 'bootstrap', TIS_CSS_URL.'bootstrap.min.css', array() , '4.1.3' );
	        wp_enqueue_style( 'bootstrap' );
	        wp_enqueue_style( 'magnific-popup', TIS_CSS_URL. 'magnific-popup.css', array(), '1.1.0' );
            wp_enqueue_script( 'magnific-popup', TIS_JS_URL. 'jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
            wp_enqueue_style( 'jquery-scrollbar', TIS_CSS_URL. 'jquery.mCustomScrollbar.min.css', array(), false );
            wp_enqueue_script( 'jquery-scrollbar', TIS_JS_URL. 'jquery.mCustomScrollbar.min.js' , array( 'jquery' ), false, true );
            wp_enqueue_script( 'anime', TIS_JS_URL. 'anime.min.js', array( 'jquery' ), '1.1.0', true );
	        wp_register_script('bootstrap', TIS_JS_URL.'bootstrap.min.js', array() , '4.1.3' );
        	wp_enqueue_script( 'bootstrap' );
        	wp_register_style( 'swiper', TIS_CSS_URL.'swiper.min.css', array() , '4.4.5' );
        	wp_enqueue_style( 'swiper' );
        	wp_register_script( 'swiper', TIS_JS_URL . 'swiper.min.js', array( 'jquery' ), '4.4.5', true );
			wp_enqueue_script( 'swiper' );
			wp_register_script( 'lazysizes', TIS_JS_URL.'lazysizes.min.js', array() , '4.1.5' );
			wp_enqueue_script( 'lazysizes' );
			wp_register_style( 'tis-frontend-css', TIS_CSS_URL.'frontend.css', array() , TIS_VERSION );
        	wp_enqueue_style( 'tis-frontend-css' );
        	wp_register_script( 'tis-frontend', TIS_JS_URL . 'tis-frontend.js', array( 'jquery' ), TIS_VERSION, true );
			// Localize the script with new data
			$localize_array = self::tis_localize();
			wp_enqueue_script( 'tis-frontend' );
			wp_localize_script( 'tis-frontend', 'tis_strings', $localize_array );
		}
		/**
		 * Set Localize Strings
		 * @return array
		 */
		public static function tis_localize() {
			// hotspot_type is not used yet
			$tis_localize = array(
				'text' => array(
						'add_product' => __( 'Add product', 'familab-instagram-shop' ),
						'remove_image' => __( 'Remove Image', 'familab-instagram-shop' ),
						'select_product' => __('Select product', 'familab-instagram-shop'),
						'order_image'=>__('Order image', 'familab-instagram-shop')
				),
				'ajax_url'=>admin_url('admin-ajax.php')
			);
			return apply_filters( 'tis_localize', $tis_localize );
		}
		public static function tis_auto_refresh_images(){
		    $cache_run =  get_transient('tis_auto_refresh_images');
		    if ($cache_run == false){
                $args = array(
                    'posts_per_page'   => -1,
                    'offset'           => 0,
                    'post_type'        => 'familab-instagram',
                    'post_status'      => 'publish',
                    'suppress_filters' => true,
                );
                $posts_array = get_posts( $args );
                if( !empty($posts_array)){
                    $tis_ins_token = get_option('tis_ins_token', false);
                    foreach ($posts_array as $post){
                        $pin_data_str = get_post_meta( $post->ID, 'tis_pin_data', true );
                        if ( trim( $pin_data_str ) != '' ) {
                            $images_list = array();
                            $pin_data_array = json_decode( $pin_data_str );
                            foreach ( $pin_data_array as $image_item=>$value ) {
                                if ($value->src == '' || !is_array(@getimagesize($value->src))){
                                    $images_list[] = array('src'=>$value->src,'id'=>$image_item);
                                }
                            }
                            if (sizeof($images_list)> 0){
                                $update_status = false;
                                foreach ($images_list as $img_item) {
                                    $image_id = $img_item['id'];
                                    $request_url = 'https://api.instagram.com/v1/media/'.$image_id.'?access_token='.$tis_ins_token;
                                    $response = wp_remote_get( $request_url );
                                    if ( ! is_wp_error( $response ) ) {
                                        $response_code = wp_remote_retrieve_response_code( $response );
                                        if ($response_code == 200){
                                            $response_body = json_decode( $response['body'] );
                                            $update_status = true;
                                            $image_data = $response_body->data;
                                            $pin_data_array->$image_id->src = $image_data->images->standard_resolution->url;
                                            $pin_data_array->$image_id->thumb = $image_data->images->thumbnail->url;
                                            $pin_data_array->$image_id->low_res = $image_data->images->low_resolution->url;
                                        }
                                    }
                                }
                                if ($update_status)
                                    update_post_meta( $post->ID, 'tis_pin_data', json_encode($pin_data_array) );
                            }
                        }
                        $pin_data_str = '';
                    }
                }
                set_transient('tis_auto_refresh_images',true,3*DAY_IN_SECONDS);
            }
        }
    }
	add_action('plugins_loaded','Familab_Instagram_Shop::initialize',9);
}
?>