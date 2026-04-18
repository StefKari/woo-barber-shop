(function ($) {
    "use strict";
    $(document).ready(function($){
        function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var _extends=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var i=arguments[t];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(e[n]=i[n])}return e},_createClass=function(){function e(e,t){for(var i=0;i<t.length;i++){var n=t[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,i,n){return i&&e(t.prototype,i),n&&e(t,n),t}}(),Slinky=function(){function e(t){var i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};_classCallCheck(this,e),this.settings=_extends({},this.options,i),this._init(t)}return _createClass(e,[{key:"options",get:function(){return{resize:!0,speed:300,theme:"slinky-theme-default",title:!1}}}]),_createClass(e,[{key:"_init",value:function(e){this.menu=$(e),this.base=this.menu.children().first();this.base;var t=this.menu,i=this.settings;t.addClass("slinky-menu").addClass(i.theme),this._transition(i.speed),$("a + ul",t).prev().addClass("next"),$("li > a",t).wrapInner("<span>");var n=$("<li>").addClass("header");$("li > ul",t).prepend(n);var s=$("<a>").prop("href","#").addClass("back");$(".header",t).prepend(s),i.title&&$("li > ul",t).each(function(e,t){var i=$(t).parent().find("a").first().text();if(i){var n=$("<header>").addClass("title").text(i);$("> .header",t).append(n)}}),this._addListeners(),this._jumpToInitial()}},{key:"_addListeners",value:function(){var e=this,t=this.menu,i=this.settings;$("a",t).on("click",function(n){if(e._clicked+i.speed>Date.now())return!1;e._clicked=Date.now();var s=$(n.currentTarget);(0===s.attr("href").indexOf("#")||s.hasClass("next")||s.hasClass("back"))&&n.preventDefault(),s.hasClass("next")?(t.find(".active").removeClass("active"),s.next().show().addClass("active"),e._move(1),i.resize&&e._resize(s.next())):s.hasClass("back")&&(e._move(-1,function(){t.find(".active").removeClass("active"),s.parent().parent().hide().parentsUntil(t,"ul").first().addClass("active")}),i.resize&&e._resize(s.parent().parent().parentsUntil(t,"ul")))})}},{key:"_jumpToInitial",value:function(){var e=this.menu.find(".active");e.length>0&&(e.removeClass("active"),this.jump(e,!1))}},{key:"_move",value:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:function(){};if(0!==e){var i=this.settings,n=this.base,s=Math.round(parseInt(n.get(0).style.left))||0;n.css("left",s-100*e+"%"),"function"==typeof t&&setTimeout(t,i.speed)}}},{key:"_resize",value:function(e){this.menu.height(e.outerHeight())}},{key:"_transition",value:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:300,t=this.menu,i=this.base;t.css("transition-duration",e+"ms"),i.css("transition-duration",e+"ms")}},{key:"jump",value:function(e){var t=!(arguments.length>1&&void 0!==arguments[1])||arguments[1];if(e){var i=this.menu,n=this.settings,s=$(e),a=i.find(".active"),r=0;a.length>0&&(r=a.parentsUntil(i,"ul").length),i.find("ul").removeClass("active").hide();var l=s.parentsUntil(i,"ul");l.show(),s.show().addClass("active"),t||this._transition(0),this._move(l.length-r),n.resize&&this._resize(s),t||this._transition(n.speed)}}},{key:"home",value:function(){var e=!(arguments.length>0&&void 0!==arguments[0])||arguments[0],t=this.base,i=this.menu,n=this.settings;e||this._transition(0);var s=i.find(".active"),a=s.parentsUntil(i,"ul");this._move(-a.length,function(){s.removeClass("active").hide(),a.not(t).hide()}),n.resize&&this._resize(t),!1===e&&this._transition(n.speed)}},{key:"destroy",value:function(){var e=this,t=this.base,i=this.menu;$(".header",i).remove(),$("a",i).removeClass("next").off("click"),i.css({height:"","transition-duration":""}),t.css({left:"","transition-duration":""}),$("li > a > span",i).contents().unwrap(),i.find(".active").removeClass("active"),i.attr("class").split(" ").forEach(function(e){0===e.indexOf("slinky")&&i.removeClass(e)}),["settings","menu","base"].forEach(function(t){return delete e[t]})}}]),e}();jQuery.fn.slinky=function(e){return new Slinky(this,e)};
        window.familab = window.familab || {};
        familab.Drawers = (function () {
            var Drawer = function (id, position, options) {
                var defaults = {
                    close: '.js-drawer-close',
                    open: '.js-drawer-open-' + position,
                    openClass: 'js-drawer-open',
                    dirOpenClass: 'js-drawer-open-' + position
                };
                this.$nodes = {
                    parent: $('body, html'),
                    page: $('.site-content'),
                    moved: $('.site-content')
                };
                this.config = $.extend(defaults, options);
                this.position = position;
                this.$drawer = $('#' + id);
                if (!this.$drawer.length) {
                    return false;
                }
                this.drawerIsOpen = false;
                this.init();
            };
            Drawer.prototype.init = function () {
                var $openBtn = $(this.config.open);
                $openBtn.attr('aria-expanded', 'false');
                $openBtn.on('click', $.proxy(this.open, this));

                this.$drawer.find(this.config.close).on('click', $.proxy(this.close, this));
            };
            Drawer.prototype.open = function (evt) {
                var externalCall = false;
                if (this.drawerIsOpen) {
                    return;
                }
                if (evt) {
                    evt.preventDefault();
                } else {
                    externalCall = true;
                }
                if (evt && evt.stopPropagation) {
                    evt.stopPropagation();
                    // save the source of the click, we'll focus to this on close
                    this.$activeSource = $(evt.currentTarget);
                }
                if (this.drawerIsOpen && !externalCall) {
                    return this.close();
                }
                this.$nodes.parent.addClass(this.config.openClass + ' ' + this.config.dirOpenClass);
                this.drawerIsOpen = true;
                if (this.config.onDrawerOpen && typeof(this.config.onDrawerOpen) == 'function') {
                    if (!externalCall) {
                        this.config.onDrawerOpen();
                    }
                }
                if (this.$activeSource && this.$activeSource.attr('aria-expanded')) {
                    this.$activeSource.attr('aria-expanded', 'true');
                }
                this.bindEvents();
            };
            Drawer.prototype.close = function () {
                if (!this.drawerIsOpen) {
                    return;
                }
                $(document.activeElement).trigger('blur');
                this.$nodes.parent.removeClass(this.config.dirOpenClass + ' ' + this.config.openClass);
                this.drawerIsOpen = false;
                Drawer.prototype.removeTrapFocus({
                    $container: this.$drawer,
                    $elementToFocus: this.$drawer.find('.drawer__close-button'),
                    namespace: 'drawer_focus'
                });
                if (this.$activeSource && this.$activeSource.attr('aria-expanded')) {
                    this.$activeSource.attr('aria-expanded', 'false');
                }
                this.unbindEvents();
            };
            Drawer.prototype.trapFocus = function (options) {
                var eventName = options.namespace
                    ? 'focusin.' + options.namespace
                    : 'focusin';
                if (!options.$elementToFocus) {
                    options.$elementToFocus = options.$container;
                    options.$container.attr('tabindex', '-1');
                }
                options.$elementToFocus.focus();
                $(document).on(eventName, function (evt) {
                    if (options.$container[0] !== evt.target && !options.$container.has(evt.target).length) {
                        options.$container.focus();
                    }
                });
            };
            Drawer.prototype.removeTrapFocus = function (options) {
                var eventName = options.namespace
                    ? 'focusin.' + options.namespace
                    : 'focusin';
                if (options.$container && options.$container.length) {
                    options.$container.removeAttr('tabindex');
                }
                $(document).off(eventName);
            };
            Drawer.prototype.bindEvents = function() {
                this.$nodes.page.on('touchmove.drawer', function () {
                    return false;
                });
                this.$nodes.page.on('click.drawer', $.proxy(function () {
                    this.close();
                    return false;
                }, this));
                this.$nodes.parent.on('keyup.drawer', $.proxy(function(evt) {
                    if (evt.keyCode === 27) {
                        this.close();
                    }
                }, this));
            };
            Drawer.prototype.unbindEvents = function() {
                this.$nodes.page.off('.drawer');
                this.$nodes.parent.off('.drawer');
            };
            return Drawer;
        })();
        function init_mobile_menu(){
            var mobile_menu = $('.js-slinky-menu');
            mobile_menu.slinky({
                resize: true,
                title: true,
                speed: 300
            });
            $('.js-drawer-open-left').attr('aria-controls', 'Familab_MobileMenu').attr('aria-expanded', 'false');
            familab.LeftDrawer = new familab.Drawers('Familab_MobileMenu', 'left', {
            });
        };
        function searchDrawer() {
            $('.js-drawer-open-top').attr('aria-controls', 'Familab_SearchDrawer').attr('aria-expanded', 'false');
            familab.SearchDrawer = new familab.Drawers('Familab_SearchDrawer', 'top', {

            });
        };
        function cartDrawer() {
            $('.js-drawer-open-cart').attr('aria-controls', 'Familab_CartDrawer').attr('aria-expanded', 'false');
            familab.CartDrawer = new familab.Drawers('Familab_CartDrawer', 'cart', {});
            $(document).on('click', '.close-mini-cart.js-drawer-close', function(){
                familab.CartDrawer.close();

            })
        };
        function familab_login(){
            $(document).on('click', '#customerLogin .familab-button-login', function(){
                var username = $('#Customer_username').val(),
                    password = $('#Customer_password').val();
                $.ajax({
                    type: 'POST',
                    url: urus_ajax_frontend.ajaxurl,
                    dataType: 'json',
                    data: {
                        security: urus_ajax_frontend.security,
                        action:'urus_ajax_login',
                        username: username,
                        password: password
                    },
                    beforeSend: function() { // before jQuery send the request we will push it to our array
                        $('#customerLogin .familab-loader').fadeIn(200);
                    },
                    success: function (data) {
                        var status = data.loggedin;
                        if (status) {
                            $('#customerLogin .panel_content').html(data.message);
                        }else{
                            $('#customerLogin .drawer-login-fail').html(data.message)
                        }
                        $('#customerLogin .familab-loader').fadeOut(200);
                    }
                })
            })
        };
        init_mobile_menu();
        searchDrawer();
        familab_login();
        cartDrawer();
        generate_options_html();
        check_ajax_button_state();
        ajax_add_to_cart_variable_mobile();
        resize_window_listener();
        function resize_window_listener(){
            var drawer_class = 'drawer';
            var active_drawers = document.getElementsByClassName(drawer_class);
            for (var i=0; i<active_drawers.length; i++){
                active_drawers[i].addEventListener("webkitTransitionEnd", function(){
                    if (! $('body').hasClass('js-drawer-open') ) {
                        window.dispatchEvent(new Event('resize'));
                    }
                });
                active_drawers[i].addEventListener("transitionend", function(){
                    if (! $('body').hasClass('js-drawer-open') ) {
                        window.dispatchEvent(new Event('resize'));
                    }
                });
            }
        };
        function generate_options_html(){
            var extended_btn = $('.add_to_cart_extend'),
                variations_form = $('.variations_form '),
                ajax_atc_btn = $('.urus-single-add-to-cart-btn.product-type-variable');
            var variant_html = '';
            variations_form.find('select').each(function () {
                var _this = $(this),
                    _this_label =  $(this).data('id'),
                    label_name = "";
                label_name = _this.closest('tr').find(".label").text();
                label_name = typeof label_name !== "undefined" ? label_name : "";
                variant_html += '<div class="data-val" parent-name="'+_this.attr('name')+'">';
                variant_html += '<div class="variant-name">' + label_name + '</div>';
                variant_html += '<div class="variant-values">';
                _this.find('option').each(function () {
                    var _ID        = $(this).parent().data('id'),
                        _data      = $(this).data(_ID),
                        _value     = $(this).attr('value'),
                        _name      = $(this).html(),
                        _data_type = $(this).data('type')? $(this).data('type') :'text',
                        _itemclass = _data_type,
                        tooltip    = $(this).data('tooltip'),
                        width      = $(this).data('width'),
                        height     = $(this).data('height');

                    if ( $(this).is(':selected') ) {
                        _itemclass += ' active';
                    }
                    if ( $(this).attr('data-highlight') == 1 ) {
                        _itemclass += ' highlight';
                    }
                    if ( _value !== '' ) {
                        if ( _data_type == 'color' || _data_type == 'photo' ) {
                            variant_html += '<a class="change-value ' + _itemclass + '" href="#" style="background: ' + _data + '; background-size: cover; display: inline-block; background-repeat: no-repeat; width:'+width+'px;height:'+height+'px; " aria-label="'+tooltip+'"  data-value="' + _value + '"><span></span></a>';
                        } else {
                            variant_html += '<a class="change-value ' + _itemclass + '" href="#" data-value="' + _value + '"><span>' + _name + '</span></a>';
                        }
                    }
                });
                variant_html += '</div>';
                variant_html += '</div>';
            });
            variant_html += '<a href="javascript:void(0);" class="close_ajax_options"><span class="urus-icon urus-icon-close"></span></a>';
            extended_btn.html(variant_html);
        };
        function check_ajax_button_state(){
            var extended_btn = $('.add_to_cart_extend'),
                variations_form = $('.variations_form '),
                ajax_atc_btn = $('.urus-single-add-to-cart-btn.product-type-variable');
            var all_options_active = true;
            $('.add_to_cart_extend').find('.data-val').each(function(i,e){
                if ($(this).find('.change-value.active').length > 0) {
                    if(variations_form.find('.single_add_to_cart_button').hasClass('wc-variation-is-unavailable') ){
                        all_options_active = false;
                        return false;
                    }
                    all_options_active = true;
                }else{
                    all_options_active = false;
                    return false;
                }
            });
            if ( extended_btn.is(':visible') ) {//button extended
                if ( all_options_active ) {
                    ajax_atc_btn.addClass('add_to_cart_button');
                    ajax_atc_btn.removeClass('disabled');
                    ajax_atc_btn.html(urus_ajax_frontend.add_to_cart);//Available
                }else{
                    ajax_atc_btn.html(urus_ajax_frontend.unavailable);//Unavailable
                    ajax_atc_btn.removeClass('add_to_cart_button');
                    ajax_atc_btn.addClass('disabled');
                }
            }else{
                ajax_atc_btn.html(urus_ajax_frontend.select_options);//Select option first
            }
            $('body').trigger( 'wc_additional_variation_images_frontend_before_show_variation' );
        };
        function ajax_add_to_cart_variable_mobile(){
            var extended_btn = $('.add_to_cart_extend'),
                variations_form = $('.variations_form '),
                ajax_atc_btn = $('.urus-single-add-to-cart-btn.product-type-variable');
            $(document).on('click', '.urus-single-add-to-cart-btn.product-type-variable' , function(e){
                e.preventDefault();
                var _this = $(this);
                if (extended_btn.is(':visible')){
                    if( ! _this.hasClass('disabled') ){
                        var _form = variations_form,
                            _data = _form.serializeObject();
                        if (_this.val()) {
                            _data.product_id = _this.val();
                        }
                        $(document.body).trigger('adding_to_cart', [_this, _data]);
                        _this.addClass('loading');
                        // Ajax action.
                        $.ajax({
                            type: 'POST',
                            url: urus_ajax_frontend.ajaxurl,
                            data: {
                                security: urus_ajax_frontend.security,
                                data: _data,
                                action:'urus_add_cart_single_ajax'
                            },
                            success: function (response) {
                                if (!response) {
                                    return;
                                }
                                if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
                                    window.location = wc_add_to_cart_params.cart_url;
                                    return;
                                }
                                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, _this]);
                                _this.removeClass('loading');
                            },
                        });
                    }else{
                        extended_btn.fadeOut(function(){
                        	extended_btn.removeClass('is-extended');
                            check_ajax_button_state();
                        });
                    }
                }else{
                    extended_btn.fadeIn(function(){
                    	extended_btn.addClass('is-extended');
                        check_ajax_button_state();
                    });
                }
            });
            $(document).on('click', '.add_to_cart_extend .change-value', function (e) {
                e.preventDefault();
                $("html, body").animate({ scrollTop: $(".main-content").offset().top }, "slow");
                var _this   = $(this),
                    _change = _this.data('value'),
                    parent_name = _this.parents('.data-val').attr('parent-name'),
                    extended_btn = $('.add_to_cart_extend'),
                    variations_form = $('.variations_form '),
                    ajax_atc_btn = $('.urus-single-add-to-cart-btn.product-type-variable');

                if(_this.hasClass('active')){
                    _this.removeClass('active');
                    variations_form.find('select[name='+parent_name+']').val('').trigger('change');
                    ajax_atc_btn.removeClass('add_to_cart_button');
                    ajax_atc_btn.addClass('disabled');
                    ajax_atc_btn.html(urus_ajax_frontend.unavailable);
                    return false;
                }
                variations_form.find('select[name='+parent_name+']').val(_change).trigger('change');
                _this.addClass('active').siblings().removeClass('active');
                check_ajax_button_state();

            });
	        $(document).on('click', 'body.single-product.is-mobile', function (event) {
				var testform = $(event.target).closest('.add_to_cart_extend').length;
				var testbtn =  $(event.target).is('.urus-single-add-to-cart-btn.product-type-variable');
				if (testbtn == false && $('.add_to_cart_extend').hasClass('is-extended') ) {
	            	if ($(event.target).is('.close_ajax_options') || $(event.target).closest('.close_ajax_options').length || testform == 0 ) {
	            		event.preventDefault();
	                 	$('.add_to_cart_extend').fadeOut(function(){
	                 		$('.add_to_cart_extend').removeClass('is-extended');
		                    check_ajax_button_state();
		                });
	            	}
	            }
	        });
        };
        $(document).on('click', 'a.disabled', function(e){
            e.preventDefault();
        });
    });


    $(".aws-search-btn").click(function () {
        alert(test);
        // $(".aws-wrapper").fadeToggle(800);
        // $( ".aws-search-form" ).toggleClass( "flex-row-custom" );
    });
})
(jQuery);
