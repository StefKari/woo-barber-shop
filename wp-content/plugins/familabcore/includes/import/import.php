<?php
if( !class_exists('Familab_Core_Import')) {
	define( 'FAMILAB_CORE_IMPORT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
    if (!class_exists('Familab_WP_Importer_Logger')){
        require_once  dirname( __FILE__ ) . '/class-logger.php';
    }
    require_once  dirname( __FILE__ ) . '/class-wxr-import-info.php';
    require_once  dirname( __FILE__ ) . '/class-logger-serversentevents.php';
	class Familab_Core_Import {
		public $demos = array();
		public $fetch_attachments = true;
		protected $data;
		protected $setting;
		public function __construct() {
			add_action('after_setup_theme',array($this,'settup_import_data'));
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'wp_ajax_familab-wxr-import', array( $this, 'stream_import' ) );
			add_action('wp_ajax_familab_set_home_page',array($this,'set_home_page'));
		}
		public function register_menu() {
			if ( current_theme_supports('familab-core') ) {
				add_submenu_page( 'themes.php',
                    FAMILAB_THEME_NAME.' Import',
                    FAMILAB_THEME_NAME.' Import',
					'manage_options',
					'familab-core-import',
					array(
						&$this,
						'dispatch',
					) );
			}
		}
		public function set_home_page(){
            check_ajax_referer( 'familab_intro_ajax_admin', 'security' );
            $data = get_option('familab_pages_options_preset',array());
            $selected_page = isset($_POST['selected_page'])? $_POST['selected_page'] : '';
            $result = array('success' => true);
            $result['options_preset'] = $data;
            $result['selected_page'] = $selected_page;
            if ($selected_page !='' && isset($data['homes'][$selected_page])){
                $data_page = $data['homes'][$selected_page];
                $result['data_page'] = $data_page;
                if (isset($data_page['slug']) || isset($data_page['name'])){
                    if (!is_null($page = get_page_by_title($data_page['name']))){
                        update_option( 'page_on_front', $page->ID );
                    }else if (isset($data_page['slug']) && !is_null($page = get_page_by_path($data_page['slug']))){
                        update_option( 'page_on_front', $page->ID );
                    }else{
                        $result['success'] = false;
                        $result['debug'] = 'get page false';
                    }
                }else{
                    $result['success'] = false;
                    $result['debug'] = 'get page false';
                }
                if ($result['success'] && isset($data_page['settings'])){
                    $theme_option = get_option(FAMILAB_THEME_SLUG,array());
                    $option_maps = apply_filters('familab_core_import_maps_required',array());
                    //$map_type = '';
                    $upload_dir = wp_upload_dir();
                    $package_key = $data_page['package_key'];
                    foreach ($data_page['settings'] as $key => $val){
                        if (!key_exists($key,$theme_option)){
                            continue;
                        }
                        if (in_array($key,$option_maps['background'])){
                            //$map_type = 'background';
                            if (isset($val['background-image'])){
                                $old_bg_url = $val['background-image'];
                                if (stripos($old_bg_url,'uploads') !== false){
                                    $old_url_info = pathinfo($old_bg_url);
                                    $_urlxc = $upload_dir['basedir'].DS. $old_url_info['basename'];
                                    if ( file_exists( $_urlxc ) ) {
                                        $val['background-image']  = $upload_dir['baseurl'].DS . $old_url_info['basename'];
                                    }
                                }
                            }
                            if (isset($val['media']) && isset($val['media']['thumbnail'])){
                                $old_me_url = $val['media']['thumbnail'];
                                if (stripos($old_me_url,'uploads') !== false){
                                    $old_me_url_info = pathinfo($old_me_url);
                                    $_urlxc = $upload_dir['basedir'].DS. $old_me_url_info['basename'];
                                    if ( file_exists( $_urlxc ) ) {
                                        $val['background-image']  = $upload_dir['baseurl'].DS . $old_me_url_info['basename'];
                                    }
                                }
                            }
                            $theme_option[$key] = array_merge($theme_option[$key],$val);
                        }else if (in_array($key,$option_maps['media'])){
                            //$map_type = 'media';
                            if (isset($val['url'])){
                                $old_url = $val['url'];
                                if (stripos($old_url,'uploads') !== false){
                                    $old_url_info = pathinfo($old_url);
                                    $_urlxc = $upload_dir['basedir'].DS. $old_url_info['basename'];
                                    if ( file_exists( $_urlxc ) ) {
                                        $val['url']  = $upload_dir['baseurl'].DS . $old_url_info['basename'];
                                    }
                                }
                            }
                            if (isset($val['thumbnail'])){
                                $old_tb_url = $val['thumbnail'];
                                if (stripos($old_tb_url,'uploads') !== false){
                                    $old_tb_url_info = pathinfo($old_url);
                                    $_urlxc = $upload_dir['basedir'].DS. $old_tb_url_info['basename'];
                                    if ( file_exists( $_urlxc ) ) {
                                        $val['url']  = $upload_dir['baseurl'].DS . $old_tb_url_info['basename'];
                                    }
                                }
                            }
                            $theme_option[$key] = array_merge($theme_option[$key],$val);
                        }else if (in_array($key,$option_maps['post'])){
                            //$map_type = 'post';
                            $processed_posts = get_option('familab_imported_maps',array());
                            if (!$val){
                                $theme_option[$key] = $val;
                            }
                            else if (!empty($processed_posts) && isset($processed_posts[$package_key]['post'][$val])) {
                                $theme_option[$key] = $processed_posts[$package_key]['post'][$val];
                            }
                        }else{
                            $theme_option[$key] = $val;
                        }
                    }
                    $result['theme_option'] = $theme_option;
                    update_option(FAMILAB_THEME_SLUG,$theme_option);
                }
            }else{
                $result['success'] = false;
                $result['debug'] = 'ajax false';
            }
		    wp_send_json($result);
        }
		public function settup_import_data($demos = array()){
			$this->demos = apply_filters('familab_core_import_demos',$demos);
		}
		public function dispatch() {
			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
			switch ( $step ) {
				case 0:
					$this->display_intro_step();
					break;
				case 1:
					$this->display_import_step();
					break;
			}
		}
		public function display_intro_step($display_info = true){
			$demos = $this->demos;
			$import_time = get_option('familab_import_times',array());
            wp_enqueue_style( 'igrowl', FAMILAB_CORE_PLUGIN_URL. '/assets/3rd-party/iGrowl/css/igrowl.min.css', array(), false );
            wp_enqueue_script( 'igrowl', FAMILAB_CORE_PLUGIN_URL. '/assets/3rd-party/iGrowl/js/igrowl.min.js', array( 'jquery' ), false, true );
            wp_enqueue_script( 'familab-intro-admin',FAMILAB_CORE_PLUGIN_URL. '/assets/js/intro.js', array( ), '1.0.0', true );
            wp_localize_script( 'familab-intro-admin', 'familab_intro_ajax_admin', array(
                    'ajaxurl'  => admin_url( 'admin-ajax.php' ),
                    'security' => wp_create_nonce( 'familab_intro_ajax_admin' ),
                    'done_msg' => esc_html__('All setting successfully'),
                    'err_msg' => esc_html__('Setting AJAX problem. Please, try setting data manually.')
                )
            );
			require __DIR__ . '/templates/intro.php';
		}
		/**
		 * Display the actual import step.
		 */
		protected function display_import_step() {
			$args = wp_unslash( $_POST );
			if (empty($this->demos)){
			    return;
            }
			if (!isset($args['type'])){
			    foreach ($this->demos as $type => $demo){
                    $args['type'] = $type;
                    break;
                }
			}
			if ($args['type'] == 'min'){
				$this->fetch_attachments = false;
			}
			do_action('familab_core_before_import',$args);
			// Check the nonce.
            $upload_dir = wp_upload_dir();
            $_x_path       = "{$upload_dir['basedir']}".DS.FAMILAB_THEME_SLUG.'_import'.DS.$args['type'].DS;
			$file = $_x_path . 'content.xml';
			$importer = $this->get_importer();
            $importer->download_package($args['type'],'content',$_x_path,false);
            if ( ! file_exists( $file ) ) {
                wp_die( sprintf( esc_html__( 'Can not find import file: %s' ), $file ) );
                return;
            }
			$data_result = $importer->get_preliminary_information( $file );

            $data = $data_result['data_info'];
			if ( is_wp_error( $data ) ) {
				return;
			}
			if (!$this->fetch_attachments){
                $data->package_count = 0;
            }
            $GLOBALS['import_path'] = $_x_path;
			$mapping = $this->get_author_mapping($data->users);
			$fetch_attachments = $this->fetch_attachments;
			if (isset($this->demos[$args['type']]['sliders'])){
			    $slider_packages = $this->demos[$args['type']]['sliders'];
            }else{
                $slider_packages = false;
            }
			$type = $args['type'];
			// Set our settings
			$settings = compact( 'mapping', 'fetch_attachments' ,'type','slider_packages');

			update_option('familab_wxr_import_settings',$settings);
            update_option('familab_wxr_import_data',$data_result['data_xml']);
			//update_post_meta( $this->id, '_wxr_import_settings', $settings );
			// Time to run the import!
			set_time_limit( 0 );
			// Ensure we're not buffered.
			wp_ob_end_flush_all();
			flush();
			require __DIR__ . '/templates/import.php';
		}
		/**
		 * Get mapping data from request data.
		 *
		 * Parses form request data into an internally usable mapping format.
		 *
		 * @param array $args Raw (UNSLASHED) POST data to parse.
		 * @return array Map containing `mapping` and `slug_overrides` keys.
		 */
		protected function get_author_mapping( $args ) {
			$mapping = array();
			foreach ( $args as $author ) {
				$mapping[] = array(
					'old_slug' => $author['author_login'],
					'old_id'   => $author['author_id'],
					'new_id'   => get_current_user_id(),
				);
			}
            $slug_overrides = array();
            return compact( 'mapping', 'slug_overrides' );
		}
		/**
		 * Get the importer instance.
		 *
		 * @return WXR_Importer
		 */
		protected function get_importer() {
            require_once  dirname( __FILE__ ) . '/class-wxr-importer.php';
			$importer = new Familab_WXR_Importer( $this->get_import_options() );
			$logger = new Familab_WP_Importer_Logger_ServerSentEvents();
			$importer->set_logger( $logger );
			return $importer;
		}
		/**
		 * Get options for the importer.
		 *
		 * @return array Options to pass to WXR_Importer::__construct
		 */
		protected function get_import_options() {
			$options = array(
				'fetch_attachments' => $this->fetch_attachments,
				'default_author'    => get_current_user_id(),
			);
			return $options;
		}
		/**
		 * Run an import, and send an event-stream response.
		 *
		 * Streams logs and success messages to the browser to allow live status
		 * and updates.
		 */
		public function stream_import() {
			// Turn off PHP output compression
			$previous = error_reporting( error_reporting() ^ E_WARNING );
			ini_set( 'output_buffering', 'off' );
			ini_set( 'zlib.output_compression', false );
			error_reporting( $previous );
			if ( $GLOBALS['is_nginx'] ) {
				// Setting this header instructs Nginx to disable fastcgi_buffering
				// and disable gzip for this request.
				header( 'X-Accel-Buffering: no' );
				header( 'Content-Encoding: none' );
			}
			// Start the event stream.
			header( 'Content-Type: text/event-stream' );
			$settings = get_option('familab_wxr_import_settings');
			if ( empty( $settings ) ) {
				// Tell the browser to stop reconnecting.
				status_header( 204 );
				exit;
			}

			// 2KB padding for IE
			echo ':' . str_repeat( ' ', 2048 ) . "\n\n";

			// Time to run the import!
			set_time_limit( 0 );

			// Ensure we're not buffered.
			wp_ob_end_flush_all();
			flush();
			$mapping = $settings['mapping'];
			$this->fetch_attachments = (bool) $settings['fetch_attachments'];
			$import_type = $settings['type'];

			$importer = $this->get_importer();
			if ( ! empty( $mapping['mapping'] ) ) {
				$importer->set_user_mapping( $mapping['mapping'] );
			}
			if ( ! empty( $mapping['slug_overrides'] ) ) {
				$importer->set_user_slug_overrides( $mapping['slug_overrides'] );
			}

			// Are we allowed to create users?
			if ( ! $this->allow_create_users() ) {
				add_filter( 'wxr_importer.pre_process.user', '__return_null' );
			}
			// Keep track of our progress
			add_action( 'wxr_importer.processed.widget', array( $this, 'imported_widget' ) );
            add_action( 'wxr_importer.process_false.widget', array( $this, 'imported_widget' ) );
			add_action( 'wxr_importer.processed.setting', array( $this, 'imported_setting' ) );
			add_action( 'wxr_importer.processed.package', array( $this, 'imported_package' ) );
			add_action( 'wxr_importer.process_failed.package', array( $this, 'imported_package' ) );
			add_action( 'wxr_importer.process_already_imported.package', array( $this, 'imported_package' ) );
			add_action( 'wxr_importer.processed.post', array( $this, 'imported_post' ), 10, 2 );
			add_action( 'wxr_importer.process_invalid.post', array( $this, 'invalid_post' ),10,1);
			add_action( 'wxr_importer.process_failed.post', array( $this, 'imported_post' ), 10, 2 );
			add_action( 'wxr_importer.process_already_imported.post', array( $this, 'already_imported_post' ), 10 );
			add_action( 'wxr_importer.process_skipped.post', array( $this, 'already_imported_post' ), 10 );
			add_action( 'wxr_importer.processed.comment', array( $this, 'imported_comment' ) );
			add_action( 'wxr_importer.process_already_imported.comment', array( $this, 'imported_comment' ) );
			add_action( 'wxr_importer.processed.term', array( $this, 'imported_term' ) );
			add_action( 'wxr_importer.process_failed.term', array( $this, 'imported_term' ) );
			add_action( 'wxr_importer.process_already_imported.term', array( $this, 'imported_term' ) );
			add_action( 'wxr_importer.processed.category', array( $this, 'imported_category' ) );
			add_action( 'wxr_importer.process_failed.category', array( $this, 'imported_category' ) );
			add_action( 'wxr_importer.process_already_imported.category', array( $this, 'imported_category' ) );
            $slider_packages = (isset($settings['slider_packages']))? $settings['slider_packages']:false;
			// Clean up some memory
			unset( $settings );
			// Flush once more.
			flush();

			if ($this->fetch_attachments){
                $importer->process_package($import_type);
            }

            $upload_dir = wp_upload_dir();
            $_x_path       = "{$upload_dir['basedir']}".DS.FAMILAB_THEME_SLUG.'_import'.DS.$import_type.DS;
            $GLOBALS['import_path'] = $_x_path;
            $file = $_x_path . 'content.xml';
			$err = $importer->import( $file );

            $importer->import_woocommerce_image_sizes();
			$importer->import_page_options();
			$importer->import_widgets();
			$importer->import_menus();
			$importer->import_woocommerce_pages();
            $importer->import_redux_option(FAMILAB_THEME_SLUG);
            $importer->import_filter_options();
            $importer->import_rev_sliders($import_type,$slider_packages);

			// Remove the settings to stop future reconnects.
			//delete_post_meta( $this->id, '_wxr_import_settings' );
			delete_option('familab_wxr_import_settings');
            delete_option('familab_wxr_import_data');
			// Let the browser know we're done.
			$complete = array(
				'action' => 'complete',
				'error' => false,
			);
			if ( is_wp_error( $err ) ) {
                $complete['error'] = $err->get_error_message();
            }
            $import_times = get_option('familab_import_times',array());
			if (isset($import_times[$import_type])){
                $import_times[$import_type]++;
            }else{
                $import_times[$import_type] = 1;
            }
            update_option('familab_import_times',$import_times);
			$this->emit_sse_message( $complete );
			exit;
		}
		/**
		 * Decide whether or not the importer is allowed to create users.
		 * Default is true, can be filtered via import_allow_create_users
		 *
		 * @return bool True if creating users is allowed
		 */
		protected function allow_create_users() {
			return apply_filters( 'import_allow_create_users', true );
		}
		/**
		 * Emit a Server-Sent Events message.
		 *
		 * @param mixed $data Data to be JSON-encoded and sent in the message.
		 */
		protected function emit_sse_message( $data ) {
			echo "event: message\n";
			echo 'data: ' . wp_json_encode( $data ) . "\n\n";
			// Extra padding.
			echo ':' . str_repeat( ' ', 2048 ) . "\n\n";
			flush();
		}

		/**
		 * Send message when a post has been imported.
		 *
		 * @param int $id Post ID.
		 * @param array $data Post data saved to the DB.
		 */
		public function imported_post( $id, $data ) {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => ( $data['post_type'] === 'attachment' ) ? 'media' : 'posts',
				'post_id' => $data['post_id'],
				'delta'  => 1,
			));
		}
		/**
		 * Send message when a invalid post_type.
		 *
		 * @param int $id Post ID.
		 * @param array $data Post data saved to the DB.
		 */
		public function invalid_post($data ) {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => ( $data['post_type'] === 'attachment' ) ? 'media' : 'posts',
                'post_id' => $data['post_id'],
				'delta'  => 1,
			));
		}

		/**
		 * Send message when a post is marked as already imported.
		 *
		 * @param array $data Post data saved to the DB.
		 */
		public function already_imported_post( $data ) {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => ( $data['post_type'] === 'attachment' ) ? 'media' : 'posts',
                'post_id' => $data['post_id'],
				'delta'  => 1,
			));
		}

		/**
		 * Send message when a comment has been imported.
		 */
		public function imported_comment() {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'comments',
				'delta'  => 1,
			));
		}

		/**
		 * Send message when a term has been imported.
		 */
		public function imported_term() {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'terms',
				'delta'  => 1,
			));
		}

		/**
		 * Send message when a user has been imported.
		 */
		public function imported_user() {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'users',
				'delta'  => 1,
			));
		}

		/**
		 * Send message when a user is marked as already imported.
		 *
		 * @param array $data User data saved to the DB.
		 */
		public function already_imported_user( ) {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'users',
				'delta'  => 1,
			));
		}

		/**
		 * Send message when a term has been imported.
		 */
		public function imported_category() {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'categories',
				'delta'  => 1,
			));
		}
		/**
		 * Send message when package download
		 */
		public function imported_package() {
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'package',
				'delta'  => 1,
			));
		}
		public function imported_setting(){
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'setting',
				'delta'  => 1,
			));
		}
		public function imported_widget(){
			$this->emit_sse_message( array(
				'action' => 'updateDelta',
				'type'   => 'widget',
				'delta'  => 1,
			));
		}
	}
}
