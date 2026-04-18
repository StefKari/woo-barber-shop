<?php
$post_class ='post-item col-sm-12';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?>>
    <div class="post-inner">
        <div class="info">
            <div class="post-item-head">
                
                <h3 class="post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
            </div>
            <div class="metas">
                <span class="author"><span class="by"><?php esc_html_e('By: ','urus');?></span><?php the_author();?></span>
                <span class="date"><?php echo get_the_date();?></span>
            </div>

            <div class="excerpt"><?php echo wp_trim_words(apply_filters('the_excerpt', get_the_excerpt()), 60, '...');?></div>
            <div class="post-footer">
                <a class="readmore" href="<?php the_permalink();?>"><span class="text"><?php esc_html_e('Read More','urus');?></span></a>

            </div>
        </div>
    </div>
    
</article>