;(function ($) {
    "use strict";
    var $window   = $(window),
        $document = $(document),
        $body     = $('body'),
        has_rtl = $body.hasClass('rtl'),
        window_width = $window.width(),
        window_resize_width = $window.width(),
        pjax_popstate = false,
        URUS;
    URUS = {
        init: function () {
            this.backtotop();
            this.woo_quantily();
            this.lazy_load();
            this.init_carousel();
            this.woo_message();
            this.urus_chosen();
            this.shopFilter();
            this.wooPriceSlider();
            this.product_thumb();
            this.newsletter();
            this.footer_menu_toggle();
            this.back_history();
            this.fillter_block_mobile();
            this.sidebar_mobile();
            this.product_variation_ajax_add_to_cart();
            this.header_block_search();
            this.shortcode_products_loadmore();
            this.scrollbar();
            this.smooth_scroll_target();
            this.sticky_sidebar();
            this.single_add_to_cart_form();
            this.size_guide();
            this.mini_cart_fixed();
            this.urus_tab();
            this.miniCart();
            this.countdown();
            this.menu_bar();
            this.header_sticky();
            this.urus_mobile_footer();
            this.quick_view();
            this.urus_quick_view();
            this.wishlist();
            this.single_mobile_tab();
            this.urus_instant_search();
            this.ajax_login();
            this.infload_init();
            this.filter_ajax();
            this.category_menu();
            this.urus_wishlist();
            this.check_wishlist_state();
            this.urus_compare();
            this.filterStatus();
            this.masonry();
            this.fullscreen_page();
            this.product_item_thumb_swipper(true);
            this.product_item_zoom();
            this.urus_quick_add_variable();
            this.vertical_menu();
            this.shopSideBar();
            this.slick_slider();
        },
        onResize: function () {
            this.urus_chosen();
            this.sticky_sidebar();
            window_resize_width = $window.width();
        },
        ajaxComplete: function () {
            this.lazy_load();
            this.scrollbar();
            this.sticky_sidebar();
            this.masonry();
        },
        scroll: function () {
            if ($window.scrollTop() > 1000) {
                $('.backtotop').addClass('show');
                $('.compare-toggle').addClass('show');
            } else {
                $('.backtotop').removeClass('show');
                $('.compare-toggle').removeClass('show');
            }
        },
        product_item_thumb_swipper: function (arg) {
            if ($('.urus-gallery-top').length && $('.urus-gallery-top').next('.urus-gallery-thumbs').length) {
                if (arg == 'update') {
                    $('.urus-gallery-top').each(function () {
                        var update_swiper = $(this).get(0).swiper;
                        update_swiper.update();
                    });
                    return;
                }
                $('.urus-gallery-top').not('.swiper-initialized').each(function () {
                    var this_obj = $(this),
                        thumb_item = this_obj.next('.urus-gallery-thumbs'),
                        direction = 'horizontal',
                        spaceBetween = 4,
                        slidesPerView = 3,
                        space = this_obj.data('space'),
                        nbs = this_obj.data('nbs');
                    if ($(this).closest('.product-item').hasClass('cart_and_icon') || $(this).closest('.product-item').hasClass('full')) {
                        direction = 'vertical';
                    }
                    if (typeof (space) != "undefined") {
                        spaceBetween = parseInt(space);
                    }
                    if (typeof (nbs) != "undefined") {
                        slidesPerView = parseInt(nbs);
                    }
                    var galleryThumbs = new Swiper(thumb_item, {
                        spaceBetween: spaceBetween,
                        slidesPerView: slidesPerView,
                        direction: direction,
                        watchSlidesVisibility: true,
                        watchSlidesProgress: true,
                        on: {
                            init: function () {
                                thumb_item.addClass('swiper-initialized');
                                this_obj.addClass('swiper-initialized');
                            },
                            slideChangeTransitionEnd: function () {
                                URUS.lazy_load();
                            }
                        }
                    });
                    var galleryTop = new Swiper($(this), {
                        spaceBetween: 10,
                        thumbs: {
                            swiper: galleryThumbs,
                        },
                    });
                });
            }
        },
        fullscreen_page: function () {
            if ($('#fullscreen-template').length) {
                if (window != top){
                    var root_doccument = $(top.document);
                    if (root_doccument.find('body').hasClass('elementor-editor-active')){
                        return;
                    }
                }
                var u = {
                    show_header:function(){
                        if (window_resize_width <= 1024){
                            main_header.hide();
                            main_header = mobile_header;
                        }
                        else{
                            mobile_header.hide();
                            main_header = $('#header');
                        }
                        main_header.show();
                    },
                    height_item_first:function (obj) {
                        if (typeof obj == "undefined" || obj == "null"){
                            return;
                        }
                        if (window_resize_width <= 1024 && obj.isFirst){
                            var main_header_h = main_header.height();
                            if (typeof main_header_h !== "undefined" && main_header_h !== "null"){
                                var window_h = $window.height();
                                var height = window_h - main_header_h;
                                $(obj.item).height(height);
                                $(obj.item).css("min-height", height+"px");
                            }
                        }
                    }
                };
                var main_header = $('#header');
                var mobile_header = $(".header-mobile-responsive");
                mobile_header = mobile_header.length ? mobile_header : $(".familab-header-mobile");
                $('#fullscreen-template').fullpage({
                    //options here
                    autoScrolling: true,
                    scrollHorizontally: true,
                    navigation: false,
                    navigationPosition: 'right',
                    verticalCentered: true,
                    onLeave: function (index, nextIndex, direction) {
                        //after leaving section 2
                        if (direction == 'down') {
                            main_header.fadeOut('slow');
                        } else if (direction == 'up' && index.index == 1) {
                            main_header.fadeIn('slow');
                        }
                        u.height_item_first(this);
                    },
                    afterResize: function(width, height){
                        u.show_header();
                    },
                    afterRender: function(){
                        u.show_header();
                        u.height_item_first(this);
                        $document.on("CloseWishlist", function () {
                            URUS.fullscreen_page();
                        });

                    },

                });

            }
        },
        masonry: function () {
            if ($('.urus-masonry').length) {
                $('.urus-masonry').each(function () {
                    var $msnry = $(this),
                        settings = $msnry.data('settings');
                    settings.transitionDuration = '1s';
                    var $grid = $msnry.masonry(settings);
                    $grid.on('layoutComplete', function (event, laidOutItems) {
                        $msnry.addClass('initialization');
                    });
                    // layout Masonry after each image loads
                    $grid.imagesLoaded().progress(function () {
                        $grid.masonry('layout');
                    });
                });
            }

        },
        infload_init: function () {
            var shop_wapper = $('#shop-page-wapper'),
                infloadScroll = shop_wapper.find('.infload-controls').hasClass('scroll-mode') ? true : false,
                infloadControls = shop_wapper.find('.infload-controls');
            if (infloadScroll) {
                var pxFromWindowBottomToBottom,
                    pxFromMenuToBottom = Math.round($document.height() - infloadControls.offset().top);

                $(window).scroll(function () {
                    pxFromWindowBottomToBottom = 0 + $document.height() - ($(window).scrollTop()) - $(window).height();
                    if ((pxFromWindowBottomToBottom) < pxFromMenuToBottom) {
                        $('.infload-controls a:not(".infload-to-top")').trigger('click');
                    }
                });

            }
            $document.on('click', '.infload-controls a', function (event) {

                URUS.shopInfLoadGetPage(event);
                return false;
            });

        },
        shopInfLoadGetPage: function (event) {
            var shop_wapper = $('#shop-page-wapper'),
                infloadControls = shop_wapper.find('.infload-controls'),
                nextPageLink = shop_wapper.find('.infload-link a '),
                nextPageUrl = nextPageLink.attr('href');

            if (nextPageUrl) {
                if (infloadControls.hasClass('urus-loader')) return;
                infloadControls.addClass('urus-loader');
                history.pushState({}, '', nextPageUrl);

                // No pjax
                $.ajax({
                    url: nextPageUrl,
                    dataType: 'html',
                    cache: false,
                    headers: {'cache-control': 'no-cache'},
                    method: 'GET',
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log('Error - ' + errorThrown);
                    },
                    complete: function () {
                        // Hide 'loader'
                        infloadControls.removeClass('urus-loader');
                    },
                    success: function (response) {

                        var $response = $('<div>' + response + '</div>'), // Wrap the returned HTML string in a dummy 'div' element we can get the elements
                            $newElements = $response.find('.product-list-wapper ul.products li');

                        // Hide new elements/products before they're added
                        $newElements.addClass('hide');

                        // Append the new elements
                        shop_wapper.find('.products').append($newElements);

                        // Show new elements/products
                        setTimeout(function () {
                            $newElements.removeClass('hide');
                            $document.trigger('core_page_loaded');
                        }, 300);

                        var $shopHeadingwElement = $response.find('.shop-heading').html();
                        shop_wapper.find('.shop-heading').html($shopHeadingwElement);
                        var documentTitle = $response.find('title').text();

                        $document.prop('title', documentTitle);


                        // Get the 'next page' URL
                        nextPageUrl = $response.find('.infload-link').children('a').attr('href');

                        if (nextPageUrl) {
                            nextPageLink.attr('href', nextPageUrl);
                        } else {
                            shop_wapper.addClass('all-products-loaded');

                            if (self.infloadScroll) {
                                self.infscrollLock = true; // "Lock" scroll (no more products/pages)
                            } else {
                                infloadControls.addClass('hide-btn'); // Hide "load" button (no more products/pages)
                            }
                            nextPageLink.removeAttr('href');
                        }

                        if ($('.urus-masonry').length) {
                            var $msnry = $('.urus-masonry');
                            if ($msnry.hasClass('initialization')) {
                                $msnry.append($newElements).masonry('appended', $newElements);
                            } else {
                                URUS.masonry();
                            }

                        }

                        URUS.init_carousel();
                        URUS.product_item_thumb_swipper(true);

                    }
                });
            }

        },
        backtotop: function () {
            $document.on('click', 'a.backtotop ,.infload-to-top', function () {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
        },
        woo_quantily: function () {
            $body.on('click', '.quantity .quantity-plus', function () {
                var obj_qty = $(this).closest('.quantity').find('input.qty'),
                    val_qty = parseInt(obj_qty.val()),
                    //min_qty  = parseInt(obj_qty.attr('min')),
                    max_qty = parseInt(obj_qty.attr('max')),
                    step_qty = parseInt(obj_qty.attr('step'));
                val_qty = val_qty + step_qty;
                if (max_qty && val_qty > max_qty) {
                    val_qty = max_qty;
                }
                obj_qty.val(val_qty);
                obj_qty.trigger("change");
                return false;
            });
            $body.on('click', '.quantity .quantity-minus', function () {
                var obj_qty = $(this).closest('.quantity').find('input.qty'),
                    val_qty = parseInt(obj_qty.val()),
                    min_qty = parseInt(obj_qty.attr('min')),
                    max_qty = parseInt(obj_qty.attr('max')),
                    step_qty = parseInt(obj_qty.attr('step'));
                val_qty = val_qty - step_qty;
                if (min_qty && val_qty < min_qty) {
                    val_qty = min_qty;
                }
                if (!min_qty && val_qty < 0) {
                    val_qty = 0;
                }
                obj_qty.val(val_qty);
                obj_qty.trigger("change");
                return false;
            });
        },
        urus_chosen: function () {
            if ($('.shop-control select').length > 0) {
                if (pjax_popstate) {
                    $('.shop-control select').chosen("destroy");
                    $('.shop-control .chosen-container').remove();
                }
                $('.shop-control select').each(function(){
                    $(this).css('visibility', 'hidden').css('display', 'block').css('position', 'absolute');
                    $(this).chosen(
                        {
                            disable_search_threshold: 20
                        }
                    );
                });
            }
        },
        lazy_load: function (e_obj) {
            if (urus_ajax_frontend.enable_lazy != 1) return false;
            var $lazy = $('.lazy');
            if (typeof e_obj != "undefined" && e_obj !== null && e_obj.length > 0) {
                $lazy = e_obj.find('.lazy');
            }
            $lazy.each(function () {
                var _config = [];
                var this_obj = $(this);
                _config.beforeLoad = function (element) {
                    if (element.is('div') == true) {
                        element.addClass('loading-lazy');
                    } else {
                        element.parent().addClass('loading-lazy');
                    }
                };
                _config.afterLoad = function (element) {
                    if (element.is('div') == true) {
                        element.removeClass('loading-lazy');
                    } else {
                        element.parent().removeClass('loading-lazy');
                    }
                    this_obj.removeClass('lazy');
                };
                _config.effect = "fadeIn";
                _config.enableThrottle = true;
                _config.throttle = 500;
                _config.effectTime = 200;
                if (this_obj.closest('.megamenu').length > 0) {
                    _config.delay = 0;
                }
                this_obj.lazy(_config);
            });
        },
        init_carousel: function (arg) {
            if (arg == 'update') {
                $('.urus-swiper.swiper-container').each(function (i, e) {
                    var update_swiper = $(this).get(0).swiper;
                    update_swiper.update();

                });
                return;
            }
            $('.urus-swiper.swiper-container').not('.swiper-initialized').each(function (i, e) {
                var this_Swiper = $(this),
                    config = $(this).data('slick'),
                    responsive_info = $(this).data('responsive'),
                    breakpoints_data = {},
                    spaceBetween = (typeof config.slidesMargin !== 'undefined') ? config.slidesMargin : 0,
                    loop = config.infinite,
                    slidesPerColumn = (typeof config.rows !== 'undefined') ? config.rows : 1,
                    slidesPerView = (typeof config.slidesToShow !== 'undefined') ? config.slidesToShow : 4,
                    speed = (typeof config.speed !== 'undefined') ? config.speed : 300,
                    dots = (typeof config.dots !== 'undefined') ? config.dots : false,
                    arrows = (typeof config.arrows !== 'undefined') ? config.arrows : false,
                    autoplay = (typeof config.autoplay !== 'undefined') ? config.autoplay : false,
                    builder = $(this).data('builder');
                if (typeof responsive_info != "undefined" && responsive_info.length){
                    var max_breakpoint = 0,
                        min_breakpoint = 1920;
                    if (typeof builder != "undefined" && builder == 'elementor'){
                    } else {
                        builder = 'other';
                    }
                $.each(responsive_info, function (i, e) {
                        if (e.breakpoint > max_breakpoint) {
                            max_breakpoint = e.breakpoint;
                            slidesPerView =  e.settings.slidesToShow;
                        }
                        if (min_breakpoint > e.breakpoint){
                            min_breakpoint = e.breakpoint;
                        }
                                breakpoints_data[e.breakpoint] = {
                        'slidesPerGroup': e.settings.slidesToShow,
                        'slidesPerView': e.settings.slidesToShow,
                        'spaceBetween': (typeof e.settings.slidesMargin !== 'undefined') ? e.settings.slidesMargin : spaceBetween,
                    };
                });
                    if ( builder == 'other' && min_breakpoint > 0) {
                        breakpoints_data[0] = breakpoints_data[min_breakpoint];
                    }
                }
                var _configSlide = {
                    breakpoints: breakpoints_data,
                    spaceBetween: spaceBetween,
                    loop: loop,
                    autoplay: autoplay,
                    watchOverflow: true,
                    slidesPerColumn: slidesPerColumn,
                    slidesPerView: slidesPerView,
                    slidesPerGroup: slidesPerView,
                    speed: speed,
                    watchSlidesVisibility: true,
                    on: {
                        init: function () {
                            this_Swiper.addClass('swiper-initialized');
                            URUS.nav_swiper_center(this_Swiper);
                            var parent_row = this_Swiper.parents('.vc_row');
                            var this_swiper_api = this_Swiper.get(0).swiper;
                            if (parent_row.attr('data-vc-full-width') == 'true' ) {
                                //is Visual composer fullwidth row, update the swiper again
                                window.setTimeout(function(){
                                    //update the
                                    this_swiper_api.update();
                                },1000);
                            }
                        },
                        slideChangeTransitionEnd: function () {
                            URUS.lazy_load();
                        },
                        resize: function () {
                            URUS.nav_swiper_center(this_Swiper);
                        },
                    }
                };
                if (dots) {
                    _configSlide.pagination = {
                        el: this_Swiper.find('.swiper-pagination'),
                        type: 'bullets',
                        clickable: true,
                    };
                } else {
                    $(this).find('.swiper-pagination').remove();
                }
                if (arrows) {
                    _configSlide.navigation = {
                        nextEl: this_Swiper.siblings('.slick-arrow.next'),
                        prevEl: this_Swiper.siblings('.slick-arrow.prev'),
                    };
                } else {
                    this_Swiper.siblings('.slick-arrow.next').remove();
                    this_Swiper.siblings('.slick-arrow.prev').remove();
                }
                if (this_Swiper.hasClass('custom-slide')) {
                    var this_obj = this_Swiper;
                    this_obj.find('.swiper-wrapper>div').each(function () {
                        $(this).addClass('swiper-slide');
                    });
                }
                //Add class swiper-slide for custom slider
                var new_swiper = new Swiper(this_Swiper, _configSlide);
            });
        },

        woo_message: function () {
            $document.on('click', 'a.add_to_cart_button', function () {

                if ($(this).is('.product_type_variable, .isw-ready')) {

                }else{
                    $('a.add_to_cart_button').removeClass('recent-added');
                    $(this).addClass('recent-added');
                }
            });
            $document.on('click', 'button.single_add_to_cart_button', function () {
                $('button.single_add_to_cart_button').removeClass('recent-added');
                $(this).addClass('recent-added');
            });
            $document.on('click', '.button.variation-form-submit', function () {
                $('.button.variation-form-submit').removeClass('recent-added');
                $(this).addClass('recent-added');
                console.log('recent-added');
            });
            $document.on('change', '.urus-deal-add-to-cart input[name$="quantity"]', function () {
                $('form.cart input[name$="quantity"]').val($(this).val());
                $('form.cart input[name$="quantity"]').trigger('change');
            });
            $document.on('click', '.urus-deal-add-to-cart .urus-single-add-to-cart-deal', function () {
                $('form.cart button[name$="add-to-cart"]').trigger('click');
            });
            $body.on('adding_to_cart', function () {
                $('.js-drawer-open-cart.mobile-open-cart').html('<i class="fa fa-spinner fa-spin"></i>');
                $('.urus-single-add-to-cart-btn').css('pointer-events', 'none');
            });
            $body.on('added_to_cart', function (event, fragments, cart_hash, button) {
                //$('.js-drawer-open-cart.mobile-open-cart').html(urus_ajax_frontend.icon_cart + '<span class="cart-counter">' + fragments['urus-minicart'].count + '</span>');
                $('.js-drawer-open-cart.mobile-open-cart').html(urus_ajax_frontend.icon_cart + fragments['.cart-counter']);
                $('.urus-single-add-to-cart-btn').css('pointer-events', 'auto');
                $('.urus-single-add-to-cart-btn').html(familab_ajax.item_added_to_cart);
                if ($('.cart-type-drawer').length) {
                    //Check if added to cart from wishlist
                    if ($('#Familab_WishlistDrawer').hasClass('wishlist-opened') || $('#urus-quickview').hasClass('quickview-opened') || $('#urus-quickview').hasClass('is-visible')) {
                        $('#Familab_WishlistDrawer').trigger('CloseWishlist');
                        $('#urus-quickview').trigger('closeQuickView', 'add-to-cart');
                        familab.CartDrawer.open();
                    } else {
                        familab.CartDrawer.open();
                    }
                } else {
                    var $recentAdded = $('.add_to_cart_button.recent-added, button.single_add_to_cart_button.recent-added'),
                        $img = $recentAdded.closest('.product-item').find('img.img-responsive'),
                        pName = $recentAdded.closest('.product-item').find('.woocommerce-loop-product__title a').text().trim();
                        if (pName == '' && $('body.single-product.is-mobile').length) {
                            pName = $('#summary .product_title').text().trim();
                        }
                    var variable_recent_added = $('.variation-form-submit.recent-added');
                    $('.add_to_cart_button.product_type_variable.isw-ready').removeClass('loading');
                    // if add to cart from wishlist
                    if (!$img.length) {
                        $img = $recentAdded.closest('tr')
                            .find('.product-thumbnail img');
                    }
                    // if add to cart from single product page
                    if (!$img.length) {
                        $img = $recentAdded.closest('.summary')
                            .prev()
                            .find('.woocommerce-main-image img');
                    }

                    // if add to cart from quick variable product select
                    if (!$img.length) {
                        $img = variable_recent_added.closest('.product-item').find('img.img-responsive');
                    }

                    if (typeof pName == 'undefined' || pName == '') {
                        pName = $recentAdded.closest('.summary').find('.product_title').text().trim();
                    }
                    if (typeof pName == 'undefined' || pName == '') {
                        pName = $recentAdded.closest('tr').find('.product-name a').text().trim();
                    }


                    if (typeof pName == 'undefined' || pName == '') {
                        pName = variable_recent_added.closest('.product-item').find('.woocommerce-loop-product__title a').text().trim();
                    }// if add to cart from quick variable product select
                    var img_src = '';
                    if ($img.length) {
                        img_src = $img.attr('src');
                    }
                    // reset state after 5 sec
                    setTimeout(function () {
                        variable_recent_added.removeClass('added').removeClass('recent-added');
                        $recentAdded.removeClass('added').removeClass('recent-added');
                        $recentAdded.next('.added_to_cart').remove();
                    }, 5000);
                    $.iGrowl({
                        title: pName,
                        message: urus_ajax_frontend.added_to_cart_notification_text,
                        small: true,
                        delay: 3000,
                        placement: {
                            x: 'right',
                            y: 'bottom'
                        },
                        type: 'success',
                        image: {
                            src: img_src,
                            class: 'product-image'
                        },
                        animShow: 'fadeInRight',
                        animHide: 'fadeOutRight',
                    });
                }
            });
        },
        shopFilter: function () {
            $document.on('click', '.btn-filter', function () {
                $(this).closest('.shop-filter-sidebar').addClass('opened');
                return false;
            });
            $document.on('click', '.close-filter', function () {
                $(this).closest('.shop-filter-sidebar').removeClass('opened');
                return false;
            });
            $document.on('click', '.open-mobile-filters', function (e) {
                e.preventDefault();
                var mobile_shop_filter = $(this).closest('.mobile-shop-content').find('.mobile_shop_filter');
                mobile_shop_filter.toggleClass('opened');
                $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
            });
            $document.on('click', '.block-filter-dropdown .show-filter-btn', function (e) {
                e.preventDefault();
                var filter = $(this).closest('.shop-control').siblings('.filter-dropdown-content');
                filter.toggleClass('opened');
                $(this).toggleClass('active');
            });
            $document.on('click', '.block-filter-canvas .show-filter-btn', function (e) {
                e.preventDefault();
                var canvas_shop_filter = $(this).closest('.shop-control').siblings('.filter-canvas-content');
                canvas_shop_filter.toggleClass('opened');
                $(this).toggleClass('active');
                $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
            });
            $document.on('click', '.block-filter-drawer .show-filter-btn', function (e) {
                e.preventDefault();
                var drawer_shop_filter = $(this).closest('.shop-control').siblings('.drawer-fillter-wrapper');
                drawer_shop_filter.toggleClass('opened');
                $(this).toggleClass('active');
                setTimeout(function(){
                    URUS.masonry();
                },600);

            });
            $document.on('click', function (event) {
                if ($(event.target).closest('.mobile_shop_filter').length || $(event.target).hasClass('.mobile_shop_filter')) {
                    var mobile_shop_filter = $(event.target).closest('.mobile_shop_filter');
                    if (!$(event.target).closest(".mobile_shop_filter_content").length) {
                        event.preventDefault();
                        mobile_shop_filter.removeClass('opened');
                        $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
                    } else if ($(event.target).hasClass('close-mobile-filter')) {
                        event.preventDefault();
                        mobile_shop_filter.removeClass('opened');
                        $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
                    }
                } else if ($(event.target).closest('.filter-canvas-content').length || $(event.target).hasClass('.filter-canvas-content')) {
                    var canvas_shop_filter = $(event.target).closest('.filter-canvas-content');
                    if (!$(event.target).closest(".urus_filter_content_wrapper").length) {
                        event.preventDefault();
                        canvas_shop_filter.removeClass('opened');
                        $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
                    } else if ($(event.target).hasClass('close-block-filter-canvas')) {
                        event.preventDefault();
                        canvas_shop_filter.removeClass('opened');
                        $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
                    }
                }else if ($(event.target).prev('.accordion-filter-all').length) {
                    event.preventDefault();
                    $('.accordion-filter-all').removeClass('opened');
                    $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
                }
            });
            $document.on('click', '.urus-filter-accordion a', function () {
                var id = $(this).attr('href');
                if ($(this).closest('.widget-toggle').hasClass('active')) {
                    $(id).removeClass('opened');
                    $(this).closest('.widget-toggle').removeClass('active');
                } else {
                    $(this).closest('.urus-filter-accordion').find('.widget-toggle').removeClass('active');

                    $('.filter-accordion-content').find('.filter-widget-item').removeClass('opened');
                    $(id).addClass('opened');
                    $(this).closest('.widget-toggle').addClass('active');
                }
                return false;
            });
            $document.on('click', '.accordion-filter-all .list-widget a', function (e) {
                e.preventDefault();
                var this_obj = $(this),
                    id = this_obj.attr('href'),
                    all_wrrap = $(this).closest('.accordion-filter-all');
                if (this_obj.closest('.widget-list-item').hasClass('active')) {
                    $(id).removeClass('opened');
                    this_obj.closest('.widget-list-item').removeClass('active');
                } else {
                    var title = this_obj.data('title');
                    all_wrrap.find('.widget-list-item').removeClass('active');
                    all_wrrap.find('.filter-widget-item').removeClass('opened');
                    $(id).addClass('opened');
                    this_obj.closest('.widget-list-item').addClass('active');
                    all_wrrap.find('.sidedrawer__heading').html(title);
                    all_wrrap.find('.sidedrawer__heading_prev').addClass('visible');
                }
                return false;
            });
            $document.on('click', '.accordion-filter-all .sidedrawer__heading_prev', function (e) {
                e.preventDefault();
                var this_obj = $(this),
                    all_wrrap = this_obj.closest('.accordion-filter-all');
                all_wrrap.find('.filter-widget-item').removeClass('opened');
                all_wrrap.find('.widget-list-item').removeClass('active');
                var title = $('.accordion-filter-all').find('.sidedrawer__heading').data('title');
                all_wrrap.find('.sidedrawer__heading').html(title);
                this_obj.removeClass('visible');
                return false;
            });
            $document.on('click', '.accordion-filter-all .sidedrawer__heading_close', function (e) {
                e.preventDefault();
                $('.accordion-filter-all').removeClass('opened');
                $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
                return false;
            });
            $document.on('click', '.urus-filter-accordion .all-filter a', function (e) {
                e.preventDefault();
                $('.accordion-filter-all').toggleClass('opened');
                $body.toggleClass('disabled-scroll-body', !$body.hasClass('disabled-scroll-body'));
                return false;
            });
        },
        shopSideBar: function() {
            var close = {
                buttonClose: function () {
                    var _this = this;
                    $document.on("click",'.sidebar .close-block-sidebar', function (event) {
                        event.preventDefault();
                        _this.closeTab();
                    });
                },
                clickOutsideTab: function(){
                    var _this = this;
                    $document.on("click",'.mobile-sidebar-overlay', function (event) {
                        if ($(window).innerWidth() <= 991) {
                            event.preventDefault();
                            _this.closeTab();
                        }
                    });
                },
                closeTab: function () {
                    $("body:not(.single-product) .shop-page .sidebar").removeClass("opened");
                }
            };
            $document.on("click", 'body:not(.single-product) .shop-page .sidebar-button', function (event) {
                event.preventDefault();
                $("body:not(.single-product) .shop-page .sidebar").toggleClass("opened");
            });

            close.buttonClose();
            close.clickOutsideTab();
        },
        filterStatus: function () {
            if ($('.accordion-filter-all .widget-list-item a').length) {
                $('.accordion-filter-all .widget-list-item a').each(function () {
                    var id = $(this).attr('href');
                    if (!$(id).length) {
                        $(this).closest('.widget-list-item').addClass('disable');
                    }
                });
            }
        },
        product_thumb: function () {
            $document.on('click', '.swiper-wrapper.swiper-initialized li', function () {
                $(this).addClass('swiper-slide-active');
                $(this).siblings().removeClass('swiper-slide-active');
                return false;
            });
            if ($('.flex-control-thumbs').length) {
                $('.flex-control-thumbs').not('.swiper-initialized').each(function (i,e) {
                    var this_obj = $(this),
                        _responsive = JSON.parse(urus_ajax_frontend.product_thumb_data_responsive),
                        _config = JSON.parse(urus_ajax_frontend.product_thumb_data_slick),
                        direction = 'horizontal',
                        slidesPerView = 'auto',
                        spaceBetween = (typeof _config.slidesMargin !== 'undefined') ? _config.slidesMargin : 0;
                    var responsive_info = _responsive;
                    var breakpoints = {};

                    this_obj.addClass('swiper-wrapper');
                    this_obj.removeClass(' flex-control-thumbs');
                    this_obj.find('li').addClass('swiper-slide');
                    if (has_rtl) {
                        this_obj.wrap('<div class="swiper-container thumbs" dir="rtl" />');
                    } else {
                        this_obj.wrap('<div class="swiper-container thumbs" />');
                    }
                    if (_config.vertical == true) {
                        direction = 'vertical';
                        this_obj.parents('.swiper-container').wrap('<div class="swiper-thumbs-left" />');
                    } else {
                        $.each(responsive_info, function (i, e) {
                            breakpoints[e.breakpoint] = {
                                'slidesPerView': e.slidesToShow,
                                'spaceBetween': (typeof e.slidesMargin !== 'undefined') ? e.slidesMargin : spaceBetween,
                            };
                        });
                        slidesPerView = _config.slidesToShow;
                    }
                    var swiper_container = this_obj.parents('.swiper-container');
                    var product_thumbs_swiper = new Swiper(swiper_container, {
                        spaceBetween: spaceBetween,
                        breakpoints: breakpoints,
                        slidesPerView: slidesPerView,
                        direction: direction,
                        observe: true,
                        observeParents: true,
                        speed: 300,
                        freeMode: true,
                        freeModeSticky: true,
                        on: {
                            init: function () {
                                swiper_container.addClass('swiper-initialized');
                            }
                        }
                    });
                    this_obj.addClass('swiper-initialized flex-control-nav');
                });
            }
        },
        newsletter: function () {
            $document.on('click', '.newsletter-form-button', function () {
                var thisWrap = $(this).closest('.urus-newsletter-form');
                var email = thisWrap.find('input[name="email"]').val();
                if (thisWrap.hasClass('urus-loader')) {
                    return false;
                }
                var data = {
                    action: 'submit_mailchimp_via_ajax',
                    email: email
                };
                thisWrap.addClass('urus-loader');
                $.post(urus_ajax_frontend.ajaxurl, data, function (response) {
                    if ($.trim(response.success) == 'yes') {
                        thisWrap.find('input[name="email"]').val('');
                        $.iGrowl({
                            message: response.message,
                            small: true,
                            delay: 3000,
                            placement: {
                                x: 'right',
                                y: 'bottom'
                            },
                            type: 'success',
                            animShow: 'fadeInRight',
                            animHide: 'fadeOutRight',
                        });
                        if ($('.mfp-close')) {
                            $('.mfp-close').trigger('click');
                        }
                        $(document.body).trigger('urus_newsletter_success', response.message);
                    } else {
                        $.iGrowl({
                            message: response.message,
                            small: true,
                            delay: 3000,
                            placement: {
                                x: 'right',
                                y: 'bottom'
                            },
                            type: 'notice',
                            animShow: 'fadeInRight',
                            animHide: 'fadeOutRight',
                        });
                        $(document.body).trigger('urus_newsletter_error', response.message);
                    }
                    thisWrap.removeClass('urus-loader');
                });
                return false;
            });
        },
        filter_ajax: function () {
            if (urus_ajax_frontend.enable_ajax_filter == 0 || urus_ajax_frontend.is_shop == 0) return false;
            var container = '.urus-shop-page-content',
                fragment = '.urus-shop-page-content';
            if (urus_ajax_frontend.instant_filter == 0) {
                var container1 = '.urus_shop_control_top_wrapper';
                if ($(container1).length){
                    container = container1;
                    fragment = container1;
                }
            }
            var ajaxLinks = '.widget_product_categories a,.widget_layered_nav a,.urus_widget_orderby_filter a,.fiter-prices-link a,.urus_widget_brand a,.woocommerce-pagination a, a.switch-column',
                scrollToTop = function () {
                    if ($('#product-list-wapper').length) {
                        var h = 0;
                        if ($('#wpadminbar').length) {
                            h = $('#wpadminbar').innerHeight();
                        }
                        $('html, body').stop().animate({
                            scrollTop: $('body').offset().top - h,
                        }, 800);
                    }
                };
            $document.pjax(ajaxLinks, container, {
                timeout: 10000,
                scrollTo: false,
                fragment: fragment,
                dataType: 'text'
            });

            var filter_all = '.filter-actions a,.urus-filter-active a',
                active_filtered,
                filter_form;
            $document.pjax(filter_all, container, {
                timeout: 10000,
                scrollTo: false,
                fragment: fragment,
                dataType: 'text'
            });
            $document.on('click', '.widget_price_filter form .button', function (e) {
                var form = $('.widget_price_filter form');
                filter_form = form;
                $.pjax({
                    container: container,
                    fragment: fragment,
                    timeout: 10000,
                    url: form.attr('action'),
                    data: form.serialize(),
                    scrollTo: false,
                });
                return false;
            });

            $document.on('change', '.urus-widget-layered-nav select', function (e) {
                var t = $(this);
                setTimeout(function () {
                    var form = t.closest('.urus-widget-layered-nav').find('form');
                    filter_form = form;
                    $.pjax({
                        container: container,
                        fragment: fragment,
                        timeout: 10000,
                        url: form.attr('action'),
                        data: form.serialize(),
                        scrollTo: false,
                    });
                    return false;
                }, 100);
            });
            $document.on('change', '.filter_dropdown_price', function (e) {
                var t = $(this);
                setTimeout(function () {
                    var form = t.closest('.urus_widget_price_filter').find('form');
                    filter_form = form;
                    $.pjax({
                        container: container,
                        fragment: fragment,
                        timeout: 10000,
                        url: form.attr('action'),
                        data: form.serialize(),
                        scrollTo: false,
                    });
                    return false;
                }, 100);
            });
            $document.on('submit', '.woocommerce-ordering', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                if (form.hasClass('running')){
                    return false;
                }
                form.addClass('running');
                var currentURL=location.protocol + '//' + location.host + location.pathname;
                setTimeout(function () {
                    filter_form = form;
                    $.pjax({
                        container: container,
                        fragment: fragment,
                        timeout: 10000,
                        url: currentURL,
                        data: form.serialize(),
                        scrollTo: false,
                    });
                    form.removeClass('running');
                    return false;
                }, 100);
            });
            $document.on('change', '.woocommerce-ordering select', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                if (form.hasClass('running')){
                    return false;
                }
                form.submit();
            });
            $document.on('change', '.widget_product_categories select', function (e) {
                var t = $(this);
                setTimeout(function () {
                    var form = t.closest('.widget_product_categories').find('form');
                    filter_form = form;
                    $.pjax({
                        container: container,
                        fragment: fragment,
                        timeout: 10000,
                        url: form.attr('action'),
                        data: form.serialize(),
                        scrollTo: false,
                    });
                }, 100);
                return false;
            });
            $document.on('price_slider_change', function () {
                var form = $('.widget_price_filter form');
                filter_form = form;
                $.pjax({
                    container: container,
                    fragment: fragment,
                    timeout: 10000,
                    url: form.attr('action'),
                    data: form.serialize(),
                    scrollTo: false,
                });
            });
            $document.on('change', '.urus_widget_brand select', function (e) {
                var t = $(this);
                setTimeout(function () {
                    var form = t.closest('.urus_widget_brand').find('form');
                    filter_form = form;
                    $.pjax({
                        container: container,
                        fragment: fragment,
                        timeout: 10000,
                        url: form.attr('action'),
                        data: form.serialize(),
                        scrollTo: false,
                    });
                    return false;
                }, 100);
            });
            var sub_cat = '.product-subcategory-wapper .sub-cat-item a';
            $document.pjax(sub_cat, '#shop-page-wapper', {
                timeout: 10000,
                scrollTo: false,
                fragment: '#shop-page-wapper',
                dataType: 'text'
            });
            $document.on('pjax:error', function (xhr, textStatus, error) {
                $body.removeClass('urus-loader');
            });
            $document.on('pjax:beforeSend', function (xhr) {
                $body.addClass('urus-loader');
                if (typeof (xhr.relatedTarget) != 'undefined' && $(xhr.relatedTarget).hasClass('switch-column')) {
                    var col = $(xhr.relatedTarget).attr('data-col');
                    if (typeof col != "undefined"){
                        Cookies.set('woo_lg_items',col);
                    }
                }
            });
            $document.on('pjax:popstate', function (evt) {
                //disable pjax on browser back/forward
                if (evt.direction == 'back' || evt.direction == 'forward') {
                    pjax_popstate = true;
                }
            });
            $document.on('pjax:start', function (xhr) {
                active_filtered = '';
                var taget;
                if (typeof (xhr.relatedTarget) == 'undefined') {
                    if (typeof filter_form != 'undefined' && filter_form.length){
                        taget = filter_form;
                    }else{
                        return;
                    }
                }else{
                    taget = $(xhr.relatedTarget);
                }
                var filter_wapper = taget.closest('.urus-block-filter-wapper');
                if (filter_wapper.hasClass('opened') ){
                    active_filtered = '.urus-block-filter-wapper';
                }else{
                    if (filter_wapper.length){
                        if (filter_wapper.hasClass('filter-accordion-content')){
                            var accordion_opened = filter_wapper.find('.opened');
                            if (accordion_opened.length){
                                active_filtered = '#'+ accordion_opened.attr('id');
                            }
                        }else if(filter_wapper.hasClass('filter-drawer-content')){
                            active_filtered = '.drawer-fillter-wrapper';
                        }
                    }else{
                        filter_wapper = taget.closest('.sidedrawer__inner');
                        if (filter_wapper.length){
                            active_filtered = '#'+filter_wapper.find('.opened').attr('id');
                        }
                    }
                }
            });
            $document.on('pjax:success',function(xhr){
                var parent_active = $(active_filtered).closest('.accordion-filter-all');
                if (typeof  parent_active != 'undefined' && parent_active.length){
                    parent_active.addClass('opened');
                    parent_active.find('.sidedrawer__heading_prev').addClass('visible');
                    parent_active.find('.widget-list-item[data-id="'+active_filtered.replace('#','')+'"]').addClass('active');
                    $(active_filtered).addClass('opened');
                }else{
                    parent_active = $(active_filtered).closest('.filter-accordion-content');
                    if (typeof  parent_active != 'undefined' && parent_active.length){
                        $(active_filtered).addClass('opened');
                        parent_active.prev('.shop-control').find('.urus-filter-accordion .widget-toggle[data-id="'+active_filtered.replace('#','')+'"]').addClass('active');
                    }else{
                        $(active_filtered).addClass('opened');
                    }
                }
                if ($body.children('.select2-container').length){
                    $body.children('.select2-container').remove();
                }
                if (typeof (xhr.relatedTarget) != 'undefined') {
                    if (typeof (xhr.relatedTarget.className) != 'undefined') {
                        if ($(xhr.relatedTarget).hasClass('page-numbers')) {
                            scrollToTop();
                        }
                    }
                }
            });
            $document.on('pjax:end', function () {
                $('#shop-page-wapper').removeClass('all-products-loaded');
                $('.woocommerce-ordering').removeClass('running');
                URUS.init_carousel();
                URUS.urus_chosen();
                URUS.filterStatus();
                URUS.scrollbar();
                URUS.product_item_zoom();
                var $script = urus_ajax_frontend.response_script;
                for (var i in $script) {
                    $.getScript($script[i], function (data, textStatus, jqxhr) {
                    });
                }
                $( '.variations_form' ).each( function() {
                    $( this ).wc_variation_form();
                });
                $body.removeClass('urus-loader');
                if ($("body:not(.single-product) .shop-page .sidebar").length){
                    $("body:not(.single-product) .shop-page .sidebar").removeClass("opened");
                }
            });

        },
        footer_menu_toggle: function () {
            var w = $(window).width();
            if (w < 768) {
                $('.urus-custom-menu').each(function (i, item) {
                    if ($(this).find('.title').length > 0) {
                        $(this).addClass('closed');
                    } else {
                        $(this).removeClass('closed');
                    }
                });
            }
            $(window).on('resize', function () {
                w = window_resize_width;
                if (w < 768) {
                    $('.urus-custom-menu').each(function (i, item) {
                        if ($(this).find('.title').length > 0) {
                            $(this).addClass('closed');
                        } else {
                            $(this).removeClass('closed');
                        }
                    });
                } else {
                    $('.urus-custom-menu').removeClass('closed');
                }
            });

            $document.on('click', 'footer .urus-custom-menu .title a', function (event) {
                event.preventDefault();
                if (w > 767) return false;
                $(this).closest('.urus-custom-menu').toggleClass('closed');
                return false;
            });
        },
        back_history: function () {
            $document.on('click', '.back-history', function () {
                parent.history.back();
                return false;
            });
        },
        fillter_block_mobile: function () {
            $document.on('click', '.filter-toggle', function () {
                $('.urus-shop-top-filter,.block-filter-dropdown').addClass('open');
                return false;
            });
            $document.on('click', '.close-block-filter', function () {
                $('.urus-shop-top-filter,.block-filter-dropdown').removeClass('open');
                return false;
            });
        },
        sidebar_mobile: function () {
            $document.on('click', '.sidebar-toggle', function () {
                $('.sidebar').addClass('open');
                $(this).toggleClass('open');
                return false;
            });
            $document.on('click', '.close-block-sidebar', function () {
                $('.sidebar').removeClass('open');
                return false;
            });
        },
        single_add_to_cart_form: function () {
            $document.on('click', '.single-mobile-add-to-cart', function () {
                $('#summary').addClass('open').find('.cart').append('<a href="#" class="close-form-cart">' + urus_ajax_frontend.icon_close + '</a>');
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
            $document.on('click', '.close-form-cart', function () {
                $('#summary').removeClass('open').find('.close-form-cart').remove();
                return false;
            });
        },
        product_variation_ajax_add_to_cart: function () {
            $document.on('click', '.product-item:not(.product-type-external) .single_add_to_cart_button, #urus-quickview .single_add_to_cart_button', function (e) {
                e.preventDefault();
                var this_obj = $(this);
                if (!this_obj.hasClass('disabled')) {
                    var this_parent = $(this).parents('#urus-quickview');
                    if (this_parent.length){
                        var variation_id = this_parent.find('input[name=variation_id].variation_id').val();
                        if (variation_id == '' || variation_id === 0 ) {
                            this_parent.find('.woocommerce-variation.single_variation').html(wc_add_to_cart_variation_params.i18n_no_matching_variations_text);
                            return;
                        }
                    }
                    var _form = this_obj.closest('form'),
                        _data = _form.serializeObject();
                    if (this_obj.val()) {
                        _data.product_id = this_obj.val();
                    }
                    // Trigger event.
                    $(document.body).trigger('adding_to_cart', [this_obj, _data]);
                    this_obj.addClass('loading');
                    // Ajax action.
                    $.ajax({
                        type: 'POST',
                        url: urus_ajax_frontend.ajaxurl,
                        data: {
                            security: urus_ajax_frontend.security,
                            data: _data,
                            action: 'urus_add_cart_single_ajax'
                        },
                        success: function (response) {
                            if (!response) {
                                return;
                            }

                            // Redirect to cart option
                            if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
                                window.location = wc_add_to_cart_params.cart_url;
                                return;
                            }

                            // Trigger event so themes can refresh other areas.
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, this_obj]);
                            this_obj.removeClass('loading');
                        },
                    });
                }
                e.preventDefault();
            });
        },
        header_block_search: function () {
            $document.on('click', '.form-search-mobile >a', function () {
                $(this).closest('.form-search-mobile').toggleClass('open');
                return false;
            });
        },
        shortcode_products_loadmore: function () {
            $document.on('click', '.urus-products .loadmore-button', function () {
                var atts = $(this).data('atts');
                var page = parseInt($(this).attr('data-page'));
                var wapper = $(this).closest('.loadmore-wapper');
                wapper.addClass('loading');
                var data = {
                    action: 'urus_products_fronted_load_more',
                    security: urus_ajax_frontend.security,
                    atts: atts,
                    page: page
                };
                var t = $(this);
                $.post(urus_ajax_frontend.ajaxurl, data, function (response) {
                    wapper.removeClass('loading');
                    if (response.type == 'done') {
                        t.closest('.urus-products').find('.product-list-grid').append(response.data);
                        if (response.show_button == 0) {
                            wapper.remove();
                        } else {
                            t.attr('data-page', page + 1);
                        }
                    } else {
                        wapper.remove();
                    }
                });
                return false;
            });
        },

        scrollbar: function () {
            $('.widget_shopping_cart_content .product_list_widget, #yith-quick-view-content .summary.entry-summary ,.form-search-categories .chosen-container .chosen-results,.header-sidebar__inner,.woocommerce-mini-cart.cart_list, .section-demos .section-main,.promo-demo-list, .filter-canvas-content .urus_filter_content').mCustomScrollbar(
                {
                    setHeight: false,
                    autoHideScrollbar: true,
                }
            );
        },
        smooth_scroll_target: function () {
            $document.on('click', 'a[href^="#smooth_scroll_"]', function (event) {
                var el = $(this).attr("href") === "#" ? "" : jQuery.attr(this, 'href');
                if (el === "") {
                    return;
                }
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: $(el).offset().top
                }, 500);
            });
        },
        sticky_sidebar: function () {
            if ($('.urus-single-product-top').hasClass('sticky-layout')) {
                if (window_width < 768) return false;
                $('#summary').stickySidebar({
                    topSpacing: 120,
                    containerSelector: '.urus-single-product-top',
                    innerWrapperSelector: '.summary__inner__wapper'
                });
            }
            if ($('.urus-single-product-top .urus_sticky_single').length) {
                $('.urus-single-product-top .urus_sticky_single').each(function () {
                    $(this).stickySidebar({
                        topSpacing: 160,
                        containerSelector: '.urus-single-product-top',
                        innerWrapperSelector: '.sticky_content'
                    });
                });
            }
            if ($('.urus-content-inner  .sidebar-area').length && urus_ajax_frontend.enable_sticky_sidebar == 1) {
                $('.urus-content-inner  .sidebar-area').each(function () {
                    $(this).stickySidebar({
                        topSpacing: 120,
                        containerSelector: '.urus-content-inner',
                        innerWrapperSelector: '.sidebar__inner'
                    });
                });
            }
        },
        size_guide: function () {
            $document.on('click', '.product_size_chart__wapper a', function () {
                var id = $(this).data('id');
                var selft = $(this);
                var wap = selft.closest('.product_size_chart__wapper');
                wap.addClass('urus-loader');
                var data = {
                    action: 'urus_popup_size_chart',
                    id: id
                };
                $.post(urus_ajax_frontend.ajaxurl, data, function (response) {
                    $.magnificPopup.open({
                        items: {
                            src: '<div class="white-size-guid mfp-with-anim">' + response + '</div>', // can be a HTML string, jQuery object, or CSS selector
                            type: 'inline',

                        },
                        removalDelay: 500, //delay removal by X to allow out-animation
                        callbacks: {
                            beforeOpen: function () {
                                this.st.mainClass = 'mfp-zoom-in';
                            }
                        },
                        midClick: true
                    });
                    wap.removeClass('urus-loader');
                });
                return false;
            });
        },
        mini_cart_fixed: function () {
            $document.on('click', '.menu-cart-item.fixed .cart-link', function () {
                $(this).closest('.menu-cart-item.fixed').addClass('open');
                return false;
            });
            $document.on('click', '.close-mini-cart', function () {
                $(this).closest('.menu-cart-item.fixed').removeClass('open');
                return false;
            });
            $document.on('click', function (event) {
                if (!$(event.target).closest(".menu-cart-item.fixed").length && !$(event.target).hasClass('ajax_add_to_cart')) {
                    $(".menu-cart-item.fixed").removeClass('open');
                }
            });
        },
        urus_mobile_footer: function () {
            if ($(window).innerWidth() <= 991) {
                var lastScrollTop = 0;
                $(window).scroll(function (event) {
                    var st = $(this).scrollTop();
                    if (st > lastScrollTop) {
                        if ($(window).scrollTop() + $(window).height() + 55 >= $document.height()) {
                            $('.mobile-nav').addClass('is-sticky');
                        } else {
                            $('.mobile-nav').removeClass('is-sticky');
                        }
                    } else {
                        $('.mobile-nav').addClass('is-sticky');
                    }
                    lastScrollTop = st;
                });
            }
        },
        urus_tab: function () {
            /* Ovic Ajax Tabs */
            $document.on('click', '.urus-tab .tab-link a, .urus-accordion .panel-heading a', function (e) {
                e.preventDefault();
                var this_obj = $(this),
                    _data = this_obj.data(),
                    _tabID = this_obj.attr('href'),
                    _loaded = this_obj.closest('.tab-link,.urus-accordion').find('a.loaded').attr('href');

                if (_data.ajax == 1 && !this_obj.hasClass('loaded')) {
                    $(_tabID).closest('.tab-container,.urus-accordion').addClass('loading');
                    this_obj.parent().addClass('active').siblings().removeClass('active');
                    $.ajax({
                        type: 'POST',
                        url: urus_ajax_frontend.ajaxurl,
                        data: {
                            security: urus_ajax_frontend.security,
                            id: _data.id,
                            section_id: _data.section,
                            action: 'urus_get_tabs_shortcode'
                        },
                        beforeSend: function () {
                            $(_tabID).closest('.tab-container,.urus-accordion').fadeTo("slow", 0.3);
                        },
                        success: function (response) {
                            if (response.success == 'ok') {
                                $(_tabID).html($(response.html).find('.vc_tta-panel-body').html());
                                $('[href="' + _loaded + '"]').removeClass('loaded');
                                if (urus_ajax_frontend.enable_lazy){
                                    if ($(_tabID).find('.lazy').length > 0) {
                                        URUS.lazy_load($(_tabID));
                                    }
                                }
                                $(_tabID).trigger('urus_ajax_tabs_complete');
                                this_obj.addClass('loaded');
                            } else {
                                $(_tabID).closest('.tab-container,.urus-accordion').removeClass('loading');
                                $(_tabID).html('<strong>Error: Can not Load Data ...</strong>');
                            }

                            if ($(_tabID).find('.swiper-container').length > 0) {
                                URUS.init_carousel();
                            }
                            /* for accordion */
                            this_obj.closest('.panel-default').addClass('active').siblings().removeClass('active');
                            this_obj.closest('.urus-accordion').find(_tabID).slideDown(400);
                            this_obj.closest('.urus-accordion').find('.panel-collapse').not(_tabID).slideUp(400);

                        },
                        complete: function () {
                            setTimeout(function (_tabID, _tab_animated, _loaded) {
                                $(_tabID).closest('.tab-container,.urus-accordion').removeClass('loading');
                                $(_tabID).addClass('active').siblings().removeClass('active');
                                $(_loaded).html('');
                            }, 10, _tabID, _data.animate, _loaded);
                            $(_tabID).closest('.tab-container,.urus-accordion').fadeTo("slow", 1);
                            setTimeout(function () {
                                if ($(_tabID).find('.swiper-container:not(.updated)').length > 0) {
                                    URUS.init_carousel('update');
                                    $(_tabID).find('.swiper-container:not(.updated)').addClass("updated");
                                }
                            }, 100);
                        },
                        ajaxError: function () {
                            $(_tabID).closest('.tab-container,.urus-accordion').removeClass('loading');
                            $(_tabID).html('<strong>Error: Can not Load Data ...</strong>');
                        }
                    });

                }
                else {
                    this_obj.parent().addClass('active').siblings().removeClass('active');
                    $(_tabID).addClass('active').siblings().removeClass('active');
                    $(_tabID).closest('.tab-container').find('.tab-panel:not(.active)').fadeOut(500);
                    /* for accordion */
                    this_obj.closest('.panel-default').addClass('active').siblings().removeClass('active');
                    this_obj.closest('.urus-accordion').find(_tabID).slideDown(400);
                    this_obj.closest('.urus-accordion').find('.panel-collapse').not(_tabID).slideUp(400);
                    if ($(_tabID).find('.product-list-owl:not(.updated)').length){
                        URUS.init_carousel('update');
                        $(_tabID).find('.product-list-owl').addClass('updated');
                    }
                }
            });
            $document.on('click', '.canvas-tabs-title a', function (e) {
                e.preventDefault();
                var tab_id = $(this).data('tab_id'),
                    title_element = $(this).closest('.cv_tab_title');
                if (!title_element.hasClass('active')) {
                    $('.cv_tab_title').removeClass('active');
                    $('.wc-tab-canvas').removeClass('open');
                    $('#' + tab_id).addClass('open');
                    title_element.addClass('active');
                } else {
                    $('#' + tab_id).removeClass('open');
                    title_element.removeClass('active');
                }
            });

            $document.on('click', '.wc-tab-canvas', function (e) {
                e.preventDefault();
                var target = $(e.target),
                    target_close = true;
                if (target.closest('.tab_detail').length) {
                    target_close = false;
                }
                if (target.hasClass('close-tab') || target_close) {
                    $('.cv_tab_title').removeClass('active');
                    $('.wc-tab-canvas').removeClass('open');
                }
            });
        },
        miniCart: function () {
            var $minicart_content = $('.urus-mini-cart-content'),
                undoTimeout;

            $document.on('click', '.woocommerce-mini-cart-item .remove', function (e) {
                e.preventDefault();
                var $cart_item_key = $(this).data('cart_item_key'),
                    $item = $(this).closest('.woocommerce-mini-cart-item');
                var data = {
                    cart_item_key: $cart_item_key,
                    action: 'urus_remove_from_cart'
                };

                //remove last deleted item
                $item.siblings('.deleted').remove();


                //Animating for deleted item
                $item.css('height', $item.outerHeight());
                $item.addClass('deleted');
                $item.on('transitionend', function (e) {
                    $item.addClass('height0');
                });
                //Animating for deleted item
                $minicart_content.addClass('loading');
                $.post(urus_ajax_frontend.ajaxurl, data, function (response) {
                    $minicart_content.find('.mini-cart-undo').addClass('visible');

                    // wait 8 seconds before completely remove the items
                    undoTimeout = setTimeout(function () {
                        resetUndo();
                    }, 10000);
                    updateCartFragments('remove', response);

                    $minicart_content.removeClass('loading');
                });
                return false;
            });

            $document.on('click', '.mini-cart-undo a', function (e) {
                e.preventDefault();
                if (undoTimeout) {
                    clearInterval(undoTimeout);
                }

                var $item = $minicart_content.find('.woocommerce-mini-cart-item.deleted'),
                    cart_item_key = $item.find('.remove').data('cart_item_key');

                if ($minicart_content.find('.woocommerce-mini-cart-item').length > 0) {
                    $minicart_content.find('.woocommerce-mini-cart__empty-message').addClass('hidden');
                }
                $item.removeClass('deleted');
                var data = {
                    cart_item_key: cart_item_key,
                    action: 'urus_undo_remove_cart_item'
                };
                $.post(urus_ajax_frontend.ajaxurl, data, function (response) {
                    resetUndo();
                    updateCartFragments('undo', response);
                });
                return false;
            });

            var resetUndo = function () {
                if (undoTimeout) {
                    clearInterval(undoTimeout);
                }

                $minicart_content.find('.mini-cart-undo').removeClass('visible');
                $minicart_content.find('.woocommerce-mini-cart-item.deleted').remove();
            };

            var updateCartFragments = function (action, data) {
                if (action === 'remove' || action === 'undo') {
                    // just update cart count & cart total, don't update the product list
                    if (typeof data.fragments !== 'undefined') {
                        $.each(data.fragments, function (key, value) {
                            if (key === 'urus-minicart') {
                                var $emptyMessage = $minicart_content.find('.woocommerce-mini-cart__empty-message'),
                                    $total = $minicart_content.find('.woocommerce-mini-cart__total'),
                                    $buttons = $minicart_content.find('.woocommerce-mini-cart__buttons');

                                if (action == 'remove' && value.count == 0) {
                                    $emptyMessage.removeClass('hidden');
                                    $total.addClass('hidden');
                                    $buttons.addClass('hidden');

                                } else if (action == 'undo' && value.count > 0) {
                                    $total.removeClass('hidden');
                                    $buttons.removeClass('hidden');
                                }

                                // update cart count
                                var cc = $document.find('.cart-counter');
                                cc.html(value.count);
                                if (value.count == 0){
                                    cc.closest('.icon-count').addClass('empty');
                                } else {
                                    cc.closest('.icon-count').removeClass('empty');
                                }

                                // update cart total
                                $minicart_content.find('.woocommerce-mini-cart__total .woocommerce-Price-amount').html(value.total);
                            }
                        });
                    }

                    // if you are in the Cart page, trigger wc_update_cart event
                    if ($body.hasClass('woocommerce-cart')) {
                        $body.trigger('wc_update_cart');
                    }
                }

                $body.trigger('wc_fragments_refreshed');
            };
        },
        countdown: function () {
            if ($('.urus-countdown').length) {
                $('.urus-countdown').each(function () {
                    var time = $(this).data('datetime');
                    $(this).countdown(time, function (event) {
                        $(this).html(event.strftime('<span class="box-count days"><span class="num">%D</span> <span class="text">' + urus_ajax_frontend.days_text + '</span></span> <span class="box-count hrs"><span class="num">%H</span><span class="text">' + urus_ajax_frontend.hrs_text + '</span></span> <span class="box-count min"><span class="num">%M</span><span class="text">' + urus_ajax_frontend.mins_text + '</span></span> <span class="box-count secs"><span class="num">%S</span><span class="text">' + urus_ajax_frontend.secs_text + '</span></span>'));
                    });
                });
            }
        },
        menu_bar: function () {
            $document.on('click', '.model_menu_btn', function (e) {

                var mn = $(this).closest('.main-menu-wapper').find('.model_menu_wrapper');
                if (!mn.hasClass('opened')) {
                    mn.addClass('opened');
                    $body.addClass('urus_disabled_scroll');
                }
                return false;
            });
            $document.on('click', '.close_model_menu', function (e) {

                var mn = $(this).closest('.main-menu-wapper').find('.model_menu_wrapper');
                if (mn.hasClass('opened')) {
                    mn.removeClass('opened');
                    $body.removeClass('urus_disabled_scroll');
                }
                return false;

            });
            $document.on('click', 'html', function (e) {
                if ($(e.target).hasClass('model_menu_wrapper') && $(e.target).parents('.model_menu_wrapper').length == 0) {
                } else {
                    if (!$(e.target).parents('.model_menu_wrapper').length) {
                        $('.model_menu_wrapper').removeClass('opened');
                    }
                }
            });

            $document.on('click', '.hamburger-menu  .menu-item-has-children >a', function (e) {
                if (!$(e.target).closest('ul').find('.sub-menu').length) {
                    return;
                }
                e.preventDefault();
                var t = $(this);
                t.closest('ul').addClass('open-sub');
                t.closest('.menu-item').addClass('has-sub-open');

                $('.model_menu_wrapper .prev-menu').addClass('visible');
            });

            $document.on('click', '.menu-bar-item>a', function () {
                $(this).closest('.menu-bar-item').toggleClass('open');
                $body.toggleClass('header-sidebar-fixed-open');
                return false;
            });
            $document.on('click', '.close-header-sidebar', function () {
                $(this).closest('.menu-bar-item').removeClass('open');
                $body.removeClass('header-sidebar-fixed-open');
                return false;
            });
            $document.on('click', '.prev-menu', function () {
                var wap = $(this).closest('.model_menu__inner');
                if (wap.find('.open-sub').length) {
                    wap.find('.open-sub').each(function () {
                        if (!$(this).find('.open-sub').length) {
                            $(this).removeClass('open-sub').find('.has-sub-open').removeClass('has-sub-open');
                        }
                    });
                    if (!wap.find('.open-sub').length) {
                        $(this).removeClass('visible');
                    }
                }
                return false;
            });
        },
        header_sticky: function () {
            if ($body.find(".fullscreen-template").length){
                return;
            }
            var window_size = $body.innerWidth();
            var top = 0;
            if ($('#wpadminbar').length) {
                top += $('#wpadminbar').outerHeight();
            }
            var max_h = $('#header').outerHeight();
            var topSpacing = top + max_h + 10;
            topSpacing = '-' + topSpacing;
            if (urus_ajax_frontend.enable_sticky_header == 1 && $(".header-sticky").length > 0) {
                if (window_size > 991) {
                    var sticky = $(".header-sticky");
                    sticky.FmSticky({topSpacing: topSpacing});
                }
            }
        },
        label: function () {
            $('.product-item .group-control .group-right .hint--*').hover(function () {
                    $(this).addClass('hover');
                },
                function () {
                    $(this).removeClass('hover');
                });
        },
        quick_view: function () {
            $document.on('click', '#yith-quick-view-close', function () {
                $body.removeClass('disabled-scroll-body');
            });
        },
        urus_quick_view: function () {
            var $quickView = $('#urus-quickview');
            $(document).on('click','#urus-quickview .reset_variations', function(){
                $('#urus-quickview .attribute-select').val('').trigger('change');
            });
            $('#urus-quickview form').on('submit', function(e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
            });
            $(document).on('change','#urus-quickview .attribute-select', function(){
                var variationData = $('#urus-quickview .variations_form').data('product_variations');
                var template       = false,
                    variation_id   = '',
                    $template_html = '',
                    $check_variation = false;

                var attributes        = urus_getChosenAttributes($quickView),
                    currentAttributes = attributes.data;

                if ( attributes.count === attributes.chosenCount ) {
                    var matching_variations = urus_findMatchingVariations( variationData, currentAttributes ),
                        variation           = matching_variations.shift();
                    if ( variation ) {
                        if ( ! variation.variation_is_visible ) {
                            template = urus_wp_template( 'quickview-unavailable-variation-template' );
                        } else {
                            template     = urus_wp_template( 'quickview-variation-template' );
                            variation_id = variation.variation_id;
                        }

                        $template_html = template( {
                            variation: variation
                        } );
                        $template_html = $template_html.replace( '/*<![CDATA[*/', '' );
                        $template_html = $template_html.replace( '/*]]>*/', '' );
                        if (variation.is_in_stock) {
                            $('#urus-quickview .single_add_to_cart_button').text(urus_ajax_frontend.add_to_cart);
                            $('#urus-quickview .single_add_to_cart_button').prop('disabled', false);
                        }else{
                            $('#urus-quickview .single_add_to_cart_button').text(urus_ajax_frontend.unavailable);
                            $('#urus-quickview .single_add_to_cart_button').prop('disabled', true);
                        }
                        $('#urus-quickview .woocommerce-variation.single_variation').html($template_html);
                        $('#urus-quickview').find( 'input[name="variation_id"], input.variation_id' ).val( variation_id ).change();
                        $check_variation = true;
                    }
                }
                 if (!$check_variation) {
                    $('#urus-quickview').find( 'input[name="variation_id"], input.variation_id' ).val('').change();
                    $('#urus-quickview .woocommerce-variation.single_variation').html('');
                }
            });
            $document.on('click', '#urus-quickview  .woocommerce-product-gallery__image > a', function (e) {
                e.preventDefault();
            });

            if ($quickView.hasClass('quickview-style-02')) {
                $quickView.on('closeQuickView', function(){
                    closeQuickView();
                });
                var closeQuickView = function () {
                    $body.removeClass('js-drawer-open js-drawer-open-quickview');
                    $quickView.removeClass('quickview-opened');
                };
                var openQuickView = function () {
                    $body.addClass('js-drawer-open js-drawer-open-quickview');
                    $quickView.addClass('quickview-opened');
                    $quickView.find('.swiper-container').addClass('swiper-initialized');
                };
                // close the quick view when clicked outside
                $document.on('click', 'body.js-drawer-open-quickview', function (event) {
                    if ($(event.target).is('.quick-view-close') || $(event.target).closest('.quick-view-close').length || !$(event.target).closest("#urus-quickview").length) {
                        event.preventDefault();
                        closeQuickView();
                    }
                });
                // if user has pressed 'Esc'
                $document.keyup(function (event) {
                    if (event.which == '27') {
                        closeQuickView();
                    }
                });

                $document.on('click', '.quick-view-btn a', function (e) {
                    e.preventDefault();
                    var t = $(this);
                    if (t.hasClass('disabled')) {
                        return;
                    }
                    var productImage = t.closest('.product-item').find('.product-thumb');
                    if (!productImage.length) {
                        return false;
                    }
                    var productId = t.closest('.quick-view-btn').data('pid'),
                        selectedImage = productImage.find('.main-thumb img.wp-post-image');
                    var data = {
                        productId: productId,
                        action: 'urus_quick_view'
                    };

                    t.addClass('disabled loading');
                    $.ajax({
                        type: "POST",
                        url: familab_ajax.ajaxurl,
                        data: data,
                        success: function (response) {
                            var res = $(response);
                            $quickView.empty().html(response);
                            var q_owl = $quickView.find('.swiper-container.urus-swiper');
                            URUS.init_carousel(q_owl);
                            t.removeClass('disabled loading');
                            $quickView.find('.summary').mCustomScrollbar();
                            openQuickView();
                        },
                        error: function (data) {
                            t.removeClass('disabled loading');
                        }
                    });
                });
            }
            else {
                $quickView.on('closeQuickView', function(evt, arg){
                    if (arg == 'add-to-cart') {
                        $quickView.removeClass('is-visible');
                        $quickView.removeClass('updated');
                        $body.removeClass('quick-view-opened');
                        $('.product-item').removeClass('empty-box');
                        $quickView.removeAttr('style').removeAttr('data-product-id');
                        $quickView.find('.images').html('').removeAttr('class').addClass('images');
                        $quickView.find('.entry-summary').remove();
                    }else{
                        closeQuickView_s();
                    }

                });
                var sliderFinalWidth = 500, // the quick view image slider width
                    maxQuickWidth = 1000;
                var events = function () {
                    $document.on('click', '.quick-view-btn a', function (e) {
                        e.preventDefault();
                        var t = $(this);
                        if (t.hasClass('disabled')) {
                            return;
                        }
                        var productImage = t.closest('.product-item').find('.product-thumb');
                        if (!productImage.length) {
                            return false;
                        }
                        var productId = t.closest('.quick-view-btn').data('pid'),
                            selectedImage = productImage.find('.main-thumb img.wp-post-image'),
                            p_img = selectedImage.clone();

                        p_img.attr('width', sliderFinalWidth + 'px');
                        $("#urus-quickview .images").empty().append(p_img);

                        t.addClass('disabled');
                        $quickView.addClass('urus-loader');
                        $body.addClass('quick-view-opened');

                        var data = {
                            productId: productId,
                            action: 'urus_quick_view'
                        };

                        $("#urus-quickview").attr('data-featured-image', p_img.attr('src'));


                        $("#urus-quickview").css({
                            'top': (selectedImage.offset().top - $window.scrollTop()) + 'px',
                            'left': (selectedImage.offset().left) + 'px',
                            'width': (selectedImage.width()) + 'px'
                        });
                        animateQuickView(selectedImage, sliderFinalWidth, maxQuickWidth, 'open');

                        $.post(familab_ajax.ajaxurl, data, function (response) {
                            var res = $(response);
                            var new_obj = $('<div></div>');
                            new_obj.html(res.find('.urus-product-gallery__wrapper').clone());
                            var $b = new_obj.find('img').first();

                            if (typeof $b != 'undefined') {
                                $b.on('load', function (e) {
                                    $quickView.empty().html(response);
                                    var q_owl = $quickView.find('.swiper-container.urus-swiper');
                                    $quickView.find('.summary').mCustomScrollbar();
                                    if (q_owl.length > 0) {
                                        URUS.init_carousel(q_owl);
                                    }
                                    updateQuickView(maxQuickWidth);
                                });

                            } else {
                                if ($quickView.hasClass('updated')) {
                                    $quickView.find('.summary').html(res.find('.summary').html());
                                    $quickView.find('.summary').mCustomScrollbar();
                                    updateQuickView(maxQuickWidth);
                                } else {
                                    $quickView.addClass('updated');
                                }
                            }


                            $quickView.removeClass('urus-loader');
                            t.removeClass('disabled');
                        });
                        return false;
                    });

                    // close the quick view when clicked outside
                    $document.on('click', '.quick-view-close', function (event) {
                        event.preventDefault();
                        closeQuickView_s(sliderFinalWidth, maxQuickWidth);
                    });

                    // if user has pressed 'Esc'
                    $document.keyup(function (event) {
                        if (event.which == '27') {
                            closeQuickView_s(sliderFinalWidth, maxQuickWidth);
                        }
                    });
                    // center quick-view on window resize
                    $window.on('resize', function () {
                        if ($quickView.hasClass('is-visible')) {
                            resizeQuickView();
                        }
                    });

                };
                events();
                var resizeQuickView = function () {
                    var quickViewLeft = (
                            window_width - $quickView.width()
                        ) / 2,
                        quickViewTop = (
                            $window.height() - $quickView.height()
                        ) / 2;
                    $quickView.css({
                        'top': quickViewTop,
                        'left': quickViewLeft,
                    });
                };
                var animateQuickView = function (image, finalWidth, maxQuickWidth, animationType) {
                    // store some image data (width, top position, ...)
                    // store window data to calculate quick view panel position
                    var target = '#urus-quickview',
                        timeline = anime.timeline(),
                        parentListItem = image.parents('.product-item'),
                        topSelected = image.offset().top - $window.scrollTop(), // the selected image top value
                        leftSelected = image.offset().left, // the selected image left value
                        widthSelected = image.width(), // the selected image width
                        windowWidth = window_width,
                        windowHeight = $window.height(),
                        finalLeft = (
                            windowWidth - finalWidth
                        ) / 2,
                        finalHeight = 470,
                        finalTop = (
                            windowHeight - finalHeight
                        ) / 2,
                        quickViewWidth = (
                            windowWidth * 0.8 < maxQuickWidth
                        ) ? windowWidth * 0.8 : maxQuickWidth,
                        quickViewLeft = (
                            windowWidth - quickViewWidth
                        ) / 2;
                    if (animationType == 'open') {
                        // hide the image in the gallery
                        parentListItem.addClass('empty-box');
                        timeline.add({
                            targets: target,
                            top: [topSelected, finalTop + 'px'],
                            left: [leftSelected, finalLeft + 'px'],
                            width: [widthSelected, finalWidth + 'px'],
                            duration: 1200,
                            easing: 'easeOutExpo',
                            elasticity: 200,
                            begin: function (anim) {
                                $quickView.addClass('is-visible');
                            },
                            complete: function (anim) {
                            }
                        });
                    } else {
                        //timeline = anime.timeline();
                        $quickView.removeClass('add-content');
                        timeline.add({
                            targets: target,
                            left: [quickViewLeft + 'px', finalLeft + 'px'],
                            width: [quickViewWidth + 'px', finalWidth + 'px'],
                            duration: 500,
                            easing: 'linear',
                            begin: function (anim) {
                                var current_image = $quickView.find('.images .swiper-slide-active img').eq(0);

                                $quickView.find('.images').html(current_image);

                            },
                            complete: function (anim) {
                                $body.removeClass('quick-view-opened');
                            }
                        }).add({
                            targets: target,
                            top: [finalTop + 'px', topSelected],
                            left: [finalLeft + 'px', leftSelected],
                            width: [finalWidth + 'px', widthSelected],
                            duration: 500,
                            easing: 'easeInCubic',
                            begin: function (anim) {
                            },
                            complete: function (anim) {
                                $quickView.removeClass('is-visible');
                                $quickView.removeClass('updated');
                                $body.removeClass('quick-view-opened');
                                parentListItem.removeClass('empty-box');
                                $quickView.removeAttr('style').removeAttr('data-product-id');
                                $quickView.find('.images').html('').removeAttr('class').addClass('images');
                                if ($quickView.find('.entry-summary').length > 0) {
                                    $quickView.find('.entry-summary').remove();
                                }
                            }
                        });
                    }
                };
                var updateQuickView = function (maxQuickWidth) {
                    $quickView.find('.swiper-container').addClass('swiper-initialized');
                    // store some image data (width, top position, ...)
                    // store window data to calculate quick view panel position
                    var target = '#urus-quickview',
                        //timeline       = anime.timeline(),
                        windowWidth = window_width,
                        quickViewWidth = (
                            windowWidth * 0.8 < maxQuickWidth
                        ) ? windowWidth * 0.8 : maxQuickWidth,
                        quickViewLeft = (
                            windowWidth - quickViewWidth
                        ) / 2,
                        z = anime({
                            targets: target,
                            left: quickViewLeft + 'px',
                            width: quickViewWidth + 'px',
                            duration: 600,
                            easing: 'easeInOutQuad'
                        });
                    z.begin = function () {
                        $quickView.addClass('add-content');
                    };
                    z.complete = function () {
                        $document.trigger('qv_loader_stop');
                    };
                };
                var closeQuickView_s = function (finalWidth, maxQuickWidth) {
                    var selectedImage = $('.empty-box').find('.main-thumb img.wp-post-image');
                    if (selectedImage.length) {
                        animateQuickView(selectedImage, finalWidth, maxQuickWidth, 'close');
                    }
                };
            }
        },
        wishlist: function () {
            var updat_wishlist_count = function () {
                var data = {
                    action: 'urus_get_wishlist_count'
                };
                $.post(urus_ajax_frontend.ajaxurl, data, function (response) {
                    if (response == 0) {
                        $('.menu-wishlist-item a .count').remove();
                        return false;
                    }
                    if ($('.menu-wishlist-item a .icon .count').length) {
                        $('.menu-wishlist-item a .count').html(response);
                    } else {
                        $('.menu-wishlist-item .icon').append('<span class="count">' + response + '</span>');
                    }
                    var xMax = parseInt('6');
                    var shake = anime({
                        targets: '.menu-wishlist-item .count',
                        easing: 'easeInOutSine',
                        duration: 550,
                        translateY: [
                            {
                                value: xMax * -1,
                            },
                            {
                                value: xMax,
                            },
                            {
                                value: xMax / -2,
                            },
                            {
                                value: xMax / 2,
                            },
                            {
                                value: 0,
                            }
                        ],
                        autoplay: false,
                    });
                    shake.restart();
                });
            };
            $document.on('removed_from_wishlist', function (el, el_wrap) {
                updat_wishlist_count();
            });
            $document.on('added_to_wishlist', function () {
                $('#yith-wcwl-popup-message').remove();
                updat_wishlist_count();
            });
        },
        single_mobile_tab: function () {
            $document.on('click', '.woocommerce-tabs-mobile .tabs-mobile a', function () {
                var tabid = $(this).data('tab_id');
                $body.addClass('js-drawer-open');
                $('.woocommerce-tabs-mobile').find('.wc-tab-mobile').removeClass('open');
                $('#' + tabid).addClass('open');
                URUS.lazy_load();
                return false;
            });
            $document.on('click', '.wc-tab-mobile .close-tab', function () {
                $body.removeClass('js-drawer-open');
                $(this).closest('.wc-tab-mobile').removeClass('open');
                return false;
            });
        },
        search_json: function (jsonObj, key, sku) {
            var title_arr = [],
                content_arr = [],
                excerpt_arr = [];
            var search_key = key.toLowerCase(),
                current_lang = familab_ajax._urus_current_lang,
                products_ids = [];
            if (typeof sku == 'undefined'){
                sku = false;
            }
            if (sku) {
                //$(jsonObj).each(function(i, v){
                $.each(jsonObj,function(i, v){
                    if (typeof v.language == 'undefined' || (v.language == null) || (v.language != null && current_lang && v.language.language_code == current_lang)) {
                        if (v.title.toLowerCase().includes(search_key)) {
                            if (products_ids.indexOf(v.id) == -1){
                            title_arr.push(i);
                                products_ids.push(v.id);
                            }
                        } else if (v.excerpt.toLowerCase().includes(search_key) || v.content.toLowerCase().includes(search_key) || (typeof(v.product_sku) != "undefined" && v.product_sku.toString().toLowerCase().includes(search_key)) || (typeof(v.variant_sku) != "undefined" && v.variant_sku.join(' ').toLowerCase().includes(search_key)) ) {
                            if (products_ids.indexOf(v.id) == -1){
                            content_arr.push(i);
                                products_ids.push(v.id);
                            }
                        }
                    }
                });
            }else{
                $.each(jsonObj,function(i, v){
                    if (typeof v.language == 'undefined' || (v.language == null) || (v.language != null && current_lang && v.language.language_code == current_lang)) {
                        if (v.title.toLowerCase().includes(search_key)) {
                            if (products_ids.indexOf(v.id) == -1){
                            title_arr.push(i);
                                products_ids.push(v.id);
                            }
                        } else if (v.excerpt.toLowerCase().includes(search_key) || v.content.toLowerCase().includes(search_key) ) {
                            if (products_ids.indexOf(v.id) == -1){
                            content_arr.push(i);
                                products_ids.push(v.id);
                            }
                        }
                    }
                });
            }

            var new_arr = $.merge(title_arr, content_arr);
            return new_arr;
        },
        fillResult: function (products, results, nbs) {
            var new_html = '',
                new_results = null;
            if (results.length > nbs) {
                new_results = results.slice(nbs, results.length);
            } else {
                new_results = [];
            }
            $(results).each(function (i, e) {
                if (i < nbs) {
                    if (new_results.length > 0 && i + 2 == nbs) {
                        new_html += '<div class="search-result-item infinite-search">';
                    } else {
                        new_html += '<div class="search-result-item">';
                    }
                    new_html += '    <div class="search-thumb">';
                    new_html += '    <a href="' + products[e].url + '">';
                    new_html += '      <img src="' + products[e].img + '">';
                    new_html += '   </a>';
                    new_html += '    </div>';
                    new_html += '    <div class="search-info">';
                    new_html += '      <h3 class="product-title"><a href="' + products[e].url + '">' + products[e].title + '</a></h3>';
                    new_html += '      <div class="product-price">' + products[e].price_html + '</div>' + products[e].rating;
                    new_html += '    </div>';
                    new_html += '</div>';
                } else {
                    return false;
                }
            });
            $('#search_drawer_content').attr('data-products', JSON.stringify(new_results));
            $('#search_drawer_content').attr('data-ipp', nbs);
            $('.inline-search-result-wrapper .inline-search-result').attr('data-products', JSON.stringify(new_results));
            $('.inline-search-result-wrapper .inline-search-result').attr('data-ipp', nbs);
            return new_html;
        },
        urus_delay: function (callback, ms) {
            var timer = 0;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        },
        urus_instant_search: function () {
            // SEARCH STYLE DRAWER
            var products = Object.values(familab_ajax._urus_live_search_products);
            $('#search_drawer_content').attr('data-products', '[]').attr('data-ipp', '');
            //Disable submit if there is no option available
            var is_submit_form = $('#familab-search-mobile').attr('data-form-submit');
            if (is_submit_form == 'false' ) {
                $document.on('submit', '#familab-search-mobile, .mobile-search-input .form-search', function (evt) {
                    evt.preventDefault();
                    $(this).find('input[name="s"]').blur();
                });
            }

            $("#search_drawer_content").on('scroll', function () {
                var obj = $(this),
                    results = JSON.parse(obj.attr('data-products')),
                    ipp = obj.data('ipp'),
                    pos = $(this).offset().top + $(this).outerHeight();
                if (obj.find('.infinite-search').length > 0) {
                    var infinite_obj = obj.find('.infinite-search');
                    if (pos > infinite_obj.offset().top) {
                        infinite_obj.removeClass('infinite-search');
                        var new_html = URUS.fillResult(products, results, ipp);
                        $('#search_drawer_content .search-result').append(new_html);
                    }
                }
            });

            $('#familab-search-mobile input[name="s"]').on('keyup', URUS.urus_delay(function (e) {
                var q = $(this).val();
                var search_sku = false;
                if (q == '') {
                    $('#familab-search-mobile').find('.btn_clear_text').hide();
                    $('#search_drawer_content .search-result').html('');
                    $(this).attr('last-search-field', q);
                    return;
                }
                $('#familab-search-mobile').find('.btn_clear_text').show();
                if (q == $(this).attr('last-search-field')) { return;}
                if ( $('#familab-search-mobile').attr('data-search-sku') == 'true') {
                    search_sku = true;
                }
                if (products.length || !$.isEmptyObject(products)) {
                    $('#search_drawer_content').attr('data-products', '[]').attr('data-ipp', '');
                    $('#search_drawer_content .search-result').html('');
                    var new_html = '';
                    $('#search_drawer_content').scrollTop(0);
                    var found_values = URUS.search_json(products, q, search_sku);
                    if (found_values.length > 0) {
                        var ipp = 12;
                        new_html = URUS.fillResult(products, found_values, ipp);
                    } else {
                        new_html = familab_ajax.search_empty;
                    }
                    $('#search_drawer_content .search-result').html(new_html);
                    $(".search-drawer-inner").trigger('scroll');
                } else {
                    $('#search_drawer_content .search-result').html(familab_ajax.search_empty);
                }
                $(this).attr('last-search-field', q);
            }, 500));

            // SEARCH STYLE INLINE
            $('.familab-header-mobile.style2 .serchfield').on('input change', function () {
                var search_container = $('.familab-header-mobile.style2 .mobile-search-input');
                var search_result_field = search_container.find('.inline-search-result');
                var q = $(this).val();
                if (q == '') {
                    search_container.find('.button-search').removeClass('cancel-search').hide().html(urus_ajax_frontend.icon_search);
                    window.setTimeout(function () {
                        search_container.find('.button-search').fadeIn('fast');
                    }, 300);
                    search_result_field.html('');
                } else {
                    if (search_container.find('.button-search').hasClass('cancel-search')) {
                    } else {
                        search_container.find('.button-search').addClass('cancel-search').hide().html(urus_ajax_frontend.icon_close);
                        window.setTimeout(function () {
                            search_container.find('.button-search').fadeIn('fast');
                        }, 300);
                    }
                }
            });
            $('.familab-header-mobile.style2 .serchfield').on('keyup', URUS.urus_delay(function (e) {
                var search_container = $('.familab-header-mobile.style2 .mobile-search-input'),
                    search_result_field = search_container.find('.inline-search-result'),
                    q = $(this).val(),
                    search_sku = false;
                if (q == '') {
                    search_result_field.html('');
                    return;
                }
                if ( $('#familab-search-mobile').attr('data-search-sku') == 'true') {
                    search_sku = true;
                }

                if (products.length || !$.isEmptyObject(products)) {
                    var new_html = '';
                    search_result_field.scrollTop(0);
                    var found_values = URUS.search_json(products, q, search_sku);
                    if (found_values.length > 0) {
                        var ipp = 10;
                        new_html = URUS.fillResult(products, found_values, ipp);
                    } else {
                        new_html = familab_ajax.search_empty;
                    }
                    search_result_field.html(new_html);
                    $(".inline-search-result-wrapper").trigger('scroll');
                } else {
                    search_result_field.html(familab_ajax.search_empty);
                }
            }, 500));
            $(".familab-header-mobile.style2 .inline-search-result-wrapper").on('scroll', function () {
                var search_container = $('.familab-header-mobile.style2 .mobile-search-input');
                var search_result_field = search_container.find('.inline-search-result');

                var results = JSON.parse(search_result_field.attr('data-products'));
                var ipp = search_result_field.data('ipp');
                var pos = $(this).offset().top + $(this).outerHeight();
                if (search_result_field.find('.infinite-search').length > 0) {
                    var infinite_obj = search_result_field.find('.infinite-search');
                    if (pos > infinite_obj.offset().top + infinite_obj.outerHeight()) {
                        infinite_obj.removeClass('infinite-search');
                        var new_html = URUS.fillResult(products, results, ipp);
                        search_result_field.append(new_html);
                    }
                }
            });
            $document.on('click', '.familab-header-mobile.style2 .cancel-search', function (e) {
                e.preventDefault();
                $('.familab-header-mobile.style2 .serchfield').val('');
                $('.familab-header-mobile.style2 .serchfield').trigger('change');

            });
            $document.on('click', '#familab-search-mobile .btn_clear_text', function (e) {
                e.preventDefault();
                $('#familab-search-mobile input[name="s"]').val('').attr('last-search-field', '').focus();
                $('#search_drawer_content .search-result').html('');
                $('#familab-search-mobile').find('.btn_clear_text').hide();
            });
            $('#search_drawer_content').mCustomScrollbar(
                {
                    setHeight: false,
                    autoHideScrollbar: true,
                    callbacks:{
                        onScrollStart: function(){
                            $('#search_drawer_content').trigger('scroll');
                        }
                    }
                }
            );
        },
        ajax_login: function () {
            // Perform AJAX login on form submit
            $('form#login').on('submit', function (e) {
                $('form#login').addClass('urus-loader');
                $('form#login .status').html('');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: urus_ajax_frontend.ajaxurl,
                    data: {
                        'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                        'username': $('form#login #username').val(),
                        'password': $('form#login #password').val(),
                        'security': $('form#login #security').val()
                    },
                    success: function (data) {
                        $('form#login').removeClass('urus-loader');

                        if (data.loggedin == true) {
                            $('form#login .status').html('<div class="alert alert-info">' + data.message + '</div>');
                            document.location.href = urus_ajax_frontend.ajax_login_redirecturl;
                        } else {
                            $('form#login .status').html('<div class="alert alert-warning">' + data.message + '</div>');
                        }
                    }
                });
                e.preventDefault();
            });
        },
        init_multiscroll: function () {
            if ($('#multiscroll').length) {
                var entering = true;
                var multiscroll = $('#multiscroll').multiscroll({
                    verticalCentered: true,
                    scrollingSpeed: 300,
                    easing: 'easeInQuart',
                    loopBottom: false,
                    loopTop: false,
                    css3: true,
                    normalScrollElements: '.container-wapper > div:not(.multiscroll-wrapp), .container-wapper > section:not(.multiscroll-wrapp)',
                    keyboardScrolling: false,
                    allowScrolling: false,
                    touchSensitivity: 5,
                    // Custom selectors
                    sectionSelector: '.ms-section',
                    leftSelector: '.ms-left',
                    rightSelector: '.ms-right',
                    //events
                    onLeave: function (index, nextIndex, direction) {
                    },
                    afterLoad: function (anchorLink, index) {
                    },
                    afterRender: function () {
                    },
                    afterResize: function () {
                    },
                });
                $(window).on('resize', function () {
                    if ($(window).width() < 992) {
                        $.fn.multiscroll.destroy();
                        var multi_instance = $('#multiscroll'),
                            left_panel = multi_instance.find('.ms-left'),
                            right_panel = multi_instance.find('.ms-right'),
                            multiscroll_html = '',
                            left_length = left_panel.find('.ms-section').length;
                        multiscroll_html += '<div class="multiscroll-mobile">';
                        left_panel.find('.ms-section').each(function (i, e) {
                            var html_left = $("<div />").append($(this).clone()).html();
                            var html_right = $("<div />").append(right_panel.find('.ms-section').eq(left_length - 1 - i).clone()).html();
                            multiscroll_html += html_left;
                            multiscroll_html += html_right;
                        });
                        multiscroll_html += '</div>';
                        if ($('.multiscroll-mobile').length == 0) {
                            $('.multiscroll-wrapp').after(multiscroll_html);
                        }

                    } else {
                        $('.multiscroll-mobile').remove();
                        $.fn.multiscroll.build();
                    }
                });

            }
        },
        category_menu: function () {
            $document.on('click', '.widget_product_categories .toggle-cat', function () {
                if ($(this).next('ul').is(':visible')) {
                    $(this).next('ul').slideUp('fast');
                    $(this).removeClass('active');
                } else {
                    $(this).closest('ul').children('.has-subnav').children('.active').next('ul').slideUp('fast');
                    $(this).closest('ul').children('.has-subnav').children('.active').removeClass('active');
                    $(this).next().slideToggle('fast');
                    $(this).addClass('active');
                }
                return false;
            });
        },
        urus_wishlist: function () {
            $('#Familab_WishlistDrawer').on('LoadWishlist', function () {
                var wishlist_drawer = $(this);
                if ($('body').hasClass('js-drawer-open-left')) {
                    familab.LeftDrawer.close();
                }
                wishlist_drawer.addClass('wishlist-opened');
                $body.addClass('js-drawer-open js-drawer-open-wishlist');
                //open the drawer
                var new_html = '';
                var products_array = [];
                var cookies_products = Cookies.get(urus_ajax_frontend.site_id + '_urus_wishlist_cookies');
                if (typeof (cookies_products) == 'undefined' || cookies_products == '' || cookies_products == '[]') {
                    $('#Familab_WishlistDrawer .wishlist-inner').html('<div class="wishlist-empty">' + urus_ajax_frontend.empty_wishlist + '</div>');
                } else {
                    products_array = JSON.parse(cookies_products);
                    $(products_array).each(function (i, product) {
                        new_html = '';
                        if (!$('.single-wishlist-item[data-product-id=' + product.id + ']').length) {
                            new_html += '<div class="single-wishlist-item removed" data-product-id="' + product.id + '">';
                            new_html += '   <div class="product-remove">';
                            new_html += '      <a href="javascript:void(0);" class="remove_from_wishlist" title="' + urus_ajax_frontend.remove_from_wishlist + '">×</a>';
                            new_html += '   </div>';
                            new_html += '   <div class="product-thumbnail">';
                            new_html += '       <a href="' + product.url + '">';
                            new_html += '           <img width="600" height="735" src="' + product.image + '" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" >';
                            new_html += '       </a>';
                            new_html += '   </div>';
                            new_html += '   <div class="product-info">';
                            new_html += '       <div class="product-name">';
                            new_html += '          <a href="' + product.url + '">' + product.title + '</a>';
                            new_html += '       </div>';
                            new_html += '       <div class="product-price">';
                            new_html += product.price;
                            new_html += '       </div>';
                            new_html += '       <div class="product-add-to-cart">';
                            if (product.is_simple) {
                                new_html += '       <a href="/?add-to-cart=' + product.id + '" data-quantity="1" class="add_to_cart_button ajax_add_to_cart" data-product_id="' + product.id + '" aria-label="' + urus_ajax_frontend.add_to_cart + '" rel="nofollow">' + urus_ajax_frontend.add_to_cart + '</a>';
                            } else {
                                new_html += '       <a href="' + product.url + '" class="add_to_cart_button" aria-label="Select options" rel="nofollow">' + urus_ajax_frontend.view_product + '</a>';
                            }
                            new_html += '       </div>';
                            new_html += '    </div>';
                            new_html += '</div>';

                            if ($('#Familab_WishlistDrawer .wishlist-empty').length) {
                                $('#Familab_WishlistDrawer .wishlist-inner').html(new_html);
                            } else {
                                $('#Familab_WishlistDrawer .wishlist-inner').prepend(new_html);
                            }
                            window.setTimeout(function () {
                                $('.single-wishlist-item[data-product-id=' + product.id + ']').removeClass('removed');
                            }, 350);
                        }
                    });
                }

                $('#Familab_WishlistDrawer .wishlist-count').html(products_array.length);
            });

            $('#Familab_WishlistDrawer').on('CloseWishlist', function () {
                $('#Familab_WishlistDrawer').removeClass('wishlist-opened');
                $body.removeClass('js-drawer-open js-drawer-open-wishlist');
            });

            $document.on('click', '.js-urus-wishlist', function () {
                $('#Familab_WishlistDrawer').trigger('LoadWishlist');
            });
            $document.on('click', '.js-close-wishlist', function () {
                $('#Familab_WishlistDrawer').trigger('CloseWishlist');
            });

            $document.on('click', '#Familab_WishlistDrawer .added_to_cart', function (e) {
                if ($('.cart-type-drawer').length) {
                    e.preventDefault();
                    //Check if added to cart from wishlist
                    if ($('#Familab_WishlistDrawer').hasClass('wishlist-opened')) {
                        $('#Familab_WishlistDrawer').trigger('CloseWishlist');
                        familab.CartDrawer.open();
                    } else {
                        familab.CartDrawer.open();
                    }

                }
            });

            $document.on('click', '.remove_from_wishlist', function () {
                var current_item = $(this).parents('.single-wishlist-item'),
                    cookie_products = Cookies.get(urus_ajax_frontend.site_id + '_urus_wishlist_cookies'),
                    products_array = JSON.parse(cookie_products),
                    selected_id = current_item.attr('data-product-id');
                $(products_array).each(function (i, e) {
                    if (e.id == selected_id) {
                        products_array.splice(i, 1);
                    }
                });
                if (products_array.length == 0) {
                    Cookies.remove(urus_ajax_frontend.site_id + '_urus_wishlist_cookies');
                } else {
                    Cookies.set(urus_ajax_frontend.site_id + '_urus_wishlist_cookies', JSON.stringify(products_array));
                }
                current_item.addClass('removed');

                window.setTimeout(function () {
                    current_item.remove();
                }, 600);
                $('#Familab_WishlistDrawer .wishlist-count').html(products_array.length);
                URUS.check_wishlist_state();
                //Update wishlsitb buttons status
            });

            $document.on('click', '.urus-add-to-wishlist-btn', function () {
                var product_info = $(this).data('product-info'),
                    product_id = product_info.id,
                    product_title = product_info.title,
                    product_url = product_info.url,
                    product_price_html = product_info.price,
                    product_img_url = product_info.thumb,
                    product_type = product_info.type,
                    is_simple = false,
                    trigger_open = false;
                if ($(this).attr('data-auto-open') == 0 && $(this).hasClass('wishlist-added')) {
                    trigger_open = true;
                }else if($(this).attr('data-auto-open') == 1 ) {
                    trigger_open = true;
                }

                if (product_type == 'simple') {
                    is_simple = true;
                }

                var cookie_products = Cookies.get(urus_ajax_frontend.site_id + '_urus_wishlist_cookies');
                var products_array = [];
                if (typeof (cookie_products) == 'undefined') {
                    Cookies.set(urus_ajax_frontend.site_id + '_urus_wishlist_cookies', JSON.stringify(products_array));
                    //cookie not set, creating new cookie...
                } else {
                    products_array = JSON.parse(cookie_products);
                    //get products array from Cookie
                }

                //If the product ID not in array, add them to new array list
                var is_duplicate = false;
                $(products_array).each(function (i, e) {
                    if (e.id == product_id) {
                        is_duplicate = true;
                    }
                });
                if (is_duplicate == false) {
                    products_array.push({
                        "id": product_id,
                        "title": product_title,
                        "price": product_price_html,
                        "url": product_url,
                        "image": product_img_url,
                        "is_simple": is_simple
                    });
                    Cookies.set(urus_ajax_frontend.site_id + '_urus_wishlist_cookies', JSON.stringify(products_array));
                    //If the product ID not in array, add them to new array list
                }

                if (trigger_open) {
                    $('#Familab_WishlistDrawer').trigger('LoadWishlist');
                    //Show wishlist on button clicked
                }else{
                    $.iGrowl({
                        title: product_title,
                        message: urus_ajax_frontend.urus_added_to_wishlist_text,
                        small: true,
                        delay: 3000,
                        placement: {
                            x: 'right',
                            y: 'bottom'
                        },
                        type: 'success',
                        image: {
                            src: product_img_url,
                            class: 'product-image'
                        },
                        animShow: 'fadeInRight',
                        animHide: 'fadeOutRight',
                    });
                    //Show notification for product added to wishlist
                }

                URUS.check_wishlist_state();
                //Update wishlsitb buttons status

            });
            $document.on('click', 'body.js-drawer-open-wishlist', function (evt) {
                var wl_drawer = $('#Familab_WishlistDrawer');
                if (wl_drawer.has(evt.target).length == 0 && !wl_drawer.is(evt.target)) {
                    wl_drawer.trigger('CloseWishlist');
                }
            });
        },
        check_wishlist_state: function () {
            var products_array = [];
            $document.find('.urus-add-to-wishlist-btn').removeClass('wishlist-added');
            $document.find('.urus-add-to-wishlist-btn').attr('aria-label', urus_ajax_frontend.add_to_wishlist);
            var cookies_products = Cookies.get(urus_ajax_frontend.site_id + '_urus_wishlist_cookies');
            if (typeof (cookies_products) == 'undefined' || cookies_products == '' || cookies_products == '[]') {
                //DO nothing
            } else {
                products_array = JSON.parse(cookies_products);
                $(products_array).each(function (i, product) {
                    var current_button = $document.find('.urus-add-to-wishlist-btn[data-product-id="' + product.id + '"]');
                    current_button.addClass('wishlist-added');
                    current_button.attr('aria-label', urus_ajax_frontend.view_wishlist);
                });
            }
        },
        nav_swiper_center: function (container) {
            if (container.hasClass('nav-center') || container.parents(".urus-slide").hasClass("nav-center")) {
                var nav;
                if (container.hasClass("custom-slide")) {
                    nav = container.closest(".urus-slide").find('.slick-arrow');
                } else {
                    nav = container.closest('.slide-inner').find('.slick-arrow');
                }
                if (!nav.length || typeof nav === "null" || typeof nav === "undefined") {
                    return;
                }
                var height = 0,
                    images = container.find("figure img");

                if (images.length <= 0) {
                    return;
                }

                images.each(function (i, item) {
                    var tmp_h = item.offsetHeight;
                    height = tmp_h > height ? tmp_h : height;
                });
                if (!height || typeof height == "undefined") {
                    return;
                }
                nav.css("top", height / 2 + "px");
            }
        },
        urus_compare: function () {
            $('#urus-compare img').on('load', function (evt) {
                $(evt.target).removeClass('img_loading');
                if ($('#urus-compare img.img_loading').length == 0) {
                    hide_compare_loader();
                }
            });
            $document.on('click', '.compare-panel-btn', function (e) {
                e.preventDefault();
                open_compare();
            });
            var show_compare_loader = function () {
                $('#urus-compare .compare-loader').addClass('urus-loader active');
            };
            var hide_compare_loader = function () {
                $('#urus-compare .compare-loader').removeClass('urus-loader active');
            };
            var open_compare = function () {
                show_compare_loader();
                check_compare_state();
                generate_compare_heading();

                $('#urus-compare').addClass('active');
                $('#urus-compare .compare-heading').show();

            };
            var minimize_compare = function () {
                $('#urus-compare').removeClass('extended');
                $('#urus-compare .compare-table').hide();
                $('#urus-compare .compare-heading').fadeIn();
                $body.removeClass('modal-open');
            };

            var close_compare = function () {
                $('#urus-compare').removeClass('active');
                $('#urus-compare').removeClass('extended');
                $('#urus-compare .compare-table').fadeOut();
                $('#urus-compare .compare-heading').show();
                $body.removeClass('modal-open');
            };
            var extend_compare = function () {
                $('#urus-compare .compare-heading').hide();
                $('#urus-compare').addClass('extended');
                $('#urus-compare .compare-table').fadeIn();
                $body.addClass('modal-open');

            };
            var check_compare_state = function () {
                var products_array = [];
                $document.find('.urus-compare').removeClass('compare-added');
                var cookies_products = Cookies.get(urus_ajax_frontend.site_id + '_urus_compare_cookies');
                if (typeof (cookies_products) == 'undefined' || cookies_products == '' || cookies_products == '[]') {
                    //DO nothing
                } else {
                    products_array = JSON.parse(cookies_products);
                    $(products_array).each(function (i, product) {
                        var current_button = $document.find('.urus-compare[data-product-id="' + product.id + '"]');
                        current_button.addClass('compare-added');
                    });
                }
            };
            var generate_compare_heading = function () {
                var new_html = '';
                var products_array = [];
                var cookies_products = Cookies.get(urus_ajax_frontend.site_id + '_urus_compare_cookies');

                var single_compare_items = $('#urus-compare .single-compare-item');
                var placeholder_url = single_compare_items.attr('data-placeholder');
                single_compare_items.attr('data-product-id', '');
                single_compare_items.addClass('placeholder');
                single_compare_items.find('.product-small-thumbnail .small-thumb-img').attr('src', placeholder_url);
                single_compare_items.find('.product-thumbnail-hover .big-thumb-img').attr('src', placeholder_url);
                single_compare_items.find('.product-title').html('');

                if (typeof (cookies_products) == 'undefined' || cookies_products == '' || cookies_products == '[]') {

                } else {
                    products_array = JSON.parse(cookies_products);
                    $(products_array).each(function (i, product) {
                        var current_item = single_compare_items.eq(i);
                        current_item.attr('data-product-id', product.id);
                        current_item.removeClass('placeholder');
                        current_item.find('.product-small-thumbnail .small-thumb-img').attr('src', product.image);
                        current_item.find('.product-small-thumbnail .small-thumb-img').addClass('img_loading');
                        current_item.find('.product-thumbnail-hover .big-thumb-img').attr('src', product.image);
                        current_item.find('.product-thumbnail-hover a').attr('href', product.url);
                        current_item.find('.product-title').html(product.title);

                    });
                }
            };

            var generate_compare_content = function (data) {

                var all_attrs = {};
                var attribute_values = {};
                var row_begin = 4;
                var table_compare = $('#urus-compare .compare-table tbody');
                $.each(data, function (i, e) {
                    var p_id = e.id;
                    $.each(e.attribute_label, function (k, v) {
                        if (all_attrs[k] != v) {
                            all_attrs[k] = v;
                        }
                    });
                    attribute_values[p_id] = e.attribute_values;

                });
                //Get all available attributes
                $.each(all_attrs, function (k, v) {
                    if (table_compare.find('tr.compare_attr_' + k).length == 0) {
                        var row = '<tr class="compare-attr compare_attr_' + k + '">';
                        row += '<th>' + v + '</th>';
                        row += '</tr>';
                        table_compare.find('tr').eq(row_begin).after(row);
                    }

                });
                //Generate all rows

                $('#urus-compare tr.image td').remove();
                $('#urus-compare tr.price td').remove();
                $('#urus-compare tr.add-to-cart td').remove();
                $('#urus-compare tr.description td').remove();
                $('#urus-compare tr.stock td').remove();
                $('#urus-compare tr.remove-item td').remove();
                $('#urus-compare tr.compare-attr td').remove();
                //reset all rows

                $.each(data, function (i, product) {
                    var image_html = '',
                        price_html = '',
                        add_to_cart_html = '',
                        description_html = '',
                        stock_html = '',
                        remove_item_html = '';

                    image_html += '<td data-product-id="' + product.id + '">';
                    image_html += '<div class="product-thumb">';
                    image_html += '<img src="' + product.thumb + '" alt="' + product.title + '" >';
                    image_html += '</div>';
                    image_html += '<div class="product-title">';
                    image_html += '   <a href="' + product.url + '">' + product.title + '</a>';
                    image_html += '</div>';
                    image_html += '</td>';

                    price_html += '<td data-product-id="' + product.id + '">';
                    price_html += '<div class="price-wrap">';
                    price_html += product.price;
                    price_html += '</div>';
                    price_html += '</td>';

                    add_to_cart_html += '<td data-product-id="' + product.id + '">';
                    add_to_cart_html += '<div class="add-to-cart-wrap">';
                    if (product.type == 'simple') {
                        add_to_cart_html += '       <a href="/?add-to-cart=' + product.id + '" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart" data-product_id="' + product.id + '" aria-label="' + urus_ajax_frontend.add_to_cart + '" rel="nofollow">' + urus_ajax_frontend.add_to_cart + '</a>';
                    } else {
                        add_to_cart_html += '       <a href="' + product.url + '" class="add_to_cart_button button" aria-label="' + urus_ajax_frontend.view_product + '" rel="nofollow">' + urus_ajax_frontend.view_product + '</a>';
                    }
                    add_to_cart_html += '</div>';
                    add_to_cart_html += '</td>';

                    description_html += '<td data-product-id="' + product.id + '">';
                    description_html += '<div class="desc-wrap">';
                    description_html += product.desc;
                    description_html += '</div>';
                    description_html += '</td>';

                    stock_html += '<td data-product-id="' + product.id + '">';
                    stock_html += '<div class="desc-wrap">';
                    stock_html += product.available;
                    stock_html += '</div>';
                    stock_html += '</td>';

                    remove_item_html += '<td data-product-id="' + product.id + '">';
                    remove_item_html += '<div class="remove-compare-wrap">';
                    remove_item_html += '<a href="javascript:void(0);" class="remove_from_compare-extend button" data-product-id="' + product.id + '">'+urus_ajax_frontend.remove_btn+'</a>';
                    remove_item_html += '</div>';
                    remove_item_html += '</td>';


                    $('#urus-compare tr.image').append(image_html);
                    $('#urus-compare tr.price').append(price_html);
                    $('#urus-compare tr.add-to-cart').append(add_to_cart_html);
                    $('#urus-compare tr.description').append(description_html);
                    $('#urus-compare tr.stock').append(stock_html);
                    $('#urus-compare tr.remove-item').append(remove_item_html);

                    $.each(all_attrs, function (k, v) {
                        var pa_values = attribute_values[product.id];
                        var single_value = pa_values[k];
                        var col_html = '<td class="product-attributes product_' + product.id + '">';
                        $.each(single_value, function (i, e) {
                            col_html += e;
                            if (i < single_value.length - 1) {
                                col_html += ', ';
                            }
                        });
                        col_html += '</td>';

                        $('#urus-compare tr.compare_attr_' + k).append(col_html);

                    });
                });

                $('#urus-compare').addClass('compared');
            };

            $document.on('click', '.urus-compare', function () {

                var maximum_compare = 4;
                var product_info = $(this).data('product-info'),
                    product_id = product_info.id,
                    product_title = product_info.title,
                    product_url = product_info.url,
                    product_thumb = product_info.thumb,
                    products_array = [],
                    cookie_products = Cookies.get(urus_ajax_frontend.site_id + '_urus_compare_cookies');
                //var product_ids = [];
                if (typeof (cookie_products) == 'undefined') {
                    Cookies.set(urus_ajax_frontend.site_id + '_urus_compare_cookies', JSON.stringify(products_array));
                    //cookie not set, creating new cookie...
                } else {
                    products_array = JSON.parse(cookie_products);
                    //get products array from Cookie
                }
                //If the product ID not in array, add them to new array list
                var is_duplicate = false;
                $(products_array).each(function (i, e) {
                    if (e.id == product_id) {
                        is_duplicate = true;
                    }
                });
                if (is_duplicate == false) {
                    if (products_array.length >= maximum_compare) {
                        //Compare list is full, need to remove 1 before add to cookie
                        products_array.splice(0, 1);
                    }
                    products_array.push({
                        "id": product_id,
                        "title": product_title,
                        "url": product_url,
                        "image": product_thumb
                    });
                    Cookies.set(urus_ajax_frontend.site_id + '_urus_compare_cookies', JSON.stringify(products_array));
                    //If the product ID not in array, add them to new array list
                    $('#urus-compare').removeClass('compared');
                }
                open_compare();
            });
            $document.on('click', '.compare-close-btn', function () {
                close_compare();
            });

            $document.on('click', '.compare-minimize-btn', function () {
                minimize_compare();
            });
            $document.on('click', '.compare-buttons .clear-btn', function () {
                Cookies.remove(urus_ajax_frontend.site_id + '_urus_compare_cookies');

                $('#urus-compare').removeClass('compared');
                open_compare();
            });

            $document.on('click', '.remove_from_compare', function () {
                var product_id = $(this).parents('.single-compare-item').attr('data-product-id');
                var current_cookie = Cookies.get(urus_ajax_frontend.site_id + '_urus_compare_cookies');
                var products_array = JSON.parse(current_cookie);
                $(products_array).each(function (i, e) {
                    if (e.id == product_id) {
                        products_array.splice(i, 1);
                    }
                });
                Cookies.set(urus_ajax_frontend.site_id + '_urus_compare_cookies', JSON.stringify(products_array));
                $('#urus-compare').removeClass('compared');
                open_compare();
            });
            $document.on('click', '.remove_from_compare-extend', function () {
                var product_id = $(this).attr('data-product-id');
                var current_cookie = Cookies.get(urus_ajax_frontend.site_id + '_urus_compare_cookies');
                var products_array = JSON.parse(current_cookie);
                $(products_array).each(function (i, e) {
                    if (e.id == product_id) {
                        products_array.splice(i, 1);
                    }
                });
                Cookies.set(urus_ajax_frontend.site_id + '_urus_compare_cookies', JSON.stringify(products_array));
                $('#urus-compare').removeClass('compared');
                $('#urus-compare .compare-table td[data-product-id=' + product_id + ']').remove();
                generate_compare_heading();
            });


            $document.on('click', '.begin-compare-btn', function () {
                var compare_products = [];
                $('#urus-compare .single-compare-item').not('.placeholder').each(function () {
                    compare_products.push($(this).attr('data-product-id'));
                });
                if (compare_products.length == 0) {
                    return;
                }
                if (!$('#urus-compare').hasClass('compared')) {
                    show_compare_loader();
                    $.ajax({
                        type: 'POST',
                        url: urus_ajax_frontend.ajaxurl,
                        data: {
                            security: urus_ajax_frontend.security,
                            data: compare_products,
                            action: 'urus_compare_products'
                        },
                        success: function (response) {
                            if (!response) {
                                return;
                            }
                            generate_compare_content(response);
                            hide_compare_loader();
                        },
                    });
                }
                extend_compare();
            });
        },
        product_item_zoom: function () {
            if ($('a.product-item-zoom').length) {
                $('a.product-item-zoom').each(function () {
                    var src = $(this).data('src');
                    $(this).zoom({
                        touch: false,
                        url: src
                    });
                });
            }
        },
        wooPriceSlider: function () {
            // woocommerce_price_slider_params is required to continue, ensure the object exists
            if (typeof woocommerce_price_slider_params === 'undefined' || $('.price_slider_amount #min_price').length < 1 || !$.fn.slider) {
                return false;
            }
            var $slider = $('.price_slider');
            if ($slider.slider('instance') !== undefined) return;
            // Get markup ready for slider
            $('input#min_price, input#max_price').hide();
            $('.price_slider, .price_label').show();
            // Price slider uses $ ui
            var min_price = $('.price_slider_amount #min_price').data('min'),
                max_price = $('.price_slider_amount #max_price').data('max'),
                current_min_price = parseInt(min_price, 10),
                current_max_price = parseInt(max_price, 10);

            if ($('.products').attr('data-min_price') && $('.products').attr('data-min_price').length > 0) {
                current_min_price = parseInt($('.products').attr('data-min_price'), 10);
            }
            if ($('.products').attr('data-max_price') && $('.products').attr('data-max_price').length > 0) {
                current_max_price = parseInt($('.products').attr('data-max_price'), 10);
            }

            $slider.slider({
                range: true,
                animate: true,
                min: min_price,
                max: max_price,
                values: [current_min_price, current_max_price],
                create: function () {

                    $('.price_slider_amount #min_price').val(current_min_price);
                    $('.price_slider_amount #max_price').val(current_max_price);

                    $(document.body).trigger('price_slider_create', [current_min_price, current_max_price]);
                },
                slide: function (event, ui) {

                    $('input#min_price').val(ui.values[0]);
                    $('input#max_price').val(ui.values[1]);

                    $(document.body).trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
                },
                change: function (event, ui) {

                    $(document.body).trigger('price_slider_change', [ui.values[0], ui.values[1]]);
                }
            });
            setTimeout(function () {
                $(document.body).trigger('price_slider_create', [current_min_price, current_max_price]);
                if ($slider.find('.ui-slider-range').length > 1) $slider.find('.ui-slider-range').first().remove();
            }, 10);
        },
        urus_quick_add_variable: function (){
            if (urus_ajax_frontend.enable_variation_loop_product != 1 || urus_ajax_frontend.enable_quick_add_loop_product != 1){
                return;
            }
            $(document).on('click', '.product-item.product-type-variable  .product_type_variable.add_to_cart_button', function(evt){
                evt.preventDefault();
                var variation_form = $(this).parents('.product-item').find('.select-option-extend');
                variation_form.toggleClass('active');
                 $(this).parents('.product-item').toggleClass('select_exend_opened');

            });
            $(document).on('click', '.product-item.product-type-variable  .close-form', function(evt){
                evt.preventDefault();
                var variation_form = $(this).parents('.product-item').find('.select-option-extend');
                variation_form.removeClass('active');
                $(this).parents('.product-item').removeClass('select_exend_opened');

            });
            $(document).on('change','.product-item.product-type-variable .select-option-extend .attribute-select', function(e){
                var form_container = $(this).parents('.product-item.product-type-variable').find('.select-option-extend .variations_form');
                var product_item = form_container.parents('.product-item.product-type-variable');
                var variationData = form_container.data('product_variations');
                var template       = false,
                    variation_id   = '',
                    $template_html = '',
                    $check_variation = false;

                var attributes        = urus_getChosenAttributes(form_container),
                    currentAttributes = attributes.data;
                if ( attributes.count === attributes.chosenCount ) {
                    var matching_variations = urus_findMatchingVariations( variationData, currentAttributes ),
                        variation           = matching_variations.shift();
                    if ( variation ) {
                        if ( ! variation.variation_is_visible ) {
                            template = urus_wp_template( 'quickview-unavailable-variation-template' );
                        } else {
                            template     = urus_wp_template( 'quickview-variation-template' );
                            variation_id = variation.variation_id;
                        }

                        $template_html = template( {
                            variation: variation
                        } );
                        $template_html = $template_html.replace( '/*<![CDATA[*/', '' );
                        $template_html = $template_html.replace( '/*]]>*/', '' );
                        if (variation.is_in_stock) {
                            product_item.find('.variation-form-submit').text(urus_ajax_frontend.add_to_cart);
                            product_item.find('.variation-form-submit').prop('disabled', false);
                        }else{
                            product_item.find('.variation-form-submit').text(urus_ajax_frontend.unavailable);
                            product_item.find('.variation-form-submit').prop('disabled', true);
                        }
                        product_item.find('.woocommerce-variation.single_variation').html($template_html);
                        product_item.find( 'input[name="variation_id"], input.variation_id' ).val( variation_id ).change();
                        $check_variation = true;
                    }
                }
                 if (!$check_variation) {
                    product_item.find( 'input[name="variation_id"], input.variation_id' ).val('').change();
                    product_item.find('.woocommerce-variation.single_variation').html('');
                }
            });

            $(document).on('click', '.product-item.product-type-variable  .variation-form-submit', function(evt){
                evt.preventDefault();
                var this_obj = $(this);
                var product_item = this_obj.parents('.product-item.product-type-variable');
                var product_id = product_item.find('input[name=product_id]').val();
                var variation_id = product_item.find('input[name=variation_id]').val();
                if (!this_obj.hasClass('disabled')) {
                    if (variation_id == '' || variation_id === 0) {
                        product_item.find('.woocommerce-variation.single_variation').html(wc_add_to_cart_variation_params.i18n_no_matching_variations_text);
                        return;
                    }
                    var _form = product_item.find('form'),
                        _data = _form.serializeObject();
                    // Trigger event.
                    //clean duplicated data
                    $.each(_data, function(attr,value_arr){
                        if (Array.isArray(value_arr)) {
                            _data[attr] = value_arr[0];
                        }
                    });
                    $(document.body).trigger('adding_to_cart', [this_obj, _data]);
                    this_obj.addClass('urus-loader disabled');
                    // Ajax action.
                    $.ajax({
                        type: 'POST',
                        url: urus_ajax_frontend.ajaxurl,
                        data: {
                            security: urus_ajax_frontend.security,
                            data: _data,
                            action: 'urus_add_cart_single_ajax'
                        },
                        success: function (response) {
                            if (!response) {
                                return;
                            }
                            // Trigger event so themes can refresh other areas.
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, this_obj]);
                            this_obj.removeClass('urus-loader disabled');
                        },
                    });
                }
            });
        },
        vertical_menu: function () {
            var has_veritcal_menu = $(".header .vertical-menu").length;
            if (has_veritcal_menu){
                var wrapper_menu = $(".vertical-wrapper ");
                var item_show = 0;
                var always_open = wrapper_menu.hasClass('always-open');
                var block_title = wrapper_menu.find(".block-title");
                var default_open = wrapper_menu.hasClass("menu-open");
                var btn_event = {
                    toggle_menu: function () {
                        if (!always_open) {
                            $document.on('click', ".vertical-wrapper .block-title", function (event) {
                                event.preventDefault();
                                wrapper_menu.toggleClass("menu-opened");
                            });
                        }
                        return;
                    },
                };

                if (!always_open){
                    btn_event.toggle_menu();
                }
                if (default_open){
                    wrapper_menu.addClass("menu-opened");
                }
            }
            return;
        },
        slick_slider: function () {
            //elementor slickslider
            $('.urus-slick-slider .urus-slides').not('.slick-initialized').each(function() {
                $(this).slick($(this).data('slider_options'));
            });
        },
    };

    $(window).scroll(function () {
        URUS.scroll();
        var scrollPercent = Math.round(100 * $(window).scrollTop() / ($document.height() - $(window).height()));
        var $round = $('.backtotop-round'),
            roundRadius = $round.find('circle').attr('r'),
            roundCircum = 2 * roundRadius * Math.PI,
            roundDraw = scrollPercent * roundCircum / 100;
        $round.css('stroke-dasharray', roundDraw  + ' 999');
    });
    $document.ajaxComplete(function () {
        URUS.ajaxComplete();
    });
    $(window).on('resize', function () {
        URUS.onResize();
    });
    $document.ready(function () {
        URUS.init();
        if ( $('.post').length ) { $('.post').fitVids(); }
        //disable scroll
        $document.on('click','.yith-wcqv-button-wapper a.yith-wcqv-button, .compare-button a.compare',function () {
            $body.addClass('disabled-scroll-body');
        });
        $document.on('click','#cboxClose, #cboxOverlay',function (e) {
            e.preventDefault();
            $body.removeClass('disabled-scroll-body');
        });
        $('.product-video-button').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false
        });

        $document.on('click','.switch-column',function (e) {
            e.preventDefault();
        });
        $document.on('click','.header-promo-control',function(e){
            e.preventDefault();
            $(this).closest('.header-promo').addClass('closed');
            $body.addClass('promo-closed');
            $body.animate({
                'padding-top': 0,
            }, 500);
        });
        $('.menu-myaccount-item.popup>a').magnificPopup({
            type:'inline',
            midClick: true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
            callbacks: {
                beforeOpen: function() {
                    this.st.mainClass = 'mfp-zoom-in';
                },
                open: function() {

                },
                close: function() {
                    // Will fire when popup is closed
                }
                // e.t.c.
            }
        });

        $('.product-360-button').magnificPopup({
            type:'inline',
            midClick: true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
            callbacks: {
                open: function() {
                    $(window).trigger('resize');
                },
                close: function() {
                    // Will fire when popup is closed
                }
                // e.t.c.
            }
        });
        shortcode_video();
    });

    $document.keyup(function(e){
        //press ESC
        if( e.keyCode === 27 ){
            $body.removeClass('disabled-scroll-body');
        }
    });
    $document.on('yith_woocompare_open_popup', function () {
        $document.on('click','#cboxClose',function () {
            $body.removeClass('disabled-scroll-body');
        });
    });
    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if ( o[ this.name ] ) {
                if ( !o[ this.name ].push ) {
                    o[ this.name ] = [ o[ this.name ] ];
                }
                o[ this.name ].push(this.value || '');
            } else {
                o[ this.name ] = this.value || '';
            }
        });
        return o;
    };
    $('#header').ready(function() {
        if ($(".preloader").length){
            setTimeout(function (){
                $(".preloader").fadeOut();
            },1000);
        }
    });


    $document.on('focus','.drawer_input input',function () {
        $body.addClass('search-drawer-has-focus');
    });

    $document.on('found_variation',function (e,variation) {
        var form = $(e.target);
        if (form.hasClass('has_changed') ){
            if ($body.hasClass('single-product')){
                var single_product = form.closest('.urus-single-product-top');
                if (single_product.find('.single-product-gallery').length){
                    var gallery_img_acvite = single_product.find('.single-product-gallery-item[data-id_img="' + variation.image_id + '"]');
                    if (typeof gallery_img_acvite != "undefined" &&  gallery_img_acvite.length){
                        $('html, body').animate({
                            scrollTop: gallery_img_acvite.offset().top
                        }, 600);
                    }
                }else if (single_product.find('.single-product-gallery__slider__wrapper').length){
                    var single_slide = single_product.find('.single-product-gallery__slider__wrapper').get(0).swiper,
                        single_slide_active = single_product.find('.single-product-gallery-item[data-id_img="' + variation.image_id + '"]');
                    if (typeof single_slide_active != "undefined" &&  single_slide_active.length){
                        var single_slide_index = single_product.find('.single-product-gallery__slider__wrapper .swiper-slide').index(single_slide_active);
                        single_slide.slideTo(single_slide_index);
                    }else{
                        single_slide.slideTo(0);
                    }
                }
            }else {
                var product = form.closest('.product'),
                    id_img = variation.image_id,
                    gallery_slide = product.find('.urus-gallery-top');
                if (typeof (gallery_slide) != "undefined" && gallery_slide.length) {
                    var gallery_img = gallery_slide.find('.slide_img[data-id_img="' + id_img + '"]');
                    var my_gallery = $(product).find('.urus-gallery-top').get(0).swiper;
                    if (gallery_img.length) {
                        var gallery_index = gallery_slide.find('.slide_img').index(gallery_img);
                        my_gallery.slideTo(gallery_index);
                    } else {
                        my_gallery.slideTo(0);
                    }
                } else {
                    var item_slider = product.find('.urus-product-item-slider');
                    if (typeof (item_slider) != "undefined" && item_slider.length) {
                        var slide_img = item_slider.find('.slide_img[data-id_img="' + id_img + '"]'),
                            my_slider = $(product).find('.urus-product-item-slider').get(0).swiper;
                        if (slide_img.length) {
                            var slide_index = item_slider.find('.slide_img').index(slide_img);
                            my_slider.slideTo(slide_index);
                        } else {
                            my_slider.slideTo(0);
                        }
                    }
                }
            }
        }
    });


    $document.on('variation_gallery_images',function () {
        URUS.product_thumb();
    });
    $document.on('variation_gallery_images_slider',function () {
        URUS.init_carousel();
    });
    function shortcode_video() {

        /*
            script urus short code video
         */
        //Video by youtube link
        // YouTube API stuff
        var tag = document.createElement("script");
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName("script")[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        var iframeIds = [];
        var iframeObjects = [];
        var iframes = document.querySelectorAll(".urus_shortcode_video iframe, .urus_shortcode_video video");

        iframes.forEach(function(iframe, i) {
            var iframeId = "urus-iframe-video"+i;
            iframe.setAttribute("id", iframeId);
            iframeIds.push(iframeId);
        });

        window.onresize = function () {
            iframes.forEach(function(iframe, i) {
                if (iframe.tagName.toLowerCase() !== "iframe"){
                    return;
                }
                var iframwWidth = iframe.offsetWidth;
                iframe.setAttribute('height', iframe.offsetWidth / 1.77+"px");
            });
        };

        window.onYouTubeIframeAPIReady = function () {
            iframeIds.forEach(function(iframeId) {
                var videoElement = document.getElementById(iframeId);
                if (videoElement.tagName.toLowerCase() !== "iframe"){
                    return;
                }
                var videoId = videoElement.getAttribute('data-videoid');
                var iframwWidth = videoElement.offsetWidth;
                var player = new YT.Player(iframeId, {
                    videoId: videoId,
                    events: {
                        onReady: onPlayerReady,
                        onStateChange: onPlayerStateChange
                    }
                });
            });
        };
        function onPlayerStateChange(event) {
            click_on_image(event);
        }

        function onPlayerReady(event) {
            var iframeObject = event.target;
            var iframeElement = iframeObject.a;
            var videoContainer = iframeElement.closest(".urus_shortcode_video");
            var play = videoContainer.querySelector(".play");
            var stop = videoContainer.querySelector(".pause");
            var iframwWidth = iframeElement.attributes.width.value;

            if (iframwWidth.indexOf("%") != -1){
                iframeElement.setAttribute('height', iframeElement.offsetWidth / 1.78+"px");
            }

            // Push current iframe object to array
            iframeObjects.push(iframeObject);
            if (typeof play != "undefined" && play != null && typeof stop != "undefined" && stop != null){
                play.addEventListener("click", function() {
                    iframe_play(iframeObject, iframeElement);
                });

                stop.addEventListener("click", function() {
                    iframe_pause(iframeObject, iframeElement);
                });
            }
            click_on_image(event);
        }
        function iframe_play(obj, el){
            obj.playVideo();
            el.closest(".urus_shortcode_video").classList.add('isPlaying');
        }
        function iframe_pause(obj, el){
            obj.pauseVideo();
            el.closest(".urus_shortcode_video").classList.remove('isPlaying');
        }
        function click_on_image(event){
            var iframeObject = event.target;
            var iframeElement = iframeObject.a;
            var iframeContainer = iframeElement.closest(".urus_shortcode_video");
            var backgroundImage =  iframeContainer.querySelector(".video-background img");
            if (backgroundImage !== null){
                backgroundImage.addEventListener('click', function () {
                    if (event.data === -1){
                        //unstarted
                        return;
                    }
                    if (event.data === YT.PlayerState.PLAYING){
                        iframe_pause(iframeObject, iframeElement);
                    }
                    else{
                        iframe_play(iframeObject, iframeElement);
                    }
                });
            }
            if (event.data == YT.PlayerState.PLAYING) {
                iframeContainer.classList.add('isPlaying');
            }
            else{
                //status: pause, unstarted, stop
                iframeContainer.classList.remove('isPlaying');
            }
        }
        //Video by external link
        window.onload = function () {
            iframeIds.forEach(function(iframeId) {
                var videoElement = document.getElementById(iframeId);
                var videoContainer = videoElement.closest('.urus_shortcode_video');
                var play = videoContainer.querySelector(".play");
                var backgroundImage = videoContainer.querySelector(".video-background img");
                var stop = videoContainer.querySelector(".pause");
                if (videoElement.tagName.toLowerCase() !== "video"){
                    return;
                }

                if (typeof play == "undefined" || play == null || typeof stop == "undefined" || stop == null){
                    videoElement.setAttribute('controls', '');
                    return;
                }
                videoElement.addEventListener('ended', function () {
                    videoContainer.classList.remove('isPlaying');
                });
                if (videoElement.paused === false){
                    videoContainer.classList.add('isPlaying');
                }
                if (backgroundImage !== null){
                    backgroundImage.addEventListener('click', function () {
                        event.stopPropagation();
                        if (!videoElement.paused){
                            video_play(videoElement, videoContainer);
                        }
                        else{
                            video_pause(videoElement, videoContainer);
                        }
                    });
                }
                play.addEventListener('click',function () {
                    videoElement.play();
                    videoContainer.classList.add('isPlaying');
                });
                stop.addEventListener('click',function () {
                    videoElement.pause();
                    videoContainer.classList.remove('isPlaying');
                });

            });
            function video_play(obj, el) {
                el.classList.remove('isPlaying');
                obj.pause();
            };
            function video_pause(obj, el) {
                el.classList.add('isPlaying');
                obj.play();
            };
        };
    }

    /** =========================================
     Select variable functions
     =============================================**/
    function urus_getChosenAttributes(container){
        var data   = {};
        var count  = 0;
        var chosen = 0;
        container.find('.attribute-select').each(function(){
            var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
            var value          = $( this ).val() || '';
            if ( value.length > 0 ) {
                chosen ++;
            }
            count ++;
            data[ attribute_name ] = value;
        });

        return {
            'count'      : count,
            'chosenCount': chosen,
            'data'       : data
        };

    }
    /**
     * Find matching variations for attributes.
     */
    function urus_findMatchingVariations( variations, attributes ) {
        var matching = [];
        for ( var i = 0; i < variations.length; i++ ) {
            var variation = variations[i];

            if ( urus_isMatch( variation.attributes, attributes ) ) {
                matching.push( variation );
            }
        }
        return matching;
    };
    /**
     * See if attributes match.
     * @return {Boolean}
     */
    function urus_isMatch( variation_attributes, attributes ) {
        var match = true;
        for ( var attr_name in variation_attributes ) {
            if ( variation_attributes.hasOwnProperty( attr_name ) ) {
                var val1 = variation_attributes[ attr_name ];
                var val2 = attributes[ attr_name ];
                if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
                    match = false;
                }
            }
        }
        return match;
    };

    function urus_wp_template( templateId ) {
        var html = document.getElementById( 'tmpl-' + templateId ).textContent;
        var hard = false;
        // any <# #> interpolate (evaluate).
        hard = hard || /<#\s?data\./.test( html );
        // any data that is NOT data.variation.
        hard = hard || /{{{?\s?data\.(?!variation\.).+}}}?/.test( html );
        // any data access deeper than 1 level e.g.
        // data.variation.object.item
        // data.variation.object['item']
        // data.variation.array[0]
        hard = hard || /{{{?\s?data\.variation\.[\w-]*[^\s}]/.test ( html );
        if ( hard ) {
            return wp.template( templateId );
        }
        return function template ( data ) {
            var variation = data.variation || {};
            return html.replace( /({{{?)\s?data\.variation\.([\w-]*)\s?(}}}?)/g, function( _, open, key, close ) {
                // Error in the format, ignore.
                if ( open.length !== close.length ) {
                    return '';
                }
                var replacement = variation[ key ] || '';
                // {{{ }}} => interpolate (unescaped).
                // {{  }}  => interpolate (escaped).
                // https://codex.wordpress.org/Javascript_Reference/wp.template
                if ( open.length === 2 ) {
                    return window.escape( replacement );
                }
                return replacement;
            });
        };
    };
})(jQuery, window, document);
