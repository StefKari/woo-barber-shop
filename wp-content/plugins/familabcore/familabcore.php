<?php
/*
 * Plugin Name: Familab Core
 * Plugin URI: https://familab.net/
 * Description: Core functions for WordPress theme
 * Author: Familab
 * Version: 1.4.7
 * Author URI: https://familab.net/
 * Text Domain: familabcore
 * Domain Path: /languages
 */
if (!defined('ABSPATH')) {
    exit; // disable direct access
}
require_once plugin_dir_path(__FILE__) . '/libraries/Mobile_Detect.php';
if (!class_exists('Familab_Core')) {
    class Familab_Core
    {
        protected $prefix = 'Familab_Core_';

        //protected $theme_info = array();
        public function __construct()
        {
            add_filter('extra_theme_headers', array($this, 'extra_theme_headers'));
            $this->setup_constants();
            $this->load_plugin_textdomain();
            // Register class autoloader.
            spl_autoload_register(array($this, 'autoload'));
            if (class_exists('WooCommerce')) {
                new Familab_Core_Variation_Swatches();
            }
            add_filter('familab_server_environment', array($this, 'server_environment'));
            new Familab_Core_Megamenu();
            if (is_admin()) {
                if (FAMILAB_MARKET != 'TemplateMonster') {
                    new Familab_Core_Import();
                }
                new Familab_Core_Market_Check();
                new Familab_Core_Theme_Update();
            }
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts_and_styles'));
            add_action('widgets_init', array($this, 'register_widgets'));
        }

        /**
         * Method to autoload class declaration file.
         *
         * @param string $class_name Name of class to load declaration file for.
         *
         * @return  mixed
         */
        public static function enqueue_admin_scripts_and_styles()
        {
            wp_enqueue_style('core_admin', FAMILAB_CORE_PLUGIN_URL . 'assets/css/core_admin.css', array(), '1.0.0');
        }

        public function extra_theme_headers($headers)
        {
            $headers[] = 'Market';
            $headers[] = 'PrivateKey';
            return $headers;
        }

        public function server_environment()
        {
            ?>
            <table class="wp-list-table widefat striped system-tb">
                <tbody>
                <tr>
                    <td class="title"><?php _e('Server Info', 'familabcore'); ?></td>
                    <td><?php echo esc_html($_SERVER['SERVER_SOFTWARE']); ?></td>
                    <td>
                        <?php _e('Information about the web server that is currently hosting your site.', 'familabcore') ?>
                    </td>
                </tr>
                <tr>
                    <td class="title"><?php _e('PHP Version', 'familabcore'); ?></td>
                    <td><?php if (function_exists('phpversion')) {
                            $php_version = esc_html(phpversion());
                            if (version_compare($php_version, '5.6', '<')) {
                                echo '<mark class="error">' . FAMILAB_THEME_NAME . esc_html__(' requires PHP version 5.6 or greater. Please contact your hosting provider to upgrade PHP version.', 'familabcore') . '</mark>';
                            } else {
                                echo e_data($php_version);
                            }
                        }
                        ?></td>
                    <td>
                        <?php _e('The version of PHP installed on your hosting server.', 'familabcore') ?>
                    </td>
                </tr>
                <?php if (function_exists('ini_get')) : ?>
                    <tr>
                        <td class="title"><?php _e('PHP Post Max Size', 'familabcore'); ?></td>
                        <td><?php echo ini_get('post_max_size'); ?></td>
                        <td>
                            <?php _e('The largest filesize that can be contained in one post.', 'familabcore') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="title"><?php _e('PHP Time Limit', 'familabcore'); ?></td>
                        <td><?php
                            $time_limit = ini_get('max_execution_time');
                            if ($time_limit > 0 && $time_limit < 180) {
                                echo '<mark class="error">' . sprintf(__('%s - We recommend setting max execution time to at least 180. See: <a href="%s" target="_blank">Increasing max execution to PHP</a>', 'familabcore'), $time_limit, 'http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded') . '</mark>';
                            } else {
                                echo '<mark class="yes">' . $time_limit . '</mark>';
                            }
                            ?></td>
                        <td>
                            <?php _e('The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'familabcore') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="title"><?php _e('PHP Max Input Vars', 'familabcore'); ?></td>
                        <td><?php
                            $max_input_vars = ini_get('max_input_vars');
                            if ($max_input_vars < 5000) {
                                echo '<mark class="error">' . sprintf(__('%s - We recommend setting max input vars to at least 5000, this limitation will truncate POST data such as menus.', 'familabcore'), $max_input_vars) . '</mark>';
                            } else {
                                echo '<mark class="yes">' . $max_input_vars . '</mark>';
                            }
                            ?></td>
                        <td>
                            <?php _e('The maximum number of variables your server can use for a single function to avoid overloads.', 'familabcore') ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="title"><?php _e('MySQL Version', 'familabcore'); ?></td>
                    <td>
                        <?php
                        global $wpdb;
                        echo e_data($wpdb->db_version());
                        ?>
                    </td>
                    <td>
                        <?php _e('The version of MySQL installed on your hosting server.', 'familabcore') ?>
                    </td>
                </tr>
                <tr>
                    <td class="title"><?php _e('Max Upload Size', 'familabcore'); ?></td>
                    <td><?php echo size_format(wp_max_upload_size()); ?></td>
                    <td>
                        <?php _e('The largest filesize that can be uploaded to your WordPress installation.', 'familabcore') ?>
                    </td>
                </tr>
                <tr>
                    <td class="title"><?php _e('Default Timezone is UTC', 'familabcore'); ?></td>
                    <td><?php
                        $default_timezone = date_default_timezone_get();
                        if ('UTC' !== $default_timezone) {
                            echo '<mark class="error">&#10005; ' . sprintf(__('Default timezone is %s - it should be UTC', 'familabcore'), $default_timezone) . '</mark>';
                        } else {
                            echo '<mark class="yes">&#10004;</mark>';
                        } ?>
                    </td>
                    <td>
                        <?php _e('The default timezone for your server.', 'familabcore') ?>
                    </td>
                </tr>
                <tr>
                    <td class="title"><?php _e('fsockopen/cURL', 'familabcore'); ?></td>
                    <td><?php
                        if (function_exists('fsockopen') || function_exists('curl_init')) {
                            echo '<mark class="yes">&#10004;</mark>';
                        } else {
                            echo '<mark class="error">&#10005; ' . _e('Your server does not have fsockopen or cURL enabled. Please contact your hosting provider to enable it.', 'familabcore') . '</mark>';
                        } ?>
                    </td>
                    <td>
                        <?php _e('Plugins may use it when communicating with remote services.', 'familabcore') ?>
                    </td>
                </tr>
                <tr>
                    <td class="title"><?php _e('DOMDocument', 'familabcore'); ?></td>
                    <td><?php
                        if (class_exists('DOMDocument')) {
                            echo '<mark class="yes">&#10004;</mark>';
                        } else {
                            echo '<mark class="error">&#10005; ' . sprintf(__('Your server does not have <a href="%s">the DOM extension</a> class enabled. Please contact your hosting provider to enable it.', 'familabcore'), 'http://php.net/manual/en/intro.dom.php') . '</mark>';
                        } ?>
                    </td>
                    <td>
                        <?php _e('WordPress Importer use DOMDocument.', 'familabcore') ?>
                    </td>
                </tr>
                <tr>
                    <td class="title"><?php _e('XMLReader', 'familabcore'); ?></td>
                    <td><?php
                        if (class_exists('XMLReader')) {
                            echo '<mark class="yes">&#10004;</mark>';
                        } else {
                            echo '<mark class="error">&#10005; ' . sprintf(__('Your server does not have <a href="%s">the XMLReader extension</a> class enabled. Please contact your hosting provider to enable it.', 'familabcore'), 'http://php.net/manual/en/intro.xmlreader.php') . '</mark>';
                        } ?>
                    </td>
                    <td>
                        <?php _e('WordPress Importer use XMLReader.', 'familabcore') ?>
                    </td>
                </tr>
                <tr>
                    <td class="title"><?php _e('Remote Get', 'familabcore'); ?></td>
                    <td><?php
                        $response = wp_remote_get(FAMILAB_CORE_PLUGIN_URL . '/assets/remote_test.json',array('sslverify' => FALSE));
                        if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300) {
                            echo '<mark class="yes">&#10004;</mark>';
                        } else {
                            echo '<mark class="error">&#10005; ' . _e(' WordPress function <a href="https://codex.wordpress.org/Function_Reference/wp_remote_get">wp_remote_get()</a> test failed. Please contact your hosting provider to enable it.', 'familabcore') . '</mark>';
                        } ?>
                    </td>
                    <td>
                        <?php _e('Retrieve the raw response from the HTTP request using the GET method. (wp_remote_get function)', 'familabcore') ?>
                    </td>
                </tr>
                <tr>
                    <td class="title"><?php _e('Sever status', 'familabcore'); ?></td>
                    <td><?php
                        $check_familab_remote = get_transient('check_familab_remote');
                        if ($check_familab_remote === false) {
                            $response = wp_remote_get(FAMILAB_API_URL . '/changelog/' . FAMILAB_THEME_SLUG, array('timeout' => 120));
                            if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300) {
                                set_transient('check_familab_remote', 'yes', DAY_IN_SECONDS);
                                echo '<mark class="yes">&#10004;</mark>';
                            } else {
                                set_transient('check_familab_remote', 'no', DAY_IN_SECONDS);
                                echo '<mark class="error">&#10005; ' . _e(' WordPress function <a href="https://codex.wordpress.org/Function_Reference/wp_remote_get">wp_remote_get()</a> test failed. Please contact your hosting provider to enable it.', 'familabcore') . '</mark>';
                            }
                        } else {
                            if ($check_familab_remote == 'yes') {
                                echo '<mark class="yes">&#10004;</mark>';
                            } else {
                                echo '<mark class="error">&#10005; ' . _e(' WordPress function <a href="https://codex.wordpress.org/Function_Reference/wp_remote_get">wp_remote_get()</a> test failed. Please contact your hosting provider to enable it.', 'familabcore') . '</mark>';
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php _e('Retrieve the raw response from the HTTP request using the GET method. (wp_remote_get function)', 'familabcore') ?>
                    </td>
                </tr>
                </tbody>
            </table>
            </br>
            <a href="<?php echo esc_url(admin_url('admin.php?page=' . FAMILAB_THEME_SLUG . '-intro&tab=system&refresh_data=1')) ?>"><?php esc_html_e('Refresh Theme Info', 'familabcore') ?></a>
            <?php
        }

        public function autoload($class_name)
        {
            // Verify class prefix.
            if (0 !== strpos($class_name, $this->prefix)) {
                return false;
            }
            // Generate file path from class name.
            $base = plugin_dir_path(__FILE__) . '/includes/';
            $path = strtolower(str_replace('_', '/', substr($class_name, strlen($this->prefix))));
            // Check if class file exists.
            $standard = $path . '.php';
            $alternative = $path . '/' . current(array_slice(explode('/', str_replace('\\', '/', $path)), -1)) . '.php';
            while (true) {
                // Check if file exists in standard path.
                if (@is_file($base . $standard)) {
                    $exists = $standard;
                    break;
                }
                // Check if file exists in alternative path.
                if (@is_file($base . $alternative)) {
                    $exists = $alternative;
                    break;
                }
                // If there is no more alternative file, quit the loop.
                if (false === strrpos($standard, '/') || 0 === strrpos($standard, '/')) {
                    break;
                }
                // Generate more alternative files.
                $standard = preg_replace('#/([^/]+)$#', '-\\1', $standard);
                $alternative = implode('/', array_slice(explode('/', str_replace('\\', '/', $standard)), 0, -1)) . '/' . substr(current(array_slice(explode('/', str_replace('\\', '/', $standard)), -1)), 0, -4) . '/' . current(array_slice(explode('/', str_replace('\\', '/', $standard)), -1));
            }
            // Include class declaration file if exists.
            if (isset($exists)) {
                return include_once $base . $exists;
            }

            return false;
        }

        public function setup_constants()
        {
            $theme = wp_get_theme();
            if (!empty($theme['Template'])) {
                $theme = wp_get_theme($theme['Template']);
            }
            $market = $theme->get('Market');
            if (!$market) {
                $market = 'tf';
            }
            if (!defined('DS')) {
                define('DS', DIRECTORY_SEPARATOR);
            }
            define('FAMILAB_CORE_SITE_URI', site_url());
            define('FAMILAB_CORE_PLUGIN_URL', plugin_dir_url(__FILE__));
            define('FAMILAB_CORE_PLUGIN_DIR', dirname(__FILE__));
            if (!defined('FAMILAB_THEME_NAME')) {
                define('FAMILAB_THEME_NAME', $theme['Name']);
            }
            if (!defined('FAMILAB_THEME_SLUG')) {
                define('FAMILAB_THEME_SLUG', $theme['Template']);
            }
            if (!defined('FAMILAB_THEME_VERSION')) {
                define('FAMILAB_THEME_VERSION', $theme['Version']);
            }
            if (!defined('FAMILAB_THEME_DIR')) {
                define('FAMILAB_THEME_DIR', get_template_directory());
            }
            if (!defined('FAMILAB_THEME_URI')) {
                define('FAMILAB_THEME_URI', get_template_directory_uri());
            }
            if (!defined('FAMILAB_API_URL')) {
                define('FAMILAB_API_URL', 'https://api.familab.net');
            }
            if (!defined('FAMILAB_DOC_URL')) {
                define('FAMILAB_DOC_URL', 'https://docs.familab.net');
            }
            if (!defined('FAMILAB_CORE_VERSION')) {
                define('FAMILAB_CORE_VERSION', '1.0');
            }
            if (!defined('FAMILAB_MARKET')) {
                define('FAMILAB_MARKET', $market);
            }
            if (!defined('FAMILAB_ADMIN_IMAGES')) {
                define('FAMILAB_ADMIN_IMAGES', FAMILAB_THEME_URI . '/assets/images/admin');
            }
            if ($market == 'TemplateMonster') {
                $lic = get_option('familab_license_key' . FAMILAB_THEME_SLUG);
                $key = $theme->get('PrivateKey');
                if (!$lic && $key) {
                    update_option('familab_license_key' . FAMILAB_THEME_SLUG, $key);
                }
            }
        }

        public function register_widgets()
        {
            $widgets = apply_filters('familab_widgets', array());
            if ($widgets) {
                foreach ($widgets as $class => $w_list) {
                    if ($class == 'no_required') {
                        $this->add_widget($w_list);
                    } else {
                        if (class_exists($class)) {
                            $this->add_widget($w_list);
                        }
                    }
                }
            }
        }

        public function add_widget($widgets = array())
        {
            foreach ($widgets as $w) {
                register_widget($w);
            }
        }

        public static function RequestChangeLog()
        {
            $theme_changelog = get_transient('theme_changelog_' . FAMILAB_THEME_SLUG);
            if ($theme_changelog === false) {
                $parse = parse_url(get_site_url());
                $back_uri = $parse['host'];
                //new-changelog
                $request = wp_remote_get(FAMILAB_API_URL . '/new-changelog/' . FAMILAB_THEME_SLUG.'/'.$back_uri, array('timeout' => 120));
                if (is_wp_error($request)) {
                    set_transient('theme_changelog_' . FAMILAB_THEME_SLUG, null, DAY_IN_SECONDS);
                    return;
                } else {
                    $theme_changelog = wp_remote_retrieve_body($request);
                    set_transient('theme_changelog_' . FAMILAB_THEME_SLUG, $theme_changelog, DAY_IN_SECONDS);
                    return json_decode($theme_changelog, true);
                }
            } else {
                if (is_null($theme_changelog)) {
                    return;
                }
            }
            return json_decode($theme_changelog, true);
        }

        public static function theme_version()
        {
            $theme_version = get_transient('theme_version_' . FAMILAB_THEME_SLUG);
            if ($theme_version === false) {
                $new_vesion = 0;
                $updates = self::RequestChangeLog();
                if (is_array($updates)) {
                    foreach ($updates as $key => $val) {
                        if (version_compare($key, FAMILAB_THEME_VERSION) == 1) {
                            $new_vesion = array();
                            $new_vesion['new_version'] = $key;
                            $new_vesion['time'] = $val['time'];
                            break;
                        }
                    }
                }
                set_transient('theme_version_' . FAMILAB_THEME_SLUG, $new_vesion, DAY_IN_SECONDS);
            } else {
                $new_vesion = $theme_version;
            }
            return $new_vesion;
        }

        public static function theme_has_update()
        {
            $theme_version = self::theme_version();
            if (isset($theme_version['new_version']) && version_compare($theme_version['new_version'], FAMILAB_THEME_VERSION, '>')) {
                return true;
            } else {
                return false;
            }
        }

        public static function hexToRgb($hex, $alpha = false)
        {
            $hex = str_replace('#', '', $hex);
            $length = strlen($hex);
            $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
            $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
            $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
            if ($alpha) {
                $rgb['a'] = $alpha;
            }
            return $rgb;
        }

        public static function lumdiff($R1, $G1, $B1, $R2, $G2, $B2)
        {
            $L1 = 0.2126 * pow($R1 / 255, 2.2) +
                0.7152 * pow($G1 / 255, 2.2) +
                0.0722 * pow($B1 / 255, 2.2);

            $L2 = 0.2126 * pow($R2 / 255, 2.2) +
                0.7152 * pow($G2 / 255, 2.2) +
                0.0722 * pow($B2 / 255, 2.2);

            if ($L1 > $L2) {
                return ($L1 + 0.05) / ($L2 + 0.05);
            } else {
                return ($L2 + 0.05) / ($L1 + 0.05);
            }
        }

        public static function check_request_by_pjax()
        {
            if (isset($_SERVER["HTTP_X_PJAX"])) {
                return true;
            }
            return false;
        }

        public function load_plugin_textdomain()
        {
            $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
            $locale = apply_filters('plugin_locale', $locale, 'familabcore');
            unload_textdomain('familabcore');
            load_textdomain('familabcore', WP_LANG_DIR . '/familabcore/familabcore-' . $locale . '.mo');
            load_plugin_textdomain('familabcore', false, plugin_basename(dirname(__FILE__)) . '/languages');
        }
    }

    function initialize()
    {
        $familab_core = new Familab_Core();
    }

    add_action('plugins_loaded', 'initialize', 9);
}
