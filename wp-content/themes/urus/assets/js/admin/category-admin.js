/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
(function ($) {
    "use strict";
    $( document ).ready( function( $ ){
        var file_frame = [];
        // handles upload image
        $( '.urus_upload_image_button').on( 'click', function( event ){
            var t = $(this),
                id = t.attr('id');
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if ( file_frame[id] ) {
                file_frame[id].open();
                return;
            }
            // Create the media frame.
            file_frame[id] = wp.media.frames.downloadable_file = wp.media( {
                title: urus_category.labels.upload_file_frame_title,
                button: {
                    text: urus_category.labels.upload_file_frame_button
                },
                multiple: false
            } );
            // When an image is selected, run a callback.
            file_frame[id].on( 'select', function() {
                var attachment = file_frame[id].state().get( 'selection' ).first().toJSON();

                t.prev().val( attachment.id );
                t.parent().prev().find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
                t.next().show();
            } );
            // Finally, open the modal.
            file_frame[id].open();
        } );
        // handles remove image
        $( '.urus_remove_image_button').on( 'click', function( event ){
            var t = $(this);
            event.preventDefault();
            t.siblings('input').val('');
            t.parent().prev().find( 'img' ).attr( 'src', urus_category.wc_placeholder_img_src );
            t.hide();
            return false;
        } );
        // hide remove button when not needed
        $( '.category_background' ).each( function(){
            var t = $(this);
            if( ! t.val() || t.val() == '0' ){
                t.siblings( '.urus_remove_image_button').hide();
            }
        } );
    });
})(jQuery, window, document);