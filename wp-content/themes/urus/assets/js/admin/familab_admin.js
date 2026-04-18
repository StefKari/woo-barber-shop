;(function ($) {
    "use strict";
   /* FRAMEWORK JS */
    $(document).on('click', '.vc_edit-form-tab .tab-item', function () {
        var _this     = $(this),
            _content  = _this.closest('.vc_edit-form-tab'),
            _parent   = _this.closest('.vc_shortcode-param'),
            _data_tab = _this.data('tabs');

        _content.find('.vc_shortcode-param').not(_parent).css('display', 'none');
        _this.addClass('active').siblings().removeClass('active');
        _content.find('.vc_shortcode-param.' + _data_tab).css('display', 'block');
    });
    $(document).on('click','.tr-tab:not(".disabled"), .tr-tab-ex:not(".disabled")',function (e) {
        e.preventDefault();
        if (!$(this).hasClass('active')){
            var tab_id = $(this).data('tab');
            $('.tr-tab, .tr-tab-ex').removeClass('active');
            $('.tr-tab-content .tab-pane').removeClass('active');
            $('.tr-tab[data-tab="'+tab_id+'"],.tr-tab-ex[data-tab="'+tab_id+'"]').addClass('active');
            $("#"+tab_id).addClass('active');
        }
    });
    $(document).on('click','.tr-tab.disabled, .tr-tab-ex.disabled, .welcome-icon.disabled',function (e) {
        e.preventDefault();
        return false;
    });

    $(document).ajaxComplete(function (event, request, options) {
        if ( request && 4 === request.readyState && 200 === request.status && options.data && 0 <= options.data.indexOf('action=vc_edit_form') ) {
            if ( wp.media ) {
                wp.media.view.Modal.prototype.on('close', function () {
                    setTimeout(function () {
                        $('.supports-drag-drop').css('display', 'none');
                    }, 1000);
                });
            }
        }
    });
    $(document).on('click','#rebuild_search_index_action',function () {
        $('.rebuild_search_index_notification').html('Synchronizing products, please wait...');
        var index_page = 0,last_msg='',start_sku = false,msg = '',sku_pos = 0,stop_sku = false,checking = false,checking_sku = false;
        var si_index = setInterval (function(){
            if (!checking){
                checking = true;
        $.ajax({
            type: 'POST',
            url: urus_ajax_admin.ajaxurl,
            data: {
                security: urus_ajax_admin.security,
                        action: 'rebuild_search_index',
                        page: index_page,
            },
            success: function (response) {
                if (!response) {
                            console.log('rebuild_search_index !response');
                            clearInterval(si_index);
                }
                        checking = false;
                        index_page++;
                        if (response.finish) {
                            console.log('rebuild_search_index finish');
                            clearInterval(si_index);
                            start_sku = true;
                            if (response.total > 0){
                                msg = response.msg;
                            } else {
                                msg = last_msg;
                            }
                            var s_index = setInterval(function () {
                $.ajax({
                    type: 'POST',
                    url: urus_ajax_admin.ajaxurl,
                    data: {
                        security: urus_ajax_admin.security,
                        action: 'rebuild_search_sku_index',
                                        pos: sku_pos
                    },
                    success: function (response) {
                                        sku_pos = 1;
                        if (response.finish){
                                            clearInterval(s_index);
                            $('.rebuild_search_index_notification').html(msg);
                                        }
                                    },
                                    error: function(e, ts, et) {
                                        clearInterval(s_index);
                                        $('.rebuild_search_index_notification').html('error while synchronizing the product');
                                    }
                                });
                            },15000);
                        } else {
                            last_msg = response.msg;
                        }
                    },                    
                });
            }
        },15000);
                            var s_index = setInterval(function(){
            if (start_sku){
                if (!checking_sku){
                    checking_sku = true;
                                $.ajax({
                                    type: 'POST',
                                    url: urus_ajax_admin.ajaxurl,
                                    data: {
                                        security: urus_ajax_admin.security,
                                        action: 'rebuild_search_sku_index',
                            pos: sku_pos
                                    },
                                    success: function (response) {
                            checking_sku = false;
                            sku_pos = 1;
                                        if (response.finish) {
                                            clearInterval(s_index);
                                            $('.rebuild_search_index_notification').html(msg);
                                        }
                        },
                                });
                }
                        }
            if (stop_sku){
                clearInterval(s_index);
                    }
        },15000);




    });
    $(document).on('click','.button-cancel',function () {
        $.magnificPopup.close();
        return false;
    });

    $(document).on('change','input[name="option"]',function () {
        var value = $(this).val();
        if (value == 'page'){
            $('.box-wrap.select-page').show();
        } else{
            $('.box-wrap.select-page').hide();
        }
    });
    $(document).on('click','.select-page .box',function () {
        var waper = $(this);
        var checkBoxes =  waper.find("input[name=recipients\\[\\]]");
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
        $(this).toggleClass('selected');
    });
    $(document).on('click','#tr-plugins-bulk .install_plugins',function (e) {
        e.preventDefault();
        if ($(this).hasClass('disable')){
            return false;
        }
        var form = $('#tr-plugins-bulk');
        form.submit();
    });
    $(document).on('change','#tr-plugin-action',function () {
        //'action'  => 'tgmpa-bulk-install',
        var ob = $(this);
        var pls = [];
        var form = $('#tr-plugins-bulk');
        if (form.find('input[name="plugin"]').length > 0)
            form.find('input[name="plugin"]').remove();
        if (ob.val() == 'active_all'){
            pls = form.data('active').split(',');
            ob.next('.install_plugins').removeClass('disable');
            form.find('input[name="action"]').val('tgmpa-bulk-activate');
        }else if(ob.val() == 'required'){
            pls = form.data('required').split(',');
            ob.next('.install_plugins').removeClass('disable');
            form.find('input[name="action"]').val('tgmpa-bulk-install');
        }else if (ob.val() == 'all'){
            pls = form.data('all').split(',');
            ob.next('.install_plugins').removeClass('disable');
            form.find('input[name="action"]').val('tgmpa-bulk-install');
        }else{
            ob.next('.install_plugins').addClass('disable').attr('href','#');
        }
        if (pls.length > 0){
            $.each(pls,function (i,v) {
                form.append('<input type="hidden" name="plugin[]" value="'+v+'">');
            });
        }
    });
})(jQuery, window, document);
