<?php
if( !class_exists('Urus_Shortcodes_Instagram')){
    class  Urus_Shortcodes_Instagram extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'instagram';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_instagram', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_instagram_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_instagram', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-instagram' );
            $css_class[]  = isset( $atts['layout'] ) ? $atts['layout'] : '';
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $css_class[]  = isset($atts['owl_nav_position']) ? $atts['owl_nav_position'] : "";;
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_instagram', $atts );
            ob_start();
            if ( $atts['image_source'] == 'instagram' ) {
                if ( intval( $atts['id_instagram'] ) === 0 || intval( $atts['token'] ) === 0 ) {
                    esc_html_e( 'No user ID specified.', 'urus' );
                }
                if ( !empty( $id_instagram ) && !empty( $token ) ) {
                    $response = wp_remote_get( 'https://api.instagram.com/v1/users/' . esc_attr( $id_instagram ) . '/media/recent/?access_token=' . esc_attr( $token ) . '&count=' . esc_attr( $atts['items_limit'] ) );
                    if ( !is_wp_error( $response ) ) {
                        $items         = array();
                        $response_body = json_decode( $response['body'] );
                        $response_code = json_decode( $response['response']['code'] );
                        if ( $response_code != 200 ) {
                            echo '<p>' . esc_html__( 'User ID and access token do not match. Please check again.', 'urus' ) . '</p>';
                        } else {
                            $items_as_objects = $response_body->data;
                            if ( !empty( $items_as_objects ) ) {
                                foreach ( $items_as_objects as $item_object ) {
                                    $item['link']     = $item_object->link;
                                    $item['user']     = $item_object->user;
                                    $item['likes']    = $item_object->likes;
                                    $item['comments'] = $item_object->comments;
                                    $item['src']      = $item_object->images->{$atts['image_resolution']}->url;
                                    $item['width']    = $item_object->images->{$atts['image_resolution']}->width;
                                    $item['height']   = $item_object->images->{$atts['image_resolution']}->height;
                                    $items[]          = $item;
                                }
                            }
                        }
                    }
                }
            } else {
                if ( $atts['image_gallery'] ) {
                    $instagram_list_class[] = 'urus-gallery-image';
                    $image_gallery          = explode( ',', $atts['image_gallery'] );
                    foreach ( $image_gallery as $image ) {
                        $image_thumb = wp_get_attachment_image_src( $image, 'full' );
                        $items[]     = array(
                            'link'   => $image_thumb[0],
                            'src'    => $image_thumb[0],
                            'width'  => $image_thumb[1],
                            'height' => $image_thumb[2],
                        );
                    }
                }
            }
            $owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if( isset($items) && !empty($items)):?>
                   
                    <div class="instagrams swiper-container urus-swiper " <?php echo esc_attr( $owl_settings ); ?>>
                        <div class="swiper-wrapper">
                            <?php foreach ( $items as $item ):
                                $enable_lazy = Urus_Helper::get_option('theme_use_lazy_load',0);
                                if($enable_lazy ==1){
                                    $img_lazy = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $item['width'] . "%20" . $item['height'] . "%27%2F%3E";
                                }else{
                                    $img_lazy = $item['src'];
                                }
                                ?>
                                <div class="swiper-slide">
                                    <div class="instagram-item">
                                        <a href="<?php echo esc_url( $item['link'] ) ?>" class="thumb">
                                            <figure>
                                                <img class="img-responsive lazy" src="<?php echo esc_attr( $img_lazy ); ?>"
                                                     data-src="<?php echo esc_url( $item['src'] ); ?>"
                                                    <?php echo image_hwstring( $item['width'], $item['height'] ); ?>
                                                     alt="<?php the_title_attribute(); ?>"/>
                                            </figure>
                                            <?php if($atts['image_source'] == 'instagram'):?>
                                                
                                                <span class="info">
                                                    <span class="urus-icon-instagram"></span>
                                                </span>
                                            <?php endif;?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <!-- If we need navigation buttons -->
                    <div class="slick-arrow next">
                        <?php echo familab_icons('arrow-right'); ?>
                    </div>
                    <div class="slick-arrow prev">
                        <?php echo familab_icons('arrow-left'); ?>
                    </div>
                <?php endif;?>
            </div>
            <?php
            return apply_filters('urus_shortcode_instagram_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_instagram',
                'name'        => esc_html__( 'Instagram', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Instagram feed', 'urus' ),
                'params'      => array(
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Layout', 'urus' ),
                        'param_name' => 'layout',
                        'value'      => array(
                            esc_html__( 'Default', 'urus' )   => 'default',
                        ),
                        'std'        => 'default',
                    ),
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Image Source', 'urus' ),
                        'param_name' => 'image_source',
                        'value'      => array(
                            esc_html__( 'From Instagram', 'urus' )   => 'instagram',
                            esc_html__( 'From Local Image', 'urus' ) => 'gallery',
                        ),
                        'std'        => 'instagram',
                    ),
                    array(
                        'type'       => 'attach_images',
                        'heading'    => esc_html__( 'Image Gallery', 'urus' ),
                        'param_name' => 'image_gallery',
                        'dependency' => array(
                            'element' => 'image_source',
                            'value'   => array( 'gallery' ),
                        ),
                    ),
                    array(
                        'type'       => 'dropdown',
                        'heading'    => esc_html__( 'Image Resolution', 'urus' ),
                        'param_name' => 'image_resolution',
                        'value'      => array(
                            esc_html__( 'Thumbnail', 'urus' )           => 'thumbnail',
                            esc_html__( 'Low Resolution', 'urus' )      => 'low_resolution',
                            esc_html__( 'Standard Resolution', 'urus' ) => 'standard_resolution',
                        ),
                        'std'        => 'thumbnail',
                        'dependency' => array(
                            'element' => 'image_source',
                            'value'   => array( 'instagram' ),
                        ),
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'ID Instagram', 'urus' ),
                        'param_name'  => 'id_instagram',
                        'admin_label' => true,
                        'dependency'  => array(
                            'element' => 'image_source',
                            'value'   => array( 'instagram' ),
                        ),
                        'std' => '8513910764'
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Token Instagram', 'urus' ),
                        'param_name'  => 'token',
                        'dependency'  => array(
                            'element' => 'image_source',
                            'value'   => array( 'instagram' ),
                        ),
                        'description' => wp_kses( sprintf( '<a href="%s" target="_blank">' . esc_html__( 'Get Token Instagram Here!', 'urus' ) . '</a>', 'http://instagram.pixelunion.net' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
                        'std' => '8513910764.1677ed0.c2b2498047774884869506a8171bcab0'
                    ),
                    array(
                        'type'        => 'number',
                        'heading'     => esc_html__( 'Items Instagram', 'urus' ),
                        'param_name'  => 'items_limit',
                        'description' => esc_html__( 'the number items show', 'urus' ),
                        'std'         => '10',
                        'dependency'  => array(
                            'element' => 'image_source',
                            'value'   => array( 'instagram' ),
                        ),
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'urus'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'urus' ),
                    ),
                ),
            );
            $params['params'] = array_merge(
                $params['params'],
                Urus_Pluggable_Visual_Composer::vc_carousel(  )
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}