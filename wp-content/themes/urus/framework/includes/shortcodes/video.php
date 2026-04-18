<?php
if( !class_exists('Urus_Shortcodes_Video')){
    class Urus_Shortcodes_Video extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'video';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_video', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_video_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_video', $atts ) : $atts;
            $product_style =  $title = '';
            extract( $atts );
            $css_class    = array( 'urus-video' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_video', $atts );
            $ytb_video = (isset($atts['source']) && $atts['source'] == "youtube") ? true : false;
	        $show_control = "&controls=1";
            if (isset($atts['show_control'])){
                if ($atts['show_control'] != "default"){
                    $show_control = "&controls=0";
                }
            }
            $video_width = (isset($atts['width']) && $atts['width']) ? $atts['width'] : 500;
            $unit = preg_replace('/\d/','', $video_width);
            $video_width = preg_replace('/\D/','', $video_width);
            $unit = empty($unit) ? "px" : $unit;
            $video_height = "height=". round($video_width / 1.77, 2).$unit;
            $video_height = is_numeric(strpos($unit, "%")) ? "" : $video_height;
            $video_width = "width=".$video_width.$unit;
            $image = isset($atts['background_img']) && $atts['background_img'] ? Urus_Helper::resize_image($atts['background_img'],false,false,false,false) : "";
            if ($ytb_video && (is_numeric(strpos($atts['url'], "//youtube.com")) || is_numeric(strpos($atts['url'], "//www.youtube.com")))){
                $video_loop = (isset($atts['video_loop']) && $atts['video_loop']) ? "&loop=1" : "";
                parse_str( parse_url( $atts['url'], PHP_URL_QUERY ), $array_url_params );
                $ytb_video_id = $array_url_params['v'];
                $atts['url'] = "https://www.youtube.com/embed/".$ytb_video_id."?enablejsapi=1&cc_load_policy=0&modestbranding=0&showinfo=0&origin=".get_site_url().$show_control.$video_loop;
            }
            else{
                $show_control = $atts['show_control'] != "default" ? "" : "controls";
	            $video_loop = (isset($atts['video_loop']) && $atts['video_loop'] != 1) ? "loop" : "";
            }

            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if ($atts['title']):?>
                    <h3 class="block-title"><?php echo  esc_html($atts['title']);?></h3>
                <?php endif;?>
                <div class="urus_shortcode_video" <?php echo esc_attr($video_width) ?>>
                    <?php if($atts['url']): ?>
                        <?php if ($ytb_video == true): ?>
                            <iframe class="player" frameborder="0" data-videoId="<?php echo isset($ytb_video_id) ? esc_attr($ytb_video_id) : ""; ?>" src="<?php echo esc_url($atts['url']) ?>" <?php echo esc_attr($video_width." ".$video_height) ?> allowfullscreen></iframe>
                        <?php else: ?>
                            <video <?php echo esc_attr($video_width." ".$video_height." ".$show_control." ".$video_loop) ?> preload="auto">
                                <source src="<?php echo esc_url($atts['url']) ?>" type="<?php echo esc_attr($atts['source_type']) ?>">
                            </video>
                        <?php endif; ?>
                    <?php endif; ?>
	                <?php if (!empty($image) && isset($image['img'])): ?>
                        <a href="javascript:void(0);" class="video-background">
                            <?php echo Urus_Helper::escaped_html($image['img']);?>
                        </a>
	                <?php endif;  ?>
                    <?php if($atts['show_control'] != "default"): ?>
                        <div class="buttons">
                            <button class="play"><?php echo esc_html('Play', 'urus') ?></button>
                            <button class="pause"><?php echo esc_html('Pause', 'urus') ?></button>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <?php
            return apply_filters('urus_shortcode_video_output', ob_get_clean(), $atts, $content);
        }

        public function vc_map(){
            if (!function_exists('vc_map'))
                return false;


            $params    = array(
                'base'        => 'urus_video',
                'name'        => esc_html__( 'Video', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Short code video', 'urus' ),
                'params'      => array(
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Title', 'urus' ),
                        'param_name'  => 'title',
                        'admin_label' => true,
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Source video', 'urus' ),
                        'param_name'  => 'source',
                        'value'       => array(
                            esc_html("Youtube link", 'urus') => "youtube",
                            esc_html("External link", 'urus') => "custom",
                        ),
                        'admin_label'   =>  true,
                        "std"           => "youtube",
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Type of video', 'urus' ),
                        'param_name'  => 'source_type',
                        'value'       => array(
                            esc_html("MP4", 'urus') => "video/mp4",
                            esc_html("WebM", 'urus') => "video/webm",
                            esc_html("Ogg", 'urus') => "video/ogg",
                        ),
                        'admin_label'   =>  true,
                        "std"           => "video/mp4",
                        'dependency'  => array(
                            'element'            => 'source',
                            'value_not_equal_to' => array(
                                'youtube',
                            ),
                        ),
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'External link', 'urus' ),
                        'param_name'  => 'url',
                        'admin_label' => true,
                        'description' => esc_html__("Example for external link: http://example.com/video.mp4.  Example for youtube link: https://www.youtube.com/watch?v=KREnGJ1234", 'urus')
                    ),
                    array(
                        'type'        => 'attach_image',
                        'heading'     => esc_html__( 'Background Video', 'urus' ),
                        'param_name'  => 'background_img',
                        'admin_label' => true,
                        'value'       => '',
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Video width', 'urus' ),
                        'param_name'  => 'width',
                        'admin_label' => true,
                        'description' => esc_html__("Example: 100%, 1000px, 1000em, 1000rem (area unit of css)", "urus"),
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Show control video', 'urus' ),
                        'param_name'  => 'show_control',
                        'value'       => array(
                            esc_html("Control default", 'urus') => "default",
                            esc_html("Control custom", 'urus') => "custom",
                        ),
                        'admin_label'   =>  true,
                        "std"           => "default"
                    ),
                    array(
                        'type'        => 'checkbox',
                        'heading'     => esc_html__( 'Video loop', 'urus' ),
                        'param_name'  => 'video_loop',
                        'value'       => array(
                            esc_html("Video loop") => true
                        ),
                        'admin_label'   =>  true,
                    ),
                ),
            );

            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }

    }
}