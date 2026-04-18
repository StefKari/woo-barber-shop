<?php

if ( !class_exists( 'Urus_Widgets_Lastest_Post' ) ) {

    class Urus_Widgets_Lastest_Post extends Urus_Widgets{

        function __construct(){

            $this->widget_cssclass    = 'urus_widget_lastest_post';
            $this->widget_description = esc_html__( "Display the Post.", 'urus' );
            $this->widget_id          = 'urus_widget_lastest_post';
            $this->widget_name        = esc_html__( 'Urus: Lastest Posts', 'urus' );

            $all_categories = array(
                'all' => esc_html__('All Category','urus')
            );
            $categories = get_categories('hide_empty=0&depth=1&type=post');
            foreach ( $categories as $category){
                $all_categories[$category->term_id] = $category->cat_name;
            }

            $this->settings           = array(
                'title'  => array(
                    'type'  => 'text',
                    'std'   => esc_html__( 'Latest Posts', 'urus' ),
                    'label' => esc_html__( 'Title', 'urus' ),
                ),
                'categories'  => array(
                    'type'  => 'select',
                    'std'   => 'all',
                    'label' => esc_html__( 'Categories', 'urus' ),
                    'options' => $all_categories
                ),
                'number'  => array(
                    'type'  => 'text',
                    'std'   => 3,
                    'label' => esc_html__( 'Number of posts to show', 'urus' ),
                ),

            );

            parent::__construct();

        }

        public function widget( $args, $instance ){

            $this->widget_start( $args, $instance );
            $query        = array('showposts' => $instance['number'], 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'cat' => $instance['categories']);
            $loop         = new WP_Query($query);
            $width = 80;
            $height = 80;
            $crop = true;
            if( $loop->have_posts()){
                ?>
                <div class="list-post">
                <?php
                $i=0;
                while ($loop->have_posts()){
                    $thumb  ='';
                    $loop->the_post();
                    $i++;
                    $post_thumbnail_id = get_post_thumbnail_id(  );
                    $image = Urus_Helper::resize_image($post_thumbnail_id,$width,$height,$crop,true);
                    ?>
                    <div class="post">
                        <?php if( $image ):?>
                        <div class="thumb">
                            <?php echo Urus_Helper::escaped_html($image['img']);?>
                        </div>
                        <?php endif;?>
                        <div class="info">
                            <div class="metas">
                                <span class="time">
                                    <?php echo get_the_date();?>
                                </span>
                            </div>
                            <h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
                            <span class="comment-count">
                                <?php printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', 'urus' ), number_format_i18n( get_comments_number() ) );?>
                            </span>

                        </div>
                    </div>
                    <?php
                }
                ?>
                </div>
                <?php
            }
            wp_reset_postdata();
            $this->widget_end( $args );
        }
    }
}

