;(function ($) {
    "use strict";
    $(document).ready(function ($) {
        $.fn.urus_vc_datetime  = function () {
            var _this = $(this);
            _this.on('urus_date_time', function () {
                _this.each(function () {
                    var _date  = $(this).find('.vc-field-date').val(),
                        _time  = $(this).find('.vc-field-time').val(),
                        _value = $(this).find('.wpb_vc_param_value');

                    _value.val(_date + ' ' + _time);
                });
            }).trigger('urus_date_time');
            $(document).on('change', function () {
                _this.trigger('urus_date_time');
            });
        };
        $.fn.urus_vc_datepicker = function () {
            var _this = $(this);
            _this.on('urus_datepicker', function () {
                _this.each(function () {
                    var $this   = $(this),
                        $input  = $this.find('input'),
                        options = JSON.parse($this.find('.cs-datepicker-options').val()),
                        wrapper = '<div class="cs-datepicker-wrapper"></div>',
                        $datepicker;

                    var defaults = {
                        beforeShow: function (input, inst) {
                            $datepicker = $('#ui-datepicker-div');
                            $datepicker.wrap(wrapper);
                        },
                        onClose: function () {
                            var cancelInterval = setInterval(function () {
                                if ( $datepicker.is(':hidden') ) {
                                    $datepicker.unwrap(wrapper);
                                    clearInterval(cancelInterval);
                                }
                            }, 100);
                        }
                    };

                    options = $.extend({}, options, defaults);

                    $input.datepicker(options);
                });
            }).trigger('urus_datepicker');
            $(document).on('change', function () {
                _this.trigger('urus_datepicker');
            });
        };
        $('.urus-vc-field-date').urus_vc_datepicker();
        $('.vc-date-time-picker').urus_vc_datetime();
    });
})(jQuery, window, document);