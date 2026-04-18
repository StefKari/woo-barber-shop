<?php
if( !class_exists('Urus_Elementor_Blogs')){
    class Urus_Elementor_Blogs extends Urus_Elementor{
        public $name ='blogs';
        public $title ='Blogs';
        public $icon ='eicon-product-categories';
        /**
         * Register the widget controls.
         *
         * Adds different input fields to allow the user to change and customize the widget settings.
         *
         * @since 1.0.0
         *
         * @access protected
         */
        protected function _register_controls() {
            $categories_array = array(
                '' => esc_html__('All', 'urus')
            );
            $args = array();
            $categories = get_categories($args);
            foreach ($categories as $category) {
                $categories_array[$category->slug] =$category->name;
            }
            
            $this->start_controls_section(
                'content_section',
                [
                    'label' => esc_html__( 'Content', 'urus' ),
                ]
            );
            $this->add_control(
                'layout',
                [
                    'label' => esc_html__( 'Layout', 'urus' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => [
                        'default' => esc_html__( 'Default', 'urus' ),
                        'layout2' => esc_html__( 'Layout 01', 'urus' ),
                        'layout4' => esc_html__( 'Layout 02', 'urus' ),
                    ],
                    'default' => 'default',
                ]
            );
            $this->add_control(
                'target',
                [
                    'label' => esc_html__( 'Target', 'urus' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => [
                        'category' => esc_html__( 'Category', 'urus' ),
                        'posts' => esc_html__( 'Post(s)', 'urus' ),
                    ],
                    'default' => 'category',
                    
                ]
            );
            $this->add_control(
                'post_ids',
                [
                    'label' => esc_html__( 'Post IDs', 'urus' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'placeholder' => esc_html__( 'Enter IDs', 'urus' ),
                    'label_block'=> true,
                    'description' => esc_html__('Ex: 1,2,3,...', 'urus'),
                    'condition' => array(
                        'target' => 'posts'
                    ),
                ]
            );
            
            $this->add_control(
                'category_slug',
                [
                    'label' => esc_html__( 'Categorys', 'urus' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $categories_array,
                    'default' => '',
        
                ]
            );
            $this->add_control(
                'per_page',
                [
                    'label' => esc_html__( 'Number Post', 'urus' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                    'default' => 10,
                    'description' => esc_html__('Number post in a slide', 'urus'),
                    'condition' => array(
                        'target' => 'category'
                    ),
                ]
            );
            $this->add_control(
                'orderby',
                [
                    'label' => esc_html__( 'Orderby', 'urus' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => [
                        'none' => esc_html__('None', 'urus'),
                        'ID' => esc_html__('ID', 'urus'),
                        'author' => esc_html__('Author', 'urus'),
                        'name' => esc_html__('Name', 'urus'),
                        'date' => esc_html__('Date', 'urus'),
                        'modified' => esc_html__('Modified', 'urus'),
                         'rand' => esc_html__('Rand', 'urus'),
                    ],
                    'default' => 'none',
                    'condition' => array(
                        'target' => 'category'
                    ),
        
                ]
            );
            $this->add_control(
                'order',
                [
                    'label' => esc_html__( 'Order', 'urus' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => [
                        'ASC' => esc_html__('ASC', 'urus'),
                        'DESC' => esc_html__('DESC', 'urus'),
                    ],
                    'default' => 'DESC',
                    'condition' => array(
                        'target' => 'category'
                    ),
        
                ]
            );
            $this->end_controls_section();
    
            /// Carousel Layout
            $this->start_controls_section(
                'carousel_settings_section',
                [
                    'label' => esc_html__( 'Carousel Settings', 'urus' ),
                ]
            );
            $this->add_control(
                'liststyle',
                [
                    'label' => esc_html__( 'View', 'urus' ),
                    'type' => \Elementor\Controls_Manager::HIDDEN,
                    'default' => 'owl',
                ]
            );
    
            $carousel_settings = Urus_Pluggable_Elementor::elementor_carousel('liststyle','owl');
    
            foreach ( $carousel_settings as $key => $value){
                $this->add_control($key,$value);
            }
            $this->end_controls_section();
        
        }
        /**
         * Render the widget output on the frontend.
         *
         * Written in PHP and used to generate the final HTML.
         *
         * @since 1.0.0
         *
         * @access protected
         */
        protected function render() {
            $atts = $this->get_settings_for_display();
            $css_class    = array( 'urus-blogs' );
            $css_class[] = $atts['layout'];
    
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
            ?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <?php if( $posts->have_posts()):?>
                    <?php if( $atts['layout'] =='layout2'):?>
                        <div class="slide-inner">
                            <div class="blog-list swiper-container urus-swiper nav-center" <?php echo esc_attr( $owl_settings ); ?> data-thumb=<?php echo esc_attr( $class_thumb ); ?>>
                                <div class="swiper-wrapper">
                                    <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                                        <?php
                                        $image_thumb = Urus_Helper::resize_image( get_post_thumbnail_id(), 357, 357, true,true );
                                        ?>
                                        <div class="swiper-slide">
                                            <article <?php post_class($class_item); ?>>
                                                <div class="post-thumb">
                                                    <a href="<?php the_permalink();?> ">
                                                        <figure><?php echo Urus_Helper::escaped_html($image_thumb['img']);?></figure>
                                                    </a>
                                                </div>
                                                <div class="info">
                                                    <h3 class="post-title "><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
                                                    <div class="metas">
                                                        <span class="date"><?php echo get_the_date();?></span>
                                                    </div>
                                                    <div class="excerpt">
                                                        <?php
                                                            echo wp_trim_words(apply_filters('the_excerpt', get_the_excerpt()), 15 , '...');
                                                        ?>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                            <!-- If we need navigation buttons -->
                            <div class="slick-arrow next">
                                <?php echo familab_icons('arrow-right'); ?>
                            </div>
                            <div class="slick-arrow prev">
                                <?php echo familab_icons('arrow-left'); ?>
                            </div>
                        </div>
                    <?php elseif( $atts['layout'] =='layout1'):?>
                        <div class="slide-inner">
                            <div class="blog-list swiper-container urus-swiper nav-center" <?php echo esc_attr( $owl_settings ); ?>  data-thumb=<?php echo esc_attr( $class_thumb ); ?>>
                                <div class="swiper-wrapper">
                                    <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                                        <?php
                                        $image_thumb = Urus_Helper::resize_image( get_post_thumbnail_id(), 700, 784, true,true );
                                        ?>
                                        <div class="swiper-slide">
                                            <article <?php post_class($class_item); ?>>
                                                <div class="post-thumb">
                                                    <a href="<?php the_permalink();?> ">
                                                        <figure><?php echo Urus_Helper::escaped_html($image_thumb['img']);?></figure>
                                                    </a>
                                                    <span class="date">
                                                    <span class="day"><?php echo get_the_date('d');?></span>
                                                    <span class="month"><?php echo get_the_date('M');?></span>
                                                </span>
                                        
                                                </div>
                                                <div class="info">
                                                    <div class="metas">
                                                        <?php Urus_Helper::get_category();?>
                                                    </div>
                                                    <h3 class="post-title "><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
                                                </div>
                                            </article>
                                        </div>
                                    <?php endwhile; ?>
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
                        </div>
                    <?php else:?>
                        <div class="slide-inner">
                            <div class="blog-list swiper-container urus-swiper nav-center" <?php echo esc_attr( $owl_settings ); ?>  data-thumb=<?php echo esc_attr( $class_thumb ); ?>>
                                <div class="swiper-wrapper">
                                    <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                                        <?php
                                        $image_thumb = Urus_Helper::resize_image( get_post_thumbnail_id(), 500, 499, true,true );
                                        ?>
                                        <div class="swiper-slide">
                                            <article <?php post_class($class_item); ?>>
                                                <div class="post-thumb">
                                                    <a href="<?php the_permalink();?> ">
                                                        <figure><?php echo Urus_Helper::escaped_html($image_thumb['img']);?></figure>
                                            
                                                    </a>
                                                    <span class="date">
                                                <span class="day"><?php echo get_the_date('d');?></span>
                                                <span class="month"><?php echo get_the_date('M');?></span>
                                            </span>
                                        
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
                    <?php endif;?>
                <?php else:?>
                    <p><?php esc_html_e('No Post','urus');?></p>
                <?php endif;?>
            </div>
            <?php
            wp_reset_postdata();
        
        }
    }
}