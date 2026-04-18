<?php
if( !class_exists('Urus_Widgets_Filter_Orderby')){
    class Urus_Widgets_Filter_Orderby extends Urus_Widgets{
        public function __construct(){
            $this->widget_cssclass    = 'urus_widget_orderby_filter';
            $this->widget_description = esc_html__( 'Orderby Product list .', 'urus' );
            $this->widget_id          = 'urus_widget_orderby_filter';
            $this->widget_name        = esc_html__( 'Urus: Filter Sort by', 'urus' );
            $this->settings           = array(
                'title' => array(
                    'type'  => 'text',
                    'std'   => esc_html__( 'Sort by', 'urus' ),
                    'label' => esc_html__( 'Title', 'urus' ),
                ),
            );

            parent::__construct();
        }
        public function widget( $args, $instance ){
            $this->widget_start( $args, $instance );
            $catalog_orderby_options = Urus_Pluggable_WooCommerce::get_catalog_ordering();
            $current_page_url = Urus_Pluggable_WooCommerce::get_current_page_url();
            if( !empty($catalog_orderby_options)){
                ?>
                <ul class="filter-order-by filter-links">
                    <?php
                    $query_string = apply_filters('urus_widget_current_page_url',array());
                    $curent_url = add_query_arg($query_string,$current_page_url);
                    ?>
                    <?php foreach( $catalog_orderby_options['options'] as $key =>$value):?>
                        <?php
                        if( $key==$catalog_orderby_options['selected'] ) $class ='selected'; else $class ='';
                        $orderby_args = array('orderby' => $key);
                        $item_link =  add_query_arg($orderby_args,$curent_url);
                        ?>
                        <li class="<?php echo esc_attr( $class );?>">
                            <a class="order-by" data-value="<?php echo esc_attr($key);?>" href="<?php echo esc_url($item_link);?>"><?php echo esc_html($value);?></a>
                        </li>
                    <?php endforeach;?>
                </ul>
                <?php
            }

            $this->widget_end( $args );
        }
    }
}