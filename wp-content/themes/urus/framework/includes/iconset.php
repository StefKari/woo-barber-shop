<?php
if( !class_exists('Urus_iconset')){
	class Urus_iconset{
		public static $icons = array();
		protected static $initialized = false;
		public static function initialize() {
			if ( self::$initialized ) {
				return;
			}
			self::setIcons();
			self::$initialized = true;
		}
		public static function setIcons(){
		    self::$icons = array(
                'arrow-top' => '<i class="urus-icon urus-icon-up"></i>',
                'arrow-bottom' => '<i class="urus-icon urus-icon-down"></i>',
                'arrow-left' => '<i class="urus-icon urus-icon-prev"></i>',
                'arrow-right' => '<i class="urus-icon urus-icon-next"></i>',
                'arrow-left2' => '<i class="urus-icon urus-icon-prev-1"></i>',
                'arrow-right2' => '<i class="urus-icon urus-icon-next-1"></i>',
                'arrow-left3' => '<i class="urus-icon urus-icon-prev-1"></i>',
                'arrow-right3' => '<i class="urus-icon urus-icon-next-1"></i>',
                
                'user' => '<i class="urus-icon urus-icon-user"></i>',
                'list' => '<i class="urus-icon urus-icon-list"></i>',
                'heart' => '<i class="urus-icon urus-icon-heart"></i>',
                'currency' => '',
                'cart' => '<i class="urus-icon urus-icon-cart"></i>',
                
                'instagram' => '<i class="urus-icon urus-icon-instagam"></i>',
                'menu' => '<i class="urus-icon urus-icon-bar"></i>',
                'truck' => '',
                'hammer' => '',
                'instagram-circle' => '<i class="urus-icon urus-icon-instagam"></i>',
                'pattern' => '',
                
                'search' => '<i class="urus-icon urus-icon-search"></i>',
                
                'box' => '<i class="urus-icon urus-icon-box"></i>',
                'close' => '<i class="urus-icon urus-icon-close"></i>',
                'grid' => '<i class="urus-icon urus-icon-grid"></i>',
                'filter' => '<i class="urus-icon urus-icon-filter"></i>',
                '360' => '<span class="urus-icon icon-urus-icon-360"></span>',
                'compare' => '<span class="urus-icon icon-urus-icon-compare"></span>',
                'back'	=> '<i class="urus-icon fa fa-arrow-left"></i>',
            );
        }
	}
}
if (!function_exists('familab_icons')){
    function familab_icons ($icon){
        $familab_icons = Urus_iconset::$icons;
        if (isset($familab_icons[$icon])) {
            return $familab_icons[$icon];
        }else{
            return  esc_html('Icon not found ('.$icon.')');
        }
    }
}
