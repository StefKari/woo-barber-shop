;(function ($) {
    "use strict";
    function variations_custom(event) {
        var form = event.target;
        if (typeof (form) == "undefined" || !$(form).hasClass('variations_form')){
            form = '.variations_form';
        }
        if ($(form).length)
        $(form).each(function () {
            var current_form = $(this);
            current_form.find('.data-val').html('');
            var product = current_form.closest('.product'),
                product_item = current_form.closest('.product-item'),
                swatch_obj = product_item.find('.product-loop-variations_swatch_attribute'),
                swatch_attr = swatch_obj.data('attribute');
            if (swatch_obj.length){
                swatch_obj.html('');
            }
            current_form.find('select.swatches_style,select.swatches_style_extend').each(function () {

                var _this = $(this),
                    attr_name = _this.data('id'),
                    fill_swatch = false;
                if (swatch_obj.length && 'pa_'+swatch_attr == attr_name){
                    fill_swatch = true;
                }
                _this.hide();
                _this.find('option').each(function () {
                    var _ID        = $(this).parent().data('id'),
                        _data      = $(this).data(_ID),
                        _value     = $(this).attr('value'),
                        _name      = $(this).html(),
                        _data_type = $(this).data('type')? $(this).data('type') :'text',
                        _data_highlight = $(this).data('highlight')? $(this).data('highlight') :'',
                        _itemclass = _data_type+(_data_highlight? ' highlight':''),
                        tooltip    = $(this).data('tooltip'),
                        width      = $(this).data('width'),
                        height     = $(this).data('height'),
                        url        = $(this).data('url');
                    if ( $(this).is(':selected') ) {
                        _itemclass += ' active';
                    }
                    if ( _value !== '' ) {
                        if ( _data_type == 'color' || _data_type == 'photo' ) {

                            if (fill_swatch){
                                if( _this.hasClass('swatches_style_extend')){
                                    swatch_obj.append('<a data-name="attribute_'+attr_name+'" aria-label="'+tooltip+'"  data-value="' + _value + '" class="photo-extend familab_swatch_attribute hint--top hint--bounce '+_itemclass+'" href="#" ><span style="display:inline-block; width:'+width+'px; height:'+height+'px:" class="img"><img src="'+url+'" alt=""></span></a>');
                                }else{
                                    swatch_obj.append('<a data-name="attribute_'+attr_name+'" aria-label="'+tooltip+'"  data-value="' + _value + '" class="familab_swatch_attribute hint--top hint--bounce '+_itemclass+'" href="#" style="background: ' + _data + '; background-size: cover; display: inline-block; background-repeat: no-repeat; width:'+width+'px;height:'+height+'px; "><span></span></a>');

                                }
                                _itemclass +=' hidden';

                            }
                            if (_this.hasClass('swatches_style_extend')) {
                                _this.parent().find('.attribute-'+attr_name).append('<a class="change-value hint--top hint--bounce photo-extend ' + _itemclass + '" href="#"  aria-label="'+tooltip+'"  data-value="' + _value + '"><span style="display:inline-block; width:'+width+'px; height:'+height+'px:" class="img"><img src="'+url+'" alt=""></span></a>');
                            }else{
                                _this.parent().find('.attribute-'+attr_name).append('<a class="change-value hint--top hint--bounce ' + _itemclass + '" href="#" style="background: ' + _data + '; background-size: cover; display: inline-block; background-repeat: no-repeat; width:'+width+'px;height:'+height+'px; " aria-label="'+tooltip+'"  data-value="' + _value + '"><span></span></a>');
                            }


                        } else {
                            if (fill_swatch){
                                swatch_obj.append('<a data-name="attribute_'+attr_name+'" aria-label="'+tooltip+'"  data-value="' + _value + '" class="familab_swatch_attribute hint--top hint--bounce '+_itemclass+'" href="#" style="background: ' + _data + '; background-size: cover; display: inline-block; background-repeat: no-repeat; width:'+width+'px;height:'+height+'px; "><span>' + _name + '</span></a>');
                                _itemclass +=' hidden';
                            }
                            _this.parent().find('.attribute-'+attr_name).append('<a class="change-value ' + _itemclass + '" href="#" data-value="' + _value + '"><span>' + _name + '</span></a>');
                        }
                    }
                });
            });
        });
    }

    $(document).on('click', '.reset_variations', function () {
        $('.variations_form').find('.change-value').removeClass('active');
    });
    $(document).on('click','.product-loop-variations_swatch_attribute .familab_swatch_attribute',function (e) {
        var _this   = $(this),
            _change = _this.data('value'),
            _name = _this.data('name'),
            _product_item = _this.closest('.product'),
            _form = _product_item.find('.variations_form'),
            _product_img_wrap = _product_item.find('.woocommerce-product-gallery__image'),
            _img_item = _product_img_wrap.find('.wp-post-image'),
            _o_src = _img_item.attr('data-o_src');
        if (typeof (_o_src) != "undefined" && _o_src.length){
            if (_o_src.indexOf('data:image') >= 0){
                var img_src = _product_img_wrap.attr('data-thumb');
                if (typeof(img_src) == "undefined"){
                    img_src = _img_item.attr('data-o_data-src');
                }
                _img_item.attr('data-o_src',img_src);
            }
        }
        if (!_form.hasClass('has_changed')){
            _form.addClass('has_changed');
        }
        if(_this.hasClass('active')){
            _this.removeClass('active');
            _form.find('select[name="'+_name+'"]').val('').trigger('change');
            return false;
        }
        _form.find('select[name="'+_name+'"]').val(_change).trigger('change');

        _this.addClass('active').siblings().removeClass('active');
        e.preventDefault();
    });
    $(document).on('click', '.variations_form .change-value', function (e) {
        var _this   = $(this),
            _change = _this.data('value'),
            _product_item = _this.closest('.product'),
            _product_img_wrap = _product_item.find('.woocommerce-product-gallery__image'),
            _img_item = _product_img_wrap.find('.wp-post-image'),
            _o_src = _img_item.attr('data-o_src');
        if (typeof (_o_src) != "undefined" && _o_src.length){
            if (_o_src.indexOf('data:image') >= 0){
                var img_src = _product_img_wrap.attr('data-thumb');
                if (typeof(img_src) == "undefined"){
                    img_src = _img_item.attr('data-o_data-src');
                }
                _img_item.attr('data-o_src',img_src);
            }
        }
        var _form =  _this.closest('.variations_form');

        if (!_form.hasClass('has_changed')){
            _form.addClass('has_changed');
        }
        if(_this.hasClass('active')){
            _this.closest('.data-val').prev('select').val('').trigger('change');
            _this.removeClass('active');
            if($('.add_to_cart_extend').length){
                $('.add_to_cart_extend').find('a.change-value').removeClass('active');
            }
            return false;
        }
        _this.closest('.data-val').prev('select').val(_change).trigger('change');
        _this.addClass('active').siblings().removeClass('active');
        if($('.add_to_cart_extend').length){
            $('.add_to_cart_extend').find('a.change-value').removeClass('active');
            $('.add_to_cart_extend').find('a.change-value[data-value="'+_change+'"]').addClass('active');    
        }
        e.preventDefault();
    });
    $(document).on('woocommerce_variation_has_changed wc_variation_form', function (e) {
        variations_custom(e);
    });

    $(document).on('qv_loader_stop', function (e) {
        variations_custom(e);
    });
    $(document).on('core_page_loaded', function (e) {
        $( '.variations_form' ).each( function() {
            $( this ).wc_variation_form();
        });
      variations_custom(e);
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        variations_custom(event);
    });
})(jQuery);
