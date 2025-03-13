<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<?php
			$bg_color = 'none';
			if ( $v_data['g_status'] == getStatus('submit') ) {
				$bg_color = '#9698ff';
			}
		?>

		<tr class="cursor-p" onclick="mutasi.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode_mutasi']; ?>" data-edit="" style="background-color: <?php echo $bg_color; ?>">
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_mutasi'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['kode_mutasi']; ?></td>
			<td><?php echo strtoupper($v_data['nama_pic']); ?></td>
			<td><?php echo strtoupper($v_data['nama_gudang_asal']); ?></td>
			<td><?php echo strtoupper($v_data['nama_gudang_tujuan']); ?></td>
			<td>
				<?php
					if ( !empty($v_data['list_coa']) ) {
						$idx = 0;
						foreach ($v_data['list_coa'] as $key => $value) {
							if ( $idx == 0 ) {
								echo $value['coa'];
							} else {
								echo '<br>'.$value['coa'];
							}

							$idx++;
						}
					} else {
						echo '-';
					}
				?>
			</td>
			<td>
				<?php
					if ( !empty($v_data['list_coa']) ) {
						$idx = 0;
						foreach ($v_data['list_coa'] as $key => $value) {
							if ( $idx == 0 ) {
								echo $value['ket_coa'];
							} else {
								echo '<br>'.$value['ket_coa'];
							}

							$idx++;
						}
					} else {
						echo '-';
					}
				?>
			</td>
			<td><?php echo ($v_data['g_status'] == getStatus('submit')) ? 'BELUM' : 'SUDAH'; ?></td>
			<td class="text-right">
				<?php echo angkaDecimal($v_data['total']); ?>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="8">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>