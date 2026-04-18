<?php
if( !class_exists('Urus_Elementor_Feature_Box')){
    class Urus_Elementor_Feature_Box extends Urus_Elementor{
        public $name ='feature_box';
        public $title ='Feature Box';
        public $icon ='eicon-icon-box';
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
                'layout',
                [
                    'label' => esc_html__( 'Layout', 'urus' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => [
                        'default' => esc_html__( 'Default', 'urus' ),
                        'layout1' => esc_html__( 'Layout 01', 'urus' ),
                        'layout2' => esc_html__( 'Layout 02', 'urus' ),
                        'layout3' => esc_html__( 'Layout 03', 'urus' ),
                        'layout4' => esc_html__( 'Layout 04', 'urus' ),
                        'layout5' => esc_html__( 'Layout 05', 'urus' ),
                    ],
                    'default' => 'default',
                    'label_block'=> true
                ]
            );
            $this->add_control(
                'icon',
                [
                    'label' => esc_html__( 'Icon', 'urus' ),
                    'type' => \Elementor\Controls_Manager::ICON,
                    'label_block'=> true,
                    'options' => $this->urus_elementer_icon(),
                ]
            );
            $this->add_control(
                'title',
                [
                    'label' => esc_html__( 'Title', 'urus' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'placeholder' => esc_html__( 'Enter your text', 'urus' ),
                    'label_block'=> true
                ]
            );
            $this->add_control(
                'subtitle',
                [
                    'label' => esc_html__( 'Subtitle', 'urus' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'placeholder' => esc_html__( 'Enter your text', 'urus' ),
                    'label_block'=> true
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
            $css_class    = array( 'urus-feature-box' );
            $css_class[] = $atts['layout'];
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="icon">
                    <span class="<?php echo esc_attr($atts['icon']);?>"></span>
                </div>
                <div class="content">
                    <?php if( $atts['title']):?>
                        <h3 class="title"><?php echo esc_html( $atts['title']);?></h3>
                    <?php endif;?>
                    <?php if( $atts['subtitle']):?>
                        <div class="subtitle"><?php echo esc_html( $atts['subtitle']);?></div>
                    <?php endif;?>
                </div>
            </div>
            <?php
        }
    }
}