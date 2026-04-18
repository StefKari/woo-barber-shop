<?php

if (!defined('ABSPATH')){
    exit;
}

class WCPA_Backend extends WCPA_Order_Meta
{

    /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file = '', $version = '1.0.0')
    {
        $this->_version = $version;
        $this->_token = WCPA_TOKEN;
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

        $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        register_activation_hook($this->file, array($this, 'install'));
        // register_deactivation_hook($this->file, array($this, 'deactivation'));

        add_action('upgrader_process_complete', array($this, 'upgrader_process_complete'), 10, 2);

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'), 10, 1);

        add_filter('woocommerce_order_item_display_meta_value', array($this, 'display_meta_value'), 10, 3);

        add_filter('post_row_actions', array($this, 'post_row_actions'), 10, 2);
        add_action('wp_ajax_wcpa_duplicate_form', array($this, 'duplicate_form'));

        add_action('woocommerce_before_order_itemmeta', array($this, 'order_item_line_item_html'), 10, 3);

        add_action('woocommerce_before_save_order_items', array($this, 'before_save_order_items'), 10, 2);

        add_action('woocommerce_order_item_get_formatted_meta_data', array($this, 'order_item_get_formatted_meta_data'), 10, 2);

        add_filter('manage_product_posts_columns', array($this, 'manage_products_columns'), 20, 1);
        add_action('manage_product_posts_custom_column', array($this, 'manage_products_column'), 10, 2);

        add_filter("manage_taxonomies_for_" . WCPA_POST_TYPE . "_columns", array($this, 'manage_taxonomies_for_list'), 10, 2);


        add_action('save_post', array($this, 'delete_transient'), 1);
        add_action('edited_term', array($this, 'delete_transient'));
        add_action('delete_term', array($this, 'delete_transient'));
        add_action('created_term', array($this, 'delete_transient'));

        // Plugin Deactivation Survey
        add_action('admin_footer', array($this, 'wcpa_deactivation_form'));

        WCPA_Form_Editor::instance();
        WCPA_Product_Meta::instance();
        WCPA_Settings::instance();
    }

    static function view($view, $data = array())
    {
        extract($data);
        include(plugin_dir_path(__FILE__) . 'views/' . $view . '.php');
    }

    /**
     *
     *
     * Ensures only one instance of WCPA is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main WCPA instance
     */
    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

    public function delete_transient($arg = false)
    {
        if ($arg) {
            in_array(get_post_type($arg), ['product', WCPA_POST_TYPE]) && delete_transient(WCPA_PRODUCTS_TRANSIENT_KEY);
        } else {
            delete_transient(WCPA_PRODUCTS_TRANSIENT_KEY);
        }

    }

    /**
     * Handling duplicate ajax action
     * @access  public
     * @since   4.3.3
     * @return  string
     */
    public function duplicate_form()
    {

        // Check the nonce
        check_ajax_referer('wcpa_duplicate_form', 'wcpa_nonce');

        // Get variables
        $original_id = sanitize_text_field($_POST['original_id']);


        global $wpdb;

        $_duplicate = get_post($original_id);

        if (!isset($_duplicate->post_type) || $_duplicate->post_type !== WCPA_POST_TYPE) {
            return false;
        }


        $duplicate['post_title'] = $_duplicate->post_title . ' ' . __('Copy', 'wcpa-text-domain');
        $duplicate['post_type'] = WCPA_POST_TYPE;

        $duplicate_id = wp_insert_post($duplicate);

        $settings = get_post_meta($original_id, WCPA_META_SETTINGS_KEY, true);
        update_post_meta($duplicate_id, WCPA_META_SETTINGS_KEY, $settings);

        $fb_meta = get_post_meta($original_id, WCPA_FORM_META_KEY, true);
        $fb_meta_obj = json_decode($fb_meta);
        $old_id = array();
        if ($fb_meta_obj && is_array($fb_meta_obj)) {
            foreach ($fb_meta_obj as $v) {
                if (isset($v->elementId)) {
                    $_tmp = $v->elementId;
                    $v->elementId = 'wcpa-' . sanitize_title($v->type) . '-' . uniqid();
                    $old_id[$_tmp] = $v->elementId;
                    //replace id in relation
                }
                if (isset($v->name)) {
                    $v->name = sanitize_title($v->type) . '-' . uniqid();
                }
            }
            foreach ($fb_meta_obj as $v) {
                if (isset($v->relations) && is_array($v->relations)) {
                    foreach ($v->relations as $rel) {
                        if (isset($rel->rules) && is_array($rel->rules)) {
                            foreach ($rel->rules as $rul) {
                                if (isset($rul->rules->cl_field) && isset($old_id[$rul->rules->cl_field])) {
                                    $rul->rules->cl_field = $old_id[$rul->rules->cl_field];
                                }
                            }
                        }
                    }
                }
            }
        }

        update_post_meta($duplicate_id, WCPA_FORM_META_KEY, wp_slash(json_encode($fb_meta_obj)));


        echo $duplicate_id;

        wp_die();
    }

    /**
     * Add duplicate form link
     * @access  public
     * @since   4.3.3
     * @return  string
     */
    public function post_row_actions($actions, $post)
    {
        // Check for your post type.
        if ($post->post_type == WCPA_POST_TYPE) {
            $ml = new WCPA_Ml();
            if ($ml->is_active() && !$ml->is_default_lan()) {
                return $actions;
            }
            $label = __('Duplicate Form', 'wcpa-text-domain');

            // Create a nonce & add an action
            $nonce = wp_create_nonce('wcpa_duplicate_form');
            $actions['duplicate_wcpa'] = '<a class="wcpa_duplicate_form" data-nonce="' . $nonce . '" href="#" data-postid="' . $post->ID . '">' . $label . '</a>';
        }


        return $actions;
    }

// End admin_enqueue_styles ()

    /**
     *    Create post type forms
     */
    public function register_type_forms()
    {

        $post_type = WCPA_POST_TYPE;
        $labels = array(
            'name' => 'Product Form',
            'singular_name' => 'Product Form',
            'name_admin_bar' => 'WCPA_Form',
            'add_new' => _x('Add New Form', $post_type, 'woo-custom-product-addons'),
            'add_new_item' => sprintf(__('Add New %s', 'woo-custom-product-addons'), 'Form'),
            'edit_item' => sprintf(__('Edit %s', 'woo-custom-product-addons'), 'Form'),
            'new_item' => sprintf(__('New %s', 'woo-custom-product-addons'), 'Form'),
            'all_items' => sprintf(__('Custom Product Addons', 'woo-custom-product-addons'), 'Form'),
            'view_item' => sprintf(__('View %s', 'woo-custom-product-addons'), 'Form'),
            'search_items' => sprintf(__('Search %s', 'woo-custom-product-addons'), 'Form'),
            'not_found' => sprintf(__('No %s Found', 'woo-custom-product-addons'), 'Form'),
            'not_found_in_trash' => sprintf(__('No %s Found In Trash', 'woo-custom-product-addons'), 'Form'),
            'parent_item_colon' => sprintf(__('Parent %s'), 'Form'),
            'menu_name' => 'Custome Product Options'
        );

        $args = array(
            'labels' => apply_filters($post_type . '_labels', $labels),
            'description' => '',
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=product',
            'show_in_nav_menus' => false,
            'query_var' => false,
            'can_export' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'rest_base' => $post_type,
            'hierarchical' => false,
            'show_in_rest' => false,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => array('title'),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-post',
            'taxonomies' => array('product_cat')
        );

        register_post_type($post_type, apply_filters($post_type . '_register_args', $args, $post_type));
    }

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles()
    {
        wp_register_style($this->_token . '-admin', esc_url($this->assets_url) . 'css/admin.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-admin');
    }

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts()
    {
        $form_builder = $this->_token . '-form-builder';
        wp_register_script($this->_token . '-backend', esc_url($this->assets_url) . 'js/backend.js', array('jquery'), $this->_version);
        wp_register_script($form_builder, esc_url($this->assets_url) . 'js/form-builder.min.js', array('jquery'), $this->_version);

        wp_enqueue_script('jquery');

        $screen = get_current_screen();
        if (isset($screen->id) && $screen->id === 'wcpa_pt_forms') {
            wp_enqueue_script($form_builder);
        }
        wp_enqueue_script($this->_token . '-backend');
        require_once('translations.php');
        wp_localize_script($form_builder, 'form_builder_i18n', $translations);
    }

    public function manage_taxonomies_for_list($tax, $post_type)
    {
        $taxonomies = get_object_taxonomies($post_type, 'object');
        return wp_filter_object_list($taxonomies, array(), 'and', 'name');
    }

    public function manage_products_columns($columns)
    {

        return array_merge(array_slice($columns, 0, -2, true), ['wcpa_forms' => __('Product Forms', 'woo-custom-product-addons')], array_slice($columns, -2, null, true));
    }

    public function manage_products_column($column_name, $post_id)
    {
        if ($column_name == 'wcpa_forms') {
            $forms = get_post_meta($post_id, WCPA_PRODUCT_META_KEY, true);
            $link = '';
            if (is_array($forms)) {
                foreach ($forms as $v) {
                    $link .= '<a href="' . get_edit_post_link($v) . '">' . get_the_title($v) . '</a>, ';
                }
            }
            echo trim($link, ', ');
        }
    }

    public function upgrader_process_complete($upgrader_object, $options)
    {
        // The path to our plugin's main file
        $our_plugin = plugin_basename($this->file);
        // If an update has taken place and the updated type is plugins and the plugins element exists
        if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
            // Iterate through the plugins being updated and check if ours is there
            foreach ($options['plugins'] as $plugin) {
                if ($plugin == $our_plugin) {
                    $migration = new WCPA_Migration();
                    if ($migration->check_has_to_migrate()) {
                        $response = $migration->version_migration();
                    }
                }
            }
        }
    }

    public function forms_table_content($column_name, $post_id)
    {
        if ($column_name == 'category') {
            $categories = wp_get_post_terms($post_id, 'product_cat', array('fields' => 'names'));
            echo implode(', ', $categories);
        }
    }

    /*
    * Deactivation form
    */
    public function wcpa_deactivation_form() {
        $currentScreen = get_current_screen();
        $screenID = $currentScreen->id;
        if ( $screenID == 'plugins' ) {
            $view = '<div id="aco-survey-form-wrap"><div id="aco-survey-form">
            <p>If you have a moment, please let us know why you are deactivating this plugin. All submissions are anonymous and we only use this feedback for improving our plugin.</p>
            <form method="POST">
                <input name="Plugin" type="hidden" placeholder="Plugin" value="'.WCPA_TOKEN.'" required>
                <input name="Date" type="hidden" placeholder="Date" value="'.date("m/d/Y").'" required>
                <input name="Website" type="hidden" placeholder="Website" value="'.get_site_url().'" required>
                <input name="Title" type="hidden" placeholder="Title" value="'.get_bloginfo( 'name' ).'" required>
                <input name="Version" type="hidden" placeholder="Version" value="'.WCPA_VERSION.'" required>
                <input type="radio" id="temporarily" name="Reason" value="I\'m only deactivating temporarily">
                <label for="temporarily">I\'m only deactivating temporarily</label><br>
                <input type="radio" id="notneeded" name="Reason" value="I no longer need the plugin">
                <label for="notneeded">I no longer need the plugin</label><br>
                <input type="radio" id="short" name="Reason" value="I only needed the plugin for a short period">
                <label for="short">I only needed the plugin for a short period</label><br>
                <input type="radio" id="better" name="Reason" value="I found a better plugin">
                <label for="better">I found a better plugin</label><br>
                <input type="radio" id="upgrade" name="Reason" value="Upgrading to PRO version">
                <label for="upgrade">Upgrading to PRO version</label><br>
                <input type="radio" id="requirement" name="Reason" value="Plugin doesn\'t meets my requirement">
                <label for="requirement">Plugin doesn\'t meets my requirement</label><br>
                <input type="radio" id="broke" name="Reason" value="Plugin broke my site">
                <label for="broke">Plugin broke my site</label><br>
                <input type="radio" id="stopped" name="Reason" value="Plugin suddenly stopped working">
                <label for="stopped">Plugin suddenly stopped working</label><br>
                <input type="radio" id="bug" name="Reason" value="I found a bug">
                <label for="bug">I found a bug</label><br>
                <input type="radio" id="other" name="Reason" value="Other">
                <label for="other">Other</label><br>
                <p id="aco-error"></p>
                <div class="aco-comments" style="display:none;">
                    <textarea type="text" name="Comments" placeholder="Please specify" rows="2"></textarea>
                    <p>For support queries <a href="https://support.acowebs.com/portal/en/newticket?departmentId=361181000000006907&layoutId=361181000000074011" target="_blank">Submit Ticket</a></p>
                </div>
                <button type="submit" class="aco_button" id="aco_deactivate">Submit & Deactivate</button>
                <a href="#" class="aco_button" id="aco_cancel">Cancel</a>
                <a href="#" class="aco_button" id="aco_skip">Skip & Deactivate</a>
            </form></div></div>';
            echo $view;
        } ?>
        <style>
            #aco-survey-form-wrap{ display: none;position: absolute;top: 0px;bottom: 0px;left: 0px;right: 0px;z-index: 10000;background: rgb(0 0 0 / 63%); } #aco-survey-form{ display:none;margin-top: 15px;position: fixed;text-align: left;width: 40%;max-width: 600px;z-index: 100;top: 50%;left: 50%;transform: translate(-50%, -50%);background: rgba(255,255,255,1);padding: 35px;border-radius: 6px;border: 2px solid #fff;font-size: 14px;line-height: 24px;outline: none;}#aco-survey-form p{font-size: 14px;line-height: 24px;padding-bottom:20px;margin: 0;} #aco-survey-form .aco_button { margin: 25px 5px 10px 0px; height: 42px;border-radius: 6px;background-color: #1eb5ff;border: none;padding: 0 36px;color: #fff;outline: none;cursor: pointer;font-size: 15px;font-weight: 600;letter-spacing: 0.1px;color: #ffffff;margin-left: 0 !important;position: relative;display: inline-block;text-decoration: none;line-height: 42px;} #aco-survey-form .aco_button#aco_deactivate{background: #fff;border: solid 1px rgba(88,115,149,0.5);color: #a3b2c5;} #aco-survey-form .aco_button#aco_skip{background: #fff;border: none;color: #a3b2c5;padding: 0px 15px;float:right;}#aco-survey-form .aco-comments{position: relative;}#aco-survey-form .aco-comments p{ position: absolute; top: -24px; right: 0px; font-size: 14px; padding: 0px; margin: 0px;} #aco-survey-form .aco-comments p a{text-decoration:none;}#aco-survey-form .aco-comments textarea{background: #fff;border: solid 1px rgba(88,115,149,0.5);width: 100%;line-height: 30px;resize:none;margin: 10px 0 0 0;} #aco-survey-form p#aco-error{margin-top: 10px;padding: 0px;font-size: 13px;color: #ea6464;}
        </style>
    <?php }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install()
    {
        $this->_log_version_number();
        $this->_update_settings();
        $migration = new WCPA_Migration();
        if ($migration->check_has_to_migrate()) {
            $response = $migration->version_migration();
        }

        $this->_protect_upload_dir();
    }

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number()
    {
        update_option($this->_token . '_version', $this->_version);
    }

    private function _update_settings()
    {
        $settings = get_option(WCPA_SETTINGS_KEY, array());
        if (!isset($settings['show_meta_in_cart'])) {
            $settings['show_meta_in_cart'] = true;
        }
        if (!isset($settings['show_meta_in_checkout'])) {
            $settings['show_meta_in_checkout'] = true;
        }
        if (!isset($settings['show_meta_in_order'])) {
            $settings['show_meta_in_order'] = true;
        }
        if (!isset($settings['form_loading_order_by_date'])) {
            $count_posts = wp_count_posts(array('post_type' => WCPA_POST_TYPE));
            if ($count_posts && isset($count_posts->publish) && $count_posts->publish>1) {
                $settings['form_loading_order_by_date'] = false;
            } else {
                $settings['form_loading_order_by_date'] = true;
            }
        }

        if (!isset($settings['hide_empty_data'])) {
            $count_posts = wp_count_posts(array('post_type' => WCPA_POST_TYPE));
            if ($count_posts && isset($count_posts->publish) && $count_posts->publish>1) {
                $settings['hide_empty_data'] = false;
            } else {
                $settings['hide_empty_data'] = true;
            }
        }

        update_option(WCPA_SETTINGS_KEY, $settings);
    }

    private function _protect_upload_dir()
    {
        $upload_dir = wp_upload_dir();

        $files = array(
            array(
                'base' => $upload_dir['basedir'] . '/' . WCPA_UPLOAD_DIR,
                'file' => '.htaccess',
                'content' => 'Options -Indexes' . "\n"
                    . ''
            )
        ,
            array(
                'base' => $upload_dir['basedir'] . '/' . WCPA_UPLOAD_DIR,
                'file' => 'index.php',
                'content' => '<?php ' . "\n"
                    . '// Silence is golden.'
            )
        );

        foreach ($files as $file) {


            if ((wp_mkdir_p($file['base'])) && (!file_exists(trailingslashit($file['base']) . $file['file']))) {  // If file not exist
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }

}
