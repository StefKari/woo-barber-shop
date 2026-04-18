<?php
    
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    $enable_post_related = Urus_Helper::get_option('enable_post_related',0);
    if($enable_post_related == 0 ) return;
    
    global $post;
    
    $tags = wp_get_post_tags( $post->ID );
    if ( $tags ) {
        $tag_ids = array();
        foreach ( $tags as $tag ) {
            $tag_ids[] = $tag->term_id;
        }
        
        $args          = array(
            'tag__in'             => $tag_ids,
            'post__not_in'        => array( $post->ID ),
            'posts_per_page'      => 5,
            'ignore_sticky_posts' => 1
        );
        $related_query = new WP_Query( $args );
        if ( $related_query->have_posts() ) {
            $blog_layout = Urus_Helper::get_option('single_blog_layout','left');
            if( $blog_layout == 'full'){
                $atts = array(
                    'loop'         => 'false',
                    'ts_items'     => 1,
                    'xs_items'     => 2,
                    'sm_items'     => 3,
                    'md_items'     => 3,
                    'lg_items'     => 3,
                    'ls_items'     => 3,
                    'navigation'   => 'false',
                    'slide_margin' => 30,
                    'dots' => 'false'
                );
        
            }else{
                $atts = array(
                    'loop'         => 'false',
                    'ts_items'     => 1,
                    'xs_items'     => 2,
                    'sm_items'     => 3,
                    'md_items'     => 3,
                    'lg_items'     => 3,
                    'ls_items'     => 3,
                    'navigation'   => 'false',
                    'slide_margin' => 30,
                    'dots' => 'false'
                );
        
            }
    
            $atts = apply_filters('urus_related_post_carousel_settings',$atts);
            $carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
            ?>
            <div class="post__related__wapper">
                <div class="urus-section-title text-center">
                    <h3 class="title"><?php esc_html_e( 'Related Posts', 'urus' ); ?></h3>
                    <div class="subtitle"><?php esc_html_e('You may also like…','urus');?></div>
                </div>
                <div class="urus-blog urus-related-posts-wrap">
                    <div class="swiper-container urus-swiper nav-center" <?php echo esc_attr($carousel_settings);?>>
                        <div class="swiper-wrapper">
                            <?php
                                $post_class ='post-item';
                                while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                                    <div class="swiper-slide">
                                        <article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?>>
                                            <?php if( has_post_thumbnail()):?>
                                                <?php
                                                $image = Urus_Helper::resize_image(get_post_thumbnail_id(),440,363,true,true);
                                                ?>

                                                <div class="post-thumbnail">
                                                    <a href="<?php the_permalink();?>">
                                                        <?php echo Urus_Helper::escaped_html($image['img']);?>
                                                    </a>
                                                    <span class="date">
                                                        <span class="day"><?php echo get_the_date('d');?></span>
                                                        <span class="month"><?php echo get_the_date('M');?></span>
                                                    </span>
                                                </div>
                                            <?php endif;?>
                                            <div class="info">
                                                <div class="metas">
                                                    <?php Urus_Helper::get_category();?>
                                                </div>
                                                <h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
                                                <a class="readmore" href="<?php the_permalink();?>"><span class="text"><?php esc_html_e('Read More','urus');?></span><i class="arrow urus-icon-next-1"></i></a>
                                            </div>
                                        </article>
                                    </div>
                                <?php
                                endwhile;
                            ?>
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
            </div>
            
            <?php
        }
        wp_reset_postdata();
    }