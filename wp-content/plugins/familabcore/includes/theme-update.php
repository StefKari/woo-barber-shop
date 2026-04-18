<?php
if( !class_exists('Familab_Core_Theme_Update')){
    class Familab_Core_Theme_Update{
        public function __construct() {
            if (FAMILAB_MARKET == 'tf'){
                add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ),999 );
            }else{
                add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update_v2' ),999 );
            }
        }
        public function check_for_update_v2($transient) {
            $update = Familab_Core::theme_version();
            if ($update && Familab_Core::theme_has_update()){
                $response = array(
                    //'url'         => esc_url(FAMILAB_API_URL.'/changelog/html/'.FAMILAB_THEME_SLUG),
                    'url'         => esc_url(FAMILAB_DOC_URL.'/'.FAMILAB_THEME_SLUG.'/changelogs/'),
                    'new_version' => $update['new_version'],
                );
                $transient->response[ FAMILAB_THEME_SLUG ] = $response;
                // If the purchase code is valide, user can get the update package
                $license_key = get_option('familab_license_key'.FAMILAB_THEME_SLUG);
                $parse = parse_url(get_site_url());
                $back_uri = $parse['host'];
                $transient->response[ FAMILAB_THEME_SLUG ]['package'] = FAMILAB_API_URL.'/package_v2/'.FAMILAB_THEME_SLUG.'/'.$license_key.'/'.$update['new_version'].'.zip'.'/'.esc_url($back_uri);
            }
            return $transient;
        }
        public static function check_for_update($transient) {
            $update = Familab_Core::theme_version();
            if ($update && Familab_Core::theme_has_update()){
                $response = array(
                    'url'         => esc_url(FAMILAB_DOC_URL.'/'.FAMILAB_THEME_SLUG.'/changelogs/'),
                    'new_version' => $update['new_version'],
                );
                $transient->response[ FAMILAB_THEME_SLUG ] = $response;
                // If the purchase code is valide, user can get the update package
                $license_key = get_option('familab_license_key'.FAMILAB_THEME_SLUG);
                $check_license = Familab_Core_Market_Check::check_license($license_key);
                if ($check_license) {
                    $parse = parse_url(get_site_url());
                    $back_uri = $parse['host'];
                    $transient->response[ FAMILAB_THEME_SLUG ]['package'] = FAMILAB_API_URL.'/package/'.FAMILAB_THEME_SLUG.'/'.$license_key.'/'.md5($back_uri).'/'.$update['new_version'].'.zip';
                } else {
                    unset( $transient->response[ FAMILAB_THEME_SLUG ]['package'] );
                }
            }
            return $transient;
        }
    }
}