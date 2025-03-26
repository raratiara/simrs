									<div class="row">
										<div class="col-md-12 col-sm-12">
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Nomor RM </label>
												<div class="col-md-4">
													<?=$txtnorm;?>
													<input type="hidden" id="id_pendaftaran" name="id_pendaftaran">
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Tgl Lahir</label>
												<div class="col-md-4">
													<?=$txttgllahir;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Nama Pasien </label>
												<div class="col-md-4">
													<?=$txtnamapasien;?>
												</div>
											</div>


											
		<div class="row">										
			<label class="col-md-2 col-sm-4 col-xs-4 control-label no-padding-left"; style="text-align:left; font-size: 18px;"><b>Resep</b></label>
		</div>
		<div class="row">
			<!--  -->
			<table class="table table-border">
				<thead>
					<tr>
						<td>No</td>
						<td>Kode</td>
						<td>Nama</td>
						<td>Qty</td>
						<td>Satuan</td>
					</tr>
				</thead>
				<tbody>
					<?php
					if($data_resep != ''){
						foreach ($data_resep as $row) {
							?>
							<tr>
								<td>1</td>
								<td><?=$row->kode_obat?></td>
								<td><?=$row->nama_obat?></td>
								<td><?=$row->qty?></td>
								<td><?=$row->satuan_id?></td>
							</tr>
							<?php
						}
					}else{
						?>
						<tr>
							<td>1</td>
							<td>Konsultasi Dokter</td>
							<td>1x</td>
							<td><span class="biaya_dokter"></span></td>
							<td><span class="biaya_dokter"></span></td>
						</tr>
						<?php
					}
					?>
					
					
				</tbody>
			</table>
		</div>
	


											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Status Lengkap</label>
												<div class="col-md-6">
													<?=$selstatus;?>
												</div>
											</div>
										</div>
									</div>
									
									
									