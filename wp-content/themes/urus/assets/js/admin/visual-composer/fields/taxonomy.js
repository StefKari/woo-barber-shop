;(function ($) {
    "use strict";
    $(document).ready(function ($) {
        $('.urus_vc_taxonomy').chosen();
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        $('.urus_vc_taxonomy').chosen();
    });
})(jQuery, window, document);