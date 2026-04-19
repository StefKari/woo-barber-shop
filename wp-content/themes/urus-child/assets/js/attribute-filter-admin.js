/**
 * Barbers – Admin: Media uploader za slike atributa
 */
(function ($) {
    'use strict';

    var frame;

    $(document).on('click', '.barbers-upload-image-btn', function (e) {
        e.preventDefault();

        var $btn     = $(this);
        var $wrap    = $btn.closest('.barbers-attr-image-wrap, td');
        var $input   = $wrap.find('#barbers_attribute_image_id');
        var $preview = $wrap.find('.barbers-attr-image-preview');
        var $remove  = $wrap.find('.barbers-remove-image-btn');

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Odaberi sliku atributa',
            button: { text: 'Koristi ovu sliku' },
            multiple: false,
            library: { type: 'image' }
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            $input.val(attachment.id);
            $preview.html('<img src="' + attachment.url + '" style="width:100px;height:100px;object-fit:cover;" alt="">');
            $btn.text('Zameni sliku');
            $remove.show();
        });

        frame.open();
    });

    $(document).on('click', '.barbers-remove-image-btn', function (e) {
        e.preventDefault();
        var $wrap = $(this).closest('.barbers-attr-image-wrap, td');
        $wrap.find('#barbers_attribute_image_id').val('');
        $wrap.find('.barbers-attr-image-preview').html('');
        $wrap.find('.barbers-upload-image-btn').text('Dodaj sliku');
        $(this).hide();
        frame = null;
    });

})(jQuery);