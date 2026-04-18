
<div class="familab-content">
    <?php
    if ($display_info):
        $info = apply_filters('theme_infomation',array());
    ?>
    <div class="container-fluid tr-container">
        <div class="dashboard-head text-center">
            <div class="familab-core-theme-thumb">
                <img class="dashboard-logo" src="<?php echo(FAMILAB_THEME_URI.'/assets/images/logo.svg')?>">
            </div>
            <div class="theme-desc">
				<?php print_r($info['desc']); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="familab-wrap">
        <?php
        $valid = true;
        if ( ! is_writable( ABSPATH . 'wp-content' ) ) :
	        $valid = false;
	        ?>
            <div class="notice notice-error" style="display: block !important;">
                <p>
			        <?php echo sprintf(__( 'Could not write files into directory: <strong>%swp-content</strong>. Tips: Try to change Chmod of folder to 755 or 777.',
				        'familabcore' ),
				        str_replace( '\\', '/', ABSPATH ) ) ?>
                </p>
            </div>
        <?php
        endif;
        if ( function_exists( 'phpversion' ) ) :
	        $php_version = esc_html( phpversion() );
	        if ( version_compare( $php_version, '5.6', '<' ) ) :
		        $valid = false;
		        ?>
                <div class="notice notice-error" style="display: block !important;">
                    <p>
				        <?php esc_html_e( 'Insight Core requires PHP version 5.6 or greater. Please contact your hosting provider to upgrade PHP version.',
					        'familabcore' ) ?>
                    </p>
                </div>
	        <?php
	        endif;
        endif;
        if ( ! function_exists( 'fsockopen' ) && ! function_exists( 'curl_init' ) ) :
	        $valid = false;
	        ?>
            <div class="notice notice-error" style="display: block !important;">
                <p>
			        <?php esc_html_e( 'Your server does not have fsockopen or cURL enabled. Please contact your hosting provider to enable it.',
				        'familabcore' ) ?>
                </p>
            </div>
        <?php
        endif;
        if ( ! class_exists( 'DOMDocument' ) ) :
	        $valid = false;
	        ?>
            <div class="notice notice-error" style="display: block !important;">
                <p>
			        <?php printf( __( 'Your server does not have <a href="%s">the DOM extension</a> class enabled. Please contact your hosting provider to enable it.',
				        'familabcore' ),
				        'http://php.net/manual/en/intro.dom.php' ) ?>
                </p>
            </div>
        <?php
        endif;
        if ( ! class_exists( 'XMLReader' ) ) :
	        $valid = false;
	        ?>
            <div class="notice notice-error" style="display: block !important;">
                <p>
			        <?php printf( __( 'Your server does not have <a href="%s">the XMLReader extension</a> class enabled. Please contact your hosting provider to enable it.',
				        'familabcore' ),
				        'http://php.net/manual/en/intro.xmlreader.php' ) ?>
                </p>
            </div>
        <?php
        endif;
        $time_limit = ini_get( 'max_execution_time' );
        if ( $time_limit < 180 ) :
	        ?>
            <div class="notice notice-warning" style="display: block !important;">
                <p>
			        <?php printf( __( '<b>WARNING: </b>Your server does not meet the importer requirements. The PHP max execution time currently is %s. We recommend setting PHP max execution time to at least 180. See: <a href="%s" target="_blank">Increasing max execution to PHP</a>. 
<br/>If you are unsure of how to make these changes, or if you are on shared hosting that prevents you from making them yourself, you should contact your hosting provider and ask them to increase your maximum execution time.',
				        'familabcore' ),
				        $time_limit,
				        'http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded' ) ?>
                </p>
            </div>
        <?php
        endif;
        if ( ! $valid ) {
	        die;
        }
        ?>
        <div class="postbox tr-box">
            <h2><?php echo __('Import Notice','familabcore')?></h2>
            <?php esc_html_e( 'Our demo data import lets you have the whole data package in minutes, delivering all kinds of essential things quickly and simply. You may not have enough time for a coffee as the import is too fast!',
                'familabcore' ) ?>
            <br/>
            <br/>
            <i>
                <?php esc_html_e( 'Notice: Before import, Make sure your website data is empty (posts, pages, menus...etc...)',
                    'familabcore' ); ?>
                </br>
                <?php esc_html_e( 'We suggest you use the plugin', 'familabcore' ); ?>
                <a href="<?php echo esc_url( admin_url() ); ?>/plugin-install.php?tab=plugin-information&plugin=wordpress-reset&TB_iframe=true&width=800&height=550"
                   class="thickbox" title="Install Wordpress Reset">"Wordpress Reset"</a>
                <?php esc_html_e( 'to reset your website before import.', 'familabcore' ); ?>
            </i>
        </div>
        <?php
        $data_pages = array();
        $data_home = array();
        if (isset($demos['shop'])){
            $data_pages['shop'] = $demos['shop'];
        }
        if (isset($demos['single'])){
            $data_pages['single'] = $demos['single'];
        }
        ?>
        <div class="row">
            <?php foreach ($demos as $type => $demo): ?>
            <?php
                if (!class_exists( 'VC_Manager' ) && (!isset($demo['builder']) || $demo['builder'] == 'vc')) {
                    continue;
                }
                if(!did_action( 'elementor/loaded' ) && isset($demo['builder']) && $demo['builder'] == 'elementor'){
                    continue;
                }
                if (!isset($import_time[$type])){
                    $import_time[$type] = 0;
                }else{
                    if (isset($demo['homes'])){
                        if (isset($data_pages['homes'])){
                            $data_home = array_merge($data_pages['homes'],$demo['homes']);
                        }else{
                            $data_home = $demo['homes'];
                        }
                        $data_pages['homes'] = $data_home;
                    }
                }
            ?>
            <div class="col-md-3">
                <div class="content-box welcome-panel import-demo">
                    <h2 class="welcome-icon dashicons-album"><?php echo $demo['name']; ?></h2>
                    <p><?php echo $demo['description']; ?></p>
                    <div class="import-demo-screenshoot">
                        <img class="img-responsive" src="<?php echo $demo['screenshot']; ?>" alt="<?php echo $demo['name']; ?>">
                    </div>
                    <div class="import-box-footer">
                        <span class="text-left">
                            <?php _e(sprintf('Installed (%s) times',$import_time[$type]),'familabcore'); ?>
                        </span>
                        <span class="text-right">
                            <form method="post" action="admin.php?page=familab-core-import&step=1">
                                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('install_demo_'.$type); ?>">
                                <input type="hidden" name="type" value="<?php echo $type; ?>">
                                <button type="submit" name="install_demo" value="1"><?php _e('Install','familabcore'); ?></button>
                            </form>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (sizeof($data_pages) > 0): ?>
            <?php
                update_option('familab_pages_options_preset',$data_pages);
            ?>
            <div class="postbox tr-box import_pages">
                <h2><?php esc_html_e('Quick setup theme Options','familabcore') ?></h2>
                <div class="row">
                    <?php if (isset($data_pages['homes'])): ?>
                        <div class="col-md-3">
                            <div class="content-box welcome-panel import-demo">
                                <h3><?php esc_html_e('Home pages setup','familabcore') ?></h3>
                                <div class="view_panel">
                                    <img src="<?php echo(FAMILAB_THEME_URI.'/screenshot.png')?>" id="set_homepage_preview"/>
                                    <div class="form-setting">
                                        <select name="home_pages">
                                            <option value=""><?php esc_html_e('-- select --','familabcore');?></option>
                                            <?php foreach ($data_pages['homes'] as $key =>$home_page): ?>
                                                <option value="<?php echo $key;?>" data-thumb="<?php echo $home_page['thumbnail']; ?>"><?php echo $home_page['name'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="" id="set_home_page_demo"><?php esc_html_e('Setup home page','familabcore');?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
