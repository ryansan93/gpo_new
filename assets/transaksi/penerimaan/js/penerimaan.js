var terima = {
	start_up: function () {
		terima.setting_up();
	}, // end - start_up

	setting_up: function() {
        var today = moment(new Date()).format('YYYY-MM-DD');
        $("#StartDate, #EndDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        $("#StartDate").on("dp.change", function (e) {
            var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
            $("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
        });
        $("#EndDate").on("dp.change", function (e) {
            var maxDate = dateSQL($("#EndDate").data("DateTimePicker").date())+' 23:59:59';
            if ( maxDate >= (today+' 00:00:00') ) {
                $("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
            }
        });

        $("#TglTerima").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y',
            // minDate: moment(new Date((today+' 00:00:00')))
        });
        if ( !empty($("#TglTerima").find('input').data('tgl')) ) {
            var tgl = $("#TglTerima").find('input').data('tgl');
            $("#TglTerima").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }

        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $('select.supplier').select2();
        $('.gudang').select2().on('select2:select', function (e) {
            terima.getPo( $(this) );
            terima.getPoItem('');

            // $('input.supplier').removeAttr('disabled');
            var gudang = $(this).val();

            if ( !empty(gudang) ) {
                $('select.supplier').removeAttr('disabled');
            } else {
                $('select.supplier').attr('disabled', 'disabled');
            }
            $('select.supplier').val('').trigger('change');
        });

        $('.item').select2().on('select2:select', function (e) {
            var _tr = $(this).closest('tr');
            var select_satuan = $(_tr).find('select.satuan');

            var data = e.params.data.element.dataset;
            var coa = data.coa;
            var ket_coa = data.ketcoa;

            $(_tr).find('td.coa').html( coa+'<br>'+ket_coa );

            var satuan = JSON.parse( data.satuan );

            var opt = '<option value="">Pilih Satuan</option>';
            for (var i = 0; i < satuan.length; i++) {
                opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'">'+satuan[i].satuan+'</option>';
            }

            $(select_satuan).html( opt );
            $(select_satuan).removeAttr('disabled');
            $(_tr).find('.jumlah').removeAttr('disabled');
            $(_tr).find('.harga').removeAttr('disabled');
        });

        $('select.po').select2();
    }, // end - setting_up

    addRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        $(tr).find('select.item').select2('destroy')
                                   .removeAttr('data-live-search')
                                   .removeAttr('data-select2-id')
                                   .removeAttr('aria-hidden')
                                   .removeAttr('tabindex');
        $(tr).find('select.item option').removeAttr('data-select2-id');

        var tr_clone = $(tr).clone();

        $(tr_clone).find('input, select').val('');
        $(tr_clone).find('select.satuan').html('<option value="">Pilih Satuan</option>');
        $(tr_clone).find('select.satuan').attr('disabled', 'disabled');
        $(tr_clone).find('.jumlah').attr('disabled', 'disabled');
        $(tr_clone).find('.harga').attr('disabled', 'disabled');

        $(tr_clone).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $(tbody).append( $(tr_clone) );

        $.each($(tbody).find('select.item'), function(a) {
            $(this).select2();
            $(this).on('select2:select', function (e) {
                var _tr = $(this).closest('tr');
                var select_satuan = $(_tr).find('select.satuan');

                var data = e.params.data.element.dataset;
                var satuan = JSON.parse( data.satuan );

                var opt = '<option value="">Pilih Satuan</option>';
                for (var i = 0; i < satuan.length; i++) {
                    opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'">'+satuan[i].satuan+'</option>';
                }

                $(select_satuan).html( opt );
                $(select_satuan).removeAttr('disabled');
                $(_tr).find('.jumlah').removeAttr('disabled');
                $(_tr).find('.harga').removeAttr('disabled');
            });
        });
    }, // end - addRow

    removeRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        if ( $(tbody).find('tr').length > 0 ) {
            $(tr).remove();
        }
    }, // end - addRow

	changeTabActive: function(elm) {
        var vhref = $(elm).data('href');
        var edit = $(elm).data('edit');
        // change tab-menu
        $('.nav-tabs').find('a').removeClass('active');
        $('.nav-tabs').find('a').removeClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

        // change tab-content
        $('.tab-pane').removeClass('show');
        $('.tab-pane').removeClass('active');
        $('div#'+vhref).addClass('show');
        $('div#'+vhref).addClass('active');

        if ( vhref == 'action' ) {
            var v_id = $(elm).attr('data-id');

            terima.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/Penerimaan/loadForm',
            data : {
                'id' :  v_id,
                'resubmit' : resubmit
            },
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ showLoading(); },
            success : function(html){
                hideLoading();
                $(dcontent).html(html);
                terima.setting_up();
            },
        });
    }, // end - loadForm

	getLists: function() {
        var dcontent = $('div#riwayat');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err > 0 ) {
            bootbox.alert('Harap lengkapi data terlebih dahulu.');
        } else {
            var tbody = $(dcontent).find('table.tbl_riwayat tbody');

            var params = {
                'start_date': dateSQL( $(dcontent).find('#StartDate').data('DateTimePicker').date() ),
                'end_date': dateSQL( $(dcontent).find('#EndDate').data('DateTimePicker').date() )
            };

            $.ajax({
                url : 'transaksi/Penerimaan/getLists',
                data : {
                    'params' : params
                },
                type : 'GET',
                dataType : 'HTML',
                beforeSend : function(){ showLoading(); },
                success : function(html){
                    hideLoading();
                    $(tbody).html(html);
                },
            });
        }
    }, // end - getLists

    getPo: function(elm) {
        var val = $(elm).select2('val');

        var opt = '<option value="">Pilih PO</option>';
        if ( !empty(val) ) {
            var params = {'kode_gudang': val};

            $('select.po').removeAttr('disabled');

            $.ajax({
                url : 'transaksi/Penerimaan/getPo',
                data : {
                    'params' : params
                },
                type : 'POST',
                dataType : 'JSON',
                beforeSend : function(){ showLoading('Get PO . . .'); },
                success : function(data){
                    hideLoading();

                    if ( data.status == 1 ) {
                        for (var i = 0; i < data.content.length; i++) {
                            opt += '<option value="'+data.content[i].no_po+'" data-supplier="'+data.content[i].supplier+'" data-supplierkode="'+data.content[i].supplier_kode+'">'+data.content[i].tgl_po+' | '+data.content[i].no_po+' | '+data.content[i].supplier+'</option>';
                        }
                    } else {
                        bootbox.alert( data.message );
                    }
                    
                    $('select.po').html( opt );
                    $('select.po').select2().on('select2:select', function (e) {
                        var val = $(this).select2('val');

                        var supplier = e.params.data.element.dataset.supplier;
                        var supplier_kode = e.params.data.element.dataset.supplierkode;

                        $('select.supplier').val(supplier_kode).trigger('change');

                        // $('input.supplier').val( supplier );

                        terima.getPoItem( val );
                    });
                },
            });
        } else {
            $('select.po').attr('disabled', 'disabled');
            $('select.po').html( opt );
            $('select.po').select2();
        }
    }, // end - getPo

    getPoItem: function(no_po) {
        var params = {'no_po': no_po};

        $.ajax({
            url : 'transaksi/Penerimaan/getPoItem',
            data : {
                'params' : params
            },
            type : 'POST',
            dataType : 'JSON',
            beforeSend : function(){ showLoading('Get PO Item . . .'); },
            success : function(data){
                hideLoading();

                if ( data.status == 1 ) {
                    $('table.tbl_detail').find('tbody').html( data.content.html );

                    $.map( $('table.tbl_detail').find('tbody tr'), function(tr) {
                        $(tr).find('select.item').select2().on('select2:select', function (e) {
                            var _tr = $(this).closest('tr');
                            var select_satuan = $(_tr).find('select.satuan');

                            var data = e.params.data.element.dataset;
                            var coa = data.coa;
                            var ket_coa = data.ketcoa;

                            $(_tr).find('td.coa').html( coa+'<br>'+ket_coa );

                            var satuan = JSON.parse( data.satuan );

                            var opt = '<option value="">Pilih Satuan</option>';
                            for (var i = 0; i < satuan.length; i++) {
                                opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'">'+satuan[i].satuan+'</option>';
                            }

                            $(select_satuan).html( opt );
                            $(select_satuan).removeAttr('disabled');
                            $(_tr).find('.jumlah').removeAttr('disabled');
                            $(_tr).find('.harga').removeAttr('disabled');
                        });

                        $(tr).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
                            // $(this).priceFormat(Config[$(this).data('tipe')]);
                            priceFormat( $(this) );
                        });
                    });

                    // terima.setting_up();
                    terima.hitGrandTotal();
                } else {
                    bootbox.alert( data.message );
                }
            },
        });
    }, // end - getPoItem

	save: function() {
		var dcontent = $('#action');
		var err = 0;
		$.map( $(dcontent).find('[data-required=1]'), function(ipt) {
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
			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if ( result ) {
					var detail = $.map( $(dcontent).find('.tbl_detail tbody tr'), function(tr) {
						var _detail = {
                            'item_kode': $(tr).find('.item').select2('val'),
                            'satuan': $(tr).find('.satuan').val(),
							'pengali': $(tr).find('.satuan option:selected').attr('data-pengali'),
							'jumlah_terima': numeral.unformat($(tr).find('input.jumlah').val()),
							'harga': numeral.unformat($(tr).find('input.harga').val())
						};

						return _detail;
					});

					var data = {
						'tgl_terima': dateSQL( $(dcontent).find('#TglTerima').data('DateTimePicker').date() ),
                        'no_faktur': $(dcontent).find('.no_faktur').val(),
                        'nama_pic': $(dcontent).find('.nama_pic').val(),
                        'gudang': $(dcontent).find('.gudang').select2('val'),
                        'supplier': $(dcontent).find('.supplier option:selected').text(),
                        'supplier_kode': $(dcontent).find('.supplier').select2('val'),
                        // 'supplier': $(dcontent).find('.supplier').val(),
						'no_po': $(dcontent).find('.po').select2('val'),
						'detail': detail
					};

					$.ajax({
		                url: 'transaksi/Penerimaan/save',
		                dataType: 'json',
		                type: 'post',
		                data: {
		                	'params': data
		                },
		                beforeSend: function() { showLoading('Proses Simpan . . .'); },
		                success: function(data) {
		                    hideLoading();
		                    if ( data.status == 1 ) {
		                    	terima.hitungStok( data.content.id );
		                    } else {
		                        bootbox.alert(data.message);
		                    };
		                },
		            });
				}
			});
		}
	}, // end - save

    hitungStok: function (kode) {
        var params = {'kode': kode};

        $.ajax({
            url: 'transaksi/Penerimaan/hitungStok',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading('Hitung Stok . . .'); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert( data.message, function () {
                        location.reload();
                    });
                } else {
                    bootbox.alert( data.message );
                }
            }
        });
    }, // end - hitungStok

    hitTotal: function (elm) {
        var tr = $(elm).closest('tr');

        var jumlah = numeral.unformat($(tr).find('.jumlah').val());
        var harga = numeral.unformat($(tr).find('.harga').val());

        var total = harga * jumlah;

        $(tr).find('.total').val( numeral.formatDec(total) );

        terima.hitGrandTotal();
    }, // end - hitTotal

    hitGrandTotal: function() {
        var grand_total = 0;
        $.map( $('tr.data'), function(tr) {
            var total = numeral.unformat($(tr).find('input.total').val());

            grand_total += parseFloat( total );
        });

        $('tfoot td.total').find('b').html( numeral.formatDec(grand_total) );
    }, // end - hitGrandTotal

    // exportExcel : function () {
    //     var _data = '<table border="1">'+$('table.tbl_riwayat').html()+'</table>';

    //     var blob = new Blob([_data], { type: 'application/vnd.ms-excel' });
    //     var downloadUrl = URL.createObjectURL(blob);
    //     var a = document.createElement("a");
    //     a.href = downloadUrl;
    //     a.download = "export-penerimaan-barang.xls";
    //     document.body.appendChild(a);
    //     a.click();
    // }, // end - exportExcel

    exportExcel: function(elm) {
		var dcontent = $('div#riwayat');
		
        var err = 0

        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			var params = {
                'start_date': dateSQL( $(dcontent).find('#StartDate').data('DateTimePicker').date() ),
                'end_date': dateSQL( $(dcontent).find('#EndDate').data('DateTimePicker').date() )
            };

			$.ajax({
	            url: 'transaksi/Penerimaan/excryptParamsExportExcel',
	            data: {
	                'params': params
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();
	                if ( data.status == 1 ) {
	                	window.open('transaksi/Penerimaan/exportExcel/'+data.content.data, 'blank');
	                } else {
	                    bootbox.alert(data.message);
	                }
	            }
	        });
		}
	}, // end - exportExcel
};

terima.start_up();