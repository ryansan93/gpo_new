var nama = null;
var status = null;

var jp = {
	start_up: function () {
	}, // end - start_up

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/JenisPesanan/modalAddForm',{
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

		        $(this).find('.nama').val(nama);
				$(this).find('.status').val(status);
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		var tr = $(elm).closest('tr');

		$('.modal').modal('hide');

        $.get('parameter/JenisPesanan/modalEditForm',{
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

		  //       $(this).find('.nama').val(nama);
				// $(this).find('.status').val(status);
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

			nama = $(div).find('.nama').val().toUpperCase();

			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if ( result ) {
					var data = {
						'nama': nama,
						'exclude': ( $(div).find('input#exclude').is(':checked') ) ? '1' : '0',
						'include': ( $(div).find('input#include').is(':checked') ) ? '1' : '0'
					};

			        $.ajax({
			            url: 'parameter/JenisPesanan/save',
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
			                    	jp.modalAddForm();
			                    });
			                }
			            }
			        });
				} else {
					jp.modalAddForm();
				}
			});
		}
	}, // end - save

	edit: function(elm) {
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

			var kode = $(elm).data('kode');
			nama = $(div).find('.nama').val().toUpperCase();

			bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
				if ( result ) {
					var data = {
						'kode': kode,
						'nama': nama,
						'exclude': ( $(div).find('input#exclude').is(':checked') ) ? '1' : '0',
						'include': ( $(div).find('input#include').is(':checked') ) ? '1' : '0'
					};

			        $.ajax({
			            url: 'parameter/JenisPesanan/edit',
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
			                    	jp.modalEditForm();
			                    });
			                }
			            }
			        });
				} else {
					jp.modalEditForm();
				}
			});
		}
	}, // end - edit

	delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				kode = $(tr).data('kode');

		        $.ajax({
		            url: 'parameter/JenisPesanan/delete',
		            data: {
		                'kode': kode
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
};

jp.start_up();