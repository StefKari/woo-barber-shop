<?php
if( !class_exists('Urus_Elementor_Instagram_Shop')){
    class  Urus_Elementor_Instagram_Shop extends  Urus_Elementor{
        public $name ='instagram_shop';
        public $title ='Instagram Shop';
        public $icon ='eicon-featured-image';

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
            $args = array(
                'posts_per_page'   => -1,
                'offset'           => 0,
                'post_type'        => 'familab-instagram',
                'post_status'      => 'publish',
                'suppress_filters' => true,
            );
            $posts_array = get_posts( $args );
            $settings = array();
            if( !empty($posts_array)){
                foreach ($posts_array as $post){
                    $settings[$post->ID] = $post->post_title;
                }
            }
            wp_reset_postdata();
            $this->start_controls_section(
                'content_section',
                [
                    'label' => esc_html__( 'Content', 'urus' ),
                ]
            );
            $this->add_control(
                'instagram',
                [
                    'label' => esc_html__( 'Instagram', 'urus' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $settings,
                ]
            );

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
            $css_class    = array( 'urus-instagram-shop tis-vc' );
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php echo do_shortcode(Tis_Shortcode::get_shortcode_string($atts['instagram']));?>
            </div>
            <?php
        }
    }
}
