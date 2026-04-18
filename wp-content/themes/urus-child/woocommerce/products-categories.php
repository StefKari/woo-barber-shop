<?php
$shop_categories_display = Urus_Helper::get_option('shop_categories_display',array());
$args = array(
    'hide_empty' => 1,
);
if( !empty($shop_categories_display)){
    $args = array(
        'hide_empty' => 0,
        'orderby' => 'slug__in',
    );

    $args['slug'] = $shop_categories_display;
}

$list_product_cats = Urus_Pluggable_WooCommerce::get_categories($args);
$atts = array(
    'loop'         => 'false',
    'ts_items'     => 2,
    'xs_items'     => 2,
    'sm_items'     => 3,
    'md_items'     => 3,
    'lg_items'     => 4,
    'ls_items'     => 4,
    'navigation'   => 'true',
    'slide_margin' => 30
);
$carousel_settings = Urus_Helper::carousel_data_attributes('',$atts);
?>
<?php if( !empty($list_product_cats)):?>
<div class="shop-categories">
    <h3 class="title"><span class="text"><?php esc_html_e('Shop More categories','urus');?></span></h3>
    <div class="urus-categories swiper-container urus-swiper nav-center" <?php echo Urus_Helper::escaped_html($carousel_settings);?>>
        <div class="swiper-wrapper">
            <?php foreach ($list_product_cats as $category):?>
                <div class="swiper-slide">
                    <?php
                    $thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );
                    $image = Urus_Helper::resize_image($thumbnail_id,270,300,true,true);
                    $link = get_term_link($category);
                    ?>
                    <div class="category-item">
                        <div class="thumb">
                            <a href="<?php echo esc_url($link);?>">
                                <figure>
                                    <?php echo Urus_Helper::escaped_html($image['img']);?>
                                </figure>
                                <span class="count"><?php echo wp_specialchars_decode(sprintf(esc_html__('%s item(s)','urus'),$category->count));?></span>
                            </a>
                        </div>
                        <div class="info">
                            <h3 class="category-name"><a href="<?php echo esc_url($link);?>"><?php echo esc_html($category->name);?></a></h3>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
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
<?php endif;?>
