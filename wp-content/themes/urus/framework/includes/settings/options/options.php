<?php
if(!class_exists('Urus_Settings_Options')){

    class Urus_Settings_Options{


        public static function get_sidebars(){

            global $wp_registered_sidebars;

            $sidebars =  array();

            foreach ( $wp_registered_sidebars as $sidebar ){
                $sidebars[  $sidebar['id'] ] =   $sidebar['name'];
            }

            return $sidebars;
        }

        public static function get_attributes(){
            $attributes = array();
            if( function_exists('wc_get_attribute_taxonomies')){
                $taxonomies = wc_get_attribute_taxonomies();
                if( !empty($taxonomies)){
                    foreach ( $taxonomies as $taxonomie){
                        $attributes[$taxonomie->attribute_name] = $taxonomie->attribute_label;
                    }
                }
            }
            return $attributes;
        }
        public static function get_socials(){
            $socials = array();
            $all_socials = Urus_Helper::get_all_social();

            foreach ( $all_socials  as $social ){
                $socials[$social['id']] = $social['name'];
            }
            return $socials;
        }
        public static function get_product_category(){
            $cats = array();
            $args = array(
                'hide_empty' => 0,
            );
            $categories = Urus_Pluggable_WooCommerce::get_categories($args);
            if (!empty($categories)){
                foreach ($categories as $category){
                    $cats[$category->slug] = $category->name;
                }
            }
            return $cats;
        }
    }
}