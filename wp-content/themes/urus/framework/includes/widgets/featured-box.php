<?php
if( !class_exists('Urus_Widgets_Featured_Box')){
    class Urus_Widgets_Featured_Box extends Urus_Widgets{
        public function __construct(){
            
            $this->widget_cssclass    = 'urus_widget_featured_box urus-featured-box ';
            $this->widget_description = esc_html__( 'Display a custom box.', 'urus' );
            $this->widget_id          = 'urus_widget_featured_box';
            $this->widget_name        = esc_html__( 'Urus: Featured Box', 'urus' );
    
            $this->settings           = array(
                'box_title'  => array(
                    'type'  => 'text',
                    'std'   => '',
                    'label' => esc_html__( 'Box Title', 'urus' ),
                ),
                'icon_class'  => array(
                    'type'  => 'text',
                    'std'   => '',
                    'label' => esc_html__( 'Icon Class', 'urus' ),
                ),
                'content_text'  => array(
                    'type'  => 'textarea',
                    'std'   => '',
                    'label' => esc_html__( 'Content Text', 'urus' ),
                ),
    
                
            );
            
            parent::__construct();
        }
        public function widget( $args, $instance ){
            $this->widget_start( $args, $instance );
            $icon_class = isset($instance['icon_class'])? $instance['icon_class'] :'';
            $content_text = isset($instance['content_text'])? $instance['content_text'] :'';
            $box_title = isset($instance['box_title'])? $instance['box_title'] :'';
            ?>
            <div class="content-box">
                <?php if($icon_class):?>
                <div class="icon">
                    <span class="<?php echo esc_attr($icon_class);?>"></span>
                </div>
                <?php endif;?>
                <div class="content-text">
                    <h3 class="title"><?php echo esc_html($box_title);?></h3>
                    <div class="text"><?php echo esc_html($content_text);?></div>
                </div>
            </div>
            <?php
            $this->widget_end( $args );
        }
    }
}