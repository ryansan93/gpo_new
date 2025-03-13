<style type="text/css">
	@media print {
	    html, body {
	        height: 99%;
	        width: 99%;
	    }
	}

	@page { 
		/*size: 8.3in 5.7in;*/
		size: A5 landscape;
		margin: 0em 2em 0em 2em; 
	}

	div.contain {
		font-size: 9pt;
	}

	div.page-break {
		page-break-after: always;
	}

	div.page-break-avoid {
		page-break-after: auto;
	}

	p {
		margin: 0px;
	}

	ol { 
		counter-reset: item;
		margin: 0px;
		vertical-align: top;
	}
	li { 
		display: block; 
		margin: 0px;
		padding: 0px;
		vertical-align: top;
	}
	li:before { 
		content: counters(item, ".") ". ";
		counter-increment: item;
		vertical-align: top;
	}

	table.border-field td, table.border-field th {
		border: 1px solid;
		border-collapse: collapse;
		padding-left: 3px;
		padding-right: 3px;
		padding-top: 3px;
		padding-bottom: 3px;
	}

	table {
		border-collapse: collapse;
	}

	.text-center {
		text-align: center;
	}

	.text-right {
		text-align: right;
	}

	.text-left {
		text-align: left;
	}

	.top td {
		border-top: 1px solid black;
	}

	.bottom td {
		border-bottom: 1px solid black;
	}

	td.kiri {
		border-left: 1px solid black;
		padding-left: 3px;
		padding-right: 3px;
	}

	td.kanan {
		border-right: 1px solid black;
		padding-left: 3px;
		padding-right: 3px;
	}

	/*@page{
		margin: 1em 2em;
	}*/

	.col-xs-1 {
		width: 8.33333333%;
	}
	.col-xs-2 {
		width: 16.66666667%;
	}
	.col-xs-3 {
		width: 25%;
	}
	.col-xs-4 {
		width: 33.33333333%;
	}
	.col-xs-5 {
		width: 41.66666667%;
	}
	.col-xs-6 {
		width: 50%;
	}
	.col-xs-7 {
		width: 58.33333333%;
	}
	.col-xs-8 {
		width: 66.66666667%;
	}
	.col-xs-9 {
		width: 75%;
	}
	.col-xs-10 {
		width: 83.33333333%;
	}
	.col-xs-11 {
		width: 91.66666667%;
	}
	.col-xs-12 {
		width: 100%;
	}
</style>

<?php 
	$print_tax = 1;
	$jml_baris = count($data['detail']);
	if ( $data['tax'] > 0 ) {
		$jml_baris += 1;
		$print_tax = 0;
	}
	$jumlah_page = (isset($data['detail']) && !empty($data['detail'])) ? ceil($jml_baris / 12) : 0; 

	// $jumlah_page = ceil(25 / 12); 
?>
<?php $jumlah_cetak = 1; $grand_total = 0; ?>
<?php for ($i=0; $i < $jumlah_page; $i++) { ?>
	<?php
		$cls_page_break = "page-break";
		if ( $jumlah_cetak == $jumlah_page ) {
			$cls_page_break = "page-break-avoid";
		}
	?>
	<div class="contain <?php echo $cls_page_break; ?>" style="width: 100%; padding-top: 1em;">
		<div style="display: inline; margin: 0px; padding: 0px;">
			<div style="display: inline-block; text-align: left; width: 65.5%;">
				<label class="col-xs-12" style="font-size: 18px; display: inline-block; margin-bottom: 10px;"><b><?php echo $data['bagian']; ?></b></label>
				<br>
				<label class="col-xs-12" style="display: inline-block; border: 1px solid black; padding: 5px; height: 30px;"><?php echo strtoupper($data['supplier']); ?></label>
			</div>
			<div class="col-xs-1" style="display: inline-block; text-align: left; margin: 0px; padding: 0px;">&nbsp;</div>
			<div class="col-xs-3" style="display: inline-block; text-align: left;">
				<label class="col-xs-12" style="font-size: 18px; display: inline-block; margin-bottom: 10px; text-decoration: underline"><b>PURCHACE ORDER</b></label>
				<br>
				<label class="col-xs-12" style="display: inline;">
					<label class="col-xs-3" style="display: inline-block;">NO. PO</label>
					<label class="col-xs-9" style="display: inline-block;"><?php echo ' : '.strtoupper($data['no_po']); ?></label>
					<?php
						$tanggal = substr($data['tgl_po'], 8, 2);
						$bulan = substr($data['tgl_po'], 5, 2);
						$tahun = substr($data['tgl_po'], 0, 4);

						$tgl_po = $tanggal.'-'.$bulan.'-'.$tahun;
					?>
					<label class="col-xs-3" style="display: inline-block;">DATE</label>
					<label class="col-xs-9" style="display: inline-block;"><?php echo ' : '.strtoupper($tgl_po); ?></label>
				</label>
			</div>
			<div class="col-xs-12" style="display: inline-block; text-align: left;">&nbsp;</div>
			<br>
			<div class="col-xs-12" style="display: inline-block; text-align: left;">
				<table class="border-field" style="width: 100%;">
					<thead>
						<tr>
							<th colspan="3">
								<div class="col-xs-12" style="display: inline-block; text-align: left;">
									<label class="col-xs-12" style="display: inline-block; margin-bottom: 10px;"><b>DATE REQUIRED</b></label>
								</div>
							</th>
							<th colspan="3" style="text-align: left;">
								<label class="col-xs-11" style="display: inline-block; margin-bottom: 10px;"><b>TERMS OF PAYMENT</b></label>
							</th>
						</tr>
						<tr>
							<th style="width: 10%;">ITEM NO</th>
							<th style="width: 10%;">QTY</th>
							<th style="width: 10%;">UNIT</th>
							<th style="width: 35%;">DESCRIPTION</th>
							<th style="width: 15%;">UNIT PRICE</th>
							<th style="width: 20%;">AMOUNT</th>
						</tr>
					</thead>
					<tbody>
						<?php $total = 0; ?>
						<?php for ($j=1; $j <= 12; $j++) { ?>
							<?php $idx = (($i*12)+$j) - 1; ?>
							<?php if ( isset($data['detail'][ $idx ]) ): ?>
								<tr>
									<td align="center"><?php echo $j; ?></td>
									<td align="right"><?php echo angkaDecimal($data['detail'][ $idx ]['jumlah']); ?></td>
									<td align="center"><?php echo $data['detail'][ $idx ]['satuan']; ?></td>
									<td><?php echo $data['detail'][ $idx ]['item']['nama']; ?></td>
									<td align="right"><?php echo angkaDecimal($data['detail'][ $idx ]['harga']); ?></td>
									<td align="right"><?php echo angkaDecimal($data['detail'][ $idx ]['harga'] * $data['detail'][ $idx ]['jumlah']); ?></td>
								</tr>
								<?php 
									$total += $data['detail'][ $idx ]['harga'] * $data['detail'][ $idx ]['jumlah']; 
									$grand_total += $data['detail'][ $idx ]['harga'] * $data['detail'][ $idx ]['jumlah'];
								?>
							<?php else: ?>
								<?php if ( $print_tax == 0 ): ?>
									<?php if ( $data['tax'] > 0 ) { ?>
										<?php
											$tax_nilai = $grand_total * ($data['tax']/100);
											$grand_total += $tax_nilai;

											$print_tax = 1;
										?>
										<tr>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>** PURCHASE TAX **</td>
											<td align="right"><?php echo (is_numeric( $data['tax'] ) && floor( $data['tax'] ) != $data['tax']) ? angkaDecimal($data['tax']) : angkaRibuan($data['tax']).'%'; ?></td>
											<td align="right"><?php echo angkaDecimal($tax_nilai); ?></td>
										</tr>
									<?php } ?>
								<?php else: ?>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								<?php endif ?>
							<?php endif ?>
						<?php } ?>
						<tr>
							<td colspan="5" style="text-align: right;"><b>TOTAL</b></td>
							<td align="right"><?php echo angkaDecimal($grand_total); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br>
			<div style="display: inline-block; text-align: left; margin-top: 5px;">
				<label style="display: inline-block; font-size: 8px; width: 50%;">
					<label class="col-xs-12" style="display: inline-block;"><b>Important Instruction :</b></label><br>
					<label class="col-xs-12" style="display: inline-block;">
						<ol style="padding-left: 0px;">
							<li><label style="display: inline-block; width: 97%;">Goods should be delivered to the above mentioned address between 8.30 a.m and 3.30 p.m<br>along with 2 (two) copies of invoice and 2 (two) copies of delivery note.</label></li>
							<li><label style="display: inline-block; width: 97%;">The red copy of this order must be returned to us with your signature for order acceptance.</label></li>
							<li><label style="display: inline-block; width: 97%;">Please show the purchase order number on all packages, invoice and delivery notes referring<br>to this order.</label></li>
						</ol>
					</label>
				</label>
				<label style="display: inline-block; width: 49%;">
					<label style="display: inline-block; width: 40%; text-align: center;">
						<label class="col-xs-12" style="display: inline-block;">&nbsp;</label>
						<br>
						<br>
						<br>
						<br>
						<label class="col-xs-12" style="display: inline-block;">PURCHASING DEPT.</label>
					</label>
					<label style="display: inline-block; width: 60%; text-align: center;">
						<label class="col-xs-12" style="display: inline-block;"><?php echo $data['gudang']['branch']['nama']; ?></label>
						<br>
						<br>
						<br>
						<br>
						<label class="col-xs-12" style="display: inline-block;">GENERAL MANAGER</label>
					</label>
				</label>
			</div>
		</div>
	</div>
	<?php $jumlah_cetak++; ?>
<?php } ?>