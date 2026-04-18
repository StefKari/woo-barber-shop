<?php
/**
 * 1.0
 * @package    Urus
 * @author     Familab <contact@familab.net>
 * @copyright  Copyright (C) 2018 familab.net. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://familab.net
 */

// Prevent direct access to this file
defined( 'ABSPATH' ) || die( 'Direct access to this file is not allowed.' );


$familab_theme = wp_get_theme();

if ( ! empty( $familab_theme['Template'] ) ) {
	$familab_theme = wp_get_theme( $familab_theme['Template'] );
}
if ( ! defined( 'DS' ) ) {
    define( 'DS', DIRECTORY_SEPARATOR );
}

if ( ! defined( 'URUS_API_URL' ) ) {
	define( 'URUS_API_URL', 'https://api.familab.net' );
}
if ( ! defined( 'URUS_THEME_NAME')) {
	define( 'URUS_THEME_NAME', $familab_theme['Name'] );
}
if ( ! defined( 'URUS_THEME_SLUG' ) ) {
	define( 'URUS_THEME_SLUG', $familab_theme['Template'] );
}
if ( ! defined( 'URUS_THEME_VERSION' ) ) {
	define( 'URUS_THEME_VERSION', $familab_theme['Version'] );
}
if ( ! defined( 'URUS_THEME_DIR' ) ) {
	define( 'URUS_THEME_DIR', get_template_directory() );
}
if ( ! defined( 'URUS_THEME_URI' ) ) {
	define( 'URUS_THEME_URI', trailingslashit ( get_template_directory_uri() ));
}

define( 'URUS_CHILD_THEME_URI', get_stylesheet_directory_uri() );
define( 'URUS_CHILD_THEME_DIR', get_stylesheet_directory() );


define( 'URUS_IMAGES', URUS_THEME_URI . '/assets/images' );

if ( ! defined( 'URUS_ADMIN_IMAGES' ) ) {
    define( 'URUS_ADMIN_IMAGES', URUS_THEME_URI . '/assets/images/admin' );
}

define( 'URUS_FRAMEWORK_DIR', URUS_THEME_DIR . '/framework' );
define( 'URUS_FRAMEWORK_URI', URUS_THEME_URI . '/framework' );

define( 'URUS_INC_DIR', URUS_FRAMEWORK_DIR . '/includes' );
define( 'URUS_INC_URI', URUS_FRAMEWORK_URI .'/includes' );


if( !class_exists('Urus')){
    class  Urus{
        /**
         * Define valid class prefix for autoloading.
         *
         * @var  string
         */
        private static $ad_prefix;
        private static $page_type;
        private static $act;
        private static $space;
        protected static $prefix = 'Urus_';
        public function __construct() {
            self::$ad_prefix = 'admin';
            self::$page_type = 'page';
            self::$act = 'add';
            self:: $space = '_';
	        self::initialize();
        }
        public static function initialize() {
            // Register class autoloader.
            $func = 'menu';
            $urus_action = self::get_action($func);
	        add_action( $urus_action, array( __CLASS__, 'register_action' ),1 );
            spl_autoload_register( array( __CLASS__, 'autoload' ) );
            // Register necessary actions.
            add_action('urus_blog_breadcrumbs',array(__CLASS__,'breadcrumbs'));
            add_filter( 'comment_form_fields', array(__CLASS__,'move_comment_field_to_bottom') );
            add_filter('pre_option_posts_per_page',array( __CLASS__,'limit_posts_per_page'));

            add_filter('upload_mimes',array( __CLASS__,'cc_mime_types'));
	        add_filter( 'wp_check_filetype_and_ext', array( __CLASS__, 'wp_check_filetype_and_ext' ), 100, 4 );
	        add_filter( 'wp_prepare_attachment_for_js', array( __CLASS__, 'wp_prepare_attachment_for_js' ), 10, 3 );
	        add_filter( 'wp_generate_attachment_metadata', array( __CLASS__, 'wp_generate_attachment_metadata' ), 10, 2 );
	        add_action('urus_before_content',array(__CLASS__,'preloader_html'),1);
	        add_filter('pre_get_posts',array(__CLASS__,'pre_get_posts'),999,1);
	        add_filter('enable_mobile_page',array(__CLASS__,'enable_mobile_page'),999,1);

	        new Urus_Mobile_Detect();
            Urus_Pluggable::initialize();
        }
        /**
         * Method to autoload class declaration file.
         *
         * @param   string  $class_name  Name of class to load declaration file for.
         *
         * @return  mixed
         */

        public static function autoload( $class_name ) {
            // Verify class prefix.
            if ( 0 !== strpos( $class_name, self::$prefix ) ) {
                return false;
            }
            // Generate file path from class name.
            $base = URUS_INC_DIR.'/';
            $path = strtolower( str_replace( '_', '/', substr( $class_name, strlen( self::$prefix ) ) ) );
            // Check if class file exists.
            $standard    = $path . '.php';
            $alternative = $path . '/' . current( array_slice( explode( '/', str_replace( '\\', '/', $path ) ), -1 ) ) . '.php';
            while ( true ) {
                // Check if file exists in standard path.
                if ( file_exists( $base . $standard ) ) {
                    $exists = $standard;
                    break;
                }
                // Check if file exists in alternative path.
                if ( file_exists( $base . $alternative ) ) {
                    $exists = $alternative;
                    break;
                }
                // If there is no more alternative file, quit the loop.
                if ( false === strrpos( $standard, '/' ) || 0 === strrpos( $standard, '/' ) ) {
                    break;
                }
                // Generate more alternative files.
                $standard    = preg_replace( '#/([^/]+)$#', '-\\1', $standard );
                $alternative = implode( '/', array_slice( explode( '/', str_replace( '\\', '/', $standard ) ), 0, -1 ) ) . '/' . substr( current( array_slice( explode( '/', str_replace( '\\', '/', $standard ) ), -1 ) ), 0, -4 ) . '/' . current( array_slice( explode( '/', str_replace( '\\', '/', $standard ) ), -1 ) );
            }
            // Include class declaration file if exists.
            if ( isset( $exists ) ) {
              include_once $base . $exists;
            }
            return false;
        }

        public static function register_action(){
            $menu = self::$act.self::$space.'menu'.self::$space.self::$page_type;
            $menu(
                esc_html__( 'Urus', 'urus' ),
                esc_html__( 'Urus', 'urus' ),
                'manage_options',
                'urus-intro',
                array( 'Urus_Dashboard', 'html'),
	            URUS_ADMIN_IMAGES.'/icon.png',
                2
            );
            // Add submenu items.
            $menu_sub = self::$act.self::$space.'submenu'.self::$space.self::$page_type;
            $menu_sub(URUS_THEME_SLUG.'-intro',
                esc_html__( 'Urus Dashboard', 'urus' ),
                esc_html__( 'Dashboard', 'urus' ),
                'manage_options',
                'urus-intro',
                array( 'Urus_Dashboard', 'html' )
            );
        }
        public static function breadcrumbs(){
            if( !function_exists('breadcrumb_trail')) return;
            if( is_front_page()) return;
            $args = array(
                'container'       => 'div',
                'before'          => '',
                'after'           => '',
                'show_on_front'   => true,
                'network'         => false,
                'show_title'      => true,
                'show_browse'     => false,
                'post_taxonomy'   => array(),
                'echo'            => true
            );
            ?>
            <div class="urus-breadcrumbs">
                <?php breadcrumb_trail($args); ?>
            </div>
            <?php

        }
        public static function move_comment_field_to_bottom( $fields ) {
            return $fields;
        }
        public static function limit_posts_per_page( $posts_per_page ) {
            if( isset($_GET['posts_per_page']) && is_numeric($_GET['posts_per_page']) && $_GET['posts_per_page'] > 0 ){
                return $_GET['posts_per_page'];
            }

            return $posts_per_page;
        }

        public static function RequestChangeLog(){
            if (class_exists('Familab_Core')){
                return Familab_Core::RequestChangeLog();
            }else{
                return;
            }
        }

        public static function cc_mime_types($mimes) {
          $mimes['svg'] = 'image/svg+xml';
          return $mimes;
        }
	    public static function wp_check_filetype_and_ext( $data = null, $file = null, $filename = null, $mimes = null ) {
		    $ext = isset( $data['ext'] ) ? $data['ext'] : '';
		    if ( strlen( $ext ) < 1 ) {
			    $exploded = explode( '.', $filename );
			    $ext      = strtolower( end( $exploded ) );
		    }
		    if ( $ext === 'svg' ) {
			    $data['type'] = 'image/svg+xml';
			    $data['ext']  = 'svg';
		    }
		    return $data;
	    }
        public static function wp_generate_attachment_metadata( $metadata, $attachment_id ){
		    if( get_post_mime_type( $attachment_id ) == 'image/svg+xml' ){
			    $svg_path = get_attached_file( $attachment_id );
			    $dimensions = self::svg_dimensions( $svg_path );
			    $metadata['width'] = $dimensions->width;
			    $metadata['height'] = $dimensions->height;
		    }
		    return $metadata;
	    }
	    public static function svg_dimensions( $svg ){
		    $svg = simplexml_load_file( $svg );
		    $width = 0;
		    $height = 0;
		    if( $svg ){
			    $attributes = $svg->attributes();
			    if( isset( $attributes->width, $attributes->height ) ){
				    $width = floatval( $attributes->width );
				    $height = floatval( $attributes->height );
			    }elseif( isset( $attributes->viewBox ) ){
				    $sizes = explode( " ", $attributes->viewBox );
				    if( isset( $sizes[2], $sizes[3] ) ){
					    $width = floatval( $sizes[2] );
					    $height = floatval( $sizes[3] );
				    }
			    }
		    }
		    return (object)array( 'width' => $width, 'height' => $height );
	    }
	    public static function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {

		    if( $response['mime'] == 'image/svg+xml' && empty( $response['sizes'] ) ){
			    $svg_path = get_attached_file( $attachment->ID );
			    if( ! file_exists( $svg_path ) ){
				    $svg_path = $response['url'];
			    }
			    $dimensions = self::svg_dimensions( $svg_path );
			    $response['sizes'] = array(
				    'full' => array(
					    'url' => $response['url'],
					    'width' => $dimensions->width,
					    'height' => $dimensions->height,
					    'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait'
				    )
			    );
		    }
		    return $response;
	    }
        public static function comment_callback($comment, $args, $depth){
            if ( 'div' === $args['style'] ) {
                $tag       = 'div';
                $add_below = 'comment';
            } else {
                $tag       = 'li';
                $add_below = 'div-comment';
            }?>
            <<?php echo esc_attr($tag); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID() ?>"><?php
            if ( 'div' != $args['style'] ) { ?>
                <div id="div-comment-<?php comment_ID() ?>" class="comment-body clearfix"><?php
            } ?>
            <div class="comment-author vcard">
            <?php
            if ( $args['avatar_size'] != 0 ) {
                echo '<div class="author-avatar">';
                echo get_avatar( $comment, $args['avatar_size'] );
                echo '</div>';
            }

            ?>
            <div class="comment-content">
                <div class="comment-meta commentmetadata">
                    <?php echo '<cite class="fn">'.get_comment_author_link().'</cite> ';?>

                    <?php
                        if ( $comment->comment_approved == '0' ) { ?>
                            <em class="comment-awaiting-moderation">
                                <?php esc_html_e( 'Your comment is awaiting moderation.','urus' ); ?>
                            </em>
                            <br/>
                            <?php
                        }
                    ?>
                    <span class="reply">
                        <?php
                            comment_reply_link(
                                array_merge(
                                    $args,
                                    array(
                                        'add_below' => $add_below,
                                        'depth'     => $depth,
                                        'max_depth' => $args['max_depth']
                                    )
                                )
                            ); ?>
                    </span>
                    <div class="comment-date">
                        <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
                            <?php
                                echo get_comment_date();
                            ?>
                        </a>
                    </div>
                </div>
                <?php comment_text(); ?>
            </div>

            </div>



            <?php
            if ( 'div' != $args['style'] ) : ?>
                </div><?php
            endif;

        }
        public static function get_action($func = ''){
            return self::$ad_prefix.self::$space.$func;
        }

        public static function preloader_html(){
            $enable_page_preloader = Urus_Helper::get_option('enable_page_preloader',0);
            $preloader_style = Urus_Helper::get_option('preloader_style','audio');
            if( $enable_page_preloader == 1 ){
              $html ='<div class="preloader urus-loader '.esc_attr($preloader_style).'"></div>';
              echo Urus_Helper::escaped_html($html);
            }
        }

        public static function pre_get_posts( $query ){
            $enable_mobile_page = Urus_Helper::get_option('enable_mobile_page',0,false);

            if($enable_mobile_page == 0){
                return $query;
            }
            if ( is_admin() ) {
                return $query;
            }
            if ( $query->is_main_query() ) {
                if ( is_home() ) {
                    // Default homepage or blog Archive
                    $page_id = get_option( 'page_for_posts' );
                } elseif ( !is_single() && !empty( $query->query_vars['page_id'] ) ) {
                    // static homepage aka front page
                    $page_id = $query->query_vars['page_id'];
                } else {
                    //everything else
                    $page_id = get_queried_object_id();
                }
                if ( is_page() && Urus_Mobile_Detect::isMobile() ) {
                    $children = get_pages(
                        array(
                            'child_of' => $page_id,
                            'echo'     => 0,
                        )
                    );
                    if ( isset( $children[0]->ID ) ) {
                        $query->set( 'page_id', $children[0]->ID );
                    }
                }
            }

            return $query;
        }

        public static function enable_mobile_page($enable_mobile_page){

            return $enable_mobile_page;
        }
    }
}
if( !function_exists('e_data')){
    function e_data($string){
        return trim($string);
    }
}

/* Enter your custom functions here */ // Remove the additional information tab
function woo_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] );
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

add_action( 'after_setup_theme', 'remove_pgz_theme_support', 100 );

function remove_pgz_theme_support() { 
remove_theme_support( 'wc-product-gallery-zoom' );
}


add_filter( 'woocommerce_currency_symbol', 'my_custom_currency_symbol' );
function my_custom_currency_symbol( $symbol ) {
    return 'RSD';
}



// Load Breadcrumbs.
get_template_part( 'libraries/breadcrumbs' );
get_template_part( 'libraries/class-tgm-plugin-activation' );
get_template_part( 'libraries/MCAPI.class' );
get_template_part( 'libraries/Mobile_Detect' );
new Urus();
