<?php
if( !class_exists('Urus_Settings')){
    class Urus_Settings{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;

        public static $args     = array();
        public static $sections = array();
        public static $theme;
        public static $ReduxFramework;
        /**
         * Initialize pluggable functions for Visual Composer.
         *
         * @return  void
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }
            if ( !class_exists( "ReduxFramework" ) ) {
                return;
            }
            //self::initSettings();
            add_action('init',array(__CLASS__,'initSettings'));
            // State that initialization completed.
            self::$initialized = true;
        }
	    public static function enqueue_scripts(){
		    $page = isset($_REQUEST['page'])?$_REQUEST['page']:'';
		    if ($page =='urus_options') {
			    wp_enqueue_style( 'urus-setting', URUS_THEME_URI . '/assets/css/admin/setting.css' );
		    }
	    }
        public static function initSettings() {
            // Just for demo purposes. Not needed per say.
            self::$theme = wp_get_theme();
            // Set the default arguments
            self::setArguments();
            // Create the sections and fields
            self::setSections();
            if ( !isset(  self::$args['opt_name'] ) ) { // No errors please
                return;
            }
            $sections = apply_filters( 'urus_all_theme_options_sections', self::$sections ) ;
            self::$ReduxFramework = new ReduxFramework( $sections, self::$args );
        }

        /**
         *
         * All the possible arguments for Redux.
         * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
         **/
        public static function setArguments() {


            self::$args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'           => 'urus', // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'       => '<span class="theme-name">' . sanitize_text_field( URUS_THEME_NAME ) . '</span>', // Name that appears at the top of your panel
                'display_version'    => URUS_THEME_VERSION, // Version that appears at the top of your panel
                'menu_type'          => 'submenu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'     => false, // Show the sections below the admin menu item or not
                'menu_title'         => esc_html__( 'Theme Options', 'urus' ),
                'page_title'         => esc_html__( 'Theme Options', 'urus' ),
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key'     => '', // Must be defined to add google fonts to the typography module
                //'async_typography'    => true, // Use a asynchronous font on the front end or font string
                //'admin_bar'           => false, // Show the panel pages on the admin bar
                'global_variable'    => 'urus', // Set a different name for your global variable other than the opt_name
                'dev_mode'           => false, // Show the time the page took to load, etc
                'customizer'         => true, // Enable basic customizer support
                // OPTIONAL -> Give you extra features
                'page_priority'      => null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'        => 'urus-intro', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'   => 'manage_options', // Permissions needed to access the options panel.
                'menu_icon'          => '', // Specify a custom URL to an icon
                'last_tab'           => '', // Force your panel to always open to a specific tab (by id)
                'page_icon'          => 'icon-themes', // Icon displayed in the admin panel next to your menu_title
                'page_slug'          => 'urus_options', // Page slug used to denote the panel
                'save_defaults'      => true, // On load save the defaults to DB before user clicks save or not
                'default_show'       => false, // If true, shows the default value next to each field that is not the default value.
                'default_mark'       => '', // What to print by the field's title if the value shown is default. Suggested: *
                // CAREFUL -> These options are for advanced use only
                'transient_time'     => 60 * MINUTE_IN_SECONDS,
                'output'             => true, // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'         => true, // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                'footer_credit'      => esc_html__( 'Familab WordPress Team', 'urus' ), // Disable the footer credit of Redux. Please leave if you can help it.
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'           => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'show_import_export' => true, // REMOVE
                'system_info'        => false, // REMOVE
                'help_tabs'          => array(),
                'help_sidebar'       => '',
                'show_options_object'=>false,
            );

        }
        public static function setSections() {
            self::$sections = array_merge(self::$sections,Urus_Settings_Options_General::get());
            self::$sections = array_merge(self::$sections,Urus_Settings_Options_Layout::get());
            self::$sections = array_merge(self::$sections,Urus_Settings_Options_Color::get());
            self::$sections = array_merge(self::$sections,Urus_Settings_Options_Header::get());
            self::$sections = array_merge(self::$sections,Urus_Settings_Options_Footer::get());
            self::$sections = array_merge(self::$sections,Urus_Settings_Options_Blog::get());
            if( class_exists('WooCommerce')){
                self::$sections = array_merge(self::$sections,Urus_Settings_Options_Woocommerce::get());
            }
            self::$sections = array_merge(self::$sections,Urus_Settings_Options_Mobile::get());
            self::$sections = array_merge(self::$sections,Urus_Settings_Options_Typography::get());
			
        }
    }
}