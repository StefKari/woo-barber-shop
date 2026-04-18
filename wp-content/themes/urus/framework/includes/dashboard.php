<?php
if (!class_exists('Urus_Dashboard')) {
    class  Urus_Dashboard
    {
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;
        public static $info = array();

        /**
         * Initialize pluggable functions for Visual Composer.
         *
         * @return  void
         */
        public static function initialize()
        {
            // Do nothing if pluggable functions already initialized.
            if (self::$initialized) {
                return;
            }
            self::$info['desc'] = sprintf('%s<br/>%s', esc_html__('Urus is a modern, clean and professional WooCommerce Wordpress Theme, It is fully', 'urus'), esc_html__('responsive, it looks stunning on all types of screens and devices.', 'urus'));
            self::$info['welcome'] = sprintf('%s<br/>%s', esc_html__('Thank you for choosing Urus. This is a great theme for any e-commerce purpose or simply blogging, news.', 'urus'), esc_html__('You can easily customize your website without the knowledge of code.', 'urus'));
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'), 999);
            add_filter('theme_infomation', array(__CLASS__, 'theme_infomation'), 999);
            add_filter('familab_theme_id', array(__CLASS__, 'familab_theme_id'), 9999);
            add_action('after_switch_theme', array(__CLASS__, 'after_switch_theme'));
            add_action('admin_init', array(__CLASS__, 'redirect_welcome_page'));
            // State that initialization completed.
            self::$initialized = true;
        }

        public static function theme_infomation()
        {
            return self::$info;
        }

        public static function familab_theme_id()
        {
            return '23782046';
        }

        public static function enqueue_scripts()
        {
            $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
            if ($page == URUS_THEME_SLUG . '-intro') {
                wp_enqueue_style('urus-admin', URUS_THEME_URI . 'assets/css/admin/familab_admin.css');
                wp_enqueue_style('bootstrap-grid', URUS_THEME_URI . 'assets/3rd-party/bootstrap/bootstrap-grid.min.css');
                wp_enqueue_script('urus-admin', URUS_THEME_URI . 'assets/js/admin/familab_admin.js');
            }
            wp_enqueue_style('urus-admin2', URUS_THEME_URI . 'assets/css/admin/admin.css');
        }

        public static function html()
        {
            if (isset($_GET['refresh_data']) && $_GET['refresh_data']) {
                delete_transient('theme_changelog_' . URUS_THEME_SLUG);
                delete_transient('check_license' . URUS_THEME_SLUG);
                delete_option('familab_plugins_version');
                set_site_transient('update_themes', null);
            }
            $info = self::$info;
            $has_update = false;
            $transient_update_themes = get_option('_site_transient_update_themes');
            if (isset($transient_update_themes->response[URUS_THEME_SLUG]['new_version'])) {
                $theme_version = $transient_update_themes->response[URUS_THEME_SLUG]['new_version'];
            } elseif (isset($transient_update_themes->checked[URUS_THEME_SLUG])) {
                $theme_version = $transient_update_themes->checked[URUS_THEME_SLUG];
            } else {
                $requested_ver =  Familab_Core::theme_version();
                if (isset($requested_ver['new_version']))
                    $theme_version = $requested_ver['new_version'];
                else
                $theme_version = URUS_THEME_VERSION;
            }
            if (class_exists('Familab_Core')){
                $has_update =  Familab_Core::theme_has_update();
            } elseif (version_compare($theme_version, URUS_THEME_VERSION) == 1) {
                $has_update = true;
            }
            $tab_active = isset($_GET['tab']) ? $_GET['tab'] : '';
            $familab_plugin_actived = true;
            if (!class_exists('Familab_Core')) {
                $familab_plugin_actived = false;
            }
            ?>
            <div class="container-fluid tr-container import-sample-data-wrap">
                <div class="dashboard-head text-center hidden">
                    <div class="familab-core-theme-thumb">
                        <img class="dashboard-logo" src="<?php echo esc_url(URUS_IMAGES . '/logo.svg') ?>">
                    </div>
                    <div class="theme-desc">
                        <?php print_r($info['desc']); ?>
                    </div>
                </div>
                <div id="tr-tabs-container">
                    <ul class="nav-tab-wrapper">
                        <li class="tr-tab <?php if ($tab_active == '' || $tab_active == 'welcome') {
                            echo 'active';
                        } ?>" data-tab="welcome"><?php esc_html_e('Welcome', 'urus'); ?></li>
                        <li class="tr-tab <?php if ($tab_active == 'update') {
                            echo 'active';
                        } ?>" data-tab="update"><?php esc_html_e('License', 'urus'); ?></li>
                        <li class="tr-tab <?php if ($tab_active == 'plugins') {
                            echo 'active';
                        } ?>" data-tab="plugins"><?php esc_html_e('Plugins', 'urus'); ?></li>
                        <li class="tr-tab <?php if ($tab_active == 'import') {
                            echo 'active';
                        } ?><?php if (!$familab_plugin_actived) {
                            echo ' disabled';
                        } ?>" data-tab="import"><?php esc_html_e('Import data', 'urus'); ?></li>
                        <li class="tr-tab <?php if ($tab_active == 'support') {
                            echo 'active';
                        } ?>" data-tab="support"><?php esc_html_e('Support', 'urus'); ?></li>
                        <li class="tr-tab <?php if ($tab_active == 'system') {
                            echo 'active';
                        } ?>" data-tab="system"><?php esc_html_e('System', 'urus'); ?></li>
                    </ul>
                    <div class="tab-content tr-tab-content">
                        <div id="welcome" class="tab-pane <?php if ($tab_active == '' || $tab_active == 'welcome') {
                            echo 'active';
                        } ?>" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="postbox tr-box">
                                        <h1><?php esc_html_e('Welcome to', 'urus');
                                            echo ' ' . URUS_THEME_NAME . ' ' . URUS_THEME_VERSION; ?></h1>
                                        <div class="welcome-text">
                                            <?php echo e_data($info['welcome']); ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-8 col-lg-12">

                                                <div class="text-center">
                                                    <img class="theme_demo"
                                                         src="<?php echo esc_url(URUS_ADMIN_IMAGES . '/theme_demo.png') ?>">
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-12">
                                                <div class="welcome-panel tr-panel">
                                                    <h3><?php esc_html_e('Theme update', 'urus'); ?></h3>
                                                    <div class="welcome-icon dashicons-megaphone">
                                                        <div class="version-info">
                                                            <div class="vesion-title"><?php esc_html_e('Installed Version', 'urus'); ?></div>
                                                            <div class="versions"><?php echo URUS_THEME_VERSION ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="welcome-icon dashicons-cloud">
                                                        <div class="version-info">
                                                            <?php if (isset($theme_version) && $has_update) { ?>
                                                                <div class="vesion-title"><?php esc_html_e('Latest Available Version', 'urus'); ?></div>
                                                                <div class="versions"><?php echo e_data($theme_version) ?></div>
                                                            <?php } else { ?>
                                                                <div class="vesion-title"><?php esc_html_e('Your theme is up to date!', 'urus'); ?></div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <?php if ($has_update) { ?>
                                                        <div class="update-info">
                                                            <?php esc_html_e('The latest version of this theme', 'urus'); ?>
                                                            <br>
                                                            <?php esc_html_e('is available, ', 'urus'); ?>
                                                            <p>
                                                                <a class="tr-tab-ex show-update tr-button" href="#"
                                                                   data-tab="update"><?php esc_html_e('update today!', 'urus'); ?></a>
                                                            </p>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="welcome-panel-content">
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-12">
                                                <div class="postbox tr-box">
                                                    <div class="welcome-panel">
                                                        <h3><?php esc_html_e('Quick Setings', 'urus'); ?></h3>
                                                        <ul>
                                                            <li>
                                                                <a class="welcome-icon dashicons-admin-plugins tr-tab-ex"
                                                                   href="#"
                                                                   data-tab="plugins"><?php esc_html_e('Install Required Plugins', 'urus'); ?></a>
                                                            </li>
                                                            <li><a class="welcome-icon
        dashicons-edit tr-tab-ex <?php if (!$familab_plugin_actived) {
                                                                    echo ' disabled';
                                                                } ?>" href="#"
                                                                   data-tab="import"><?php esc_html_e('Install Demo Content', 'urus'); ?></a>
                                                            </li>
                                                            <li><a class="welcome-icon
        dashicons-admin-generic <?php if (!$familab_plugin_actived) {
                                                                    echo ' disabled';
                                                                } ?>"
                                                                   href="<?php echo esc_url(admin_url('admin.php?page=urus_options')); ?>"><?php esc_html_e('Theme Options', 'urus'); ?></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-12">
                                                <div class="postbox tr-box">
                                                    <div class="welcome-panel">
                                                        <h3><?php esc_html_e('Support', 'urus'); ?></h3>
                                                        <ul>
                                                            <li><a class="welcome-icon dashicons-media-document"
                                                                   target="_blank"
                                                                   href="https://docs.familab.net/urus"><?php esc_html_e('Online Documentation', 'urus'); ?></a>
                                                            </li>
                                                            <li><a class="welcome-icon dashicons-editor-ol"
                                                                   target="_blank"
                                                                   href="https://support.familab.net/knowledgebase/"><?php esc_html_e('FAQs', 'urus'); ?></a>
                                                            </li>
                                                            <li><a class="welcome-icon dashicons-editor-help"
                                                                   target="_blank"
                                                                   href="https://support.familab.net/submit-ticket/"><?php esc_html_e('Request Support', 'urus'); ?></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="changelog">
                                        <?php
                                        $changelog = Urus::RequestChangeLog();
                                        if (is_array($changelog) && count($changelog) > 0) {
                                            $nb_ver = 0;
                                            ?>
                                            <table class="wp-list-table widefat striped changelogs">
                                                <thead>
                                                <th>Time</th>
                                                <th>Version</th>
                                                <th>Description</th>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($changelog as $ver => $l) {
                                                    $nb_ver++;
                                                    if ($nb_ver > 6){
                                                        break;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?php echo e_data($l['time']); ?></td>
                                                        <td><?php echo e_data($ver); ?></td>
                                                        <td><?php echo e_data($l['desc']); ?></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="support" class="tab-pane <?php if ($tab_active == 'support') {
                            echo 'active';
                        } ?>" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-4 col-md-12">
                                    <div class="content-box welcome-panel">
                                        <h2 class="welcome-icon dashicons-media-document"><?php esc_html_e('Documentation', 'urus'); ?></h2>
                                        <p><?php esc_html_e('Here is our user guide for Urus, including basic setup steps, as well as Urus features and elements for your reference.', 'urus'); ?></p>
                                        <a target="_blank" rel="noopener noreferrer" href="http://docs.familab.net/urus"
                                           class="tr-button dark"><?php esc_html_e('Read Documentation', 'urus'); ?></a>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    <div class="content-box welcome-panel">
                                        <h2 class="welcome-icon dashicons-video-alt3"><?php esc_html_e('Video Tutorials', 'urus'); ?></h2>
                                        <p class=""><?php esc_html_e('Video tutorials is the great way to show you how to setup Urus theme, make sure that the feature works as it\'s designed.', 'urus'); ?></p>
                                        <a target="_blank" rel="noopener noreferrer"
                                           href="https://www.youtube.com/channel/UCAH90EWu2pX3HzrtFP0zIYQ"
                                           class="tr-button dark"><?php esc_html_e('See Video', 'urus'); ?></a>
                                        <div class="ribbon"><span><?php esc_html_e('Coming soon', 'urus'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    <div class="content-box welcome-panel">
                                        <h2 class="welcome-icon
dashicons-format-status"><?php esc_html_e('Forum', 'urus'); ?></h2>
                                        <p><?php esc_html_e('Can\'t find the solution on documentation? We\'re here to help, even on weekend. Just click here to start 1on1 chatting with us!', 'urus'); ?></p>
                                        <a target="_blank" rel="noopener noreferrer"
                                           href="https://support.familab.net/submit-ticket/"
                                           class="tr-button dark"><?php esc_html_e('Request Support', 'urus'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="update" class="tab-pane <?php if ($tab_active == 'update') {
                            echo 'active';
                        } ?>" role="tabpanel">
                            <?php
                            if (class_exists('Familab_Core_Market_Check')) {
                                Familab_Core_Market_Check::license_form();
                            }
                            ?>
                        </div>
                        <div id="plugins" class="tab-pane <?php if ($tab_active == 'plugins') {
                            echo 'active';
                        } ?>" role="tabpanel">
                            <?php echo Urus_Plugins::displayPluginsPage(); ?>
                        </div>
                        <div id="system" class="tab-pane <?php if ($tab_active == 'system') {
                            echo 'active';
                        } ?>" role="tabpanel">
                            <div class="postbox-content">
                                <h2><?php esc_html_e('Server Environment', 'urus') ?></h2>
                                <?php apply_filters('familab_server_environment', ''); ?>
                            </div>

                        </div>
                        <div id="import" class="tab-pane <?php if ($tab_active == 'import') {
                            echo 'active';
                        } ?>">
                            <?php do_action('urus_sample_data_tab'); ?>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <?php
        }

        public static function after_switch_theme()
        {
            if (!get_option('urus_has_active_theme')) {
                update_option('urus_has_active_theme', 1);
            }
        }

        public static function redirect_welcome_page()
        {
            $urus_has_active_theme = get_option('urus_has_active_theme', true);
            if ($urus_has_active_theme == 1) {
                update_option('urus_has_active_theme', 0);
                wp_redirect(admin_url('admin.php?page=urus-intro&tab=welcome'));
            }
        }
    }
}
