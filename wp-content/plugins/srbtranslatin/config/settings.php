<?php
    /**
     * Settings config
     *
     * @package srbtranslatin
     * @subpackage Config
     */

defined( 'ABSPATH' ) || exit;

$wplang = get_locale();

$disable_permalinks = 'sr_RS' === $wplang || 'bs_BA' === $wplang;
$navigation_menus   = get_registered_nav_menus();

$nav_menus = 0 === count( $navigation_menus ) ? false : true;

/*
 * Translating before 'init' triggers a _load_textdomain_just_in_time notice (WP 6.7+).
 * The settings array is first read while the plugin boots, where only the keys and
 * defaults matter, so labels stay untranslated until 'init' has fired.
 */
$stl_translate = did_action( 'init' );
$stl__         = $stl_translate ? '__' : static function ( $text ) {
	return $text;
};
$stl_x         = $stl_translate ? '_x' : static function ( $text ) {
	return $text;
};

return array(
    'name'     => $stl__( 'srbtranslatin - Serbian Latinisation', 'srbtranslatin' ),
    'basename' => STL_PLUGIN_BASENAME,
    'meta'     => array(
        array(
            'link' => 'https://oblak.studio/plugins/srbtranslatin/',
            'text' => $stl__( 'Documentation', 'srbtranslatin' ),
        ),
    ),
    'page'     => array(
        'root'       => true,
        'parent'     => 'options-general.php',
        'title'      => $stl__( 'Latinisation', 'srbtranslatin' ),
        'menu_title' => $stl__( 'Settings', 'default' ),
        'cap'        => 'manage_options',
        'image'  => file_get_contents( STL_PLUGIN_PATH . 'assets/images/stl-logo.svg' ), // phpcs:ignore
        'prio'       => 99,
    ),
    'settings' => array(
        'general'        => array(
            array(
                'title' => $stl_x( 'General settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => $stl__( 'General settings control main functionality of the plugin', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_general_settings',
            ),

            array(
                'title'   => $stl__( 'Default script', 'srbtranslatin' ),
                'desc'    => $stl__( 'Default script used for the website if user did not select a script', 'srbtranslatin' ),
                'id'      => 'default_script',
                'type'    => 'select',
                'default' => 'cir',
                'options' => stl_get_available_scripts(),
            ),

            array(
                'title'   => $stl__( 'URL Parameter', 'srbtranslatin' ),
                'id'      => 'url_param',
                'desc'    => $stl__( 'URL parameter used for script selector', 'srbtranslatin' ),
                'type'    => 'text',
                'default' => 'pismo',
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_general_settings',
            ),

            array(
                'title' => $stl_x( 'Menu settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => $stl__( 'Options that control the display of the script selector', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_menu_settings',
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_menu_settings',
            ),
        ),
        'menu'           => array(
            array(
                'title' => $stl_x( 'Navigation menu settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => $stl__( 'Menu settings control extending and tweaking the script selector in theme menus', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_menu_settings',
            ),
            array(
                'id'   => 'srbtranslatin_menu_warning',
                'type' => 'info',
                'text' => ! $nav_menus
                    ? '<strong>' . $stl__( 'Options in this section are disabled because you do not have any navigation menus registered', 'srbtranslatin' ) . '</strong>'
                    : '',
            ),

            array(
                'title'    => $stl__( 'Extend navigation menu', 'srbtranslatin' ),
                'desc'     => $stl__( 'Adds a script selector to the navigation menu', 'srbtranslatin' ),
                'id'       => 'extend',
                'type'     => 'checkbox',
                'disabled' => ! $nav_menus,
                'default'  => 'yes',
            ),

            array(
                'title'    => $stl__( 'Navigation menu to extend', 'srbtranslatin' ),
                'desc'     => $stl__( 'Select the navigation menu you want to extend', 'srbtranslatin' ),
                'id'       => 'extend_menu',
                'type'     => 'select',
                'options'  => array_merge(
                    array( '' => $stl__( 'Select a menu', 'srbtranslatin' ) ),
                    $navigation_menus,
                ),
                'disabled' => ! $nav_menus,
                'default'  => '',
            ),

            array(
                'title'    => $stl__( 'Selector type', 'srbtranslatin' ),
                'desc'     => $stl__( 'Choose the type of the script selector', 'srbtranslatin' ),
                'id'       => 'selector_type',
                'type'     => 'select',
                'options'  => array(
                    'submenu' => $stl__( 'Submenu', 'srbtranslatin' ),
                    'inline'  => $stl__( 'Inline', 'srbtranslatin' ),
                ),
                'default'  => 'submenu',
                'disabled' => ! $nav_menus,

            ),

            array(
                'title'    => $stl__( 'Menu item title', 'srbtranslatin' ),
                'desc'     => $stl__( 'Title of the menu item', 'srbtranslatin' ),
                'id'       => 'menu_title',
                'type'     => 'text',
                'default'  => $stl__( 'Script', 'srbtranslatin' ),
                'disabled' => ! $nav_menus,

            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_media_settings',
            ),
        ),
        'media'          => array(
            array(
                'title' => $stl_x( 'File and Media settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => $stl__( 'File and media settings control filename transliteration and media saving', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_media_settings',
            ),

            array(
                'title'   => $stl__( 'Transliterate uploads', 'srbtranslatin' ),
                'type'    => 'checkbox',
                'desc'    => $stl__( 'Transliterate filenames on upload', 'srbtranslatin' ),
                'default' => 'yes',
                'id'      => 'transliterate_uploads',
            ),

            array(
                'title'   => $stl__( 'Script specific filenames', 'srbtranslatin' ),
                'type'    => 'checkbox',
                'desc'    => $stl__( 'Check this box if you want to have separate filenames for each script', 'srbtranslatin' ),
                'id'      => 'separate_uploads',
                'default' => 'yes',
            ),

            array(
                'title'   => $stl__( 'Filename separator', 'srbtranslatin' ),
                'type'    => 'text',
                'desc'    => $stl__( 'Separator used for script specific filenames', 'srbtranslatin' ),
                'id'      => 'filename_separator',
                'class'   => 'small-text',
                'default' => '-',
            ),

            array(
                'title'   => $stl__( 'Transliteration method', 'srbtranslatin' ),
                'type'    => 'select',
                'desc'    => $stl__( 'Choose if you want to limit the script specific filenames on the entire website, or in content only', 'srbtranslatin' ),
                'id'      => 'transliteration_method',
                'options' => array(
                    'website' => $stl__( 'Entire website', 'srbtranslatin' ),
                    'content' => $stl__( 'Content only', 'srbtranslatin' ),
                ),
                'default' => 'website',
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_media_settings',
            ),
        ),
        'wpml'           => array(
            array(
                'id'      => 'extend_ls',
                'title'   => $stl__( 'Enable', 'srbtranslatin' ),
                'type'    => 'checkbox',
                'default' => 'yes',
                'desc'    => $stl__( 'Extend WPML Language Switcher', 'srbtranslatin' ),
            ),
        ),
        'polylang'       => array(),
        'translatepress' => array(),
        'advanced'       => array(
            array(
                'title' => $stl_x( 'Advanced settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => $stl__( 'Advanced settings control permalink and search settings', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_advanced_settings',
            ),

            array(
                'title'    => $stl__( 'Fix Permalinks', 'srbtranslatin' ),
                'desc'     => $stl__( 'Fixes permalinks for cyrillic scripts', 'srbtranslatin' ),
                'id'       => 'fix_permalinks',
                'type'     => 'checkbox',
                'default'  => 'no',
                'disabled' => $disable_permalinks,
                'tooltip'  => $disable_permalinks
                    ? sprintf(
                        // translators: %s is the current locale.
                        $stl__( 'This option is currently disabled because your current locale is set to %s which will automatically change permalnks', 'srbtranslatin' ),
                        $wplang
                    )
                    : null,
            ),

            array(
                'title'   => $stl__( 'Fix Search', 'srbtranslatin' ),
                'desc'    => $stl__( 'Enables searching cyrillic content via latin script', 'srbtranslatin' ),
                'id'      => 'fix_search',
                'type'    => 'checkbox',
                'default' => 'no',
            ),

            array(
                'title'   => $stl__( 'Fix Ajax', 'srbtranslatin' ),
                'desc'    => $stl__( 'Transliterates ajax calls', 'srbtranslatin' ),
                'id'      => 'fix_ajax',
                'type'    => 'checkbox',
                'default' => 'no',
            ),

            array(
                'title'   => $stl__( 'Fix Titles', 'srbtranslatin' ),
                'desc'    => $stl__( 'Fixes titles for cyrillic scripts', 'srbtranslatin' ),
                'id'      => 'fix_titles',
                'type'    => 'checkbox',
                'default' => 'no',
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_advanced_settings',
            ),
        ),
    ),
);
