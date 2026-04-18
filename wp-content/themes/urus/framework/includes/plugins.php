<?php
if( !class_exists('Urus_Plugins')){
    class Urus_Plugins{
        public static $plugins = array();
        public static $config = array();
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
            if( !class_exists('TGM_Plugin_Activation')){
                return false;
            }
            self::config();
            self::plugins();

            if( function_exists('tgmpa')){
                tgmpa( self::$plugins, self::$config );
            }
            add_filter( 'upgrader_pre_download', array(__CLASS__,'preUpgradeFilter'
            ), 999, 4 );
            add_filter('pre_set_site_transient_update_plugins',array(__CLASS__,'transient_update_plugins'),999);
            // State that initialization completed.
            if ( isset( $_GET['tgmpa-deactivate'] ) && 'deactivate-plugin' == $_GET['tgmpa-deactivate'] ) {
                check_admin_referer( 'tgmpa-deactivate', 'tgmpa-deactivate-nonce' );
                $plugins = TGM_Plugin_Activation::$instance->plugins;
                foreach ( $plugins as $plugin ) {
                    if ( $plugin['slug'] == $_GET['plugin'] ) {
                        self::deactivate_plugins( $plugin['file_path'] );
                    }
                }
            }
            if ( isset( $_GET['tgmpa-activate'] ) && 'activate-plugin' == $_GET['tgmpa-activate'] ) {
                check_admin_referer( 'tgmpa-activate', 'tgmpa-activate-nonce' );
                $plugins = TGM_Plugin_Activation::$instance->plugins;
                foreach ( $plugins as $plugin ) {
                    if ( isset( $_GET['plugin'] ) && $plugin['slug'] == $_GET['plugin'] ) {
                        self::activate_plugins( $plugin['file_path'] );
                    }
                }
            }
            self::$initialized = true;
        }
        public static function plugins(){
            self::$plugins = array(
                'familabcore'=>array(
                    'name'               => 'Familab Core',
                    'slug'               => 'familabcore',
                    'source'             => self::getPluginDownloadLink('familabcore'),
                    'required'           => true,
                    'force_activation'   => false,
                    'force_deactivation' => false,
                    'external_url'       => '',
                    'source_type'        => 'external',
                    'file_path'          => 'familabcore/familabcore.php',
                ),
                'js_composer'=>array(
                    'name'               => 'WPBakery Visual Composer',
                    'slug'               => 'js_composer',
                    'source'             => self::getPluginDownloadLink('js_composer'),
                    'required'           => true,
                    'force_activation'   => false,
                    'force_deactivation' => false,
                    'external_url'       => '',
                    'source_type'        => 'external',
                    'file_path'          => 'js_composer/js_composer.php',
                ),
                'revslider'=>array(
                    'name'               => 'Slider Revolution',
                    'slug'               => 'revslider',
                    'source'             => self::getPluginDownloadLink('revslider'),
                    'required'           => true,
                    'force_activation'   => false,
                    'force_deactivation' => false,
                    'external_url'       => '',
                    'source_type'        => 'external',
                    'file_path'          => 'revslider/revslider.php',
                ),
                'redux-framework'=>array(
                    'name'      => 'Redux Framework',
                    'slug'      => 'redux-framework',
                    'required'  => true,
                    'file_path' =>'redux-framework/redux-framework.php',
                    'source_type'        => 'repo', // Plugins On wordpress.org
                ),
                'woocommerce'=>array(
                    'name'      => 'WooCommerce',
                    'slug'      => 'woocommerce',
                    'required'  => true,
                    'source_type'        => 'repo', // Plugins On wordpress.org
                    'file_path'          => 'woocommerce/woocommerce.php',
                ),
                'meta-box'=>array(
                    'name'      => 'Meta Box – WordPress Custom Fields Framework',
                    'slug'      => 'meta-box',
                    'required'  => true,
                    'source_type'        => 'repo', // Plugins On wordpress.org
                    'file_path'          => 'meta-box/meta-box.php',
                ),
                'vafpress-post-formats-ui-develop'=>array(
                    'name'               => 'vafpress-post-formats-ui-develop',
                    'slug'               => 'vafpress-post-formats-ui-develop',
                    'source'             => self::getPluginDownloadLink('vafpress-post-formats-ui-develop'),
                    'required'           => false,
                    'force_activation'   => false,
                    'force_deactivation' => false,
                    'external_url'       => '',
                    'source_type'        => 'external',
                    'file_path'          => 'vafpress-post-formats-ui-develop/vp-post-formats-ui.php',
                ),
                'familab-instagram-shop' => array(
                    'name'               => 'Familab Instagram Shop',
                    'slug'               => 'familab-instagram-shop',
                    'source'             => self::getPluginDownloadLink('familab-instagram-shop'),
                    'required'           => false,
                    'force_activation'   => false,
                    'force_deactivation' => false,
                    'external_url'       => '',
                    'source_type'        => 'external',
                    'file_path'          => 'familab-instagram-shop/familab-instagram-shop.php',
                ),

                'contact-form-7'=>array(
                    'name'      => 'Contact Form 7',
                    'slug'      => 'contact-form-7',
                    'required'  => false,
                    'source_type'        => 'repo', // Plugins On wordpress.org
                    'file_path'          => 'contact-form-7/wp-contact-form-7.php',
                ),
                'yith-product-size-charts-for-woocommerce'=>array(
                    'name'      => 'YITH Product Size Charts for WooCommerce',
                    'slug'      => 'yith-product-size-charts-for-woocommerce',
                    'required'           => false,
                    'source_type'        => 'repo', // Plugins On wordpress.org
                    'file_path'          => 'yith-product-size-charts-for-woocommerce/init.php',
                ),
                'yith-woocommerce-frequently-bought-together'=>array(
                    'name'      => 'YITH WooCommerce Frequently Bought Together',
                    'slug'      => 'yith-woocommerce-frequently-bought-together',
                    'required'  => false,
                    'source_type'        => 'repo', // Plugins On wordpress.org
                    'file_path'          => 'yith-woocommerce-frequently-bought-together/init.php',
                ),
            );
        }

        public static function config(){
            self::$config =  array(
                'id'           => 'urus-tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
                'default_path' => '',                      // Default absolute path to bundled plugins.
                'menu'         => 'urus-install-plugins', // Menu slug.
                'parent_slug'  => 'urus-intro',            // Parent menu slug.
                'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
                'has_notices'  => true,                    // Show admin notices or not.
                'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
                'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
                'is_automatic' => true,                   // Automatically activate plugins after installation or not.
                'message'      => '',                      // Message to output right before the plugins table.
            );
        }
        public static function getPluginDownloadLink($slug){
            return URUS_API_URL.'/plugin/'.URUS_THEME_SLUG.'/'.$slug;
            //return get_template_directory().'/plugins/'.$slug.'.zip';
        }
        public static function plugin_action( $item ,$installed_plugins = array()) {
            if (empty($installed_plugins)){
                $installed_plugins        = get_plugins();
            }
            $item['sanitized_plugin'] = $item['name'];
            $actions                  = array();
            // We have a repo plugin
            if ( ! $item['version'] ) {
                $item['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );
            }
            if ( ! isset($installed_plugins[ $item['file_path'] ] ) ) {
                // Display install link
                $actions = sprintf( '<a href="%1$s" title="Install %2$s">%3$s</a>',
                    esc_url( wp_nonce_url( add_query_arg( array(
                        'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
                        'plugin'        => urlencode( $item['slug'] ),
                        'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
                        'plugin_source' => urlencode( $item['source'] ),
                        'tgmpa-install' => 'install-plugin',
                        'back_url' => true
                    ),
                        TGM_Plugin_Activation::$instance->get_tgmpa_url() ),
                        'tgmpa-install',
                        'tgmpa-nonce' ) ),
                    $item['sanitized_plugin'],esc_html__('Install','urus'));
            } elseif (!self::checkPlugin( $item['file_path']) ) {
                // Display activate link
                $actions = sprintf( '<a href="%1$s" title="Activate %2$s">%3$s</a>',
                    esc_url( add_query_arg( array(
                        'plugin'               => urlencode( $item['slug'] ),
                        'plugin_name'          => urlencode( $item['sanitized_plugin'] ),
                        'plugin_source'        => urlencode( $item['source'] ),
                        'tgmpa-activate'       => 'activate-plugin',
                        'tgmpa-activate-nonce' => wp_create_nonce( 'tgmpa-activate' ),
                    ),
                        admin_url( 'admin.php?page=urus-intro&tab=plugins' ) ) ),
                    $item['sanitized_plugin']
                    ,esc_html__('Activate','urus'));
            } elseif ( version_compare( $installed_plugins[ $item['file_path'] ]['Version'], $item['version'], '<' ) ) {
                // Display update link
                $actions = sprintf( '<a href="%1$s" title="Install %2$s">%3$s</a>',
                    wp_nonce_url( add_query_arg( array(
                        'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
                        'plugin'        => urlencode( $item['slug'] ),
                        'tgmpa-update'  => 'update-plugin',
                        'plugin_source' => urlencode( $item['source'] ),
                        'version'       => urlencode( $item['version'] ),
                    ),
                        TGM_Plugin_Activation::$instance->get_tgmpa_url() ),
                        'tgmpa-update',
                        'tgmpa-nonce' ),
                    $item['sanitized_plugin'] ,
                    esc_html__('Update','urus'));
            } elseif ( self::checkPlugin( $item['file_path'] ) ) {
                // Display deactivate link
                $actions = sprintf( '<a href="%1$s" title="Deactivate %2$s">%3$s</a>',
                    esc_url( add_query_arg( array(
                        'plugin'                 => urlencode( $item['slug'] ),
                        'plugin_name'            => urlencode( $item['sanitized_plugin'] ),
                        'plugin_source'          => urlencode( $item['source'] ),
                        'tgmpa-deactivate'       => 'deactivate-plugin',
                        'tgmpa-deactivate-nonce' => wp_create_nonce( 'tgmpa-deactivate' ),
                    ),
                    admin_url( 'admin.php?page=urus-intro&tab=plugins' ) ) ),
                    $item['sanitized_plugin'],
                    esc_html__('Deactivate','urus'));
            }
            return $actions;
        }
        public static function preUpgradeFilter( $reply, $package, $updater )
        {
            $check_list = self::plugins_fillter();
            $condition1 = false;
            $condition2 = false;
            $download_link = false;
            $slug = false;
            foreach ($check_list as $k => $pl){
                if (isset($updater->skin->plugin) && $updater->skin->plugin === $pl['file_path']){
                    $condition1 = true;
                    $slug = $k;
                    $download_link = self::getPluginDownloadLink($slug);
                }
                if (isset( $updater->skin->plugin_info ) && ($updater->skin->plugin_info['Name'] ===  $pl['name'])||($updater->skin->plugin_info['TextDomain']) === $k){
                    $condition2 = true;
                    $slug = $k;
                    $download_link = self::getPluginDownloadLink($slug);
                }
            }
            if (!$condition1 && !$condition2) {
                return $reply;
            }
            if (!$download_link || !$slug){
                return $reply;
            }
            $res = $updater->fs_connect( array( WP_CONTENT_DIR ) );
            if ( ! $res ) {
                return new WP_Error( 'no_credentials', esc_html__( "Error! Can't connect to filesystem", 'urus' ) );
            }

            $updater->strings['downloading_package_url'] = esc_html__( 'Getting download link...', 'urus' );
            $updater->skin->feedback( 'downloading_package_url' );

            $response = wp_remote_get( URUS_API_URL .'/plugins/'.URUS_THEME_SLUG, array( 'timeout' => 120 ) );

            if ( ! $response ) {
                return new WP_Error( 'no_credentials', esc_html__( 'Download link could not be retrieved', 'urus' ) );
            }

            $updater->strings['downloading_package'] = esc_html__( 'Downloading package...', 'urus' );
            $updater->skin->feedback( 'downloading_package' );

            $downloaded_archive = download_url($download_link );
            if ( is_wp_error( $downloaded_archive ) ) {
                return $downloaded_archive;
            }

            $plugin_directory_name = dirname( $check_list[$slug]['file_path'] );

            // WP will use same name for plugin directory as archive name, so we have to rename it
            if ( basename( $downloaded_archive, '.zip' ) !== $plugin_directory_name ) {
                $new_archive_name = dirname( $downloaded_archive ) . '/' . $plugin_directory_name . time() . '.zip';
                if ( rename( $downloaded_archive, $new_archive_name ) ) {
                    $downloaded_archive = $new_archive_name;
                }
            }
            return $downloaded_archive;
        }

        public static function transient_update_plugins($checked_data){
            //Comment out these two lines during testing.
            if (empty($checked_data->checked))
                return $checked_data;
            $plugin_version = self::getPluginsVersion();
            $installed_plugins = TGM_Plugin_Activation::$instance->get_plugins();
            $tgm_plugins = self::$plugins;
            foreach ($plugin_version as $plugin_slug => $host_version){
                if (isset($tgm_plugins[$plugin_slug])){
                    $file_path = $tgm_plugins[$plugin_slug]['file_path'];
                    if (isset($installed_plugins[$file_path])){
                        if (version_compare($host_version, $installed_plugins[$file_path]['Version'],'>')){
                            if ( empty( $checked_data->response[ $file_path ] ) ) {
                                $checked_data->response[ $file_path ] = new stdClass;
                            }
                            $checked_data->response[ $file_path ]->slug        = $plugin_slug;
                            $checked_data->response[ $file_path ]->plugin      = $file_path;
                            $checked_data->response[ $file_path ]->new_version = $host_version;
                            $checked_data->response[ $file_path ]->package     = $tgm_plugins[$plugin_slug]['source'];
                            if ( empty( $checked_data->response[ $file_path ]->url ) && ! empty( $tgm_plugins[$plugin_slug]['external_url'] ) ) {
                                $checked_data->response[ $file_path ]->url = $tgm_plugins[$plugin_slug]['external_url'];
                            }
                        }
                    }
                }
            }
            return $checked_data;
        }
        public static function checkPlugin($slug){
            $a_p = get_option('active_plugins', array());
            return in_array($slug,$a_p) || self::checkNetWordPlugin($slug);
        }
        Public static function checkNetWordPlugin($slug){
            if ( !is_multisite() )
                return false;
            if ( isset($ps[$slug]) )
                return true;
            return false;
        }
        public static function getBulkLink(){
            return  TGM_Plugin_Activation::$instance->get_tgmpa_url();
        }
        public static function getPluginsVersion(){
            $plugins_version = get_transient('familab_plugins_version');
            if ($plugins_version === false) {
            $request = wp_remote_get( URUS_API_URL .'/plugins/'.URUS_THEME_SLUG, array( 'timeout' => 120 ) );
            if ( is_wp_error( $request ) ) {
                return;
                }else{
                    $plugins_version = wp_remote_retrieve_body( $request );
                    set_transient('familab_plugins_version',$plugins_version,DAY_IN_SECONDS);
                    return json_decode( $plugins_version, true );
                }
            }else{
                if (is_null($plugins_version)){
                    return;
                }
            }
            return json_decode( $plugins_version, true );
        }
        public static function displayPluginsPage(){
            $tgm_plugins = TGM_Plugin_Activation::$instance->plugins;
            $installed_plugins = TGM_Plugin_Activation::$instance->get_plugins();
            $tgm_plugins_required = 0;
            $tgm_plugins_active = 0;
            $tgm_plugin_install = 0;
            $tgm_plugins_action = array();
            $required_str = '';
            $all_str = '';
            $active_str = '';

            ?>
            <div class="postbox tr-box">
                <h2><?php esc_html_e('Install Plugins','urus') ?></h2>
                <table class="wp-list-table widefat striped plugins">
                    <thead>
                    <tr>
                        <th><?php esc_html_e('Plugin','urus') ?></th>
                        <th><?php esc_html_e('Version','urus') ?></th>
                        <th><?php esc_html_e('Type','urus') ?></th>
                        <th><?php esc_html_e('Action','urus') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach ( $tgm_plugins as $tgm_plugin ) {
                        $tgm_plugins_action[ $tgm_plugin['slug']] = Urus_Plugins::plugin_action( $tgm_plugin,$installed_plugins );
                        if (isset($installed_plugins[$tgm_plugin['file_path']]['Version']) && !empty($installed_plugins[$tgm_plugin['file_path']]['Version']))
                            $installed_ver = $installed_plugins[$tgm_plugin['file_path']]['Version'];
                        else{
                            $installed_ver = '';
                        }
                        ?>
                        <tr>
                            <td>
                                <?php
                                if (( ! empty( $installed_plugins[$tgm_plugin['file_path']] ) )){
                                    if (!self::checkPlugin($tgm_plugin['file_path'])){
                                        $tgm_plugins_active++;
                                        $active_str .= ','.$tgm_plugin['slug'];
                                        echo '<span>' . $tgm_plugin['name'] . '</span>';
                                    }else{
                                        echo '<span class="actived">' . $tgm_plugin['name'] . '</span>';
                                    }
                                }else{
                                    if ($tgm_plugin['required']){
                                        $tgm_plugins_required++;
                                        $required_str .= ','.$tgm_plugin['slug'];
                                        $all_str .= ','.$tgm_plugin['slug'];;
                                    }else{
                                        $tgm_plugin_install++;
                                        $all_str .= ','.$tgm_plugin['slug'];;
                                    }
                                    echo '<span>' . $tgm_plugin['name'] . '</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($installed_ver != ''){
                                    echo sprintf(
                                        '<p>'. esc_html__( 'Installed version:', 'urus' ) . ' %1$s</p>',
                                        $installed_ver
                                    );
                                }
                                if ($tgm_plugin['version'] && version_compare($tgm_plugin['version'], $installed_ver)){
                                    echo sprintf(
                                        '<p>' . esc_html__( 'Available version:', 'urus' ) . ' %1$s</p>',
                                        $tgm_plugin['version']
                                    );
                                }
                                ?></td>
                            <td><?php echo( isset( $tgm_plugin['required'] ) && ( $tgm_plugin['required'] == true ) ? 'Required' : 'Recommended' ); ?></td>
                            <td>
                                <?php
                                    echo e_data($tgm_plugins_action[ $tgm_plugin['slug'] ]); ?>
                            </td>
                        </tr>
                        <?php
                    }
                    $required_str = ltrim($required_str,',');
                    $all_str = ltrim($all_str,',');
                    $active_str = ltrim($active_str,',');
                    ?>
                    </tbody>
                </table>
                <!--                                <label for=tr-plugin-action">Bulk action</label>-->
                <form id="tr-plugins-bulk" method="post" action="<?php echo esc_url(Urus_Plugins::getBulkLink()) ?>" data-all="<?php echo e_data($all_str) ?>" data-required="<?php echo e_data($required_str) ?>" data-active="<?php echo e_data($active_str) ?>">
                    <?php wp_nonce_field('bulk-plugins'); ?>
                    <input type="hidden" name="action"/>
                    <input type="hidden" name="tgmpa-page" value="<?php echo urlencode( TGM_Plugin_Activation::$instance->menu );?>">
                    <input type="hidden" name="plugin_status" value="all">
                    <input type="hidden" name="back_url" value="1">
                    <select id="tr-plugin-action">
                        <option value="">Chose action</option>
                        <option value="all" <?php if ($tgm_plugin_install < 1) echo 'disabled' ?>>Install All plugins</option>
                        <option value="required" <?php if ($tgm_plugins_required < 1) echo 'disabled' ?>>Install required only</option>
                        <option value="active_all" <?php if ($tgm_plugins_active < 1) echo 'disabled' ?>>Active All deactivated plugins</option>
                    </select>
                    <a  data-type="all" rel="noopener noreferrer" href="#" class="tr-button dark install_plugins disable"><?php esc_html_e('Apply','urus');?></a>
                </form>
            </div>
            <?php
        }

        public static function plugins_fillter(){
            $plugins = array(
                'js_composer' => array(
                    'name' => 'WPBakery Visual Composer',
                    'file_path' => 'js_composer/js_composer.php'
                )
            );
            return $plugins;
        }

        public static function deactivate_plugins( $plugins, $silent = false, $network_wide = null ) {
            if ( is_multisite() )
                $network_current = get_site_option( 'active_sitewide_plugins', array() );
            $current = get_option( 'active_plugins', array() );
            $do_blog = $do_network = false;

            foreach ( (array) $plugins as $plugin ) {
                $plugin = plugin_basename( trim( $plugin ) );
                if ( ! checkPlugin($plugin) )
                    continue;
                $network_deactivating = false !== $network_wide && checkNetWordPlugin( $plugin );
                if ( ! $silent ) {
                    /**
                     * Fires before a plugin is deactivated.
                     *
                     * If a plugin is silently deactivated (such as during an update),
                     * this hook does not fire.
                     *
                     * @since 2.9.0
                     *
                     * @param string $plugin               Path to the main plugin file from plugins directory.
                     * @param bool   $network_deactivating Whether the plugin is deactivated for all sites in the network
                     *                                     or just the current site. Multisite only. Default is false.
                     */
                    do_action( 'deactivate_plugin', $plugin, $network_deactivating );
                }

                if ( false !== $network_wide ) {
                    if ( checkNetWordPlugin( $plugin ) ) {
                        $do_network = true;
                        unset( $network_current[ $plugin ] );
                    } elseif ( $network_wide ) {
                        continue;
                    }
                }

                if ( true !== $network_wide ) {
                    $key = array_search( $plugin, $current );
                    if ( false !== $key ) {
                        $do_blog = true;
                        unset( $current[ $key ] );
                    }
                }

                if ( ! $silent ) {
                    /**
                     * Fires as a specific plugin is being deactivated.
                     *
                     * This hook is the "deactivation" hook used internally by register_deactivation_hook().
                     * The dynamic portion of the hook name, `$plugin`, refers to the plugin basename.
                     *
                     * If a plugin is silently deactivated (such as during an update), this hook does not fire.
                     *
                     * @since 2.0.0
                     *
                     * @param bool $network_deactivating Whether the plugin is deactivated for all sites in the network
                     *                                   or just the current site. Multisite only. Default is false.
                     */
                    do_action( "deactivate_{$plugin}", $network_deactivating );

                    /**
                     * Fires after a plugin is deactivated.
                     *
                     * If a plugin is silently deactivated (such as during an update),
                     * this hook does not fire.
                     *
                     * @since 2.9.0
                     *
                     * @param string $plugin               Path to the main plugin file from plugins directory.
                     * @param bool   $network_deactivating Whether the plugin is deactivated for all sites in the network.
                     *                                     or just the current site. Multisite only. Default false.
                     */
                    do_action( 'deactivated_plugin', $plugin, $network_deactivating );
                }
            }

            if ( $do_blog )
                update_option('active_plugins', $current);
            if ( $do_network )
                update_site_option( 'active_sitewide_plugins', $network_current );
        }
        public static function activate_plugins( $plugins, $redirect = '', $network_wide = false, $silent = false ) {
            if ( !is_array($plugins) )
                $plugins = array($plugins);
            $errors = array();
            foreach ( $plugins as $plugin ) {
                if ( !empty($redirect) )
                    $redirect = add_query_arg('plugin', $plugin, $redirect);
                $result = activate_plugin($plugin, $redirect, $network_wide, $silent);
                if ( is_wp_error($result) )
                    $errors[$plugin] = $result;
            }
            if ( !empty($errors) )
                return new WP_Error('plugins_invalid', esc_html__('One of the plugins is invalid.','urus'), $errors);
            return true;
        }
    }
}
