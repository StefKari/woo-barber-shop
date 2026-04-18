<?php
if( !class_exists('Urus_Elementor_Countdown')){
    
    class  Urus_Elementor_Countdown extends  Urus_Elementor{
        public $name ='urus_countdown';
        public $title ='Countdown';
        public $icon ='eicon-countdown';
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
        
        
            $this->start_controls_section(
                'content_section',
                [
                    'label' => esc_html__( 'Content', 'urus' ),
                ]
            );
            $this->add_control(
                'time',
                [
                    'label' => esc_html__( 'Time', 'urus' ),
                    'type' => \Elementor\Controls_Manager::DATE_TIME,
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
            $css_class    = array( 'urus-shortcode-countdown' );
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="urus-countdown" data-datetime="<?php echo date( 'm/j/Y g:i:s', strtotime($atts['time']) ); ?>"></div>
            </div>
            <?php
        
        }
    }
    
}