(function ($) {
    $(document).on('change','.form-setting select[name=home_pages]',function(){
        $img_thumb = $(this).find('option:selected').data('thumb');
        $("#set_homepage_preview").animate({
            opacity: 0,
        },300,function () {
            $('#set_homepage_preview').attr('src',$img_thumb);
        });
        setTimeout(function () {
            $('#set_homepage_preview').animate({
                opacity: 1,
            },200);
        },1000);
    });
    $(document).on('click','#set_home_page_demo',function (e) {
        var home_select = $(this).prev(),
            selected_key = home_select.val();
        if (typeof selected_key == 'undefined' || selected_key == '')
            return;


        console.log($img_thumb);
        /*$.iGrowl({
            message: "Hello world!",
            small: true,
            delay: 3000,
            placement: {
                x: 'right',
                y: 'bottom'
            },
            type: 'success',
            animShow: 'fadeInRight',
            animHide: 'fadeOutRight',
        });*/
        //console.log(familab_intro_ajax_admin);
        $.ajax({
            method: "POST",
            url: familab_intro_ajax_admin.ajaxurl,
            data: {
                action: 'familab_set_home_page',
                security: familab_intro_ajax_admin.security,
                selected_page: selected_key
            }
        }).success(function( result ) {
            if (result.success){
                $.iGrowl({
                    message: familab_intro_ajax_admin.done_msg,
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
            }else{
                $.iGrowl({
                    message: familab_intro_ajax_admin.err_msg,
                    small: true,
                    delay: 3000,
                    placement: {
                        x: 'right',
                        y: 'bottom'
                    },
                    type: 'error',
                    animShow: 'fadeInRight',
                    animHide: 'fadeOutRight',
                });
            }
        });
    })
})(jQuery);
