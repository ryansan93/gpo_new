<style type="text/css">
	.str { mso-number-format:\@; }
	.decimal_number_format { mso-number-format: "\#\,\#\#0.00"; }
	.number_format { mso-number-format: "\#\,\#\#0"; }
</style>
<div style="width: 100%;">
	<h3>Laporan Hutang Pelanggan</h3>
</div>
<div style="width: 100%; font-size: 10pt;">
	<table>
		<!-- <tr>
			<td style="width: 5%;">Branch</td>
			<td style="width: 3%;">:</td>
			<td><?php echo $branch; ?></td>
		</tr> -->
		<tr>
			<td style="width: 5%;">Periode</td>
			<td style="width: 3%;">:</td>
			<td><?php echo tglIndonesia($start_date, '-', ' '); ?> s/d <?php echo tglIndonesia($end_date, '-', ' '); ?></td>
		</tr>
	</table>
</div>
<table border="1">
	<thead>
		<tr>
			<th class="col-xs-1">Tgl Faktur</th>
			<th class="col-xs-1">Kasir</th>
			<th class="col-xs-1">Kode Faktur</th>
			<th class="col-xs-1">Group Member</th>
			<th class="col-xs-1">Member</th>
			<th class="col-xs-1">Hutang</th>
			<th class="col-xs-1">Bayar</th>
			<th class="col-xs-2">Remark</th>
			<th class="col-xs-1">Jenis Bayar</th>
			<th class="col-xs-1">Tanggal Bayar</th>
			<th class="col-xs-1">Bayar</th>
		</tr>
	</thead>
	<tbody>
		<?php if ( !empty($data) && count($data) > 0 ): ?>
			<?php $total_hutang = 0; $total_bayar = 0; ?>
			<?php foreach ($data as $k_data => $v_data): ?>
				<?php if ( !empty($v_data['jenis_bayar']) ): ?>
					<?php foreach ($v_data['jenis_bayar'] as $k_jb => $v_jb): ?>
						<tr class="search">
							<td class="str"><?php echo substr($v_data['tgl_pesan'], 0, 10); ?></td>
							<td class="str"><?php echo $v_data['nama_kasir']; ?></td>
							<td class="str"><?php echo $v_data['faktur_kode']; ?></td>
							<td class="str"><?php echo !empty($v_data['member_group']) ? $v_data['member_group'] : '-'; ?></td>
							<td class="str"><?php echo $v_data['member']; ?></td>
							<td class="decimal_number_format"><?php echo ($v_data['hutang']); ?></td>
							<td class="decimal_number_format"><?php echo ($v_data['bayar']); ?></td>
							<td class="str"><?php echo !empty($v_data['remark']) ? $v_data['remark'] : '-'; ?></td>
							<td class="str"><?php echo !empty($v_jb['jenis_bayar']) ? $v_jb['jenis_bayar'] : '-'; ?></td>
							<td class="str"><?php echo !empty($v_jb['tgl_bayar']) ? substr($v_jb['tgl_bayar'], 0, 10) : '-'; ?></td>
							<td class="decimal_number_format"><?php echo ((float) $v_jb['nominal']); ?></td>
						</tr>
					<?php endforeach ?>
				<?php else: ?>
					<tr class="search">
						<td class="str"><?php echo substr($v_data['tgl_pesan'], 0, 10); ?></td>
						<td class="str"><?php echo $v_data['nama_kasir']; ?></td>
						<td class="str"><?php echo $v_data['faktur_kode']; ?></td>
						<td class="str"><?php echo !empty($v_data['member_group']) ? $v_data['member_group'] : '-'; ?></td>
						<td class="str"><?php echo $v_data['member']; ?></td>
						<td class="decimal_number_format"><?php echo ($v_data['hutang']); ?></td>
						<td class="decimal_number_format"><?php echo ($v_data['bayar']); ?></td>
						<td class="str"><?php echo !empty($v_data['remark']) ? $v_data['remark'] : '-'; ?></td>
						<td class="str"><?php echo '-'; ?></td>
						<td class="str"><?php echo '-'; ?></td>
						<td class="decimal_number_format"><?php echo 0; ?></td>
					</tr>
				<?php endif ?>
				<?php 
					$total_hutang += $v_data['hutang']; 
					$total_bayar += $v_data['bayar']; 
				?>
			<?php endforeach ?>
			<tr>
				<td class="text-right str" colspan="5"><b>TOTAL</b></td>
				<td class="decimal_number_format"><b><?php echo ($total_hutang); ?></b></td>
				<td class="decimal_number_format"><b><?php echo ($total_bayar); ?></b></td>
				<td class="text-right"></td>
				<td class="text-right"></td>
				<td class="text-right"></td>
				<td class="decimal_number_format"><b><?php echo ($total_bayar); ?></b></td>
			</tr>
		<?php else: ?>
			<tr>
				<td colspan="8">Data tidak ditemukan.</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>