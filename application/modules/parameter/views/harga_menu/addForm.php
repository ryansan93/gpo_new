<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Harga Menu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Branch</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-8 form-control branch" data-required="1">
								<option>-- Pilih Branch --</option>
								<?php if ( !empty($branch) ): ?>
									<?php foreach ($branch as $key => $val): ?>
										<option value="<?php echo $val['kode_branch']; ?>"><?php echo strtoupper($val['kode_branch']).' | '.strtoupper($val['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Menu</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-8 form-control menu" data-required="1">
								<option>-- Pilih Menu --</option>
								<?php if ( !empty($menu) ): ?>
									<?php foreach ($menu as $key => $val): ?>
										<option value="<?php echo $val['kode_menu']; ?>" data-branch="<?php echo $val['branch_kode']; ?>"><?php echo strtoupper($val['jenis']['nama']).' | '.strtoupper($val['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
					</tr>
					<!-- <tr>
						<td class="col-sm-2">				
							<label class="control-label">Jenis Pesanan</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-6 form-control jenis_pesanan" data-required="1">
								<option>-- Pilih Jenis Pesanan --</option>
								<?php if ( !empty($jenis_pesanan) ): ?>
									<?php foreach ($jenis_pesanan as $key => $val): ?>
										<option value="<?php echo $val['kode']; ?>"><?php echo strtoupper($val['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
					</tr> -->
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Tgl Berlaku</label>
						</td>
						<td class="col-sm-10">
							<div class="col-sm-3 input-group date datetimepicker" name="tglBerlaku" id="TglBerlaku">
						        <input type="text" class="form-control text-center" placeholder="Berlaku" data-required="1" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</td>
					</tr>
					<!-- <tr>
						<td class="col-sm-2">				
							<label class="control-label">Harga</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-3 form-control text-right harga uppercase" placeholder="Harga" data-required="1" data-tipe="decimal">
						</td>
					</tr> -->
				</tbody>
			</table>

			<small>
				<table class="table table-bordered tbl_jenis_pesanan" style="margin-bottom: 0px;">
					<thead>
						<tr>
							<th class="col-xs-8">Jenis Pesanan</th>
							<th class="col-xs-4">Harga</th>
						</tr>
					</thead>
					<tbody>
						<?php if ( !empty($jenis_pesanan) ): ?>
							<?php foreach ($jenis_pesanan as $key => $val): ?>
								<tr class="data">
									<td class="kode" data-val="<?php echo $val['kode']; ?>"><?php echo strtoupper($val['nama']); ?></td>
									<td>
										<input type="text" class="form-control text-right harga" data-required="1" data-tipe="decimal" placeholder="Harga" maxlength="14">
									</td>
								</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr>
								<td colspan="2">Data jenis pesanan tidak ditemukan.</td>
							</tr>
						<?php endif ?>
					</tbody>
				</table>
			</small>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="hm.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>