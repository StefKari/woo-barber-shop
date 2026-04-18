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
        $(document).on('click','.product-gallery-upload',function (event) {
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
                title: product_gallery_360.labels.upload_file_frame_title,
                button: {
                    text: product_gallery_360.labels.upload_file_frame_button
                },
                multiple: 'add'
            } );
            // When an image is selected, run a callback.
            file_frame[id].on( 'select', function() {
                //var ids =[];
                var attachments =  file_frame[id].state().get('selection').map(function( attachment ) {
                    attachment.toJSON();
                    $('#product-gallery-images-'+field_id).append('<li class="image"><img src="'+attachment.attributes.url+'" alt=""><input type="hidden" name="_gallery_360degree['+field_id+'][]" value="'+attachment.id+'"><a href="#" class="delete remove-product-gallery-image"><span class="dashicons dashicons-dismiss"></span></a></li>');
                });
            });
            // Finally, open the modal.
            file_frame[id].open();
        });
        $(document).on('click','.remove-product-gallery-image',function () {
            $(this).closest('.image').remove();
            return false;
        });
    });
})(jQuery, window, document);