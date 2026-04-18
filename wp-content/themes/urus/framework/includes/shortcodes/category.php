<?php
if( !class_exists('Urus_Shortcodes_Category')){
    class Urus_Shortcodes_Category extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'category';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_category', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_category_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_category', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-category' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_category', $atts );
            $category = get_term_by('slug',$atts['taxonomy'],'product_cat');
            $btn_shopnow = array('layout1', 'layout2', 'layout4', 'layout9', 'layout11');
            $list_cat = $atts['ids'];
            $list_cat_chil = array();
            if( !empty($category)){
                if( isset($atts['category_image']) && $atts['category_image'] >0){
                    $image = Urus_Helper::resize_image($atts['category_image'],false,false,true,true);
                }else{
                    $thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );
                    $image = Urus_Helper::resize_image($thumbnail_id,false,false,true,true);
                }
                $link = get_term_link($category);
            }
            if (!empty($list_cat)){
                $list_cat = explode(',', $list_cat);
                foreach ($list_cat as $key => $val){
                    $cat_tem = get_term_by('slug', $val,'product_cat');
                    if (empty($cat_tem)){
                        continue;
                    }
                    $term_link = get_term_link( $cat_tem->slug, 'product_cat' );
                    $cat_tem->term_link = $term_link;
                    array_push($list_cat_chil, $cat_tem);
                }
            }
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if(!empty( $category)):
                    $category_name = $category->name;
                    if( isset($atts['title']) && $atts['title'] !=''){
                        $category_name = $atts['title'];
                    }
                    ?>
                    <div class="product-category-item category-item">
                        <div class="inner">
                            <div class="thumb">
                                <a href="<?php echo esc_url($link);?>">
                                    <figure>
                                        <?php echo Urus_Helper::escaped_html($image['img']);?>
                                    </figure>
                                </a>
                            </div>
                            <div class="info">
	                            <?php if ($atts['layout'] == "layout4"): ?>
                                    <div class="info-inner">
                                <?php endif; ?>
                                    <h3 class="category-name">
                                        <a class="link__hover" href="<?php echo esc_url($link);?>">
                                            <?php echo esc_html($category_name);?>
                                        </a>
                                    </h3>
                                    <?php if ($atts['layout'] == "default"): ?>
                                        <div class="count"><?php echo esc_html($category->count).' ';?><?php esc_html_e('products','urus');?></div>
                                    <?php endif; ?>
                                    <?php if (in_array($atts['layout'], $btn_shopnow)): ?>
                                        <a href="<?php echo esc_url($link) ?>" title="<?php echo esc_html($category->name) ?>" class="button-link"><?php esc_html_e('Shop now','urus');?></a>
                                    <?php endif; ?>
                                <?php if ($atts['layout'] == "layout4"): ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($list_cat) && $atts['layout'] == "layout11"): ?>
                                <div class="list-cat-child">
                                    <ul class="inner-list">
	                                    <?php foreach ($list_cat_chil as $key => $val): ?>
                                            <li class="cat-item"><a href="<?php echo esc_url($val->term_link); ?>"><?php echo esc_html($val->name) ?></a></li>
	                                    <?php endforeach; ?>
                                    </ul>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif;?>
            </div>
            <?php
            return apply_filters('urus_shortcode_category_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){

            $params    = array(
                'base'        => 'urus_category',
                'name'        => esc_html__( 'Product Category', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display single category', 'urus' ),
                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'value'       => array(
                            esc_html__( 'Default', 'urus')      => 'default',
                            esc_html__( 'Layout 01', 'urus')    => 'layout1',
                            esc_html__( 'Layout 02', 'urus')    => 'layout2',
                            esc_html__( 'Layout 03', 'urus')    => 'layout3',
                            esc_html__( 'Layout 04', 'urus')    => 'layout4',
                            esc_html__( 'Layout 05', 'urus')    => 'layout5',
                            esc_html__( 'Layout 06', 'urus')    => 'layout6',
                            esc_html__( 'Layout 07', 'urus')    => 'layout7',
                            esc_html__( 'Layout 08', 'urus')    => 'layout8',
                            esc_html__( 'Layout 09', 'urus')    => 'layout9',
                            esc_html__( 'Layout 10', 'urus')    => 'layout10',
                            esc_html__( 'Layout 11', 'urus')    => 'layout11',
                        ),
                        'admin_label' => true,
                        'param_name'  => 'layout',
                        'description' => esc_html__('Select layout.', 'urus'),
                        'std'         => 'default'
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Title', 'urus' ),
                        'param_name'    => 'title',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'        => 'taxonomy',
                        'heading'     => esc_html__( 'Product Category', 'urus' ),
                        'param_name'  => 'taxonomy',
                        'options'     => array(
                            'multiple'   => false,
                            'hide_empty' => false,
                            'taxonomy'   => 'product_cat',
                        ),
                        'placeholder' => esc_html__( 'Choose category', 'urus' ),
                        'description' => esc_html__( 'Note: If you want to narrow output, select category(s) above. Only selected categories will be displayed.', 'urus' ),
                        'admin_label' => true,
                    ),
                    array(
                        "type"        => 'attach_image',
                        "heading"     => esc_html__('Image', 'urus'),
                        "param_name"  => 'category_image',
                        "value"       => '',
                        "description" => esc_html__( 'By default, the category image is select.', 'urus' )
                    ),
                    array(
	                    'type'        => 'taxonomy',
	                    'heading'     => esc_html__( 'List Child Of Category', 'urus' ),
	                    'param_name'  => 'ids',
	                    'options'     => array(
		                    'multiple'      => true,
		                    'sortable'      => true,
		                    'unique_values' => true,
		                    'taxonomy'   => 'product_cat',
	                    ),
	                    'save_always' => true,
	                    'description' => esc_html__( 'Enter List of Categories', 'urus' ),
	                    'dependency'  => array(
		                    'element' => 'layout',
		                    'value'   => array( 'layout11' ),
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
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}