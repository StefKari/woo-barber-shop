<?php
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Utils;
use \Elementor\Group_Control_Background;

if( !class_exists('Urus_Elementor_Newsletter')){
    class Urus_Elementor_Newsletter extends Urus_Elementor {
        public $name ='newsletter';
        public $title ='Newsletter';
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
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'default' => esc_html__( 'Default', 'urus' ),
                        'layout1' => esc_html__( 'Layout 01', 'urus' ),
                        'layout2' => esc_html__( 'Layout 02', 'urus' ),
                        'layout3' => esc_html__( 'Layout 03', 'urus' ),
                        'layout4' => esc_html__( 'Layout 04', 'urus' ),
                        'layout5' => esc_html__( 'Layout 05', 'urus' ),
                        'layout6' => esc_html__( 'Layout 06', 'urus' ),
                        'layout7' => esc_html__( 'Layout 07', 'urus' ),
                        'layout8' => esc_html__( 'Layout 08', 'urus' ),
                        'layout9' => esc_html__( 'Layout 09', 'urus' ),
                        'layout10' => esc_html__( 'Layout 10', 'urus' ),
                        'layout11' => esc_html__( 'Layout 11', 'urus' ),
                        'layout12' => esc_html__( 'Layout 12', 'urus' ),
                    ],
                    'default' => 'default',
                    'label_block'=> true
                ]
            );
            $this->add_control(
                'title',
                [
                    'label' => esc_html__( 'Title', 'urus' ),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__( 'Enter your title', 'urus' ),
                    'label_block'=> true
                ]
            );
            $this->add_control(
                'subtitle',
                [
                    'label' => esc_html__( 'Subtitle', 'urus' ),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__( 'Enter your title', 'urus' ),
                    'label_block'=> true
                    
                ]
            );
            $this->add_control(
                'placeholder',
                [
                    'label' => esc_html__( 'Placeholder', 'urus' ),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__( 'Enter your title', 'urus' ),
                    'default' => esc_html__('Enter your email address','urus'),
                    'label_block'=> true
                ]
            );
            $this->add_control(
                'button_text',
                [
                    'label' => esc_html__( 'Button Text', 'urus' ),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__( 'Enter your title', 'urus' ),
                    'default' => esc_html__('Sign up','urus'),
                    'label_block'=> true
                ]
            );
	        $this->add_group_control(
		        Group_Control_Background::get_type(),
		        [
			        'name' => 'background',
			        'label' => __( 'Background', 'urus' ),
			        'types' => [ 'classic', 'gradient', 'video' ],
			        'selector' => '{{WRAPPER}} .urus-newsletter',
                    'condition' => [
                            'layout' => array('layout3', 'layout4')
                    ]
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
            $css_class    = array( 'urus-newsletter' );
            $css_class[] = $atts['layout'];
	        $newsletter_inner = array('layout3', 'layout4');
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
		        <?php if(in_array($atts['layout'], $newsletter_inner)): ?>
                <div class="newsletter-inner">
			        <?php endif; ?>
			        <?php if($atts['title']):?>
                        <h3 class="title"><?php echo esc_html($atts['title']);?></h3>
			        <?php endif;?>
			        <?php if($atts['subtitle']):?>
                        <div class="subtitle"><?php echo esc_html($atts['subtitle']);?></div>
			        <?php endif;?>
                    <div class="urus-newsletter-form">
                        <input type="email" name="email" class="form-field" placeholder="<?php echo esc_attr($atts['placeholder']);?>">
                        <button class="newsletter-form-button"><?php echo esc_html($atts['button_text']);?></button>
                    </div>
			        <?php if(in_array($atts['layout'], $newsletter_inner)): ?>
                </div>
	        <?php endif; ?>
            </div>
            <?php
        
        }
    }
}