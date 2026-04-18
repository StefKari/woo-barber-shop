<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'TisPostType' ) ) {
	class TisPostType {
		public function __construct() {
			add_action( 'init', array( $this, 'initialize' ), 99 );
		}
		
		public static function initialize() {
			if( ! class_exists('WooCommerce')){
				return;
			}
			$args = array(
				'labels'              => array(
					'name'          => __( 'Instagram Shop', 'familab-instagram-shop' ),
					'singular_name' => __( 'Instagram Shop', 'familab-instagram-shop' ),
					'add_new'       => __( 'Add New', 'familab-instagram-shop' ),
					'add_new_item'  => __( 'Add new Product Pin', 'familab-instagram-shop' ),
					'edit_item'     => __( 'Edit Product Pin', 'familab-instagram-shop' ),
					'new_item'      => __( 'New Product Pin', 'familab-instagram-shop' ),
					'view_item'     => __( 'View Product Pin', 'familab-instagram-shop' ),
					'menu_name'     => __( 'Instagram Shop', 'familab-instagram-shop' ),
				),
				'supports'            => array( 'page-attributes' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'familab-instagram-shop',
				'menu_position'       => 40,
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'post',
			);
			register_post_type( 'familab-instagram', $args );
			
			// Check if zaniss page is requested.
			global $pagenow, $post_type, $post;
			
			if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
				// Get current post type.
				if ( ! isset( $post_type ) ) {
					$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null;
				}
				
				if ( empty( $post_type ) && ( isset( $post ) || isset( $_REQUEST['post'] ) ) ) {
					$post_type = isset( $post ) ? $post->post_type : get_post_type( $_REQUEST['post'] );
				}
				
				if ( 'familab-instagram' == $post_type ) {
					add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 9999 );

					/* Remove Visual composer script/style */
					add_action( 'vc_backend_editor_enqueue_js_css',  array( __CLASS__, 'remove_vc_output'), 9999 );
					if ( 'edit.php' == $pagenow ) {
						add_filter( 'manage_familab-instagram_posts_columns', array( __CLASS__, 'register_columns' ) );
						add_action( 'manage_familab-instagram_posts_custom_column', array( __CLASS__, 'display_columns' ), 10, 2 );
					} else if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

						
						if ( ! isset( $_REQUEST['action'] ) || 'trash' != $_REQUEST['action'] ) {
							// Register necessary actions / filters to override Item Details screen.	
							add_action( 'edit_form_after_title', array( __CLASS__,'load_edit_form' ), 1);
							add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
						}
					}

					
				}
			}
			
			add_action( 'do_meta_boxes', array(__CLASS__, 'remove_revolution_slider_meta_boxes') );
		}

		/**
		 * Remove Rev Slider Metabox
		 */
		public static function remove_revolution_slider_meta_boxes() {
			remove_meta_box( 'mymetabox_revslider_0', 'familab-instagram', 'normal' );
		}

		
		public static function remove_vc_output(){
		    wp_dequeue_style( 'js_composer' );
		}

		/**
		 * Setup bulk actions for in stock alert subscription screen.
		 *
		 * @param   array $actions Current actions.
		 *
		 * @return  array
		 */
		public static function bulk_actions( $actions ) {
			// Remove edit action.
			unset( $actions['edit'] );
			
			return $actions;
		}
		
		/**
		 * Register columns for in stock alert subscription screen.
		 *
		 * @param   array $columns Current columns.
		 *
		 * @return  array
		 */
		public static function register_columns( $columns ) {
			$columns = array(
				'cb'        => '<input type="checkbox" />',
				'title'     => __( 'Name', 'familab-instagram-shop' ),
				'shortcode' => __( 'Shortcode', 'familab-instagram-shop' ),
				'date'      => __( 'Time', 'familab-instagram-shop' ),
			);
			
			return $columns;
		}
		
		/**
		 * Display columns for in stock alert subscription screen.
		 *
		 * @param   array $column  Column to display content for.
		 * @param   int   $post_id Post ID to display content for.
		 *
		 * @return  array
		 */
		public static function display_columns( $column, $post_id ) {
			switch ( $column ) {
				case 'shortcode' :
				$shortcode = Tis_Shortcode::get_shortcode_string($post_id);
				echo '<span>'.$shortcode.'</span>';
				break;
			}	
		}

		/**
		 * Enqueue assets for custom add/edit item form.
		 *
		 * @return  string
		 */
		public static function enqueue_assets() {
			// Check if WR Mapper page is requested.
			global $pagenow, $post_type;
			wp_dequeue_script( 'select2' );
			if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
				if ( 'familab-instagram' == $post_type && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
						wp_enqueue_media();
				}
			}
		}

		/**
		 * Load custom add/edit item form.
		 *
		 * @return  void
		 */
		public static function load_edit_form() {
			// Load edit page template
			require_once TIS_DIR_PATH . 'pages/edit-page-functions.php';
			require_once TIS_DIR_PATH . 'pages/edit-page-template.php';
			
		}
		/**
		 * Save custom post type extra data.
		 *
		 * @param   int $id Current post ID.
		 *
		 * @return  void
		 */
		public static function save_post( $id ) {

			// Publish post if needed.
			if ( ! defined( 'DOING_AUTOSAVE' ) || ! DOING_AUTOSAVE ) {
				$post = get_post( $id );
				
				if ( __( 'Auto Draft' ) != $post->post_title && 'publish' != $post->post_status ) {
					wp_publish_post( $post );
				}
			}
			
			// Check Autosave
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
				return $id;
			}
			// Don't save if only a revision
			if ( isset( $post->post_type ) && $post->post_type == 'revision' ) {

				return $id;
			}
			// Check permissions
			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				return $id;
			}
			
			if ( ! isset( $_POST['tis_edit_nonce'] ) ) {
				return $id;
			}
			
			if ( ! wp_verify_nonce( $_POST['tis_edit_nonce'], 'tis_edit_nonce' ) ) {
				return $id;
			}
			
			if ( isset( $_POST['social_shop_pin'] ) ) {
				$pin_data = $_POST['social_shop_pin'];
				$pin_data = stripcslashes($pin_data);
				update_post_meta( $id, 'tis_pin_data', maybe_serialize( $pin_data ) );
			} else {
				delete_post_meta( $id, 'tis_pin_data' );
			}
			
			// Save shortcode options
			if ( isset( $_POST['tis_use_custom_responsive'] ) ) {
				$use_custom_responsive = $_POST['tis_use_custom_responsive'];
				update_post_meta( $id, 'tis_use_custom_responsive', $use_custom_responsive );
			} else {
				update_post_meta( $id, 'tis_use_custom_responsive', 'no' );
			}
			
			$items_on_screen_meta_keys = array(
				'tis_xl_cols',
				'tis_lg_cols',
				'tis_md_cols',
				'tis_sm_cols',
				'tis_xs_cols'
			);
			
			foreach ( $items_on_screen_meta_keys as $items_on_screen_meta_key ) {
				if ( isset( $_POST[ $items_on_screen_meta_key ] ) ) {
					$items_on_screen = $_POST[ $items_on_screen_meta_key ];
					update_post_meta( $id, $items_on_screen_meta_key, $items_on_screen );
				}
			}

			if ( isset( $_POST['tis_style'] ) ) {
				$tis_style = $_POST['tis_style'];
				update_post_meta( $id, 'tis_style', $tis_style );
			} else {
				update_post_meta( $id, 'tis_style', 'grid' );
			}

			if ( isset( $_POST['tis_type'] ) ) {
				$tis_type = $_POST['tis_type'];
				update_post_meta( $id, 'tis_type', $tis_type );
			} else {
				update_post_meta( $id, 'tis_type', 'pin' );
			}

			if ( isset( $_POST['tis_resolution'] ) ) {
				$tis_resolution = $_POST['tis_resolution'];
				update_post_meta( $id, 'tis_resolution', $tis_resolution );
			} else {
				update_post_meta( $id, 'tis_resolution', 'large' );
			}
			if ( isset( $_POST['carousel_space'] ) ) {
				$carousel_space = $_POST['carousel_space'];
				update_post_meta( $id, 'carousel_space', $carousel_space );
			} else {
				update_post_meta( $id, 'carousel_space', 0 );
			}
		}
	}
	
	new TisPostType();
}