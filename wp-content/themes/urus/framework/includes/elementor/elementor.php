<?php
if (!class_exists('Urus_Elementor')){
    
    class  Urus_Elementor  extends \Elementor\Widget_Base {
        
        public $name ='';
        public $title ='';
        public $icon ='';
        public $categories = ['urus'];
        
        public function get_name() {
            return $this->name;
        }
    
        public function get_title() {
            return 'Urus: '.$this->title;
        }
    
        public function get_icon() {
            return $this->icon;
        }
    
        public function get_categories() {
            return $this->categories;
        }

	    public static function urus_elementer_icon(){
        	$icons = \Elementor\Control_Icon::get_icons();
        	$new_icons = array(
		        'urus-icon urus-icon-search' => 'Urus search',
		        'urus-icon urus-icon-heart' => 'Urus heart',
		        'urus-icon urus-icon-box' => 'Urus box',
		        'urus-icon urus-icon-shield' => 'Urus shield',
		        'urus-icon urus-icon-shield-1' => 'Urus shield 01',
		        'urus-icon urus-icon-shield-1-1' => 'Urus shield 02',
		        'urus-icon urus-icon-close' => 'Urus close',
		        'urus-icon urus-icon-plus' => 'Urus plus',
		        'urus-icon urus-icon-minus' => 'Urus minus',
		        'urus-icon urus-icon-play' => 'Urus play',
		        'urus-icon urus-icon-360' => 'Urus 360',
		        'urus-icon urus-icon-ruler' => 'Urus ruler',
		        'urus-icon urus-icon-full-screen' => 'Urus zoom',
		        'urus-icon urus-icon-compare' => 'Urus compare',
		        'urus-icon urus-icon-check' => 'Urus check',
		        'urus-icon urus-icon-prev' => 'Urus prev',
		        'urus-icon urus-icon-next' => 'Urus next',
		        'urus-icon urus-icon-down' => 'Urus down',
		        'urus-icon urus-icon-up' => 'Urus up',
		        'urus-icon urus-icon-prev-1' => 'Urus prev 01',
		        'urus-icon urus-icon-next-1' => 'Urus next 01',
		        'urus-icon urus-icon-up-1' => 'Urus up 01',
		        'urus-icon urus-icon-down-1' => 'Urus down 01',
		        'urus-icon urus-icon-down-2' => 'Urus down 02',
		        'urus-icon urus-icon-up-2' => 'Urus up 02',
		        'urus-icon urus-icon-prev-2' => 'Urus prev 02',
		        'urus-icon urus-icon-next-2' => 'Urus next 02',
		        'urus-icon urus-icon-prev-3' => 'Urus prev 03',
		        'urus-icon urus-icon-next-3' => 'Urus next 03',
		        'urus-icon urus-icon-up-3' => 'Urus up 03',
		        'urus-icon urus-icon-down-3' => 'Urus down 03',
		        'urus-icon urus-icon-up-4' => 'Urus up 04',
		        'urus-icon urus-icon-down-4' => 'Urus down 04',
		        'urus-icon urus-icon-prev-4' => 'Urus prev 04',
		        'urus-icon urus-icon-next-4' => 'Urus next 04',
		        'urus-icon urus-icon-up-5' => 'Urus up 05',
		        'urus-icon urus-icon-down-5' => 'Urus down 05',
		        'urus-icon urus-icon-prev-5' => 'Urus prev 05',
		        'urus-icon urus-icon-next-5' => 'Urus next 05',
		        'urus-icon urus-icon-cart' => 'Urus cart',
		        'urus-icon urus-icon-envelope' => 'Urus envelope',
		        'urus-icon urus-icon-envelope-1' => 'Urus envelope',
		        'urus-icon urus-icon-envelope-2' => 'Urus envelope 02',
		        'urus-icon urus-icon-user' => 'Urus user',
		        'urus-icon urus-icon-user-1' => 'Urus user 01',
		        'urus-icon urus-icon-reload' => 'Urus reload',
		        'urus-icon urus-icon-grid' => 'Urus grid',
		        'urus-icon urus-icon-list' => 'Urus list',
		        'urus-icon urus-icon-filter' => 'Urus filter',
		        'urus-icon urus-icon-filter-1' => 'Urus filter 01',
		        'urus-icon urus-icon-lock' => 'Urus lock',
		        'urus-icon urus-icon-percentage' => 'Urus percentage',
		        'urus-icon urus-icon-pause' => 'Urus pause',
		        'urus-icon urus-icon-customer' => 'Urus customer',
		        'urus-icon urus-icon-customer-1' => 'Urus customer 01',
		        'urus-icon urus-icon-return' => 'Urus return',
		        'urus-icon urus-icon-return-1' => 'Urus return 01',
		        'urus-icon urus-icon-table' => 'Urus table',
		        'urus-icon urus-icon-close-1' => 'Urus close 01',
		        'urus-icon urus-icon-diamon' => 'Urus diamond',
		        'urus-icon urus-icon-diamond-1' => 'Urus diamond 01',
		        'urus-icon urus-icon-diamon-2' => 'Urus diamond 02',
		        'urus-icon urus-icon-gift' => 'Urus gift',
		        'urus-icon urus-icon-instagam' => 'Urus instagram',
		        'urus-icon urus-icon-instagram-1' => 'Urus instagram 01',
		        'urus-icon urus-icon-refund' => 'Urus refund',
		        'urus-icon urus-icon-shirt' => 'Urus shirt',
		        'urus-icon urus-icon-truck' => 'Urus truck',
		        'urus-icon urus-icon-truck-1' => 'Urus truck 01',
		        'urus-icon urus-icon-truck-2' => 'Urus truck 02',
		        'urus-icon urus-icon-air' => 'Urus air',
		        'urus-icon urus-icon-clock' => 'Urus clock',
		        'urus-icon urus-icon-phone' => 'Urus phone',
		        'urus-icon urus-icon-point' => 'Urus point',
		        'urus-icon urus-icon-garantia' => 'Urus garantia',
		        'urus-icon urus-icon-quote' => 'Urus quote',
		        'urus-icon urus-icon-exit' => 'Urus exit',
		        'urus-icon urus-icon-line' => 'Urus line',
	        );
        	$arr = array_merge($new_icons, $icons);
		    return $arr;
	    }
    }
}