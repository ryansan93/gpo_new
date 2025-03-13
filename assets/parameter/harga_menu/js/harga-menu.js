var hm = {
	start_up: function () {
		$('.tbl_harga select.branch').select2();
		$('.tbl_harga select.menu').select2();

		$('.tbl_harga select.branch').on('select2:select', function (e) {
			var val = e.params.data.id;

			if ( !empty(val) ) {
				$('.tbl_harga select.menu').removeAttr('disabled');
	        	$('.tbl_harga select.menu').find('option:not([data-branch='+val+'])').attr('disabled', 'disabled');
	        	$('.tbl_harga select.menu').select2();
			} else {
				$('.tbl_harga select.menu').attr('disabled', 'disabled');
				$('.tbl_harga select.menu').select2('val', '');
			}

        	hm.search();
		});

		$('.tbl_harga select.menu').on('select2:select', function (e) {
        	hm.search();
		});

		$('.tbl_harga select.jenis_pesanan').on('change', function () {
        	hm.search();
		});
	}, // end - start_up

	search: function () {
		var branch = $('.tbl_harga select.branch').select2('val');
		var menu = $('.tbl_harga select.menu').select2('val');
		var jenis_pesanan = $('.tbl_harga select.jenis_pesanan').val();

		var search = '';

		$('.tbl_harga').find('tbody tr').removeClass('hide');
		if ( !empty(branch) ) {
			search += '[data-branch="'+branch+'"]';
		}
		if ( !empty(menu) ) {
			search += '[data-menu="'+menu+'"]';
		}
		if ( !empty(jenis_pesanan) ) {
			search += '[data-jp="'+jenis_pesanan+'"]';
		}

		if ( !empty(search) ) {
			$('.tbl_harga').find('tbody tr:not('+search+')').addClass('hide');
		}
	}, // end - search

	getMenuByBranch: function (kode_branch) {
		var modal = $('.modal');

		$.ajax({
            url: 'parameter/HargaMenu/getMenuByBranch',
            data: {
                'kode_branch': kode_branch
            },
            type: 'GET',
            dataType: 'HTML',
            beforeSend: function() { showLoading(); },
            success: function(html) {
                hideLoading();

            	$('.menu').html(html);
	        	$('.menu').select2();
            }
        });
	}, // end - getMenu

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/HargaMenu/modalAddForm',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});

                $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this) );
				});

                var today = moment(new Date()).format('YYYY-MM-DD');
				$("#TglBerlaku").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date((today+' 00:00:00')))
		        });

		        $('.modal .menu').find('option').removeAttr('disabled');

		        $(this).find('.menu').select2();
		        $(this).find('.branch').select2();
		        $(this).find('.branch').on('select2:select', function (e) {
		        	var val = e.params.data.id;

		        	// hm.getMenuByBranch( val );

		        	$('.modal .menu').find('option').removeAttr('disabled', 'disabled');
		        	$('.modal .menu').find('option:not([data-branch='+val+'])').attr('disabled', 'disabled');
		        	$('.modal .menu').select2();
		        });
		        $(this).removeAttr('tabindex');
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		var tr = $(elm).closest('tr');

		$('.modal').modal('hide');

        $.get('parameter/HargaMenu/modalEditForm',{
        	'kode': $(tr).data('kode')
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});

                $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this) );
				});

				$("#StartDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date(($("#StartDate input").data('tgl')+' 00:00:00')))
		        });
		        $("#EndDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date(($("#EndDate input").data('tgl')+' 23:59:59')))
		        });
		        $("#StartDate").on("dp.change", function (e) {
	        		var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
	            	$("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
		        });
		        $("#EndDate").on("dp.change", function (e) {
	        		var maxDate = dateSQL($("#EndDate").data("DateTimePicker").date())+' 23:59:59';
	            	$("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
		        });

				$(this).find('#StartDate').data('DateTimePicker').date(moment(new Date($("#StartDate input").data('tgl'))));
				$(this).find('#EndDate').data('DateTimePicker').date(moment(new Date($("#EndDate input").data('tgl'))));
            });
        },'html');
	}, // end - modalEditForm

	save: function() {
		var div = $('.modal');

		var err = 0;
		$.map( $(div).find('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			$('.modal').modal('hide');

			var menu = $(div).find('.menu').val();
			var list_jenis_pesanan = $.map( $(div).find('.tbl_jenis_pesanan tbody tr.data'), function (tr) {
				var _data = {
					'jenis_pesanan': $(tr).find('td.kode').attr('data-val'),
					'harga': numeral.unformat( $(tr).find('input').val() )
				};

				return _data;
			});
			var tgl_berlaku = dateSQL($(div).find('#TglBerlaku').data('DateTimePicker').date());

			if ( list_jenis_pesanan.length > 0 ) {
				bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
					if ( result ) {
						var data = {
							'menu': menu,
							'list_jenis_pesanan': list_jenis_pesanan,
							'tgl_berlaku': tgl_berlaku
						};

				        $.ajax({
				            url: 'parameter/HargaMenu/save',
				            data: {
				                'params': data
				            },
				            type: 'POST',
				            dataType: 'JSON',
				            beforeSend: function() { showLoading(); },
				            success: function(data) {
				                hideLoading();
				                if ( data.status == 1 ) {
				                	bootbox.alert(data.message, function() {
				                		location.reload();
				                	});
				                } else {
				                    bootbox.alert(data.message, function() {
				                    	hm.modalAddForm();
				                    });
				                }
				            }
				        });
					} else {
						hm.modalAddForm();
					}
				});
			} else {
				bootbox.alert('Jenis pesanan tidak ditemukan.');
			}
		}
	}, // end - save

	delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				var data = {
					'menu': $(tr).find('td.menu').data('val'),
					'jenis_pesanan': $(tr).find('td.jenis_pesanan').data('val'),
					'tgl_berlaku': $(tr).find('td.tgl_mulai').data('val'),
					'harga': $(tr).find('td.harga').data('val')
				};

		        $.ajax({
		            url: 'parameter/HargaMenu/delete',
		            data: {
		                'params': data
		            },
		            type: 'POST',
		            dataType: 'JSON',
		            beforeSend: function() { showLoading(); },
		            success: function(data) {
		                hideLoading();
		                if ( data.status == 1 ) {
		                	bootbox.alert(data.message, function() {
		                		location.reload();
		                	});
		                } else {
		                    bootbox.alert(data.message);
		                }
		            }
		        });
			}
		});
    }, // end - delete

	showNameFile: function(elm, isLable = 1) {
		var _label = $(elm).closest('label');
		var _spanfile = _label.next('span');
		var _allowtypes = $(elm).data('allowtypes').split('|');
		var _type = $(elm).get(0).files[0]['name'].split('.').pop();
		var _namafile = $(elm).val();
		_namafile = _namafile.substring(_namafile.lastIndexOf("\\") + 1, _namafile.length);
		var temp_url = URL.createObjectURL($(elm).get(0).files[0]);
	
		if (in_array(_type, _allowtypes)) {
		  var _nameHtml = '<u>' + _namafile + '</u> ';

		  if (isLable == 1) {
			if (_spanfile.length) {
			  _spanfile.html(_nameHtml);
			} else {
			  if ( $(_label).next('a').length > 0 ) {
				$(_label).next('a').remove();
			  }
			  $('<a href='+temp_url+' target="_blank">' + _nameHtml + '</a>').insertAfter(_label);
			}
		  }else if (isLable == 0) {
			$(elm).closest('label').attr('title', _namafile);
		  }
		  $(elm).attr('data-filename', _namafile);
		} else {
			$(elm).val('');
			$(elm).closest('label').attr('title', '');
			$(elm).attr('data-filename', '');
			_spanfile.html('');
			bootbox.alert('Format file tidak sesuai. Mohon attach ulang.');
		}
	}, // end - showNameFile

	setBindSHA1 : function(){
        $('input:file').off('change.sha1');
        $('input:file').on('change.sha1',function(){
            var elm = $(this);
            var file = elm.get(0).files[0];
            elm.attr('data-sha1', '');
            sha1_file(file).then(function (sha1) {
                elm.attr('data-sha1', sha1);
            });
        });
    }, // end - setBindSHA1

    showNameFile : function(elm, isLable = 1) {
        var _label = $(elm).closest('label');
        var _a = _label.prev('a[name=dokumen]');
        _a.removeClass('hide');
        // var _allowtypes = $(elm).data('allowtypes').split('|');
        var _dataName = $(elm).data('name');
        var _allowtypes = ['xlsx'];
        var _type = $(elm).get(0).files[0]['name'].split('.').pop();
        var _namafile = $(elm).val();
        var _temp_url = URL.createObjectURL($(elm).get(0).files[0]);
        _namafile = _namafile.substring(_namafile.lastIndexOf("\\") + 1, _namafile.length);

        if (in_array(_type, _allowtypes)) {
            if (isLable == 1) {
                if (_a.length) {
                    _a.attr('title', _namafile);
                    _a.attr('href', _temp_url);
                    if ( _dataName == 'name' ) {
                        $(_a).text( _namafile );  
                    }
                }
            } else if (isLable == 0) {
                $(elm).closest('label').attr('title', _namafile);
            }
            $(elm).attr('data-filename', _namafile);
        } else {
            $(elm).val('');
            $(elm).closest('label').attr('title', '');
            $(elm).attr('data-filename', '');
            _a.addClass('hide');
            bootbox.alert('Format file tidak sesuai. Mohon attach ulang.');
        }
    }, // end - showNameFile

    importForm: function() {
        $.get('parameter/HargaMenu/importForm',{
        },function(data){
            var _options = {
                className : 'veryWidth',
                message : data,
                size : 'large',
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                var modal_dialog = $(this).find('.modal-dialog');
                var modal_body = $(this).find('.modal-body');

                $(modal_dialog).css({'max-width' : '40%'});
                $(modal_dialog).css({'width' : '40%'});

                var modal_header = $(this).find('.modal-header');
                $(modal_header).css({'padding-top' : '0px'});

                hm.setBindSHA1();
                
                $('.modal').removeAttr('tabindex');
            });
        },'html');
    }, // end - importForm

    import: function() {
		var file_tmp = $('.file_lampiran').get(0).files[0];

		if ( !empty($('.file_lampiran').val()) ) {
            $('.modal').modal('hide');
            
			var formData = new FormData();
	        formData.append('file', file_tmp);
            
            showLoading('Proses import data harga menu . . .');
			$.ajax({
                url: 'parameter/HargaMenu/import',
				dataType: 'json',
	            type: 'post',
	            async:false,
	            processData: false,
	            contentType: false,
	            data: formData,
				beforeSend: function() {
                    showLoading('Proses import data harga menu . . .');
				},
				success: function(data) {
					hideLoading();
					if ( data.status == 1 ) {
						bootbox.alert(data.message, function() {
							location.reload();
                            // $('.modal').modal('hide');
						});
					} else {
						bootbox.alert(data.message);
					};
				},
		    });
		} else {
			bootbox.alert('Harap isi lampiran terlebih dahulu.');
		}
	}, // end - import
};

hm.start_up();