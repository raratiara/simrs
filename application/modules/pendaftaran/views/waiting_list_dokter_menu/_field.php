									<div class="row">
										<div class="col-md-12 col-sm-12">
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Nomor RM </label>
												<div class="col-md-4">
													<?=$txtnorm;?>
													<input type="hidden" id="id_pendaftaran" name="id_pendaftaran">
													<input type="hidden" id="status" name="status">
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
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Tinggi Badan</label>
												<div class="col-md-8">
													<?=$txttinggibadan;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Suhu Tubuh</label>
												<div class="col-md-8">
													<?=$txtsuhutubuh;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Tekanan Darah</label>
												<div class="col-md-8">
													<?=$txttekanandarah;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Saturasi</label>
												<div class="col-md-8">
													<?=$txtsaturasi;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Berat Badan </label>
												<div class="col-md-8">
													<?=$txtberatbadan;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Denyut Nadi</label>
												<div class="col-md-8">
													<?=$txtdenyutnadi;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Frekuensi Napas</label>
												<div class="col-md-8">
													<?=$txtfrekuensinapas;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Tingkat Nyeri</label>
												<div class="col-md-8">
													<?=$txttingkatnyeri;?>
												</div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12">
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Wawancara Medis</label>
												<div class="col-md-4">
													<?=$txtwawancara;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Diagnosa</label>
												<div class="col-md-4">
													<?=$txtdiagnosa;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Pemeriksaan Penunjang</label>
												<div class="col-md-4">
													<?=$rdopemeriksaanpenunjang;?>
												</div>
											</div>
										</div>
										
									</div>


<div class="row ca">
    <div class="col-md-12">
		<div class="portlet box green">
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-cubes"></i>Resep </div>
				<div class="tools">
					<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addcarow" value="Add Row" />
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-scrollable tablesaw-cont">
				<table class="table table-striped table-bordered table-hover ca-list tablesaw tablesaw-stack" data-tablesaw-mode="stack" id="tblResep">
					<thead>
						<tr>
							<th scope="col">No</th>
							<th scope="col">Kode</th>
							<th scope="col">Nama</th>
							<th scope="col" style="text-align:right;">Qty</th>
							<th scope="col">Satuan</th>
							<th scope="col"></th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
					<tfoot>
					</tfoot>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>
									
									
									