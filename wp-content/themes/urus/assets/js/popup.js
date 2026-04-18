(function ($) {
    "use strict"; // Start of use strict

    /* ---------------------------------------------
     Init popup
     --------------------------------------------- */
    function urus_init_popup() {
        if ( urus_popup_frontend.enable_popup_mobile != 0) {
            if ( $(window).innerWidth() < 768 ) {

                return false;
            }
        }
        var disabled_popup_by_user = getCookie('urus_disabled_popup_by_user');
        if ( disabled_popup_by_user == 'true' ) {
            return false;
        } else {
            if ( $('body').hasClass('urus-popup-on') && urus_popup_frontend.enable_popup == 1 ) {
                setTimeout(function () {
                    var data = {
                        action: 'urus_popup_load_content',
                        current_page_id:urus_popup_frontend.current_page_id
                    };

                    $.post(urus_popup_frontend.ajaxurl, data, function (response) {
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup mfp-with-anim">'+response.content+'</div>', // can be a HTML string, jQuery object, or CSS selector
                                type: 'inline',

                            },
                            removalDelay: 500, //delay removal by X to allow out-animation
                            callbacks: {
                                open: function() {
                                  // Will fire when this exact popup is open
                                   $( 'div.wpcf7 > form' ).each( function() {
                                        var $form = $( this );
                                        wpcf7.initForm( $form );
                                        var form_action = $form.attr('action');
                                        var action_url = '#' + form_action.split('#')[1];
                                        if ( wpcf7.cached ) {
                                            wpcf7.refill( $form );
                                        }
                                        $form.attr('action', action_url);
                                    } );
                                   //ADD CONTACT FORM 7 SUPPORT
                                },
                                beforeOpen: function() {
                                    this.st.mainClass = response.display_effect;
                                    
                                }
                            },
                            midClick: true
                        });

                    });
                }, urus_popup_frontend.delay_time);

            }
        }
    }

    $(document).on('change', '.urus_disabled_popup_by_user', function () {
        if ( $(this).is(":checked") ) {
            setCookie("urus_disabled_popup_by_user", 'true', 7);
            if($('.mfp-close')){
                $('.mfp-close').trigger('click');
            }
        } else {
            setCookie("urus_disabled_popup_by_user", '', 0);
        }
    });

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires     = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca   = document.cookie.split(';');
        for ( var i = 0; i < ca.length; i++ ) {
            var c = ca[ i ];
            while ( c.charAt(0) == ' ' ) {
                c = c.substring(1);
            }
            if ( c.indexOf(name) == 0 ) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    /* ---------------------------------------------
     Scripts ready
     --------------------------------------------- */
    $(document).ready(function () {
        urus_init_popup();
    });
})
(jQuery); // End of use strict