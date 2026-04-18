/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
;(function ($) {
    "use strict";
    $( document ).ready( function( $ ){
        var file_frame = [];
        // handles upload image
        $(document).on('click','.woo-variation-gallery-upload',function (event) {
            var t = $(this),
                id = t.attr('id'),
                field_id = t.data('id');

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if ( file_frame[id] ) {
                file_frame[id].open();
                return;
            }

            // Create the media frame.
            file_frame[id] = wp.media.frames.downloadable_file = wp.media( {
                title: woo_variation_gallery.labels.upload_file_frame_title,
                button: {
                    text: woo_variation_gallery.labels.upload_file_frame_button
                },
                multiple: 'add'
            } );

            // When an image is selected, run a callback.
            file_frame[id].on( 'select', function() {
                var ids =[];
                var attachments =  file_frame[id].state().get('selection').map(function( attachment ) {
                       attachment.toJSON();
                       $('#woo-variation-gallery-images-'+field_id).append('<li class="image"><img src="'+attachment.attributes.url+'" alt=""><input type="hidden" name="urus_woo_variation_gallery_images['+field_id+'][]" value="'+attachment.id+'"><a href="#" class="delete remove-woo-variation-gallery-image"><span class="dashicons dashicons-dismiss"></span></a></li>');
                });

                VariationChanged();
            });
            // Finally, open the modal.
            file_frame[id].open();
        });
        var VariationChanged = function () {
            $('#variable_product_options .wc_input_price').trigger('change');
        };
        $(document).on('click','.remove-woo-variation-gallery-image',function () {
            $(this).closest('.image').remove();
            VariationChanged();
            return false;
        });

    } );
})(jQuery, window, document);