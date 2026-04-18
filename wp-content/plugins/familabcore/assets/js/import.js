(function ($) {
	var post_excuted = [],
	wxrImport = {
		complete: {
			package: 0,
			posts: 0,
			media: 0,
			comments: 0,
			terms: 0,
			categories: 0,
            widget :0,
        	setting :0,
		},
		updateDelta: function (type, delta) {
			this.complete[ type ] += delta;
			var self = this;
			requestAnimationFrame(function () {
				self.render();
			});
		},
		updateProgress: function ( type, complete, total ) {
			var text = complete + '/' + total;
			document.getElementById( 'completed-' + type ).innerHTML = text;
			total = parseInt( total, 10 );
			if ( 0 === total || isNaN( total ) ) {
                complete = 1;
				total = 1;
			}

			var percent = parseInt( complete, 10 ) / total,
				display_percent = Math.round( percent * 100 );
			if (display_percent > 100){
				display_percent = 100;
			}
            if (type ==  'package'){
                if (!$('#progressbar-package').hasClass('running')&& !$('#progressbar-package').hasClass('done') && !$('#progressbar-package').hasClass('pre-done')){
                    $('#progressbar-package').addClass('running');
                    $('#progressbar-package').attr('current-percent',0);
                    var interval_obj = setInterval(function(){
                        if ($('#progressbar-package').hasClass('done')){
                            clearInterval(interval_obj);
                            $('#progressbar-package').css('width','100%');
                            $('#progress-package').html('100%');
                        }else{
                            var p = parseInt($('#progressbar-package').attr('current-percent'));
                            if (p < 99){
                                var pc = p+1;
                                $('#progressbar-package').css('width',pc+'%');
                                $('#progress-package').html(pc+'%');
                                $('#progressbar-package').attr('current-percent',pc);
                            }else{
                                clearInterval(interval_obj);
                            }
                        }
                    }, 300);
                }
                if (display_percent == 100){
                    document.getElementById('progress-package').innerHTML = display_percent + '%';
                    document.getElementById( 'progressbar-package').style.width = display_percent + '%';
				}
			}else{
                document.getElementById('progress-' + type).innerHTML = display_percent + '%';
                document.getElementById( 'progressbar-' + type ).style.width = display_percent + '%';
			}
            if ( display_percent == 100){
                if (type ==  'package'){
                    $('#progressbar-package').removeClass('running');
				}
                $('#progressbar-' + type).addClass('pre-done');
            	setTimeout(function () {
                    $('#progressbar-' + type).addClass('done');
                    $('#progressbar-' + type).removeClass('pre-done');
                },500);
            }
		},
		render: function () {
			var types = Object.keys( this.complete ),
			 	complete = 0,
			 	total = 0,
			 	complate_check = true;
			for (var i = types.length - 1; i >= 0; i--) {
				var type = types[i];
				if (!$('#progressbar-' + type).hasClass('pre-done') && !$('#progressbar-' + type).hasClass('done')){
                    this.updateProgress( type, this.complete[ type ], this.data.count[ type ] );
				}
				if (this.complete[ type ] < this.data.count[ type ]){
					complate_check = false;
				}
                complete += this.complete[ type ];
                total += this.data.count[ type ];
			}
			if (!complate_check && complete >= total){
				complete =  total - (total/100);
			}
			this.updateProgress( 'total', complete, total );
		}
	};

	wxrImport.data = wxrImportData;
	wxrImport.render();

	var evtSource = new EventSource( wxrImport.data.url );
	evtSource.onmessage = function ( message ) {
		var data = JSON.parse( message.data );
		switch ( data.action ) {
			case 'updateDelta':
				if (data.type == 'posts' || data.type == 'media'){
					if (typeof (data.post_id) != 'undefined' && post_excuted.indexOf(data.post_id) == -1){
						post_excuted.push(data.post_id);
						wxrImport.updateDelta( data.type, data.delta );
					}
				}else{
					wxrImport.updateDelta( data.type, data.delta );
				}
				break;
			case 'complete':
				evtSource.close();
				var import_status_msg = jQuery('#import-status-message');
				import_status_msg.text( wxrImport.data.strings.complete );
				import_status_msg.removeClass('notice-info');
				import_status_msg.addClass('notice-success');
				break;
		}
	};
	evtSource.addEventListener( 'log', function ( message ) {
		var data = JSON.parse( message.data );
		/*if (data.level == 'error'){
			console.log(data);
		}*/
		// add row to the table, allowing DataTable to keep rows sorted by log-level
		// add row to the table, allowing DataTable to keep rows sorted by log-level
		var table = $('#import-log').DataTable();
		var rowNode = table
			.row.add( [data.level, data.message] )
			.draw()
			.node();
		$( rowNode ).addClass( data.level );
	});

	// sorting/pagination of log messages, using the DataTables jquery plugin
	$( '#import-log' ).DataTable( {
		order: [[ 0, 'asc' ]],
		columns: [
			{ type: 'log-level' },
			{ type: 'string' },
		],
		lengthMenu: [[ 10, 20, 40, -1 ], [ 10, 20, 40, 'All' ]],
		pageLength: 10,
		pagingType: 'full_numbers',
	});
	// extend DataTables to allow sorting by log-level
	$.extend( jQuery.fn.dataTableExt.oSort, {
	    'log-level-asc': function( a, b ) {
	    	return log_level_orderby( a, b );
	    },
	    'log-level-desc': function(a,b) {
	    	return - log_level_orderby( a, b );
	    }
	} );

	/**
	 * Ordering by log-level
	 *
	 * @param a
	 * @param b
	 * @returns -1, 0, 1
	 */
	function log_level_orderby( a, b ) {
		switch ( a ) {
			case 'error':
				switch ( b ) {
					case 'error':
						return 0;
					default:
						return 1;
				}
			case 'warning':
				switch ( b ) {
					case 'error':
						return -1;
					case 'warning':
						return 0;
					default:
						return 1;
				}
			case 'notice':
				switch ( b ) {
					case 'error':
					case 'warning':
						return -1;
					case 'notice':
						return 0;
					default:
						return 1;
				}
			case 'info':
				return -1;
			default:
				return 0;
		}
	}
})(jQuery);
