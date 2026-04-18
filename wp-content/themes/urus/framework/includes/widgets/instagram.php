<?php
if( !class_exists('Urus_Widgets_Instagram')){
    class Urus_Widgets_Instagram extends Urus_Widgets{
        public function __construct() {
            $this->widget_cssclass    = 'urus-widget-instagram';
            $this->widget_description = esc_html__( "Display instagram.", 'urus' );
            $this->widget_id          = 'urus_widget_instagram';
            $this->widget_name        = esc_html__( 'Urus: Instagram', 'urus' );
            $this->settings           = array(
                'title'       => array(
                    'type'  => 'text',
                    'std'   => esc_html__( 'Instagram', 'urus' ),
                    'label' => esc_html__( 'Title', 'urus' ),
                ),
                'id_instagram'       => array(
                    'type'  => 'text',
                    'std'   => '8513910764',
                    'label' => esc_html__( 'ID Instagram', 'urus' ),
                ),
                'token'       => array(
                    'type'  => 'text',
                    'std'   => '8513910764.1677ed0.c2b2498047774884869506a8171bcab0',
                    'label' => esc_html__( 'Token Instagram', 'urus' ),
                ),
                'image_resolution'        => array(
                    'type'    => 'select',
                    'std'     => 'thumbnail',
                    'label'   => esc_html__( 'Image Resolution', 'urus' ),
                    'options' => array(
                        'thumbnail'         => esc_html__( 'Thumbnail', 'urus' ),
                        'low_resolution' => esc_html__( 'Low Resolution', 'urus' ),
                        'standard_resolution'   => esc_html__( 'Standard Resolution', 'urus' ),
                    ),
                ),
                'number'      => array(
                    'type'  => 'number',
                    'step'  => 1,
                    'min'   => 1,
                    'max'   => '',
                    'std'   => 6,
                    'label' => esc_html__( 'Number of products to show', 'urus' ),
                ),
            );
        
            parent::__construct();
        }
    
        public function widget( $args, $instance ){
            $this->widget_start( $args, $instance );
            $id_instagram = isset($instance['id_instagram'] ) ? $instance['id_instagram']  : 0;
            $token = isset($instance['token'] ) ? $instance['token']  :'';
            $number = isset($instance['number'] ) ? $instance['number']  :'';
            $image_resolution = isset($instance['image_resolution'] ) ? $instance['image_resolution']  :'thumbnail';
            if ( intval( $id_instagram) === 0 || intval( $token ) === 0 ) {
                esc_html_e( 'No user ID specified.', 'urus' );
            }
            if ( !empty( $id_instagram ) && !empty( $token ) ) {
                $response = wp_remote_get( 'https://api.instagram.com/v1/users/' . esc_attr( $id_instagram ) . '/media/recent/?access_token=' . esc_attr( $token ) . '&count=' . esc_attr($number ) );
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
                                $item['src']      = $item_object->images->{$image_resolution}->url;
                                $item['width']    = $item_object->images->{$image_resolution}->width;
                                $item['height']   = $item_object->images->{$image_resolution}->height;
                                $items[]          = $item;
                            }
                        }
                    }
                }
                if( isset($items) && !empty($items)){
                    ?>
                    <div class="instagrams clearfix">
                        <?php foreach ( $items as $item ):
                            $enable_lazy = Urus_Helper::get_option('theme_use_lazy_load',0);
                            if( $enable_lazy == 1){
                                $img_lazy = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . $item['width'] . "%20" . $item['height'] . "%27%2F%3E";
                            } else{
                                $img_lazy = $item['src'];
                            }
                            
                            
                            ?>
                            <div class="instagram-item">
                                <a href="<?php echo esc_url( $item['link'] ) ?>" class="thumb">
                                    <figure>
                                        <img class="img-responsive lazy" src="<?php echo esc_attr( $img_lazy ); ?>"
                                             data-src="<?php echo esc_url( $item['src'] ); ?>"
                                            <?php echo image_hwstring( $item['width'], $item['height'] ); ?>
                                             alt="<?php the_title_attribute(); ?>"/>
                                    </figure>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
                }
            }
            
            
            $this->widget_end( $args );
        }
    }
}