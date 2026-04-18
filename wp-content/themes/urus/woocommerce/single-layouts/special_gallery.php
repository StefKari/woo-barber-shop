<?php
do_action('urus_sticky_single_product_tab');
?>
<div class="urus-single-product-top special-sticky-layout summary entry-summary row">
    <div class="col-lg-3">
        <div class="urus_sticky_single">
            <div class="sticky_content sticky_content_left">
                <?php
                do_action('urus_left_sticky_single_product_summary');
                ?>
            </div>
        </div>
    </div>
    <div class="urus-product-special_gallery__wrapper col-lg-6">
        <div class="urus-product-gallery__wrapper__inner ">
            <?php
            do_action( 'urus_single_product_gallery' );
            ?>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="urus_sticky_single">
            <div class="sticky_content sticky_content_right">
                <?php
                do_action( 'urus_right_sticky_single_product_summary' );
                ?>
            </div>
        </div>
    </div>
</div>
