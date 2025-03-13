<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $grand_total = 0; $grand_jumlah = 0; $grand_total_ppn = 0;  $grand_total_service_charge = 0; $grand_total_after_ppn = 0; ?>
	<?php foreach ($data as $k_shift => $v_shift): ?>
		<tr class="shift">
			<th colspan="9" style="background-color: #abf5bf;"><?php echo strtoupper(tglIndonesia($v_shift['nama'], '-', ' ')); ?></th>
		</tr>

		<?php $total_shift = 0; ?>
		<?php $total_ppn_shift = 0; ?>
		<?php $total_service_charge_shift = 0; ?>
		<?php $total_after_ppn_shift = 0; ?>
		<?php $jumlah_shift = 0; ?>
		<?php foreach ($v_shift['detail'] as $k_tanggal => $v_tanggal): ?>
			<tr class="tanggal">
				<th colspan="9" style="background-color: #dedede;"><?php echo strtoupper(tglIndonesia($v_tanggal['tanggal'], '-', ' ')); ?></th>
			</tr>
			<?php $total = 0; ?>
			<?php $total_ppn = 0; ?>
			<?php $total_service_charge = 0; ?>
			<?php $total_after_ppn = 0; ?>
			<?php $jumlah = 0; ?>
			<?php foreach ($v_tanggal['kasir'] as $k_kasir => $v_kasir): ?>
				<tr class="kasir">
					<th colspan="9" style="background-color: #99ccff;"><?php echo strtoupper($v_kasir['nama_kasir']); ?></th>
				</tr>
				<?php $total_kasir_non_ppn = 0; ?>
				<?php $total_kasir_ppn = 0; ?>
				<?php $total_kasir_service_charge = 0; ?>
				<?php $total_kasir_after_ppn = 0; ?>
				<?php $jumlah_kasir = 0; ?>
				<?php foreach ($v_kasir['faktur'] as $k_faktur => $v_faktur): ?>
					<?php $idx_faktur = 0; ?>
					<?php foreach ($v_faktur['menu'] as $k_menu => $v_menu): ?>
						<tr>
							<?php if ( $idx_faktur == 0 ): ?>
								<td rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo strtoupper($v_faktur['kode_faktur']); ?></td>
								<td rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo strtoupper($v_faktur['member']); ?></td>
							<?php endif ?>
							<td><?php echo strtoupper($v_menu['nama']); ?></td>
							<td class="text-right"><?php echo angkaRibuan($v_menu['jumlah']); ?></td>
							<td class="text-right"><?php echo angkaRibuan($v_menu['harga']); ?></td>
							<?php if ( $idx_faktur == 0 ): ?>
								<td class="text-right" rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo angkaDecimal($v_faktur['total']); ?></td>
								<td class="text-right" rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo angkaDecimal($v_faktur['ppn']); ?></td>
								<td class="text-right" rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo angkaDecimal($v_faktur['service_charge']); ?></td>
								<td class="text-right" rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo angkaDecimal($v_faktur['grand_total']); ?></td>
							<?php endif ?>

							<?php $jumlah_shift += $v_menu['jumlah']; $jumlah += $v_menu['jumlah']; $grand_jumlah += $v_menu['jumlah']; ?>
							<?php $jumlah_kasir += $v_menu['jumlah']; ?>
						</tr>
						<?php $idx_faktur++; ?>
					<?php endforeach ?>
					<?php $total_shift += $v_faktur['total']; ?>
					<?php $total_ppn_shift += $v_faktur['ppn']; ?>
					<?php $total_service_charge_shift += $v_faktur['service_charge']; ?>
					<?php $total_after_ppn_shift += $v_faktur['grand_total']; ?>

					<?php $total += $v_faktur['total']; ?>
					<?php $total_ppn += $v_faktur['ppn']; ?>
					<?php $total_service_charge += $v_faktur['service_charge']; ?>
					<?php $total_after_ppn += $v_faktur['grand_total']; ?>

					<?php $total_kasir_non_ppn += $v_faktur['total']; ?>
					<?php $total_kasir_ppn += $v_faktur['ppn']; ?>
					<?php $total_kasir_service_charge += $v_faktur['service_charge']; ?>
					<?php $total_kasir_after_ppn += $v_faktur['grand_total']; ?>

					<?php $grand_total += $v_faktur['total']; ?>
					<?php $grand_total_ppn += $v_faktur['ppn']; ?>
					<?php $grand_total_service_charge += $v_faktur['service_charge']; ?>
					<?php $grand_total_after_ppn += $v_faktur['grand_total']; ?>
				<?php endforeach ?>
				<tr class="total">
					<td class="text-right" colspan="3"><b>TOTAL PER KASIR - <?php echo strtoupper($v_kasir['nama_kasir']); ?></b></td>
					<td class="text-right"><b><?php echo angkaRibuan($jumlah_kasir); ?></b></td>
					<td class="text-right"></td>
					<td class="text-right"><b><?php echo angkaDecimal($total_kasir_non_ppn); ?></b></td>
					<td class="text-right"><b><?php echo angkaDecimal($total_kasir_ppn); ?></b></td>
					<td class="text-right"><b><?php echo angkaDecimal($total_kasir_service_charge); ?></b></td>
					<td class="text-right"><b><?php echo angkaDecimal($total_kasir_after_ppn); ?></b></td>
				</tr>
			<?php endforeach ?>
			<tr class="total">
				<td class="text-right" colspan="3"><b>TOTAL PER TANGGAL - <?php echo strtoupper(tglIndonesia($v_tanggal['tanggal'], '-', ' ')); ?></b></td>
				<td class="text-right"><b><?php echo angkaRibuan($jumlah); ?></b></td>
				<td class="text-right"></td>
				<td class="text-right"><b><?php echo angkaDecimal($total); ?></b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_ppn); ?></b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_service_charge); ?></b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_after_ppn); ?></b></td>
			</tr>
		<?php endforeach ?>
		<tr class="total">
			<td class="text-right" colspan="3"><b>TOTAL PER SHIFT - <?php echo strtoupper(tglIndonesia($v_shift['nama'], '-', ' ')); ?></b></td>
			<td class="text-right"><b><?php echo angkaRibuan($jumlah_shift); ?></b></td>
			<td class="text-right"></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_shift); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_ppn_shift); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_service_charge_shift); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_after_ppn_shift); ?></b></td>
		</tr>
	<?php endforeach ?>
	<tr class="grand_total">
		<td class="text-right" colspan="3"><b>GRAND TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaRibuan($grand_jumlah); ?></b></td>
		<td class="text-right"></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total_ppn); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total_service_charge); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total_after_ppn); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="9">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>