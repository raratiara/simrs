									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No ST </label>
												<div class="col-md-8">
													<?=$txtsurat;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Tanggal</label>
												<div class="col-md-8">
													<?=$txtdatesurat;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Penanda Tangan</label>
												<div class="col-md-8">
													<?=$seloTtd;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Jabatan</label>
												<div class="col-md-8">
													<?=$txtjabatan;?>
												</div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12">
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Kepada</label>
												<div class="col-md-10">
													<?=$txtname;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Alamat</label>
												<div class="col-md-10">
													<?=$txtaddress;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Isi Surat</label>
												<div class="col-md-10">
													<?=$txtdescription;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Penutup</label>
												<div class="col-md-10">
													<?=$txtnote;?>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Detail Quote </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addrow" value="Add Row" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover order-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Deskripsi</th>
																<th scope="col" style="text-align:right;">Qty</th>
																<th scope="col">Satuan</th>
																<th scope="col" style="text-align:right;">Harga Satuan</th>
																<th scope="col" style="text-align:right;">Total Harga</th>
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