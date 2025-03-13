<div class="col-xs-12 no-padding header">
	<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right:5px;">
		<div class="col-xs-12 no-padding">
			<label class="control-label">Gudang</label>
		</div>
		<div class="col-xs-12 no-padding">
			<select class="form-control gudang" data-required="1">
				<?php foreach ($gudang as $key => $value): ?>
					<option value="<?php echo $value['kode_gudang']; ?>"><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			</select>
		</div>
	</div>

	<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left:5px;">
		<div class="col-xs-12 no-padding">
			<label class="control-label">Tgl Stok Opname</label>
		</div>
		<div class="col-xs-12 no-padding">
			<div class="input-group date datetimepicker" name="tglStokOpname" id="TglStokOpname">
		        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
		        <span class="input-group-addon">
		            <span class="glyphicon glyphicon-calendar"></span>
		        </span>
		    </div>
		</div>
	</div>

	<div class="col-xs-12 no-padding hide" style="margin-bottom: 5px;">
		<div class="col-xs-12 no-padding">
			<label class="control-label">Group Item</label>
		</div>
		<div class="col-xs-12 no-padding">
			<select class="form-control group_item" multiple="multiple">
				<?php foreach ($group_item as $key => $value): ?>
					<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			</select>
		</div>
	</div>

	<div class="col-xs-12 no-padding">
		<button type="button" class="btn btn-primary col-xs-12 btn-list-item" onclick="so.getListItem(this)"><i class="fa fa-search"></i> Tampilkan Item</button>
	</div>	
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<!-- <div class="col-xs-12 search left-inner-addon pull-right no-padding" style="padding-bottom: 10px;">
	<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_item" placeholder="Search" onkeyup="filter_all(this)">
</div> -->

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered tbl_item" style="margin-bottom: 0px;">
			<thead>
				<tr class="search">
					<td></td>
					<td>
						<div class="col-xs-12 search left-inner-addon pull-right no-padding">
							<i class="fa fa-search" style="padding: 12px;"></i><input class="form-control filter_by_column" type="search" data-table="tbl_item" data-column="group_item" placeholder="Search">
						</div>
					</td>
					<td>
						<div class="col-xs-12 search left-inner-addon pull-right no-padding">
							<i class="fa fa-search" style="padding: 12px;"></i><input class="form-control filter_by_column" type="search" data-table="tbl_item" data-column="item" placeholder="Search">
						</div>
					</td>
					<td colspan="4"></td>
				</tr>
				<tr>
					<th class="col-xs-1">Kode</th>
					<th class="col-xs-1">Group Item</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga Satuan (Rp.)</th>
					<th class="col-xs-1">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="7">Data tidak ditemukan.</td>
				</tr>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="so.save()"><i class="fa fa-save"></i> Simpan</button>
</div>