<?php
    if( !class_exists('Urus_Elementor_Instagram_Feed')){
        class  Urus_Elementor_Instagram_Feed extends  Urus_Elementor{
            public $name ='instagram_feed';
            public $title ='Instagram Feed';
            public $icon ='eicon-product-categories';
            /**
             * Register the widget controls.
             *
             * Adds different input fields to allow the user to change and customize the widget settings.
             *
             * @since 1.0.0
             *
             * @access protected
             */
            protected function _register_controls() {
                $categories_array = array(
                    '' => esc_html__('All', 'urus')
                );
                $args = array();
                $categories = get_categories($args);
                foreach ($categories as $category) {
                    $categories_array[$category->slug] =$category->name;
                }
        
                $this->start_controls_section(
                    'content_section',
                    [
                        'label' => esc_html__( 'Content', 'urus' ),
                    ]
                );
                $this->add_control(
                    'layout',
                    [
                        'label' => esc_html__( 'Layout', 'urus' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'default' => esc_html__( 'Default', 'urus' ),
                        ],
                        'default' => 'default',
                    ]
                );
                
                $this->add_control(
                    'image_source',
                    [
                        'label' => esc_html__( 'Image Source', 'urus' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'instagram' => esc_html__( 'From Instagram', 'urus' ),
                            'gallery' => esc_html__( 'Gallery', 'urus' ),
                        ],
                        'default' => 'instagram',
            
                    ]
                );
                $this->add_control(
                    'image_gallery',
                    [
                        'label' => esc_html__( 'Image Gallery', 'urus' ),
                        'type' => \Elementor\Controls_Manager::GALLERY,
                        'default' => [],
                        'condition' => array(
                            'image_source' => 'gallery'
                        ),
                    ]
                );
                $this->add_control(
                    'image_resolution',
                    [
                        'label' => esc_html__( 'Image Resolution', 'urus' ),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'options' => [
                            'thumbnail' => esc_html__( 'Thumbnail', 'urus' ),
                            'low_resolution' => esc_html__( 'Low Resolution', 'urus' ),
                            'standard_resolution' => esc_html__( 'Standard Resolution', 'urus' ),
                        ],
                        'default' => 'thumbnail',
                        'condition' => array(
                            'image_source' => 'instagram'
                        ),
        
                    ]
                );
                $this->add_control(
                    'id_instagram',
                    [
                        'label' => esc_html__( 'ID Instagram', 'urus' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'placeholder' => esc_html__( 'Enter ID', 'urus' ),
                        'label_block'=> true,
                        'default' => '8513910764',
                        'condition' => array(
                            'image_source' => 'instagram'
                        ),

                    ]
                );
                $this->add_control(
                    'token',
                    [
                        'label' => esc_html__( 'Token', 'urus' ),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'placeholder' => esc_html__( 'Enter ID', 'urus' ),
                        'label_block'=> true,
                        'default' => '8513910764.1677ed0.c2b2498047774884869506a8171bcab0',
                        'description' => wp_kses( sprintf( '<a href="%s" target="_blank">' . esc_html__( 'Get Token Instagram Here!', 'urus' ) . '</a>', 'http://instagram.pixelunion.net' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
                        'condition' => array(
                            'image_source' => 'instagram'
                        ),
        
                    ]
                );
                $this->add_control(
                    'items_limit',
                    [
                        'label' => esc_html__( 'Items Limit', 'urus' ),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                        'default' => 10,
                        'description' => esc_html__('The number items show', 'urus'),
                        'condition' => array(
                            'image_source' => 'instagram'
                        ),
                    ]
                );
                
                $this->end_controls_section();
        
                /// Carousel Layout
                $this->start_controls_section(
                    'carousel_settings_section',
                    [
                        'label' => esc_html__( 'Carousel Settings', 'urus' ),
                    ]
                );
                $this->add_control(
                    'liststyle',
                    [
                        'label' => esc_html__( 'View', 'urus' ),
                        'type' => \Elementor\Controls_Manager::HIDDEN,
                        'default' => 'owl',
                    ]
                );
        
                $carousel_settings = Urus_Pluggable_Elementor::elementor_carousel('liststyle','owl');
        
                foreach ( $carousel_settings as $key => $value){
                    $this->add_control($key,$value);
                }
                $this->end_controls_section();
        
            }
            /**
             * Render the widget output on the frontend.
             *
             * Written in PHP and used to generate the final HTML.
             *
             * @since 1.0.0
             *
             * @access protected
             */
            protected function render() {
                $atts = $this->get_settings_for_display();
                $css_class    = array( 'urus-instagram nav-center' );
                $css_class[]  = isset( $atts['layout'] ) ? $atts['layout'] : '';
                extract( $atts );
                
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
        
            }
        }
    }
    