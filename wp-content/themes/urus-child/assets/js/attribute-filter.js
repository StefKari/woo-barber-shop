(function ($) {
    'use strict';

    function moveMobileFilter() {
        var $filter       = $('.mobile_shop_heading .barbers-attr-filter');
        var $toolbar = $('.toolbar-products-mobile').parent();

        if ( ! $filter.length || ! $toolbar.length ) {
            return;
        }

        // Premesti filter neposredno iznad toolbar kontejnera
        $toolbar.before( $filter );
    }

    $(document).ready(function () {
        moveMobileFilter();
    });

    $(document).on('click', '.barbers-attr-filter__link', function () {
        $(this).closest('.barbers-attr-filter').css('opacity', '0.5');
    });

})(jQuery);