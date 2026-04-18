<?php
if( !class_exists('Familab_Core_Market_Check')){
    class Familab_Core_Market_Check{
        public static $initialized;
        public static $item_id;
        public function __construct() {
            if ( self::$initialized ) {
                return;
            }
            add_action('admin_init',array($this,'save_license_key'));
            // State that initialization completed.
            self::$item_id = apply_filters('familab_theme_id',false);
            self::$initialized = true;
        }
        public static function license_form(){
            $license_key = get_option('familab_license_key'.FAMILAB_THEME_SLUG);
            $check_license = false;
            if($license_key ==""){
                $license_key = get_option('familab_license_key');
                if ($license_key !=""){
                    update_option('familab_license_key'.FAMILAB_THEME_SLUG,$license_key);
                }
            }
            ?>
            <?php if($license_key !=""){
                $check_license_result = self::check_license($license_key,false,true);
                $has_full_info = false;
                if (is_array($check_license_result) && isset($check_license_result['status'])){
                    $license_status = $check_license_result['status'];
                    $has_full_info = true;
                }else{
                    $license_status = $check_license_result;
                }
                if(!$license_status){
                    $check_license = false;
                    ?>
                    <div class="familab-alert familab-alert-warning">
                        <p>
                            <?php
                                if ($has_full_info):
                                    if (isset($check_license_result['msg'])):?>
                                        <strong>
                                            <?php esc_html_e($check_license_result['msg']);?>
                                        </strong>
                                    <?php endif;
                                    if (isset($check_license_result['domain_active'])):
                                        $current_domain = parse_url(get_site_url());
                                        if ($current_domain['host'] != $check_license_result['domain_active']):?>
                                            <strong>
                                                <?php esc_html_e('The license is already activated by another domain!','familabcore');?>
                                            </strong>
                                            <span><?php esc_html_e('If you need assistance, please leave your ticket via our support system','familabcore');?></span>
                                            <a class="welcome-icon dashicons-editor-help"
                                               target="_blank"
                                               href="https://support.familab.net/submit-ticket/"><?php esc_html_e('Request Support', 'urus'); ?></a>
                                        <?php endif;
                                    endif
                            ?>
                            <?php else: ?>
                                <strong>
                                    <?php esc_html_e('License has not been verify. ','familabcore');?>
                                </strong>
                            <?php endif;?>
                        </p>
                        <?php 
                            if (isset($check_license_result['status'])){
                                echo '<pre style="display: none">';
                                print_r($check_license_result);
                                echo '</pre>';
                            }
                        ?>
                    </div>
                <?php }else{
                    $check_license = true;
                    ?>
                    <div class="familab-alert familab-alert-notice">
                        <p>
                            <strong><?php esc_html_e('License has been activated','familabcore');?></strong>
                        </p>
                    </div>
                <?php }
                ?>
            <?php }
            if (!$check_license) {
                ?>
                <div class="postbox-content">
                    <p>
                        <?php printf(esc_html__( 'In order to install sample data, automatic update theme you need to validate %s purchase code and validate %s today for premium support,', 'familabcore' ),FAMILAB_THEME_NAME,FAMILAB_THEME_NAME); ?>
                        <br/>
                        <?php esc_html_e( 'latest updates, eCommerce guides, and much more.', 'familabcore' )
                        ?>
                        <a target="_blank"
                           href="https://support.familab.net/knowledgebase/how-to-get-envato-purchase-code/"><?php esc_html_e( 'How to get envato purchase code.', 'familabcore' ); ?></a>
                    </p>
                    <form class="license-form"
                          action="<?php echo esc_url( admin_url( 'admin.php?page='.FAMILAB_THEME_SLUG.'-intro&tab=update' ) ); ?>"
                          method="post">
                        <p>
                            <input placeholder="<?php esc_attr_e( 'Purchase Code', 'familabcore' ); ?>"
                                   class="input-text" type="text" name="license_key"
                                   value="<?php echo esc_attr( $license_key ); ?>">
                        </p>
                        <button class="save_license_key tr-button"><?php esc_html_e( 'Save', 'familabcore' ); ?></button>
                        <input type="hidden" name="famila_action" value="save_license_key">
                    </form>
                </div>
                <?php
            }

            if ($check_license && self::theme_has_update()){
                $update_url = wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&amp;theme=' . base64_encode( FAMILAB_THEME_SLUG ) ), 'upgrade-theme_' . FAMILAB_THEME_SLUG );
                $html = sprintf('<a href="%1$s" %2$s>%3$s</a>.' ,
                    $update_url,
                    sprintf( 'aria-label="%s" id="update-theme" data-slug="%s"',
                        esc_attr( sprintf( esc_attr__( 'Update %s Now' ,'familabcore'), FAMILAB_THEME_NAME ) ),
                        FAMILAB_THEME_SLUG
                    ),
                    esc_html__('Update Now','familabcore')
                );
                $fmdebug = false;
                if ($fmdebug){
                    $parse = parse_url(get_site_url());
                    $back_uri = $parse['host'];
                    $update = Familab_Core::theme_version();
                    echo FAMILAB_API_URL.'/package/'.FAMILAB_THEME_SLUG.'/'.$license_key.'/'.md5($back_uri).'/'.$update['new_version'].'.zip';
                }
                echo e_data($html);
            }
        }
        public static function theme_has_update(){
            $transient_update_themes = get_option('_site_transient_update_themes');
            $avaiable_update = false;
            if (isset($transient_update_themes->response[FAMILAB_THEME_SLUG]['new_version'])){
                $theme_version = $transient_update_themes->response[FAMILAB_THEME_SLUG]['new_version'];
            }elseif (isset($transient_update_themes->checked[FAMILAB_THEME_SLUG])){
                $theme_version = $transient_update_themes->checked[FAMILAB_THEME_SLUG];
            }else{
                $theme_version =  FAMILAB_THEME_VERSION;
            }
            if ( version_compare( $theme_version, FAMILAB_THEME_VERSION ) == 1 ) {
                $avaiable_update = true;
            }
            return $avaiable_update;
        }
        public static function check_license($license_key = '',$skip_transient = false, $full_info = false){
            if ($license_key == '')
                $license_key = get_option('familab_license_key'.FAMILAB_THEME_SLUG);
            if (!$license_key == ''){
                $site_code = base64_encode(get_site_url());
                $familab_salt = get_option('familab_salt'.$site_code);
                if ($familab_salt==''){
                if (get_transient('check_license'.FAMILAB_THEME_SLUG) === false || $skip_transient){
                        $url = FAMILAB_API_URL.'/purchasecode/'.FAMILAB_THEME_SLUG.'/'.$license_key.'/'.$site_code;
                    $reponsive = wp_remote_get($url);
                    if( !is_wp_error($reponsive)){
                        $result = isset($reponsive['body']) ? json_decode($reponsive['body']) : false;
                        if (!$result){
                            set_transient('check_license'.FAMILAB_THEME_SLUG,0,DAY_IN_SECONDS);
                            return false;
                        }
                        if( $result->status ){
                                update_option('familab_salt'.$site_code,$result->salt,false);
                            set_transient('check_license'.FAMILAB_THEME_SLUG,1,DAY_IN_SECONDS);
                            return true;
                        }else{
                            if ($full_info){
                                return (array)$result;
                            }
                        }
                    }
                    }
                }else{
                    return true;
                }
            }
            return false;
        }
        public function save_license_key(){
            if( isset($_POST['famila_action']) && $_POST['famila_action'] == 'save_license_key'){
                if (!self::$item_id){
                    self::$item_id = apply_filters('familab_theme_id',self::$item_id);
                }
                $license_key = isset($_POST['license_key'])? $_POST['license_key'] :'';
                delete_transient('check_license'.FAMILAB_THEME_SLUG);
                update_option('familab_license_key'.FAMILAB_THEME_SLUG,$license_key);
                self::check_license($license_key,true);
                /*$redirect_uri = admin_url('admin.php?page='.FAMILAB_THEME_SLUG.'-intro&tab=update');
                $redirect_uri = urldecode($redirect_uri);
                $parse = parse_url($redirect_uri);
                $domain =  $parse['host'];
                $url = 'https://support.familab.net/?help_action=verify-purchase-code&domain='.$domain.'&license_key='.$license_key.'&item_id='.self::$item_id.'&redirect_uri='.$redirect_uri.'';
                wp_redirect($url);*/
            }
        }
    }
}
