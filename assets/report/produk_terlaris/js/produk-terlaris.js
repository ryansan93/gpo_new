var pt = {
	startUp: function () {
		pt.settingUp();
	}, // end - startUp

	settingUp: function() {
		$("#StartDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });
        $("#EndDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        $('.filter').select2();
        $('.branch').select2();
        $('.jumlah').select2();
	}, // end - settingUp

	getLists: function(elm) {
		var err = 0

		$.map( $('[data-required=1]'), function(ipt) {
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
			var params = {
				'start_date': dateSQL($('#StartDate').data('DateTimePicker').date()),
				'end_date': dateSQL($('#EndDate').data('DateTimePicker').date()),
				'filter': $('.filter').select2().val(),
				'branch': $('.branch').select2().val(),
				'jumlah': $('.jumlah').select2().val()
			};

			$.ajax({
	            url: 'report/ProdukTerlaris/getLists',
	            data: {
	                'params': params
	            },
	            type: 'GET',
	            dataType: 'HTML',
	            beforeSend: function() { showLoading(); },
	            success: function(html) {
	                hideLoading();

	                $('.report').html( html );
	            }
	        });
		}
	}, // end - getLists

	// export_excel : function () {
	// 	var _data = '<table border="1">'+$('table.tbl_report').html()+'</table>';

    //     var blob = new Blob([_data], { type: 'application/vnd.ms-excel' });
    //     var downloadUrl = URL.createObjectURL(blob);
    //     var a = document.createElement("a");
    //     a.href = downloadUrl;
    //     a.download = "export-performance-produk-dan-member.xls";
    //     document.body.appendChild(a);
    //     a.click();
	// }, // end - export_excel

	exportExcel: function(elm) {
		var err = 0

		$.map( $('[data-required=1]'), function(ipt) {
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
			var params = {
				'start_date': dateSQL($('#StartDate').data('DateTimePicker').date()),
				'end_date': dateSQL($('#EndDate').data('DateTimePicker').date()),
				'filter': $('.filter').select2().val(),
				'branch': $('.branch').select2().val(),
				'jumlah': $('.jumlah').select2().val()
			};

			$.ajax({
	            url: 'report/ProdukTerlaris/excryptParamsExportExcel',
	            data: {
	                'params': params
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();
	                if ( data.status == 1 ) {
	                	window.open('report/ProdukTerlaris/exportExcel/'+data.content.data, 'blank');
	                } else {
	                    bootbox.alert(data.message);
	                }
	            }
	        });
		}
	}, // end - exportExcel
};

pt.startUp();