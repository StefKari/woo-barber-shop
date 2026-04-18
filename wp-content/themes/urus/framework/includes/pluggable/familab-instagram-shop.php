<?php
 if( !class_exists('Urus_Pluggable_Familab_Instagram_Shop')){
     class  Urus_Pluggable_Familab_Instagram_Shop{
         /**
          * Variable to hold the initialization state.
          *
          * @var  boolean
          */
         protected static $initialized = false;
         /**
          * Initialize pluggable functions for Visual Composer.
          *
          * @return  void
          */
         public static function initialize() {
             // Do nothing if pluggable functions already initialized.
             if ( self::$initialized ) {
                 return;
             }
             
             // State that initialization completed.
             self::$initialized = true;
         }
     }
 }