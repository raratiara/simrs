<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
		<div class="row">
			<label class="col-md-12 col-sm-4 col-xs-4 control-label no-padding-center"><b>INVOICE / TAGIHAN PASIEN</b></label>
		</div>
		<div class="row">
			<label class="col-md-12 col-sm-4 col-xs-4 control-label no-padding-center">No Tagihan : 
			<span class="no_tagihan"></span></label>		
		</div>
		<div class="row">
			<label class="col-md-12 col-sm-4 col-xs-4 control-label no-padding-center">Tanggal : <span class="tgl_tagihan"></span></label>
			<input type="hidden" id="pendaftaran_id" name="pendaftaran_id">
		</div>
	</div>

	<div class="col-md-12 col-sm-12 col-xs-12">
	</div>

	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Nama</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="nama"></span>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">No RM</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="no_rm"></span>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Jenis Kelamin</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="jenis_kelamin"></span>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Tanggal Lahir</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="tgl_lahir"></span>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Dokter</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="dokter"></span>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Unit</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="unit"></span>
			</div>
		</div>
	</div>

	<div class="col-md-12 col-sm-12 col-xs-12">
	</div>

	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-left"; style="text-align:left; font-size: 18px;"><b>Rincian Biaya</b></label>
		</div>
		<div class="row">
		
			<table class="table table-border">
				<thead>
					<tr>
						<td>No</td>
						<td>Deskripsi Layanan</td>
						<td>Jumlah</td>
						<td>Harga Satuan (Rp)</td>
						<td>Subtotal (Rp)</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>Konsultasi Dokter</td>
						<td>1x</td>
						<td><span class="biaya_dokter"></span></td>
						<td><span class="biaya_dokter"></span></td>
					</tr>
					<tr>
						<td>2</td>
						<td>Obat-Obatan</td>
						<td><span class="qty_item_obat"></span> Item</td>
						<td><span class="biaya_obat"></span></td>
						<td><span class="biaya_obat"></span></td>
					</tr>
				
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Total Biaya</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="total_biaya"></span>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Diskon (jika ada)</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="diskon"></span>
			</div>
		</div>
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Total yang harus dibayar</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <span class="total_ygharus_dibayar"></span>
			</div>
		</div>
	</div>

	<div class="col-md-12 col-sm-12 col-xs-12">
	</div>

	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-right">Metode Pembayaran</label>
			<div class="col-md-8 col-sm-8 col-xs-8">
				: <?=$rdometodepembayaran?>
			</div>
		</div>
	</div>
	
	
</div>
									