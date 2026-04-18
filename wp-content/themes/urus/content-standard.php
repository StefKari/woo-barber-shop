<?php
    $post_class ='post-item col-sm-12';
    global $wp_query;
    $word = 70;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?>>
    <div class="post-inner">
        <?php Urus_Helper::post_thumb();?>
        <div class="info">
            <div class="post-item-head">
                <span class="date">
                    <span class="day"><?php echo get_the_date('d');?></span>
                    <span class="month"><?php echo get_the_date('M');?></span>
                </span>
                <div class="post-categories">
                    <?php the_category(', ');?>
                </div>
                <h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
            </div>
            <div class="excerpt">
                <?php
                    if( Urus_Mobile_Detect::isMobile()){
                        echo wp_trim_words(apply_filters('the_excerpt', get_the_excerpt()), 30, '...');
                    }else{
                        echo wp_trim_words(apply_filters('the_excerpt', get_the_excerpt()), $word, '...');
                    }
                ?>
            </div>
            <div class="metas">
                <span class="author"><?php esc_html_e('By : ','urus'); ?><span class="name"><?php the_author();?></span></span>
                
                <span class="comment-count">
                <?php printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', 'urus' ), number_format_i18n( get_comments_number() ) );?>
                </span>
            </div>
            <div class="post-footer">
                <a class="readmore" href="<?php the_permalink();?>"><span class="text"><?php esc_html_e('Read More','urus');?></span></a>
            
            </div>
        </div>
    </div>
</article>