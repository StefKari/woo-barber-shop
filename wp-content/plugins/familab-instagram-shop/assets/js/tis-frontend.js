jQuery(document).ready(function($){
    var isMobile = false; //initiate as false
    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
        isMobile = true;
    }
    init_tis_carousel();
    init_magnific_popup();
    //init_custom_scrollbar();

    if (isMobile) {
         $('.tis-product-pins:not(.tis-modal-product-pins) .pin-text').on('click', function (e) { 
             var elm = $(this).parents('.single-pin');
           click_on_pin_function(elm);
         })
    }else{
        $(document).on('mouseover', '.tis-product-pins:not(.tis-modal-product-pins) .single-pin', function (e) {
            var elm = $(this);
           click_on_pin_function(elm);
        });
        $(document).on('mouseleave', '.tis-product-pins:not(.tis-modal-product-pins) .single-pin', function (e) {
             hide_all_popups();
        });
    }

	

    // Close popup
    $(document).on('click', '.tis-image-items .close-popup', function (e) {
    	 hide_all_popups();
    });


    function click_on_pin_function(elm){
        hide_all_popups();
        var $this = elm;
        var this_offset = $this.offset();
        var this_left = this_offset.left;
        var this_w = $this.innerWidth();
        var thisPopup = $this.find('.single-pin-product');
        var popup_offset = thisPopup.offset();
        var popup_left = popup_offset.left;
        var popup_w = thisPopup.innerWidth();
        if($this.parents('.tis-image-items').hasClass('tis-modal-product-pins')){
            var ww = $this.parents('.tis-modal-product-pins');
            
        }else{
            var ww = $(window).innerWidth();
        }
        
        var is_popup_center = false;
        $this.addClass('active');
        thisPopup.addClass('tis-current-popup');
        
        if (thisPopup.is('.popup-right') && this_left < popup_w && ww - this_left - this_w - 10 < popup_w) {
            is_popup_center = true;
        }
        if (thisPopup.is('.popup-left') && this_left < popup_w) {
            is_popup_center = true;
        }
        if (thisPopup.is('.popup-center') && this_left < popup_w && this_left + this_w + popup_w + 10 > ww) {
            is_popup_center = true;
        }
        
        if (!is_popup_center) {
            var is_popup_outside_right = this_left + this_w + popup_w + 10 > ww;
            var is_popup_outside_left = this_left + 10 < popup_w;
            
            // In case of not left and not right
            if (!is_popup_outside_left && !is_popup_outside_right) {
                thisPopup.removeClass('popup-left popup-center').addClass('popup-right');
            }
            else {
                if (is_popup_outside_right) {
                    thisPopup.removeClass('popup-right popup-center').addClass('popup-left');
                }
                if (is_popup_outside_left) {
                    thisPopup.removeClass('popup-left popup-center').addClass('popup-right');
                }
            }
        }
        else {
            thisPopup.removeClass('popup-left popup-right').addClass('popup-center');
            var popup_new_left = this_left - ((ww - popup_w) / 2);
            thisPopup.css({
                'left': '-' + popup_new_left + 'px',
                'right': 'auto'
            });
            var arrow_pos_left = popup_new_left + this_w / 2;
            var css_top_arrow = '';
            if (!$('head .tis-popup-style').length) {
                $('head').append('<style type="text/css" class="tis-popup-style">.popup-center.tis-current-popup:before {left: ' + arrow_pos_left + 'px !important;}</style>');
            }
            else {
                $('head .tis-popup-style').replaceWith('<style type="text/css" class="tis-popup-style">.popup-center.tis-current-popup:before {left: ' + arrow_pos_left + 'px !important;}</style>');
            }
        }
    }

    function init_tis_carousel(){

        /** TIS STYLE 2 ====================================================================== **/
        if (! isMobile) {
            $(document).on('click', '.tis-modal-popup .tis-modal-close', function(){
                $(document).find('.tis-modal-popup').fadeOut();
                $('body').removeClass('modal-open');
            })
            
            $(document).on('mouseover', '.tis-modal-popup .single-modal-product, .tis-modal-popup .single-pin', function (e) {
                var curr_slide = $(this).parents('.swiper-slide'),
                    product_id = $(this).data('product-id');

                curr_slide.find('.products-content').addClass('hovering');
                curr_slide.find('.single-pin[data-product-id="'+product_id+'"]').addClass('hovering');
                curr_slide.find('.single-pin[data-product-id="'+product_id+'"] .single-pin-product').addClass('tis-current-popup');
                curr_slide.find('.single-modal-product[data-product-id="'+product_id+'"]').addClass('hovering');

            });

            $(document).on('mouseleave', '.tis-modal-popup .single-pin, .tis-modal-popup .single-modal-product', function (e) {
                hide_all_popups();
            });

            $(document).keyup(function(e){
                //press ESC
                if( e.keyCode === 27 ){
                    if ($('body').hasClass('modal-open') &&  $(document).find('.tis-modal-popup').hasClass('opening')) {
                        $(document).find('.tis-modal-popup').fadeOut();
                        $('body').removeClass('modal-open');
                    }
                }
                
            })

        }
        /** TIS STYLE 2 ====================================================================== **/

        /** TIS STYLE Carousel ====================================================================== **/
            $('.tis-image-items.carousel-style .swiper-container').each(function(){
                var this_swiper = $(this);
                var post_id = $(this).data('post-id');
                var config = $(this).data(),
                    slidesPerView = config.perview,
                    spaceBetween = config.space,
                    breakpoints = config.breakpoints;
                new_swiper = new Swiper(this_swiper, {
                    slidesPerView: slidesPerView,
                    freeMode: true,
                    freeModeMomentumBounceRatio: 1.5,
                    spaceBetween: spaceBetween,
                    breakpoints: breakpoints,
                    effect:'slide',
                    on: {
                        init: function () {
                            this_swiper.addClass('swiper-initialized');
                            var parent_row = this_swiper.parents('.vc_row');
                            if (parent_row.attr('data-vc-full-width') == 'true' ) {
                                //is Visual composer fullwidth row, update the swiper again
                                window.setTimeout(function(){
                                    //update the 
                                    new_swiper.update();
                                },2000)
                            }
                            
                        }
                    },
                    navigation: {
                        nextEl: '.post-id-'+post_id+'.tis-carousel-next',
                        prevEl: '.post-id-'+post_id+'.tis-carousel-prev',
                    }

                });
            })
        /** TIS STYLE Carousel ====================================================================== **/
    }

    function init_magnific_popup(){
        
 
            $(document).on('click', '.instagram-shop-modal-popup', function(){
                $('.tis-image-items .instagram-shop-modal-popup').removeClass('current-click-index');
                
            })
            $('.instagram-shop-modal-popup').magnificPopup({
                type: 'inline',
                mainClass: 'mfp-fade', // this class is for CSS animation below
                removalDelay: 300,
                midClick: true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
                showCloseBtn: true,
                callbacks: {
                    beforeOpen: function() {
                    },
                    elementParse: function(item) {
                        $(item.el).addClass('current-click-index');
                    },
                    open: function() {
                        var _this_popup = this;
                        $('body').css('overflow-y', 'hidden');
                        $('.tis-modal-popup .swiper-container').each(function(){
                            var this_swiper = $(this);
                            var post_id = $(this).data('post-id');
                            var new_swiper =  new Swiper(this_swiper, {
                                slidesPerView: 1,
                                allowTouchMove: false,
                                effect:'fade',
                                autoHeight: true,
                                spaceBetween:30,
                                on:{
                                    init: function () {
                                        this_swiper.addClass('swiper-initialized');
                                    },
                                    transitionEnd: function(){
                                    }
                                },
                                navigation: {
                                    nextEl: $(this).parents('.tis-modal-popup').find('.tis-modal-next'),
                                    prevEl: $(this).parents('.tis-modal-popup').find('.tis-modal-prev'),
                                },
                                keyboard: {
                                    enabled: true,
                                    onlyInViewport: false,
                                },

                            });
                      
                            var clicked_el = $('.tis-image-items').find('.current-click-index').parents('.single-image'),
                                clicked_index = clicked_el.attr('data-index'),
                                post_id = clicked_el.parents('.tis-image-items').data('post-id');
                            new_swiper.slideTo(clicked_index, 0);
                            document.addEventListener('lazyloaded', function(e){
                                if ($(e.target).hasClass('tis-modal-image') && $(e.target).closest('.swiper-slide-active').length){
                                    new_swiper.update();
                                }
                            });
                        });
                    },


                    close: function() {
                        // Will fire when popup is closed
                        $('body').css('overflow-y', 'auto');
                    }
                    // e.t.c.
                }
            });
    }

    function init_custom_scrollbar(){
        $('.tis-modal-popup .modal-right-content').mCustomScrollbar(
            {
                setHeight: false,
                autoHideScrollbar: true,
            }
        );
    }

    function hide_all_popups(){
        $(document).find('.single-pin').removeClass('active hovering');
        $(document).find('.single-pin-product').removeClass('tis-current-popup');
        $(document).find('.single-modal-product.product-item').removeClass('hovering');
        $(document).find('.tis-modal-popup .products-content').removeClass('hovering');
    }
                
})