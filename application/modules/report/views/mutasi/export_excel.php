<style type="text/css">
	.str { mso-number-format:\@; }
	.decimal_number_format { mso-number-format: "\#\,\#\#0\.00"; }
	/* .decimal_number_format4 { mso-number-format: "\#\,\#\#0.0000"; } */
	.number_format { mso-number-format: "\#\,\#\#0"; }
</style>
<div style="width: 100%;">
	<h3>Laporan Penerimaan Barang</h3>
</div>
<div style="width: 100%; font-size: 10pt;">
	<table>
		<tr>
			<td style="width: 5%;">Gudang Asal</td>
			<td style="width: 3%;">: <?php echo strtoupper(implode(", ", $data['gudang_asal'])); ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Gudang Tujuan</td>
			<td style="width: 3%;">: <?php echo strtoupper(implode(", ", $data['gudang_tujuan'])); ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Periode</td>
			<td style="width: 3%;">: <?php echo substr($data['start_date'], 0, 10).' s/d '.substr($data['end_date'], 0, 10); ?></td>
		</tr>
	</table>
</div>
<table border="1">
	<thead>
		<tr>
			<th>Tanggal</th>
			<th>Kode Mutasi</th>
			<th>Asal</th>
			<th>Tujuan</th>
			<th>Nama Item</th>
			<th>COA SAP</th>
			<th>Satuan</th>
			<th>Jumlah</th>
			<th>Harga (Rp.)</th>
			<th>Nilai</th>
		</tr>
	</thead>
	<tbody>
		<?php if ( !empty($data['detail']) && count($data['detail']) > 0 ): ?>
			<?php $grand_total = 0; ?>
			<?php foreach ($data['detail'] as $k_tanggal => $v_tanggal): ?>
				<?php $total_per_tanggal = 0; ?>
				<?php foreach ($v_tanggal['detail'] as $k_kode => $v_kode): ?>
					<?php $total_per_kode = 0; ?>
					<?php foreach ($v_kode['detail'] as $k_det => $v_det): ?>
						<?php $total = $v_det['jumlah'] * $v_det['harga']; ?>
						<?php $grand_total += $total; ?>
						<?php $total_per_tanggal += $total; ?>
						<?php $total_per_kode += $total; ?>
						<tr>
							<td><?php echo $v_det['tgl_mutasi']; ?></td>
							<td><?php echo $v_det['kode_mutasi']; ?></td>
							<td><?php echo $v_det['nama_gudang_asal']; ?></td>
							<td><?php echo $v_det['nama_gudang_tujuan']; ?></td>
							<td><?php echo $v_det['nama_item']; ?></td>
							<td><?php echo $v_det['coa']; ?></td>
							<td><?php echo $v_det['satuan']; ?></td>
							<td class="decimal_number_format" align="right"><?php echo number_format((float)$v_det['jumlah'], 2, ',', ''); ?></td>
							<!-- <td class="decimal_number_format4" align="right"><?php echo ($v_det['harga']); ?></td> -->
							<td class="decimal_number_format" align="right"><?php echo number_format((float)$v_det['harga'], 2, ',', ''); ?></td>
							<td class="decimal_number_format" align="right"><?php echo number_format((float)$total, 2, ',', ''); ?></td>
						</tr>
					<?php endforeach ?>
					<tr>
						<td align="right" colspan="9"><b>TOTAL</b></td>
						<td class="decimal_number_format" align="right"><b><?php echo number_format((float)$total_per_kode, 2, ',', ''); ?></b></td>
					</tr>
				<?php endforeach ?>
				<tr>
					<td align="right" colspan="9"><b>TOTAL PER TANGGAL - <?php echo tglIndonesia($v_det['tgl_mutasi'], '-', ' '); ?></b></td>
					<td class="decimal_number_format" align="right"><b><?php echo number_format((float)$total_per_tanggal, 2, ',', ''); ?></b></td>
				</tr>
			<?php endforeach ?>
			<tr>
				<td align="right" colspan="9"><b>TOTAL</b></td>
				<td class="decimal_number_format" align="right"><b><?php echo number_format((float)$grand_total, 2, ',', ''); ?></b></td>
			</tr>
		<?php else: ?>
			<tr>
				<td colspan="10">Data tidak ditemukan.</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>