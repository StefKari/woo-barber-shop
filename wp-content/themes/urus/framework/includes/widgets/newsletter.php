<?php
if( !class_exists('Urus_Widgets_Newsletter')){
    class Urus_Widgets_Newsletter extends Urus_Widgets{
        function __construct(){

            $this->widget_cssclass    = 'urus_widget_newsletter urus-newsletter';
            $this->widget_description = esc_html__( "Display Newsletter.", 'urus' );
            $this->widget_id          = 'urus_widget_newsletter';
            $this->widget_name        = esc_html__( 'Urus: Newsletter', 'urus' );

            $this->settings           = array(
                'title'  => array(
                    'type'  => 'text',
                    'std'   => esc_html__( 'GET 20% OFF', 'urus' ),
                    'label' => esc_html__( 'Title', 'urus' ),
                ),
                'subtitle'  => array(
                    'type'  => 'text',
                    'std'   => esc_html__( 'By subscribing to our newsletter', 'urus' ),
                    'label' => esc_html__( 'Sub Title', 'urus' ),
                ),
            );

            parent::__construct();

        }

        public function widget( $args, $instance ){
            $this->widget_start( $args, $instance );
            $subtitle = isset($instance['subtitle'])? $instance['subtitle'] :'';
            ?>
            <?php if($subtitle):?>
            <div class="subtitle"><?php echo esc_html($subtitle);?></div>
            <?php endif;?>
            <div class="urus-newsletter-form">
                <input type="email" name="email" class="form-field" placeholder="<?php echo esc_attr__('Enter your email','urus');?>">
                <button class="newsletter-form-button"><?php  esc_html_e('Sign up','urus');?></button>
            </div>
            <?php
            $this->widget_end( $args );
        }
    }
}