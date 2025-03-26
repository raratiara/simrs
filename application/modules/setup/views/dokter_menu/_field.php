									
		


									<div class="form-group">
										<label class="col-md-3 control-label no-padding-right">Poli <span class="required">*</span></label>
										<div class="col-md-9">
											<?=$selpoli;?>
											<input type="hidden" id="id" name="id">
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label no-padding-right">Spesialis <span class="required">*</span></label>
										<div class="col-md-9">
											<?=$txtspesialis;?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label no-padding-right">Nama <span class="required">*</span></label>
										<div class="col-md-9">
											<?=$txtnama;?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label no-padding-right">Foto <span class="required">*</span></label>
										<div class="col-md-9">
											<?=$txtfoto;?>
											<span class="file_foto"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label no-padding-right">Tgl Join </label>
										<div class="col-md-9">
											<input type="date" id="tgl_join" name="tgl_join">
										</div>
									</div>

									<div class="row ca">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Jadwal </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addreimrow" value="Add Row" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover reim-list tablesaw tablesaw-stack" id="tblJadwal" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Hari</th>
																<th scope="col">Jam</th>
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



									

									