<?php
if( !class_exists('Urus_Widgets_Active_Filters')){
    class Urus_Widgets_Active_Filters extends Urus_Widgets{
        public function __construct() {
            $this->widget_cssclass    = 'urus-widget-active_filters';
            $this->widget_description = esc_html__( "Display active filters.", 'urus' );
            $this->widget_id          = 'urus_widget_active_filters';
            $this->widget_name        = esc_html__( 'Urus: Active Filters', 'urus' );
            $this->settings           = array( );
            
            parent::__construct();
        }
        
        public function widget( $args, $instance ){
            $this->widget_start( $args, $instance );
            Urus_Pluggable_WooCommerce::filter_active();
            $this->widget_end( $args );
        }
    }
}