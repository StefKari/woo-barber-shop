<?php
if( !class_exists('Urus_Shortcodes_Blogs')){
    class  Urus_Shortcodes_Blogs extends Urus_Shortcodes{
        /**
         * Shortcode name.
         *
         * @var  string
         */
        public $shortcode = 'blogs';
        function __construct(){
            parent::__construct();
            add_action( 'vc_before_init', array($this, 'vc_map'));
        }

        static public function add_css_generate( $atts ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_blogs', $atts ) : $atts;
            // Extract shortcode parameters.
            extract( $atts );
            $css = '';
            return apply_filters( 'urus_shortcodes_title_css_render', $css, $atts );
        }
        public function output_html( $atts, $content = null ){
            $atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'urus_blogs', $atts ) : $atts;
            extract( $atts );
            $css_class    = array( 'urus-blogs' );
            $css_class[]  = $atts['el_class'];
            $css_class[]  = $atts['layout'];
            $css_class[]  = $atts['urus_custom_id'];
            $class_editor = isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : '';
            $css_class[]  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_editor, 'urus_blogs', $atts );
	        $nav_position = isset($atts['owl_nav_position']) ? $atts['owl_nav_position'] : "";
            $args = array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page'      => $atts['per_page'],
                'suppress_filter'     => true,
                'orderby'             => $atts['orderby'],
                'order'               => $atts['order']
            );
            //class thumb (css position arrows)
            $class_thumb = 'post-thumb';

            if( $atts['target'] == 'category'){
                /* Get category id*/
                if ( $atts['category_slug'] ) {
                    $idObj = get_category_by_slug($atts['category_slug'] );
                    if (is_object($idObj)) {
                        $args['cat'] = $idObj->term_id;
                    }
                }
            }
            if( $atts['target'] == 'posts'){
                $args['post__in'] = array_map( 'trim', explode( ',', $atts['post_ids'] ) );
                $args['orderby']  = 'post__in';
            }
            $posts = new WP_Query($args);

            $class_item = array('post-item');
            $owl_settings = Urus_Helper::carousel_data_attributes( 'owl_', $atts );
	        $owl_dots_style =  isset($atts['owl_dots_style']) ? $atts['owl_dots_style'] : "";
            ob_start();
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if( $posts->have_posts()):?>
                        <div class="slide-inner">
                            <div class="blog-list swiper-container urus-swiper <?php echo esc_attr($nav_position) ?>" data-dots-style="<?php echo esc_attr( $owl_dots_style ) ?>" <?php echo esc_attr( $owl_settings ); ?>  data-thumb=<?php echo esc_attr( $class_thumb ); ?>>
                                <div class="swiper-wrapper">
                                    <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                                        <?php
                                        $image_thumb = Urus_Helper::resize_image( get_post_thumbnail_id(), 500, 530, true,true );
                                        ?>
                                        <div class="swiper-slide">
                                            <article <?php post_class($class_item); ?>>
                                                <div class="post-thumb">
                                                    <?php if ($atts['layout'] == "layout4"): ?>
                                                        <div class="post-date">
                                                            <span class="post-day"><?php echo get_the_date("d") ?></span>
                                                            <span class="post-month"><?php echo get_the_date("M") ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <a href="<?php the_permalink();?> ">
                                                        <figure><?php echo Urus_Helper::escaped_html($image_thumb['img']);?></figure>

                                                    </a>
                                                </div>
                                                <div class="info">
                                                    <div class="metas">
                                                        <?php Urus_Helper::get_category();?>
                                                    </div>
                                                    <h3 class="post-title "><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
                                                </div>
                                                <div class="excerpt">
                                                    <?php
                                                        echo wp_trim_words(apply_filters('the_excerpt', get_the_excerpt()), 15 , '...');
                                                    ?>
                                                </div>
                                               <?php if ($atts['layout'] != "layout4"):  ?>
                                                   <a class="read-more" href="<?php the_permalink();?> "><?php echo esc_html('Read more', 'urus') ?></a>
                                                <?php endif; ?>
                                            </article>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                            <div class="slick-arrow next">
                                <?php echo familab_icons('arrow-right'); ?>
                            </div>
                            <div class="slick-arrow prev">
                                <?php echo familab_icons('arrow-left'); ?>
                            </div>
                        </div>
                <?php else:?>
                    <p><?php esc_html_e('No Post','urus');?></p>
                <?php endif;?>
            </div>
            <?php
            wp_reset_postdata();
            return apply_filters('urus_shortcode_blogs_output', ob_get_clean(), $atts, $content);
        }
        public function vc_map(){
            $categories_array = array(
                esc_html__('All', 'urus') => ''
            );
            $args = array();
            $categories = get_categories($args);
            foreach ($categories as $category) {
                $categories_array[$category->name] = $category->slug;
            }

            $params    = array(
                'base'        => 'urus_blogs',
                'name'        => esc_html__( 'Blogs', 'urus' ),
                'icon'        => URUS_THEME_URI. 'assets/images/admin/vc_icon.svg',
                'category'    => esc_html__( 'Urus Elements', 'urus' ),
                'description' => esc_html__( 'Display Blogs list', 'urus' ),
                'params'      => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Layout', 'urus' ),
                        'param_name'  => 'layout',
                        'value'       => array(
                            esc_html__( 'Default', 'urus' ) => 'default',
                            esc_html__( 'Layout 01', 'urus' ) => 'layout2',
                            esc_html__( 'Layout 02', 'urus' ) => 'layout4',

                        ),
                        'std'         => 'default',
                    ),

                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Target', 'urus' ),
                        'param_name'  => 'target',
                        'value'       => array(
                            esc_html__( 'Category', 'urus' ) => 'category',
                            esc_html__( 'Post(s)', 'urus' )    => 'posts',
                        ),
                        'std'         => 'category',
                        'admin_label'   => true,
                    ),
                    array(
                        'type'          => 'textfield',
                        'heading'       => esc_html__( 'Post IDs', 'urus' ),
                        'param_name'    => 'post_ids',
                        'admin_label'   => true,
                        'description' => esc_html__('Ex: 1,2,3,...', 'urus'),
                        'dependency'  => array(
                            'element' => 'target',
                            'value'   => array( 'posts' ),
                        ),
                    ),
                    array(
                        'param_name'  => 'category_slug',
                        'type'        => 'dropdown',
                        'value'       => $categories_array, // here I'm stuck
                        'heading'     => esc_html__('Category', 'urus'),
                        "admin_label" => true,
                        'dependency'  => array(
                            'element' => 'target',
                            'value'   => array( 'category' ),
                        ),
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__('Number Post', 'urus'),
                        'param_name'  => 'per_page',
                        'std'         => 10,
                        'admin_label' => true,
                        'description' => esc_html__('Number post in a slide', 'urus'),
                        'dependency'  => array(
                            'element' => 'target',
                            'value'   => array( 'category' ),
                        ),
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading"     => esc_html__("Order by", 'urus'),
                        "param_name"  => "orderby",
                        "value"       => array(
                            esc_html__('None', 'urus')     => 'none',
                            esc_html__('ID', 'urus')       => 'ID',
                            esc_html__('Author', 'urus')   => 'author',
                            esc_html__('Name', 'urus')     => 'name',
                            esc_html__('Date', 'urus')     => 'date',
                            esc_html__('Modified', 'urus') => 'modified',
                            esc_html__('Rand', 'urus')     => 'rand',
                        ),
                        'std'         => 'date',
                        "description" => esc_html__("Select how to sort retrieved posts.", 'urus'),
                        'dependency'  => array(
                            'element' => 'target',
                            'value'   => array( 'category' ),
                        ),
                    ),
                    array(
                        "type"        => "dropdown",
                        "heading"     => esc_html__("Order", 'urus'),
                        "param_name"  => "order",
                        "value"       => array(
                            esc_html__('ASC', 'urus')  => 'ASC',
                            esc_html__('DESC', 'urus') => 'DESC'
                        ),
                        'std'         => 'DESC',
                        "description" => esc_html__("Designates the ascending or descending order.", 'urus'),
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
                Urus_Pluggable_Visual_Composer::vc_carousel()
            );
            $params = apply_filters($this->shortcode.'_shortcode_setup', $params);
            vc_map( $params );
        }
    }
}