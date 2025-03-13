<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Nama PiC</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control nama_pic uppercase" placeholder="Nama PiC" data-required="1">
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Asal</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control asal" data-required="1">
			<option value="">Pilih Gudang</option>
			<?php if ( !empty($gudang) ): ?>
				<?php foreach ($gudang as $key => $value): ?>
					<option value="<?php echo $value['kode_gudang']; ?>"><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tujuan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control tujuan" data-required="1">
			<option value="">Pilih Gudang</option>
			<?php if ( !empty($gudang) ): ?>
				<?php foreach ($gudang as $key => $value): ?>
					<option value="<?php echo $value['kode_gudang']; ?>"><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Mutasi</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglMutasi" id="TglMutasi">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">No. SJ</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control no_sj uppercase" placeholder="No. SJ">
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Lampiran SJ</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding attachment" style="margin-top: 0px;">
			<a name="dokumen" class="text-right hide" target="_blank" style="padding-right: 10px;"><i class="fa fa-file"></i></a>
            <label class="" style="margin-bottom: 0px;">
                <input style="display: none;" placeholder="Dokumen" class="file_lampiran no-check" type="file" onchange="mutasi.showNameFile(this)" data-name="name" data-allowtypes="doc|pdf|docx|jpg|jpeg|png">
                <i class="glyphicon glyphicon-paperclip cursor-p" title="Attachment"></i> 
            </label>
		</div>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-2">COA SAP</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga Satuan (Rp.)</th>
					<th class="col-xs-1">Total</th>
					<th class="col-xs-1">Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select class="form-control item" data-required="1" disabled="disabled">
							<option value="">-- Pilih Item --</option>
							<?php if ( !empty($item) ): ?>
								<?php foreach ($item as $k_item => $v_item): ?>
									<option value="<?php echo $v_item['kode']; ?>" data-satuan='<?php echo json_encode($v_item['satuan']); ?>' data-coa='<?php echo $v_item['group']['coa']; ?>' data-ketcoa='<?php echo $v_item['group']['ket_coa']; ?>'><?php echo strtoupper($v_item['nama']); ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
					</td>
					<td class="coa">
						-
					</td>
					<td>
						<select class="form-control satuan" data-required="1" disabled>
							<option value="">Pilih Satuan</option>
						</select>
					</td>
					<td>
						<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal" data-required="1" maxlength="10" onblur="mutasi.hitTotal(this)">
					</td>
					<td class="harga text-right">0</td>
					<td class="total text-right">0</td>
					<td>
						<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
							<button type="button" class="btn btn-danger" onclick="mutasi.removeRow(this);"><i class="fa fa-minus"></i></button>
						</div>
						<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
							<button type="button" class="btn btn-primary" onclick="mutasi.addRow(this);"><i class="fa fa-plus"></i></button>
						</div>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" class="text-right"><b>TOTAL</b></td>
					<td class="text-right grand_total"><b>0</b></td>
				</tr>
			</tfoot>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Keterangan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<textarea class="form-control keterangan"></textarea>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="mutasi.save()"><i class="fa fa-save"></i> Simpan</button>
</div>