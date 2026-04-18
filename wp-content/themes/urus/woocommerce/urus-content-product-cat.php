<?php
if ( ! defined( 'ABSPATH' ) ) {
exit;
}
$product_item_class = array('col-sm-4');
?>
<li <?php wc_product_cat_class( $product_item_class, $category ); ?>>
    <?php
        $thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );
        $thumb = Urus_Helper::resize_image($thumbnail_id,500,600,true,true);
        $link = get_term_link($category);
    ?>
    <div class="inner">
        <a class="cat-link" href="<?php echo esc_url($link);?>"></a>
        <div class="thumb">
            
            <?php echo e_data($thumb['img']);?>
        </div>
        <div class="info">
            <h3 class="cat-name"><?php echo esc_html($category->name);?></h3>
            <span class="count"><?php echo sprintf( esc_html__( '%s products', 'urus' ), $category->count );?></span>
        </div>
    </div>
</li>