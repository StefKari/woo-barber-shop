(function ($) {
    "use strict"; // Start of use strict

    function getAllValues(element) {
        var allValues = {};
        $(element).find('input,select,textarea').each(function () {
            var type = $(this).prop("type");
            // checked radios/checkboxes
            if ( (type == "checkbox" || type == "radio") ) {
                if ( this.checked ) {
                    allValues[ $(this).attr("name") ] = $(this).val();
                } else {
                    allValues[ $(this).attr("name") ] = 0;
                }
            } else {
                allValues[ $(this).attr("name") ] = $(this).val();
            }
        });
        return allValues;
    }
    function familab_settings_image() {
        if ( $('.set_custom_images').length > 0 ) {
            if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
                $(document).on('click', '.set_custom_images', function (e) {

                    e.preventDefault();
                    var button                      = $(this),
                        id                          = $(this).closest('.submenu-item-bg').find('.process_custom_images'),
                        t                           = $(this);
                    wp.media.editor.send.attachment = function (props, attachment) {
                        id.val(attachment.id);
                        t.closest('.submenu-item-bg').find('.image-preview').html('<img src="' + attachment.url + '" alt=""> <a class="remove-menu-bg" href="#"><span class="fip-fa dashicons dashicons-no-alt"></span></a>');
                    };
                    wp.media.editor.open(button);
                    return false;
                });
            }
        }
        if ( $('.set_icon_image').length > 0 ) {
            if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
                $(document).on('click', '.set_icon_image', function (e) {

                    e.preventDefault();
                    var button = $(this),
                        id     = $(this).closest('.icon-image-settings').find('.icon_image'),
                        t      = $(this);

                    wp.media.editor.send.attachment = function (props, attachment) {
                        id.val(attachment.id);
                        t.closest('.icon-image-settings').find('.image-preview').html('<img src="' + attachment.url + '" alt=""> <a class="remove_icon_image" href="#"><span class="fip-fa dashicons dashicons-no-alt"></span></a>');
                    };
                    wp.media.editor.open(button);
                    return false;
                });
            }
        }
        if ( $('.set_label_image').length > 0 ) {
            if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
                $(document).on('click', '.set_label_image', function (e) {

                    e.preventDefault();
                    var button = $(this),
                        id     = $(this).closest('.label-image-settings').find('.label_image'),
                        t      = $(this);

                    wp.media.editor.send.attachment = function (props, attachment) {
                        id.val(attachment.id);
                        t.closest('.label-image-settings').find('.image-preview').html('<img src="' + attachment.url + '" alt=""> <a class="remove_label_image" href="#"><span class="fip-fa dashicons dashicons-no-alt"></span></a>');
                    };
                    wp.media.editor.open(button);
                    return false;
                });
            }
        }
    }
    $(document).ready(function () {
        // Enable Menu
        $(document).on('click', '#familab_menu_meta_box .familabcore-menu-save', function () {
            var megamenu_enabled        = $('#familab_menu_meta_box .megamenu_enabled:checked').val(),
                megamenu_mobile_enabled = $('#familab_menu_meta_box .megamenu_mobile_enabled:checked').val(),
                megamenu_layout         = $('#familab_menu_meta_box .megamenu_layout').val(),
                menu_id                 = $('#familab_menu_meta_box .menu_id').val();

            if ( typeof megamenu_enabled === "undefined" ) {
                megamenu_enabled = 0;
            }
            $('#familab_menu_meta_box .spinner').css('visibility', 'visible');

            var data = {
                action: 'familab_menu_save_settings',
                security: familab_nav_menu.security,
                megamenu_enabled: megamenu_enabled,
                menu_id: menu_id,
                megamenu_mobile_enabled: megamenu_mobile_enabled,
                megamenu_layout: megamenu_layout
            };
            $.post(familab_nav_menu.ajaxurl, data, function (response) {
                $('#familab_menu_meta_box .spinner').css('visibility', 'hidden');
                if ( megamenu_enabled == 1 ) {
                    $('.familabcore-menu-settings').removeClass('hidden');
                } else {
                    $('.familabcore-menu-settings').addClass('hidden');
                }
            });

        });
        $(document).on('click', '.familabcore-menu-settings', function () {
            var $this       = $(this),
                url         = $this.attr('url'),
                item_id     = $this.data('item_id'),
                popup       = $('.content-popup-megamenu'),
                curent_item = $this.closest('.menu-item');

            if ( !curent_item.hasClass('menu-item-depth-0') ) {
                curent_item.find('.familabcore-menu-setting-for-depth-0').css('opacity', 0);
            } else {
                curent_item.find('.familabcore-menu-setting-for-depth-0').css('opacity', 1);
            }
            $this.addClass('loading');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'familab_get_form_settings',
                    item_id: item_id,
                    iframe: url,
                },
                success: function (response) {
                    if ( response.success == 'yes' ) {
                        popup.append(response.html);
                        $.magnificPopup.open({
                            items: {
                                src: response.html
                            },
                            type: 'inline'
                        });
                    }
                },
                complete: function () {
                    $this.removeClass('loading');
                    familab_settings_image();
                }
            });
            return false;
        });
        $(document).on('click', '.tabs-settings a', function () {
            var container = $(this).closest('.familabcore-menu-item-settings-popup-content'),
                id        = $(this).attr('href');
            $(this).closest('.tabs-settings').find('li').removeClass('active');
            $(this).closest('li').addClass('active');
            container.find('.tab-container .familabcore-menu-tab-content').removeClass('active');
            container.find(id).addClass('active');
            return false;
        });
        $(document).on('click', '.fip-icons-container .icon', function () {
            var value = $(this).data('value');
            $(this).closest('.fip-icons-container').find('.icon').removeClass('selected');
            $(this).addClass('selected');

            $(this).closest('.edit_form_line.icon-settings').find('.selected-icon').html('<i class="' + value + '"></i>');
            $(this).closest('.edit_form_line.icon-settings').find('input.familab_menu_settings_menu_icon').val(value);
        });
        $(document).on('click', '.selector-button.remove', function () {
            $(this).closest('.edit_form_line.icon-settings').find('.icon').removeClass('selected');

            $(this).closest('.edit_form_line.icon-settings').find('.selected-icon').html('');
            $(this).closest('.edit_form_line.icon-settings').find('input.familab_menu_settings_menu_icon').val('');
        });
        $(document).on('keyup', '.icons-search-input', function () {

            var v = $(this).val();

            if ( v !== '' ) {
                v = v.toLocaleLowerCase();
                $('.fip-icons-container .icon').addClass('hide');
                $('.fip-icons-container .icon[data-value*="' + v + '"]').removeClass('hide');
            } else {
                $('.fip-icons-container .icon').removeClass('hide');
            }
        });
        $(document).on('click', '.remove-menu-bg', function () {
            $(this).closest('.submenu-item-bg').find('.process_custom_images').val(0);
            $(this).closest('.image-preview').html('');
        });
        $(document).on('click', '.familabcore-menu-save-settings', function () {
            $(this).html('Saving..');
            var t             = $(this),
                item_id       = $(this).data('item_id'),
                container     = $('#familabcore-menu-item-settings-popup-content-' + item_id),
                menu_settings = getAllValues(container),
                data          = {
                    action: 'familab_menu_save_all_settings',
                    menu_settings: menu_settings,
                    item_id: item_id
                },
                publishPost   = container.find('iframe').contents().find('input#publish'),
                formPost      = container.find('iframe').contents().find('form#post'),
                item_current  = $('#menu-item-' + item_id);

            publishPost.trigger('click');
            $.post(familab_nav_menu.ajaxurl, data, function (response) {
                if ( response.status == true ) {
                    if ( response.url != "" ) {
                        var _content = document.createElement('IFRAME');
                        _content.setAttribute('src', response.url);
                        container.find('#familabcore-menu-item-settings-' + item_id).attr('url', response.url);
                        container.find('.familabcore-menu-tab-builder').html(_content);
                    }

                    var settings = response.settings;
                    if ( settings.menu_icon_type == 'font-icon' ) {
                        if ( settings.menu_icon != "" ) {
                            item_current.find('.menu-icon').html('<span class="' + settings.menu_icon + '"></span>');
                        } else {
                            item_current.find('.menu-icon').html('');
                        }
                    }
                    if ( settings.menu_icon_type == 'image' ) {
                        if ( settings.icon_image_url != "" ) {
                            item_current.find('.menu-icon').html('<img src="' + settings.icon_image_url + '">');
                        } else {
                            item_current.find('.menu-icon').html('');
                        }
                    }
                }
                t.html('Save All');
            });
            return false;
        });
        $(document).on('change', 'input.enable_mega', function () {
            if ( this.checked ) {

                var item_id      = $(this).data('item_id'),
                    data         = {
                        action: 'familab_menu_create_mega_menu',
                        item_id: item_id
                    },
                    container    = $('#familabcore-menu-item-settings-popup-content-' + item_id),
                    item_current = $('#menu-item-' + item_id);

                $.post(familab_nav_menu.ajaxurl, data, function (response) {
                    if ( response.status == true ) {
                        if ( response.url != "" ) {
                            var _content = document.createElement('IFRAME');
                            _content.setAttribute('src', response.url);
                            container.find('.familabcore-menu-tab-builder').html(_content);
                            item_current.find('.familabcore-menu-settings').attr('url', response.url);
                        }
                    }
                });
            }
        });
        $(document).on('change', 'input.megamenu_enabled', function () {
            if ( this.checked ) {

            }
        });
        $(document).on('change', 'select.menu_icon_type', function () {
            var container = $(this).closest('.familabcore-menu-tab-icons'),
                val       = $(this).val();

            if ( val == 'font-icon' ) {
                container.find('.edit_form_line.icon-settings').show();
                container.find('.edit_form_line.icon-image-settings').hide();
            }
            if ( val == 'image' ) {
                container.find('.edit_form_line.icon-settings').hide();
                container.find('.edit_form_line.icon-image-settings').show();
            }
        });

        $(document).on('click', '.remove_icon_image', function () {
            $(this).closest('.icon-image-settings').find('.icon_image').val(0);
            $(this).closest('.image-preview').html('');
        });

        $(document).on('click', '.remove_label_image', function () {
            $(this).closest('.label-image-settings').find('.label_image').val(0);
            $(this).closest('.image-preview').html('');
        });

    });
    $(document).on('click', '.click-un', function () {
        var artists        = $(this).val(),
            artistTemplate = _.template(
                $('#item-list-tpl')[ 0 ].text
            );
        console.log($('#item-list-tpl')[ 0 ].text);
        $(this).next().html(content);
    });
})(jQuery); // End of use strict