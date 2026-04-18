<?php if ( has_post_format('gallery') ) : ?>
    <?php $images = get_post_meta( get_the_ID(), '_format_gallery_images', true ); ?>
    <?php if ( $images ) : ?>
        <?php
        $atts = array(
            'loop'         => 'false',
            'ts_items'     => 1,
            'xs_items'     => 1,
            'sm_items'     => 1,
            'md_items'     => 1,
            'lg_items'     => 1,
            'ls_items'     => 1,
            'navigation'   => 'true',
            'slide_margin' => 40,
            'dots'         => 'false'
        );
        $carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
        ?>
        <div class="post-format post-gallery nav-center" >
            <div class="swiper-container urus-swiper custom-slide" <?php echo esc_attr( $carousel_settings );?>>
                <div class="swiper-wrapper" >
                    <?php foreach ( $images as $image_id ) : ?>
                        <?php
                        $width = 1040;
                        $height = 600;
                        $crop = true;
                        $img = Urus_Helper::resize_image($image_id,$width,$height,$crop,true);
                        ?>
                        <div class="grid__item">
                            <?php echo Urus_Helper::escaped_html($img['img']);?>
                        </div>
                    <?php endforeach; ?>
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
        
    <?php endif; ?>
<?php elseif ( has_post_format('video') ) : ?>
    <div class="post-format post-video">
        <?php $video = get_post_meta( get_the_ID(), '_format_video_embed', true ); ?>
        <?php if ( wp_oembed_get($video) ) : ?>
            <?php echo wp_oembed_get($video); ?>
            <?php else : ?>
            <?php echo wp_kses_post($video); ?>
        <?php endif; ?>
    </div>
<?php elseif ( has_post_format('audio') ) : ?>
    <div class="post-format post-audio">
        <?php $audio = get_post_meta( get_the_ID(), '_format_audio_embed', true ); ?>
        <?php if ( wp_oembed_get($audio) ) : ?>
            <?php echo wp_oembed_get($audio); ?>
        <?php else : ?>
            <?php echo wp_kses_post($audio); ?>
        <?php endif; ?>
    </div>
<?php else:?>
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-format post-standard">
            <?php if ( !is_single() ) : ?>
                <a href="<?php the_permalink(); ?>"><?php Urus_Helper::post_thumb();?></a>
            <?php else : ?>
                <?php Urus_Helper::post_thumb();?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>