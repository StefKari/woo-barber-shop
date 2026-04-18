<?php
$post_class =array('post-item grid__item') ;
$blog_layout = Urus_Helper::get_option('blog_layout','left');
if( $blog_layout == 'full'){
    $post_class[] ='col-lg-4 col-md-6 col-sm-6 col-ts-12 col-xs-12';
}else{
    $post_class[] ='col-lg-6 col-md-6 col-sm-6 col-ts-12 col-xs-12';
}
$word = 30;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?>>
    <div class="post-inner">
    <?php Urus_Helper::post_thumb();?>
    <div class="info">
            <div class="post-item-head">
                
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
        <div class="post-footer">
                <a class="readmore" href="<?php the_permalink();?>"><span class="text"><?php esc_html_e('Read More','urus');?></span><i class="arrow urus-icon-arrow-right"></i></a>
            </div>
        </div>
    </div>
</article>