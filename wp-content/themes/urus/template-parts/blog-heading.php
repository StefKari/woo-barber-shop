<?php

$blog_heading_style = Urus_Helper::get_option('blog_heading_style','banner');
$page_heading_background_css ='';
$blog_heading_background = Urus_Helper::get_option('blog_heading_background',array());

if( is_page()) {
    $page_id = get_the_ID();
}
if (!is_front_page() && is_home()){
    $page_id = get_option( 'page_for_posts' );
}
$key_prefix = 'background-';
if (isset($page_id) && $page_id){
    $enable_page_option = Urus_Helper::get_post_meta($page_id,'enable_page_option',false);
    if ($enable_page_option) {
    $page_heading_background = Urus_Helper::get_post_meta($page_id, 'page_heading_background', array());
        $blog_heading_style =  Urus_Helper::get_post_meta($page_id,'page_heading_style',false);
}
}
if (isset($page_heading_background) && !empty($page_heading_background)){
    $page_bg = false;
    foreach ($page_heading_background as $v){
        if ($v != ''){
            $page_bg = true;
        }
    }
    if ($page_bg){
        $blog_heading_background = $page_heading_background;
        $key_prefix = '';
    }
}

if($blog_heading_style =='banner'){
    if( isset($blog_heading_background[$key_prefix.'color']) && $blog_heading_background[$key_prefix.'color']!=''){
        $page_heading_background_css .= 'background-color: '.$blog_heading_background[$key_prefix.'color'].';';
    }
    if( isset($blog_heading_background[$key_prefix.'image']) && $blog_heading_background[$key_prefix.'image']!=''){
        $page_heading_background_css .= ' background-image: url("'.$blog_heading_background[$key_prefix.'image'].'");';
    }
    if( isset($blog_heading_background[$key_prefix.'repeat']) && $blog_heading_background[$key_prefix.'repeat']!=''){
        $page_heading_background_css .= ' background-repeat: '.$blog_heading_background[$key_prefix.'repeat'].';';
    }
    if( isset($blog_heading_background[$key_prefix.'position']) && $blog_heading_background[$key_prefix.'position']!=''){
        $page_heading_background_css .= ' background-position: '.$blog_heading_background[$key_prefix.'position'].';';
    }
    if( isset($blog_heading_background[$key_prefix.'attachment']) && $blog_heading_background[$key_prefix.'attachment']!=''){
        $page_heading_background_css .= ' background-attachment: '.$blog_heading_background[$key_prefix.'attachment'].';';
    }
    if( isset($blog_heading_background[$key_prefix.'size']) && $blog_heading_background[$key_prefix.'size']!=''){
        $page_heading_background_css .= ' background-size:'.$blog_heading_background[$key_prefix.'size'].';';
    }
}
?>
<div style="<?php echo esc_attr($page_heading_background_css);?>" class="blog-heading <?php echo esc_attr($blog_heading_style);?>">
    <div class="inner">
        <?php if ( is_home() ) : ?>
            <?php if( is_front_page()):?>
                <h1 class="page-title blog-title"><?php esc_html_e('Latest Posts','urus');?></h1>
            <?php else:?>
                <h1 class="page-title blog-title"><?php single_post_title(); ?></h1>
            <?php endif;?>
        <?php elseif( is_single() ):?>
            <h1  class="page-title"><?php the_title(); ?></h1>
        <?php elseif(is_page()):?>
            <h1  class="page-title"><?php the_title(); ?></h1>
        <?php elseif(is_search()):?>
            <h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'urus' ), get_search_query() ); ?></h1>
        <?php else:?>
            <h1  class="page-title"><?php the_archive_title( '', '' ); ?></h1>
            <?php
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
            ?>
        <?php endif;?>
        <?php do_action('urus_blog_breadcrumbs');?>
    </div>
</div>
