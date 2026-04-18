;(function ($) {
    "use strict";
    var variationGallery = function(_target, variation){
        var variation_gallery_images =[];
        var _product = $(_target).closest( '.product' );
        if(typeof variation.variation_gallery_images != "undefined"){
            variation_gallery_images = variation.variation_gallery_images;
        }else{
            if ( $('#variation_gallery_defaut_images').length){
                variation_gallery_images =  $('#variation_gallery_defaut_images').data('variation_gallery_images');
            }
        }

        if(jQuery.inArray(urus_variation_gallery.woo_single_used_layout, ['vertical','horizontal','background','extra-sidebar']) !== -1){
            variationGalleryInit(_product,variation_gallery_images);
        }
        if(jQuery.inArray(urus_variation_gallery.woo_single_used_layout, ['list','special_gallery','gallery','gallery2']) !== -1){
            variationGalleryList(_product,variation_gallery_images);
        }
        if(jQuery.inArray(urus_variation_gallery.woo_single_used_layout, ['large','special_slider','special_centered_slider']) !== -1){
            variationGallerySlider(_product,variation_gallery_images);
        }
    };
    var variationGalleryInit = function(product, variation_gallery_images){
        var $target = product.find('.woocommerce-product-gallery--with-images');
        var gallery_html ='<div class="flexslider variationGallery"><ul class="slides">';

        $.each(variation_gallery_images,function ( key, image) {
            gallery_html = gallery_html+'<li data-thumb="'+image.gallery_thumbnail_src+'" class="woocommerce-product-gallery__image swiper-slide"><a style="display: block;" href="'+image.full_src+'">'+image.image +'</a></li>';
        });
        gallery_html = gallery_html+'</ul></div>';

        var height = $target.innerHeight();

        $target.css("opacity", 0).css('height',height+'px').css('overflow','hidden').empty().html(gallery_html);
        $('.flexslider.variationGallery').flexslider({
            animation:'slide',
            slideshow:false,
            directionNav:true,
            animationLoop:false,
            controlNav: 'thumbnails',
            allowOneSlide: false,
            start: function() {
                $target.css( 'opacity', 1 );
                gallery_center_nav();
            },
            after: function( slider ) {
                $target.css('height','auto');
                $target.css('overflow','inherit');
            },

        });
        $(document).trigger( 'variation_gallery_images',[variation_gallery_images] );
    };
    var zoomInnt = function(){
        if ( $('.woocommerce-product-gallery__image a').length){
            $('.woocommerce-product-gallery__image a').each(function () {
                var src = $(this).attr('href');
                $(this).zoom({
                    touch:false,
                    url:src
                });
            });
        }
    };
    var variationGalleryList = function(product, variation_gallery_images){
        var $target = product.find('.single-product-gallery');
        var gallery_html ='';
        $.each(variation_gallery_images,function ( key, image) {
            gallery_html = gallery_html+'<div class="single-product-gallery-item"> <div data-thumb="'+image.gallery_thumbnail_src+'" class="woocommerce-product-gallery__image swiper-slide"><a style="display: block;" href="'+image.full_src+'">'+image.image +'</a></div></div>';
        });

        $target.empty().html(gallery_html);

        $(document).trigger( 'variation_gallery_images_list' );
    };

    var variationGallerySlider = function(product, variation_gallery_images){
        var  $target = product.find('.single-product-gallery__slider__wrapper');
        var $wrapper = $('.single-product-gallery__slider__wrapper .swiper-wrapper');
        var gallery_html ='';

        $.each(variation_gallery_images,function ( key, image) {
            gallery_html = gallery_html+'<div class="single-product-gallery-item swiper-slide"> <div data-thumb="'+image.gallery_thumbnail_src+'" class="woocommerce-product-gallery__image"><a style="display: block;" href="'+image.full_src+'">'+image.image +'</a></div></div>';
        });

        $target.removeClass('swiper-container-horizontal swiper-initialized');
        $wrapper.empty().removeAttr('style');
        $wrapper .html(gallery_html);

        $(document).trigger( 'variation_gallery_images_slider' );
    };
    var initPhotoswipe = function(){
        var $target = $('.woocommerce-product-gallery');
        if ( !$target.find('.urus-woocommerce-product-gallery__trigger').length) {
            $target.prepend( '<a href="#" class="urus-woocommerce-product-gallery__trigger">🔍</a>' );
        }

        $(document).on('click','.urus-woocommerce-product-gallery__trigger',function () {
            openPhotoSwipe();
            return false;
        });
        $(document).on('click','.woocommerce-product-gallery__image a',function (e) {
            e.preventDefault();
        });
    };
    var openPhotoSwipe = function() {
        var pswpElement = document.querySelectorAll('.pswp')[0];
        // build items array
        var items = [];

        $('.woocommerce-product-gallery .woocommerce-product-gallery__image').each(function (i, el ) {
            var img = $( el ).find( 'img' );

            if ( img.length ) {
                var large_image_src = img.attr( 'data-large_image' ),
                    large_image_w   = img.attr( 'data-large_image_width' ),
                    large_image_h   = img.attr( 'data-large_image_height' ),
                    item            = {
                        src  : large_image_src,
                        w    : large_image_w,
                        h    : large_image_h,
                        title: img.attr( 'data-caption' ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
                    };
                items.push( item );
            }
        });

        // define options (if needed)
        var options = {
            // history & focus options are disabled on CodePen
            history: false,
            focus: false,

            showAnimationDuration: 0,
            hideAnimationDuration: 0

        };

        var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
        gallery.init();
        // Gallery starts closing
        gallery.listen('close', function() {
            setTimeout(function () {
                $('.pswp').removeClass('pswp--open');
            },800);

        });
    };
    var gallery_center_nav = function () {
        $(window).on('resize load', function(){
            var gallery = $(".woocommerce-product-gallery .flex-viewport");
            if(gallery.length){
                var nav = $(".woocommerce-product-gallery .flex-direction-nav a");
                var gallery_h = gallery.height();

                if(typeof gallery_h === "undefined" || !gallery_h){
                    return;
                }
                nav.css("top", gallery_h / 2 + "px");
            }
        });
    };
    $(document).on('variation_gallery_images variation_gallery_images_list variation_gallery_images_slider',function () {
        initPhotoswipe();
    });

    $(document).on('variation_gallery_images',function () {
        zoomInnt();
    });


    // $(document).on('woocommerce_variation_has_changed wc_variation_form', function ( ) {
    //     setTimeout(function(){
    //         variationGallery();
    //     }, 100);
    // });
    $(document).on('found_variation',function (e,variation) {
        variationGallery(e.target,variation);
    });

    $(document).on('click','.pswp__button--close',function () {
        $('.pswp').removeClass('pswp--open');
    });

    $(document).ajaxComplete(function (event, xhr, settings) {

    });
})(jQuery);
