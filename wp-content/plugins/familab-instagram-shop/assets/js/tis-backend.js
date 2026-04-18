jQuery(document).ready(function ($) {
  	"use strict";
	tis_check_images_from_metafields();
	tis_init_sortable();
	tis_init_dragable();
	tis_init_select2();
	//ADD IMAGE TO POST
	$(document).on('click', '.instagram-item', function (evt) {
	 	tis_select_image(evt);
	})
	//REMOVE IMAGE FROM POST
	$(document).on('click', '.tis-remove-image', function (e) {
		var accepted = false;
		if ($(this).parents('.single-image').find('.single-pin').length > 0) {
			var r = confirm("There are some pin already added to this image, do you wish to continue?");
			if (r == true) {
			    accepted = true;
			}
		}else{
			 accepted = true;
		}
		if (accepted) {
			var image_id = $(this).parents('.single-image').data('id');
		 	$('.instagram-item[data-id='+image_id+']').find('input').prop('checked', false);
		 	$('.instagram-item[data-id='+image_id+']').removeClass('selected');
		 	$(this).parents('.single-image').fadeOut(400, function(){
		 		$(this).remove();
		 		tis_update_pin_data();
		 	})
		}
	})
	//ADD PIN TO IMAGE
	$(document).on('click', '.single-image-holder > img', function (e) {
		var cursor_pos = {}; // Percent
		var thisImgHolder = $(this).parents('.single-image-holder');
        var img_holder_offset = thisImgHolder.offset();
        var holder_w = thisImgHolder.innerWidth();
    	var holder_h = thisImgHolder.innerHeight();
    	var image_id =  $(this).parents('.single-image').data('id');
        cursor_pos = {
            left: ((e.pageX - img_holder_offset.left - 15) / holder_w) * 100,
            top: ((e.pageY - img_holder_offset.top - 15) / holder_h) * 100
        };
	    var cur_modal = $('#tis-edit-modal');
	    var cur_number = thisImgHolder.find('.single-pin ').length+1;
 		cur_modal.find('.tis-pin-text').val(cur_number);
       	cur_modal.find('.single-pin').text(cur_number);
       	cur_modal.attr('data-pos-x', cursor_pos.left );
       	cur_modal.attr('data-pos-y', cursor_pos.top );
       	cur_modal.attr('data-image-id', image_id );
       	cur_modal.attr('is-editing','false');
	    cur_modal.modal('show');
	})
	//EDIT SINGLE PIN
	$(document).on('click', '.single-pin', function (e) {
		var cur_modal = $('#tis-edit-modal'),
			cur_number = $(this).attr('data-number'),
			image_id = $(this).parents('.single-image').attr('data-id'),
			pos_x = $(this)[0].style.left,
			pos_y = $(this)[0].style.top,
			pin_style = $(this).attr('data-pin-style'),
			product_id =  $(this).attr('data-product-id'),
			current_image = $('.single-image[data-id='+image_id+']');
		cur_modal.find('.tis-pin-text').val(cur_number);
       	cur_modal.find('.single-pin').text(cur_number);
       	cur_modal.find('.tis-pin-style').val(pin_style);
       	cur_modal.find('.pin-preview .single-pin').attr('class', 'single-pin ' + pin_style );
       	cur_modal.attr('data-pos-x', pos_x );
       	cur_modal.attr('data-pos-y', pos_y );
       	cur_modal.attr('data-image-id', image_id );
       	if (product_id != '') {
       		tis_load_single_product(product_id);
       	}
       	cur_modal.attr('is-editing','true');
	    cur_modal.modal('show');
	})
	// SAVE PIN DATA
	$(document).on('click', '.tis-modal-save', function (e) {
		var cur_modal = $('#tis-edit-modal'),
			cur_number = cur_modal.find('.tis-pin-text').val(),
			image_id = cur_modal.attr('data-image-id'),
			pos_x = cur_modal.attr('data-pos-x'),
			pos_y = cur_modal.attr('data-pos-y'),
			pin_style = cur_modal.find('.tis-pin-style').val(),
			product_id = $('#tis-product-select').val(),
			current_image = $('.single-image[data-id='+image_id+']');
		if (cur_modal.attr('is-editing') == 'false') {
			tis_add_single_pins(image_id, cur_number, pos_x, pos_y, product_id, pin_style );
		}else{
			var pin_list = $('.single-image[data-id="'+image_id+'"] .single-pin');
			$(pin_list).each(function(i,e){
				var top = $(e)[0].style.top;
				var left = $(e)[0].style.left;
				var $this = $(this);
				if (top == pos_y && left == pos_x) {
					$this.attr('data-pin-style', pin_style );
					$this.attr('data-product-id', product_id );
					$this.attr('data-number', cur_number );
					$this.html(cur_number);
					$this.removeClass (function (index, className) {
					    return (className.match (/(^|\s)style\S+/g) || []).join(' ');
					});
					$this.addClass(pin_style);
				}
			})
		}
		tis_update_pin_to_image(current_image);
		cur_modal.modal('hide');
	})
	//DELETE SINGLE PIN
	$(document).on('click', '.tis-modal-delete', function (e) {
		var cur_modal = $('#tis-edit-modal'),
			image_id = cur_modal.attr('data-image-id'),
			pos_x = cur_modal.attr('data-pos-x'),
			pos_y = cur_modal.attr('data-pos-y'),
			current_image = $('.single-image[data-id='+image_id+']');
		var pin_list = $('.single-image[data-id="'+image_id+'"] .single-pin');
		$(pin_list).each(function(i,e){
			var top = $(e)[0].style.top;
			var left = $(e)[0].style.left;
			var $this = $(this);
			if (top == pos_y && left == pos_x) {
				$this.remove();
			}
		})
		tis_update_pin_to_image(current_image);
		cur_modal.modal('hide');
	})
	//SWITCH VIEW 
	$(document).on('click', '.tis-toggle-view', function (e) {
		var current_view = $('.single-image').attr('class');
		var new_view;
		if (current_view.includes('col-6')) { 
		 	new_view = 'col-2';
		}else if (current_view.includes('col-4')){
			new_view = 'col-6';
		}else if (current_view.includes('col-3')){
			new_view = 'col-4';
		}else if (current_view.includes('col-2')){
			new_view = 'col-3';
		}
		$('.tis-js-choosen-images .single-image').attr('class', 'single-image ' + new_view);
		$(this).attr('data-current-view',new_view );
	})
	//LOAD MORE IMAGES
	$(document).on('click', '.tis-insta-loadmore', function (e) {
		e.preventDefault();
		var pageUrl = $(this).attr('data-next-url');
		if ($(this).hasClass('loading')) {
			return false;
		}else{
			tis_loadmore(pageUrl);	
		}
	})
	//CHECK MODAL STATUS --->
		$('#tis-edit-modal').on('show.bs.modal', function (e) {
		  	if ($(this).attr('is-editing') == 'false') {
		  		$(this).find('.tis-modal-delete').addClass('d-none');
		  	}else{
		  		$(this).find('.tis-modal-delete').removeClass('d-none');
		  	}
		})
		$('#tis-edit-modal').on('hidden.bs.modal', function () {
		   	$('#tis-product-select').find('option').remove();//Clear current selector
			$('.tis-product-preview').attr('src', $('.tis-product-preview').data('src') );//Clear current image
		})
	//CHECK MODAL STATUS <---
	// REFRESH EXPIRED IMAGES ->>>>>>>>
	$(document).on('click', '.tis-refresh-images', function (e) {
		e.preventDefault();
		if ($(this).hasClass('disabled')) { return;}
		tis_refresh_images();
	})
	// REFRESH EXPIRED IMAGES
	//UPDATE PREVIEW PIN STYLE --->
		$(document).on('change', '#tis-edit-modal .tis-pin-style', function (e) {
			var thisModal = $(this).parents('#tis-edit-modal');
			var single_pin = thisModal.find('.pin-preview .single-pin');
			single_pin.removeClass();
			single_pin.addClass($(this).val() + ' single-pin');
		})
	//UPDATE PREVIEW PIN STYLE <---
	//CHECK BACKDROP POPUP --->
		$(document).on('click', '.tis-toggle-picker', function (e) {
			$('.tis-drawer-settings').removeClass('expanded');
			$('.tis-drawer-images').toggleClass('expanded');
			tis_check_backdrop();
		})
		$(document).on('click', '.tis-toggle-settings', function (e) {
			$('.tis-drawer-images').removeClass('expanded');
			$('.tis-drawer-settings').toggleClass('expanded');
			tis_check_backdrop();
		})
		$(document).on('click', '.tis-popup-close', function (e) {
			$('.tis-drawer-images').removeClass('expanded');
			$('.tis-drawer-settings').removeClass('expanded');
			$('.instagram-images-overlay').fadeOut(300);
		})
	//CHECK BACKDROP POPUP <---
	// SHORTCODE SETTINGS CONTROL BUTTONS --->
		$(document).find('#tis_use_custom_responsive').on('change', function(){
			$(this).parents('.shortcode-use-custom-responsive').find('.tis-settings-responsive').fadeToggle();
		})
		$(document).find('.shortcode-style-select select').on('change', function(){
			var responsive_wrapper = $('.shortcode-use-custom-responsive'),
				responsive_setting = responsive_wrapper.find('.tis-settings-responsive'),
				custom_responsive =  $('#tis_use_custom_responsive'),
				carousel_space = $('.shortcode-carousel-space'),
				current_val = $(this).val().toString();
			switch(current_val) {
			  	case 'masorny':
			  	case 'pyramid':
			  	case 'masorny2':
			  	case 'masorny3':
				    responsive_wrapper.fadeOut();
				    responsive_setting.fadeOut();
				   	custom_responsive.prop('checked', false);
				   	carousel_space.fadeOut();
				    break;
			 	default:
			 		if (current_val == 'carousel') {
			 			var input_html = $('.shortcode-use-custom-responsive .hidden-settings[name="responsive_input"]').val();
			 			$('.tis-settings-responsive').html(input_html);
			 			carousel_space.fadeIn();
			 		}else{
			 			var input_html = $('.shortcode-use-custom-responsive .hidden-settings[name="responsive_dropdown"]').val();
			 			$('.tis-settings-responsive').html(input_html);
			 			carousel_space.fadeOut();
			 		}
			 		responsive_wrapper.fadeIn();
					if (custom_responsive.prop('checked') == true) {
						responsive_setting.fadeIn();
					}else{
						responsive_setting.fadeOut();
					}	
			}
		})
		$(document).on('click', '.tis_copy_shortcode', function(){
			$('#tis_short_code_copy').select();
			document.execCommand("copy");
			$('.tis-copy-notice').fadeIn();
			window.setTimeout(function(){
				$('.tis-copy-notice').fadeOut();
			}, 2000);
		})
	// SHORTCODE SETTINGS CONTROL BUTTONS <---
	// ========================
  	// * UPDATE PINS DATA TO CURRENT IMAGE
  	// ========================
  	function tis_update_pin_to_image($image){
  		var products = {};
		$image.find('.single-pin').each(function(i, e){
  			var single_pin_data = [],
  				number = $(this).data('number'),
  				product_id = $(this).data('product-id'),
  				style = $(this).data('pin-style'),
  				x_pos = $(this)[0].style.left,
  				y_pos = $(this)[0].style.top;
  			single_pin_data = [product_id, x_pos, y_pos, number, style];
  			products[i] = single_pin_data;
		})
		$image.attr('data-products', JSON.stringify(products));
		tis_update_pin_data();
		tis_init_dragable();
  	}
	// ========================
  	// * LOAD SELECTED IMAGES
  	// ========================
  	function tis_check_images_from_metafields(){
  		//Get current saved images in JSON
  		var images_data = $('#tis-metafields').val();
  		if (images_data != '') {
  			var img_array = JSON.parse(images_data);
	  		$.each(img_array, function(i,e){
	  			//Check the checkbox if images already choosen
	  			$('.instagram-item[data-id='+i+']').find('input').prop('checked', true);
	  			$('.instagram-item[data-id='+i+']').addClass('selected');
	  		})
  		}
  	}
  	// ========================
  	// * UPDATE METAFIELD DATA
  	// ========================
  	function tis_update_pin_data(){
  		$('#tis-metafields').val('');
  		var all_images = {};
  		var single_image_arr = $('.tis-image-items .single-image');
  		if (single_image_arr.length > 0) {
  			single_image_arr.each(function(){
  				var product_list = $(this).attr('data-products');
  				if (product_list != '') {
  					product_list = JSON.parse(product_list);
  				}
	  			var image_id = $(this).data('id'),
	  			image_data = {
						"src": $(this).data('src'),
						"width": $(this).data('width'),
						"height": $(this).data('height'),
						"link": $(this).data('link'),
						"caption": $(this).data('caption'),
						"products": product_list,
						"thumb": $(this).data('thumb'),
						"low_res": $(this).data('low-res'),
				};
				all_images[image_id] = image_data;
	  		})
	  		$('#tis-metafields').val( JSON.stringify(all_images));
  		}else{
  			$('#tis-metafields').val('');
  		}
  		if ($('.tis-image-items .single-image').length > 0) {
			$('.tis-error-field.no-image').fadeOut();
		}else{
			$('.tis-error-field.no-image').fadeIn();
		}
  	}
  	// ========================
  	// * ADD SELECTED IMAGE TO POST
  	// ========================
	function tis_update_post_images($obj){
		var duplicated = false;
		var image_id = $obj.data('id'),
			image_src = $obj.data('src'),
			image_width = $obj.data('width'),
			image_height = $obj.data('height'),
			image_link = $obj.data('link'),
			image_thumb = $obj.data('thumb'),
			image_low_res = $obj.data('low-res'),
			image_caption = b64EncodeUnicode($obj.data('caption'));
		//check for existing images
		$('.tis-image-items .single-image').each(function(){
  			var cur_image_id = $(this).data('id');
  			if(image_id == cur_image_id){
  				duplicated = true;
  			}
  		})
  		if (duplicated) {
  			//Already has an image with the same id
  		}else{
  			var current_view = $('.tis-toggle-view').attr('data-current-view');
  			var template = '<div class="'+current_view+' single-image"';
					template += 'data-id="'+image_id+'"';
					template += 'data-link="'+image_link+'"';
					template += 'data-thumb="'+image_thumb+'"';
					template += 'data-low-res="'+image_low_res+'"';
					template += 'data-src="'+image_src+'"';
					template += 'data-products=""';
					template += 'data-width="'+image_width+'" data-height="'+image_height+'"';
					template += 'data-caption="'+image_caption+'">';
					template += '<div class="single-image-action">';
					template += '	<div class="tis-move-image btn-primary" title="'+tis_strings.text.order_image+'"><i class="fa fa-arrows"></i></div>';
					template += '	<div class="tis-remove-image btn-danger" title="'+tis_strings.text.remove_image+'"><i class="fa fa-trash"></i></div>';
					template += '</div>';
					template += '<div class="single-image-holder">';
					template += '	<img width="'+image_width+'" height="'+image_height+'" src="'+image_src+'">';
					template += '</div>';
				template += '</div>';
			$('.tis-image-items').append(template);
	        //tis_update_pin_data();//Update metafield data
	        tis_init_sortable(); //Update sortable
  		}
	}
	// ========================
  	// * SORTABLE MODULE
  	// ========================
	function tis_init_sortable() {
        $('.tis-sortable').sortable({
        	handle: ".tis-move-image",
            update: function (event, ui) {
                tis_update_pin_data();
            }
        });
	}
	// ========================
  	// * DRAGABLE MODULE
  	// ========================
	function tis_init_dragable() {
		$('.single-image-holder .single-pin').each(function () {
            $(this).draggable({
                'containment': 'parent', // .img-holder
                'scroll': false,
                'stop': function (event, ui) {
                    var thisParent = ui.helper.closest('.single-image-holder');
                    var current_image =  ui.helper.parents('.single-image');
                    var parent_w = thisParent.innerWidth();
                    var parent_h = thisParent.innerHeight();
                    var ui_top_percent = 0;
                    var ui_left_percent = 0;
                    if (parent_h > 0) {
                        ui_top_percent = (ui.position.top * 100)/ parent_h;
                    }
                    if (parent_w > 0) {
                        ui_left_percent = (ui.position.left * 100) / parent_w;
                    }
                    ui.helper.attr('data-top_percent', ui_top_percent).attr('data-left_percent', ui_left_percent).css({
                        'top': ui_top_percent + '%',
                        'left': ui_left_percent + '%'
                    });
                    tis_update_pin_to_image(current_image);
                }
            });
        });
	}
	// ========================
  	// * SELECT/UNSELECT IMAGE
  	// ========================
  	function tis_select_image(evt){
  		var $obj, $obj_input;
  		if ($(evt.target).hasClass('tis-checkbox') ) {
  			//clicking on checkbox
  			$obj_input = $(evt.target);
  			$obj = $obj_input.parents('.instagram-item');
  		}else{
  			//clicking on image
  			$obj = $(evt.target).parents('.instagram-item');
  			$obj_input = $obj.find('input');
  			if ($obj_input.prop('checked') == true) {
  				$obj_input.prop('checked', false);
  			}else{
  				$obj_input.prop('checked', true);
  			}
  		}
  		var image_id = $obj.data('id');
  		//check & uncheck single image
  		if ($obj_input.prop('checked') == true) {
			$obj.addClass('selected');
			tis_update_post_images($obj);
  			tis_update_pin_data();
		}else{
			var accepted = false;
			var image_id = $obj.data('id');
			if($('.single-image[data-id='+image_id+']').find('.single-pin').length > 0) {
				var r = confirm("There are some pin already added to this image, do you wish to continue?");
				if (r == true) {
				    accepted = true;
				}else{
				}
			}else{
				 accepted = true;
			}
			if (accepted) {
				$obj.removeClass('selected');
				//Remove image from list if unchecked
		  		$('.tis-image-items .single-image').each(function(){
		  			var cur_image_id = $(this).data('id');
		  			if(image_id == cur_image_id){
		  				$(this).fadeOut( 400, function() {
						    $(this).remove();
						    tis_update_pin_data();
						});
		  			}
		  		})
			}else{
				if ($obj_input.prop('checked') == true) {
	  				$obj_input.prop('checked', false);
	  			}else{
	  				$obj_input.prop('checked', true);
	  			}
			}
		}
		if (accepted) {
			var image_id = $(this).parents('.single-image').data('id');
		 	$('.instagram-item[data-id='+image_id+']').find('input').prop('checked', false);
		 	$('.instagram-item[data-id='+image_id+']').removeClass('selected');
		 	$(this).parents('.single-image').fadeOut(400, function(){
		 		$(this).remove();
		 		tis_update_pin_data();
		 	})
		}
		// aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
  	}
  	// ========================
  	// * Add pins to image 
  	// ========================
  	function tis_add_single_pins(image_id, number, x_pos, y_pos, product_id, pin_style ){
  		if ($('.single-pin.ui-draggable').length) {
            $('.single-pin.ui-draggable').draggable('destroy');
        }
        var single_pin_el = '<div class="single-pin '+pin_style+'" data-pin-style="'+pin_style+'" data-product-id="'+product_id+'" data-number="'+number+'" style="top: ' + y_pos + '%; left: ' + x_pos + '%;">'+number+'</div>';
		$('.single-image[data-id='+image_id+'] .single-image-holder').append(single_pin_el);
  	}
  	// ========================
  	// * Check backdrop for popup
  	// ========================
	function tis_check_backdrop(){
		if ($('.tis-drawer-settings').hasClass('expanded') || $('.tis-drawer-images').hasClass('expanded') ) {
			$('.instagram-images-overlay').fadeIn(300);	
		}else{
			$('.instagram-images-overlay').fadeOut(300);	
		}
	}
	// ========================
  	// * Load single product
  	// ========================
	function tis_load_single_product(id){
		$.ajax({
		  method: "POST",
		  url: tis_strings.ajax_url,
		  data: { 
		  	 action: 'tis_load_single_product',
		  	 q:id
		  }
		})
		  .done(function( result ) {  	
       		$('#tis-product-select').append('<option selected="selected" value="'+result.id+'" title="'+result.title+'">'+result.title+'</option>');
       		$('.tis-product-preview').attr('src', result.img);
		  });
	}
	// ========================
  	// * SELECT2 MODULE
  	// ========================
  	function tis_init_select2(){
  		$("#tis-product-select").select2({
		    ajax: {
		    url: tis_strings.ajax_url,
		    type: 'POST',
		    dataType: 'json',
		    delay: 250,
		    data: function (params) {
		      return {
		        q: params.term, // search term
		        page: params.page || 1,
		        perpage: $(this).data('perpage'),
		        action: 'tis_load_products'
		      };
		    },
		    processResults: function (data, params) {
		      // parse the results into the format expected by Select2
		      // since we are using custom formatting functions we do not need to
		      // alter the remote JSON data, except to indicate that infinite
		      // scrolling can be used
		      params.page = params.page || 1;
		      var perpage = data.perpage;
	      	  return {
		        results: data.items,
		        pagination: {
		          more: (params.page * perpage) < data.total
		        }
		      };
		    },
		    cache: false
		  },
		  placeholder: tis_strings.text.select_product,
		  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		  minimumInputLength: 1,
		  templateResult: tis_select_formatHTML,
		  templateSelection: tis_select_format_data
		})
  	}
  	function tis_select_formatHTML (data) {
	  if (data.loading) {
	    return data.text;
	  }
	  	var markup = '';
	  		markup += '<div class="tis-select2-option data-id="'+data.id+'">';
	  		markup += '<img src="'+data.img+'">';
	  		markup += '<span>'+data.title+'</span>';
	  		markup += '</div>';
	  return markup;
	}
	function tis_select_format_data (data) {
		$('#tis-edit-modal .tis-product-preview').attr('src', data.img);
	  return data.title || data.text;
	}
	// ============================
  	// * Load more instagram images
  	// ============================
	function tis_loadmore(pageUrl){
		var noncefield = $('.tis-load-more-nonce').val(),
			load_more_btn =  $('.tis-insta-loadmore');
		$.ajax({
		 	method: "POST",
		  	url: tis_strings.ajax_url,
		  	data: { 
		  	 	action: 'tis_load_more_instagram',
		  	 	next_page_url: pageUrl,
		  	 	nonce: noncefield
		  	},
		  	beforeSend: function(){
				load_more_btn.addClass('loading');
			}
		}).done(function( result ) {  	
	   		if (result['err'] === 'yes') {
                load_more_btn.removeClass('loading');
            }
            else {
                load_more_btn.removeClass('loading');
                $('.tis-js-instagram-images .insta-images-wrapper .instagram-items').append(result['html']);
                if (result['has_more'] === 'yes') {
                   	load_more_btn.attr('data-next-url', result['next_url']);
                }
                else {
                    load_more_btn.remove();
                }
            }
            tis_check_images_from_metafields();
	  	});
	}
	// ============================
  	// * Refresh expired images
  	// ============================
  	function testImages(Images, Callback){
	    // Keep the count of the verified images
	    var allLoaded = 0;
	    // The object that will be returned in the callback
	    var _log = {
	        success: [],
	        error: []
	    };
	    // Executed everytime an img is successfully or wrong loaded
	    var verifier = function(){
	        allLoaded++;
	        // triggers the end callback when all images has been tested
	        if(allLoaded == Images.length){
	            Callback.call(undefined, _log);
	        }
	    };
	    // Loop through all the images URLs
	    for (var index = 0; index < Images.length; index++) {
	        // Prevent that index has the same value by wrapping it inside an anonymous fn
	        (function(i){
	            // Image path providen in the array e.g image.png
	            var imgSource = Images[i][0];
	            var imgId = Images[i][1];
	            var img = new Image();
	            img.addEventListener("load", function(){
	                _log.success.push(imgId);
	                verifier();
	            }, false); 
	            img.addEventListener("error", function(){
	                _log.error.push(imgId);
	                verifier();
	            }, false); 
	            img.src = imgSource;
	        })(index);
	    }
	}
	function tis_refresh_images(){
		var test_images_list = [];
		//Check the images status
		$('.tis-js-choosen-images .single-image').each(function(){
			var this_image = $(this);
			var image_src =  this_image.attr('data-src');
			var image_id =  this_image.attr('data-id');
			test_images_list.push([image_src, image_id]);
		})
		testImages(test_images_list, function(result){
			var expired_list = result.error;
			if (expired_list.length == 0 ) { return; }
			$('.tis-refresh-images').addClass('disabled');
			$('.tis-refresh-images .fa-refresh').addClass('fa-spin');
			var noncefield = $('.tis-load-more-nonce').val();
			$.ajax({
			 	method: "POST",
			  	url: tis_strings.ajax_url,
			  	data: { 
			  	 	action: 'tis_refresh_instagram',
			  	 	expired_list: expired_list,
			  	 	nonce: noncefield,
			  	 	post_id: $('#post_ID').val()
			  	}
			}).done(function( result ) {  	
		   		if (result != false) {
		   			$.each(result, function(key,value){
		   				var current_image = $('.tis-js-choosen-images .single-image[data-id='+key+']');
		   				current_image.attr('data-src', value.src);
		   				current_image.attr('data-low-res', value.low_res);
		   				current_image.attr('data-thumb', value.thumb);
		   				current_image.find('.single-image-holder img').attr('src', value.src);
		   			})
		   		}
		   		$('.tis-refresh-images').removeClass('disabled');
		   		$('.tis-refresh-images .fa-refresh').removeClass('fa-spin');
		   		tis_update_pin_data();
		  	});
		});
	}

	//Extended functions for encoding

	// Encoding UTF8 ⇢ base64
	function b64EncodeUnicode(str) {
	    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
	        return String.fromCharCode(parseInt(p1, 16))
	    }))
	}
	// Decoding base64 ⇢ UTF8
	function b64DecodeUnicode(str) {
	    return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
	        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
	    }).join(''))
	}
})