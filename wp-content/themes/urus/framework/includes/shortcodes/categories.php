<?php
if( !class_exists('Urus_Shortcodes_Categories')){
    class Urus_Shortcodes_Categories extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'categories';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_categories', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_categories', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-categories' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_categories', $atts );


            $args = array(
                'hide_empty' => 0,
                'orderby' => 'slug__in',
            );

            if( $atts['target']=="category" && $atts['product_cats']!=''){
                $args['slug'] = explode(',',$atts['product_cats']);
            }


            $categories = Urus_Pluggable_WooCommerce::get_categories($args);

            $category_item_class   = array( 'product-category-item category-item' );

            $category_list_class = array();
            $owl_settings       = '';
	        $owl_dots_style =  isset($atts['owl_dots_style']) ? $atts['owl_dots_style'] : "";
            if ( $atts[ 'liststyle' ] == 'grid' ) {
                $category_list_class[] = 'product-category-list-grid row auto-clear equal-container better-height ';

                $category_item_class[] = $atts[ 'boostrap_rows_space' ];
                $category_item_class[] = 'col-bg-' . $atts[ 'boostrap_bg_items' ];
                $category_item_class[] = 'col-lg-' . $atts[ 'boostrap_lg_items' ];
                $category_item_class[] = 'col-md-' . $atts[ 'boostrap_md_items' ];
                $category_item_class[] = 'col-sm-' . $atts[ 'boostrap_sm_items' ];
                $category_item_class[] = 'col-xs-' . $atts[ 'boostrap_xs_items' ];
                $category_item_class[] = 'col-ts-' . $atts[ 'boostrap_ts_items' ];
            }
            if ( $atts[ 'liststyle' ] == 'owl' ) {

                $category_list_class[] = 'product-category-list-owl swiper-container urus-swiper nav-center';

                $category_item_class[] = $atts[ 'owl_rows_space' ];


                $owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
            }
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
               <?php if( !empty($categories)):?>
                   <?php if ( $atts[ 'liststyle' ] == 'grid' ): ?>
                       <ul class="<?php echo esc_attr( implode( ' ', $category_list_class ) ); ?>" >
                           
                             <?php foreach ($categories as $category):?>
                                 <?php
                                 $thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );
                                 $image = Urus_Helper::resize_image($thumbnail_id,270,300,true,true);
                                 $link = get_term_link($category);
                                 ?>
                                 <li class="<?php echo esc_attr( implode( ' ', $category_item_class ) ); ?>">
                                     <div class="inner">
                                         <div class="thumb">
                                             <a href="<?php echo esc_url($link);?>">
                                                 <figure>
                                                     <?php echo Urus_Helper::escaped_html($image['img']);?>
                                                 </figure>
                                                 <span class="count"><?php echo wp_specialchars_decode(sprintf(esc_html__('%s item(s)','urus'),$category->count));?></span>
                                             </a>
                                         </div>
                                         <div class="info">
                                             <h3 class="category-name"><a href="<?php echo esc_url($link);?>"><?php echo esc_html($category->name);?></a></h3>
                                         </div>
                                     </div>
                                 </li>
                             <?php endforeach;?>
                         </ul>

                       </div>
                   <?php elseif($atts[ 'liststyle' ] == 'owl'):?>
                       <div class="<?php echo esc_attr( implode( ' ', $category_list_class ) ); ?>" data-dots-style="<?php echo esc_attr( $owl_dots_style ) ?>" <?php echo esc_attr( $owl_settings ); ?>>
                          <div class="swiper-wrapper">
                            <?php foreach ($categories as $category):?>
                             <?php
                             $thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );
                             $image = Urus_Helper::resize_image($thumbnail_id,270,300,true,true);
                             $link = get_term_link($category);
                             ?>
                             <div class="<?php echo esc_attr( implode( ' ', $category_item_class ) ); ?>">
                                 <div class="inner">
                                     <div class="thumb">
                                         <a href="<?php echo esc_url($link);?>">
                                             <figure><?php echo Urus_Helper::escaped_html($image['img']);?></figure>
                                             <span class="count"><?php echo wp_specialchars_decode(sprintf(esc_html__('%s item(s)','urus'),$category->count));?></span>
                                         </a>
                                     </div>
                                     <div class="info">
                                         <h3 class="category-name"><a href="<?php echo esc_url($link);?>"><?php echo esc_html($category->name);?></a></h3>
                                     </div>
                                 </div>
                             </div>
                            <?php endforeach;?>
                          </div>
                          <!-- If we need pagination -->
                            <div class="swiper-pagination"></div>
                       </div>
                       <!-- If we need navigation buttons -->
                        <div class="slick-arrow next">
                            <?php echo familab_icons('arrow-right'); ?>
                        </div>
                        <div class="slick-arrow prev">
                            <?php echo familab_icons('arrow-left'); ?>
                        </div>
                    <?php endif;?>
               <?php else:?>
                   <p>
                       <strong><?php esc_html_e( 'No Categories', 'urus' ); ?></strong>
                   </p>
               <?php endif;?>
            </div>
            <?php
            return apply_filters('urus_shortcode_categories_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_categories',
                'name'        => esc_html__( 'Product Categories', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Category list', 'urus' ),
                'params'      => array(
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Product List style', 'urus' ),
                        'param_name'  => 'liststyle',
                        'value'       => array(
                            esc_html__( 'Grid Layout', 'urus' )       => 'grid',
                            esc_html__('Owl Carousel Layout', 'urus') => 'owl',
                        ),
                        'description' => esc_html__( 'Select a style for list', 'urus' ),
                        'std'         => 'grid',
                        'admin_label'   => true,
                    ),

                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Target', 'urus' ),
                        'param_name'  => 'target',
                        'value'       => array(
                            esc_html__( 'All', 'urus' ) => 'all',
                            esc_html__( 'Category(s)', 'urus' )    => 'category',
                        ),
                        'std'         => 'all',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'autocomplete',
                        'heading'     => esc_html__( 'Category(s)', 'urus' ),
                        'param_name'  => 'product_cats',
                        'settings'    => array(
                            'multiple'      => true,
                            'sortable'      => true,
                            'unique_values' => true,
                        ),
                        'save_always' => true,
                        'description' => esc_html__( 'Enter List of Category', 'urus' ),
                        'dependency'  => array(
                            'element' => 'target',
                            'value'   => array( 'category' ),
                        ),

                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__('CSS box', 'urus'),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design Options', 'urus' ),
                    ),
                ),
            );
            $params['params'] = array_merge(
                $params['params'],
                Urus_Pluggable_Visual_Composer::vc_carousel( 'liststyle', 'owl' ),
                Urus_Pluggable_Visual_Composer::vc_bootstrap( 'liststyle', 'grid' )
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}