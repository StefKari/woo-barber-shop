<?php
if( !class_exists('Urus_Mobile_Detect')){
    class  Urus_Mobile_Detect{
        public static $detect;
        public static $has_detect = false;
        public function __construct(){
            if( !class_exists('Mobile_Detect')){
                return;
            }
            self::$has_detect = true;
            self::$detect = new Mobile_Detect();
        }
        public static function isMobile(){
            global $urus_mobile;
            if (empty($urus_mobile)){
                $urus_mobile = false;
                if(self::$has_detect && self::$detect->isMobile() && !self::$detect->isTablet() ){
                    $urus_mobile = true;
                }
                $GLOBALS['urus_mobile'] = $urus_mobile;
            }
            return $urus_mobile;
        }
        public static function isTablet(){
            global $urus_tablet;
            if (empty($urus_tablet)){
                $urus_tablet = false;
                if( self::$has_detect && self::$detect->isTablet()  ){
                    $urus_mobile = true;
                }
                $GLOBALS['urus_tablet'] = $urus_tablet;
            }
            return $urus_tablet;
        }
    }
}