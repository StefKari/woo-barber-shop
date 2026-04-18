<?php

$post_class ='post-item single-post';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?>>

    <div class="post-header">

        <h1 class="post-title"><?php the_title();?></h1>

        <div class="metas">

            <span class="author"><?php esc_html_e('By : ','urus'); ?><span class="name"><?php the_author();?></span></span>
            <span class="comment-count">
                <?php printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', 'urus' ), number_format_i18n( get_comments_number() ) );?>
            </span>
        </div>

    </div>

    <?php get_template_part('template-parts/post-format');?>

    <div class="info">
        <div class="content-post">
            <?php the_content();?>
            <?php
            wp_link_pages( array(
                'before'      => '<div class="page-links">',
                'after'       => '</div>',
                'link_before' => '<span>',
                'link_after'  => '</span>',
                'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'urus' ) . ' </span>%',
                'separator'   => ''
            ) );
            ?>
        </div>
        <div class="info-bottom clearfix">
            <?php if( has_tag()):?>
            <div class="tags">
                <span class="title"><?php esc_html_e('Tags: ','urus');?></span>
                <?php the_tags(esc_html__('','urus'),', ','');?>
            </div>
            <?php endif;?>
            <div class="categories">
                <span class="title"><?php esc_html_e('Categories:','urus');?></span>
                <?php the_category(', ');?>
            </div>
        </div>

    </div>
    <?php get_template_part( 'template-parts/blog', 'author' ); ?>

    <?php

    // Previous/next post navigation.

    $next_post     = get_next_post();

    $previous_post = get_previous_post();
    $enable_post_navigation = Urus_Helper::get_option('enable_post_navigation',1);
    ?>

    <?php if ( (! empty( $next_post ) || ! empty( $previous_post ))  && $enable_post_navigation ==1) : ?>

        <div class="footer-post">
            <div class="post-expand">
                <?php get_template_part('template-parts/blog','share');?>
            </div>
            <?php

            if ( ! empty( $next_post ) && ! empty( $previous_post ) ) :

                the_post_navigation( array(

                    'next_text' => '<span class="post-text"><span class="meta-nav" aria-hidden="true">' . esc_html__( 'Next', 'urus' ) . '</span> ' .

                        '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'urus' ) . '</span> ' .

                        '<span class="post-title">%title</span></span>' . get_the_post_thumbnail( $next_post->ID, 'thumbnail' ),

                    'prev_text' => get_the_post_thumbnail( $previous_post->ID, 'thumbnail' ) . '<span class="post-text"><span class="meta-nav" aria-hidden="true">' . esc_html__( 'Previous', 'urus' ) . '</span> ' .

                        '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'urus' ) . '</span> ' .

                        '<span class="post-title">%title</span></span> ',

                ) );

            elseif ( empty( $next_post ) && ! empty( $previous_post ) ):

                the_post_navigation( array(

                    'prev_text' => get_the_post_thumbnail( $previous_post->ID, 'thumbnail' ) . '<span class="post-text"><span class="meta-nav" aria-hidden="true">' . esc_html__( 'Previous', 'urus' ) . '</span> ' .

                        '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'urus' ) . '</span> ' .

                        '<span class="post-title">%title</span></span> ',

                ) );

            elseif ( empty( $previous_post ) && ! empty( $next_post ) ):

                the_post_navigation( array(

                    'next_text' => '<span class="post-text"><span class="meta-nav" aria-hidden="true">' . esc_html__( 'Next', 'urus' ) . '</span> ' .

                        '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'urus' ) . '</span> ' .

                        '<span class="post-title">%title</span></span>' . get_the_post_thumbnail( $next_post->ID, 'thumbnail' ),

                ) );

            endif;

            ?>

        </div>

    <?php endif; ?>
    <?php get_template_part( 'template-parts/blog', 'related' ); ?>


    

</article>